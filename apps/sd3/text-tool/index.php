<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Seiken Densetsu 3 / Secret of Mana 2</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta name="author" content="Marco Russo">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script type="text/javascript" src="script.js"></script>
</head>
<body onload="imagesCache()">
	<div id="main">
		<div id="title">
			Seiken Densetsu 3 / Secret of Mana 2
			<div id="version">
				Version: November 16, 2014
			</div>
		</div>
		<div id="float1">
			<img src="images/back.gif" alt="<-" onclick="changeBg(0)">
		</div>
		<div id="float2">
			<div id="bgimage" onclick="changeBg(1)">
				<div id="window">
					<div id="chars"></div>
				</div>
			</div>
			<form name="formular" action="script.js">
				<textarea name="input" cols="1" rows="1" onkeyup="work()"></textarea><br>
				<input type="button" value="Random Text" class="button" onClick="chooseText()">
				<input type="reset" value="Clear Screen" class="button" onclick="clearWork()">
			</form>
			<div id="infobox">
				<div id="counter1"></div>
				<div id="counter2"></div>
				<div id="counter3"></div>
				<div id="alert"></div>
				<div id="specinfo"></div>
			</div>
		</div>
		<div id="float3">
			<img src="images/next.gif" alt="->" onclick="changeBg(1)">
		</div>
		<div id="credit">
			by Marco Russo
		</div>
	</div>
</body>
</html>