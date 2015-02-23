<?php
include "MySQLAuth.php";
$DB = "Forensics_2015";
$tbl = "Tournaments";
$HOST = "localhost";
$DBConn = mysql_connect($HOST, $UN, $PW);
mysql_select_db($DB, $DBConn);
if ( isset($_POST['OrderBy']) ) {
	if ( ! isset($_POST['broke']) ) {
		echo "Error - No break selection.";
		return 0;
	} elseif ( ! isset($_POST['State']) ) {
		echo "Error - No State Qualifier selection.";
		return 0;
	}
	if ( $_POST['OrderBy'] == "NameDate" ) {
		$OString = " order by Name, Date, EName, Judge asc, Round";
	} elseif ( $_POST['OrderBy'] == "NameEvent" ) {
		$OString = " order by Name, EName, Date, Judge asc, Round";
	} elseif ( $_POST['OrderBy'] == "EventName" ) {
		$OString = " order by EName, Name, Date, Judge asc, Round";
	} elseif ( $_POST['OrderBy'] == "EventDate" ) {
		$OString = " order by EName, Date, Name, Judge asc, Round";
	} elseif ( $_POST['OrderBy'] == "DateName" ) {
		$OString = " order by Date, Name, EName, Judge asc, Round";
	} elseif ( $_POST['OrderBy'] == "DateEvent" ) {
		$OString = " order by Date, EName, Name, Judge asc, Round";
	} else {
		echo "Error - No valid sorting parameter given.";
		return 0;
	}
	$WString = " where Results.SID = Students.SID and Results.TID = Tournaments.TID and Results.RID = Ballots.RID and Events.EID = Results.EID";
	if ( isset($_POST['TID']) ) {
		if ( ! $WString == "" ) {
			$WString = $WString . " and ";
		} else {
			$WString = " where ";
		}
		$WString = $WString . "Results.TID='" . $_POST['TID'] . "'";
	}
	if ( isset($_POST['SID']) ) {
		if ( ! $WString == "" ) {
			$WString = $WString . " and ";
		} else {
			$WString = " where ";
		}
		$WString = $WString . "Results.SID='" . $_POST['SID'] . "'";
	}
	if ( isset($_POST['EID']) ) {
		if ( ! $WString == "" ) {
			$WString = $WString . " and ";
		} else {
			$WString = " where ";
		}
		$WString = $WString . "Results.EID='" . $_POST['EID'] . "'";
	}
	if ( $_POST['broke'] != "2" ) {
		if ( ! $WString == "" ) {
			$WString = $WString . " and ";
		} else {
			$WString = " where ";
		}
		$WString = $WString . "Results.broke='" . $_POST['broke'] . "'";
	}
	if ( $_POST['State'] != "2" ) {
		if ( ! $WString == "" ) {
			$WString = $WString . " and ";
		} else {
			$WString = " where ";
		}
		$WString = $WString . "Results.State='" . $_POST['State'] . "'";
	}
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
	echo '<table border="1" style="border-collapse: collapse;"><tr>';
	if ( $_POST['NCol'] == '1' ) {
		echo '<th>Name</th>';
	}
	if ( $_POST['ECol'] == '1' ) {
		echo '<th>Event</th>';
	}
	if ( $_POST['TCol'] == '1' ) {
		echo '<th>Tournament</th>';
	}
	if ( $_POST['BCol'] == '1' ) {
		echo '<th>Broke</th>';
	}
	if ( $_POST['QCol'] == '1' ) {
		echo '<th>Qualified</th>';
	}
	if ( $_POST['PCol'] == '1' ) {
		echo '<th>Place</th>';
	}
	if ( $_POST['RCol'] == '1' ) {
		for ( $x = 1; $x <= $NumRounds; $x++ ) {
			echo '<th>Round ' . $x . "</th>";
		}
	}
	if ( $_POST['ToCol'] == '1' ) {
		echo '<th>Prelims</th>';
	}
	if ( $_POST['JCol'] == '1' ) {
		for ( $x = 1; $x <= $NumJudges; $x++ ) {
			echo '<th>Judge ' . $x . "</th>";
		}
	}
	if ( $_POST['ToCol'] == '1' ) {
		echo '<th>Total</th>';
	}
	$NumRows = mysql_num_rows($query);
	$RID = "";
	$On = "Prelim";
	$x = 1;
	$y = 1;
	$z = 1;
	$First = 1;
	while ( $z <= $NumRows ) {
		$Data = mysql_fetch_assoc($query);
		if ( $Data['RID'] != $RID ) {
			if ( $First == 0 ) {
				for ( $y = 1; $y <= $CellsLeft; $y++ ){
					echo '<td></td>';
				}
				if ( $_POST['ToCol'] == '1' ) {
					echo '<td>' . $TotalR . "/" . $TotalQ . '</td>';
				}
			}
			$First = 0;
			echo '</tr>
<tr>';
			if ( $_POST['NCol'] == '1' ) {
				echo '<td>' . $Data['Name'] . '</td>';
			}
			if ( $_POST['ECol'] == '1' ) {
				echo '<td>' . $Data['EName'] . '</td>';
			}
			if ( $_POST['TCol'] == '1' ) {
				echo '<td>' . $Data['TName'] . '</td>';
			}
			if ( $_POST['BCol'] == '1' ) {
				if ( $Data['broke'] == "1" ) {
					echo '<td>yes</td>';
				} else {
					echo '<td>no</td>';
				}
			}
			if ( $_POST['QCol'] == '1' ) {
				if ( $Data['State'] == "1" ) {
					echo '<td>yes</td>';
				} else {
					echo '<td>no</td>';
				}
			}
			if ( $_POST['PCol'] == '1' ) {
				echo '<td>' . $Data['place'] . '</td>';
			}
			$RID = $Data['RID'];
			$x = 1;
			$On = "Prelim";
			$TotalR = 0;
			$TotalQ = 0;
		}
		$DoFinal = 1;
		if ( $On == "Prelim" ) {
			if ( $Data['Round'] == $x ) {
				if ( $_POST['RCol'] == '1' ) {
					echo '<td>' . $Data['Rank'] . "/" . $Data['Qual'] . "</td>";
				}
				$TotalR = $TotalR + $Data['Rank'];
				$TotalQ = $TotalQ + $Data['Qual'];
			} elseif ( $x < $NumRounds ) {
				for ( $x = $x; $x <= $NumRounds; $x++ ) {
					echo '<td></td>';
				}
			}
			$CellsLeft = ($NumRounds - $x);
			if ( $_POST['JCol'] == '1' ) {
				$CellsLeft = $CellsLeft + $NumJudges;
			}
			if ( $_POST['ToCol'] == '1' ) {
				$CellsLeft = $CellsLeft + 1;
			}
			if ( $x == $NumRounds ) {
				$On = "Final";
				$x = 0;
				if ( $_POST['ToCol'] == '1' ) {
					echo '<td>' . $TotalR . "/" . $TotalQ . '</td>';
				}
				if ( $_POST['JCol'] == '1' ) {
					$CellsLeft = $NumJudges;
				} else {
					$CellsLeft = 0;
				}
				$DoFinal = 0;
			}
		}
		if ( $DoFinal == 1 && $On == "Final" ) {
			if ( $x == 1 && $CellsLeft > $NumJudges ) {
				for ( $y = 1; $y <= $CellsLeft - $NumJudges; $y++ ) {
					echo '<td></td>';
				}
			}
			if ( $Data['Judge'] == $x ) {
				if ( $_POST['JCol'] == '1' ) {
					echo '<td>' . $Data['Rank'] . "/" . $Data['Qual'] . "</td>";
				}
				$TotalR = $TotalR + $Data['Rank'];
				$TotalQ = $TotalQ + $Data['Qual'];
			}
			if ( $_POST['JCol'] == '1' ) {
				$CellsLeft = $NumJudges - $x;
			} else {
				$CellsLeft = 0;
			}
			if ($x == $NumJudges) {
				$On = "Prelim";
				$x = 0;
			}
		}
		$x = $x + 1;
		$z = $z + 1;
	}
	for ( $x = 1; $x <= $CellsLeft; $x++ ) {
		echo '<td></td>';
	}
	if ( $_POST['ToCol'] == '1' ) {
		echo '<td>' . $TotalR . "/" . $TotalQ . '</td>';
	}
	echo "</tr></table>";
} elseif ( isset($_POST['Tournaments']) ) {
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
} else {
	echo "Error - Unknown parameters.";
}
?>
