<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'smrpg');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Super Mario RPG: Legend of the Seven Stars');

define('SQLITE_FILENAME', RESOURCE_PATH . '/smrpg/db/smrpg.db');
define('LAST_ENTRY', 4352);
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
