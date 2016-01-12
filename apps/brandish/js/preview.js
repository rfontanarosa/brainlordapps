function previewBox1(text, output_tag_id, max_chars) {
	splitted_text = text.split(/\{01\}/g);
	$.each(splitted_text, function(k, text) {
		var output_outer_box = $('<div id="box1-outer"></div>');
		var output_inner_box = $('<span id="box1-inner"></span>');
		text = text.replace(/\{04\}/g, 'XXX');
		text = text.replace(/\{..\}/g, '');
		var values = text.split(' ');
		var lines = ['', '', '', '', '', '', '', '', ''];
		var line_index = 0
		var line_text = '';
		var line_size = 0;
		console.log(values);
		$.each(values, function(i, val) {
			if (val.length > max_chars) {
				/*
				lines[line_index] = val.substr(0, val.length-25);
				lines[line_index] = val.substr(val.length-25, val.length);
				line_index++;
				line_size = 0;
				*/
			}
			else {
				if ((line_size + val.length) > max_chars) {
					line_index++;
					line_size = 0;
				}
				lines[line_index] += val;
				lines[line_index] += ' ';
				line_size = lines[line_index].length;
			}
		});
		console.log(lines);
		$.each(lines, function(i, line) {
			$(output_inner_box).append(line + '<br />');
			console.log(i + ' - ' + line.length + ' - ' + line);
		});
		output_outer_box.append(output_inner_box);
		$('#' + output_tag_id).append(output_outer_box);
	});
}

function previewBox2(text, output_tag_id) {
	var output_outer_box = $('<div id="box2-outer"></div>');
	var output_inner_box = $('<span id="box2-inner"></span>');
	text = text.replace(/\{04\}/g, 'XXX');
	text = text.replace(/\{..\}/g, '');
	for (var i=0; i<10; i++) {
		var line = text.substring(16*i,16*(i+1));
		output_inner_box.append(line + '<br />');
	}
	output_outer_box.append(output_inner_box);
	$('#' + output_tag_id).append(output_outer_box);
}