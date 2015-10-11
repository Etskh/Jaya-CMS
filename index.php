<?php

$startTime = microtime(true);
require_once("lib/jaya.php");

//$route = Route::Parse( $_GET, $_POST );

$DEBUG = true;
$fullCachedFile = 'cache/home.html';

$configs = array();
$configs['stylesheets'] = '';
$configs['scripts'] = '<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>';


$buffer = '';
if( $DEBUG ) {

    ob_start();
    // Include the modules on a per-need basis
    require_once("lib/modules/Post.php");

    // and the main template
    require_once("view/static/home.php");
    $buffer = ob_get_contents();
    ob_end_clean();

    // Now save the contents
    if(file_put_contents($fullCachedFile, $buffer ) === false ) {
        die("Can't save cache file: ".$fullCachedFile);
    }

    // Theming
    $configs['stylesheets'] .= '<link rel="stylesheet" type="text/css" href="public/extern/normalize/normalize-3.0.2.css" />';
    $configs['stylesheets'] .= '<link rel="stylesheet" type="text/less" href="public/src/style.less" />';
    $configs['scripts'] .= '<script src="http://cdnjs.cloudflare.com/ajax/libs/less.js/2.5.3/less.min.js"></script>';
    $configs['scripts'] .= '<script src="/public/src/Site.js"></script>';
}
else {
    $configs['stylesheets'] .= '<link rel="stylesheet" type="text/less" href="public/style.min.css"/>';
    $configs['scripts'] .= '<script src="public/site.min.js"></script>';
}

$configs["loadtime"] = sprintf("%.4f", microtime(true) - $startTime);
$configs["source"] = "buffer";

foreach( $configs as $config => $value ) {
    $buffer = str_replace( "{{config.".$config."}}", $value, $buffer);
}

exit($buffer);
