<?php
	header('Content-Type: text/html; charset=utf-8');
	require_once 'config.inc.php';
?>

<style>

<?php

	$map1 = array();
	$map1 += array('2' => array('i', 'l', 'I'));
	$map1 += array('3' => array('j', '1', '-0', '-1', '-2', '-5', 'ì'));
	$map1 += array('4' => array('J', '-10', '-11'));
	$map1 += array('5' => array('r', 's', 't', '-3', '-6', '-7', '-8', '-9'));
	$map1 += array('6' => array('b', 'c', 'd', 'e', 'f', 'h', 'k', 'n', 'p', 'q', 'u', 'z', 'B', 'E', 'F', 'L', 'P', 'S', 'è', 'é', 'ù', 'È'));
	$map1 += array('7' => array('a', 'g', 'o', '3', '5', '6', '8', '9', 'R', '-4', 'à', 'ò', '-14'));
	$map1 += array('8' => array('v', 'x', 'y', '0', '2', '4', '7', 'D', 'Y'));
	$map1 += array('9' => array('C', 'H', 'K', 'U', 'Z'));
	$map1 += array('10' => array('m', 'w', 'G', 'T', 'X', '-13'));
	$map1 += array('11' => array('N', '-12'));
	$map1 += array('12' => array('A', 'O', 'Q', 'V'));
	$map1 += array('14' => array('M', 'W'));
	
	$map2 = array();
	$map2 += array('i' => 2, 'l' => 2, 'I' => 2);
	$map2 += array('j' => 3, '1' => 3);
	$map2 += array('J' => 4);
	$map2 += array('r' => 5, 's' => 5, 't' => 5);
	$map2 += array('b' => 6, 'c' => 6, 'd' => 6, 'e' => 6, 'f' => 6, 'h' => 6, 'k' => 6, 'n' => 6, 'p' => 6, 'q' => 6, 'u' => 6, 'z' => 6, 'B' => 6, 'E' => 6, 'F' => 6, 'L' => 6, 'P' => 6, 'S' => 6, 'È' => 6);
	$map2 += array('a' => 7, 'g' => 7, 'o' => 7, '3' => 7, '5' => 7, '6' => 7, '8' => 7, '9' => 7, 'R' => 7, '-14' => 7);
	$map2 += array('v' => 8, 'x' => 8, 'y' => 8, '0' => 8, '2' => 8, '4' => 8, '7' => 8, 'D' => 8, 'Y' => 8);
	$map2 += array('C' => 9, 'H' => 9, 'K' => 9, 'U' => 9, 'Z' => 9);
	$map2 += array('m' => 10, 'w' => 10, 'G' => 10, 'T' => 10, 'X' => 10);
	$map2 += array('N' => 11);
	$map2 += array('A' => 12, 'O' => 12, 'Q' => 12, 'V' => 12);
	$map2 += array('M' => 14, 'W' => 14);
	$map2 += array('-0' => 3, '-1' => 3, '-2' => 3, '-3' => 5, '-4' => 7, '-5' => 3, '-6' => 5, '-7' => 5, '-8' => 5, '-9' => 5, '-10' => 4, '-11' => 4, '-12' => 11, '-13' => 10);
	$map2 += array('à' => 7, 'è' => 6, 'é' => 6, 'ì' => 3, 'ò' => 7, 'ù' => 6);
	
	foreach ($map1 as $key => $values) {
		echo '.tile' . $key . 'x16 { width: ' . $key . 'px; height: 16px }';
		foreach ($values as $i => $value) {
			//echo '.ys4-' . $value . '{ background: url(./images/preview/alfabeto' . $key . '.png) ' . ($key*$i)*-1 . 'px 0; }';
			echo '.ys4-' . $value . '{ background: #c6def7 url(./images/preview/alfabeto.png) ' . ($key*$i)*-1 . 'px ' . 16*($key-1)*-1 . 'px; }';
		}
	}

