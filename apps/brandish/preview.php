<?php
	header('Content-Type: text/html; charset=utf-8');
	require_once 'config.inc.php';
?>

<style>

.tile8x16 {
	width: 8px;
	height: 16px;
}

.tile8x8 {
	width: 8px;
	height: 8px;
}

<?php

	$array1_en = array('!', '', '#', '$', '%', '&', '\'', '(', ')', '*', '+', ',', '-', '.', '/');
	$array1_en = array_merge($array1_en, array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'));
	$array1_en = array_merge($array1_en, array('', '', '', '', '', '?', ''));
	$array1_en = array_merge($array1_en, array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'));
	$array1_en = array_merge($array1_en, array('', '', '', '', '_', ''));
	$array1_en = array_merge($array1_en, array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'));
	foreach ($array1_en as $i => $value) {
		echo '.brandish-font1-en-' . $i . '{ background: url(./images/preview/brandish-font1-en.png) ' . (8*$i)*-1 . 'px 0; }';
	}

	$array1_it = array('!', '', '#', '$', '%', '&', '\'', '(', ')', '*', '+', ',', '-', '.', '/');
	$array1_it = array_merge($array1_it, array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'));
	$array1_it = array_merge($array1_it, array('', '', '', '', '', '?', ''));
	$array1_it = array_merge($array1_it, array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'));
	$array1_it = array_merge($array1_it, array('', '', '', '', '_', ''));
	$array1_it = array_merge($array1_it, array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'));
	foreach ($array1_it as $i => $value) {
		echo '.brandish-font1-it-' . $i . '{ background: url(./images/preview/brandish-font1-it.png) ' . (8*$i)*-1 . 'px 0; }';
	}

?>

.box-dialog {
	margin: 0px;
	width: 256px;
	height: 239px;
	background-image: url('./images/preview/box-dialog.png');
}
.box-dialog-container {
	margin: 0px;
	padding-top: 119px;
	padding-left: 24px;
	height: 239px;
	line-height: 6px;
}

</style>

<?php

	function textClean($text) {
		if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
			$text = str_replace(PHP_EOL, NEWLINECHAR, $text);
		}
		return $text;
	}

	function brandishBoxPropertiesCalculator($props) {
		$max_chars = 26;
		if (substr($props, 0, 12) == '{03}{0f}{1a}') {
			$max_chars = 26;
		} elseif (substr($props, 0, 12) == '{05}{10}{16}') {
			$max_chars = 22;
		} elseif (substr($props, 0, 12) == '{04}{14}{18}') {
			$max_chars = 24;
		}
		return array('max_chars' => $max_chars);
	}

	function brandishTextClean($text) {
		$text = preg_replace('/\{04\}/', 'XXX', $text);
		$text = preg_replace('/\{..\}/', '', $text); 
		return $text;
	}

	$id_text = $_POST['id_text'];
	$source = $_POST['text'];
	$cleanedSource = textClean($source);
	$boxProperties = brandishBoxPropertiesCalculator($cleanedSource);
	$max_chars = $boxProperties['max_chars'];
	$boxes = explode('{01}', $source);
	foreach ($boxes as $box) {
		$cleanedText = brandishTextClean($box);
		$values = explode(' ', $cleanedText);
		$lines = array('', '', '', '', '', '', '', '', '');
		$line_index = 0;
		$line_text = '';
		$line_size = 0;
		foreach ($values as $val) {
			if (strlen($val) > $max_chars) {
			} else {
				if (($line_size + strlen($val)) > $max_chars) {
					$line_index += 1;
					$line_size = 0;
				}
				$lines[$line_index] .= $val;
				$lines[$line_index] .= ' ';
				$line_size = strlen($lines[$line_index]);
			}
		}

		if ($id_text < 151 && $id_text > 102) echo '<div class="box-dialog"><div class="box-dialog-container">';
		else echo '<div class="box-dialog"><div class="box-dialog-container">';

		foreach ($lines as $line) {
			$line = preg_split('//u', $line, -1, PREG_SPLIT_NO_EMPTY);
			for ($i=0; $i<count($line); $i++) {
				$char = $line[$i];
				$key = array_search($char, $array1_en);
				if ($key != false) {
					echo '<img class="tile-8x8 brandish-font1-en-' . $key . '" src="./images/preview/placeholder-8x8.png">';
				}
			}
			echo '<br />';
		}
		echo '</div></div>';
	}

?>