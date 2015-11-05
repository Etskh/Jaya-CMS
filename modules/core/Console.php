<?php
require_once("TextParser.php");


class Console
{
	public function Console( $configs ) {

		$this->_file = $configs['file'];
		$this->_name = basename($this->_file);
		$this->_path = dirname($this->_file);

		$this->_commandList = array();
		if( isset($configs['commands'])) {
			$this->_commandList = $configs['commands'];
		}

		$this->_optionList = array();
		if( isset($configs['options'])) {
			$this->_optionList = $configs['options'];
		}

		$this->_colourize = false;
	}

	public function printc( $str ) {

		// TODO: If $this->_colourize is false, strip out the colour
		//

		$text = TextBlock::Parse( $str, 'Console::getConsoleColourFromHex', "\033[1m", "\033[0m" );

		print( $text->string );
	}

	static public function getConsoleColourFromHex( $hexCode ) {
		//
		// Because we're monsters, make the hex codes all fit nicely onto our scale
		// of 0 or f
		//
		for( $i = 0; $i < 3; $i++ ) {
			$hexCode[$i] = (intval($hexCode[$i], 16) > 8) ? 'f' : '0';
		}
		$colours = array(
			'000'	=> "\033[90m",
			'f00'	=> "\033[91m",
			'0f0'	=> "\033[92m",
			'ff0'	=> "\033[99m",
			'00f'	=> "\033[94m",
			'f0f'	=> "\033[95m",
			'0ff'	=> "\033[96m",
			'fff'	=> "\033[97m",
		);

		return array(
			$colors[$hexCode], "\033[0m"
		);
	}

	// Because life is short
	public function println ( $str ) {
		$this->printc($str."\n");
	}

	function printListItem( $starting, $info )
	{
		//
		// TODO: pre-colour parse these text boxes to preseve line-width
		//

		$LINE_WIDTH = 70;
		$PRE_WIDTH = 24;

		$starting = sprintf("%".($PRE_WIDTH - 4)."s    ", $starting );
		$desc = explode( "\n", wordwrap($info, $LINE_WIDTH - $PRE_WIDTH ));
		foreach( $desc as $line ) {
			$this->printc(sprintf($starting."%s\n", $line ));
			$starting = sprintf("%".$PRE_WIDTH."s", "");
		}
	}


	public function printHelp($app) {

		$this->printc(sprintf(
			"%s (%s) %s\n\n", $app->name, $this->_name, $app->configs['version']
		));

		$this->printc(sprintf(
			"  Usage:\n\t%s <action> [option [option [option...]]\n\n".
			"  Where <action> is one of the following:\n",
			$this->_name
		));

		foreach( $this->_commandList as $action => $actionInfo ) {
			$this->printListItem( $action, $actionInfo["desc"]);
		}

		if( count( $this->_optionList) > 0 ) {
			$this->printc("\n  and [option] is any of the following:\n");
			foreach( $this->_optionList as $option => $optionInfo ) {
				$this->printListItem(implode(", ", $optionInfo["args"]), $optionInfo["desc"]);
			}
		}
	}


	public function run ( $args, $app ) {

		// Set all the app configs
		//
		$this->_colourize = isset($app->configs['colourize']) && $app->configs['colourize'];

		//
		// TODO: Scan args for options,
		// if it's not an option, it must be a command
		//
		if( count($args) == 1 ) {
			$this->printHelp($app);
			return false;
		}
		$cmd = $args[1];

		//
		// Grab the action and run it
		//
		if( ! isset($this->_commandList[$cmd])) {
			$this->printHelp($app);
			return false;
		}

		$action = $this->_commandList[$cmd]['action'];

		switch($action) {
		case "{HELP}":
			$this->printHelp($app);
			break;
		case "{VERSION}":
			$this->println($app->configs['version']);
			break;
		default:
			//
			// Nothing we know? Run it on the command line
			$action = str_replace("{path}", $this->_path, $action );
			exec($action, $output, $return_var);
			break;
		}
		return true;
	}

	private $_file;
	private $_path;
	private $_name;
	private $_colourize;
	private $_commandList;
	private $_optionList;
}
