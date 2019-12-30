<?php

	session_start();

	header('Content-Type: text/html; charset=utf-8');

	require_once 'config.inc.php';

	if (UserManager::isLogged() && UserManager::getRole(APPLICATION_ID) == 'user') {

		$uname = UserManager::getUsername();

		function ys3CleanText($text) {
			$text = str_replace('{f0}CB{f1}', '', $text);
			$text = substr($text, 2);
			$text = str_replace('{{{', '', $text);
			$text = str_replace('}}}', '', $text);
			$text = str_replace('{45}', '-', $text);
			$text = str_replace('{fc}{ff}', '', $text);
			return $text;
		}

		$db = new SQLite3(SQLITE_FILENAME);
		$query = "SELECT * FROM trans INNER JOIN texts ON trans.id_text = texts.id WHERE id_text >= 427 AND id_text <= 543 AND author='$uname'";
		$results = $db->query($query);
		while ($row = $results->fetchArray()) {
			$id = $row['id_text'];
			$otext = $row['text_encoded'];
			$text = $row['new_text'];
			$text = ys3CleanText($text);
			$otext = ys3CleanText($otext);
			$len = strlen($text);
			if ($len >= 26) {
				echo '<div style="color: red;">';
			} else {
				echo '<div>';
			}
			echo $id . ' - ';
			echo $len . ' - ';
			echo $text;
			echo ' - ' . $otext;
			echo '</div>';
		}
		$results->finalize();
		$db->close();
		unset($db);

	}

?>