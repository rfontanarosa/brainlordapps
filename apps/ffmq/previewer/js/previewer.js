String.prototype.replaceAt = function(index, replacement) {
    return this.substr(0, index) + replacement + this.substr(index + replacement.length);
}

function ffmqTextClean(text) {
    text = text.replace('."', '"');
    text = text.replace(/<HERONAME>/g, 'HERONAME');
    text = text.replace(/<082CFF>/g, 'RAM');
    return text;
}

function ffmqPreviewBox(previewContainerSelector, text, boxIndex, boxType) {

    text = text.replace(/<(?:1A|1B|SPEAKER|SCROLL)[^>]*>\n/gm, '\r');
    text = text.replaceAll('<LINE>\n', '\n');
    text = ffmqTextClean(text);
    let newText = '';
    const charLimit = 208;
    const lineLimit = 3;
    let charCounter = 0;
    let wordCounter = 0;
    let lineCounter = 1;
    let spacePosition = -1;
    for (let i = 0; i < text.length; i++) {
        const utf16char = text.charAt(i);
        newText += utf16char;
        // console.log(i, utf16char, charCounter, lineCounter, spacePosition);
        if (utf16char === '\r') {
            charCounter = 0;
            lineCounter = 1;
            spacePosition = -1;
        }
        else if (utf16char === '\n') {
            charCounter = 0;
            spacePosition = -1;
            if (lineCounter === lineLimit) {
                newText = newText.slice(0, -1) + '\r';
                lineCounter = 1;
            } else {
                lineCounter += 1;
            }
        } else {
            if (utf16char === ' ') {
                spacePosition = i;
                charCounter += hashcharlist[utf16char] ? hashcharlist[utf16char] : 0;
                wordCounter = 0;
            } else {
                charCounter += hashcharlist[utf16char] ? hashcharlist[utf16char] : 0;
                wordCounter += hashcharlist[utf16char] ? hashcharlist[utf16char] : 0;
                if (charCounter > charLimit) {
                    if (spacePosition !== -1) {
                        if (lineCounter >= lineLimit) {
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

    const previewContainer = $('#' + previewContainerSelector);

    let dialogs = newText.split('\r');
    dialogs = dialogs.filter(element => element !== '');
    dialogs.forEach((dialog, index) => {

        const dialogId = `dialog-${boxIndex}-${index}`;
        const dialogClass = 'ffmq-dialog-box';
        dialogBox = `<div id="${dialogId}" class="ffmq-preview-box ${dialogClass}">\
            <div class="bgimage">\
                <div class="chars"></div>\
            </div>\
            <div class="infobox">\
                <div class="counter1"></div>\
                <div class="counter2"></div>\
                <div class="counter3"></div>\
                <div class="alert"></div>\
            </div>\
        </div>`;
        previewContainer.append(dialogBox);

        let indexLine = 0;
        let picturestring = '';
        const counterstring = ['', '', ''];
        let alert = '';
        const counter = [0, 0, 0];

        for (let i = 0; i < dialog.length; i++) {
            const utf16char = dialog.charAt(i);
            const utf16int = utf16char.charCodeAt();
            let buffer = '';
            if (hashcharlist[utf16char] > 0) {
                counter[indexLine] += hashcharlist[utf16char];
                buffer = `<div class="ffmq-font1 ffmq-font1-${utf16int}"></div>`;
            } else if (utf16char === '\n') {
                buffer = '<br />';
                indexLine++;
            } else {
                alert += utf16char;
            }
            if (buffer !== '') {
                picturestring += buffer;
            }
        };

        for (let i = 0; i < counter.length; i++) {
            if (counter[i] <= charLimit) {
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

function renderPreview(previewContainerSelector, text, id, type) {
    document.getElementById(previewContainerSelector).innerHTML = '';
    const boxType = 1;
    ffmqPreviewBox(previewContainerSelector, text, id, boxType);
}

const charlist = [];
charlist.push(["023456789ABCDEFGHJKLMNOPQRSUVWXZakmqwxz“”…/&%Èà", 8]);
charlist.push(["TYbcdeforsuvgjpyh?néèòù", 7]);
charlist.push(["t-\"", 6]);
charlist.push([" 1I", 5]);
charlist.push(["l!,.", 4]);
charlist.push(["i;:'ì", 3]);

const hashcharlist = [];
for (let i = 0; i < charlist.length; i++) {
    for (let j = 0; j < charlist[i][0].length; j++) {
        hashcharlist[charlist[i][0].charAt(j)] = charlist[i][1];
    }
}
