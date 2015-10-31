<?php


/**
 *    This represents a grouping of functionality inside the app
 */
class Module {


    /// Name of module
    public $name;

    /// The application
    public $app;

    /// List of modules
    public $dependsOn;
    public $parent;
    public $modules;

    public $sources;
    public $scripts;
    public $stylesheets;
    public $publicFiles;

    public $views;


    public $routes;





    /// Get the root module
    static public function Root( $application=false ) {
        if( Module::$s_rootModule == null ) {

            if( $application === false ) {
                ?><div style="color:#F00">We can't load in modules without an Application</div><?php
                exit();
            }
            Module::LoadAllModules($application);
        }
        return Module::$s_rootModule;
    }



    /// Adds the module and all its sources into the current scope
    static public function Requires ( $path ) {
        $module = Module::Root()->getByFullPath($path);
        $dependSources = $module->getAllSources();
        foreach( $dependSources as $sourceFile ) {
            require_once( $sourceFile );
        }
    }




    /// Constructor!
    public function Module( $name, $app, $configs ) {
        $this->name = $name;
        $this->parent = null;
        $this->modules = array();
        $this->dependsOn = array();

        $this->app = $app;

        $this->sources = array();
        $this->scripts = array();
        $this->stylesheets = array();
        $this->publicFiles = array();

        $this->views = array();

        $this->routes = array();


        // Adding child modules
        //
        if( isset($configs['children'])) {
            foreach( $configs['children'] as $childName => $submodule ) {
                if( ! isset($submodule['name']) ) {
                    $submodule['name'] = $childName;
                }
                $this->addChildModule( new Module($childName, $app, $submodule) );
            }
        }

        // Add views
        if( isset($configs['views']) ) {
            foreach( $configs['views']  as $viewName => $viewPath ) {
                $this->addView( new View( $viewName, $viewPath, $this ));
            }
        }


        // Add dependencies as strings for parsing later
        //
        if( isset($configs['dependencies']) ) {
            $this->dependsOn = $configs['dependencies'];
        }


        // Adding routes
        //
        if( isset($configs['routes'])) {
            $this->routes = $configs['routes'];
        }


        // Adding files
        //
        if( isset($configs['scripts'])) {
            $this->scripts = $configs['scripts'];
        }
        if( isset($configs['sources'])) {
            $this->sources = $configs['sources'];
        }
        if( isset($configs['stylesheets'])) {
            $this->stylesheets = $configs['stylesheets'];
        }
        if( isset($configs['public'])) {
            $this->publicFiles = $configs['public'];
        }
    }



    // Add a view object to this module
    public function addView ( $view ) {
        $this->views[$view->name] = $view;
        $view->ownerModule = $this;
    }

    /// Add a child module to this module
    public function addChildModule( $submodule ) {
        $this->modules[] = $submodule;
        $submodule->parent = $this;
    }



    /// Returns the module of full path $arPath : Array
    public function getByFullArrayPath( $arPath ) {
        $module = $this->getByFullArrayPath_r( 0, $arPath);

        if($module != null ) {
            return $module;
        }

        $moudleString = '';
        foreach( $this->modules as $module ) {
            $moudleString .= ', '.$module->name;
        }

        print("Can't find module of path: <pre>".print_r($arPath,true)."</pre>" );
        print("Possible modules are: [".substr($moduleString,2)."]");
        print("In module `$this->name`");
        die();
    }




    /// Returns the module with the full path:
    public function getByFullPath( $path ) {
        return $this->getByFullArrayPath( explode(".",$path) );
    }



    /// Gets the full programmy-path modules.core.whatever
    public function getFullPath() {
        $parentPath = '';
        if( $this->parent != null ) {
            $parentPath = $this->parent->getFullPath() . ".";
        }
        return $parentPath . $this->name;
    }




    /// Get the full directory path from the web-root
    public function getFullDirPath() {
        return str_replace(".", DIRECTORY_SEPARATOR, $this->getFullPath()).DIRECTORY_SEPARATOR;
    }



    /// Returns an array of all paths to module styles AND dependency styles
    /// it will always return the dependencies first
    /// if there are sources that are less files, it will create a css file in the cache
    public function getAllStylesheets () {
        $stylesheets = array();

        $modulePath = $this->getFullDirPath();
        foreach( $this->stylesheets as $style ) {
            $stylesheets[] = (Util::IsExtern($style)?"":$modulePath) . $style;
        }

        foreach( $this->dependsOn as $dependency ) {
            $stylesheets = array_merge($dependency->getAllStylesheets(), $stylesheets );
        }

        return $stylesheets;
    }



