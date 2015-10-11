<?php


if (!defined ("INC_PATH")) {
	// Assuming the file will always live in /lib/, 2 below the root of the
	// project directory, this will never need to be changed.
	$dir = str_replace('\\', '/', dirname(__FILE__));
	// go up another directory
	$dir = substr($dir, 0, strripos($dir, '/'));
	define("INC_PATH", $dir);
	set_include_path(get_include_path() . PATH_SEPARATOR . $dir);

	if (defined("DEVEL_ENV")) {
		error_reporting(E_ALL);
	}
	else {
		error_reporting(E_ALL ^ E_NOTICE);
	}
}


date_default_timezone_set('America/Vancouver');
