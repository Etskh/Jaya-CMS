<?php


//
// View object
//
class View extends CachedFile
{
    public function View ($name, $path) {
        parent::__construct($path, false );

        $this->name = $name;
    }

    public function getHTML( $configs ) {

        $DEBUG = isset($configs['debug']) && $configs['debug'];

        // These are all of teh cached styles
        //
        $cachedStyles = array();
        $styles = $this->ownerModule->getAllStylesheets();
        foreach( $styles as $style ) {
            if( ! Util::IsExtern($style)) {
                $cachedStyles[] = new CachedFile($style, true);
                $style = end($cachedStyles)->getCacheFilePath();
            }
            $configs['stylesheets'] .= '<link rel="stylesheet" type="text/css" href="'.$style.'" />';
        }

        // These are all the cached scripts
        //
        $cachedScripts = array();
        $scripts = $this->ownerModule->getAllScripts();
        foreach( $scripts as $script ) {
            if( ! Util::IsExtern($script)) {
                $cachedScripts[] = new CachedFile($script, true);
                $script = end($cachedScripts)->getCacheFilePath();
            }
            $configs['scripts'] .= '<script src="'.$script.'"></script>';
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
            require_once( $this->ownerModule->getFullDirPath() . $this->path );
            $buffer = ob_get_contents();
            ob_end_clean();

            // Now save the contents
            if(file_put_contents( $this->getCacheFilePath(), $buffer ) === false ) {
                $configs['errors'] .= 'Can\'t save file '.$this->getCacheFilePath().';';
            }

            /// Compile/save (re-cache) scripts and styles
            foreach($cachedStyles as $cachedStyle ) {
                $cachedStyle->compileAndSave();
            }
            foreach($cachedScripts as $cachedScript ) {
                $cachedScript->compileAndSave();
            }

            $configs['source'] = 'generated';
        }
        else {
			$buffer = file_get_contents( $this->getCacheFilePath() );
            $configs['source'] = 'pulled from cache';
        }


        $configs['loadtime'] = sprintf("%.4f", microtime(true) - floatval($configs['starttime']));
        foreach( $configs as $config => $value ) {
            $buffer = str_replace( "{{config.".$config."}}", $value, $buffer);
        }
        return $buffer;
    }

    public $name;
}
