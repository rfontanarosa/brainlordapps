<?php
	header('Content-Type: text/html; charset=utf-8');
	require_once 'config.inc.php';
?>

<style>

.tile8x16 {
	width: 8px;
	height: 16px;
}

<?php

	$array = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
	$array = array_merge($array, array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'));
	$array = array_merge($array, array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0'));
	//$array = array_merge($array, array('!', '?', '.', ',', '\'', ':', '-', '=', '', '"'));
	$array = array_merge($array, array('-0', '-1', '-2', '-3', '-4', '-5', '-6', '-7', '-8', '-9'));
	$array = array_merge($array, array('à', 'è', 'é', 'ì', 'ò', 'ù'));
	foreach ($array as $i => $value) {
		echo '.ys3-' . $value . '{ background: url(./images/preview/ys3-font1-it.png) ' . (8*$i)*-1 . 'px 0; }';
	}

?>

.box-test {
	margin: 0px;
	width: 256px;
	height: 239px;
	background-image: url('./images/preview/ys3-preview.png');
}
.box-test-container {
	margin: 0px;
	height: 239px;
	line-height: 14px;
}

</style>

<?php

	function textClean($text) {
		if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
			$text = str_replace(PHP_EOL, NEWLINECHAR, $text);
		}
		return $text;
	}

	function ys3BoxPropertiesCalculator($text) {
		$text = str_replace('{f0}', '', $text);
		$text = str_replace('{f1}', '', $text);
		$text = str_replace('{f5}', '', $text);
		$text = str_replace('{f7}', '', $text);
		$text = str_replace('{fe}', '', $text);
		$table = array(' ' => 0, 'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26, 'a' => 27, 'b' => 28);
		$left = $table[$text[1]];
		$top = $table[$text[2]];
		$width = $table[$text[3]];
		$height = $text[4];
		return array('color' => $text[0], 'left' => $left, 'top' => $top, 'width' => $width, 'height' => $height);
	}

	function ys3TextClean($text) {
		$text = str_replace('{{{', '', $text);
		$text = str_replace('}}}', '', $text);
		$text = str_replace('{f4}T', '', $text);
		$text = str_replace('{f6}A', '', $text);
		$text = str_replace('{f6}B', '', $text);
		$text = str_replace('{f6}C', '', $text);
		$text = str_replace('{f6}D', '', $text);
		$text = str_replace('{f6}E', '', $text);
		$text = str_replace('{f6}F', '', $text);
		$text = str_replace('{f6} ', '', $text);
		$text = str_replace('{fd}', '', $text);
		$text = str_replace('{fc}', '', $text);
		$text = str_replace('{ff}', '', $text);
		return $text;
	}

	function ys3TextDecoder($char) {
		switch ($char) {
			case '!':
				$char = '-0';
				break;
			case '?':
				$char = '-1';
				break;
			case '.':
				$char = '-2';
				break;
			case ',':
				$char = '-3';
				break;
			case '\'':
				$char = '-4';
				break;
			case ':':
				$char = '-5';
				break;
			case '-':
				$char = '-6';
				break;
			case '=':
				$char = '-7';
				break;
			case '"':
				$char = '-9';
				break;
		}
		return $char;
	}

	$id_text = $_POST['id_text'];
	$source = $_POST['text'];
	$source = str_replace('{02}{00}', '', $source);
	$boxes = explode('{f5}', $source);
	foreach ($boxes as $box) {
		$text = explode('{f3} ', $box);
		if (count($text) == 1) {
			$text = explode('{f2} ', $box);
		}
		$text[0] = textClean($text[0]);
		$boxProperties = ys3BoxPropertiesCalculator($text[0]);
		$paddingTop = 8 + ($boxProperties['top'] * 8);
		$paddingLeft = 8 + ($boxProperties['left'] * 8);
		$width = $paddingLeft + 8 + ($boxProperties['width'] * 8);
		echo '<div class="box-test"><div class="box-test-container" style="border-right: solid 1px yellow; width: ' . $width . 'px; padding-top: ' . $paddingTop . 'px; padding-left: ' . $paddingLeft . 'px;">';
		$cleanedText = textClean($text[1]);
		$cleanedText = ys3TextClean($cleanedText);
		$lines = explode('{fe}', $cleanedText);
		foreach ($lines as $line) {
			$line = preg_split('//u', $line, -1, PREG_SPLIT_NO_EMPTY);
			for ($i=0; $i<count($line); $i++) {
				$char = ys3TextDecoder($line[$i]);
				echo '<img class="tile-8x16 ys3-' . $char . '" src="./images/preview/placeholder-8x16.png">';
			}
			echo '<br />';
		}
		echo '</div></div>';
	}

?>