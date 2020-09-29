<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'bof');

define('TITLE', 'Breath of Fire');

define('SQLITE_FILENAME', RESOURCE_PATH . '/bof/db/bof.db');
define('LAST_ENTRY', 1701);
define('NEWLINECHAR', '{02}');
define('NEWLINE_REPLACE', 0);

function textClean($text) {
  if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
    $text = str_replace(PHP_EOL, NEWLINECHAR, $text);
  }
  return $text;
}

function tableClean($text) {
  $text = str_replace('à', '{10}', $text);
  $text = str_replace('è', '{11}', $text);
  $text = str_replace('é', '{12}', $text);
  $text = str_replace('ì', '{13}', $text);
  $text = str_replace('ò', '{14}', $text);
  $text = str_replace('ù', '{15}', $text);
  $text = str_replace('È', '{16}', $text);
  $text = str_replace('...', '{17}', $text);
  return $text;
}
