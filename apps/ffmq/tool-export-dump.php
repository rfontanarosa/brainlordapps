<?php

    session_start();

    require_once 'config.inc.php';

    $block = isset($_POST['block']) ? $_POST['block'] : 1;
    $type = isset($_POST['type']) ? $_POST['type'] : 1;

    $author = UserManager::getUsername();
    $db = new SQLite3(SQLITE_FILENAME);

    $str = '';
    $rows = $type == 1 ? DbManager::getTranslationsByUser($db, $author, $block) : DbManager::getMoreRecentTranslations($db, $block);
    foreach ($rows as $row) {
        $id2 = $row[4];
        $original_text = $row[2];
        $new_text = $row[1];
        $text = $new_text ? $new_text : $original_text;
        $str .= "$id2$text\n\n";
    }

    $filename = $block == 1 ? 'dump_ita.txt' : 'dump_ita.txt';

    header("Content-Disposition: attachment; filename=$filename");
    header('Content-Type: text/plain');
    header('Content-Length: ' . strlen($str));
    header('Connection: close');

    echo $str;

?>
