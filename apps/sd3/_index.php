<?php
	header('Content-Type: text/html; charset=utf-8');
	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	function sd3CleanText($text) {
		//$text = str_replace('<00>', "", $text);
		$text = str_replace('<01>', "", $text); //
		$text = str_replace('<02>', "", $text); //
		$text = str_replace('<08>', "", $text);
		$text = str_replace('<07>', "", $text);
		$text = str_replace('<09>', "", $text);
		$text = str_replace('<16>', "", $text);
		//$text = str_replace('<19>', "", $text);
		$text = str_replace('<C2>', "", $text);
		$text = str_replace('<C6>', "", $text);
		$text = str_replace('<F3>', "", $text);
		$text = str_replace('<BOX>', "", $text);
		$text = str_replace('<OPEN>', "", $text);
		$text = str_replace('<WAIT>', "", $text);
		$text = str_replace('<MULTI>', "", $text);
		$text = str_replace('<CHOICE>', "", $text);
		$text = str_replace('<OR>', "", $text);
		//$text = str_replace('<PAGE>', "", $text);
		$text = str_replace('<PAGE>', "<br><br>", $text);
		$text = str_replace('<END>', "", $text);
		$text = str_replace('<CLOSE>', "", $text);
		$text = str_replace('<LINE>', "", $text);
		$text = str_replace('<KEVIN>', "KEVIN", $text);
		$text = str_replace('<CARLIE>', "CARLIE", $text);
		return $text;
	}

	$db = new SQLite3('../../resources/db/sd3.db');
	$query = "SELECT * FROM texts";
	$results = $db->query($query);
	while ($row = $results->fetchArray()) {
		$id = $row['id'];
		$text = $row['text_encoded'];
		//$text = sd3CleanText($text);
		echo '<div style="border: 1px solid red;">' . $id . '<br/>';
		echo $text;
		echo '</div>';
	}
	$results->finalize();
	$db->close();
	unset($db);
?>