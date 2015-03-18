<?php
include "CommonFunctions.php";
if ( isset($_POST) && $GLOBALS['CanUserEdit'] != 1 ) {
	$myfile = fopen("/var/log/forensics/general.log","a");
	fwrite($myfile,"IP " . $_SERVER['REMOTE_ADDR'] . " tried to access page " . basename($_SERVER['PHP_SELF']) . " on " . date('Y-m-d') . " at " . date('H:i:s') . "\n");
	fclose($myfile);
	echo "Error - You aren't authorized to enter data.";
	return 0;
}
if ( isset($_POST['RID']) ) {
	if ( ! isset($_POST['SID']) ) {
		echo "Error - No partner selected.";
		return 0;
	}
	$query = mysqli_query($DBConn, "update Results set SID2='" . $_POST['SID'] . "' where RID='" . $_POST['RID'] . "';");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	echo "true";
	return 0;
} elseif ( isset($_POST['TID']) ) {
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
	if (( ! isset($_POST['qual']) )) {
		echo "Error - No 'State Qualifier' checkbox information.";
		return 0;
	}
	if (( ! isset($_POST['Event']) )) {
		echo "Error - No event information.";
		return 0;
	}
	if (( ! isset($_POST['Student']) )) {
		echo "Error - No student information.";
		return 0;
	}
	$PString = "";
	if ( $_POST['broke'] == "1" ) {
		$PString = ", place='" . $_POST['place'] . "'";
	}
	$PString = $PString . ", NumberRounds=" . $NumRounds;
	if ( $_POST['broke'] == "1" ) {
		$PString = $PString . ", NumberJudges=" . $NumJudges;
	} else {
		$PString = $PString . ", NumberJudges=0";
	}
	if ( $_POST['override'] != 1 && mysqli_num_rows(mysqli_query($DBConn, "select * from Results where EID='" . $_POST['Event'] . "' and SID='" . $_POST['Student'] . "' and TID='" . $_POST['TID'] . "';")) != 0 ) {
		echo "That entry already exists.<br>Edit the existing entry? <input type='hidden' id='override' ><input type='button' value='yes' onclick='SubmitInfo(1);'>";
		return 0;
	}
	if ( $_POST['override'] == 1 ) {
		$RIDquery = mysqli_query($DBConn, "select RID from Results where EID='" . $_POST['Event'] . "' and SID='" . $_POST['Student'] . "' and TID='" . $_POST['TID'] . "';");
		if ( !$query ) {
			echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
			return 0;
		}
		$data = mysqli_fetch_assoc($RIDquery);
		$RID = $data['RID'];
		$query = mysqli_query($DBConn, "update Results set broke='" . $_POST['broke'] . "', State='" . $_POST['qual'] . "'" . $PString . " where RID='" . $RID . "';");
		if ( !$query ) {
			echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
			return 0;
		}
		$query = mysqli_query($DBConn, "delete from Ballots where RID='" . $RID . "';");
	} else {
		$query = mysqli_query($DBConn, "insert into Results set SID='" . $_POST['Student'] . "', EID='" . $_POST['Event'] . "', TID='" . $_POST['TID'] . "', broke='" . $_POST['broke'] . "', State='" . $_POST['qual'] . "'" . $PString . ";");
		if ( !$query ) {
			echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
			return 0;
		}
		$query = mysqli_query($DBConn, "select last_insert_id() as RID;");
		if ( !$query ) {
			echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
			return 0;
		}
		$data = mysqli_fetch_assoc($query);
		$RID = $data['RID'];
	}
	$FRanks = 0;
	if (( $_POST['broke'] == 1 )) {
		for ( $x = 1; $x <= $NumJudges; $x++ ) {
			if (( ! isset($_POST['J' . $x . 'R']) )) {
				echo "Warning - Ranks for judge " . $x . " in finals are missing.<br>";
			} else {
				$FRanks = $FRanks + $_POST['J' . $x . 'R'];
				$query = mysqli_query($DBConn, "insert into Ballots set RID='" . $RID . "', Judge='" . $x . "', Rank='" . $_POST['J' . $x . 'R'] . "';");
				if ( !$query ) {
					echo "Error - MySQL error: " . mysqli_error($DBConn) . " on Judge " . $x . ".";
					return 0;
				}
			}
		}
	}
	$PRanks = 0;
	$PQuals = 0;
	for ( $x = 1; $x <= $NumRounds; $x++ ) {
		if (( ! isset($_POST['R' . $x . 'R']) || ! isset($_POST['R' . $x . 'Q']) )) {
			echo "Error - Either quals or ranks for round " . $x . " are missing.";
			return 0;
		}
		$PRanks = $PRanks + $_POST['R' . $x . 'R'];
		$PQuals = $PQuals + $_POST['R' . $x . 'Q'];
		$query = mysqli_query($DBConn, "insert into Ballots set RID='" . $RID . "', Round='" . $x . "', Rank='" . $_POST['R' . $x . 'R'] . "', Qual='" . $_POST['R' . $x . 'Q'] . "';");
		if ( !$query ) {
			echo "Error - MySQL error: " . mysqli_error($DBConn) . " on Round " . $x . ".";
			return 0;
		}
	}
	$query = mysqli_query($DBConn, "update Results set PRanks='" . $PRanks . "', PQuals='" . $PQuals . "', FRanks='" . $FRanks . "' where RID='" . $RID . "';");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	$query = mysqli_query($DBConn, "select Partner from Events where EID='" . $_POST['Event'] . "';");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	$Data = mysqli_fetch_assoc($query);
	if ( $Data['Partner'] == "1" ) {
		$query = mysqli_query($DBConn, "select FName, LName, SID from Students order by LName, FName;");
		if ( !$query ) {
			echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
			return 0;
		}
		$NumRows = mysqli_num_rows($query);
		$CurrentRow = 0;
		$StudentString = '<select name="SID" form="SIDSelect" id="SID">';
		while ( $CurrentRow < $NumRows ) {
			$results = mysqli_fetch_assoc($query);
			$StudentString = $StudentString . '<option value="' . $results['SID'] . '">' . $results['LName'] . ", " . $results['FName'] . "</option>";
			$CurrentRow++;
		}
		$StudentString = $StudentString . "</select>";
		echo 'Select the partner:<br>
<form id="SIDSelect" action="TournamentUpdate.php" method="post">
<input type="hidden" value="' . $RID . '" id="RID">
' . $StudentString . '<br>
<input type="button" value="Select" onclick="SubmitPartner();">
</form>
';
	} else {
		echo "true";
	}
	return 0;
}
?>
<html>
<head><title>Tournament Results Entry | ForensicsDB.com</title>
<link rel="stylesheet" type="text/css" href="Styles.css">
<link rel="stylesheet" media="(max-width: 800px)" href="MobileStyles.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
</head>
<body>
<h1>Tournament Update</h1>
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
<h1><div id="Header"></div></h1>
<h3 id="Message"></h3>
Select Tournament
<div id="Tourneys"><?php echo Tournaments(0); ?> <input type="button" value="Select" onclick="MakePage();"></div><form id="EntryID" action="TournamentUpdate.php"><div id="TourneyID"></div><br>
<div id="Students" style="display: none;"><?php echo Students(0); ?></div><br><br>
<div id="Events" style="display: none;"><?php echo Events(0); ?></div><br><br>
<div id="Rounds"></div>
<div id="info" style="display: none;">
    <input type="checkbox" value="broke" onchange="OutsHideShow();" id="broke">Broke<br>
    <input type="checkbox" value="StateQual" id="Qual">Qualified for State<br>
