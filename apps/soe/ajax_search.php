<?php

	session_start();

	header('Content-Type: text/plain');
	//header('Content-type: application/json');
	//header('Access-Control-Allow-Origin: *');

	require_once 'config.inc.php';

	try {
		if (UserManager::isLogged() && UserManager::getRole(APPLICATION_ID) == 'user') {
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					if (isset($_POST['type'])) {
						$data = array();
						$type = $_POST['type'];
						$text_to_search = isset($_POST['text_to_search']) ? $_POST['text_to_search'] : '';
						$author = UserManager::getUsername();
						$db = new SQLite3(SQLITE_FILENAME);
						if ($type == 'id2') {
							$query = "SELECT tx.id, ts.status FROM texts as tx LEFT JOIN (SELECT * FROM trans WHERE author = :author) as ts ON tx.id = ts.id_text WHERE id2 LIKE :text_to_search ORDER BY id ASC";
						} else if ($type == 'original') {
							$query = "SELECT tx.id, ts.status FROM texts as tx LEFT JOIN (SELECT * FROM trans WHERE author = :author) as ts ON tx.id = ts.id_text WHERE text_encoded LIKE :text_to_search ORDER BY id ASC";
						} else if ($type == 'new') {
							$query = "SELECT id_text, status FROM trans WHERE new_text LIKE :text_to_search AND author = :author ORDER BY id_text ASC";
						} else if ($type == 'comment') {
							$query = "SELECT id_text, status FROM trans WHERE comment LIKE :text_to_search AND author = :author ORDER BY id_text ASC";
						} else if ($type == 'duplicates') {
							$query = "SELECT tx.id, ts.status FROM texts as tx LEFT JOIN (SELECT * FROM trans WHERE author = :author) as ts ON tx.id = ts.id_text WHERE text_encoded = (SELECT text_encoded FROM texts WHERE id = :text_to_search) ORDER BY id ASC";
						} else if ($type == 'personal_all') {
							$query = "SELECT tx.id, ts.status FROM texts as tx LEFT JOIN (SELECT * FROM trans WHERE author = :author) as ts ON tx.id = ts.id_text ORDER BY id ASC";
						} else if ($type == 'global_untranslated') {
							$query = "SELECT id, '0' FROM texts WHERE id NOT IN (SELECT distinct(id_text) FROM trans WHERE status = 2) ORDER BY id ASC";
						} else {
							header('HTTP/1.1 400 Bad Request');
							exit;
						}
						$stmt = $db->prepare($query);
						$stmt->bindValue(':author', $author, SQLITE3_TEXT);
						if ($type == 'duplicates') {
							$stmt->bindValue(':text_to_search', "$text_to_search", SQLITE3_TEXT);
						} else if ($type == 'personal_all' || $type == 'global_untranslated') {
						} else {
							$stmt->bindValue(':text_to_search', "%$text_to_search%", SQLITE3_TEXT);
						}
						$results = $stmt->execute();
						while ($row = $results->fetchArray()) {
							array_push($data, array(
								'id' => $row[0],
								'status' => $row[1],
							));
						}
						$results->finalize();
						$db->close();
						unset($db);
						echo json_encode($data);
						break;
					} else {
						header('HTTP/1.1 422 Unprocessable Entity');
						exit;
					}
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