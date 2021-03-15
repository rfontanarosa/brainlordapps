<?php

if (!extension_loaded('sqlite3')) die('Extension php_sqlite3 not loaded');

/** DEBUG */

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', realpath(dirname(__FILE__) . '/'));
define('RESOURCE_PATH', BASE_PATH . '/../brainlordresources/');

if (!function_exists('sqlite_escape_string')) {
	function sqlite_escape_string($string) {
		return str_replace("'", "''", $string);
	}
}

class UserManager {

	public static function login($uname, $pass) {
		$xml = @simplexml_load_file(BASE_PATH . '/users.xml');
		if ($xml !== false) {
			foreach($xml->user as $user) {
				if ($uname == $user->uname) {
					if (md5($pass) == $user->pass) {
						$_SESSION['uname'] = $uname;
						foreach($user->apps->app as $app) {
							if (!isset($_SESSION['roles'])) {
								$_SESSION['roles'] = array();
							}
							$_SESSION['roles'][$app['name'] . ''] = $app['role'] . '';
						}
					} else {
						$login_response = 'Error: Invalid password';
					}
				} else {
					$login_response = 'Error: Invalid username';
				}
			}
		} else {
			$login_response = 'Error: File not found';
		}
	}

	public static function isLogged() {
		return isset($_SESSION['uname']);
	}

	public static function getUsername() {
		return $_SESSION['uname'];
	}

	public static function logout() {
		unset($_SESSION['uname']);
		session_unset();
		session_destroy();
	}

	public static function getRole($app) {
		$ret = (array_key_exists('*', $_SESSION['roles'])) ? $_SESSION['roles']['*'] : '';
		if ($ret == '') {
			$ret = (array_key_exists($app, $_SESSION['roles'])) ? $_SESSION['roles'][$app] : '';
		}
		return $ret;
	}

}

class DbManager {

	public static function countByUserAndStatus($db, $uname, $status) {
		$ret = 0;
		$query = 'SELECT COUNT(*) FROM trans WHERE author = :author AND status = :status';
		$stmt = $db->prepare($query);
		$stmt->bindValue(':author', $uname, SQLITE3_TEXT);
		$stmt->bindValue(':status', $status, SQLITE3_INTEGER);
		$results = $stmt->execute();
		if ($row = $results->fetchArray()) {
			$ret = $row[0];
		}
		$results->finalize();
		return $ret;
	}

	public static function countByUserGroupByStatus($db, $uname) {
		$ret = array(0, 0, 0);
		$query = 'SELECT status, COUNT(*) FROM translations WHERE author = :author GROUP BY status';
		$stmt = $db->prepare($query);
		$stmt->bindValue(':author', $uname, SQLITE3_TEXT);
		$results = $stmt->execute();
		while ($row = $results->fetchArray()) {
			$status = $row[0];
			$ret[$status] = $row[1];
		}
		$results->finalize();
		return $ret;
	}

	public static function countDucplicatesById($db, $id) {
		$ret = 0;
		$query = 'SELECT COUNT(*) FROM texts WHERE text_decoded = (SELECT text_decoded FROM texts WHERE id = :id)';
		$stmt = $db->prepare($query);
		$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
		$results = $stmt->execute();
		if ($row = $results->fetchArray()) {
			$ret = $row[0];
		}
		$results->finalize();
		return $ret - 1;
	}

	public static function getNextIdByUserAndId($db, $uname, $id) {
		$ret = 0;
		$query = 'SELECT MIN(id) FROM texts WHERE id NOT IN (SELECT id_text FROM translations WHERE author = :author AND status = :status) AND id > :id';
		$stmt = $db->prepare($query);
		$stmt->bindValue(':author', $uname, SQLITE3_TEXT);
		$stmt->bindValue(':status', 2, SQLITE3_INTEGER);
		$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
		$results = $stmt->execute();
		if ($row = $results->fetchArray()) {
			$ret = $row[0];
		}
		$results->finalize();
		return $ret;
	}

