function ignitionTextClean(text) {
    text = text.replace(/<..>/g, '');
    text = text.replace(/<.. ..>/g, '');
    text = text.replace(/\[PORTRAIT ..]\n/g, '');
    text = text.replace(/\[WAIT ..]/g, '');
    text = text.replace(/<CLOSE>/g, '');
    text = text.replace(/<INPUT>\n/g, '');
    text = text.replace(/<LINE>/g, '');
    text = text.replace(/<PAUSE>\n/g, '');
    text = text.replace(/<CONTINUE>\n/g, '');
    text = text.replace(/\[FC03 ..]/g, '');
    text = text.replace(/\<FC00 ..>/g, '');
    return text;
}

function ignitionRenderPreview(selector, text, config) {
    const {textId, numRows, rowMaxPixels, previewerClass, fontClass} = config;

    const previewContainer = $(`#${selector}`);
    let dialogs = text.split(/<CLEAR>\n/g);
    dialogs = dialogs.filter(element => element !== '');
    dialogs.forEach((dialog, index) => {

        const previewBoxId = `dialog-${textId}-${index}`;
        const previewBox = `<div class="${previewerClass}" id="${previewBoxId}">\
            <div class="bgimage">\
                <div class="chars"></div>\
            </div>\
            <div class="infobox"></div>\
        </div>`;
        previewContainer.append(previewBox);

        dialog = ignitionTextClean(dialog);

        let indexLine = 0;
        let picturestring = '';
        let alert = '';
        const counter = new Array(numRows).fill(0);

        for (let i = 0; i < dialog.length; i++) {
            const utf16char = dialog.charAt(i);
            const utf16int = utf16char.charCodeAt();
            let buffer = '';
            if (hashcharlist[utf16char] > 0) {
                counter[indexLine] += hashcharlist[utf16char];
                buffer = `<div class="${fontClass} ${fontClass}-${utf16int}"></div>`;
            } else if (utf16char === '\n') {
                buffer = '<br />';
                indexLine++;
            } else if (utf16char !== 13) {
                alert += utf16char;
            }
            if (buffer !== '') {
                picturestring += buffer;
            }
        }

        const previewBoxElement = $(`#${previewBoxId}`, previewContainer);
        previewBoxElement.find('.chars').html(picturestring);
        const infoboxElement = $(".infobox", previewBoxElement);
        counter.forEach((count, i) => {
            const classNames = `counter${i + 1}` + (count <= rowMaxPixels ? "" : " redtext");
            infoboxElement.append(`<div class="${classNames}">Line ${i + 1}: ${count} pixel</div>`);
        });
        infoboxElement.append(`<div class="alert">${alert !== "" ? "Unsupported character(s): " + alert : ""}</div>`);

    });

}

function renderPreview(previewContainerSelector, text, id, type) {
    document.getElementById(previewContainerSelector).innerHTML = '';
    const previewerConfig = {
        textId: id,
        numRows: 3,
        rowMaxPixels: 176,
        previewerClass: 'ignition-preview-box',
        fontClass: 'ignition-font1'
    };
    ignitionRenderPreview(previewContainerSelector, text, previewerConfig);
}

const charlist = [];
charlist.push([" abcdefghijklmno", 8]);
charlist.push(["pqrstuvwxyz.\",-'", 8]);
charlist.push([":ABCDEFGHIJKLMNO", 8]);
charlist.push(["PQRSTUVWXYZ?!()/", 8]);
charlist.push(["0123456789;", 8]);
charlist.push(["àéèìòùÈ°", 8]);

const hashcharlist = [];
for (let i = 0; i < charlist.length; i++) {
    for (let j = 0; j < charlist[i][0].length; j++) {
        hashcharlist[charlist[i][0].charAt(j)] = charlist[i][1];
    }
}
