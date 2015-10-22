<?php

// Set date and include directory

if (!defined ("INC_PATH")) {
	// Assuming the file will always live in /modules/core/, 2 below the root of the
	// project directory, this will never need to be changed.
	$dir = str_replace('\\', '/', dirname(__FILE__));
	// go up another directory
	$dir = substr($dir, 0, strripos($dir, '/'));
	// go up another directory
	$dir = substr($dir, 0, strripos($dir, '/'));
	define("INC_PATH", $dir);
	set_include_path(get_include_path() . PATH_SEPARATOR . $dir);

	error_reporting(E_ALL);
}


date_default_timezone_set('America/Vancouver');


require_once("Application.php");
require_once("Util.php");
require_once("Module.php");
require_once("CachedFile.php");
require_once("View.php");
