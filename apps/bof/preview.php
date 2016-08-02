<?php
	header('Content-Type: text/html; charset=utf-8');
	require_once 'config.inc.php';
?>

<style>


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

.tile8x16 {
	width: 8px;
	height: 16px;
}

.box-dialog {
	margin: 0px 0px 5px 0px;
	width: 256px;
	height: 239px;
	background-image: url('./images/preview/bof-preview.png');
}
.box-dialog-container {
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
		$text = str_replace('{09}{01}', 'Drogen', $text);
		$text = str_replace("{09}{02}", 'Nanai', $text);
		$text = str_replace("{09}{03}", 'Winlan', $text);
		$text = str_replace("{09}{04}", 'Romero', $text);
		$text = str_replace("{09}{05}", 'Gust', $text);
		$text = str_replace("{09}{06}", 'Camlon', $text);
		$text = str_replace("{09}{07}", 'Nada', $text);
		$text = str_replace("{09}{08}", 'Tantar', $text);
		$text = str_replace('{09}{09}', 'Tuntar', $text);
		$text = str_replace("{09}{0a}", 'Agua', $text);
		$text = str_replace("{09}{0b}", 'Auria', $text);
		$text = str_replace("{09}{0c}", 'Bleak', $text);
		$text = str_replace("{09}{0d}", 'Arad', $text);
		$text = str_replace("{09}{0e}", 'Spring', $text);
		$text = str_replace("{09}{0f}", 'Tunlan', $text);
		$text = str_replace('{09}{10}', 'Gant', $text);
		$text = str_replace("{09}{11}", 'Scande', $text);
		$text = str_replace("{09}{12}", 'Carmen', $text);
		$text = str_replace("{09}{13}", 'Gramor', $text);
		$text = str_replace("{09}{14}", 'Wisdon', $text);
		$text = str_replace("{09}{15}", 'Karma', $text);
		$text = str_replace("{09}{16}", 'Prima', $text);
		$text = str_replace("{09}{17}", 'Crypt', $text);
		$text = str_replace('{09}{18}', 'Nabal', $text);
		$text = str_replace("{09}{19}", 'Tock', $text);
		$text = str_replace("{09}{1a}", 'Spyre', $text);
		$text = str_replace("{09}{1b}", 'ObeliskX', $text);
		$text = str_replace("{09}{1c}", 'Pagoda', $text);
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
		$text = str_replace('{2d}', ' ', $text);
		$text = str_replace('{2e}', ' ', $text);
		return $text;
	}

	$id_text = $_POST['id_text'];
	$source = $_POST['text'];
	$cleanedSources = bofTextClean($source);
	$boxes = explode('{04}', $cleanedSources);
	foreach ($boxes as $box) {
		echo '<div class="box-dialog"><div class="box-dialog-container">';
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