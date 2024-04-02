<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', '7thsaga');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'The 7th Saga');

define('SQLITE_FILENAME', RESOURCE_PATH . '/7thsaga/db/7thsaga.sqlite3');
define('LAST_ENTRY', 1243);
define('NEWLINECHAR', '{f9}');
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