	public static function getPrevIdByUserAndId($db, $uname, $id) {
		$ret = 0;
		$query = 'SELECT MAX(id) FROM texts WHERE id NOT IN (SELECT id_text FROM translations WHERE author = :author AND status = :status) AND id < :id';
		$stmt = $db->prepare($query);
		$stmt->bindValue(':author', $uname, SQLITE3_TEXT);
		$stmt->bindValue(':status', 2, SQLITE3_INTEGER);
		$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
		$results = $stmt->execute();
		if ($row = $results->fetchArray()) {
			$ret = $row[0];
		}
		$results->finalize();
		return $ret;
	}

	public static function getOriginalById($db, $id) {
		$query = 'SELECT * FROM texts WHERE id = :id';
		$stmt = $db->prepare($query);
		$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
		$results = $stmt->execute();
		$row = $results->fetchArray();
		$results->finalize();
		return $row;
	}

	public static function getTranslationByUserAndOriginalId($db, $uname, $id) {
		$query = 'SELECT * FROM translations WHERE author = :author AND id_text = :id';
		$stmt = $db->prepare($query);
		$stmt->bindValue(':author', $uname, SQLITE3_TEXT);
		$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
		$results = $stmt->execute();
		$row = $results->fetchArray();
		$results->finalize();
		return $row;
	}

	public static function getOtherTranslationByOriginalId($db, $uname, $id) {
		$query = 'SELECT * FROM translations WHERE author != :author AND id_text = :id ORDER BY date DESC';
		$stmt = $db->prepare($query);
		$stmt->bindValue(':author', $uname, SQLITE3_TEXT);
		$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
		$results = $stmt->execute();
		$rows = array();
		while ($row = $results->fetchArray()) {
			$rows[] = $row;
		}
		$results->finalize();
		return $rows;
	}

	public static function getTranslationsByUser($db, $uname, $block=0) {
		$ret = array();
		$query = "SELECT text, new_text, text_encoded, id, id2 FROM texts AS t1 LEFT OUTER JOIN (SELECT * FROM trans WHERE trans.author=:author AND trans.status = 2) AS t2 ON t1.id=t2.id_text ORDER BY t1.id";
		if ($block != 0) {
			$query = "SELECT text, new_text, text_encoded, id, id2 FROM texts AS t1 LEFT OUTER JOIN (SELECT * FROM trans WHERE trans.author=:author AND trans.status = 2) AS t2 ON t1.id=t2.id_text WHERE t1.block = :block ORDER BY t1.id";	
		}
		$stmt = $db->prepare($query);
		$stmt->bindValue(':author', $uname, SQLITE3_TEXT);
		if ($block != 0) {
			$stmt->bindValue(':block', $block, SQLITE3_INTEGER);
		}
		$results = $stmt->execute();
		while ($row = $results->fetchArray()) {
			$ret[] = $row;
		}
		$results->finalize();
		return $ret;
	}

	public static function getMoreRecentTranslations($db, $block=0) {
		$ret = array();
		$query = "SELECT * FROM (SELECT text, new_text, text_encoded, id, id2, address, size, t2.author, COALESCE(t2.date, 1) AS date FROM texts AS t1 LEFT OUTER JOIN (SELECT * FROM trans WHERE status = 2) AS t2 ON t1.id=t2.id_text) WHERE 1=1 GROUP BY id HAVING MAX(date)";
		if ($block != 0) {
			$query = "SELECT * FROM (SELECT text, new_text, text_encoded, id, id2, address, size, t2.author, COALESCE(t2.date, 1) AS date FROM texts AS t1 LEFT OUTER JOIN (SELECT * FROM trans WHERE status = 2) AS t2 ON t1.id=t2.id_text WHERE t1.block = :block) WHERE 1=1 GROUP BY id HAVING MAX(date)";
		}
		$stmt = $db->prepare($query);
		if ($block != 0) {
			$stmt->bindValue(':block', $block, SQLITE3_INTEGER);
		}
		$results = $stmt->execute();
		while ($row = $results->fetchArray()) {
			$ret[] = $row;
		}
		$results->finalize();
		return $ret;
	}

}
