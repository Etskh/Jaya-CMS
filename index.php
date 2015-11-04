<?php
require_once("modules/core/Bootstrap.php");


//
// Application
//
$app = new Application('James Codes', array(
    "debug" => true,
    "database" => "sqlite:./data/site.db",
));

//
// Make this load in JSON on debug, then generate the routes
// But for the not-debug, use PHP
//
$app->createRoutes(array(
    '/' => 'james.main',
    '/posts' => 'posts.all',
    '/posts/{$postSlug}' => "posts.view",
    '/posts/{#postID}' => "posts.view",
));



$app->run();
