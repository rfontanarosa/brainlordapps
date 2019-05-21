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

	try {
		if (UserManager::isLogged() && UserManager::hasRole(APPLICATION_ID)) {
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					$data = array();
					$type = $_POST['type'];
					$text_to_search = sqlite_escape_string($_POST['text_to_search']);
					$author = UserManager::getUsername();
					$db = new SQLite3(SQLITE_FILENAME);
					$query = "SELECT id FROM texts WHERE text_encoded LIKE '%$text_to_search%' ORDER BY id ASC";
					if ($type == 'new') {
						$query = "SELECT id_text FROM trans WHERE new_text LIKE '%$text_to_search%' ORDER BY id_text ASC";
					}
					$result = $db->query($query);
					while ($row = $result->fetchArray()) {
						array_push($data, $row[0]);
					}
					$db->close();
					unset($db);
					echo json_encode($data);
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