<?php

    require_once './config.inc.php';

    if (!UserManager::isLogged() || UserManager::getRole(APPLICATION_ID) != 'user') {
        header('HTTP/1.1 401 Unauthorized');
        exit;
    }

    $block = isset($_POST['block']) ? $_POST['block'] : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : 1;

    $author = UserManager::getUsername();
    $db = new SQLite3(SQLITE_FILENAME);

    $rows = [];
    $filename = 'file.err';
    switch ($type) {
        case 1:
            $rows = DbManager::getOriginalDump($db, $block);
            $filename = 'dump.txt';
            break;
        case 2:
            $rows = DbManager::getTranslationsByUser($db, $author, $block);
            $filename = "dump_ita_$author.txt";
            break;
        case 3:
            $rows = DbManager::getMoreRecentTranslations($db, $block);
            $filename = 'dump_ita.txt';
            break;
    }
    $str = '';
    foreach ($rows as $row) {
        if ($type == 1) {
            $text = $row[1];
            $ref = $row[2];
        } else {
            $original = $row[1];
            $ref = $row[2];
            $translation = $row[3];
            $text = $translation ? $translation : $original;
        }
        $str .= "$ref\n$text\n\n";
    }

    header("Content-Disposition: attachment; filename=$filename");
    header('Content-Type: text/plain');
    header('Content-Length: ' . strlen($str));
    header('Connection: close');

    echo $str;

?>
