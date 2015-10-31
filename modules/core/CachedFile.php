<?php

/**
    This represents a single file that will be cached in the file-system (compiled, lessened
    or whatever)
*/
class CachedFile
{
    protected static $CACHE_FOLDER = 'cache';

    public $isPublic;
    public $ownerModule;
    public $path;

    public function CachedFile( $path, $isPublic, $ownerModule ) {
        $this->path = $path;
        $this->isPublic = $isPublic;
        $this->ownerModule = $ownerModule;
    }

    // Gets the template's cached path
    //
    public function getCacheFilePath() {
        // TODO: move this file_exists non-sense elsewhere
        if( ! file_exists(CachedFile::$CACHE_FOLDER)) {
            mkdir(CachedFile::$CACHE_FOLDER, 0777, true);
        }
        $folder = CachedFile::$CACHE_FOLDER . DIRECTORY_SEPARATOR;
        $folder.= ($this->isPublic?"public":"") ;//. $this->ownerModule->name;
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        // Account for compilation
        $newPath = $this->path;
        $compilations = array(
            "css" => array( "less" ),
            "js" => array( "coffee" ),
            "html" => array("php"),
        );
        foreach( $compilations as $target => $source ) {
            $newPath = str_replace( $source, $target, $newPath);
        }

        return $folder . DIRECTORY_SEPARATOR . str_replace( "/", "-", $newPath );
    }


    public function getExtension() {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }


    // Save path directly into getCacheFilePath()
    //
    public function compileAndSave() {
        ///TODO: handle errors... at all.

        /// Compile less files
        Module::Requires("extern.lessphp");

        $buffer = file_get_contents($this->path);
        switch( $this->getExtension() ) {
        case "less":
            $less = new lessc;
            $buffer = $less->compile($buffer);
            break;
        }
        file_put_contents( $this->getCacheFilePath(), $buffer );
    }


    // Minify and return string
    //
    public function minify() {
        ///TODO: handle errors... at all.

        /// Compile less files
        Module::Requires("extern.JShrink");
        Module::Requires("extern.lessphp");

        $buffer = file_get_contents($this->path);
        switch( $this->getExtension() ) {
        case "less":
            $less = new lessc;
            $less->setFormatter("compressed");
            $buffer = $less->compile($buffer);
            break;
        case "js":
            $buffer = \JShrink\Minifier::minify($buffer, array('flaggedComments' => false));
            break;
        }
        return $buffer;
    }

    /*
    static public function Never () {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
    */
}
