<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'som');
define('PREVIEWER_ID', 'som_it');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Secret of Mana (SNES)');

define('SQLITE_FILENAME', RESOURCE_PATH . '/som/db/som.sqlite3');
define('LAST_ENTRY', 2737);
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
