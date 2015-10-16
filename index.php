<?php
$startTime = microtime(true);
require_once("modules/core/Bootstrap.php");




//
// Configuration
//
$configs = array(
    "errors" => '',
    "scripts" => '',
    "stylesheets" => '',
    "source" => '',
    "request" => array(),
    "debug" => true,
    "title" => "James.Coder",
);



//
// Site's views
//
//      - this is configured by the user in JSON format, then it's changed
//      to generated PHP code like this. We also can't just do a key-lookup.
//      the keys must be iterated over... or we have an array of regexps
//
$views = array(
    "/" => "posts.main",
);







//
// TODO: Get current URL vs $views
//
$view = Module::Root()->getViewByPath( $views['/'] );


//
// Output
//
$configs['starttime'] = sprintf("%f", $startTime);
die($view->getHTML($configs));
