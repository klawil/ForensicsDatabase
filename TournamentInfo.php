<?php
include "CommonFunctions.php";
$myfile = fopen("/var/log/forensics/general.log","a");
if ( $GLOBALS['UserName'] != "" ) {
	fwrite($myfile, "User " . $GLOBALS['UserName'] . " from ");
}
fwrite($myfile, "IP " . $_SERVER['REMOTE_ADDR'] . " accessed " . basename($_SERVER['PHP_SELF']) . " on " . date('Y-m-d') . " at " . date('H:i:s') . " (Request for Info)\n");
fclose($myfile);
$tbl = "Tournaments";
function CreateQueryString() {
	if ( ! isset($_POST['broke']) ) {
		echo "Error - No break selection.";
		return 0;
	} elseif ( ! isset($_POST['State']) ) {
		echo "Error - No State Qualifier selection.";
		return 0;
	}
	if ( $_POST['OrderBy'] == "NameDate" ) {
		$OString = " order by Name, Date, EName";
	} elseif ( $_POST['OrderBy'] == "NameEvent" ) {
		$OString = " order by Name, EName, Date";
	} elseif ( $_POST['OrderBy'] == "EventName" ) {
		$OString = " order by EName, Name, Date";
	} elseif ( $_POST['OrderBy'] == "EventDate" ) {
		$OString = " order by EName, Date, Name";
	} elseif ( $_POST['OrderBy'] == "DateName" ) {
		$OString = " order by Date, Name, EName";
	} elseif ( $_POST['OrderBy'] == "DateEvent" ) {
		$OString = " order by Date, EName, Name";
	} elseif ( $_POST['OrderBy'] == "EventPRank" ) {
		$OString = " order by EName, (PRanks / NumRounds), (PQuals / NumRounds) desc, (FRanks / NumberJudges), Name, Date";
	} else {
		echo "Error - No valid sorting parameter given.";
		return 0;
	}
	$WString = " where Results.SID = Students.SID and Results.TID = Tournaments.TID and Events.EID = Results.EID";
	if ( isset($_POST['TID']) && $_POST['TID'] != '-1' ) {
		if ( ! $WString == "" ) {
			$WString = $WString . " and ";
		} else {
			$WString = " where ";
		}
		$WString = $WString . "Results.TID='" . $_POST['TID'] . "'";
	}
	if ( isset($_POST['SID']) && $_POST['SID'] != '-1' ) {
		if ( ! $WString == "" ) {
			$WString = $WString . " and ";
		} else {
			$WString = " where ";
		}
		$WString = $WString . "(Results.SID='" . $_POST['SID'] . "' or Results.SID2='" . $_POST['SID'] . "')";
	}
	if ( isset($_POST['EID']) && $_POST['EID'] != '-1') {
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
	return $WString . $OString . "|" . $WString;
}
function CSVEncode($String) {
	$String = '"' . str_replace('"', '""', $String) . '"';
	return $String;
}
if ( isset($_POST['FileType']) ) {
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=Results.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	if ( $_POST['FileType'] != "CSV" ) {
		echo "Error - No protocol for " . $_POST['FileType'] . " file types.";
		return 0;
	}
	$Strings = CreateQueryString();
	if ( $Strings == "0" ) {
		return 0;
	}
	$Strings = explode("|",$Strings);
	$QString = $Strings[0];
	$WString = $Strings[1];
	$NumRoundQuery = mysqli_query($DBConn, "SELECT max(Round) as Rd, max(Judge) as Jdg FROM Tournaments, Results, Ballots, Events, Students " . $WString . ";");
	if ( !$NumRoundQuery ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	$Data = mysqli_fetch_assoc($NumRoundQuery);
	$NumRounds = $Data['Rd'];
	$NumJudges = $Data['Jdg'];
	$query = mysqli_query($DBConn, 'select SID2, RID, concat(LName, ", ", FName) as Name, PRanks, PQuals, FRanks, TName, EName, NumberRounds, NumberJudges, broke, State, place from Students, Events, Tournaments, Results' . $QString . ";");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	$First = 1;
	if ( $_POST['NCol'] == 'on' ) {
		if ( !$First ) {
			echo ',';
		} else {
			$First = 0;
		}
		echo CSVEncode('Name');
	}
	if ( $_POST['ECol'] == 'on' ) {
		if ( !$First ) {
			echo ',';
		} else {
			$First = 0;
		}
		echo CSVEncode('Event');
	}
	if ( $_POST['TCol'] == 'on' ) {
		if ( !$First ) {
			echo ',';
		} else {
			$First = 0;
		}
		echo CSVEncode('Tournament');
	}
	if ( $_POST['BCol'] == 'on' ) {
		if ( !$First ) {
			echo ',';
		} else {
			$First = 0;
		}
		echo CSVEncode('Broke');
	}
	if ( $_POST['QCol'] == 'on' ) {
		if ( !$First ) {
			echo ',';
		} else {
			$First = 0;
		}
		echo CSVEncode('Qualified');
	}
	if ( $_POST['PCol'] == 'on' ) {
		if ( !$First ) {
			echo ',';
		} else {
			$First = 0;
		}
		echo CSVEncode('Place');
	}
	if ( $_POST['RCol'] == 'on' ) {
		for ( $x = 1; $x <= $NumRounds; $x++ ) {
			if ( !$First ) {
				echo ',';
			} else {
				$First = 0;
			}
			echo CSVEncode('Round ' . $x);
		}
	}
	if ( $_POST['ToCol'] == 'on' ) {
		if ( !$First ) {
			echo ',';
		} else {
			$First = 0;
		}
		echo CSVEncode('Prelims');
	}
	if ( $_POST['JCol'] == 'on' ) {
		for ( $x = 1; $x <= $NumJudges; $x++ ) {
			if ( !$First ) {
				echo ',';
			} else {
				$First = 0;
			}
			echo CSVEncode('Judge ' . $x);
		}
	}
	if ( $_POST['ToCol'] == 'on' ) {
		if ( !$First ) {
			echo ',';
		} else {
			$First = 0;
		}
		echo CSVEncode('Total');
	}
	echo '
';
	$MasterNumRows = mysqli_num_rows($query);
	$RID = "";
	$z = 1;
	while ( $z <= $MasterNumRows ) {
		$Data = mysqli_fetch_assoc($query);
		$RID = $Data['RID'];
		$First = 1;
		if ( $_POST['NCol'] == 'on' ) {
			if ( !$First ) {
				echo ',';
			} else {
				$First = 0;
			}
			if ( $Data['SID2'] != NULL ) {
				$NameQuery = mysqli_query($DBConn, "select concat(LName, ', ', FName) as Name from Students where SID='" . $Data['SID2'] . "';");
				$NameData = mysqli_fetch_assoc($NameQuery);
				echo CSVEncode($Data['Name'] . ' and ' . $NameData['Name']);
			} else {
				echo CSVEncode($Data['Name']);
			}
		}
		if ( $_POST['ECol'] == 'on' ) {
			if ( !$First ) {
				echo ',';
			} else {
				$First = 0;
			}
			echo CSVEncode($Data['EName']);
		}
		if ( $_POST['TCol'] == 'on' ) {
			if ( !$First ) {
				echo ',';
			} else {
				$First = 0;
			}
			echo CSVEncode($Data['TName']);
		}
		if ( $_POST['BCol'] == 'on' ) {
			if ( !$First ) {
				echo ',';
			} else {
				$First = 0;
			}
			if ( $Data['broke'] == "1" ) {
				echo CSVEncode('yes');
			} else {
				echo CSVEncode('no');
			}
		}
		if ( $_POST['QCol'] == 'on' ) {
			if ( !$First ) {
				echo ',';
			} else {
				$First = 0;
			}
			if ( $Data['State'] == "1" ) {
				echo CSVEncode('yes');
			} else {
				echo CSVEncode('no');
			}
		}
		if ( $_POST['PCol'] == 'on' ) {
			if ( !$First ) {
				echo ',';
			} else {
				$First = 0;
			}
			$place = "";
			if ( $Data['place'] == 1 ) {
				$place = "1st";
			} elseif ( $Data['place'] == 2 ) {
				$place = "2nd";
			} elseif ( $Data['place'] == 3 ) {
				$place = "3rd";
			} elseif ( $Data['place'] != "" ) {
				$place = $Data['place'] . "th";
			}
			echo CSVEncode($place);
		}
		if ( $_POST['RCol'] == 'on' ) {
			$RQuery = mysqli_query($DBConn, "select Rank, Qual from Ballots where RID='" . $RID . "' and Round is not null order by Round;");
			if ( !$query ) {
				echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
				return 0;
			}
			$NumRows = mysqli_num_rows($RQuery);
			$CurrentRow = 1;
			while ( $CurrentRow <= $NumRows ) {
				if ( !$First ) {
					echo ',';
				} else {
					$First = 0;
				}
				$RData = mysqli_fetch_assoc($RQuery);
				echo CSVEncode($RData['Rank'] . "/" . $RData['Qual']);
				$CurrentRow++;
			}
			if ( $NumRows < $NumRounds ) {
				for ( $x = $NumRows; $x < $NumRounds; $x++ ) {
					if ( !$First ) {
						echo ',';
					} else {
						$First = 0;
					}
				}
			}
		}
		if ( $_POST['ToCol'] == 'on' ) {
			if ( !$First ) {
				echo ',';
			} else {
				$First = 0;
			}
			echo CSVEncode($Data['PRanks'] . '/' . $Data['PQuals']);
		}
		if ( $_POST['JCol'] == 'on' ) {
			$JQuery = mysqli_query($DBConn, "select Rank, Qual from Ballots where RID='" . $RID . "' and Judge is not null order by Round;");
			if ( !$JQuery ) {
				echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
				return 0;
			}
			$NumRows = mysqli_num_rows($JQuery);
			$CurrentRow = 1;
			while ( $CurrentRow <= $NumRows ) {
				if ( !$First ) {
					echo ',';
				} else {
					$First = 0;
				}
				$JData = mysqli_fetch_assoc($JQuery);
				echo CSVEncode($JData['Rank']);
				$CurrentRow++;
			}
			if ( $NumRows < $NumJudges ) {
				for ( $x = $NumRows; $x < $NumJudges; $x++ ) {
					if ( !$First ) {
						echo ',';
					} else {
						$First = 0;
					}
				}
			}
		}
		if ( $_POST['ToCol'] == 'on' ) {
			if ( !$First ) {
				echo ',';
			} else {
				$First = 0;
			}
			echo CSVEncode(($Data['FRanks'] + $Data['PRanks']) . '/' . $Data['PQuals']);
		}
		$z++;
		echo '
';
	}
	return 0;
} elseif ( isset($_POST['OrderBy']) ) {
	$Strings = CreateQueryString();
	if ( $Strings == "0" ) {
		return 0;
	}
	$Strings = explode("|",$Strings);
	$QString = $Strings[0];
	$WString = $Strings[1];
	$NumRoundQuery = mysqli_query($DBConn, "SELECT max(Round) as Rd, max(Judge) as Jdg FROM Tournaments, Results, Ballots, Events, Students " . $WString . ";");
	if ( !$NumRoundQuery ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	$Data = mysqli_fetch_assoc($NumRoundQuery);
	$NumRounds = $Data['Rd'];
	$NumJudges = $Data['Jdg'];
	$query = mysqli_query($DBConn, 'select SID2, RID, concat(LName, ", ", FName) as Name, PRanks, PQuals, FRanks, TName, EName, NumberRounds, NumberJudges, broke, State, place from Students, Events, Tournaments, Results' . $QString . ";");
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
	echo '</tr>';
	$MasterNumRows = mysqli_num_rows($query);
	$RID = "";
	$z = 1;
	while ( $z <= $MasterNumRows ) {
		$Data = mysqli_fetch_assoc($query);
		$RID = $Data['RID'];
		echo '<tr>';
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
			$place = "";
			if ( $Data['place'] == 1 ) {
				$place = "1st";
			} elseif ( $Data['place'] == 2 ) {
				$place = "2nd";
			} elseif ( $Data['place'] == 3 ) {
				$place = "3rd";
			} elseif ( $Data['place'] != "" ) {
				$place = $Data['place'] . "th";
			}
			echo '<td>' . $place . '</td>';
		}
		if ( $_POST['RCol'] == '1' ) {
			$RQuery = mysqli_query($DBConn, "select Rank, Qual from Ballots where RID='" . $RID . "' and Round is not null order by Round;");
			if ( !$query ) {
				echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
				return 0;
			}
			$NumRows = mysqli_num_rows($RQuery);
			$CurrentRow = 1;
			while ( $CurrentRow <= $NumRows ) {
				$RData = mysqli_fetch_assoc($RQuery);
				echo '<td>' . $RData['Rank'] . "/" . $RData['Qual'] . "</td>";
				$CurrentRow++;
			}
			if ( $NumRows < $NumRounds ) {
				for ( $x = $NumRows; $x < $NumRounds; $x++ ) {
					echo '<td></td>';
				}
			}
		}
		if ( $_POST['ToCol'] == '1' ) {
			echo '<td>' . $Data['PRanks'] . '/' . $Data['PQuals'] . '</td>';
		}
		if ( $_POST['JCol'] == '1' ) {
			$JQuery = mysqli_query($DBConn, "select Rank, Qual from Ballots where RID='" . $RID . "' and Judge is not null order by Round;");
			if ( !$JQuery ) {
				echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
				return 0;
			}
			$NumRows = mysqli_num_rows($JQuery);
			$CurrentRow = 1;
			while ( $CurrentRow <= $NumRows ) {
				$JData = mysqli_fetch_assoc($JQuery);
				echo '<td>' . $JData['Rank'] . "</td>";
				$CurrentRow++;
			}
			if ( $NumRows < $NumJudges ) {
				for ( $x = $NumRows; $x < $NumJudges; $x++ ) {
					echo '<td></td>';
				}
			}
		}
		if ( $_POST['ToCol'] == '1' ) {
			echo '<td>' . ($Data['FRanks'] + $Data['PRanks']) . '/' . $Data['PQuals'] . '</td>';
		}
		$z++;
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
	echo "Test";
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
<head><title>Display Results | ForensicsDB.com</title>
<link rel="stylesheet" type="text/css" href="Styles.css">
<meta charset="UTF-8">
<link rel="stylesheet" media="(max-width: 800px)" href="MobileStyles.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<h1><div id="Header" style="width: 100%;">Select Results To Be Returned</div></h1>
<?php MakeHeader(); ?>
<form id="QueryForm" action="TournamentInfo.php" method="post">
<div style="display: float; float: left;">
<h3>Select Results From:</h3>
<b>Tournament:</b> <div id="Tourneys"><?php echo Tournaments(1); ?></div><br>
<b>Student:</b> <div id="Students"><?php echo Students(1); ?></div><br>
<b>Event:</b> <div id="Events"><?php echo Events(1); ?></div><br>
<div><b>Order By:</b> <select name="OrderBy" id="OrderBy"><option value="DateName">Date then Name then Event</option><option value="DateEvent">Date then Event then Name</option><option value="NameDate">Name then Date then Event</option><option value="NameEvent">Name then Event then Date</option><option value="EventDate">Event then Date then Name</option><option value="EventName">Event then Name then Date</option><option value="EventPRank">Event then Average Ranks then Name</option></select></div><br>
<div><b>Broke:</b> <select name="broke" id="broke"><option value="2">Both</option><option value="1">Yes</option><option value="0">No</option></select></div><br>
<div><b>State Qual:</b> <select name="State" id="State"><option value="2">Both</option><option value="1">Yes</option><option value="0">No</option></select></div><br>
</div>
<div id="Selection" style="display: float; /*padding-left: 20;*/"><br>
    <table style="/*padding-left: 30;*/">
        <tr><th colspan="2" style="text-align: left;"><h3>Information to Display:</h3></th></tr>
        <tr><th style="padding-right: 15; text-align: left;">Entry Information:</th><th style="text-align: left;">Results:</th></tr>
        <tr><td><input type="checkbox" id="CName" name="NCol" checked>Name</td><td><input type="checkbox" id="CPrelims" name="RCol" checked>Prelim Scores</td></tr>
        <tr><td><input type="checkbox" id="CTournament" name="TCol" checked>Tournament</td><td><input type="checkbox" id="CFinals" name="JCol" checked>Finals Scores</td></tr>
        <tr><td><input type="checkbox" id="CEvent" name="ECol" checked>Event</td><td><input type="checkbox" id="CTotals" name="ToCol" checked>Totals</td></tr>
        <tr><td><input type="checkbox" id="CBroke" name="BCol" checked>Broke</td><td><input type="checkbox" id="CPlace" name="PCol" checked>Place</td></tr>
        <tr><td><input type="checkbox" id="CQual" name="QCol" checked>State Qualifier</td><td></td></tr>
    </table>
</div>
<div class="SubmitButton"><input type="button" onclick="SubmitInfo();" value="Show Results"> <input type="button" onclick="SubmitInfo('CSV');" value="Get as CSV"></div>
<input type="hidden" name="FileType" value="CSV">
</form>
<div style="width: 100%; display: float; float: left;"></div>
<br><br>
<div id="Results" style="width: 100%; display: float; float: left;"></div>
<script>
function SubmitInfo(FileType) {
	FileType = FileType || "";
    document.getElementById("Results").innerHTML = "Loading...";
    OrderBy = document.getElementById("OrderBy").options[document.getElementById("OrderBy").selectedIndex].value;
    PostString = "OrderBy=" + OrderBy;
    if ( FileType != "" ) {
    	document.getElementById("QueryForm").submit();
    }
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
