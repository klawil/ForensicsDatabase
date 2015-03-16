<?php
include 'CommonFunctions.php';
if ( isset($_POST['delete']) ) {
	if ( $GLOBALS['CanUserEdit'] != 1 ) {
		$myfile = fopen("/var/log/forensics/general.log","a");
		fwrite($myfile,"IP " . $_SERVER['REMOTE_ADDR'] . " tried to access page " . basename($_SERVER['PHP_SELF']) . " on " . date('Y-m-d') . " at " . date('H:i:s') . "\n");
		fclose($myfile);
		echo "Error - You aren't authorized to enter data.";
		return 0;
	}
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
} elseif ( isset($_POST['RID']) ) {
	if ( $GLOBALS['CanUserEdit'] != 1 ) {
		$myfile = fopen("/var/log/forensics/general.log","a");
		fwrite($myfile,"IP " . $_SERVER['REMOTE_ADDR'] . " tried to access page " . basename($_SERVER['PHP_SELF']) . " on " . date('Y-m-d') . " at " . date('H:i:s') . "\n");
		fclose($myfile);
		echo "Error - You aren't authorized to enter data.";
		return 0;
	}
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
	if ( $_POST['broke'] == "on" ) {
		$broke = 1;
	} else {
		$broke = 0;
	}
	if ( $_POST['State'] == "on" ) {
		$State = 1;
	} else {
		$State = 0;
	}
	$SID2 = ", SID2=null";
	if ( isset($_POST['SID2']) && $_POST['SID2'] != "" ) {
		$SID2 = ", SID2='" . $_POST['SID2'] . "'";
	}
	$place = ", place=null";
	if ( $_POST['broke'] == "on" && isset($_POST['place']) && $_POST['place'] != "") {
		$place = ", place='" . $_POST['place'] . "'";
	}
	$IString = "SID='" . $_POST['SID'] ."', EID='" . $_POST['EID'] . "', NumberRounds='" . $Rounds . "', NumberJudges='" . $Judges . "', PRanks='" . $PRanks . "', PQuals='" . $PQuals . "', FRanks='" . $FRanks . "', broke='" . $broke . "', State='" . $State . "'" . $place . $SID2;
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
	if ( $GLOBALS['CanUserEdit'] != 1 ) {
		$myfile = fopen("/var/log/forensics/general.log","a");
		fwrite($myfile,"IP " . $_SERVER['REMOTE_ADDR'] . " tried to enter a partner on page " . basename($_SERVER['PHP_SELF']) . " on " . date('Y-m-d') . " at " . date('H:i:s') . "\n");
		fclose($myfile);
		echo "Error - You aren't authorized to enter data.";
		return 0;
	}
	$WString = " where TID='" . $_POST['TID'] . "' and Results.SID = Students.SID and Results.EID = Events.EID order by EName, LName, FName";
	$NumRoundQuery = mysqli_query($DBConn, "SELECT NumRounds, NumFinalsJudges FROM Tournaments where TID = '" . $_POST['TID'] . "';");
	if ( !$NumRoundQuery ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	$Data = mysqli_fetch_assoc($NumRoundQuery);
	$NumRounds = $Data['NumRounds'];
	$NumJudges = $Data['NumFinalsJudges'];
	$query = mysqli_query($DBConn, 'select Results.SID, SID2, RID, Results.EID, NumberRounds, NumberJudges, broke, State, place from Results, Students, Events' . $WString . ";");
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
		echo '<tr><td>' . Students(0,NULL,$Data['SID'],"SID[" . $RID . "]");
		if ( $Data['SID2'] != NULL ) {
			echo '<br>' . Students(0,NULL,$Data['SID2'],"SID2[" . $RID . "]");
		}
		echo '</td><td>' . Events(0,$Data['EID'],"EID[" . $RID . "]") . '</td>';
		$RQuery = mysqli_query($DBConn, "select Rank, Qual from Ballots where RID='" . $RID . "' and Round is not null order by Round;");
		if ( !$query ) {
			echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
			return 0;
		}
		$NumRows = mysqli_num_rows($RQuery);
		$CurrentRow = 1;
		while ( $CurrentRow <= $NumRows ) {
			$RData = mysqli_fetch_assoc($RQuery);
			echo '<td><input type="number" name="R' . $CurrentRow . 'R[' . $RID . ']" value="' . $RData['Rank'] . '"><input type="number" name="R' . $CurrentRow . 'Q[' . $RID . ']" value="' . $RData['Qual'] . '"></td>';
			$CurrentRow++;
		}
		if ( $NumRows < $NumRounds ) {
			for ( $x = $NumRows; $x < $NumRounds; $x++ ) {
				echo '<td><input type="number" name="R' . $x . 'R[' . $RID . ']"><input type="number" name="R' . $x . 'Q[' . $RID . ']"></td>';
			}
		}
		if ( $Data['broke'] == "1" ) {
			echo '<td style="text-align: center;"><input type="checkbox" name="broke[' . $RID . ']" checked></td>';
		} else {
			echo '<td style="text-align: center;"><input type="checkbox" name="broke[' . $RID . ']"></td>';
		}
		if ( $Data['State'] == "1" ) {
			echo '<td style="text-align: center;"><input type="checkbox" name="State[' . $RID . ']" checked></td>';
		} else {
			echo '<td style="text-align: center;"><input type="checkbox" name="State[' . $RID . ']"></td>';
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
			echo '<td><input type="number" name="J' . $CurrentRow . 'R[' . $RID . ']" value="' . $JData['Rank'] . '"></td>';
			$CurrentRow++;
		}
		if ( $CurrentRow <= $NumRounds ) {
			for ( $x = $CurrentRow; $x <= $NumRounds; $x++ ) {
				echo '<td><input type="number" name="J' . $x . 'R[' . $RID . ']"></td>';
			}
		}
		echo '<td><input type="number" name="place[' . $RID . ']" value="' . $Data['place'] . '"></td><td><input type="button" onclick="EditEntry(' . $RID . ');" value="Save"><input type="button" onclick="DeleteRID(' . $RID . ');" value="Delete"></td><td id="' . $RID . 'M" style="display: none;"></td></tr>
';
		$z++;
	}
	echo '<tr id="CloneRow" style="display: none;"><td>' . Students(0,NULL,NULL,"SID[ROW]") . '</td><td>' . Events(0,NULL,"EID[ROW]") . '</td>';
	for ( $x = $NumRows; $x < $NumRounds; $x++ ) {
		echo '<td><input type="number" name="R' . $x . 'R[ROW]"><input type="number" name="R' . $x . 'Q[ROW]"></td>';
	}
	echo '<td style="text-align: center;"><input type="checkbox" name="broke[ROW]"></td><td style="text-align: center;"><input type="checkbox" name="State[ROW]"></td>';
	for ( $x = $CurrentRow; $x <= $NumRounds; $x++ ) {
		echo '<td><input type="number" name="J' . $x . 'R[ROW]"></td>';
	}
	echo '<td><input type="number" name="place[ROW]"></td><td><input type="button" onclick="EditEntry([ROW]);" value="Save"><input type="button" onclick="DeleteRID([ROW]);" value="Delete"></td><td id="[ROW]M" style="display: none;"></td></table></form><input type="button" value="Add Row" onclick="AddRow();">';
	return 0;
}
?>
<html>
<head>
<title>Tournament Edit | ForensicsDB.com</title>
<link rel="stylesheet" type="text/css" href="Styles.css">
<link rel="stylesheet" media="(max-width: 800px)" href="MobileStyles.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
</head>
<body>
<h1>Tournament Edit</h1>
<?php
MakeHeader();
if ( $GLOBALS['CanUserEdit'] != 1 ) {
	$myfile = fopen("/var/log/forensics/general.log","a");
	fwrite($myfile,"IP " . $_SERVER['REMOTE_ADDR'] . " tried to access restricted page " . basename($_SERVER['PHP_SELF']) . " on " . date('Y-m-d') . " at " . date('H:i:s') . "\n");
	fclose($myfile);
	echo '<h2>Authentication Error: You do not have the permission to access this page</h2>
</body>
</html>';
	return 0;
}
?>
Select Tournament
<form id="TIDPick"><?php echo Tournaments(0); ?> <input type="button" value="Select" onclick="MakePage();"></form>
<div id="TourneyEdit"></div>
<script>
TID = "";
NewNum = 0;
function DeleteRID(RID) {
	RID = RID || "asdf";
	if ( RID == "asdf" ) {
		window.alert("No result ID selected");
		return 0;
	}
	document.getElementById(RID + "M").innerHTML = "Deleting...";
	document.getElementById(RID + "M").style.display = "inline";
	FString = "TID=" + TID + "&RID=" + RID + "&delete=1";
	if ( window.XMLHttpRequest ) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("POST","TournamentEdit.php",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(FString);
    xmlhttp.onreadystatechange=function() {
    	response = xmlhttp.responseText;
    	if ( response == "true" ) {
    		document.getElementById(RID + "M").innerHTML = "Deleted";
    	} else {
    		document.getElementById(RID + "M").innerHTML = response;
    	}
    }
}
function EditEntry(RID) {
	RID = RID || "asdf";
	if ( RID == "asdf" ) {
		window.alert("No result ID selected");
		return 0;
	}
	Elms = document.getElementById("TEdit").elements;
	FString = "TID=" + TID + "&RID=" + RID;
	for ( x = 0; x < Elms.length; x++ ) {
		if ( Elms[x].name.indexOf("[" + RID + "]") != '-1' ) {
			FString = FString + "&" + Elms[x].name.replace("[" + RID + "]","") + "=" + Elms[x].value;
		}
	}
	document.getElementById(RID + "M").innerHTML = "Submitting...";
	document.getElementById(RID + "M").style.display = "inline";
	if ( window.XMLHttpRequest ) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("POST","TournamentEdit.php",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(FString);
    xmlhttp.onreadystatechange=function() {
    	response = xmlhttp.responseText;
    	if ( response == "true" ) {
    		document.getElementById(RID + "M").innerHTML = "Saved";
    	} else {
    		document.getElementById(RID + "M").innerHTML = response;
    	}
    }
}
function AddRow(){
	RowHTML = document.getElementById("CloneRow").innerHTML;
	RowHTML = RowHTML.replace("[ROW]","[N" + NewNum + "]");
	document.getElementById("TEdit-Table").innerHTML = document.getElementById("TEdit-Table").innerHTML + '<tr id="N' + NewNum + '">' + RowHTML + '</tr>';
}
function MakePage() {
	document.getElementById("TourneyEdit").innerHTML = "Loading...";
	TID = document.getElementById("Tournament").options[document.getElementById("Tournament").selectedIndex].value;
	if ( window.XMLHttpRequest ) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("POST","TournamentEdit.php",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("TID=" + TID);
    xmlhttp.onreadystatechange=function() {
    	response = xmlhttp.responseText;
    	document.getElementById("TourneyEdit").innerHTML = response;
    }
}
</script>
</body>
</html>
