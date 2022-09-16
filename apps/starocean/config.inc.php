<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'starocean');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Star Ocean');

define('SQLITE_FILENAME', RESOURCE_PATH . '/starocean/db/starocean.sqlite3');
define('LAST_ENTRY', 4000);
define('NEWLINECHAR', '-----');
define('NEWLINE_REPLACE', 0);

function textClean($text) {
  if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
    $text = str_replace(PHP_EOL, NEWLINECHAR, $text);
  }
  return $text;
}

function tableClean($text) {
  return $text;
}
