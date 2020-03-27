<?php
	header('Content-Type: text/html; charset=utf-8');
	require_once 'config.inc.php';
?>

<style>

<?php

	$table = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
	$table = array_merge($table, array('', '', '', '', '', '', ')', '', '', ''));
	$table = array_merge($table, array('', '', '', '.', '', '', '', '', '', ''));
	$table = array_merge($table, array('', ''));
	$table = array_merge($table, array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'));
	$table = array_merge($table, array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'));
	$table = array_merge($table, array('', '', '?', '', '', '', ':', ';', '', 'à'));
	$table = array_merge($table, array('è', 'é', 'ì', 'ò', 'ù', 'È', '', '', '\'', '"'));
	$table = array_merge($table, array('-', ',', '', '', '', '', '', '', '', ''));
	$table = array_merge($table, array('', '', '', '', '', '', '', '', '', ''));
	$table = array_merge($table, array('', '', '', '!', '', '', '', '', '', ''));

	foreach ($table as $i => $value) {
		echo '.bl-font1-' . $i . '{ background: url(./images/preview/bl-font1-en.png) ' . (8*$i)*-1 . 'px 0; }';
	}

?>

.tile8x16 {
	width: 8px;
	height: 16px;
}

.box-dialog {
	margin: 0px 0px 5px 0px;
	width: 256px;
	height: 224px;
	background-image: url('./images/preview/bl-preview.png');
}
.box-dialog-container {
	margin: 0px;
	height: 224px;
	line-height: 13px;
	padding-top: 144px;
	padding-left: 24px;
}

</style>

<?php

	function textClean($text) {
		if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
			$text = str_replace(PHP_EOL, NEWLINECHAR, $text);
		}
		return $text;
	}

	function bofTextClean($text) {
		//
		$text = preg_replace('/\{f6}{..\}/', '', $text);
		$text = preg_replace('/\{fb}{..\}{..\}{..\}{..\}{..\}/', '', $text);
		$text = preg_replace('/\{fc}{..\}{..\}{..\}{..\}{..\}/', '', $text);
		$text = preg_replace('/\{fd}{..\}{..\}/', '', $text);
		$text = preg_replace('/\{fe}{..\}{..\}/', '', $text);
		$text = preg_replace('/\{ff}{..\}{..\}{..\}/', '', $text);
		//
		$text = str_replace('{ee}', ' ', $text);
		$text = str_replace('{ef}', ' ', $text);
		$text = str_replace('{82}', '', $text);
		$text = str_replace('{88}', 'PLAYER', $text);
		$text = str_replace('<white>', '', $text);
		$text = str_replace('<input>', '', $text);
		//
		$text = str_replace('{f7}', '', $text);
		return $text;
	}

	$id_text = $_POST['id_text'];
	$source = $_POST['text'];
	$cleanedSources = bofTextClean($source);
	$boxes = explode('{fa}', $cleanedSources);
	foreach ($boxes as $box) {
		echo '<div class="box-dialog"><div class="box-dialog-container">';
		$cleanedText = bofTextClean($box);
		$lines = explode("\n", $cleanedText);
		foreach ($lines as $i => $line) {
			$line = preg_split('//u', $line, -1, PREG_SPLIT_NO_EMPTY);
			for ($i=0; $i<count($line); $i++) {
				$char = $line[$i];
				$key = array_search($char, $table);
				if ($key !== false) {
					echo '<img class="tile-8x16 bl-font1-' . $key . '" src="./images/preview/placeholder-8x16.png" />';
				} else {
					echo '<img class="tile-8x16" src="./images/preview/placeholder-8x16.png" />';
				}
			}
			echo '<br />';
		}
		echo '</div></div>';
	}

?>