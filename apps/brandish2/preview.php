<?php
	header('Content-Type: text/html; charset=utf-8');
	require_once 'config.inc.php';
?>


<?php

	$table = array(
		'0' => 30, '1' => 31, '2' => 32, '3' => 33, '4' => 34, '5' => 35, '6' => 36, '7' => 37, '8' => 38, '9' => 39, ' ' => '20', '-' => '2b', '.' => '2e', 
		'?' => '82', '!' => '83', '~' => '84', '(' => '85', ')' => '86', 
		'[' => '9c', ']' => '9d', '\' ' => 'a0', 'a6' => 'a6', ', ' => 'ac', 'ae' => 'ae', 
		'a' => 41, 'b' => 42, 'c' => 43, 'd' => 44, 'e' => 45, 'f' => 46, 'g' => 47, 'h' => 48, 'i' => 49, 'j' => '4a', 'k' => '4b', 'l' => '4c', 'm' => '4d', 'n' => '4e', 'o' => '4f', 'p' => '50', 'q' => '51', 'r' => '52', 's' => '53', 't' => '54', 'u' => '55', 'v' => '56', 'w' => '57', 'x' => '58', 'y' => '59', 'z' => '5a', 
		'A' => 'c1', 'B' => 'c2', 'C' => 'c3', 'D' => 'c4', 'E' => 'c5', 'F' => 'c6', 'G' => 'c7', 'H' => 'c8', 'I' => 'c9', 'J' => 'ca', 'K' => 'cb', 'L' => 'cc', 'M' => 'cd', 'N' => 'ce', 'O' => 'cf', 'P' => 'd0', 'Q' => 'd1', 'R' => 'd2', 'S' => 'd3', 'T' => 'd4', 'U' => 'd5', 'V' => 'd6', 'W' => 'd7', 'X' => 'd8', 'Y' => 'd9', 'Z' => 'da', 
		'\'a' => 'e1', '\'b' => 'e2', '\'c' => 'e3', '\'d' => 'e4', '\'e' => 'e5', '\'l' => 'ec', '\'m' => 'ed', '\'n' => 'ef', '\'r' => 'f2', '\'s' => 'f3', '\'t' => 'f4', '\'v' => 'f6'
	);

?>

<style>

.box-dialog {
	padding: 0px;
	margin: 0px;
	width: 216px;
	height: 72px;
	background-image: url('./images/preview/box-dialog.png');
}

.box-dialog-container {
	color: white;
	padding-top: 7px;
	line-height: 14px;
	padding-left: 8px;
	height: 65px;
}

.box-dialog-container img {
	border: none;
	margin: 0px;
	padding: 0px;
}

.box-alert {
	padding: 0px;
	margin: 0px;
	width: 216px;
	height: 24px;
	background-image: url('./images/preview/box-alert.png');
}

.box-alert-container {
	color: white;
	padding-top: 7px;
	line-height: 8px;
	padding-left: 8px;
	height: 20px;
	text-align: center;
}

.box-alert-container img {
	border: none;
	margin: 0px;
	padding: 0px;
}

.box-game {
	padding: 0px;
	margin: 0px;
	width: 134px;
	height: 102px;
	background-image: url('./images/preview/box-game.png');
}

.box-game-container {
	color: white;
	padding-top: 7px;
	line-height: 8px;
	padding-left: 0px;
	height: 20px;
	text-align: center;
}

.box-game-container img {
	border: none;
	margin: 0px;
	padding: 0px;
}

.box-sign {
	padding: 0px;
	margin: 0px;
	width: 160px;
	height: 112px;
	background-image: url('./images/preview/box-sign.png');
}

.box-sign-container {
	color: white;
	padding-top: 7px;
	line-height: 8px;
	padding-left: 0px;
	height: 20px;
	text-align: center;
}

.box-sign-container img {
	border: none;
	margin: 0px;
	padding: 0px;
}

.box-shop {
	padding-left: 5px;
	margin: 0px;
	width: 178px;
	height: 86px;
	background-image: url('./images/preview/box-shop.png');
}

.box-shop-container {
	color: white;
	padding-top: 7px;
	line-height: 14px;
	padding-left: 0px;
	height: 86px;
}

