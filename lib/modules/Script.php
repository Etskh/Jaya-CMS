<?php

require_once("lib/extern/JShrink/src/JShrink/Minifier.php");

class Script
{
	static public function GetAllSources( &$sources = array() ) {
		$sources[] = new Script("https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js", true, false);
		$sources[] = new Script("//cdnjs.cloudflare.com/ajax/libs/less.js/2.5.3/less.min.js", true, true);

		$src = getDirContents("public/src");
		foreach( $src as $sourceFile ) {
			$sources[] = new Script($sourceFile, false, false);
		}

		return $sources;
	}

	// String: path from root folder to source
	public $filename;
	// Boolean: true if it's hosted elsewhere
	public $extern;
	// Boolean: true if it should ONLY be added during debug builds
	public $debugOnly;
	// Number: time it last changed - used for minifying
	public $timestamp;

	public function Script( $filename, $extern, $debugOnly ) {
		$this->filename = $filename;
		$this->extern = $extern;
		$this->debugOnly = $debugOnly;
		$this->timestamp = filemtime($filename);

	}
}
/*
function ConcatenateJSFiles( $output, $files ) {
    $buffer = "";
    foreach( $files as $file ) {
        $buffer .= file_get_contents($file);
    }

    // Run it through a minifier
    $buffer = \JShrink\Minifier::minify($buffer, array('flaggedComments' => false));

    file_put_contents( $output, $buffer );
}



function CheckJSSources($output) {

    $files = getDirContents("public/src");

    $outputFileTimestamp= filemtime($output);
    foreach( $files as $file ) {
        // there is a newer file
        if( filemtime($file) > $outputFileTimestamp ) {
            ConcatenateJSFiles($output, $files);
            return true;
        }
    }
    return false;
}*/
