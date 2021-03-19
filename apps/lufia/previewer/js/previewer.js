function lufiaTextClean(text) {
    return text;
}

function lufiaPreviewBox(previewContainerSelector, text, boxIndex, boxType) {

    const previewContainer = $('#' + previewContainerSelector);
    const dialogs = text.split(/<input>/g);
    dialogs.forEach((dialog, index) => {

        const dialogId = `dialog-${boxIndex}-${index}`;
        const dialogClass = 'lufia-dialog-box';
        dialogBox = `<div id="${dialogId}" class="lufia-preview-box ${dialogClass}">\
            <div class="bgimage">\
                <div class="chars"></div>\
            </div>\
            <div class="infobox">\
                <div class="counter1"></div>\
                <div class="counter2"></div>\
                <div class="counter3"></div>\
                <div class="counter4"></div>\
                <div class="alert"></div>\
            </div>\
        </div>`;
        previewContainer.append(dialogBox);

        dialog = lufiaTextClean(dialog);

        let indexLine = 0;
        let picturestring = '';
        const counterstring = ['', '', '', ''];
        let alert = '';
        const counter = [0, 0, 0, 0];

        for (let i = 0; i < dialog.length; i++) {
            const utf16char = dialog.charAt(i);
            const utf16int = utf16char.charCodeAt();
            let buffer = "";
            if (hashcharlist[utf16char] > 0) {
                counter[indexLine] += hashcharlist[utf16char];
                buffer = `<div class="lufia-font1 lufia-font1-${utf16int}"></div>`;
            } else if (utf16char == "\n") {
                buffer = '<br />';
                indexLine++;
            } else if (utf16char !== 13) {
                alert += utf16char;
            }
            if (buffer !== '') {
                picturestring += buffer;
            }
        }

        for (let i = 0; i < counter.length; i++) {
            if (counter[i] <= 240) {
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
        $(dialogSelector, previewContainer).find('.counter4').html(counterstring[3]);
        $(dialogSelector, previewContainer).find('.alert').html(alert !== '' ? `Unsupported character(s): ${alert}` : '');

    });

}

function renderPreview(previewContainerSelector, text, id, type) {
    document.getElementById(previewContainerSelector).innerHTML = '';
    const boxType = 1;
    lufiaPreviewBox(previewContainerSelector, text, id, boxType);
}

const charlist = [];
charlist.push([" !\"àèéì'()*‥,-./", 8]);
charlist.push(["0123456789:;È=>?", 8]);
charlist.push(["ABCDEFGIJKLMNO", 8]);
charlist.push(["PQRSTUVWXYZ[ò]ù_", 8]);
charlist.push(["abcdefghijklmno", 8]);
charlist.push(["pqrstuvwxyz{|}~", 8]);

const hashcharlist = [];
for (let i = 0; i < charlist.length; i++) {
    for (let j = 0; j < charlist[i][0].length; j++) {
        hashcharlist[charlist[i][0].charAt(j)] = charlist[i][1];
    }
}
