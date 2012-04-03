<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for Model Integrity
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.1
 * @author      Taufan Aditya
 */

class ModelTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     */
    public function setUp()
    {
        Model\User::setUp();
    }

    public function testApiMake()
    {
        // A model could be either instantiated or use `make` method
        $user_instantiated = new Model\User();
        $user_non_instantiated = Model\User::make();

        // Both should be instance of Gas ORM
        $this->assertInstanceOf('Gas\ORM', $user_instantiated);
        $this->assertInstanceOf('Gas\ORM', $user_non_instantiated);

        // Both should be equally same
        $this->assertEquals($user_instantiated, $user_non_instantiated);
    }
}