<?php

	header('Content-Type: application/json');

	require_once './config.inc.php';

	function json_error($status, $message) {
		http_response_code($status);
		echo json_encode(['error' => $message]);
		exit;
	}

    if (!UserManager::isLogged() || UserManager::getRole(APPLICATION_ID) != 'user') {
        json_error(401, 'Unauthorized');
    }

	try {
		switch ($_SERVER['REQUEST_METHOD']) {
			case 'POST':
				if (isset($_POST['type'])) {
					$data = array();
					$type = $_POST['type'];
					$text_to_search = isset($_POST['text_to_search']) ? $_POST['text_to_search'] : '';
					$whole_word_only = isset($_POST['whole_word_only']) && $_POST['whole_word_only'] === 'true';
					$case_sensitive = isset($_POST['case_sensitive']) && $_POST['case_sensitive'] === 'true';
					$regex = isset($_POST['regex']) && $_POST['regex'] === 'true';
					$author = isset($_POST['author']) && $_POST['author'] !== '' ? $_POST['author'] : UserManager::getUsername();
					$db = new SQLite3(SQLITE_FILENAME);
					if ($type == 'ref') {
						$query = "SELECT tx.id, COALESCE(ts.status, 0) as status FROM texts as tx LEFT JOIN (SELECT filename, file_index, status FROM translations WHERE author = :author GROUP BY filename, file_index HAVING MAX(date)) as ts ON tx.filename = ts.filename AND tx.file_index = ts.file_index WHERE ref LIKE :text_to_search ORDER BY id ASC";
					} else if ($type == 'original') {
						$query = "SELECT tx.id, COALESCE(ts.status, 0) as status, tx.text FROM texts as tx LEFT JOIN (SELECT filename, file_index, status FROM translations WHERE author = :author GROUP BY filename, file_index HAVING MAX(date)) as ts ON tx.filename = ts.filename AND tx.file_index = ts.file_index WHERE text LIKE :text_to_search ORDER BY id ASC";
					} else if ($type == 'new') {
						$query = "SELECT tx.id, COALESCE(t.status, 0) as status, t.translation FROM translations as t JOIN texts as tx ON tx.filename = t.filename AND tx.file_index = t.file_index WHERE t.translation LIKE :text_to_search AND t.author = :author GROUP BY tx.id HAVING MAX(t.date) ORDER BY tx.id ASC";
					} else if ($type == 'comment') {
						$query = "SELECT tx.id, COALESCE(t.status, 0) as status FROM translations as t JOIN texts as tx ON tx.filename = t.filename AND tx.file_index = t.file_index WHERE t.comment LIKE :text_to_search AND t.author = :author ORDER BY tx.id ASC";
					} else if ($type == 'duplicates') {
						$query = "SELECT tx.id, COALESCE(ts.status, 0) as status FROM texts as tx LEFT JOIN (SELECT * FROM translations WHERE author = :author) as ts ON tx.filename = ts.filename AND tx.file_index = ts.file_index WHERE text = (SELECT text FROM texts WHERE id = :text_to_search) ORDER BY id ASC";
					} else if ($type == 'personal_all') {
						$query = "SELECT tx.id, COALESCE(ts.status, 0) as status FROM texts as tx LEFT JOIN (SELECT * FROM translations WHERE author = :author) as ts ON tx.filename = ts.filename AND tx.file_index = ts.file_index ORDER BY id ASC";
					} else if ($type == 'personal_todo') {
						$query = "SELECT tx.id, COALESCE(ts.status, -1) as status FROM texts as tx LEFT JOIN (SELECT * FROM translations WHERE author = :author) as ts ON tx.filename = ts.filename AND tx.file_index = ts.file_index WHERE ts.filename IS NULL OR ts.status = 0 ORDER BY id ASC";
					} else if ($type == 'personal_in_progress') {
						$query = "SELECT tx.id, COALESCE(ts.status, 0) as status FROM texts as tx LEFT JOIN (SELECT * FROM translations WHERE author = :author) as ts ON tx.filename = ts.filename AND tx.file_index = ts.file_index WHERE ts.status = 1 ORDER BY id ASC";
					} else if ($type == 'personal_done') {
						$query = "SELECT tx.id, COALESCE(ts.status, 0) as status FROM texts as tx LEFT JOIN (SELECT * FROM translations WHERE author = :author) as ts ON tx.filename = ts.filename AND tx.file_index = ts.file_index WHERE ts.status = 2 ORDER BY id ASC";
					} else if ($type == 'global_untranslated') {
						$query = "SELECT id, 0 as status FROM texts WHERE id NOT IN (SELECT tx.id FROM texts as tx JOIN translations as t ON tx.filename = t.filename AND tx.file_index = t.file_index WHERE t.status = 2) ORDER BY id ASC";
					} else {
						json_error(400, 'Unknown search type');
					}
					$stmt = $db->prepare($query);
					if ($type !== 'global_untranslated') {
						$stmt->bindValue(':author', $author, SQLITE3_TEXT);
					}
					if ($type == 'duplicates') {
						$stmt->bindValue(':text_to_search', $text_to_search, SQLITE3_TEXT);
					} else if (in_array($type, ['personal_all', 'personal_todo', 'personal_in_progress', 'personal_done', 'global_untranslated'])) {
					} else {
						if ($regex && ($type === 'original' || $type === 'new')) {
							$stmt->bindValue(':text_to_search', '%', SQLITE3_TEXT);
						} else {
							$stmt->bindValue(':text_to_search', "%$text_to_search%", SQLITE3_TEXT);
						}
					}
					$regex_pattern = $text_to_search;
					if ($regex && strlen($regex_pattern) >= 2) {
							$delim = $regex_pattern[0];
							$lastDelim = strrpos($regex_pattern, $delim, 1);
							if ($lastDelim !== false && $lastDelim > 0) {
								$flags = str_replace('g', '', substr($regex_pattern, $lastDelim + 1));
								$regex_pattern = substr($regex_pattern, 0, $lastDelim + 1) . $flags;
							}
						}
					$results = $stmt->execute();
					while ($row = $results->fetchArray()) {
						if ($regex && ($type === 'original' || $type === 'new')) {
							if (@preg_match($regex_pattern, $row[2]) !== 1) continue;
						} elseif ($whole_word_only && ($type === 'original' || $type === 'new')) {
							$flags = $case_sensitive ? '' : 'i';
							if (!preg_match('/\b' . preg_quote($text_to_search, '/') . '\b/' . $flags, $row[2])) continue;
						} elseif ($case_sensitive && ($type === 'original' || $type === 'new')) {
							if (strpos($row[2], $text_to_search) === false) continue;
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
					json_error(422, 'Missing search type');
				}
			default:
				json_error(405, 'Method not allowed');
		}
	} catch (Throwable $e) {
		error_log((string)$e);
		json_error(500, 'Internal server error');
	}

?>
