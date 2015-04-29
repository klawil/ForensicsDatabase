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
	function IsInt($String){
		return (string)(int)$String == $String;
	}
	
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
			continue
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
				continue
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
function InsertResult($SID, $EID, $TID, $broke, $State, $place, $PID = NULL) {

}
?>
