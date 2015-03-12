<?php
include "CommonFunctions.php";
$myfile = fopen("/var/log/forensics/general.log","a");
if ( $GLOBALS['UserName'] != "" ) {
	fwrite($myfile, "User " . $GLOBALS['UserName'] . " from ");
}
fwrite($myfile, "IP " . $_SERVER['REMOTE_ADDR'] . " accessed " . basename($_SERVER['PHP_SELF']) . " on " . date('Y-m-d') . " at " . date('H:i:s') . " (Request for Info)\n");
fclose($myfile);
$tbl = "Tournaments";
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
	} elseif ( $_POST['OrderBy'] == "EventPRank" ) {
		$OString = " order by EName, PScore, PQual desc, FScore, Name, Date, Judge asc, Round";
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
		$WString = $WString . "(Results.SID='" . $_POST['SID'] . "' or Results.SID2='" . $_POST['SID'] . "')";
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
	$NumRoundQuery = mysqli_query($DBConn, "SELECT max(Round) as Rd, max(Judge) as Jdg FROM Tournaments, Results, Ballots, Events, Students " . $WString . ";");
	if ( !$NumRoundQuery ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . " on Judge " . $x . ".";
		return 0;
	}
	$Data = mysqli_fetch_assoc($NumRoundQuery);
	$NumRounds = $Data['Rd'];
	$NumJudges = $Data['Jdg'];
	$query = mysqli_query($DBConn, 'select SID2, Results.RID as RID, concat(LName, ", ", FName) as Name, PRanks / NumberRounds as PScore, PQuals/ NumberRounds as PQual, FRanks / NumberJudges as FScore, TName, EName, Rank, Qual, Judge, Round, broke, State, place from Students, Events, Tournaments, Results, Ballots' . $WString . $OString . ";");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	echo '<table id="Results-Table" border="1" style="border-collapse: collapse;"><tr>';
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
	$NumRows = mysqli_num_rows($query);
	$RID = "";
	$On = "Prelim";
	$x = 1;
	$y = 1;
	$z = 1;
	$First = 1;
	while ( $z <= $NumRows ) {
		$Data = mysqli_fetch_assoc($query);
		if ( $Data['RID'] != $RID ) {
			if ( $First == 0 ) {
				if ( $CellsLeft > $NumJudges ) {
					for ( $y = 1; $y < $CellsLeft - $NumJudges; $y++ ){
						echo '<td></td>';
					}
					if ( $_POST['ToCol'] == '1' ) {
						echo '<td>' . $TotalR . "/" . $TotalQ . '</td>';
					}
					$CellsLeft = $NumJudges;
				}
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
				if ( $Data['SID2'] != NULL ) {
					$NameQuery = mysqli_query($DBConn, "select concat(LName, ', ', FName) as Name from Students where SID='" . $Data['SID2'] . "';");
					$NameData = mysqli_fetch_assoc($NameQuery);
					echo '<td>' . $Data['Name'] . ' and ' . $NameData['Name'] . '</td>';
				} else {
					echo '<td>' . $Data['Name'] . '</td>';
				}
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
				if ( $Data['place'] == 1 ) {
					$place = "1st";
				} elseif ( $Data['place'] == 2 ) {
					$place = "2nd";
				} elseif ( $Data['place'] == 3 ) {
					$place = "3rd";
				} else {
					$place = $Data['place'] . "th";
				}
				echo '<td>' . $place . '</td>';
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
			} elseif ( $Data['Judge'] != NULL ) {
				$num = $x;
				for ( $x = $num; $x <= $NumRounds; $x++ ) {
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
			if ( $x >= $NumRounds ) {
				$On = "Final";
				if ( $Data['Judge'] != NULL ) {
					$x = 1;
					$DoFinal = 1;
				} else {
					$x = 0;
					$DoFinal = 0;
				}
				if ( $_POST['ToCol'] == '1' ) {
					echo '<td>' . $TotalR . "/" . $TotalQ . '</td>';
				}
				if ( $_POST['JCol'] == '1' ) {
					$CellsLeft = $NumJudges;
				} else {
					$CellsLeft = 0;
				}
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
					echo '<td>' . $Data['Rank'] . '</td>';
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
	return 0;
} elseif ( isset($_POST['Tournaments']) ) {
	if ( isset($_POST['IncludeAll']) ) {
		$Result = Tournaments(1);
	} else {
		$Result = Tournaments(0);
	}
	echo $Result;
	return 0;
} elseif ( isset($_POST['Students']) ) {
	if ( isset($_POST['IncludeAll']) ) {
		$Result = Students(1);
	} else {
		$Result = Students(0);
	}
	echo $Result;
	return 0;
} elseif ( isset($_POST['TID']) ) {
	$query = mysqli_query($DBConn, "select NumRounds, NumFinalsJudges from Tournaments where TID=" . $_POST['TID'] . ";");
	$data = mysqli_fetch_assoc($query);
	echo $data['NumRounds'] . "|" . $data['NumFinalsJudges'];
	return 0;
} elseif ( isset($_POST['Events']) ) {
	if ( isset($_POST['IncludeAll']) ) {
		$Return = Events(1);
	} else {
		$Return = Events(0);
	}
	echo $Return;
	return 0;
}
?>
<html>
<head><title>Display Results</title>
<link rel="stylesheet" type="text/css" href="Styles.css">
<meta charset="UTF-8">
<link rel="stylesheet" media="(max-width: 800px)" href="MobileStyles.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<h1><div id="Header" style="width: 100%;">Select Results To Be Returned</div></h1>
<?php MakeHeader(); ?>
<div style="display: float; float: left;">
<h3>Select Results From:</h3>
<b>Tournament:</b> <div id="Tourneys"><?php echo Tournaments(1); ?></div><br>
<b>Student:</b> <div id="Students"><?php echo Students(1); ?></div><br>
<b>Event:</b> <div id="Events"><?php echo Events(1); ?></div><br>
<div><b>Order By:</b> <select id="OrderBy"><option value="DateName">Date then Name then Event</option><option value="DateEvent">Date then Event then Name</option><option value="NameDate">Name then Date then Event</option><option value="NameEvent">Name then Event then Date</option><option value="EventDate">Event then Date then Name</option><option value="EventName">Event then Name then Date</option><option value="EventPRank">Event then Average Ranks then Name</option></select></div><br>
<div><b>Broke:</b> <select id="broke"><option value="2">Both</option><option value="1">Yes</option><option value="0">No</option></select></div><br>
<div><b>State Qual:</b> <select id="State"><option value="2">Both</option><option value="1">Yes</option><option value="0">No</option></select></div><br>
<div><input type="submit" onclick="SubmitInfo();" value="Show Results"></div>
</div>
<div id="Selection" style="display: float; padding-left: 20;"><br>
    <table style="padding-left: 30;">
        <tr><th><h3>Information to Display:</h3></th></tr>
        <tr><th style="padding-right: 15; text-align: left;">Entry Information:</th><th style="text-align: left;">Results:</th></tr>
        <tr><td><input type="checkbox" id="CName" checked>Name</td><td><input type="checkbox" id="CPrelims" checked>Prelim Scores</td></tr>
        <tr><td><input type="checkbox" id="CTournament" checked>Tournament</td><td><input type="checkbox" id="CFinals" checked>Finals Scores</td></tr>
        <tr><td><input type="checkbox" id="CEvent" checked>Event</td><td><input type="checkbox" id="CTotals" dir="" checked>Totals</td></tr>
        <tr><td><input type="checkbox" id="CBroke" dir="" checked>Broke</td><td><input type="checkbox" id="CPlace" checked>Place</td></tr>
        <tr><td><input type="checkbox" id="CQual" checked>State Qualifier</td><td></td></tr>
    </table>
</div>
</form>
<div style="width: 100%; display: float; float: left;"></div>
<br><br>
<div id="Results" style="width: 100%; display: float; float: left;"></div>
<script>
function SubmitInfo() {
    //event.preventDefault();
    document.getElementById("Results").innerHTML = "Loading...";
    OrderBy = document.getElementById("OrderBy").options[document.getElementById("OrderBy").selectedIndex].value;
    PostString = "OrderBy=" + OrderBy;
    TID = document.getElementById("Tournament").options[document.getElementById("Tournament").selectedIndex].value;
    if ( TID != "-1" ) {
        PostString = PostString + "&TID=" + TID;
    }
    EID = document.getElementById("Event").options[document.getElementById("Event").selectedIndex].value;
    if ( EID != "-1" ) {
        PostString = PostString + "&EID=" + EID;
    }
    SID = document.getElementById("Student").options[document.getElementById("Student").selectedIndex].value;
    if ( SID != "-1" ) {
        PostString = PostString + "&SID=" + SID;
    }
    broke = document.getElementById("broke").options[document.getElementById("broke").selectedIndex].value;
    PostString = PostString + "&broke=" + broke;
    State = document.getElementById("State").options[document.getElementById("State").selectedIndex].value;
    PostString = PostString + "&State=" + State;
    if ( document.getElementById("CName").checked ) {
        PostString = PostString + "&NCol=1";
    } else {
        PostString = PostString + "&NCol=0";
    }
    if ( document.getElementById("CTournament").checked ) {
        PostString = PostString + "&TCol=1";
    } else {
        PostString = PostString + "&TCol=0";
    }
    if ( document.getElementById("CEvent").checked ) {
        PostString = PostString + "&ECol=1";
    } else {
        PostString = PostString + "&ECol=0";
    }
    if ( document.getElementById("CBroke").checked ) {
        PostString = PostString + "&BCol=1";
    } else {
        PostString = PostString + "&BCol=0";
    }
    if ( document.getElementById("CQual").checked ) {
        PostString = PostString + "&QCol=1";
    } else {
        PostString = PostString + "&QCol=0";
    }
    if ( document.getElementById("CPrelims").checked ) {
        PostString = PostString + "&RCol=1";
    } else {
        PostString = PostString + "&RCol=0";
    }
    if ( document.getElementById("CFinals").checked ) {
        PostString = PostString + "&JCol=1";
    } else {
        PostString = PostString + "&JCol=0";
    }
    if ( document.getElementById("CTotals").checked ) {
        PostString = PostString + "&ToCol=1";
    } else {
        PostString = PostString + "&ToCol=0";
    }
    if ( document.getElementById("CPlace").checked ) {
        PostString = PostString + "&PCol=1";
    } else {
        PostString = PostString + "&PCol=0";
    }
    if ( window.XMLHttpRequest ) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("POST","TournamentInfo.php",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(PostString);
    xmlhttp.onreadystatechange=function() {
    	response = xmlhttp.responseText;
    	document.getElementById("Results").innerHTML = response;
    }
}
</script>
</body>
</html>
