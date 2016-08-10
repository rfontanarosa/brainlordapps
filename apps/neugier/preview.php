<?php
	header('Content-Type: text/html; charset=utf-8');
	require_once 'config.inc.php';
?>

<style>

<?php

	$map1 = array();
	$map1 += array('2' => array('-4', '-10'));
	$map1 += array('3' => array('-3', '-9'));
	$map1 += array('4' => array('i', 'l', 'I', '-2', '-6', '-13', 'ì'));
	$map1 += array('5' => array('j'));
	$map1 += array('6' => array('f', 't', 'J', '-1', '-7', '1', '-8', '-11', '-0'));
	$map1 += array('7' => array('a', 'c', 'e', 'g', 'o', 'r', 's', 'L', '-5', '-12', 'à', 'è', 'é', 'ò'));
	$map1 += array('8' => array('b', 'd', 'h', 'k', 'm', 'n', 'p', 'q', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'K', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'Y', 'Z', '0', '2', '3', '4', '5', '6', '7', '8', '9', '-14', 'ù', 'È'));

	$map2 = array();
	$map2 += array('A' => 8, 'B' => 8, 'C' => 8, 'D' => 8, 'E' => 8, 'F' => 8, 'G' => 8, 'H' => 8, 'I' => 4, 'J' => 6, 'K' => 8, 'L' => 7, 'M' => 8, 'N' => 8, 'O' => 8, 'P' => 8, 'Q' => 8, 'R' => 8, 'S' => 8, 'T' => 8, 'U' => 8, 'V' => 8, 'W' => 8, 'Y' => 8, 'Z' => 8);
	$map2 += array('a' => 7, 'b' => 8, 'c' => 7, 'd' => 8, 'e' => 7, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 4, 'j' => 5, 'k' => 8, 'l' => 4, 'm' => 8, 'n' => 8, 'o' => 7, 'p' => 8, 'q' => 8, 'r' => 7, 's' => 7, 't' => 6, 'u' => 8, 'v' => 8, 'w' => 8, 'x' => 8, 'y' => 8, 'z' => 8);
	$map2 += array('0' => 8, '1' => 6, '2' => 8, '3' => 8, '4' => 8, '5' => 8, '6' => 8, '7' => 8, '8' => 8, '9' => 8);
	$map2 += array('-0' => 6, '-1' => 6, '-2' => 4, '-3' => 3, '-4' => 2, '-5' => 7, '-6' => 4, '-7' => 6, '-8' => 6, '-9' => 3);
	$map2 += array('-10' => 2, '-11' => 6, '-12' => 7, '-13' => 4, '-14' => 8);
	$map2 += array('à' => 7, 'è' => 7, 'é' => 7, 'ì' => 4, 'ò' => 8, 'ù' => 8, 'È' => 8);

	foreach ($map1 as $key => $values) {
		echo '.tile' . $key . 'x16 { width: ' . $key . 'px; height: 16px }';
		foreach ($values as $i => $value) {
			echo '.neugier-' . $value . '{ background: #c6def7 url(./images/preview/alfabeto.png?1) ' . ($key*$i)*-1 . 'px ' . 16*($key-1)*-1 . 'px; }';
		}
	}

?>

.tile8x16 {
	width: 8px;
	height: 16px;
	background-color: transparent;
}

.box-test {
	margin: 0px;
	width: 256px;
	height: 239px;
	background-image: url('./images/preview/neugier-preview.png');
	margin-bottom: 5px;
}
.box-test-container {
	margin: 0px;
	height: 239px;
	line-height: 14px;
	padding-top: 127px;
	padding-left: 40px;
}

</style>


<?php

	function textClean($text) {
		if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
			$text = str_replace(PHP_EOL, NEWLINECHAR, $text);
		}
		return $text;
	}

	function neugierTextClean($text) {
		//
		$text = str_replace('{08}', '…', $text);
		$text = str_replace('{47}', 'ō', $text);
		$text = str_replace('{8b}', '~', $text);
		// single quotes
		$text = str_replace('{28}', '', $text);
		$text = str_replace('{42}', '', $text);
		// double quotes
		$text = str_replace('{0e}', '”', $text);
		$text = str_replace('{6f}', '“', $text);
		//
		$text = preg_replace('/\{..\}/', '', $text);

		return $text;
	}

	function neugierTextDecoder($char) {
		switch ($char) {
			case ' ':
				$char = '-0';
				break;
			case '…':
				$char = '-1';
				break;
			case ',':
				$char = '-2';
				break;
			case '.':
				$char = '-3';
				break;
			case '!':
				$char = '-4';
				break;
			case '?':
				$char = '-5';
				break;
			case ')':
				$char = '-6';
				break;
			case '”':
				$char = '-7';
				break;
			case '“':
				$char = '-8';
				break;
			case "'":
				$char = '-9';
				break;
			case ':':
				$char = '-10';
				break;
			case '-':
				$char = '-11';
				break;
			case 'ō':
				$char = '-12';
				break;
			case '(':
				$char = '-13';
				break;
			case '~':
				$char = '-14';
				break;
		}
		return $char;
	}

	$id_text = $_POST['id_text'];
	$source = $_POST['text'];
	$cleanedSources = neugierTextClean($source);

	$boxes = array();
	$lines = explode("\n", $cleanedSources);
	echo count($lines);
	for ($i=0; $i<count($lines); $i+=4) {
		$box = array_slice($lines, $i, 4);
		array_push($boxes, $box);
	}

	foreach ($boxes as $box) {
		echo '<div class="box-test"><div class="box-test-container">';
		$cleanedText = neugierTextClean($box);
		foreach ($box as $i => $line) {
			if ($i > 0) {
				echo '<img class="tile-8x16" src="./images/preview/placeholder-8x16.png" />';
			}
			$line = preg_split('//u', $line, -1, PREG_SPLIT_NO_EMPTY);
			for ($i=0; $i<count($line); $i++) {
				$char = neugierTextDecoder($line[$i]);
				$class = isset($map2[$char]) ? 'tile' . $map2[$char] . 'x16' : 'tile4x16';
				echo '<img class="tile ' . $class . ' neugier-' . $char . '" src="./images/preview/placeholder-8x16.png">';
			}
			echo '<br />';
		}
		echo '</div></div>';
	}

?>