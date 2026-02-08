<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'sd3');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Seiken Densetsu 3 (SNES)');

define('SQLITE_FILENAME', RESOURCE_PATH . '/sd3/db/sd3-chester.db');
define('LAST_ENTRY', 2567);
#define('SQLITE_FILENAME', RESOURCE_PATH . '/sd3/db/sd3-magno.db');
#define('LAST_ENTRY', 7463);
define('NEWLINECHAR', '-----');
define('NEWLINE_REPLACE', 0);

function textClean($text) {
  if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
    $text = str_replace(PHP_EOL, NEWLINECHAR, $text);
  }
  return $text;
}
