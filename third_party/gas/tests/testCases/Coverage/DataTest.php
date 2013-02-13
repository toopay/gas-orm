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

	public function testOffset()
	{
		$gas_data = new Gas\Data(array('foo' => 'bar'));

		$this->assertTrue($gas_data->offsetExists('foo'));
		$this->assertFalse($gas_data->offsetExists('undefined'));
		$this->assertFalse($gas_data->offsetExists('somethingelse'));

		$gas_data->offsetSet('lorem', 'ipsum');
		$this->assertEquals('ipsum', $gas_data->offsetGet('lorem'));

		$gas_data->offsetUnset('lorem');
		$this->assertFalse($gas_data->offsetExists('lorem'));

		$gas_data->offsetSet(NULL, 'lorem');

		$this->assertEquals('lorem',$gas_data->last());
	}

	public function testDataNavigation()
	{
		$gas_data = new Gas\Data(array('first' => 'Volatille' ,'midle' => 'Ok', 'last' => 'Late'));
		
		$this->assertTrue($gas_data->valid());
		$this->assertEquals(3, $gas_data->count());
		$this->assertEquals('first', $gas_data->key());

		$gas_data->next();

		$this->assertEquals('midle', $gas_data->key());
		$this->assertEquals('Late', $gas_data->last());

		$gas_data->rewind();

		$this->assertEquals('Volatille', $gas_data->current());

		$gas_data->ksortAsc();

		$this->assertEquals('Volatille', $gas_data->current());

		$gas_data->ksortDesc();

		$this->assertEquals('Ok', $gas_data->current());
	}

	public function testDataOperation()
	{
		$gas_data = new Gas\Data(array('foo' => 'bar'));

		$this->assertArrayHasKey('foo', $gas_data->get());

		$gas_data->set('lorem', 'ipsum');

		$this->assertArrayHasKey('lorem', $gas_data->get());
	}
	
}