</div>
<div id="Outs" style="display: none;"></div>
<div id="submit" style="display: none;"><input type="submit" onclick="SubmitInfo();" value="Submit"></div>
</form>
<script>
function SubmitPartner() {
    event.preventDefault();
    PString = "RID=" + document.getElementById("RID").value;
    PString = PString + "&SID=" + document.getElementById("SID").options[document.getElementById("SID").selectedIndex].value;
    if ( window.XMLHttpRequest ) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("POST","TournamentUpdate.php",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(PString);
    xmlhttp.onreadystatechange=function() {
    	response = xmlhttp.responseText;
    	if ( response == "true" ) {
    	    document.getElementById("Message").innerHTML = document.getElementById("Student").options[document.getElementById("Student").selectedIndex].text + " entry in " + document.getElementById("Event").options[document.getElementById("Event").selectedIndex].text + " submitted successfully.";
    	    document.getElementById("EntryID").reset();
    	    OutsHideShow();
    	} else {
    	    document.getElementById("Message").innerHTML = response;
    	}
    }
}
function SubmitInfo(override) {
    event.preventDefault();
    overrde = override || 0;
    document.getElementById("Message").innerHTML = "Submitting...";
    x = 3;
    RString = "override=" + override + "&";
    while (document.getElementById("EntryID").elements[x].value != "broke") {
        RString = RString + document.getElementById("EntryID").elements[x].id + "=" + document.getElementById("EntryID").elements[x].value + "&";
        if ( document.getElementById("EntryID").elements[x].value == "" ) {
            document.getElementById("Message").innerHTML = "Enter value for " + document.getElementById("EntryID").elements[x].id;
            return;
        }
        x = x + 1;
    }
    y = x - 1;
    IString = "";
    for ( x = 0; x < 3; x++) {
        IString = IString + document.getElementById("EntryID").elements[x].id + "=" + document.getElementById("EntryID").elements[x].value + "&";
    }
    BString = "";
    if ( document.getElementById("broke").checked ) {
        IString = IString + "broke=1&";
        z = y + 3;
        for ( x = z; x < document.getElementById("EntryID").elements.length - 1; x++ ) {
            if ( document.getElementById("EntryID").elements[x].value != "" ) {
            	BString = BString + document.getElementById("EntryID").elements[x].id + "=" + document.getElementById("EntryID").elements[x].value + "&";
            }
        }
    } else {
        IString = IString + "broke=0&";
    }
    if ( document.getElementById("Qual").checked ) {
        IString = IString + "qual=1";
    } else {
        IString = IString + "qual=0";
    }
    document.getElementById("Message").innerHTML = "";//RString + BString + IString;
    document.getElementById("Message").innerHTML = "Submitting...";
    //TString = "&TID=" + document.getElementById("
    if ( window.XMLHttpRequest ) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("POST","TournamentUpdate.php",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(RString + BString + IString);
    xmlhttp.onreadystatechange=function() {
    	response = xmlhttp.responseText;
    	if ( response.indexOf("true") > -1 ) {
    		string = response;
    		string = string.replace("true",document.getElementById("Student").options[document.getElementById("Student").selectedIndex].text + " entry in " + document.getElementById("Event").options[document.getElementById("Event").selectedIndex].text + " submitted successfully.");
    	    document.getElementById("Message").innerHTML = string;
    	    EventIndex = document.getElementById("Event").selectedIndex;
    	    document.getElementById("EntryID").reset();
    	    document.getElementById("Event").selectedIndex = EventIndex;
    	    OutsHideShow();
    	} else {
    	    document.getElementById("Message").innerHTML = response;
    	}
    }
}
function GetInfo(TID){
    if ( window.XMLHttpRequest ) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("POST","TournamentInfo.php",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("TID=" + TID);
	return xmlhttp;
}
function MakePage(){
    document.getElementById("Header").innerHTML = document.getElementById("Tournament").options[document.getElementById("Tournament").selectedIndex].text;
    xmlhttp = GetInfo(document.getElementById("Tournament").options[document.getElementById("Tournament").selectedIndex].value);
    xmlhttp.onreadystatechange = function () {
    	TInfo = xmlhttp.responseText;
    	InfoSplit = TInfo.split("|");
    	NumRounds = InfoSplit[0];
    	NumJudge = InfoSplit[1];
    	HTMLString = ""
    	for ( x = 1; x <= NumRounds; x++ ) {
    	    HTMLString = HTMLString + "Round " + x + ': <input type="number" id="R' + x + 'R"><input type="number" id="R' + x + 'Q"><br>';
    	}
    	document.getElementById("Rounds").innerHTML = HTMLString;
		HTMLString = ""
    	for ( x = 1; x <= NumJudge; x++ ) {
    	    HTMLString = HTMLString + "Judge " + x + ': <input type="number" id="J' + x + 'R"><br>';
    	}
    	HTMLString = HTMLString + 'Place: <input type="number" id="place"><br>';
    	document.getElementById("Outs").innerHTML = HTMLString;
    	document.getElementById("info").style.display = 'inline';
    	document.getElementById("Events").style.display = 'inline';
    	document.getElementById("Students").style.display = 'inline';
    	document.getElementById("submit").style.display = 'inline';
    	document.getElementById("TourneyID").innerHTML = '<input type="hidden" id="TID" value="' + document.getElementById("Tournament").options[document.getElementById("Tournament").selectedIndex].value + '">';
    }
}
function OutsHideShow() {
    if ( document.getElementById("broke").checked ) {
        document.getElementById("Outs").style.display = 'inline';
    } else {
        document.getElementById("Outs").style.display = 'none';
    }
}
</script>
</body>
</html>
