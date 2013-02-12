<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for Data Class
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.2
 * @author      Taufan Aditya
 */

class DataTest extends PHPUnit_Framework_TestCase {

	public function testOffsetExists()
	{
		$gas_data = new Gas\Data(array('foo' => 'bar'));

		$this->assertTrue($gas_data->offsetExists('foo'));

		$this->assertFalse($gas_data->offsetExists('undefined'));
		$this->assertFalse($gas_data->offsetExists('somethingelse'));
	}
	
}