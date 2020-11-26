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
					$result = array();
					$id_text = $_POST['id_text'];
					$author = UserManager::getUsername();
					$new_text = $_POST['new_text'];
					$status = $_POST['status'];
					$comment = $_POST['comment'];
					$time = time();
					$new_text = textClean($new_text);
					$new_text2 = tableClean($new_text);
					$extends_to_duplicates = $_POST['extends_to_duplicates'] === 'true';
					$db = new SQLite3(SQLITE_FILENAME);
					if ($extends_to_duplicates) {
						$query = 'SELECT id FROM texts WHERE text_encoded = (SELECT text_encoded FROM texts WHERE id = :id)';
						$stmt = $db->prepare($query);
						$stmt->bindValue(':id', $id_text, SQLITE3_INTEGER);
						$results = $stmt->execute();
						while ($row = $results->fetchArray()) {
							$id_text = $row[0];
							$query = 'INSERT OR REPLACE INTO trans VALUES (:id, :author, :new_text, :new_text2, :status, :time, :comment)';
							$stmt = $db->prepare($query);
							$stmt->bindValue(':id', $id_text, SQLITE3_INTEGER);
							$stmt->bindValue(':new_text', $new_text, SQLITE3_TEXT);
							$stmt->bindValue(':new_text2', $new_text2, SQLITE3_TEXT);
							$stmt->bindValue(':author', $author, SQLITE3_TEXT);
							$stmt->bindValue(':status', $status, SQLITE3_INTEGER);
							$stmt->bindValue(':time', $time, SQLITE3_INTEGER);
							$stmt->bindValue(':comment', $comment, SQLITE3_TEXT);
							$stmt->execute();
						}
						$results->finalize();
					} else {
						$query = 'INSERT OR REPLACE INTO trans VALUES (:id, :author, :new_text, :new_text2, :status, :time, :comment)';
						$stmt = $db->prepare($query);
						$stmt->bindValue(':id', $id_text, SQLITE3_INTEGER);
						$stmt->bindValue(':new_text', $new_text, SQLITE3_TEXT);
						$stmt->bindValue(':new_text2', $new_text2, SQLITE3_TEXT);
						$stmt->bindValue(':author', $author, SQLITE3_TEXT);
						$stmt->bindValue(':status', $status, SQLITE3_INTEGER);
						$stmt->bindValue(':time', $time, SQLITE3_INTEGER);
						$stmt->bindValue(':comment', $comment, SQLITE3_TEXT);
						$stmt->execute();
					}
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
