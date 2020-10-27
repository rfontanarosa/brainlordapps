<?php

require_once '../../config.inc.php';

define('APPLICATION_ID', 'neugier');
define('APPLICATION_PATH', '/apps/' . APPLICATION_ID);

define('TITLE', 'Neugier: Umi to Kaze no Koudou');

define('SQLITE_FILENAME', RESOURCE_PATH . '/neugier/db/neugier.db');
define('LAST_ENTRY', 220);
define('NEWLINECHAR', '{01}');
define('NEWLINE_REPLACE', 0);

function textClean($text) {
  if (defined('NEWLINE_REPLACE') && NEWLINE_REPLACE && defined('NEWLINECHAR')) {
    $text = str_replace(PHP_EOL, NEWLINECHAR, $text);
  }
  return $text;
}

function tableClean($text) {
  $text = str_replace('à', '{50}', $text);
  $text = str_replace('è', '{51}', $text);
  $text = str_replace('é', '{52}', $text);
  $text = str_replace('ì', '{53}', $text);
  $text = str_replace('ò', '{54}', $text);
  $text = str_replace('ù', '{55}', $text);
  $text = str_replace('È', '{56}', $text);
  return $text;
}
