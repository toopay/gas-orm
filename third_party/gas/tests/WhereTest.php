<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for `where` method
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.0.0
 * @author      Taufan Aditya
 */

class WhereTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     */
    public function setUp()
    {
        Model\User::setUp();
    }

    public function testWhereOneParameterString()
    {
        // Where usename is `johndoe`
        $johndoe = Model\User::where("username = 'johndoe'")->get(1);
         // Consist
        $this->assertInstanceOf('Gas\ORM', $johndoe);
        $this->assertInstanceOf('Gas\Data', $johndoe->record);
        // Check result
        $this->assertEquals($johndoe->id, '1');
        $this->assertEquals($johndoe->name, 'John Doe');
        $this->assertEquals($johndoe->username, 'johndoe');
    }

    public function testWhereOneParameterArray()
    {
        // Where usename is `johndoe`
        $johndoe = Model\User::where(array('username' => 'johndoe'))->get(1);
         // Consist
        $this->assertInstanceOf('Gas\ORM', $johndoe);
        $this->assertInstanceOf('Gas\Data', $johndoe->record);
        // Check result
        $this->assertEquals($johndoe->id, '1');
        $this->assertEquals($johndoe->name, 'John Doe');
        $this->assertEquals($johndoe->username, 'johndoe');
    }

    public function testWhereTwoParameters()
    {
        // Where usename is `johndoe`
        $johndoe = Model\User::where('username', 'johndoe')->get(1);
         // Consist
        $this->assertInstanceOf('Gas\ORM', $johndoe);
        $this->assertInstanceOf('Gas\Data', $johndoe->record);
        // Check result
        $this->assertEquals($johndoe->id, '1');
        $this->assertEquals($johndoe->name, 'John Doe');
        $this->assertEquals($johndoe->email, 'johndoe@john.com');
    } 
}