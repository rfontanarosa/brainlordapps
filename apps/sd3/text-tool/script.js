/*
Author:		Marco Russo (Special-Man, Echizen)
Version:	November 16, 2014
Code:		JavaScript, HTML & CSS
Name:		Seiken Densetsu 3 / Secret of Mana 2 - Text-Tool
Website:	http://www.secretofmana2.de

This little tool is dedicated to the video game Seiken Densetsu 3 (also known as Secret of Mana 2), which
was released for the Super Nintendo Entertainment System on September 30, 1995 (exclusively) in Japan.
Once you write some text into the form, you can immediately see how it would actually look like in the
game. Note that not all characters are supported.
Together with the translation group G-Trans I released a complete German translation patch for the game.
Version 2.02 (February 7, 2014) is the newest official version to date.
*/

/* preload images into browser cache */
function imagesCache() {
	document.formular.input.value = "";

	var images = new Array(
		"32.gif", "33.gif", "39.gif", "40.gif", "41.gif", "44.gif", "45.gif", "46.gif", "48.gif", "49.gif",
		"50.gif", "51.gif", "52.gif", "53.gif", "54.gif", "55.gif", "56.gif", "57.gif", "58.gif", "63.gif",
		"65.gif", "66.gif", "67.gif", "68.gif", "69.gif", "70.gif", "71.gif", "72.gif", "73.gif", "74.gif",
		"75.gif", "76.gif", "77.gif", "78.gif", "79.gif", "80.gif", "81.gif", "82.gif", "83.gif", "84.gif",
		"85.gif", "86.gif", "87.gif", "88.gif", "89.gif", "90.gif", "97.gif", "98.gif", "99.gif", "100.gif",
		"101.gif", "102.gif", "103.gif", "104.gif", "105.gif", "106.gif", "107.gif", "108.gif", "109.gif",
		"110.gif", "111.gif", "112.gif", "113.gif", "114.gif", "115.gif", "116.gif", "117.gif", "118.gif",
		"119.gif", "120.gif", "121.gif", "122.gif", "196.gif","214.gif", "220.gif", "223.gif", "228.gif",
		"246.gif", "252.gif", "bg0.png", "bg1.png", "bg2.png", "bg3.png", "bg4.png", "bg5.png",
		"bgx.png", "bgz.png", "window.png", "back.gif", "next.gif"
	);

	for (var i = 0; i < images.length; i++) {
		var image = new Image();
		image.src = "images/" + images[i];
	}
}

/* show text and image after 22 seconds in case of no text input */
function inputCheck() {
	if ((document.formular.input.value == "") && (timeout == 0)) {
		document.formular.input.value = " \n  What are you waiting for? :-)\n ";
		document.getElementById("bgimage").style.backgroundImage = "url(images/bgz.png)";
		document.getElementById("specinfo").innerHTML = "(22 seconds without text input)";
		bgcount = -1;
		work();
	}
}

/* change background picture */
function changeBg(x) {
	if (x == 1) {
		bgcount = (bgcount + 1) % 6;
	}
	else {
		bgcount = (bgcount + 5) % 6;
	}

	document.getElementById("bgimage").style.backgroundImage = "url(images/bg" + bgcount + ".png)";
	document.getElementById("specinfo").innerHTML = "Background-Picture: " + (bgcount + 1) + " of 6";
}

/* show random choosen quote in game screen window and text input formular */
function chooseText() {
	do {
		rand1 = Math.round((Math.random() * (randomText.length - 1)));
	}
	while (rand1 == rand2)

	document.formular.input.value = randomText[rand1];
	document.getElementById("specinfo").innerHTML = "Random text: " + (rand1 + 1) + " of " + randomText.length;
	rand2 = rand1;
	work();
}

/* clear everything */
function clearWork() {
	document.getElementById("chars").innerHTML = "";
	document.getElementById("counter1").innerHTML = "";
	document.getElementById("counter2").innerHTML = "";
	document.getElementById("counter3").innerHTML = "";
	document.getElementById("alert").innerHTML = "";
	document.getElementById("specinfo").innerHTML = "";
}

