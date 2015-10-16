<?php


//
// Module class
//
class Module {

    static public function Root() {
        if( Module::$s_rootModule == null ) {
            Module::LoadAllModules();
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


    public function Module( $name, $configs=array() ) {
        $this->name = $name;
        $this->parent = null;
        $this->modules = array();
        $this->dependsOn = array();

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
    }

    public function getByFullArrayPath_r( $depth, $arPath ) {
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

    public function getByFullArrayPath( $arPath ) {
        return $this->getByFullArrayPath_r( 0, $arPath);
    }

    public function getByFullPath( $path ) {
        return $this->getByFullArrayPath( explode(".",$path) );
    }

    public function getFullPath() {
        $parentPath = '';
        if( $this->parent != null ) {
            $parentPath = $this->parent->getFullPath() . ".";
        }
        return $parentPath . $this->name;
    }

    public function getFullDirPath() {
        return str_replace(".", DIRECTORY_SEPARATOR, $this->getFullPath()).DIRECTORY_SEPARATOR;
    }

    // Returns an array of all paths to module styles AND dependency styles
    // it will always return the dependencies first
    // if there are sources that are less files, it will create a css file in the cache
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

    public $name;
    public $dependsOn;
    public $parent;
    public $modules;

    public $sources;
    public $scripts;
    public $stylesheets;

    public $views;


    static private function CreateModuleFromArray( $moduleData ) {
        //print("<pre>".print_r($moduleData, true)."</pre>");

        $module = new Module($moduleData['name']);

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
                $module->dependsOn[] =  Module::Root()->getByFullPath($dependency);
            }
        }

        if( isset($moduleData['views']) ) {
            foreach($moduleData['views'] as $viewName => $viewPath ) {
                $module->addView( new View($viewName, $viewPath));
            }
        }

        if( isset($moduleData['children']) ) {
            foreach( $moduleData['children'] as $childName => $childData ) {
                $childData['name'] = $childName;
                $module->addChildModule( Module::CreateModuleFromArray($childData) );
            }
        }

        return $module;
    }

    static private $s_rootModule = null;
    static public function LoadAllModules() {
        Module::$s_rootModule = new Module("modules");
        $files = scandir("modules");
        foreach($files as $value){
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

                    Module::$s_rootModule->addChildModule( Module::CreateModuleFromArray($json_a) );
                }
	        }
        }
    }
}
