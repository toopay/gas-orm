<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for Core Class
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.1
 * @author      Taufan Aditya
 */

class CoreTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var object Database connection
	 */
	protected $db;

	public function setUp()
	{
		$this->db = &DB(DB_GROUP);
	}

	public function testConstructor()
	{
		$gas_core = new Gas\Core($this->db);

		$this->assertInstanceOf('Gas\\Core', $gas_core);
	}

	public function testStaticMethodToCallDBComponents()
	{
		$gas_core = new Gas\Core($this->db);

		$this->assertInstanceOf('CI_DB_pdo_utility', $gas_core::util());
		$this->assertInstanceOf('CI_DB_pdo_forge', $gas_core::forge());
	}

	public function testStaticMethodToCallLastCreated()
	{
		$gas_core = new Gas\Core($this->db);

		$this->setExpectedException('InvalidArgumentException', '[find]Could not find entity identifier');

		$gas_core::last_created();
	}

	public function testStaticMethodToCallRegexPattern()
	{
		$gas_core = new Gas\Core($this->db);

		$model = new Model\User();

		$this->assertInstanceOf('Model\\User', $gas_core::first($model));
	}
	
}