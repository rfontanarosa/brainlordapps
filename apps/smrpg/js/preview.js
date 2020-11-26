function smrpgTextClean(text) {
    text = text.replace(/\[1\]/g, "\n"); // New line
    text = text.replace(/\[6\]/g, ""); // End string
    text = text.replace(/\[0\]/g, ""); // End string, wait for input
    text = text.replace(/\[7\]/g, ""); // Option triangle
    text = text.replace(/\[12\]/g, ""); // Pause 1 second
    text = text.replace(/\[5\]/g, ""); // Pause, wait for input
    text = text.replace(/\[36\]/g, "♥");
    text = text.replace(/\[37\]/g, "♪");
    text = text.replace(/\[42\]/g, "·");
    text = text.replace(/\[43\]/g, "‥");
    return text;
}

function smrpgPreviewBox(previewContainerSelector, text, boxIndex, boxType) {
    const previewContainer = $('#' + previewContainerSelector);
    text = text.replace(/\[13\]\[.\]/g, ""); // Pause?
    text = text.replace(/\[13\]\[..\]/g, ""); // Pause?
    text = text.replace(/\[28\]\[.\]/g, ""); // RAM?
    text = text.replace(/\[2\]/g, "\t[2]");
    text = text.replace(/\[3\]/g, "\t[3]");
    const dialogs = text.split(/\[2\]|\[3\]|\[4\]/g); // Wait for input, clean previous lines / New page, wait for input / New page
    dialogs.forEach((dialog, index) => {

        const dialogId = `dialog-${boxIndex}-${index}`;
        dialogBox = '<div id="' + dialogId + '" class="smrpg-dialogbox">\
            <div class="bgimage">\
                <div class="chars"></div>\
            </div>\
            <div class="infobox">\
                <div class="counter1"></div>\
                <div class="counter2"></div>\
                <div class="counter3"></div>\
                <div class="alert"></div>\
            </div>\
        </div>';
        previewContainer.append(dialogBox);

        dialog = smrpgTextClean(dialog);

        let indexLine = 0;
        let picturestring = '';
        const counterstring = ['', '', ''];
        let alert = '';
        const counter = [0, 0, 0];

        for (let i = 0; i < dialog.length; i++) {
            const l = dialog.charAt(i);
            let picture = "";
            if (hashcharlist[l] > 0) {
                counter[indexLine] += hashcharlist[l] + 1;
                picture = '<div class="smrpg-font1 smrpg-font1-' + l.charCodeAt() + '"></div>';
            } else if (l == "\n") {
                picture = "<br>";
                indexLine++;
            } else if (l == "\t") {
                picture = '<div class="newline_newpage_arrow"></div>';
            } else if (l.charCodeAt() !== 13) {
                alert += l;
            }
            if (picture != "") {
                picturestring += picture;
            }
        }

        for (let i = 0; i < counter.length; i++) {
            if (counter[i] <= 222) {
                counterstring[i] = "Line " + (i + 1) + ": " + counter[i] + " pixel";
            } else {
                counterstring[i] = "<div class=\"redtext\">Line " + (i + 1) + ": " + counter[i] + " pixel</div>";
            }
        }

        const dialogSelector = `#${dialogId}`;
        $(dialogSelector, previewContainer).find('.chars').html(picturestring);
        $(dialogSelector, previewContainer).find('.counter1').html(counterstring[0]);
        $(dialogSelector, previewContainer).find('.counter2').html(counterstring[1]);
        $(dialogSelector, previewContainer).find('.counter3').html(counterstring[2]);
        $(dialogSelector, previewContainer).find('.alert').html(alert !== '' ? `Unsupported character(s): ${alert}` : '');

    });

}

function renderPreview(previewContainerSelector, text) {
    document.getElementById(previewContainerSelector).innerHTML = '';
    smrpgPreviewBox(previewContainerSelector, text, 0, 1);
}

const charlist = [];
charlist.push(["MW…#+×%*", 9]);
charlist.push(["♥♪‥~?©ARÀw<>&", 8]);
charlist.push(["023456789BCEGHKOPQUVXÈÉmv:;ÒÙ", 7]);
charlist.push(["!“”·/DFJNSTYZacdgknopqsuxyzàòù", 6]);
charlist.push(["(),-.1Lbefhjrtèé", 5]);
charlist.push([" '", 4]);
charlist.push(["IilìÌ", 3]);

const hashcharlist = [];
for (let i = 0; i < charlist.length; i++) {
    for (let j = 0; j < charlist[i][0].length; j++) {
        hashcharlist[charlist[i][0].charAt(j)] = charlist[i][1];
    }
}
