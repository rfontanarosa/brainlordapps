function sd3Cache() {
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
		image.src = "./images/preview/" + images[i];
	}
}

function sd3DialogClean(dialog) {
	dialog = dialog.replace(/<PAGE>\n/g, "<PAGE>");
	dialog = dialog.replace(/<END>\n/g, "<END>");
	return dialog;
}

function sd3TextClean(text) {
	text = text.replace(/<00>/g, "");
	text = text.replace(/<08>/g, "");
	text = text.replace(/<07>/g, "");
	text = text.replace(/<09>/g, "");
	text = text.replace(/<16>/g, "");
	text = text.replace(/<19>/g, "");
	text = text.replace(/<C2>/g, "");
	text = text.replace(/<C6>/g, "");
	text = text.replace(/<F3>/g, "");
	text = text.replace(/<BOX>/g, "");
	text = text.replace(/<OPEN>/g, "");
	text = text.replace(/<WAIT>/g, "");
	text = text.replace(/<MULTI>/g, "");
	text = text.replace(/<CHOICE>/g, "");
	text = text.replace(/<OR>/g, "");	
	//text = text.replace(/<PAGE>/g, "");
	text = text.replace(/<END>/g, "");
	text = text.replace(/<CLOSE>/g, "");
	text = text.replace(/<LINE>/g, "");
	text = text.replace(/<KEVIN>/g, "KEVIN");
	text = text.replace(/<CARLIE>/g, "CARLIE");
	return text;
}

function sd3Preview(dialogContainer, text) {
	$('#' + dialogContainer).empty();
	textArray = sd3DialogClean(text);
	textArray = textArray.split('<PAGE>');
	for (var j=0; j<textArray.length; j++) {
		dialogBox = '<div id="dialog' + j + '" class="sd3dialogbox"><div class="bgimage"><div class="window"><div class="chars"></div></div></div><div class="infobox"><div class="counter1"></div><div class="counter2"></div><div class="counter3"></div><div class="alert"></div></div></div>';
		$('#' + dialogContainer).append(dialogBox);
		dialogSelector = '#dialog' + j;

		$(dialogSelector).find('.chars').html('');
		$(dialogSelector).find('.counter1').html('');
		$(dialogSelector).find('.counter2').html('');
		$(dialogSelector).find('.counter3').html('');
		$(dialogSelector).find('.alert').html('');

		textDialog = textArray[j];
		textDialog = sd3TextClean(textDialog);

		var i = 0;
		var indexLine = 0;
		var k = "";
		var l = "";
		var alert = "";
		var picture = "";
		var picturestring = "";
		var counter = [0, 0, 0];
		var counterstring = ['', '', ''];

		for (i = 0; i < textDialog.length; i++) {
			l = textDialog.charAt(i);
			picture = "";
			if (hashcharlist[l] > 0) {
				counter[indexLine] += hashcharlist[l];
				picture = "<img src=\"./images/preview/" + l.charCodeAt() + ".gif\">";
			}
			else if (l == "\n") {
				picture = "<img src=\"./images/preview/32.gif\"><br>";
				indexLine++;
			}
			else if (textDialog.charAt(i) != 13) {
				k += l;
				alert = "Unsupported character(s): " + k;
			}
			// counter e counter string
			if (counter[indexLine] <= 239) {
				counterstring[indexLine] = "Line " + (indexLine + 1) + ": " + counter[indexLine] + " pixel";
				if (picture != "") {
					picturestring += picture;
				}
			}
			else {
				counterstring[indexLine] = "<div class=\"redtext\">Line " + (indexLine + 1) + ": " + counter[indexLine] + " pixel</div>";
			}
		}

		$(dialogSelector).find('.chars').html(picturestring);
		$(dialogSelector).find('.counter1').html(counterstring[0]);
		$(dialogSelector).find('.counter2').html(counterstring[1]);
		$(dialogSelector).find('.counter3').html(counterstring[2]);
		$(dialogSelector).find('.alert').html(alert);

	}

}

var charlist = [];
charlist[0] = new Array("ABCDEFGHJKLNPRSUVabcdeghknopqrstuvyz23456789àèéìòùÈ-?", 8);
charlist[1] = new Array("MOQTWXYZmwx0", 9);
charlist[2] = new Array("il.:", 3);
charlist[3] = new Array("I!()", 5);
charlist[4] = new Array("fj1", 7);
charlist[5] = new Array(",'", 4);
charlist[6] = new Array(" ", 6);

var hashcharlist = [];
for (var i = 0; i < charlist.length; i++) {
	for (var j = 0; j < charlist[i][0].length; j++) {
		hashcharlist[charlist[i][0].charAt(j)] = charlist[i][1];
	}
}