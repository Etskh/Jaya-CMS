<?php

require_once("lib/modules/View.php");

class Route
{
	static public function Add( $routeArray ) {
		foreach( $routeArray as $routePath => $routeConfig ) {
			$s_routes[] = new Route();
		}
	}

	static public function Parse( $get, $post ) {

	}

	static private $s_routes = array();

	public function Route() {

	}

}

Route::Add(array(
	"" => new View("static/home.php")
));
