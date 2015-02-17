<html>
<head><title>Enter Tournament Information</title>
<style>
input[type="number"] {
   width:40px;
}
</style>
</head>
<body>
<form id="EntryID" action="TournamentUpdate.php">
<h1><div id="Header">Select a Tournament</div></h1>
<h3 id="Message"></h3>
<div id="Tourneys"></div><br>
<div id="Students"></div><br>
<div id="Events"></div><br>
<div id="Rounds"></div>
<div id="info" style="display: none;">
    <input type="checkbox" value="broke" onchange="OutsHideShow();" id="broke">Broke<br>
    <input type="checkbox" value="StateQual" id="Qual">Qualified for State<br>
</div>
<div id="Outs" style="display: none;"></div>
<div id="submit" style="display: none;"><input type="submit" onclick="SubmitInfo();" value="Submit"></div>
</form>
<script>
function Tournaments() {
    document.getElementById("Tourneys").innerHTML = "Loading...";
    if ( window.XMLHttpRequest ) {
        xmlhttp = new XMLHttpRequest();
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("POST","TournamentInfo.php",false);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("Tournaments=1");
    response = xmlhttp.responseText;
    document.getElementById("Tourneys").innerHTML = response + '   <input type="button" value="Select" onclick="MakePage();">';
}
function MakePage(){
    document.getElementById("Header").innerHTML = document.getElementById("Tournament").options[document.getElementById("Tournament").selectedIndex].text;
    Students();
    Events();
    TInfo = GetInfo(document.getElementById("Tournament").options[document.getElementById("Tournament").selectedIndex].value);
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
        HTMLString = HTMLString + "Judge " + x + ': <input type="number" id="J' + x + 'R"><input type="number" id="J' + x + 'Q"><br>';
    }
    HTMLString = HTMLString + 'Place: <input type="number" id="place"><br>';
    document.getElementById("Outs").innerHTML = HTMLString;
    document.getElementById("info").style.display = 'inline';
    document.getElementById("submit").style.display = 'inline';
    document.getElementById("Tourneys").innerHTML = '<input type="hidden" id="TID" value="' + document.getElementById("Tournament").options[document.getElementById("Tournament").selectedIndex].value + '">';
}
function OutsHideShow() {
    if ( document.getElementById("broke").checked ) {
        document.getElementById("Outs").style.display = 'inline';
    } else {
        document.getElementById("Outs").style.display = 'none';
    }
}
Tournaments();
</script>
</body>
</html>