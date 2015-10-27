<?php
require_once("modules/core/Bootstrap.php");


//
// Application
//
$app = new Application('James Codes', array(
    "debug" => true,
));



//
// Make this load in JSON on debug, then generate the routes
// But for the not-debug, use PHP
//
$app->createRoutes(array(
    "/" => "james.main",
    "/posts" => "posts.routes",
));



$app->run();

//
//$view = $moduleRoot->getViewByPath( $views['/'] );
//$view = $moduleRoot->getViewByRoute( $route );

//
// Output
//
//die($view->getHTML($app->configs));
