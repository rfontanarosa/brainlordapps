String.prototype.replaceAt = function (index, replacement) {
  return (
    this.substr(0, index) +
    replacement +
    this.substr(index + replacement.length)
  );
};

function staroceanTextClean(text) {
  text = text.replace(/<RATIX>/g, "Ratix");
  text = text.replace(/<MILLY>/g, "Milly");
  text = text.replace(/<DORN>/g, "Dorn");
  text = text.replace(/<RONIXIS>/g, "Ronixis");
  text = text.replace(/<IRIA>/g, "Iria");
  text = text.replace(/<CIUS>/g, "Cius");
  text = text.replace(/<JOSHUA>/g, "Joshua");
  text = text.replace(/<TINEK>/g, "Tinek");
  text = text.replace(/<MARVEL>/g, "Marvel");
  text = text.replace(/<ASHLAY>/g, "Ashlay");
  text = text.replace(/<PERISIE>/g, "Perisie");
  text = text.replace(/<FEAR>/g, "Fear");
  text = text.replace(/<PAUSE><..>/g, "");
  text = text.replace(/<COLOR><..>/g, "");
  text = text.replace(/<WAIT><CLOSE>/g, "");
  return text;
}

function staroceanPreviewBox(
  previewContainerSelector,
  text,
  boxIndex,
  boxType
) {
  text = text.replaceAll("<WAIT>\n", "\r");
  text = staroceanTextClean(text);
  let newText = "";
  const charLimit = 208;
  const lineLimit = 4;
  let charCounter = 0;
  let wordCounter = 0;
  let lineCounter = 1;
  let spacePosition = -1;
  for (let i = 0; i < text.length; i++) {
    const utf16char = text.charAt(i);
    newText += utf16char;
    // console.log(i, utf16char, charCounter, lineCounter, spacePosition);
    if (utf16char === "\r") {
      charCounter = 0;
      lineCounter = 1;
      spacePosition = -1;
    } else if (utf16char === "\n") {
      charCounter = 0;
      spacePosition = -1;
      if (lineCounter === lineLimit) {
        newText = newText.slice(0, -1) + "\r";
        lineCounter = 1;
      } else {
        lineCounter += 1;
      }
    } else {
      if (utf16char === " ") {
        spacePosition = i;
        charCounter += hashcharlist[utf16char] ? hashcharlist[utf16char] : 0;
        wordCounter = 0;
      } else {
        charCounter += hashcharlist[utf16char] ? hashcharlist[utf16char] : 0;
        wordCounter += hashcharlist[utf16char] ? hashcharlist[utf16char] : 0;
        if (charCounter > charLimit) {
          if (spacePosition !== -1) {
            if (lineCounter >= lineLimit) {
              newText = newText.replaceAt(spacePosition, "\r");
              lineCounter = 1;
            } else {
              newText = newText.replaceAt(spacePosition, "\n");
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

  const previewContainer = $(`#${previewContainerSelector}`);
  let dialogs = newText.split("\r");
  dialogs = dialogs.filter((element) => element !== "");
  dialogs.forEach((dialog, index) => {
    const previewBoxClass = "starocean-preview-box";
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

    let indexLine = 0;
    let picturestring = "";
    let alert = "";
    const counter = [0, 0, 0, 0];

    for (let i = 0; i < dialog.length; i++) {
      const utf16char = dialog.charAt(i);
      const utf16int = utf16char.charCodeAt();
      let buffer = "";
      if (hashcharlist[utf16char] > 0) {
        counter[indexLine] += hashcharlist[utf16char];
        buffer = `<div class="starocean-font1 starocean-font1-${utf16int}"></div>`;
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

    const counterstring = counter.map((count, i) =>
      count <= charLimit
        ? `Line ${i + 1}: ${count} pixel`
        : `<div class="redtext">Line ${i + 1}: ${count} pixel</div>`
    );

    const previewBoxElement = $(`#${previewBoxId}`, previewContainer);
    previewBoxElement.find(".chars").html(picturestring);
    previewBoxElement.find(".counter1").html(counterstring[0]);
    previewBoxElement.find(".counter2").html(counterstring[1]);
    previewBoxElement.find(".counter3").html(counterstring[2]);
    previewBoxElement.find(".counter4").html(counterstring[3]);
    previewBoxElement
      .find(".alert")
      .html(alert !== "" ? `Unsupported character(s): ${alert}` : "");
  });
}

function renderPreview(previewContainerSelector, text, id, type) {
  document.getElementById(previewContainerSelector).innerHTML = "";
  const boxType = 1;
  staroceanPreviewBox(previewContainerSelector, text, id, boxType);
}

const charlist = [];
charlist.push(["w♥", 12]);
charlist.push(["MWm%★☆", 11]);
charlist.push(["#♪", 10]);
charlist.push(["&", 9]);
charlist.push(["4KN*", 8]);
charlist.push([
  "02356789ABCDEGHJOPQRTUVXYZabdeghknopquvxyz¿?/+-=<>ÀÒÙàèéòù",
  7,
]);
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
