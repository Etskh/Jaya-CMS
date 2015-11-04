<?php

class ModuleTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function test_ModuleLoader () {
		$app = new Application("Jaya-CMS Tests", array(
			'debug' => true
		));

		$root = Module::Root($app);
		$core = $root->getByFullPath('core');

		$this->assertEquals( $core->name, "core" );
	}

}
