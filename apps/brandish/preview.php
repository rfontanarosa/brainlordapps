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

	$table = array();
	$type = $_POST['type'];
	if ($type == 'original_text') {
		$table = array_merge($table, array('!', '', '#', '$', '%', '&', '\'', '(', ')', '*', '+', ',', '-', '.', '/'));
		$table = array_merge($table, array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'));
		$table = array_merge($table, array('', '', '', '', '', '?', ''));
		$table = array_merge($table, array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'));
		$table = array_merge($table, array('', '', '', '', '_', ''));
		$table = array_merge($table, array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'));
		$table = array_merge($table, array('', '', '', ''));
		foreach ($table as $i => $value) {
			echo '.brandish-font1-' . $i . '{ background: url(./previewer/images/brandish-font1-en.png) ' . (8*$i)*-1 . 'px 0; }';
		}
	} else {
		$table = array_merge($table, array('!', '', '#', '$', '%', '&', '\'', '(', ')', '*', '+', ',', '-', '.', '/'));
		$table = array_merge($table, array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'));
		$table = array_merge($table, array('', '', '', '', '', '?', ''));
		$table = array_merge($table, array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'));
		$table = array_merge($table, array('è', 'é', 'ì', 'ò', 'ù', 'È'));
		$table = array_merge($table, array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'));
		$table = array_merge($table, array('', 'à', '', ''));
		foreach ($table as $i => $value) {
			echo '.brandish-font1-' . $i . '{ background: url(./previewer/images/brandish-font1-it.png) ' . (8*$i)*-1 . 'px 0; }';
		}
	}

?>

.box-explore {
	margin: 0px;
	width: 256px;
	height: 224px;
	background-image: url('./previewer/images/brandish-preview-1-101.png');
}
.box-explore-container {
	margin: 0px;
	padding-top: 167px;
	padding-left: 24px;
	height: 224px;
	line-height: 6px;
}

.box-dialog {
	margin: 0px;
	width: 256px;
	height: 224px;
	background-image: url('./previewer/images/brandish-preview-243.png');
}
.box-dialog-container {
	margin: 0px;
	padding-top: 119px;
	padding-left: 24px;
	height: 224px;
	line-height: 6px;
}

.box-sign {
	margin: 0px;
	width: 256px;
	height: 224px;
	background-image: url('./previewer/images/brandish-preview-102-151.png');
}
.box-sign-container {
	margin: 0px;
	padding-top: 55px;
	padding-left: 64px;
	height: 224px;
	line-height: 6px;
}

</style>

<?php

	function brandishBoxPropertiesCalculator($text) {
		$max_chars = 26;
		$props = substr($text, 0, 12);
		if ($props == '{03}{0f}{1a}' || $props == '{03}{11}{1a}') {
			$max_chars = 26;
		} elseif ($props == '{05}{10}{16}') {
			$max_chars = 22;
		} elseif ($props == '{04}{14}{18}') {
			$max_chars = 24;
		}
		return array('max_chars' => $max_chars);
	}

	function brandishTextClean($text) {
		$text = preg_replace('/\{04\}/', 'XXX', $text);
		$text = preg_replace('/\{..\}/', '', $text); 
		return $text;
	}

	$id_text = (int)$_POST['id'];
	$source = $_POST['text'];
	$cleanedSource = textClean($source);
	$boxProperties = brandishBoxPropertiesCalculator($cleanedSource);
	$max_chars = $boxProperties['max_chars'];

	if ($id_text >= 102 && $id_text <= 151) {
		echo '<div class="box-sign"><div class="box-sign-container">';
		$cleanedText = brandishTextClean($cleanedSource);
		for ($i=0; $i<10; $i++) {
			$line = mb_substr($cleanedText, 16*$i, 16);
			$line = preg_split('//u', $line, -1, PREG_SPLIT_NO_EMPTY);
			for ($k=0; $k<count($line); $k++) {
				$char = $line[$k];
				$key = array_search($char, $table);
				if ($key !== false) {
					echo '<img class="tile-8x8 brandish-font1-' . $key . '" src="./previewer/images/placeholder-8x8.png" />';
				} else {
					echo '<img class="tile-8x8" src="./previewer/images/placeholder-8x8.png" />';
				}
			}
			echo '<br />';
		}
		echo '</div></div>';
	} else {
		$boxes = explode('{01}', $cleanedSource);
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
			if ($id_text >= 1 && $id_text <= 101) {
				echo '<div class="box-explore"><div class="box-explore-container">';
			}
			else {
				if ($id_text == 241) echo '<div class="box-explore" style="background-image: url(\'./previewer/images/brandish-preview-241.png\');">';
				else if ($id_text == 242) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-242.png\');">';
				else if ($id_text == 243) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-243.png\');">';
				else if ($id_text == 248) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-248.png\');">';
				else if ($id_text == 249) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-249.png\');">';
				else if ($id_text == 250) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-250.png\');">';
				else if ($id_text == 251) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-251.png\');">';
				else if ($id_text == 252) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-252.png\');">';
				else if ($id_text == 253) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-253.png\');">';
				else if ($id_text == 254) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-254.png\');">';
				else if ($id_text == 255) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-255.png\');">';
				else if ($id_text == 257) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-257.png\');">';
				else if ($id_text == 258) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-258.png\');">';
				else if ($id_text == 261) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-261.png\');">';
				else if ($id_text == 262) echo '<div class="box-dialog" style="background-image: url(\'./previewer/images/brandish-preview-262.png\');">';
				else echo '<div class="box-dialog">';
				echo '<div class="box-dialog-container">';
			}
			foreach ($lines as $line) {
				$line = preg_split('//u', $line, -1, PREG_SPLIT_NO_EMPTY);
				for ($i=0; $i<count($line); $i++) {
					$char = $line[$i];
					$key = array_search($char, $table);
					if ($key !== false) {
						echo '<img class="tile-8x8 brandish-font1-' . $key . '" src="./previewer/images/placeholder-8x8.png" />';
					} else {
						echo '<img class="tile-8x8" src="./previewer/images/placeholder-8x8.png" />';
					}
				}
				echo '<br />';
			}
			echo '</div></div>';
		}

	}

?>