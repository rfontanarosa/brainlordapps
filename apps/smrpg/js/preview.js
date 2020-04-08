function smrpgTextClean(text) {
    text = text.replace(/\[1\]/g, "\n"); // New line
    text = text.replace(/\[2\]/g, "\n"); // New line, wait for input
    text = text.replace(/\[6\]/g, ""); // End string
    text = text.replace(/\[0\]/g, ""); // End string, wait for input
    text = text.replace(/\[7\]/g, ""); // Option triangle
    text = text.replace(/\[12\]/g, ""); // Pause 1 second
    text = text.replace(/\[5\]/g, ""); // Pause, wait for input
    text = text.replace(/\[42\]/g, "․"); // .
    text = text.replace(/\[43\]/g, "‥"); // ..
    return text;
}

function smrpgPreviewBox(previewContainerSelector, text, boxIndex, boxType) {
    var textArray = text.split(/\[3\]|\[4\]/g); // New page, wait for input / New page
    for (var j=0; j<textArray.length; j++) {
        dialogBox = '<div id="dialog-' + boxIndex + '-' + j + '" class="smrpg-dialogbox"><div class="bgimage"><div class="chars"></div></div><div class="infobox"><div class="counter1"></div><div class="counter2"></div><div class="counter3"></div><div class="alert"></div></div></div>';
        $('#' + previewContainerSelector).append(dialogBox);
        dialogSelector = '#dialog-' + boxIndex + '-' + j;

        $(dialogSelector).find('.chars').html('');
        $(dialogSelector).find('.counter1').html('');
        $(dialogSelector).find('.counter2').html('');
        $(dialogSelector).find('.counter3').html('');
        $(dialogSelector).find('.alert').html('');

        textDialog = textArray[j];
        textDialog = smrpgTextClean(textDialog);
        console.log(textDialog);

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
                picture = '<div class="smrpg-font1 smrpg-font1-' + l.charCodeAt() + '"></div>';
            } else if (l == "\n") {
                picture = "<br>";
                indexLine++;
            } else if (l.charCodeAt() !== 13) {
                k += l;
                alert = "Unsupported character(s): " + k;
            }
            // counter e counter string
            if (counter[indexLine] <= 239) {
                counterstring[indexLine] = "Line " + (indexLine + 1) + ": " + counter[indexLine] + " pixel";
                if (picture != "") {
                    picturestring += picture;
                }
            } else {
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

function smrpgPreview(previewContainerSelector, text) {
    $('#' + previewContainerSelector).empty();
    smrpgPreviewBox(previewContainerSelector, text, 0, 1);
}

var charlist = [];
charlist[1] = new Array("MW", 9);
charlist[0] = new Array("ARw&?‥", 8);
charlist[4] = new Array(" “”()023456789BCEGHKOPQUVXmv:;", 7);
charlist[6] = new Array("!.DFJNSTYZacdgknopqsuxyz․", 6);
charlist[3] = new Array("1Lbefhjrt", 5);
charlist[5] = new Array(",'", 4);
charlist[2] = new Array("Iil", 3);

var hashcharlist = [];
for (var i = 0; i < charlist.length; i++) {
    for (var j = 0; j < charlist[i][0].length; j++) {
        hashcharlist[charlist[i][0].charAt(j)] = charlist[i][1];
    }
}