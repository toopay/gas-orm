<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for Janitor Class
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.2
 * @author      Taufan Aditya
 */

class JanitorTest extends PHPUnit_Framework_TestCase {

	public function testGetGlobalPath()
	{
		// Test basepath
		$basePath = Gas\Janitor::path('base');

		$this->assertTrue(is_dir($basePath));

		// Test apppath
		$appPath = Gas\Janitor::path('app');

		$this->assertTrue(is_dir($appPath));

		// Test invalid path
		$this->setExpectedException('InvalidArgumentException', 'empty_arguments:path');
		$undefinedPath = Gas\Janitor::path('undefined');
	}

	public function testGetInput()
	{
		$sanity_input = Gas\Janitor::get_input('some_method', array('foo'=>'bar'), TRUE);

		$this->assertArrayHasKey('foo',$sanity_input);

		$continue_even_empty = Gas\Janitor::get_input('some_method', array(), FALSE);

		$this->assertEmpty($continue_even_empty);

		$this->setExpectedException('InvalidArgumentException', 'empty_arguments:some_method');
		
		Gas\Janitor::get_input('some_method', NULL, TRUE);
	}
	
}