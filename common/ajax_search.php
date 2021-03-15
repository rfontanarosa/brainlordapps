<?php

	session_start();

	header('Content-Type: text/plain');
	//header('Content-type: application/json');
	//header('Access-Control-Allow-Origin: *');

	require_once './config.inc.php';

	try {
		if (UserManager::isLogged() && UserManager::getRole(APPLICATION_ID) == 'user') {
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					if (isset($_POST['type'])) {
						$data = array();
						$type = $_POST['type'];
						$text_to_search = isset($_POST['text_to_search']) ? $_POST['text_to_search'] : '';
						$whole_word_only = $_POST['whole_word_only'] === 'true';
						$author = UserManager::getUsername();
						$db = new SQLite3(SQLITE_FILENAME);
						if ($type == 'ref') {
							$query = "SELECT tx.id, COALESCE(ts.status, 0) as status FROM texts as tx LEFT JOIN (SELECT * FROM translations AS t2 WHERE t2.author = :author) as ts ON tx.id = ts.id_text WHERE ref LIKE :text_to_search ORDER BY id ASC";
						} else if ($type == 'original') {
							$query = "SELECT tx.id, COALESCE(ts.status, 0) as status, text_decoded FROM texts as tx LEFT JOIN (SELECT * FROM translations AS t2 WHERE author = :author) as ts ON tx.id = ts.id_text WHERE text_decoded LIKE :text_to_search GROUP BY id HAVING MAX(ts.date) ORDER BY id ASC";
						} else if ($type == 'new') {
							$query = "SELECT id_text, COALESCE(status, 0) as status, translation FROM translations WHERE translation LIKE :text_to_search AND author = :author ORDER BY id_text ASC";
						} else if ($type == 'comment') {
							$query = "SELECT id_text, COALESCE(status, 0) as status FROM translations WHERE comment LIKE :text_to_search AND author = :author ORDER BY id_text ASC";
						} else if ($type == 'duplicates') {
							$query = "SELECT tx.id, COALESCE(ts.status, 0) as status FROM texts as tx LEFT JOIN (SELECT * FROM translations WHERE author = :author) as ts ON tx.id = ts.id_text WHERE text_decoded = (SELECT text_decoded FROM texts WHERE id = :text_to_search) ORDER BY id ASC";
						} else if ($type == 'personal_all') {
							$query = "SELECT tx.id, COALESCE(ts.status, 0) as status FROM texts as tx LEFT JOIN (SELECT * FROM translations WHERE author = :author) as ts ON tx.id = ts.id_text ORDER BY id ASC";
						} else if ($type == 'global_untranslated') {
							$query = "SELECT id, 0 as status FROM texts WHERE id NOT IN (SELECT distinct(id_text) FROM translations WHERE status = 2) ORDER BY id ASC";
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
							if ($whole_word_only == 'true') {
								if (!preg_match('/\b' . $text_to_search . '\b/', $row[2])) continue;
							}
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
	} catch (Throwable $e) {
		header('HTTP/1.1 500 Internal Server Error');
		print_r($e);
		exit;
	}

?>
