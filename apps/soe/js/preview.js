String.prototype.replaceAt = function(index, replacement) {
    return this.substr(0, index) + replacement + this.substr(index + replacement.length);
}

const table = {
    "'": { "hex": "27", "width": 3 },
    " ": { "hex": "00", "width": 4 },
    "à": { "hex": "11", "width": 7 },
    "è": { "hex": "13", "width": 7 },
    "é": { "hex": "15", "width": 7 },
    "ì": { "hex": "17", "width": 4 },
    "ò": { "hex": "19", "width": 7 },
    "ù": { "hex": "1B", "width": 7 },
    "È": { "hex": "1D", "width": 6 },
    "!": { "hex": "21", "width": 4 },
    "\"": { "hex": "22", "width": 6 },
    "#": { "hex": "23", "width": 9 },
    "$": { "hex": "24", "width": 7 },
    "%": { "hex": "25", "width": 9 },
    "&": { "hex": "26", "width": 8 },
    "’": { "hex": "27", "width": 3 },
    "(": { "hex": "28", "width": 4 },
    ")": { "hex": "29", "width": 4 },
    ",": { "hex": "2C", "width": 3 },
    "-": { "hex": "2D", "width": 7 },
    ".": { "hex": "2E", "width": 3 },
    "/": { "hex": "2F", "width": 6 },
    "0": { "hex": "30", "width": 7 },
    "1": { "hex": "31", "width": 4 },
    "2": { "hex": "32", "width": 7 },
    "3": { "hex": "33", "width": 7 },
    "4": { "hex": "34", "width": 8 },
    "5": { "hex": "35", "width": 7 },
    "6": { "hex": "36", "width": 7 },
    "7": { "hex": "37", "width": 7 },
    "8": { "hex": "38", "width": 7 },
    "9": { "hex": "39", "width": 7 },
    ":": { "hex": "3A", "width": 3 },
    ";": { "hex": "3B", "width": 3 },
    "<": { "hex": "3C", "width": 5 },
    "=": { "hex": "3D", "width": 7 },
    ">": { "hex": "3E", "width": 5 },
    "?": { "hex": "3F", "width": 7 },
    "@": { "hex": "40", "width": 9 },
    "A": { "hex": "41", "width": 7 },
    "B": { "hex": "42", "width": 7 },
    "C": { "hex": "43", "width": 7 },
    "D": { "hex": "44", "width": 7 },
    "E": { "hex": "45", "width": 6 },
    "F": { "hex": "46", "width": 6 },
    "G": { "hex": "47", "width": 7 },
    "H": { "hex": "48", "width": 7 },
    "I": { "hex": "49", "width": 5 },
    "J": { "hex": "4A", "width": 7 },
    "K": { "hex": "4B", "width": 8 },
    "L": { "hex": "4C", "width": 6 },
    "M": { "hex": "4D", "width": 9 },
    "N": { "hex": "4E", "width": 8 },
    "O": { "hex": "4F", "width": 7 },
    "P": { "hex": "50", "width": 7 },
    "Q": { "hex": "51", "width": 7 },
    "R": { "hex": "52", "width": 7 },
    "S": { "hex": "53", "width": 6 },
    "T": { "hex": "54", "width": 7 },
    "U": { "hex": "55", "width": 7 },
    "V": { "hex": "56", "width": 7 },
    "W": { "hex": "57", "width": 11 },
    "X": { "hex": "58", "width": 7 },
    "Y": { "hex": "59", "width": 7 },
    "Z": { "hex": "5A", "width": 7 },
    "[": { "hex": "5B", "width": 4 },
    "\\": { "hex": "5C", "width": 6 },
    "]": { "hex": "5D", "width": 4 },
    "…": { "hex": "5F", "width": 9 },
    "`": { "hex": "60", "width": 3 },
    "a": { "hex": "61", "width": 7 },
    "b": { "hex": "62", "width": 7 },
    "c": { "hex": "63", "width": 6 },
    "d": { "hex": "64", "width": 7 },
    "e": { "hex": "65", "width": 7 },
    "f": { "hex": "66", "width": 6 },
    "g": { "hex": "67", "width": 7 },
    "h": { "hex": "68", "width": 7 },
    "i": { "hex": "69", "width": 3 },
    "j": { "hex": "6A", "width": 6 },
    "k": { "hex": "6B", "width": 7 },
    "l": { "hex": "6C", "width": 3 },
    "m": { "hex": "6D", "width": 11 },
    "n": { "hex": "6E", "width": 7 },
    "o": { "hex": "6F", "width": 7 },
    "p": { "hex": "70", "width": 7 },
    "q": { "hex": "71", "width": 7 },
    "r": { "hex": "72", "width": 6 },
    "s": { "hex": "73", "width": 6 },
    "t": { "hex": "74", "width": 5 },
    "u": { "hex": "75", "width": 7 },
    "v": { "hex": "76", "width": 7 },
    "w": { "hex": "77", "width": 11 },
    "x": { "hex": "78", "width": 7 },
    "y": { "hex": "79", "width": 7 },
    "z": { "hex": "7A", "width": 7 }
}

function soeTextClean(text) {
    text = text.replace(/{/g, '"');
    text = text.replace(/}/g, '"');
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
}

function soePreviewBox(previewContainerSelector, text, boxIndex, boxType) {
    text0 = text.replaceAll('<$86>', '\r');
    text0 = soeTextClean(text0);
    let newText = '';
    const charLimit = 152;
    const lineLimit = 4;
    let charCounter = 0;
    let wordCounter = 0;
    let lineCounter = 1;
    let spacePosition = -1;
    for (let i = 0; i < text0.length; i++) {
        const utf16char = text0.charAt(i);
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
                wordCounter = 0;
            }
            charCounter += table[utf16char] ? table[utf16char].width : 0;
            wordCounter += table[utf16char] ? table[utf16char].width : 0;
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
    // console.log(newText.replaceAll('\r', '<BOX>').replaceAll('\n', '<LINE>'));

    const previewContainer = $('#' + previewContainerSelector);

    let dialogs = newText.split('\r');
    dialogs = dialogs.filter(element => element !== '');
    dialogs.forEach((dialog, index) => {

        const dialogId = `dialog-${boxIndex}-${index}`;
        dialogBox = '<div id="' + dialogId + '" class="soe-dialogbox">\
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
        </div>';
        previewContainer.append(dialogBox);

        let indexLine = 0;
        let picturestring = '';
        const counterstring = ['', '', '', ''];
        let alert = '';
        const counter = [0, 0, 0, 0];

        for (let i = 0; i < dialog.length; i++) {
            const utf16char = dialog.charAt(i);
            const utf16int = utf16char.charCodeAt();
            let buffer = '';
            if (table[utf16char] && table[utf16char].width > 0) {
                counter[indexLine] += table[utf16char].width;
                buffer = `<img src="images/preview/${table[utf16char].hex}.bmp" alt="" />`;
            } else if (utf16char == "\n") {
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
            if (counter[i] <= 150) {
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

function renderPreview(previewContainerSelector, text) {
    document.getElementById(previewContainerSelector).innerHTML = '';
    soePreviewBox(previewContainerSelector, text, 0, 1);
}
