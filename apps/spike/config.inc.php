<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'spike');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Twisted Tales of Spike McFang, The (SNES)');

define('SQLITE_FILENAME', RESOURCE_PATH . '/spike/db/spike.sqlite3');
define('LAST_ENTRY', 252);
define('NEWLINECHAR', '');
define('NEWLINE_REPLACE', 0);

function textClean($text) {
  if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
    $text = str_replace(PHP_EOL, NEWLINECHAR, $text);
  }
  return $text;
}
