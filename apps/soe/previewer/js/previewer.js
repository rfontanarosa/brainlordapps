String.prototype.replaceAt = function(index, replacement) {
    return this.substr(0, index) + replacement + this.substr(index + replacement.length);
}

const soeTextClean = text => {
    text = text.replace(/{/g, '"');
    text = text.replace(/}/g, '"');
    text = text.replace(/<\$93>/g, "");
    text = text.replace(/<\$94>/g, "");
    text = text.replace(/<\$96>/g, "");
    text = text.replace(/<\$97>/g, "");
    text = text.replace(/<\$85>/g, "");
    text = text.replace(/<\$87>/g, "");
    text = text.replace(/<\$A3>/g, "XXXXX");
    text = text.replace(/<\Item>/g, "XXXXX");
    text = text.replace(/<Dog>/g, "XXXXX");
    text = text.replace(/<\Choice>/g, "");
    text = text.replace(/<S \$.. \$..>/g, "");
    return text;
};

const soeTextParse = (text, textCleaner, numRows, rowMaxPixels) => {
    text = text.replaceAll('<$86>', '\r');
    cleanedText = (typeof textCleaner === "function") ? textCleaner(text) : text;
    let newText = '';
    let charCounter = 0;
    let wordCounter = 0;
    let lineCounter = 1;
    let spacePosition = -1;
    for (let i = 0; i < cleanedText.length; i++) {
        const utf16char = cleanedText.charAt(i);
        newText += utf16char;
        // console.log(i, utf16char, charCounter, lineCounter, spacePosition);
        if (utf16char === '\r') {
            charCounter = 0;
            lineCounter = 1;
            spacePosition = -1;
        }
        else if (utf16char === '\n') {
            charCounter = 0;
            lineCounter += 1;
            spacePosition = -1;
        } else {
            if (utf16char === ' ') {
                spacePosition = i;
                charCounter += hashcharlist[utf16char] ? hashcharlist[utf16char] + 1 : 0; // soe space shadow fix
                wordCounter = 0;
            } else {
                charCounter += hashcharlist[utf16char] ? hashcharlist[utf16char] : 0;
                wordCounter += hashcharlist[utf16char] ? hashcharlist[utf16char] : 0;
                if (charCounter > rowMaxPixels) {
                    if (spacePosition !== -1) {
                        if (lineCounter >= numRows) {
                            newText = newText.replaceAt(spacePosition, '\r');
                            lineCounter = 1;
                        } else {
                            newText = newText.replaceAt(spacePosition, '\n');
                            lineCounter += 1;
                        }
                    }
                    spacePosition = -1;
                    charCounter = wordCounter;
                }
            }
        }
    }
    // console.log(newText.replaceAll('\r', '<BOX>').replaceAll('\n', '<LINE>'));
    return newText;
};

function soeRenderPreview(selector, text, config) {
    const {textId, numRows, rowMaxPixels, previewerClass, fontClass} = config;

    const previewContainer = $(`#${selector}`);
    let dialogs = text.split('\r');
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
            } else {
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
        numRows: 4,
        rowMaxPixels: 152,
        previewerClass: 'soe-preview-box',
        fontClass: 'soe-font1'
    };
    const parsedText = soeTextParse(text, soeTextClean, previewerConfig.numRows, previewerConfig.rowMaxPixels);
    soeRenderPreview(previewContainerSelector, parsedText, previewerConfig);
}

const charlist = [];
charlist.push(["Wmw", 11]);
charlist.push(["#%@M…", 9]);
charlist.push(["&4NK", 8]);
charlist.push(["àèéòùÀÒÙ$-02356789=?ABCDGOJHPQRTUVXYZabdeghknopquvxyz", 7]);
charlist.push(["ÈÉ\"*+/EFLS\\cfjrs", 6]);
charlist.push(["Ì<>It", 5]);
charlist.push(["ì !()1[]", 4]);
charlist.push(["',.:;`il", 3]);
charlist.push(["|", 2]);

const hashcharlist = [];
for (let i = 0; i < charlist.length; i++) {
    for (let j = 0; j < charlist[i][0].length; j++) {
        hashcharlist[charlist[i][0].charAt(j)] = charlist[i][1];
    }
}
