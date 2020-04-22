<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'brainlord');

define('TITLE', 'Brain Lord');

define('SQLITE_FILENAME', RESOURCE_PATH . '/brainlord/db/brainlord.db');
define('LAST_ENTRY', 1155);
define('NEWLINECHAR', '{f9}');
define('NEWLINE_REPLACE', 0);

function textClean($text) {
  if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
    $text = str_replace(PHP_EOL, NEWLINECHAR, $text);
  }
  return $text;
}

function tableClean($text) {
  $text = str_replace('à', '{5d}', $text);
  $text = str_replace('è', '{5e}', $text);
  $text = str_replace('é', '{5f}', $text);
  $text = str_replace('ì', '{60}', $text);
  $text = str_replace('ò', '{61}', $text);
  $text = str_replace('ù', '{62}', $text);
  $text = str_replace('È', '{63}', $text);
  $text = str_replace('°', '{64}', $text);
}