.box-shop-container img {
	border: none;
	margin: 0px;
	padding: 0px;
}

.box-information {
	padding: 0px;
	margin: 0px;
	width: 168px;
	height: 124px;
	background-image: url('./images/preview/box-information.png');
}

.box-information-container {
	color: white;
	padding-top: 70px;
	line-height: 14px;
	padding-left: 8px;
	height: 124px;
}

.box-information-container img {
	border: none;
	margin: 0px;
	padding: 0px;
}

.box-item-name {
	padding: 0px;
	margin: 0px;
	width: 120px;
	height: 24px;
	background-image: url('./images/preview/box-item-name.png');
}

.box-item-name-container {
	color: white;
	padding-top: 5px;
	line-height: 0px;
	padding-left: 8px;
	height: 24px;
}

.box-item-name-container img {
	border: none;
	margin: 0px;
	padding: 0px;
}

.box-narration {
	padding: 0px;
	margin: 0px;
	width: 256;
	height: 224px;
	background-image: url('./images/preview/box-narration.png');
}

.box-narration-container {
	color: white;
	padding-top: 5px;
	line-height: 0px;
	padding-left: 8px;
	height: 24px;
}

.box-narration-container img {
	border: none;
	margin: 0px;
	padding: 0px;
}

</style>

<?php

	$type = $_POST['type'];
	if ($type == 'new') {
		$table['à'] = '6a';
		$table['è'] = '6b';
		$table['é'] = '6c';
		$table['ì'] = '6d';
		$table['ò'] = '6e';
		$table['ù'] = '6f';
		$table['È'] = 'e1-2';
		$table['n\''] = 'e2-2';
		$table['l\''] = 'e3-2';
		$table['c\''] = 'e4-2';
		$table['d\''] = 'e5-2';
		$table['C\''] = 'ec-2';
		$table['D\''] = 'ed-2';
		$table['L\''] = 'ee-2';
		$table['s\''] = 'f3-2';
		$table['t\''] = 'f4-2';
		$table['v\''] = 'f5-2';
	}
	
	$source = $_POST['text'];
	$source = str_replace('{02}{00}', '', $source);

	$id_text = $_POST['id_text'];

	$boxes = explode('{02}', $source);
	foreach ($boxes as $box) {
		if ($id_text < 43) echo '<div class="box-game"><div class="box-game-container">';
		if ($id_text >= 42 && $id_text < 215) echo '<div class="box-alert"><div class="box-alert-container">';
		if ($id_text >= 215 && $id_text < 329) echo '<div class="box-alert"><div class="box-alert-container">';
		if ($id_text >= 329 && $id_text < 443) echo '<div class="box-item-name"><div class="box-item-name-container">';
		if ($id_text >= 443 && $id_text < 490) echo '<div class="box-information"><div class="box-information-container">';
		if ($id_text >= 490 && $id_text < 672) echo '<div class="box-dialog"><div class="box-dialog-container">';
		if ($id_text >= 672 && $id_text < 680) echo '<div class="box-narration"><div class="box-narration-container">';
		if ($id_text >= 680 && $id_text < 755) echo '<div class="box-sign"><div class="box-sign-container">';
		if ($id_text >= 755 && $id_text < 771) echo '<div class="box-dialog"><div class="box-dialog-container">';
		if ($id_text >= 771 && $id_text < 881) echo '<div class="box-shop"><div class="box-shop-container">';
		if ($id_text >= 881) echo '<div class="box-narration"><div class="box-narration-container">';
		$lines = explode('{01}', $box);
		foreach ($lines as $line) {
			$line = preg_split('//u', $line, -1, PREG_SPLIT_NO_EMPTY);
			for ($i=0; $i<count($line); $i++) {
				$value = '';
				$char = $line[$i];
				if ($char == '{') {
					if (isset($table[$line[$i+1] . $line[$i+2]])) {
						$value = $table[$line[$i+1] . $line[$i+2]];
					}
					$i = $i+3;
				} else {
					$value = $table[$char];
				}
				if ($value != '') {
					echo "<img src=\"images/preview/$value.png\" />";
				}
			}
			echo '<br />';
		}
		echo '</div></div>';
	}

?>