/* show written text from input formular in game screen window */
function work() {
	timeout = 1;
	var i = 0;
	var j = 0;
	var k = "";
	var l = "";
	var alert = "";
	var picture = "";
	var picturestring = "";
	var counter = new Array(2);
	var counterstring = new Array(2);

	for (i = 0; i <= 2; i++) {
		counter[i] = 0;
		counterstring[i] = "";
	}

	for (i = 0; i < document.formular.input.value.length; i++) {
		l = document.formular.input.value.charAt(i);
		picture = "";

		if (hashcharlist[l] > 0) {
			counter[j] += hashcharlist[l];
			picture = "<img src=\"images/" + l.charCodeAt() + ".gif\">";
		}
		else if (l == "\n") {
			picture = "<img src=\"images/32.gif\"><br>";
			j++;
		}
		else if (document.formular.input.value.charCodeAt(i) != 13) {
			k += l;
			alert = "Unsupported character(s): " + k;
		}

		if (counter[j] <= 239) {
			counterstring[j] = "Line " + (j + 1) + ": " + counter[j] + " pixel";

			if (picture != "") {
				picturestring += picture;
			}
		}
		else {
			counterstring[j] = "<div class=\"redtext\">Line " + (j + 1) + ": " + counter[j] + " pixel</div>";
		}
	}

	for (i = 0; i <= 2; i++) {
		document.getElementById("counter" + (i + 1)).innerHTML = counterstring[i];
	}

	document.getElementById("chars").innerHTML = picturestring;
	document.getElementById("alert").innerHTML = alert;

	/* show easter egg ;-) */
	if ((k == "$") && (l == "?")) {
		document.formular.input.value = "Greetings to all my friends!\nI am glad to have you by my side!";
		document.getElementById("bgimage").style.backgroundImage = "url(images/bgx.png)";
		document.getElementById("specinfo").innerHTML = "Don't look here. Read the text! :-)";
		bgcount = -1;
		work();
	}
}

/* main / global variables */
setTimeout("inputCheck()", 22000);
var timeout = 0;
var bgcount = 0;
var rand1 = 0;
var rand2 = 0;
var randomText = new Array(
	"Wer k�mpft, kann verlieren.\nWer nicht k�mpft, hat schon\nverloren. - (Bertolt Brecht)",
	"Die Kunst ist, einmal mehr\naufzustehen, als man umgeworfen\nwird. - (Winston Churchill)",
	"Sage nicht immer, was du wei�t,\naber wisse immer, was du sagst.\n- (Matthias Claudius)",
	"Fantasie ist wichtiger als Wissen,\ndenn Wissen ist begrenzt.\n- (Albert Einstein)",
	"Wer immer tut, was er schon\nkann, bleibt immer das, was er\nschon ist. - (Henry Ford)",
	"F�r die Welt bist du irgendjemand,\naber f�r irgendjemand bist du die\nWelt. - (Erich Fried)",
	"Man muss das Unm�gliche\nversuchen, um das M�gliche zu\nerreichen. - (Hermann Hesse)",
	"Was du liebst, lass frei.\nKommt es zur�ck, geh�rt es dir\nf�r immer. - (Konfuzius)",
	"Mitleid bekommt man geschenkt,\nNeid muss man sich verdienen.\n- (Robert Lembke)",
	"Willst du den Charakter eines\nMenschen erkennen, so gib ihm\nMacht. - (Abraham Lincoln)",
	"Wer glaubt, etwas zu sein, hat\naufgeh�rt, etwas zu werden.\n- (Philip Rosenthal)",
	"Stell dir vor, es ist Krieg, und\nkeiner geht hin. - (Carl Sandburg)",
	"Ihr aber seht und sagt: Warum?\nAber ich tr�ume und sage: Warum\nnicht? - (George Bernard Shaw)",
	"Man kann niemanden �berholen,\nwenn man in seine Fu�stapfen\ntritt. - (Francois Truffaut)",
	"Wer der Schnellste sein will, muss\nsich viel Zeit nehmen, es zu\nwerden. - (Bernhard von Mutius)",
	"Wer etwas f�r die Breite tun\nm�chte, der muss die Spitze\nentwickeln. - (Theo Zwanziger)"
);

var charlist = new Array();
charlist[0] = new Array("ABCDEFGHJKLNPRSUVabcdeghknopqrstuvyz23456789������-?", 8);
charlist[1] = new Array("MOQTWXYZmwx0�", 9);
charlist[2] = new Array("il.:", 3);
charlist[3] = new Array("I!()", 5);
charlist[4] = new Array("fj1", 7);
charlist[5] = new Array(",'", 4);
charlist[6] = new Array(" ", 6);

var hashcharlist = new Array();
for (var i = 0; i < charlist.length; i++) {
	for (var j = 0; j < charlist[i][0].length; j++) {
		hashcharlist[charlist[i][0].charAt(j)] = charlist[i][1];
	}
}