<?php
function ValidateArray($Array, $Validation){
	// Validates an array's values
	// @param $Array - the array to validate
	// @param $Checks - an array of checks to make
	//  [['variable', 'IsSet', 'Validate', 'Error'], ...]
	// @return Array - ['Pass', 'Error']
	$ReturnValue['Pass'] = true;
	foreach ( $Validation as $Check ) {
		if ( !isset($Ballot[$Check['variable']]) ) {
			if ( $Check['IsSet'] == 1 ) {
				$ReturnValue['Error'] = $Check['variable'] . ' not set';
				$ReturnValue['Pass'] = false;
			}
		} elseif ( !$Check['Validate']($Ballot[$Check['variable']]) ) {
			$ReturnValue['Error'] = $Check['Error'];
			$ReturnValue['Pass'] = false;
		}
		if ( !$ReturnValue['Pass'] ) {
			return $ReturnValue;
		}
	}
	return $ReturnValue;
}
function IsInt($String){
	// Determines whetehr parameter represents an integer
	// @param $String - string to check
	// @return boolean - whether the string is an integer or not
	return (string)(int)$String == $String;
}
function IsBool($String){
	// Determines whetehr parameter represents an boolean (1 or 0)
	// @param $String - string to check
	// @return boolean - whether the string is a boolean or not
	return ((string)(int)$String == 1 || (string)(int)$String == 0);
}
function IsID($DBConn, $ID, $Type) {
	// Determines whether the ID is valid
	// @param $DBConn - connection to MySQL database
	// @param $ID - the ID to check
	// @param $Type - the type of ID (TID, SID, EID)
	// @return boolean - whether the ID is valid
	
	// Set the correct tables
	if ( $Type == 'TID' ) {
		$Table = 'Tournaments';
	} elseif ( $Type == 'SID' ) {
		$Table = 'Students';
	} elseif ( $Type == 'EID' ) {
		$Table = 'Events';
	} else {
		return false;
	}
	return true;
}
function InsertBallots($Ballots, $DBConn = NULL, $Insert = NULL) {
	// Inserts Ballots from an array
	// @param $Ballots: [RID: RID,
	//  Round: Round,
	//  Judge: Judge,
	//  ElimLevel: ElimLevel,
	//  Rank: Rank,
	//  Qual: Qual]
	// @param $DBConn - connection to MySQL database
	// @param $Insert - if set, insert/updates the ballots
	$ReturnValue = ''; // Value to return at the end
	$Queries = ''; // List of queries to execute at the end
	
	// Array of variables and values to check
	$CheckArray = [['variable' => 'RID', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'RID not valid'],
	['variable' => 'Round', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Round not valid'],
	['variable' => 'Judge', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Judge not valid'],
	['variable' => 'ElimLevel', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Elimination level not valid'],
	['variable' => 'Rank', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Rank not valid'],
	['variable' => 'Qual', 'IsSet' => 0, 'Validate' => function($var){return is_numeric($var);}, 'Error' => 'Qual not valid']];
	
	// Loop through all ballots
	foreach ($Ballots as $key=>$Ballot) {
		// Check variables
		$Valid = ValidateArray($Ballot,$CheckArray);
		if ( ! $Valid['Pass'] ) {
			$ReturnValue[$key] = $Valid['Error'];
			continue;
		}
		
		// Insert data if desired
		if ( $Insert != NULL ) {
			if ( ! isset($Ballot['Qual']) ) {
				$Ballot['Qual'] = '';
			}
			
			// Check if ballot already exists
			$ExistsString = 'select * from Ballots where RID="' . MySQLEscape($DBConn,$Ballot['RID']) . '" and Round="' . MySQLEscape($DBConn,$Ballot['Round']) . '" and Judge="' . MySQLEscape($DBConn,$Ballot['Judge']) . '";';
			$ExistsQuery = MySQLQuery($DBConn,$ExistsString);
			if ( ! $ExistsQuery ) {
				$ReturnValue[$key] = $ExistsQuery;
				continue;
			}
			if ( mysqli_num_rows($ExistsQuery) == 0 ) {
				// Update query if exists
				$QueryString = 'insert into Ballots set RID="' . MySQLEscape($DBConn,$Ballot['RID']) . '", Round="' . MySQLEscape($DBConn,$Ballot['Round']) . '", Judge="' . MySQLEscape($DBConn,$Ballot['Judge']) . '", Rank="' . MySQLEscape($DBConn,$Ballot['Rank']) . '", Qual="' . MySQLEscape($DBConn,$Ballot['Qual']) . '", ElimLevel="' . MySQLEscape($DBConn,$Ballot['ElimLevel']) . '";';
			} else {
				// Insert query if doesn't exist
				$QueryString = 'update Ballots set Rank="' . MySQLEscape($DBConn,$Ballot['Rank']) . '", Qual="' . MySQLEscape($DBConn,$Ballot['Qual']) . '", ElimLevel="' . MySQLEscape($DBConn,$Ballot['ElimLevel']) . '" where RID="' . MySQLEscape($DBConn,$Ballot['RID']) . '" and Round="' . MySQLEscape($DBConn,$Ballot['Round']) . '" and Judge="' . MySQLEscape($DBConn,$Ballot['Judge']) . '";';
			}
			$Query = MySQLQuery($DBConn,$QueryString);
			if ( $Query ) {
				$ReturnValue[$key] = true;
			} else {
				$ReturnValue[$key] = $Query;
			}
		} else {
			$ReturnValue[$key] = true;
		}
	}
	return $ReturnValue;
}
function InsertResult($DBConn, $SID, $EID, $TID, $broke, $State, $PID = NULL, $place = NULL, $Update = NULL) {
	// Inserts a result into the results table
	// @param $DBConn - connection to MySQL database
	// @param $SID - the ID of the student participating
	// @param $EID - the event ID
	// @param $TID - the tournament ID
	// @param $broke - whether the student(s) broke
	// @param $State - whether the student(s) qualified for state
	// @param $PID - student ID of the partner (if required)
	// @param $place - the place the student attained
	// @return array ['Done','Error']
	//  Done - boolean - whether the data was valid and inserted
	//  Error - string - the error string if the insert was not successful or RID if insert was successful
	
	// Get variables set up
	$ReturnValue['Done'] = true;
	$Parameters = compact('DBConn','SID','EID','TID','broke','State');
	unset($Parameters['DBConn']);
	if ( $PID != NULL ) {
		$Parameters['PID'] = $PID;
	}
	if ( $place != NULL ) {
		$Parameters['place'] = $place;
	}
	
	// Escape all the parameters
	foreach ( $Parameters as $key => $Param ) {
		$Parameters[$key] = MySQLEscape($Param,$DBConn);
	}
	
	// Validate parameters format
	$CheckArray = [['variable' => 'SID', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Student not valid'],
	['variable' => 'EID', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Event not valid'],
	['variable' => 'TID', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Tournament not valid'],
	['variable' => 'broke', 'IsSet' => 1, 'Validate' => function($var){return IsBool($var);}, 'Error' => 'Broke value not valid'],
	['variable' => 'State', 'IsSet' => 1, 'Validate' => function($var){return IsBool($var);}, 'Error' => 'State value not valid'],
	['variable' => 'PID', 'IsSet' => 0, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Partner not valid'],
	['variable' => 'place', 'IsSet' => 0, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Place not valid']];
	$Valid = ValidateArray($Parameters,$CheckArray);
	
	// Validate IDs
	$IDArray = [['variable' => 'SID', 'ID' => 'SID', 'Error' => 'Invalid student'],
	['variable' => 'TID', 'ID' => 'TID', 'Error' => 'Invalid tournament'],
	['variable' => 'EID', 'ID' => 'EID', 'Error' => 'Invalid event']];
	if ( isset($Parameters['PID']) ) {
		array_push($IDArray,['variable' => 'PID', 'ID' => 'SID', 'Error' => 'Invalid partner']);
	}
	foreach ( $IDArray as $ID ) {
		if ( !IsId($DBConn,$IDArray[$ID['variable']], $ID['ID']) ) {
			$ReturnValue['Done'] = false;
			$ReturnValue['Error'] = $ID['Error'];
			return $ReturnValue;
		}
	}
	
	// Get event's partner status
	$PQuery = MySQLQuery($DBConn,'select Partner from Events where EID="' . $Parameters['EID'] . '";');
	if ( !$PQuery['Result'] ) {
		$ReturnValue['Done'] = false;
		$ReturnValue['Error'] = $PQuery['Error'];
		return $ReturnValue;
	} elseif ( mysqli_num_rows($PQuery['Query']) == 0 ) {
		$ReturnValue['Done'] = false;
		$ReturnValue['Error'] = 'No such event';
		return $ReturnValue;
	} elseif ( mysqli_num_rows($PQuery['Query']) > 1 ) {
		$ReturnValue['Done'] = false;
		$ReturnValue['Error'] = 'Impossible error: more than 1 event with that ID';
		return $ReturnValue;
	}
	$EData = mysqli_fetch_assoc($PQuery['Query']);
	if ( $EData['Partner'] == '1' && $PID == NULL ) {
		$ReturnValue['Done'] = false;
		$ReturnValue['Error'] = 'Partner required for that event';
		return $ReturnValue;
	} elseif ( $EData['Partner'] != '1' && $PID != NULL ) {
		$ReturnValue['Done'] = false;
		$ReturnValue['Error'] = 'That is not a partner event';
		return $ReturnValue;
	}
	
	// Check if partner and student are the same
	if ( $PID != NULL && $SID == $PID ) {
		$ReturnValue['Done'] = false;
		$ReturnValue['Error'] = 'Partner must be a different student';
		return $ReturnValue;
	}
	
	// Check if RID already exists
	$ExistsString = '';
	foreach ( $IDArray as $ID ) {
		if ( $ExstsString == '' ) {
			$ExistsString = 'select RID from Results where ' . $ID['variable'] . '="' . $Parameters[$ID['variable']] . '"';
		} else {
			$ExistsString = $ExistsString . ' and ' . $ID['variable'] . '="' . $Parameters[$ID['variable']] . '"';
		}
	}
	$ExistsString = $ExistsString . ';';
	$EQuery = MySQLQuery($DBConn,$ExistsString);
	if ( !$EQuery['Result'] ){
		$ReturnValue['Done'] = false;
		$ReturnValue['Error'] = $EQuery['Query'];
		return $ReturnValue;
	} elseif ( mysqli_num_rows($EQuery['Query']) == 1 && $Update == NULL ) {
		$ReturnValue['Done'] = false;
		$ReturnValue['Error'] = 'That result already exists';
		return $ReturnValue;
	}
	
	// Create and execute query
	$QueryString = '';
	if ( $Update == NULL ) {
		$BaseString = 'insert into';
	} else {
		$BaseString = 'update';
	}
	foreach ( $Parameters as $Name => $Value ) {
		if ( $QueryString == '' ) {
			$QueryString = $BaseString . ' Results set ' . $Name . '="' . $Value . '"';
		} else {
			$QueryString = $QueryString . ', ' . $Name . '="' . $Value . '"';
		}
	}
	$QueryString = $QueryString . ';';
	$Query = MySQLQuery($DBConn,$QueryString);
	if ( !$Query['Result'] ) {
		$ReturnValue['Done'] = false;
		$ReturnValue['Error'] = $Query['Query'];
		return $ReturnValue;
	}
	
	// Get RID
	$RIDString = '';
	foreach ( $IDArray as $ID ) {
		if ( $RIDString == '' ) {
			$RIDString = 'select RID from Results where ' . $ID['variable'] . '="' . $Parameters[$ID['variable']] . '"';
		} else {
			$RIDString = $RIDString . ' and ' . $ID['variable'] . '="' . $Parameters[$ID['variable']] . '"';
		}
	}
	$RIDString = $RIDString . ';';
	$RIDQuery = MySQLQuery($DBConn,$RIDString);
	if ( !$RIDQuery['Result'] ){
		$ReturnValue['Done'] = false;
		$ReturnValue['Error'] = $RIDQuery['Query'];
		return $ReturnValue;
	}
	$RID = mysqli_fetch_assoc($RIDQuery['Query']);
	$ReturnValue['Done'] = true;
	$ReturnValue['Error'] = $RID['RID'];
	return $ReturnValue;
}
?>
