<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for ORM Class
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.2
 * @author      Taufan Aditya
 */

class ORMTest extends PHPUnit_Framework_TestCase {

	public function testHook()
	{
		$someModel = new Model\User();
		$this->assertInstanceOf('\Gas\ORM', $someModel->_before_check());
		$this->assertInstanceOf('\Gas\ORM', $someModel->_before_save());
		$this->assertInstanceOf('\Gas\ORM', $someModel->_before_delete());
		$this->assertInstanceOf('\Gas\ORM', $someModel->_after_check());
		$this->assertInstanceOf('\Gas\ORM', $someModel->_after_save());
		$this->assertInstanceOf('\Gas\ORM', $someModel->_after_delete());
	}

	public function testCallback()
	{
		$someModel = new Model\User();

		$this->assertTrue($someModel->_char_check('Some character'));
		$this->assertTrue($someModel->_date_check(date('Y-m-d H:i')));
	}

	public function testModel()
	{
		$someModel = new Model\User();
		$this->assertEquals('model\user', $someModel->model());
	}
}