<style>

    .dialog-container {
        background: url('images/background1.png') no-repeat;
        border: none;
        width: 256px;
        height: 224px;
    }

    .dialog-container .dialog-box {
        width: 160px;
        height: 56px;
    }

    .dialog-container .dialog-box .dialog-box-row {
        position: relative;
        height: 12px;
        top: 20px;
        left: 20px;
    }

    .dialog-container .dialog-box img {
        float: left;
    }

</style>

<textarea name="text"></textarea>
<div id="stats">
    <div>0</div>
    <div>0</div>
    <div>0</div>
    <div>0</div>
</div>

<div class="dialog-container">
    <div class="dialog-box">
        <div class="dialog-box-row"></div>
        <div class="dialog-box-row"></div>
        <div class="dialog-box-row"></div>
        <div class="dialog-box-row"></div>
    </div>
</div>

<script src="../../../assets/libs/jquery/dist/jquery.min.js"></script>

<script src="table.js" charset="UTF-8"></script>

<script>

(function initCache() {
    Object.values(table).map(value => {var image = new Image(); image.src = `./images/font/${value.hex}.bmp`;});
})();

function preview(containerSelector, text) {

    text = text.replace(/{/g, '"').replace(/}/g, '"');

    const splitted = text.split(' ');

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

    const stats = [0, 0, 0, 0];
    $('#stats').children().each(function () {
        $(this).text(0);
    });
    $('.dialog-box-row', + containerSelector).empty();
    lines.forEach((line, index) => {
        line.split('').forEach((char) => {
            if (table.hasOwnProperty(char)) {
                var image = $('<img>', {
                    src: `images/font/${table[char].hex}.bmp`,
                    alt: ''
                }).appendTo(`.dialog-box-row:nth-child(${index + 1})`, + containerSelector);
                stats[index] += table[char].width;
                $('#stats').children().eq(index).text(stats[index]);
                if (stats[index] >= limit) {
                    $('#stats').children().eq(index).css('color', 'red');
                }
            } else {
                console.log('ERRORE');
            }
        });
    });

}

$('textarea[name="text"]').keyup(function(e) {
    e.stopPropagation();
    e.preventDefault();
    const text = $(this).val();
    preview('.dialog-container', text);
});

</script>