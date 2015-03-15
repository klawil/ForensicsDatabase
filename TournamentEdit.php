<?php
include 'CommonFunctions.php';
if ( isset($_POST['RID']) ) {
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
		if ( $NumRows < $NumRounds ) {
			for ( $x = $NumRows; $x < $NumRounds; $x++ ) {
				echo '<td><input type="number" name="J' . $x . 'R[' . $RID . ']"></td>';
			}
		}
		echo '<td><input type="number" name="place[' . $RID . ']" value="' . $Data['place'] . '"></td><td><input type="button" onclick="EditEntry(' . $RID . ');" value="Change"><input type="button" onclick="" value="Delete"></td><td id="' . $RID . 'M" style="display: none;"></td></tr>
';
		$z++;
	}
	echo '</table></form><input type="button" value="Add Row" onclick="AddRow();">';
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
	document.getElementById("TEdit-Table").innerHTML = document.getElementById("TEdit-Table").innerHTML + "<td>Test</td>";
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
