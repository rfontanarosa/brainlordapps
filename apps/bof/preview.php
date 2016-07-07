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

	$table = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
	$table = array_merge($table, array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'));
	$table = array_merge($table, array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'));
	$table = array_merge($table, array(',', '.', '!', '?', '\''));
	$table = array_merge($table, array('È', 'à', 'è', 'é', 'ì', 'ò', 'ù'));
	foreach ($table as $i => $value) {
		echo '.bof-font1-' . $i . '{ background: url(./images/preview/bof-font1-en.png) ' . (8*$i)*-1 . 'px 0; }';
	}

?>

.box-test {
	margin: 0px;
	width: 256px;
	height: 239px;
	background-image: url('./images/preview/bof-preview.png');
}
.box-test-container {
	margin: 0px;
	height: 239px;
	line-height: 14px;
	padding-top: 151px;
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
		$text = str_replace('{01}', '', $text);
		return $text;
	}

	$id_text = $_POST['id_text'];
	$source = $_POST['text'];
	$boxes = explode('{04}', $source);
	foreach ($boxes as $box) {
		echo '<div class="box-test"><div class="box-test-container">';
		$cleanedText = bofTextClean($box);
		$lines = explode("\n", $cleanedText);
		foreach ($lines as $i => $line) {
			if ($i > 0) {
				echo '<img class="tile-8x16" src="./images/preview/placeholder-8x16.png" />';
			}
			$line = preg_split('//u', $line, -1, PREG_SPLIT_NO_EMPTY);
			for ($i=0; $i<count($line); $i++) {
				$char = $line[$i];
				$key = array_search($char, $table);
				if ($key !== false) {
					echo '<img class="tile-8x16 bof-font1-' . $key . '" src="./images/preview/placeholder-8x16.png" />';
				} else {
					echo '<img class="tile-8x16" src="./images/preview/placeholder-8x16.png" />';
				}
			}
			echo '<br />';
		}
		echo '</div></div>';
	}

?>