<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'bof2');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Breath of Fire 2 (SNES)');

define('SQLITE_FILENAME', RESOURCE_PATH . '/bof2/db/bof2.sqlite3');
define('LAST_ENTRY', 428);
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
