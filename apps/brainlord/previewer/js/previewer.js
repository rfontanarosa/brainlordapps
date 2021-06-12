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

    const charLimit = 222;

    const previewContainer = $(`#${previewContainerSelector}`);
    const dialogs = text.split(/<input>/g);
    dialogs.forEach((dialog, index) => {

        const previewBoxClass = 'brainlord-preview-box';
        const previewBoxId = `dialog-${boxIndex}-${index}`;
        const previewBox = `<div class="${previewBoxClass}" id="${previewBoxId}">\
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
        previewContainer.append(previewBox);

        dialog = brainlordTextClean(dialog);

        let indexLine = 0;
        let picturestring = '';
        let alert = '';
        const counter = [0, 0, 0, 0];

        for (let i = 0; i < dialog.length; i++) {
            const utf16char = dialog.charAt(i);
            const utf16int = utf16char.charCodeAt();
            let buffer = "";
            if (hashcharlist[utf16char] > 0) {
                counter[indexLine] += hashcharlist[utf16char];
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

        const counterstring = counter.map((count, i) => count <= charLimit
            ? `Line ${i + 1}: ${count} pixel`
            : `<div class="redtext">Line ${i + 1}: ${count} pixel</div>`
        );

        const previewBoxElement = $(`#${previewBoxId}`, previewContainer);
        previewBoxElement.find('.chars').html(picturestring);
        previewBoxElement.find('.counter1').html(counterstring[0]);
        previewBoxElement.find('.counter2').html(counterstring[1]);
        previewBoxElement.find('.counter3').html(counterstring[2]);
        previewBoxElement.find('.counter4').html(counterstring[3]);
        previewBoxElement.find('.alert').html(alert !== '' ? `Unsupported character(s): ${alert}` : '');

    });

}

function renderPreview(previewContainerSelector, text, id, type) {
    document.getElementById(previewContainerSelector).innerHTML = '';
    const boxType = 1;
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
