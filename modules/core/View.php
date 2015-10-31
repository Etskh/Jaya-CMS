<?php


/**
 *  This is a file that takes a template and ends up as output
 */
class View extends CachedFile
{
    /// The name of the view object
    public $name;

    /// A list of the cached styles needed by this view
    public $cachedStyles;

    /// A list of the cached scripts needed by this view
    public $cacehdScripts;

    /** Constructor */
    public function View ($name, $path, $module ) {
        parent::__construct($path, false, $module );

        $this->name = $name;
        $this->cachedStyles = array();
        $this->cachedScripts = array();
    }


    /** Returns the HTML to the view with the given configs */
    public function getHTML( $configs ) {

        $DEBUG = isset($configs['debug']) && $configs['debug'];

        // These are all of teh cached styles
        //
        $styles = $this->ownerModule->getAllStylesheets();
        foreach( $styles as $style ) {
            if( ! Util::IsExtern($style)) {
                $this->cachedStyles[] = new CachedFile($style, true, $this->ownerModule );
                $style = end($this->cachedStyles)->getCacheFilePath();
            }

            // Throw the script tag in if it hasn't been already
            $styleString = '<link rel="stylesheet" type="text/css" href="'.$style.'" />';
            if( strpos( $configs['stylesheets'], $styleString) === false ) {
                $configs['stylesheets'] .= $styleString;
            }
        }

        // These are all the cached scripts
        //
        $scripts = $this->ownerModule->getAllScripts();
        foreach( $scripts as $script ) {
            if( ! Util::IsExtern($script)) {
                $this->cachedScripts[] = new CachedFile($script, true, $this->ownerModule );
                $script = 'http://' . $configs['hostname'] .'/'. end($this->cachedScripts)->getCacheFilePath();
            }

            // Throw the script tag in if it hasn't been already
            $scriptString = '<script src="'.$script.'"></script>';
            if( strpos( $configs['scripts'], $scriptString) === false ) {
                $configs['scripts'] .= $scriptString;
            }
        }


        if( $DEBUG ) {
            // Include all module-sources this view needs
            $dependSources = $this->ownerModule->getAllSources();
            foreach( $dependSources as $sourceFile ) {
                require_once( $sourceFile );
            }

            // Create the request object in this scope
            $request = $configs['request'];

            // and the main template
            ob_start();
            require_once( $this->ownerModule->getFullDirPath() . $this->path );
            $buffer = ob_get_contents();
            ob_end_clean();

            // Now save the contents
            if(file_put_contents( $this->getCacheFilePath(), $buffer ) === false ) {
                $configs['errors'] .= 'Can\'t save file '.$this->getCacheFilePath().';';
            }

            /// Compile/save (re-cache) scripts and styles
            foreach($this->cachedStyles as $cachedStyle ) {
                $cachedStyle->compileAndSave();
            }
            foreach($this->cachedScripts as $cachedScript ) {
                $cachedScript->compileAndSave();
            }

            $configs['source'] = 'generated';
        }
        else {
			$buffer = file_get_contents( $this->getCacheFilePath() );
            $configs['source'] = 'pulled from cache';
        }


        //
        // Look for embedded views:
        //
        while( preg_match('/{{view:\"(?P<view>[\w\.]+)\"}}/', $buffer, $matches) ) {
            if( isset($matches['view']) ) {
                $subview = Module::Root()->getViewByPath($matches['view']);
                $subHTML = $subview->getHTML($configs);
                $buffer = str_replace( '{{view:"'.$matches['view'].'"}}', $subHTML, $buffer);
            }
        }


        //
        // Now fill the views with the configs
        //
        $configs['loadtime'] = sprintf("%.4f", microtime(true) - floatval($configs['starttime']));
        $configs['request'] = false;
        foreach( $configs as $config => $value ) {
            $buffer = str_replace( "{{config.".$config."}}", $value, $buffer);
        }
        return $buffer;
    }

    /// Returns an array of strings of paths to the cached, minified, and concatenated javascript files
    // eg: array(
    //      "module-post-MainView.min.js",
    //      "core.min.js"
    //  )
    //
    public function getMinifiedJSFiles() {
        $buffer = '';
        foreach( $this->$cacehdScripts as $script ) {
            $buffer .= $script->minify();
        }
        return $buffer;
    }

    /**
     *  Sets the code for the file being returned
     */
    public function setCode( $code ) {
        $newCode = ' '.$code;
        switch($code) {
        case 404:
            $newCode.=' Not Found';
            break;
        default:
            break;
        }
        header($_SERVER["SERVER_PROTOCOL"]. $newCode);
    }

}
