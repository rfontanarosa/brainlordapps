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
    "‘": { "hex": "60", "width": 3 },
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
    text = text.replace(/<S \$.. \$..>/g, "");
    return text;
}

function soePreviewBox(previewContainerSelector, text, boxIndex, boxType) {
    const previewContainer = $('#' + previewContainerSelector);

    var textArray = text.split("<$86>");
    for (var j=0; j<textArray.length; j++) {

        dialogBox = '<div id="dialog-' + boxIndex + '-' + j + '" class="soe-dialogbox">\
            <div class="dialog-box">\
                <div class="dialog-box-row"></div>\
                <div class="dialog-box-row"></div>\
                <div class="dialog-box-row"></div>\
                <div class="dialog-box-row"></div>\
            </div>\
            <div class="infobox">\
                <div></div>\
                <div></div>\
                <div></div>\
                <div></div>\
            </div>\
        </div>';

        previewContainer.append(dialogBox);

        dialogSelector = $('#dialog-' + boxIndex + '-' + j);

        textDialog = textArray[j];
        textDialog = soeTextClean(textDialog);

        const splitted = textDialog.split(' ');
    
        const widths = splitted.map((value) => {
            return value.split('').reduce((acc, currValue) => {
                return table[currValue] !== undefined ? acc + table[currValue].width : 0;
            }, 0);
        });
    
        const limit = 149;
        const lines = ['', '', '', ''];
        let total=0, k=0, buffer='';
    
        splitted.forEach((value, index) => {
            if (index !== 0) {
                buffer += ' ';
                total += table[' '].width;
            }
            const width = widths[index];
            if (width <= limit && total + width >= limit) {
                lines[k] = buffer;
                total = 0;
                k++;
                buffer = '';
            }
            buffer += value;
            total += width;
        });
        lines[k] = buffer;

        $('.dialog-box-row', dialogSelector).empty();
        const stats = [0, 0, 0, 0];
        $('.infobox', dialogSelector).children().each(() => $(this).text(0));

        lines.forEach((line, index) => {
            const rowSelector = $(`.dialog-box-row:nth-child(${index + 1})`, dialogSelector);
            line.split('').forEach((char) => {
                if (table.hasOwnProperty(char)) {
                    var image = $('<img>', {
                        src: `images/preview/${table[char].hex}.bmp`,
                        alt: ''
                    }).appendTo(rowSelector);
                    // stats
                    stats[index] += table[char].width;
                    $('.infobox', dialogSelector).children().eq(index).text(stats[index]);
                    if (stats[index] >= limit) {
                        $('.infobox', dialogSelector).children().eq(index).css('color', 'red');
                    }
                } else {
                    console.log('ERRORE');
                }
            });
        });

    }

}

function renderPreview(previewContainerSelector, text) {
    $('#' + previewContainerSelector).empty();
    soePreviewBox(previewContainerSelector, text, 0, 1);
}
