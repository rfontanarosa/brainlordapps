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
	$table = array_merge($table, array(',', '.', '!', '?', '\'', ':', '', '', '', '', ''));
	$table = array_merge($table, array('à', 'è', 'é', 'ì', 'ò', 'ù', 'È'));
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
		// characters' name
		$text = str_replace('{07}{00}', 'RyuX', $text);
		$text = str_replace("{07}{01}", 'BoXX', $text);
		$text = str_replace("{07}{02}", 'Nina', $text);
		$text = str_replace("{07}{03}", 'OxXX', $text);
		$text = str_replace("{07}{04}", 'Gobi', $text);
		$text = str_replace("{07}{05}", 'Karn', $text);
		$text = str_replace("{07}{06}", 'Mogu', $text);
		$text = str_replace("{07}{07}", 'Bleu', $text);
		//
		$text = preg_replace('/\{08}{..\}/', 'XXXXXXX', $text);
		$text = preg_replace('/\{09}{..\}/', 'XXXXXXX', $text);
		$text = preg_replace('/\{0a}{..\}/', 'XXXXXXX', $text);
		$text = preg_replace('/\{0b}{..\}/', 'XXXXXXX', $text);
		$text = preg_replace('/\{0c}{..\}/', 'XXXXXXX', $text);
		// commands
		$text = str_replace('{01}', '', $text);
		$text = str_replace('{05}', '', $text);
		$text = str_replace('{06}', 'XXXX', $text);
		// special characters
		$text = str_replace('{28}', ' ', $text);
		$text = str_replace('{24}', ' ', $text);
		return $text;
	}

	$id_text = $_POST['id_text'];
	$source = $_POST['text'];
	$cleanedSources = bofTextClean($source);
	$boxes = explode('{04}', $cleanedSources);
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