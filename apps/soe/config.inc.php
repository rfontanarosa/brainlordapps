<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'soe');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Secret of Evermore (SNES)');

define('SQLITE_FILENAME', RESOURCE_PATH . '/soe/db/soe.sqlite3');
define('LAST_ENTRY', 3002);
define('NEWLINECHAR', PHP_EOL);
define('NEWLINE_REPLACE', 1);

function textClean($text) {
  if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
    $text = str_replace(PHP_EOL, NEWLINECHAR, $text);
  }
  return $text;
}

function tableClean($text) {
  return $text;
}
