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
				$result = array();
				$id_text = $_POST['id_text'];
				$author = UserManager::getUsername();
				$translation = $_POST['translation'];
				$status = $_POST['status'];
				$tags = '';
				$comment = $_POST['comment'];
				$date = time();
				$translation = textClean($translation);
				$extends_to_duplicates = $_POST['extends_to_duplicates'] === 'true';
				$db = new SQLite3(SQLITE_FILENAME);
				if ($extends_to_duplicates) {
					$query = 'SELECT id FROM texts WHERE text = (SELECT text FROM texts WHERE id = :id)';
					$stmt = $db->prepare($query);
					$stmt->bindValue(':id', $id_text, SQLITE3_INTEGER);
					$results = $stmt->execute();
					$ids = array();
					while ($row = $results->fetchArray()) {
						$ids[] = $row[0];
					}
					$results->finalize();
					foreach ($ids as $duplicate_id) {
						DbManager::saveTranslation($db, $duplicate_id, $author, $translation, $status, $date, $tags, $comment);
					}
				} else {
					DbManager::saveTranslation($db, $id_text, $author, $translation, $status, $date, $tags, $comment);
				}
				$db->close();
				unset($db);
				$updateDate = @date('d/m/Y, G:i', $date);
				$result['updateDate'] = $updateDate;
				echo json_encode($result);
				break;
			default:
				json_error(405, 'Method not allowed');
		}
	} catch (Throwable $e) {
		error_log((string)$e);
		json_error(500, 'Internal server error');
	}

?>
