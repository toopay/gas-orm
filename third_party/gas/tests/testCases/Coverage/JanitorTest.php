<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for Janitor Class
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.1
 * @author      Taufan Aditya
 */

class JanitorTest extends PHPUnit_Framework_TestCase {

	public function testGetInputThrowExceptionIfEmptyInput()
	{
		$this->setExpectedException('InvalidArgumentException', 'empty_arguments:some_method');
		
		Gas\Janitor::get_input('some_method', NULL, TRUE);
	}
	
}