?>

.box-dialog {
	margin: 0px;
	width: 256px;
	height: 239px;
	background-image: url('./images/preview/preview3.png');
}
.box-dialog-container {
	margin: 0px;
	height: 239px;
	line-height: 14px;
}

.box-introduction {
	margin: 0px;
	width: 256px;
	height: 239px;
	background-image: url('./images/preview/preview4.png');
}

.box-introduction-container {
	margin: 0px;
	height: 239px;
	line-height: 14px;
	text-align: center;
}

.tile4x16 {
	background-color: black !important;
}

</style>

<?php

	function textClean($text) {
		if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
			$text = str_replace(PHP_EOL, NEWLINECHAR, $text);
		}
		return $text;
	}

	function ys4BoxPropertiesCalculator($props) {
		//$props = str_replace(PHP_EOL, NEWLINECHAR, $props);
		$props = str_replace('{f0}', '', $props);
		$props = str_replace('{f1}', '', $props);
		$props = str_replace('{f5}', '', $props);
		$props = str_replace('{f6}{03}', '', $props);
		$props = str_replace('{f7}', '', $props);
		//$props = str_replace('{fe}', '', $props);
		$props = str_replace('\'', '{01}', $props);
		$props = str_replace('.', '{02}', $props);
		$props = str_replace(',', '{03}', $props);
		$props = str_replace('+', '{05}', $props);
		$props = str_replace('?', '{06}', $props);
		$props = str_replace('!', '{07}', $props);
		$props = str_replace('-', '{08}', $props);
		$props = str_replace('…', '{0b}', $props);
		$props = str_replace('"', '{0c}', $props);
		$props = str_replace('(', '{0d}', $props);
		$props = str_replace(')', '{0e}', $props);
		$props = str_replace('[', '{0f}', $props);
		$props = str_replace(']', '{10}', $props);
		$props = str_replace('#', '{1a}', $props);
		$props = str_replace('&', '{1b}', $props);
		echo $props;
		$color = substr($props, 1, 2);
		$left = substr($props, 5, 2);
		$top = substr($props, 9, 2);
		$width = substr($props, 13, 2);
		$height = substr($props, 17, 2);
		$table = array('00' => 0, '01' => 1, '02' => 2, '03' => 3, '04' => 4, '05' => 5, '06' => 6, '07' => 7, '08' => 8, '09' => 9, '0a' => 10, '0b' => 11, '0c' => 12, '0d' => 13, '0e' => 14, '0f' => 15, '10' => 16, '11' => 17, '12' => 18, '13' => 19, '14' => 20, '15' => 21, '16' => 22, '17' => 23, '18' => 24, '19' => 25, '1a' => 26, '1b' => 27, '1c' => 28);
		$colors = array('00' => '#c6def7', '01' => '#cece00', '02' => '#ff4a18', '03' => '#ff00ff', '04' => '#10ff10', '05' => '#b552ef', '06' => '#2194ff', '07' => '#42deff', '08' => '#ff8c6b', '09' => '#946bff', '0a' => '#ce8463');
		$color = $colors[$color];
		$left = $table[$left];
		$top = $table[$top];
		$width = $table[$width];
		$height = $table[$height];
		return array('color' => $color, 'left' => $left, 'top' => $top, 'width' => $width, 'height' => $height);
	}

	function ys4TextClean($text) {
		$text = str_replace('{f4}T{f4}9{fd}{ff}', '', $text);
		$text = str_replace('{fb}{f4}{14}{fe}', '', $text);
		$text = str_replace('{f5}{fe}', '', $text);
		$text = str_replace('{f4}{0a}', '', $text);
		$text = str_replace('{f4}{14}', '', $text);
		$text = str_replace('{f4}{1e}', '', $text);
		$text = str_replace('{f6}?', '', $text);
		$text = str_replace('{f6},', '', $text);
		$text = str_replace('{f6}+', '', $text);
		$text = str_replace('{f4}G', '', $text);
		$text = str_replace('{f4}0', '', $text);
		$text = str_replace('{f4}T', '', $text);
		$text = str_replace('{f6}{00}', '', $text);
		$text = str_replace('{f6}{04}', '', $text);
		$text = str_replace('{f9} ', '', $text);
		$text = str_replace('{cd}I', '', $text);
		$text = str_replace('{fd}', '', $text);
		$text = str_replace('{fc}', '', $text);
		$text = str_replace('{ff}', '', $text);
		$text = str_replace('{0b}', '…', $text);
		return $text;
	}

	function ys4TextDecoder($char) {
		switch ($char) {
			case '\'':
				$char = '-0';
				break;
			case '.':
				$char = '-1';
				break;
			case ',':
				$char = '-2';
				break;
			case '+':
				$char = '-3';
				break;
			case '?':
				$char = '-4';
				break;
			case '!':
				$char = '-5';
				break;
			case '-':
				$char = '-6';
				break;
			case '"':
				$char = '-7';
				break;
			case '(':
				$char = '-8';
				break;
			case ')':
				$char = '-9';
				break;
			case '[':
				$char = '-10';
				break;
			case ']':
				$char = '-11';
				break;
			case '#':
				$char = '-12';
				break;
			case '&':
				$char = '-13';
				break;
			case '…':
				$char = '-14';
				break;
		}
		return $char;
	}

	$id_text = $_POST['id_text'];
	$source = $_POST['pre_text'];
	$text = $_POST['text'];
	$boxes = explode('{f7}', $text);
	foreach ($boxes as $i => $box) {
		$text = explode('{f3}{00}', $box);
		if (count($text) == 1) {
			$text = explode('{f2}{00}', $box);
		}
		if (count($text) > 1) {
			$source = $text[0];
			$textWithoutProps = $text[1];
		} else {
			$textWithoutProps = $text[0];
		}
		$cleanedSource = textClean($source);
		$boxProperties = ys4BoxPropertiesCalculator($cleanedSource);
		$paddingTop = 8 + ($boxProperties['top'] * 8);
		$paddingLeft = 8 + ($boxProperties['left'] * 8);
		$width = $paddingLeft + 8 + ($boxProperties['width'] * 12);
		if ($id_text >= 1277 && $id_text < 1300) {
			echo '<style> .box-dialog-' . $i . ' .tile { background-color: ' . $boxProperties['color'] .'; } </style>';
			echo '<div class="box-introduction box-introduction-' . $i . '"><div class="box-introduction-container" style="border-right: solid 1px yellow; width: ' . $width . 'px; padding-top: ' . $paddingTop . 'px; padding-left: ' . $paddingLeft . 'px;">';
		} else {
			echo '<style> .box-dialog-' . $i . ' .tile { background-color: ' . $boxProperties['color'] .'; } </style>';
			echo '<div class="box-dialog box-dialog-' . $i . '"><div class="box-dialog-container" style="border-right: solid 1px yellow; width: ' . $width . 'px; padding-top: ' . $paddingTop . 'px; padding-left: ' . $paddingLeft . 'px;">';
		}
		$cleanedText = textClean($textWithoutProps);
		$cleanedText = ys4TextClean($cleanedText);
		$lines = explode('{fe}', $cleanedText);
		foreach ($lines as $line) {
			$line = preg_split('//u', $line, -1, PREG_SPLIT_NO_EMPTY);
			for ($i=0; $i<count($line); $i++) {
				$char = ys4TextDecoder($line[$i]);
				$class = isset($map2[$char]) ? 'tile' .$map2[$char] . 'x16' : 'tile4x16';
				echo '<img class="tile ' . $class . ' ys4-' . $char . '" src="./images/preview/placeholder-8x16.png">';
			}
			echo '<br />';
		}
		echo '</div></div>';
	}

?>