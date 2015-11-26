<?php

if (!extension_loaded('sqlite3')) die('Extension php_sqlite3 not loaded');

/** DEBUG */

//ini_set('display_errors', 1);
//error_reporting(E_ALL);

define('BASE_PATH', realpath(dirname(__FILE__) . '/'));
define('RESOURCE_PATH', BASE_PATH . '/resources');