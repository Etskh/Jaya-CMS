<?php


/// This represents a grouping of functionality inside the app
class Module {

    /// Name of module
    public $name;

    /// The application
    public $app;

    ///
    public $dependsOn;
    public $parent;
    public $modules;

    public $sources;
    public $scripts;
    public $stylesheets;

    public $views;

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

    static public function Requires ( $path ) {
        $module = Module::Root()->getByFullPath($path);
        $dependSources = $module->getAllSources();
        foreach( $dependSources as $sourceFile ) {
            require_once( $sourceFile );
        }
    }

    /// Constructor!
    public function Module( $name, $app, $configs=array() ) {
        $this->name = $name;
        $this->parent = null;
        $this->modules = array();
        $this->dependsOn = array();

        $this->app = $app;

        $this->sources = array();
        $this->scripts = array();
        $this->stylesheets = array();

        $this->views = array();

        // Adding child modules
        //
        if( isset($configs['children'])) {
            foreach( $configs['children'] as $submodule ) {
                $this->addChildModule($submodule);
            }
        }


        // Adding files
        //
        if( isset($configs['scripts'])) {
            foreach( $configs['scripts'] as $script ) {
                $this->scripts[] = $script;
            }
        }
        if( isset($configs['sources'])) {
            foreach( $configs['sources'] as $source ) {
                $this->sources[] = $source;
            }
        }
        if( isset($configs['stylesheets'])) {
            foreach( $configs['stylesheets'] as $style ) {
                $this->stylesheets[] = $style;
            }
        }
    }

    public function addView ($view ) {
        $this->views[$view->name] = $view;
        $view->ownerModule = $this;
    }

    public function addChildModule( $submodule ) {
        $this->modules[] = $submodule;
        $submodule->parent = $this;
        //print("Adding ".$submodule->name." to ".$this->name."<br/>" );
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

        //debug_print_backtrace();

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
        //die( nl2br( print_r($this->modules, true)));
        $explodedPath = explode(".",$modulePath);
        $viewName = array_pop($explodedPath);
        /// TODO: check for $explodedPath count == 0
        $ownerModule = $this->getByFullArrayPath($explodedPath);
        /// TODO: check for $ownerModule->views[ $viewName ]
        return $ownerModule->views[ $viewName ];
    }



    /// The root module; use Module::Root($app) to get it.
    static private $s_rootModule = null;


    static private function LoadAllModules( $app ) {

        Module::$s_rootModule = new Module( "modules", $app );
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

                    Module::$s_rootModule->addChildModule( Module::CreateModuleFromArray($json_a, $app) );
                }
	        }
        }

        Module::ProcessDependencies( Module::$s_rootModule );
    }


    /// Utility function to create a module from an array of data
    static private function CreateModuleFromArray( $moduleData, $app ) {

        $module = new Module($moduleData['name'], $app);

        if( isset($moduleData['sources']) ) {
            $module->sources = $moduleData['sources'];
        }
        if( isset($moduleData['stylesheets']) ) {
            $module->stylesheets = $moduleData['stylesheets'];
        }
        if( isset($moduleData['scripts']) ) {
            $module->scripts = $moduleData['scripts'];
        }


        if( isset($moduleData['dependencies']) ) {
            foreach($moduleData['dependencies'] as $dependency ) {
                // TODO: process all dependencies AFTER
                $module->dependsOn[] = $dependency; //Module::Root()->getByFullPath($dependency);
            }
        }

        if( isset($moduleData['views']) ) {
            foreach($moduleData['views'] as $viewName => $viewPath ) {
                $module->addView( new View($viewName, $viewPath, $module ));
            }
        }

        if( isset($moduleData['children']) ) {
            foreach( $moduleData['children'] as $childName => $childData ) {
                $childData['name'] = $childName;
                $module->addChildModule( Module::CreateModuleFromArray($childData, $app) );
            }
        }

        return $module;
    }


    // Process dependencies
    //
    static private function ProcessDependencies( $module ) {

        $dependencyModules = array();

        foreach( $module->dependsOn as $dependencyString ) {
            $dependencyModules[] = Module::Root( $module->app )->getByFullPath($dependencyString);
        }

        foreach( $module->modules as $children ) {
            Module::ProcessDependencies( $children );
        }

        $module->dependsOn = $dependencyModules;
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
