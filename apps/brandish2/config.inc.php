<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'brandish2');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Brandish 2: The Planet Buster');

define('SQLITE_FILENAME', RESOURCE_PATH . '/brandish2/db/brandish2.db');
define('LAST_ENTRY', 955);
define('NEWLINECHAR', '{01}');
define('NEWLINE_REPLACE', 0);

function textClean($text) {
  if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
    $text = str_replace(PHP_EOL, NEWLINECHAR, $text);
  }
  return $text;
}

function tableClean($text) {
  $text = str_replace('à', '{6A}', $text);
  $text = str_replace('è', '{6B}', $text);
  $text = str_replace('é', '{6C}', $text);
  $text = str_replace('ì', '{6D}', $text);
  $text = str_replace('ò', '{6E}', $text);
  $text = str_replace('ù', '{6F}', $text);
  $text = str_replace('È', '{E1}', $text);
  $text = str_replace("{n''}", '{E2}', $text);
  $text = str_replace("{l''}", '{E3}', $text);
  $text = str_replace("{c''}", '{E4}', $text);
  $text = str_replace("{d''}", '{E5}', $text);
  $text = str_replace("{C''}", '{EC}', $text);
  $text = str_replace("{D''}", '{ED}', $text);
  $text = str_replace("{L''}", '{EE}', $text);
  $text = str_replace("{s''}", '{F3}', $text);
  $text = str_replace("{t''}", '{F4}', $text);
  $text = str_replace("{v''}", '{F5}', $text);
  return $text;
}
