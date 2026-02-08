<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'lufia');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Lufia &amp; the Fortress of Doom (SNES)');

define('SQLITE_FILENAME', RESOURCE_PATH . '/lufia/db/lufia.sqlite3');
define('LAST_ENTRY', 3065);
define('NEWLINECHAR', '-----');
define('NEWLINE_REPLACE', 0);

function textClean($text) {
  if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
    $text = str_replace(PHP_EOL, NEWLINECHAR, $text);
  }
  return $text;
}
