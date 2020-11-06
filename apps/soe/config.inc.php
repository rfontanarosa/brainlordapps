<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'soe');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Secret of Evermore');

define('SQLITE_FILENAME', RESOURCE_PATH . '/soe/db/soe.db');
define('LAST_ENTRY', 3002);
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
