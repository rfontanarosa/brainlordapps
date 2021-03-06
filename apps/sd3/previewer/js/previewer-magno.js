function sd3TextClean(text) {
    //
    text = text.replace(/\n/g, "");
    text = text.replace(/<JUMP>/g, "\n");
    //
    text = text.replace(/<19><00>/g, "CHAR00");
    text = text.replace(/<19><01>/g, "CHAR01");
    text = text.replace(/<19><02>/g, "CHAR02");
    text = text.replace(/<19><03>/g, "CHAR03");
    text = text.replace(/<19><F8><00>/g, "DURAN6");
    text = text.replace(/<19><F8><01>/g, "KEVIN6");
    text = text.replace(/<19><F8><02>/g, "HAWK56");
    text = text.replace(/<19><F8><03>/g, "ANGELA");
    text = text.replace(/<19><F8><04>/g, "CARLIE");
    text = text.replace(/<19><F8><05>/g, "LISE56");
    text = text.replace(/<\+B.>/g, "");
    text = text.replace(/<\+J.>/g, "");
    text = text.replace(/<\+B_>/g, "");
    text = text.replace(/<\+J_>/g, "");
    text = text.replace(/<\+b_>/g, "");
    text = text.replace(/<1B><F5><..>/g, "ITEMXX");
    //
    text = text.replace(/<TAB ..>/g, ""); //TODO rimpiazzare con degli spazi
    text = text.replace(/<OPEN>/g, "");
    text = text.replace(/<WAIT>/g, "");
    text = text.replace(/<CLOSE>/g, "");
    text = text.replace(/<END>/g, "");
    //
    text = text.replace(/<..>/g, "");
    return text;
}

const BOX_BOX = 1;
const BOX_LINE = 2;

function sd3PreviewBox(previewContainerSelector, text, boxIndex, boxType) {

    if (boxType === 1) {
        dialogBox = '<div id="dialog-' + boxIndex + '" class="sd3dialogbox"><div class="bgimage"><div class="window"><div class="chars"></div></div></div><div class="infobox"><div class="counter1"></div><div class="counter2"></div><div class="counter3"></div><div class="alert"></div></div></div>';
    } else {
        dialogBox = '<div id="dialog-' + boxIndex + '" class="sd3dialogbox"><div class="bgimage"><div class="line"><div class="chars"></div></div></div><div class="infobox"><div class="counter1"></div><div class="counter2"></div><div class="counter3"></div><div class="alert"></div></div></div>';
    }

    $('#' + previewContainerSelector).append(dialogBox);

    dialogSelector = '#dialog-' + boxIndex;

    $(dialogSelector).find('.chars').empty('');
    $(dialogSelector).find('.counter1').empty('');
    $(dialogSelector).find('.counter2').empty('');
    $(dialogSelector).find('.counter3').empty('');
    $(dialogSelector).find('.alert').empty('');

    textDialog = sd3TextClean(text);

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
            picture = "<img src=\"./previewer/images/" + l.charCodeAt() + ".gif\" />";
        }
        else if (l === "\n") {
            picture = "<br />";
            indexLine++;
        }
        else if (textDialog.charCodeAt(i) != 13) {
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

function renderPreview(previewContainerSelector, text) {
    $('#' + previewContainerSelector).empty();
    const entries = text.split(/<WAIT><00><CLOSE><END>/g);
    entries.forEach((entry, index) => {
        console.log(entry, index);
        if (entry.includes("<58><OPEN><END><7B>")) {
            const textArray = entry.split('<END>');
            for (var j=0; j < textArray.length; j++) {
                sd3PreviewBox(previewContainerSelector, textArray[j], '' + index + j, BOX_BOX);
            }
        }
        else if (entry.includes("<58><OPEN>\n")) {
            const textArray = entry.split(/<..><PAUSE>\n/g);
            console.log(9999999, textArray);
            for (var j=0; j < textArray.length; j++) {
                sd3PreviewBox(previewContainerSelector, textArray[j], '' + index + j, BOX_BOX);
            }
        } else if (entry.includes("<5E><OPEN>\n")) {
            const textArray = entry.split(/<..><PAUSE>\n/g);
            for (var j=0; j < textArray.length; j++) {
                sd3PreviewBox(previewContainerSelector, textArray[j], '' + index + j, BOX_LINE);
            }
        }
    });
}

var charlist = [];
charlist[0] = new Array("/\"#$%&ABCDEFGHJKLNPRSUVabcdeghknopqrstuvyz23456789àèéìòùÈ-?", 8);
charlist[1] = new Array("*MOQTWXYZmwx0", 9);
charlist[2] = new Array("il.:", 3);
charlist[3] = new Array("I!()", 5);
charlist[4] = new Array("+fj1", 7);
charlist[5] = new Array(",'", 4);
charlist[6] = new Array(" ", 6);

var hashcharlist = [];
for (var i = 0; i < charlist.length; i++) {
    for (var j = 0; j < charlist[i][0].length; j++) {
        hashcharlist[charlist[i][0].charAt(j)] = charlist[i][1];
    }
}

function sd3Cache() {
    var images = new Array(
        "34.gif", "35.gif", "36.gif", "37.gif", "38.gif", "43.gif", "42.gif", "47.gif",
        "32.gif", "33.gif", "39.gif", "40.gif", "41.gif", "44.gif", "45.gif", "46.gif", "48.gif", "49.gif",
        "50.gif", "51.gif", "52.gif", "53.gif", "54.gif", "55.gif", "56.gif", "57.gif", "58.gif", "63.gif",
        "65.gif", "66.gif", "67.gif", "68.gif", "69.gif", "70.gif", "71.gif", "72.gif", "73.gif", "74.gif",
        "75.gif", "76.gif", "77.gif", "78.gif", "79.gif", "80.gif", "81.gif", "82.gif", "83.gif", "84.gif",
        "85.gif", "86.gif", "87.gif", "88.gif", "89.gif", "90.gif", "97.gif", "98.gif", "99.gif", "100.gif",
        "101.gif", "102.gif", "103.gif", "104.gif", "105.gif", "106.gif", "107.gif", "108.gif", "109.gif",
        "110.gif", "111.gif", "112.gif", "113.gif", "114.gif", "115.gif", "116.gif", "117.gif", "118.gif",
        "119.gif", "120.gif", "121.gif", "122.gif", "196.gif","214.gif", "220.gif", "223.gif", "228.gif",
        "246.gif", "252.gif", "bg0.png", "bg1.png", "bg2.png", "bg3.png", "bg4.png", "bg5.png",
        "bgx.png", "bgz.png", "window.png", "back.gif", "next.gif", "line.png"
    );
    for (var i = 0; i < images.length; i++) {
        var image = new Image();
        image.src = "./previewer/images/" + images[i];
    }
}

$(document).on('load', function() {
    sd3Cache();
});
