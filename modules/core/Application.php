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
			"public" => '/cache/public'
		);
		$this->configs = array_merge( $configs, $this->configs );

		$this->errors = array();
	}

	/// Adds an error to the list and gathers additional data
	public function addError( $category, $errorString ) {
		//
		// Get the stack trace... then find the function and file.
		//

		$this->errors[] = array(
			"category" => $category,
			"errorString" => $errorString,
		);
	}

	/// Returns the number of "to-dos" in the code (must be uppercase)
	public function getTodos() {
		// Start in the base directory and scan all directories searching for "TODO"
		// and then number the results

		return 0;
	}

	private $errors;
}
