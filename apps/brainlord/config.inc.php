<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'brainlord');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Brain Lord');

define('SQLITE_FILENAME', RESOURCE_PATH . '/brainlord/db/brainlord.sqlite3');
define('LAST_ENTRY', 1192);
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
