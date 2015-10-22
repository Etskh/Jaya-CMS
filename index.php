<?php
require_once("modules/core/Bootstrap.php");


//
// Application
//
$app = new Application('James Codes', array(
    "debug" => true,
));
$moduleRoot = Module::Root($app);


//
// Site's views
//
//      - this is configured by the user in JSON format, then it's changed
//      to generated PHP code like this. We also can't just do a key-lookup.
//      the keys must be iterated over... or we have an array of regexps
//
$views = array(
    "/" => "james.main",
);





//
// TODO: Get current URL vs $views
//
$view = $moduleRoot->getViewByPath( $views['/'] );


//
// Output
//
die($view->getHTML($app->configs));
