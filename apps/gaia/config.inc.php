<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'gaia');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Illusion of Gaia (SNES)');

define('SQLITE_FILENAME', RESOURCE_PATH . '/gaia/db/gaia.sqlite3');
define('LAST_ENTRY', 1332);
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
