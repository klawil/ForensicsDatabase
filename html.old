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
ChangedValues = new Array();
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
    	if ( xmlhttp.readyState == 4 ) {
			response = xmlhttp.responseText;
			if ( response == "true" ) {
				document.getElementById(RID + "M").innerHTML = "Deleted";
			} else {
				document.getElementById(RID + "M").innerHTML = response;
			}
		}
    }
}
function EditEntry(RID) {
	RID = RID || "asdf";
	if ( RID == "asdf" ) {
		window.alert("No result ID selected");
		return 0;
	}
	Elms1 = document.getElementById(RID).getElementsByTagName('select');
	Elms2 = document.getElementById(RID).getElementsByTagName('input');
	FString = "TID=" + TID + "&RID=" + RID;
	ChangedArray = new Array();
	//ChangedArray.push(1);
	for ( x = 0; x < Elms1.length; x++ ) {
		FString = FString + "&" + Elms1[x].name.replace("[" + RID + "]","") + "=" + Elms1[x].value;
		if ( typeof ChangedValues[Elms1[x].name] !== 'undefined' ) {
			ChangedValues[Elms1[x].name]['Default'] = Elms1[x].value;
			ChangedArray.push(Elms1[x].name);
		}
	}
	for ( x = 0; x < Elms2.length; x++ ) {
		if ( Elms2[x].type != "checkbox" ) {
			FString = FString + "&" + Elms2[x].name.replace("[" + RID + "]","") + "=" + Elms2[x].value;
			if ( typeof ChangedValues[Elms2[x].name] !== 'undefined' ) {
				ChangedValues[Elms2[x].name]['Default'] = Elms2[x].value;
				ChangedArray.push(Elms2[x].name);
				//window.alert(Elms2[x].name);
			}
		} else if ( Elms2[x].type == "checkbox" && !Elms2[x].checked ) {
			FString = FString + "&" + Elms2[x].name.replace("[" + RID + "]","") + "=0";
			if ( typeof ChangedValues[Elms2[x].name] !== 'undefined' ) {
				ChangedValues[Elms2[x].name]['Default'] = Elms2[x].checked;
				ChangedArray.push(Elms2[x].name);
				//window.alert(Elms2[x].name);
			}
		} else if ( Elms2[x].type == "checkbox" && Elms2[x].checked ) {
			FString = FString + "&" + Elms2[x].name.replace("[" + RID + "]","") + "=1";
			if ( typeof ChangedValues[Elms2[x].name] !== 'undefined' ) {
				ChangedValues[Elms2[x].name]['Default'] = Elms2[x].checked;
				ChangedArray.push(Elms2[x].name);
				window.alert(Elms2[x].name);
			}
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
    	if ( xmlhttp.readyState == 4 ) {
			response = xmlhttp.responseText;
			if ( response == "true" ) {
				document.getElementById(RID + "M").innerHTML = "Saved";
				for ( x in ChangedArray ) {
					ChangedValues[ChangedArray[x]]['Saved'] = true;
				}
			} else {
				document.getElementById(RID + "M").innerHTML = response;
			}
		}
    }
}
function ShowChange() {
	DString = ""
	for ( x in ChangedValues ) {
		DString = DString + x + ":" + ChangedValues[x]['Default'] + "->" + ChangedValues[x]['New'] + ":" + ChangedValues[x]['Saved'] + '\n';
	}
	window.alert(DString);
}
function StateChange(RID) {
	RIDM = RID.substring(RID.indexOf("[")+1,RID.indexOf("]")) + "M";
	Element = document.getElementsByName(RID);
	UnsavedChanges = true;
	HTMLElement = Element[0];
	if ( HTMLElement.type == "checkbox" ) {
		value = HTMLElement.checked;
	} else if ( HTMLElement.type == "select-one" ) {
		value = HTMLElement.selectedIndex;
	} else {
		value = HTMLElement.value;
	}
	if ( typeof ChangedValues[RID] !== 'undefined' ) {
		ChangedValues[RID]['New'] = value;
		if ( ChangedValues[RID]['New'] == ChangedValues[RID]['Default'] ) {
			ChangedValues[RID]['Saved'] = true;
		} else {
			ChangedValues[RID]['Saved'] = false;
		}
		if ( HTMLElement.type == "checkbox" && ChangedValues[RID]['Saved'] && ChangedValues[RID]['Default'] == HTMLElement.defaultChecked ) {
			delete ChangedValues[RID];
			UnsavedChanges = false;
			defaultVal = HTMLElement.defaultChecked;
		} else if ( HTMLElement.type == "select-one" ) {
			DefaultIndex = 0;
			while ( !HTMLElement.options[DefaultIndex].defaultSelected ){
				DefaultIndex = DefaultIndex + 1;
			}
			defaultVal = DefaultIndex;
			if ( ChangedValues[RID]['Saved'] && ChangedValues[RID]['Default'] == DefaultIndex ) {
				delete ChangedValues[RID];
				UnsavedChanges = false;
			}
		} else {
			defaultVal = HTMLElement.defaultValue;
			if ( ChangedValues[RID]['Saved'] && ChangedValues[RID]['Default'] == defaultVal ) {
				delete ChangedValues[RID];
				UnsavedChanges = false;
			}
		}
	} else {
		if ( HTMLElement.type == "checkbox" ) {
			defaultVal = HTMLElement.defaultChecked;
		} else if ( HTMLElement.type == "select-one" ) {
			DefaultIndex = 0;
			while ( !HTMLElement.options[DefaultIndex].defaultSelected ){
				DefaultIndex = DefaultIndex + 1;
			}
			defaultVal = DefaultIndex;
		} else {
			defaultVal = HTMLElement.defaultValue;
		}
		ChangedValues[RID] = {'Default': defaultVal, 'New': value, 'Saved': false};
	}
	if ( typeof ChangedValuse[RID] !== 'undefined' && ChangedValues[RID]['New'] == ChangedValues[RID]['Default'] ) {
		
	}
	if ( typeof ChangedValues[RID] !== 'undefined' && ChangedValues[RID]['New'] == ChangedValues[RID]['Default'] ) {
		ChangedValues[RID]['Saved'] = true;
		document.getElementById(RIDM).style.display = 'none';
	} else if ( typeof ChangedValues[RID] !== 'undefined' ) {
		document.getElementById(RIDM).style.display = 'inline';
		document.getElementById(RIDM).innerHTML = "Unsaved changes";
	}
}
function ChangeValues() {
	for ( x in ChangedValues ) {
		if ( document.getElementById(x).type == "select" ) {
			
			document.getElementById(x).selectedIndex = ChangedValues[x]['New'];
		} else if ( document.getElementById(x).type == "checkbox" ) {
			document.getElementById(x).checked = ChangedValues[x]['New'];
		} else {
			document.getElementById(x).value = ChangedValues[x]['New'];
		}
	}
}
function CreateEntry(RowID) {
	RowID = RowID || "asdf";
	if ( RowID == "asdf" ) {
		window.alert("No result ID selected");
		return 0;
	}
	Elms = document.getElementById("TEdit").elements;
	FString = "TID=" + TID + "&create=1";
	for ( x = 0; x < Elms.length; x++ ) {
		if ( Elms[x].name.indexOf("[" + RowID + "]") != '-1' ) {
			FString = FString + "&" + Elms[x].name.replace("[" + RowID + "]","") + "=" + Elms[x].value;
			Elms[x].defaultValue = Elms[x].value;
		}
	}
	document.getElementById(RowID + "M").innerHTML = "Submitting...";
	document.getElementById(RowID + "M").style.display = "inline";
	if ( window.XMLHttpRequest ) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("POST","TournamentEdit.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(FString);
	xmlhttp.onreadystatechange=function() {
		if ( xmlhttp.readyState == 4 ) {
			response = xmlhttp.responseText;
			if ( response.indexOf("true") > -1 ) {
				document.getElementById(RowID + "M").innerHTML = "Saved";
				RReg = new RegExp(RowID, 'g');
				response = response.split("|");
				document.getElementById(RowID).innerHTML = document.getElementById(RowID).innerHTML.replace(RReg,response[1]);
				document.getElementById(response[1] + "B").innerHTML = document.getElementById(response[1] + "B").innerHTML.replace("CreateEntry","EditEntry") + '<input type="button" onclick="DeleteRID(\'' + response[1] + '\');" value="Delete">';
				ChangeValues();
			} else {
				document.getElementById(RowID + "M").innerHTML = "Error: " + response;
			}
		}
	}
}
function AddRow(){
	RowHTML = document.getElementById("CloneRow").innerHTML;
	RowHTML = RowHTML.replace(/\[ROW\]/g,"[00" + NewNum + "]");
	RowHTML = RowHTML.replace(/NROWN/g,"00" + NewNum);
	document.getElementById("TEdit-Table").innerHTML = document.getElementById("TEdit-Table").innerHTML + '<tr id="00' + NewNum + '">' + RowHTML + '</tr>';
	NewNum = NewNum + 1;
	ChangeValues();
}
function MakePage() {
	ChangedValues = new Array();
	document.getElementById("TourneyEdit").innerHTML = "Loading Tournament...";
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
    	if ( xmlhttp.readyState == 4 ) {
			response = xmlhttp.responseText;
			document.getElementById("TourneyEdit").innerHTML = response;
		}
    }
}
</script>
</body>
</html>
