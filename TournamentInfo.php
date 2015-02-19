<?php
$DB = "Forensics_2015";
$tbl = "Tournaments";
$UN = "root";
$PW = "Kt305@1K3t";
$HOST = "localhost";
$DBConn = mysql_connect($HOST, $UN, $PW);
mysql_select_db($DB, $DBConn);
if ( isset($_POST['Tournaments']) ) {
	//echo "Test";
	$query = mysql_query("select TName, TID from Tournaments order by TName;");
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	$TournamentString = '<select id="Tournament">';
	if ( isset($_POST['IncludeAll']) ) {
		$TournamentString = $TournamentString . "<option value='-1'>All Tournaments</option>";
	}
	while ( $CurrentRow < $NumRows ) {
		$results = mysql_fetch_assoc($query);
		$TournamentString = $TournamentString . '<option value="' . $results['TID'] . '">' . $results['TName'] . "</option>";
		$CurrentRow++;
	}
	$TournamentString = $TournamentString . "</select>";
	echo $TournamentString;
} elseif ( isset($_POST['Students']) ) {
	$query = mysql_query("select FName, LName, SID from Students order by LName, FName;");
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	$StudentString = '<select id="Student">';
	if ( isset($_POST['IncludeAll']) ) {
		$StudentString = $StudentString . "<option value='-1'>All Students</option>";
	}
	while ( $CurrentRow < $NumRows ) {
		$results = mysql_fetch_assoc($query);
		$StudentString = $StudentString . '<option value="' . $results['SID'] . '">' . $results['LName'] . ", " . $results['FName'] . "</option>";
		$CurrentRow++;
	}
	$StudentString = $StudentString . "</select>";
	echo $StudentString;
} elseif ( isset($_POST['TID']) ) {
	$query = mysql_query("select NumRounds, NumFinalsJudges from Tournaments where TID=" . $_POST['TID'] . ";");
	$data = mysql_fetch_assoc($query);
	echo $data['NumRounds'] . "|" . $data['NumFinalsJudges'];
} elseif ( isset($_POST['Events']) ) {
	$query = mysql_query("select * from Events order by EName;");
	if (( mysql_errno() )) {
		echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
		return 0;
	}
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	$EventString = '<select id="Event">';
	if ( isset($_POST['IncludeAll']) ) {
		$EventString = $EventString . "<option value='-1'>All Events</option>";
	}
	while ( $CurrentRow < $NumRows ) {
		$results = mysql_fetch_assoc($query);
		$EventString = $EventString . '<option value="' . $results['EID'] . '">' . $results['EName'] . "</option>";
		$CurrentRow++;
	}
	$EventString = $EventString . "</select>";
	echo $EventString;
} elseif ( isset($_GET['OrderBy']) ) {
	if ( ! isset($_GET['broke']) ) {
		echo "Error - No break selection.";
		return 0;
	} elseif ( ! isset($_GET['State']) ) {
		echo "Error - No State Qualifier selection.";
		return 0;
	}
	if ( $_GET['OrderBy'] == "NameDate" ) {
		$OString = " order by Name, Date, EName, Judge asc, Round";
	} elseif ( $_GET['OrderBy'] == "NameEvent" ) {
		$OString = " order by Name, EName, Date, Judge asc, Round";
	} elseif ( $_GET['OrderBy'] == "EventName" ) {
		$OString = " order by EName, Name, Date, Judge asc, Round";
	} elseif ( $_GET['OrderBy'] == "EventDate" ) {
		$OString = " order by EName, Date, Name, Judge asc, Round";
	} elseif ( $_GET['OrderBy'] == "DateName" ) {
		$OString = " order by Date, Name, EName, Judge asc, Round";
	} elseif ( $_GET['OrderBy'] == "DateEvent" ) {
		$OString = " order by Date, EName, Name, Judge asc, Round";
	} else {
		echo "Error - No valid sorting parameter given.";
		return 0;
	}
	$WString = " where Results.SID = Students.SID and Results.TID = Tournaments.TID and Results.RID = Ballots.RID and Events.EID = Results.EID";
	if ( isset($_GET['TID']) ) {
		if ( ! $WString == "" ) {
			$WString = $WString . " and ";
		} else {
			$WString = " where ";
		}
		$WString = $WString . "Results.TID='" . $_GET['TID'] . "'";
	}
	if ( isset($_GET['SID']) ) {
		if ( ! $WString == "" ) {
			$WString = $WString . " and ";
		} else {
			$WString = " where ";
		}
		$WString = $WString . "Results.SID='" . $_GET['SID'] . "'";
	}
	if ( isset($_GET['EID']) ) {
		if ( ! $WString == "" ) {
			$WString = $WString . " and ";
		} else {
			$WString = " where ";
		}
		$WString = $WString . "Results.EID='" . $_GET['EID'] . "'";
	}
	if ( $_GET['broke'] != "2" ) {
		if ( ! $WString == "" ) {
			$WString = $WString . " and ";
		} else {
			$WString = " where ";
		}
		$WString = $WString . "Results.broke='" . $_GET['broke'] . "'";
	}
	if ( $_GET['State'] != "2" ) {
		if ( ! $WString == "" ) {
			$WString = $WString . " and ";
		} else {
			$WString = " where ";
		}
		$WString = $WString . "Results.State='" . $_GET['State'] . "'";
	}
	//echo 'select Results.RID, concat(LName, ", ", FName) as Name, TName, EName, Rank, Qual, Judge, Round, broke, State, place, Date from Students, Events, Tournaments, Results, Ballots' . $WString . $OString . ";";
	if (( mysql_errno() )) {
		echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . " on Judge " . $x . ".";
		return 0;
	}
	$NumRoundQuery = mysql_query("SELECT max(Round) as Rd, max(Judge) as Jdg FROM Tournaments, Results, Ballots, Events, Students " . $WString . ";");
	if (( mysql_errno() )) {
		echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . " on Judge " . $x . ".";
		return 0;
	}
	$Data = mysql_fetch_assoc($NumRoundQuery);
	$NumRounds = $Data['Rd'];
	$NumJudges = $Data['Jdg'];
	$query = mysql_query('select Results.RID as RID, concat(LName, ", ", FName) as Name, TName, EName, Rank, Qual, Judge, Round, broke, State, place from Students, Events, Tournaments, Results, Ballots' . $WString . $OString . ";");
	//echo 'select Results.RID as RID, concat(LName, ", ", FName) as Name, TName, EName, Rank, Qual, Judge, Round, broke, State, place from Students, Events, Tournaments, Results, Ballots' . $WString . $OString . ";<br>
//";
	echo "<table><tr><td>Name</td><td>Event</td><td>Tournament</td><td>Broke</td><td>Qualified</td><td>Place</td>";
	for ( $x = 1; $x <= $NumRounds; $x++ ) {
		echo "<td>Round " . $x . "</td>";
	}
	for ( $x = 1; $x <= $NumJudges; $x++ ) {
		echo "<td>Judge " . $x . "</td>";
	}
	$NumRows = mysql_num_rows($query);
	$RID = "";
	$On = "Prelim";
	$x = 1;
	$y = 1;
	while ( $y <= $NumRows ) {
	//for ( $y = 1; $y <= $NumRows; $y++ ) {
		$Data = mysql_fetch_assoc($query);
		if ( $Data['RID'] != $RID ) {
			echo "</tr>
<tr><td>" . $Data['Name'] . "</td><td>" . $Data['EName'] . "</td><td>" . $Data['TName'] . "</td><td>";
			if ( $Data['broke'] == "1" ) {
				echo "yes</td><td>";
			} else {
				echo "no</td><td>";
			}
			if ( $Data['State'] == "1" ) {
				echo "yes</td><td>";
			} else {
				echo "no</td><td>";
			}
			echo $Data['place'] . "</td>";
			$RID = $Data['RID'];
			$x = 1;
			$On = "Prelim";
		}
		if ( $On == "Prelim" ) {
			if ( $Data['Round'] == $x ) {
				echo "<td>" . $Data['Rank'] . "/" . $Data['Qual'] . "</td>";
			}
			if ($x == $NumRounds) {
				$On = "Final";
				$x = 0;
			}
		} elseif ( $On == "Final" ) {
			if ( $Data['Judge'] == $x ) {
				echo "<td>" . $Data['Rank'] . "/" . $Data['Qual'] . "</td>";
			}
			if ($x == $NumJudges) {
				$On = "Prelim";
				$x = 0;
			}
		}
		$x = $x + 1;
		$y = $y + 1;
		//echo $x . "-" . $Data['Name'] . "<br>";
	}
	echo "</tr></table>";
} else {
	echo "Error - Unknown parameters.";
}
?>
