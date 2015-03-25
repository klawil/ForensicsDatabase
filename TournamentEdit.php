<?php
include 'CommonFunctions.php';
if ( !empty($_POST) && $GLOBALS['CanUserEdit'] != 1 ) {
	$myfile = fopen("/var/log/forensics/general.log","a");
	fwrite($myfile,"IP " . $_SERVER['REMOTE_ADDR'] . " tried to access page " . basename($_SERVER['PHP_SELF']) . " on " . date('Y-m-d') . " at " . date('H:i:s') . "\n");
	fclose($myfile);
	echo "Error - You aren't authorized to enter data.";
	return 0;
}
if ( isset($_POST['delete']) ) {
	$RID = $_POST['RID'];
	$query = mysqli_query($DBConn, "delete from Results where RID='" . $RID . "';");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	$query = mysqli_query($DBConn, "delete from Ballots where RID='" . $RID . "';");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	echo "true";
	return 0;
} elseif ( isset($_POST['create']) ) {
	echo "true|meow";
	return 0;
} elseif ( isset($_POST['RID']) ) {
	$RID = $_POST['RID'];
	$tbl = "Tournaments";
	if (( ! isset($_POST['TID']) )) {
		echo "Error - No tournament ID specified";
		return 0;
	}
	$query = mysqli_query($DBConn, "SELECT NumRounds, NumFinalsJudges from Tournaments where TID='" . $_POST['TID'] . "';");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		break;
	}
	$NumRows = mysqli_num_rows($query);
	if ( $NumRows == 0 ) {
		echo "Error - No tournament with that ID";
		return 0;
	}
	$data = mysqli_fetch_assoc($query);
	$NumRounds = $data['NumRounds'];
	$NumJudges = $data['NumFinalsJudges'];
	if ( ! isset($_POST['broke']) ) {
		echo "Error - No information on whether participant broke.";
		return 0;
	} elseif ( $_POST['broke'] == "1" && ! isset($_POST['place']) ) {
		echo "Error - No place listed.";
		return 0;
	}
	if (( ! isset($_POST['State']) )) {
		echo "Error - No 'State Qualifier' checkbox information.";
		return 0;
	}
	if (( ! isset($_POST['EID']) )) {
		echo "Error - No event information.";
		return 0;
	}
	if (( ! isset($_POST['SID']) )) {
		echo "Error - No student information.";
		return 0;
	}
	$Rounds = 0;
	$PRanks = 0;
	$PQuals = 0;
	while ( isset($_POST['R' . ($Rounds + 1) . 'R']) && $_POST['R' . ($Rounds + 1) . 'R'] != "" ) {
		$PRanks = $PRanks + $_POST['R' . ($Rounds + 1) . 'R'];
		$PQuals = $PQuals + $_POST['R' . ($Rounds + 1) . 'Q'];
		$Rounds++;
	}
	$Judges = 0;
	$FRanks = 0;
	while ( $_POST['broke'] == "on" && isset($_POST['J' . ($Judges + 1) . 'R']) && $_POST['J' . ($Judges + 1) . 'R'] != "" ) {
		$FRanks = $FRanks + $_POST['J' . ($Judges + 1) . 'R'];
		$Judges++;
	}
	$SID2 = ", SID2=null";
	if ( isset($_POST['SID2']) && $_POST['SID2'] != "" ) {
		$SID2 = ", SID2='" . $_POST['SID2'] . "'";
	}
	$place = ", place=null";
	if ( $_POST['broke'] == "on" && isset($_POST['place']) && $_POST['place'] != "") {
		$place = ", place='" . $_POST['place'] . "'";
	}
	$IString = "SID='" . $_POST['SID'] ."', EID='" . $_POST['EID'] . "', NumberRounds='" . $Rounds . "', NumberJudges='" . $Judges . "', PRanks='" . $PRanks . "', PQuals='" . $PQuals . "', FRanks='" . $FRanks . "', broke='" . $_POST['broke'] . "', State='" . $_POST['State'] . "'" . $place . $SID2;
	$query = mysqli_query($DBConn, "update Results set " . $IString . " where RID='" . $RID . "';");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	$query = mysqli_query($DBConn, "delete from Ballots where RID='" . $RID . "';");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		break;
	}
	if (( $_POST['broke'] == "on" )) {
		for ( $x = 1; $x <= $NumJudges; $x++ ) {
			if (( isset($_POST['J' . $x . 'R']) && $_POST['J' . $x . 'R'] != "" )) {
				$query = mysqli_query($DBConn, "insert into Ballots set RID='" . $RID . "', Judge='" . $x . "', Rank='" . $_POST['J' . $x . 'R'] . "';");
				if ( !$query ) {
					echo "Error - MySQL error: " . mysqli_error($DBConn) . " on Judge " . $x . ".";
					return 0;
				}
			}
		}
	}
	for ( $x = 1; $x <= $NumRounds; $x++ ) {
		if (( ! isset($_POST['R' . $x . 'R']) || ! isset($_POST['R' . $x . 'Q']) )) {
			echo "Error - Either quals or ranks for round " . $x . " are missing.";
			return 0;
		}
		$query = mysqli_query($DBConn, "insert into Ballots set RID='" . $RID . "', Round='" . $x . "', Rank='" . $_POST['R' . $x . 'R'] . "', Qual='" . $_POST['R' . $x . 'Q'] . "';");
		if ( !$query ) {
			echo "Error - MySQL error: " . mysqli_error($DBConn) . " on Round " . $x . ".";
			return 0;
		}
	}
	echo "true";
	return 0;
} elseif ( isset($_POST['TID']) ) {
	$WString = " where TID='" . $_POST['TID'] . "' and Results.SID = Students.SID and Results.EID = Events.EID order by EName, LName, FName";
	$NumRoundQuery = mysqli_query($DBConn, "SELECT NumRounds, NumFinalsJudges FROM Tournaments where TID = '" . $_POST['TID'] . "';");
	if ( !$NumRoundQuery ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	$Data = mysqli_fetch_assoc($NumRoundQuery);
	$NumRounds = $Data['NumRounds'];
	$NumJudges = $Data['NumFinalsJudges'];
	$query = mysqli_query($DBConn, 'select Partner, Results.SID, SID2, RID, Results.EID, NumberRounds, NumberJudges, broke, State, place from Results, Students, Events' . $WString . ";");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	echo '<form id="TEdit"><table id="TEdit-Table" style="border-collapse: collapse;"><tr><th>Name</th><th>Event</th>';
	for ( $x = 1; $x <= $NumRounds; $x++ ) {
		echo '<th>Round ' . $x . "</th>";
	}
	echo '<th>Broke</th><th>State</th>';
	for ( $x = 1; $x <= $NumJudges; $x++ ) {
		echo '<th>Judge ' . $x . "</th>";
	}
	echo '<th>Place</th></tr>
';
	$MasterNumRows = mysqli_num_rows($query);
	$RID = "";
	$z = 1;
	while ( $z <= $MasterNumRows ) {
		$Data = mysqli_fetch_assoc($query);
		$RID = $Data['RID'];
		echo '<tr id="' . $RID . '"><td>' . Students(0,NULL,$Data['SID'],"SID[" . $RID . "]","StateChange('SID[" . $RID . "]');");
		if ( $Data['SID2'] != NULL ) {
			echo '<br>' . Students(0,NULL,$Data['SID2'],"SID2[" . $RID . "]","StateChange('SID2[" . $RID . "]');");
		} elseif ( $Data['Partner'] == 1 ) {
			echo '<br>' . Students(0,NULL,NULL,"SID2[" . $RID . "]","StateChange('SID2[" . $RID . "]');");
		}
		echo '</td><td>' . Events(0,$Data['EID'],"EID[" . $RID . "]","StateChange('EID[" . $RID . "]');") . '</td>';
		$RQuery = mysqli_query($DBConn, "select Rank, Qual from Ballots where RID='" . $RID . "' and Round is not null order by Round;");
		if ( !$query ) {
			echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
			return 0;
		}
		$NumRows = mysqli_num_rows($RQuery);
		$CurrentRow = 1;
		while ( $CurrentRow <= $NumRows ) {
			$RData = mysqli_fetch_assoc($RQuery);
			echo '<td><input type="number" name="R' . $CurrentRow . 'R[' . $RID . ']" value="' . $RData['Rank'] . '" onchange="StateChange(\'R' . $CurrentRow . 'R[' . $RID . ']\');"><input type="number" name="R' . $CurrentRow . 'Q[' . $RID . ']" value="' . $RData['Qual'] . '" onchange="StateChange(\'R' . $CurrentRow . 'Q[' . $RID . ']\');"></td>';
			$CurrentRow++;
		}
		if ( $NumRows < $NumRounds ) {
			for ( $x = $NumRows; $x < $NumRounds; $x++ ) {
				echo '<td><input type="number" name="R' . $x . 'R[' . $RID . ']" onchange="StateChange(\'R' . $x . 'R[' . $RID . ']\');"><input type="number" name="R' . $x . 'Q[' . $RID . ']" onchange="StateChange(\'R' . $x . 'Q[' . $RID . ']\');"></td>';
			}
		}
		if ( $Data['broke'] == "1" ) {
			echo '<td style="text-align: center;"><input type="checkbox" name="broke[' . $RID . ']" onchange="StateChange(\'broke[' . $RID . ']\');" checked></td>';
		} else {
			echo '<td style="text-align: center;"><input type="checkbox" name="broke[' . $RID . ']" onchange="StateChange(\'broke[' . $RID . ']\');"></td>';
		}
		if ( $Data['State'] == "1" ) {
			echo '<td style="text-align: center;"><input type="checkbox" name="State[' . $RID . ']" onchange="StateChange(\'State[' . $RID . ']\');" checked></td>';
		} else {
			echo '<td style="text-align: center;"><input type="checkbox" name="State[' . $RID . ']" onchange="StateChange(\'State[' . $RID . ']\');"></td>';
		}
		$JQuery = mysqli_query($DBConn, "select Rank, Qual from Ballots where RID='" . $RID . "' and Judge is not null order by Round;");
		if ( !$JQuery ) {
			echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
			return 0;
		}
		$NumRows = mysqli_num_rows($JQuery);
		$CurrentRow = 1;
		while ( $CurrentRow <= $NumRows ) {
			$JData = mysqli_fetch_assoc($JQuery);
			echo '<td><input type="number" name="J' . $CurrentRow . 'R[' . $RID . ']" value="' . $JData['Rank'] . '" onchange="StateChange(\'J' . $CurrentRow . 'R[' . $RID . ']\');"></td>';
			$CurrentRow++;
		}
		if ( $CurrentRow <= $NumRounds ) {
			for ( $x = $CurrentRow; $x <= $NumRounds; $x++ ) {
				echo '<td><input type="number" name="J' . $x . 'R[' . $RID . ']" onchange="StateChange(\'J' . $x . 'R[' . $RID . ']\');"></td>';
			}
		}
		echo '<td><input type="number" name="place[' . $RID . ']" value="' . $Data['place'] . '" onchange="StateChange(\'place[' . $RID . ']\');"></td><td><input type="button" onclick="EditEntry(' . $RID . ');" value="Save"><input type="button" onclick="DeleteRID(' . $RID . ');" value="Delete"></td><td id="' . $RID . 'M" style="display: none;"></td></tr>
';
		$z++;
	}
	echo '<tr id="CloneRow" style="display: none;"><td>' . Students(0,NULL,NULL,"SID[ROW]","StateChange(\'SID[ROW]\');") . '</td><td>' . Events(0,NULL,"EID[ROW]","StateChange(\'[ROW]\');") . '</td>';
	for ( $x = $NumRows; $x < $NumRounds; $x++ ) {
		echo '<td><input type="number" name="R' . $x . 'R[ROW]" onchange="StateChange(\'[ROW]\');"><input type="number" name="R' . $x . 'Q[ROW]" onchange="StateChange(\'[ROW]\');"></td>';
	}
	echo '<td style="text-align: center;"><input type="checkbox" name="broke[ROW]" onchange="StateChange(\'[ROW]\');"></td><td style="text-align: center;"><input type="checkbox" name="State[ROW]" onchange="StateChange(\'[ROW]\');"></td>';
	for ( $x = $CurrentRow; $x <= $NumRounds; $x++ ) {
		echo '<td><input type="number" name="J' . $x . 'R[ROW]" onchange="StateChange(\'[ROW]\');"></td>';
	}
	echo '<td><input type="number" name="place[ROW]" onchange="StateChange(\'[ROW]\');"></td><td id="NROWNB"><input type="button" onclick="CreateEntry(\'NROWN\');" value="Save"></td><td id="NROWNM" style="display: none;"></td></tr></table></form><input type="button" value="Add Row" onclick="AddRow();"><br><input type="button" value="Show Changed" onclick="ShowChange();">';
	return 0;
}
include "html.php";
?>
