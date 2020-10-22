<?php

	session_start();

	header('Content-Type: text/plain');
	//header('Content-type: application/json');
	//header('Access-Control-Allow-Origin: *');

	require_once 'config.inc.php';

	$charlist = array();
	$charlist[] = array("MW…#+×%*", 9);
	$charlist[] = array("♥♪‥~?©ARÀw<>&", 8);
	$charlist[] = array("()023456789BCEGHKOPQUVXÈÉmv:;ÒÙ", 7);
	$charlist[] = array("!“”·/DFJNSTYZacdgknopqsuxyzàòù", 6);
	$charlist[] = array(",-.1Lbefhjrtèé", 5);
	$charlist[] = array(" '", 4);
	$charlist[] = array("IilìÌ", 3);

	$hashcharlist = array();
	for ($i=0; $i<count($charlist); $i++) {
		$charstring = preg_split('//u', $charlist[$i][0], -1, PREG_SPLIT_NO_EMPTY);
		for ($j=0; $j<count($charstring); $j++) {
			$hashcharlist[$charstring[$j]] = $charlist[$i][1];
		}
	}

	$GLOBALS['hashcharlist'] = $hashcharlist;

	function smrpgTextClean($text) {
		$text = preg_replace("/\[1\]/", "\n", $text); // New line
		$text = preg_replace("/\[6\]/", "", $text); // End string
		$text = preg_replace("/\[0\]/", "", $text); // End string, wait for input
		$text = preg_replace("/\[7\]/", "", $text); // Option triangle
		$text = preg_replace("/\[12\]/", "", $text); // Pause 1 second
		$text = preg_replace("/\[5\]/", "", $text); // Pause, wait for input
		$text = preg_replace("/\[28\]/", "", $text); // RAM?
		$text = preg_replace("/\[36\]/", "♥", $text);
		$text = preg_replace("/\[37\]/", "♪", $text);
		$text = preg_replace("/\[42\]/", "·", $text);
		$text = preg_replace("/\[43\]/", "‥", $text);
		return $text;
	}

	function isValid($text) {
		$hashcharlist = $GLOBALS['hashcharlist'];
		$ret = true;
		$text = preg_replace("/\[13\]\[.\]/", "", $text); // Pause?
		$text = preg_replace("/\[13\]\[..\]/", "", $text); // Pause?
		$text = preg_replace("/\[28\]\[.\]/", "", $text); // RAM?
		$textArray = preg_split("/\[2\]|\[3\]|\[4\]/", $text);
		for ($j=0; $j<count($textArray); $j++) {
			$textDialog = $textArray[$j];
			$textDialog = smrpgTextClean($textDialog);
			if (substr_count($textDialog, "\n") < 3) {
				$textDialog = preg_split('//u', $textDialog, -1, PREG_SPLIT_NO_EMPTY);
				$indexLine = 0;
				$counter = [0, 0, 0];
				for ($i=0; $i<count($textDialog); $i++) {
					$char = $textDialog[$i];
					if (isset($hashcharlist[$char]) && $hashcharlist[$char] > 0) {
						$counter[$indexLine] += $hashcharlist[$char] + 1;
					} else if ($char == "\n") {
						$indexLine++;
					}
				}
				$ret = $ret && ($counter[0] <= 222 && $counter[1] <= 222 && $counter[2] <= 222);
			} else {
				$ret = false;
			}
		}
		return $ret;
	}

	try {
		if (UserManager::isLogged() && UserManager::getRole(APPLICATION_ID) == 'user') {
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'GET':
					$data = array();
					$author = UserManager::getUsername();
					$db = new SQLite3(SQLITE_FILENAME);
					$rows = DbManager::getTranslationsByUser($db, $author);
					foreach ($rows as $row) {
						$id = $row[3];
						$new_text = $row[1];
						if ($new_text) {
							$is_valid = isValid($new_text);
							if (!$is_valid) {
								array_push($data, array(
									'id' => $id,
								));
							}
							// echo $is_valid ? $id . "-------------\n" : $id . "-XXXXXXXXXXXX\n";
						}
					}
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