    // Returns an array of all paths to module scripts AND dependency scripts
    // it will always return the dependencies first
    public function getAllScripts () {
        $scripts = array();

        $modulePath = $this->getFullDirPath();
        foreach( $this->scripts as $script ) {
            $scripts[] = (Util::IsExtern($script)?"":$modulePath) . $script;
        }

        foreach( $this->dependsOn as $dependency ) {
            $scripts = array_merge($dependency->getAllScripts(), $scripts );
        }

        return $scripts;
    }


    // Returns an array of all paths to module sources AND dependency sources
    // it will always return the dependencies first
    public function getAllSources () {

        $sources = array();

        $modulePath = $this->getFullDirPath();
        foreach( $this->sources as $source ) {
            $sources[] =  $modulePath . $source;
        }

        foreach( $this->dependsOn as $dependency ) {
            $sources = array_merge($dependency->getAllSources(), $sources );
        }

        return $sources;
    }




    public function getViewByPath( $modulePath ) {

        $explodedPath = explode(".",$modulePath);
        $viewName = array_pop($explodedPath);
        /// TODO: check for $explodedPath count == 0
        $ownerModule = $this->getByFullArrayPath($explodedPath);
        /// TODO: check for $ownerModule->views[ $viewName ]
        return $ownerModule->views[ $viewName ];
    }




    public function showAllModules () {
        foreach($this->modules as $child ) {
            print( $child->name . "<br/>");
        }
    }




    /// The root module; use Module::Root($app) to get it.
    static private $s_rootModule = null;

    /// Used by the Root object when first called to bootstrap the application
    static private function LoadAllModules( $app ) {

        Module::$s_rootModule = new Module( "modules", $app, array());
        $files = scandir("modules");

        foreach($files as $value){

            //print("<p>DIR: $value</p>");

	        if( $value[0] != "." ) {
                $path = "modules" . DIRECTORY_SEPARATOR . $value . DIRECTORY_SEPARATOR . "module.json";
                // grab the module json file
                if( file_exists($path) ) {
                    $buffer = file_get_contents($path);
                    $json_a = json_decode($buffer, true);

                    // If there's no name, then we'll just use the folder
                    if( isset($json_a['name']) == false ) {
                        $json_a['name'] = $value;
                    }

                    // Now, given the name of the module:
                    // make sure it's actually a thing
                    if( ! file_exists( "modules" . DIRECTORY_SEPARATOR . $value . DIRECTORY_SEPARATOR . $value ) ) {

                        // Option one: check for a repo and try to download the submodule
                        // TODO: (try to) download the success of the application
                        // TODO: add the success in the application

                        // Option two: add the error in the application
                        // TODO: add error in the application
                        //continue;
                    }

                    Module::$s_rootModule->addChildModule( new Module( $json_a['name'], $app, $json_a ) );
                }
	        }
        }

        Module::$s_rootModule->postProcess();
    }


    /// Process dependencies & copy public files if debug mode
    private function postProcess( ) {

        // Create all dependencies
        $dependencyModules = array();
        foreach( $this->dependsOn as $dependencyString ) {
            if( is_string( $dependencyString )) {
                $dependencyModules[] = Module::Root( $this->app )->getByFullPath($dependencyString);
            }
            else {
                $dependencyModules[] = $dependencyString;
            }
        }
        $this->dependsOn = $dependencyModules;


        // Move all files marked public to the cached directory under public under the module
        // Create a cached file for them!
        if( $this->app->isDebug() ) {

            foreach($this->publicFiles as $publicFile ) {
                // TODO: move the file to the new location if the old one is older
            }

        }


        // Now perform the same for all children
        foreach( $this->modules as $child ) {
            $child->postProcess();
        }
    }


    /// Recursively finds the module of path $arPath
    private function getByFullArrayPath_r( $depth, $arPath ) {
        // 1. $arPath = array("extern","jQuery")
        // 2. $arPath = array("jQuery")
        // 3. $arPath = array()
        //print("$depth: entry \$arPath = ".print_r($arPath,true)."<br/>");
        if( count($arPath) == 0 ) {
            return $this;
        }

        $firstName = array_shift($arPath);
        //print("$depth: \$firstName = ".$firstName."<br/>");
        foreach( $this->modules as $module ) {
            if( $firstName == $module->name) {
                // 1. $arPath = array("jQuery")
                // 2. $arPath = array()
                //print("$depth: recurse \$arPath = ".print_r($arPath,true)."<br/>");
                return $module->getByFullArrayPath_r( $depth+1, $arPath);
            }
        }

        //print("$depth: return null<br/>");
        return null;
    }
}
