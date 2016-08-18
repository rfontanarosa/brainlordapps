<?php

	session_start();

	header('Content-Type: text/plain');
	//header('Content-type: application/json');
	//header('Access-Control-Allow-Origin: *');

	require_once 'config.inc.php';

	if (!function_exists('sqlite_escape_string')) {
		function sqlite_escape_string($string) {
			return str_replace("'", "''", $string);
		}
	}

	function textClean($text) {
		if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
			$text = str_replace(PHP_EOL, NEWLINECHAR, $text);
		}
		return $text;
	}

	function neugierNewTableResolve($text) {
		$text = str_replace('à', '{50}', $text);
		$text = str_replace('è', '{51}', $text);
		$text = str_replace('é', '{52}', $text);
		$text = str_replace('ì', '{53}', $text);
		$text = str_replace('ò', '{54}', $text);
		$text = str_replace('ù', '{55}', $text);
		$text = str_replace('È', '{56}', $text);
		return $text;
	}

	try {
		if (UserManager::isLogged() && UserManager::hasRole(APPLICATION_ID)) {
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					$result = array();
					$id_text = $_POST['id_text'];
					$author = UserManager::getUsername();
					$new_text = $_POST['new_text'];
					$status = $_POST['status'];
					$comment = sqlite_escape_string($_POST['comment']);
					$time = time();
					$new_text = textClean($new_text);
					$new_text = sqlite_escape_string($new_text);
					$new_text2 = neugierNewTableResolve($new_text);
					$db = new SQLite3(SQLITE_FILENAME);
					$query = "INSERT OR REPLACE INTO trans VALUES('$id_text', '$author', '$new_text', '$new_text2', '$status', '$time', '$comment')";
					$db->query($query);
					$db->close();
					unset($db);
					$updateDate = @date('d/m/Y, G:i', $time);
					$result['updateDate'] = $updateDate;
					echo json_encode($result);
					break;
				default:
					header('HTTP/1.1 405 Method Not Allowed');
					exit;
			}
		} else {
			header('HTTP/1.1 401 Unauthorized');
			exit;
		}
	} catch (Exception $e) {
		header('HTTP/1.1 500 Internal Server Error');
		exit;
	}

?>