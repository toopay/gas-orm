<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for connection method
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.0
 * @author      Taufan Aditya
 */

class ConnectionTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Gas\Core (./classes/core.php)
     */
    public function tearDown()
    {
        // Re-connect using related environment
        Gas\Core::connect(DB_GROUP);
    }

    /**
     * @see Gas\Core (./classes/core.php)
     */
    public function testConnectionViaGroup()
    {
        // Connect using a valid group name in config
        Gas\Core::connect('testing_mysql');

        $db = Gas\Core::$db;

        $this->assertInstanceOf('CI_DB', $db);
        $this->assertInstanceOf('CI_DB_Driver', $db);
    }

    /**
     * @see Gas\Core (./classes/core.php)
     */
    public function testConnectionViaDsnPDODriver()
    {
        // Connect using a valid dsn string
        Gas\Core::connect('pdo://travis:@localhost:3306/gas_test?pdodriver=mysql');

        $db = Gas\Core::$db;

        $this->assertInstanceOf('CI_DB', $db);
        $this->assertInstanceOf('CI_DB_Driver', $db);
        $this->assertInstanceOf('CI_DB_PDO_Driver', $db);
    }

    /**
     * @see Gas\Core (./classes/core.php)
     */
    public function testConnectionViaDsnNativeDriver()
    {
        // Connect using a valid dsn string
        Gas\Core::connect('mysql://travis:@localhost:3306/gas_test');

        $db = Gas\Core::$db;

        $this->assertInstanceOf('CI_DB', $db);
        $this->assertInstanceOf('CI_DB_Driver', $db);
        $this->assertInstanceOf('CI_DB_MySQL_Driver', $db);
    }
}