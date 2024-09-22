String.prototype.replaceAt = function (index, replacement) {
  return (this.slice(0, index) + replacement + this.slice(index + replacement.length));
};

function replaceText(regexes, text) {
  return regexes.reduce((curr, [pattern, replacement]) => curr.replace(pattern, replacement), text)
}

const STAROCEAN_REGEXES = [
  [/<RATIX>/g, "WWWWWWWW"],
  [/<MILLY>/g, "Milly"],
  [/<DORN>/g, "Dorn"],
  [/<RONIXIS>/g, "Ronixis"],
  [/<IRIA>/g, "Iria"],
  [/<CIUS>/g, "Cius"],
  [/<JOSHUA>/g, "Joshua"],
  [/<TINEK>/g, "Tinek"],
  [/<MARVEL>/g, "Marvel"],
  [/<PERISIE>/g, "Ashlay"],
  [/<FEAR>/g, "Fear"],
  [/<PAUSE><..>/g, ""],
  [/<COLOR><..>/g, ""],
  [/<WAIT><CLOSE>/g, ""],
  [/<CLOSE>/g, ""],
  [/<CODE 88><..>/g, ""],
];

function staroceanGetBoxProperties(boxType) {
  switch (boxType) {
    default:
      return {charLimit: 208, lineLimit: 4};
  }
}

function staroceanReplaceText(text) {
  text = text.replaceAll("<WAIT>\n", "\r");
  text.match(/<PAD><..><..>/g)?.forEach(matchResult => {
    console.log(matchResult);
    const width = parseInt(matchResult.slice(6, 8), 16);
    // const height = parseInt(matchResult.slice(10, 12), 16);
    text = text.replaceAll(matchResult, "\t".repeat(width));
  });
  return text;
}

function staroceanRenderPreview(containerSelector, text, textId, boxType) {
  const {charLimit, lineLimit} = staroceanGetBoxProperties(boxType);
  text = replaceText(STAROCEAN_REGEXES, text);
  text = staroceanReplaceText(text);
  let newText = "";
  let spaceIndex = -1;
  let charCounter = 0;
  let lineCounter = 1;
  for (let i = 0; i < text.length; i++) {
    const utf16char = text.charAt(i);
    newText += utf16char;
    // console.log(i, utf16char, charCounter, lineCounter, spacePosition);
    switch (utf16char) {
      case "\r":
        spaceIndex = -1;
        charCounter = 0;
        lineCounter = 1;
        break;
      case "\n":
        spaceIndex = -1
        charCounter = 0;
        lineCounter += 1;
        if (lineCounter > lineLimit) {
          newText = newText.slice(0, -1) + "\r";
        }
        break;
      case " ":
        spaceIndex = i;
        charCounter += hashcharlist[utf16char] ? hashcharlist[utf16char] : 0;
        break;
      default:
        charCounter += hashcharlist[utf16char] ? hashcharlist[utf16char] : 0;
        if (charCounter > charLimit) {
          if (spaceIndex !== -1) {
            if (lineCounter >= lineLimit) {
              newText = newText.replaceAt(spaceIndex, "\r");
              lineCounter = 1;
            } else {
              newText = newText.replaceAt(spaceIndex, "\n");
              lineCounter += 1;
            }
          }
          spaceIndex = -1;
        }
    }
  }

  // console.log(newText.replaceAll('\r', '<BOX>').replaceAll('\n', '<LINE>'));

  const previewContainer = document.getElementById(containerSelector);
  let dialogs = newText.split("\r");
  dialogs = dialogs.filter((element) => element !== "");
  dialogs.forEach((dialog, index) => {
    const previewBoxClass = "starocean-preview-box";
    const previewBoxId = `dialog-${textId}-${index}`;
    const previewBox = `
      <div class="${previewBoxClass}" id="${previewBoxId}">\
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
      </div>
    `;
    previewContainer.innerHTML += previewBox;

    let indexLine = 0;
    let picturestring = "";
    let alert = "";
    const charCounters = new Array(lineLimit).fill(0);
    const padCounters = new Array(lineLimit).fill(0);

    for (let i = 0; i < dialog.length; i++) {
      const utf16char = dialog.charAt(i);
      const utf16int = utf16char.charCodeAt();
      let buffer = "";
      if (hashcharlist[utf16char] > 0) {
        charCounters[indexLine] += hashcharlist[utf16char];
        buffer = `<div class="starocean-font1 starocean-font1-${utf16int}"></div>`;
      } else if (utf16char === "\t") {
        charCounters[indexLine] += 1;
        padCounters[indexLine] += 1;
        buffer = '<div style="display: inline-block; width: 1px;"></div>';
      } else if (utf16char === "\n") {
        buffer = "<br />";
        indexLine++;
      } else {
        alert += utf16char;
      }
      if (buffer !== "") {
        picturestring += buffer;
      }
    }

    const counterstring = charCounters.map((count, i) => `
      <div class="${count > charLimit ? 'redtext' : ''}">
        Line ${i + 1}: ${count} pixel --- ${padCounters[i]} pixel --- ${charLimit - count} pixel --- ${[0, 1].includes(padCounters[i] - (charLimit - count)) ? 'Y' : 'N'}
      </div>
    `);

    const previewBoxElement = previewContainer.querySelector(`#${previewBoxId}`);
    previewBoxElement.querySelector(".chars").innerHTML = picturestring;
    previewBoxElement.querySelector(".counter1").innerHTML = counterstring[0];
    previewBoxElement.querySelector(".counter2").innerHTML = counterstring[1];
    previewBoxElement.querySelector(".counter3").innerHTML = counterstring[2];
    previewBoxElement.querySelector(".counter4").innerHTML = counterstring[3];
    previewBoxElement.querySelector(".alert").innerHTML = alert !== "" ? `Unsupported character(s): ${alert}` : '';
  });
}

function renderPreview(containerSelector, text, id, type) {
  document.getElementById(containerSelector).innerHTML = "";
  const boxType = 1;
  staroceanRenderPreview(containerSelector, text, id, boxType);
}

const charlist = [];
charlist.push(["w♥", 12]);
charlist.push(["MWm%★☆", 11]);
charlist.push(["#♪", 10]);
charlist.push(["&", 9]);
charlist.push(["4KN*", 8]);
charlist.push(["02356789ABCDEGHJOPQRTUVXYZabdeghknopquvxyz¿?/+-=<>ÀÒÙàèéòù", 7]);
charlist.push(["EFLScfjrs“”ÈÉ", 6]);
charlist.push(["1t² ", 5]);
charlist.push(["'!()Ìì", 4]);
charlist.push(["Iil,.:;", 3]);

const hashcharlist = [];
for (let i = 0; i < charlist.length; i++) {
  for (let j = 0; j < charlist[i][0].length; j++) {
    hashcharlist[charlist[i][0].charAt(j)] = charlist[i][1];
  }
}
