<?php

/**
 *	This is instantiated on every page, and carries information about the health of the
 *	web-application.
*/
class Application
{
	/** The application's name (name of project) */
	public $name;
	/** Array of settings sent to each view template renderer */
	public $configs;
	/** The DB object, `false` if it's not configured */
	public $db;

	/** Creates an application, needs a name and (at least) an empty array */
	public function Application( $name, $configs ) {
		$this->version = "0.1.0";
		$this->name = $name;
		$this->configs = array(
			"version" => $this->version,
		    "errors" => '',
		    "scripts" => '',
		    "stylesheets" => '',
		    "source" => '',
		    "request" => array(),
			"title" => $name,
			"starttime" => microtime(true),
			"public" => '/cache/public',
			"hostname" => $this->getServerName(),
			"page-404" => 'core.404', // view file for 404 page - can be overwritten
			"page-403" => 'core.403', // view file for 403 page - can be overwritten
		);
		$this->configs = array_merge( $configs, $this->configs );

		$this->db = false;
		/*if( ! isset($this->configs['database']) ) {
			// TODO: Create a file-based database
			$this->configs["database"] = "sqlite:./data/site.db";
			$this->db = new Database($this->configs['database']);
		}
		else {
			$this->db = new Database($this->configs['database']);
		}*/

		$this->errors = array();
		$this->routes = array();

		Module::Root($this);
	}

	/** Adds an error to the list and gathers additional data */
	public function addError( $category, $errorString ) {
		//
		/** TODO: Get the stack trace... then find the function and file. */
		//

		$this->errors[] = array(
			"category" => $category,
			"errorString" => $errorString,
		);
	}

	/** returns true if the app is in debug mode */
	public function isDebug() {
		return $this->configs['debug'] == true;
	}



	/**
	 *	Method to create the routes for the application.
	 *	Routes are in the form of:
	 *		"/route/&slug/#id" => path
	 *	where `route` is an immutable string,
	 *	`slug` can be any URL valid slug,
	 *	`id` is an integer value
	 *
	 *	`path` is either the module path (moudule.view) to a view or "routes",
	 *	which means that the routes of this submodule will be tacked on to the end
	 *	of the initial route
	 *
	 *	Notes: since this function runs in the main sequence, it should be optimised.
	 */
	public function createRoutes( $routes ) {
		// TODO: if it's debug mode, then check all routes point to reasonable modules

		// if debug is disabled, then rely on urls.php
		// if it doesn't exist, then create it.
		$output = array();


		foreach( $routes as $route => $destination ) {
			// Turn the routes into regexes

			$patterns = array(
				'/\{#(\w+)\}/',
				'/\{\$(\w+)\}/',
			);
			$replacements = array(
				'(?P<$\1>\d+)',
				'(?P<$\1>\w+)',
			);

			$route = trim($route, '/');
			$route = preg_replace($patterns, $replacements, $route );

			$output[$route] = $destination;

			//
			//while( preg_match('/#(?P<identifier>[\w\-]+)/', $route, $matches) == 1 ) {
			//	$matches
			//}

			//$this-routes[] = array(
			//	"name" =>
			//)

			// Change all &name into (?P<name>\w+) with name as the Nth identifier

			// Change all #name into  (?P<digit>\d+) with name as the Nth identifier

			// If the trailing thing leads to a .routes, then include them in the route list
			//
			//$routeLoc = strpos( $destination, ".routes");
			//if( $routeLoc !== false ) {
			//	$modulePath = substr($destination, 0, $routeLoc );
			//	$module = Module::Root($this)->getByFullPath($modulePath);
			//
			//}
		}
		//
		// Save the routes to urls.php
		//
		$export = sprintf("<?php\ndefine(\"CACHED_URLS\", %s);?>", var_export($output,true) );
		file_put_contents("cache/urls.php", $export);

		$this->routes = $output;
	}

	/**
	 *	This is where the main begins
	 */
	public function run () {
		$url = trim($this->getCurrentUri(), '/');

		// If the currentURL is '/', then JUST return the main
		if( $url == '') {
			$view = Module::Root($this)->getViewByPath( $this->routes[''] );
			die($view->getHTML($this->configs));
		}

		foreach( $this->routes as $route => $destination ) {

			$route = "/".str_replace("/", "\/", $route)."/";
			if( preg_match($route, $url, $matches) === 1 ) {
				var_dump($matches);
				die("Matched!".$route);
			}
			//die( $route );
		}
		die("Ran");



		// Get teh current API call
		//$url = explode('/', $url);

		// Set all the important nonsense in here
		/*foreach( $this->routes as $routeKey => $path ) {
			foreach( $url as $urlKey => $urlPart ) {
				if( $urlPart == $path ) {

					/*
					since /posts == posts, continue down this route path:
					It's the last route; we can see what it points to
					"posts.routes"
					We should check the module 'posts' for things. Return the moduel object.
					$posts = the module that this thing points to.
					is the remainder ".routes"? Then we can go ahead and take the
					route list from the module and keep parsing based on that.
					*\/
				}
			}
		}*/

		// Don't know what it is, sorry

		//$view = Module::Root($this)->getViewByRoute( $route );
		$view = Module::Root($this)->getViewByPath( $this->configs['page-404'] );
		$view->setCode(404);
		die($view->getHTML($this->configs));
	}


	/** Returns the name of the server that we're asking about to get here */
	public function getServerName() {
		if( isset($_SERVER['HTTP_HOST'])) {
			return $_SERVER['HTTP_HOST'];
		}
		return 'Unknown'; // probably testing
	}


	/** Returns the current route */
	function getCurrentUri() {
		$basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
		$uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
		if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
		$uri = '/' . trim($uri, '/');
		return $uri;
	}

	/** An array of errors - added to the config at the end */
	private $errors;
	/** An array of routes that the application knows about */
	private $routes;
	/** Contains the version information for the build of Jaya-CMS */
	private $version;
}
