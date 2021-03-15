function brainlordTextClean(text) {
    text = text.replace(/\{f6}{..\}/g, '');
    text = text.replace(/\{fb}{..\}{..\}{..\}{..\}{..\}/g, '');
    text = text.replace(/\{fc}{..\}{..\}{..\}{..\}{..\}/g, '');
    text = text.replace(/\{fd}{..\}{..\}/g, '');
    text = text.replace(/\{fe}{..\}{..\}/g, '');
    text = text.replace(/\{ff}{..\}{..\}{..\}/g, '');
    text = text.replaceAll('{f3}', ' ');
    text = text.replaceAll('{82}', '');
    text = text.replaceAll('{89}', 'X');
    text = text.replaceAll('{8c}', 'X');
    text = text.replaceAll('{8d}', 'X');
    text = text.replaceAll('<name>', 'PLAYER');
    text = text.replaceAll('<ram>', 'RAM');
    text = text.replaceAll('<white>', '');
    text = text.replaceAll('{ee}', ' ');
    text = text.replaceAll('{ef}', ' ');
    text = text.replaceAll('{f7}', '');
    return text;
}

function brainlordPreviewBox(previewContainerSelector, text, boxIndex, boxType) {

    const previewContainer = $('#' + previewContainerSelector);
    const dialogs = text.split(/<input>/g);
    dialogs.forEach((dialog, index) => {

        const dialogId = `dialog-${boxIndex}-${index}`;
        const dialogClass = 'brainlord-dialog-box';
        dialogBox = `<div id="${dialogId}" class="brainlord-preview-box ${dialogClass}">\
            <div class="bgimage">\
                <div class="chars"></div>\
            </div>\
            <div class="infobox">\
                <div class="counter counter1"></div>\
                <div class="counter counter2"></div>\
                <div class="counter counter3"></div>\
                <div class="counter counter4"></div>\
                <div class="alert"></div>\
            </div>\
        </div>`;
        previewContainer.append(dialogBox);

        dialog = brainlordTextClean(dialog);

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
                counter[indexLine] += 8;
                buffer = `<div class="brainlord-font1 brainlord-font1-${utf16int}"></div>`;
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
        $(dialogSelector, previewContainer).find('.counter4').html(counterstring[3]);
        $(dialogSelector, previewContainer).find('.alert').html(alert !== '' ? `Unsupported character(s): ${alert}` : '');

    });

}

function renderPreview(previewContainerSelector, text, id, type) {
    document.getElementById(previewContainerSelector).innerHTML = '';
    const boxType = id < 4097 ? 1 : 2;
    brainlordPreviewBox(previewContainerSelector, text, id, boxType);
}

const charlist = [];
charlist.push(["01234567", 8]);
charlist.push(["89 ", 8]);
charlist.push([").", 8]);
charlist.push(["/", 8]);
charlist.push(["ABCDEFGH", 8]);
charlist.push(["IJKLMNOP", 8]);
charlist.push(["QRSTUVWX", 8]);
charlist.push(["YZabcdef", 8]);
charlist.push(["ghijklmn", 8]);
charlist.push(["opqrstuv", 8]);
charlist.push(["wxyz?", 8]);
charlist.push([":;àèé", 8]);
charlist.push(["ìòùÈ°'\"", 8]);
charlist.push(["-,·", 8]);
charlist.push(["!", 8]);

const hashcharlist = [];
for (let i = 0; i < charlist.length; i++) {
    for (let j = 0; j < charlist[i][0].length; j++) {
        hashcharlist[charlist[i][0].charAt(j)] = charlist[i][1];
    }
}
