<?php

/// This is instantiated on every page, and carries information about the health of the
/// web-application.
class Application
{
	/// The name of the Application
	public $name;
	public $configs;

	/// Constrcutor!
	public function Application( $name, $configs ) {
		$this->name = $name;
		$this->configs = array(
		    "errors" => '',
		    "scripts" => '',
		    "stylesheets" => '',
		    "source" => '',
		    "request" => array(),
			"title" => $name,
			"starttime" => microtime(true),
			"public" => '/cache/public',
			"hostname" => $this->getServerName(),
		);
		$this->configs = array_merge( $configs, $this->configs );

		$this->errors = array();

		Module::Root($this);
	}

	/// Adds an error to the list and gathers additional data
	public function addError( $category, $errorString ) {
		//
		// TODO: Get the stack trace... then find the function and file.
		//

		$this->errors[] = array(
			"category" => $category,
			"errorString" => $errorString,
		);
	}

	// returns true if the app is in debug mode
	public function isDebug() {
		return $this->configs['debug'] == true;
	}

	// Routes are in the form of:
	//		"/route/url/(#id)" => view_path or routes_path
	//
	public function createRoutes( $routes ) {
		/*foreach() {

		}*/
		$this->routes = $routes;
	}

	public function run () {
		// Get teh current API call
		$route = $this->getCurrentUri();

		// Set all the important nonsense in here
		//$view = Module::Root($this)->getViewByRoute( $route );
		$view = Module::Root($this)->getViewByPath( "james.main" );

		die($view->getHTML($this->configs));
	}

	public function getServerName() {
		return $_SERVER['HTTP_HOST'];
	}


	// Get the current route
	function getCurrentUri() {
		$basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
		$uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
		if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
		$uri = '/' . trim($uri, '/');
		return $uri;
	}


	private $errors;
	private $routes;
}
