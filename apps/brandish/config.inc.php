<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'brandish');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Brandish');

define('SQLITE_FILENAME', RESOURCE_PATH . '/brandish/db/brandish.db');
define('LAST_ENTRY', 330);
define('NEWLINECHAR', '');
define('NEWLINE_REPLACE', 0);

function textClean($text) {
  if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
    $text = str_replace(PHP_EOL, NEWLINECHAR, $text);
  }
  return $text;
}

function tableClean($text) {
  $text = str_replace('à', '{7C}', $text);
  $text = str_replace('è', '{5B}', $text);
  $text = str_replace('é', '{5C}', $text);
  $text = str_replace('ì', '{5D}', $text);
  $text = str_replace('ò', '{5E}', $text);
  $text = str_replace('ù', '{5F}', $text);
  $text = str_replace('È', '{60}', $text);
  return $text;
}
