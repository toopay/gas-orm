<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for `save` method
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.0.0
 * @author      Taufan Aditya
 */

class SaveTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     */
    public function setUp()
    {
        // Set Up then truncate the user table
        Model\User::setUp();
        Model\User::truncate();
    }

    public function testSaveInsert()
    {
        // Create new resource
        $data = array('name' => 'Mr. Foo', 'username' => 'foo', 'email' => 'foo@world.com');
        Model\User::make($data)->save();

        // Get the new created resource id, then get those resource
        $id  = Model\User::insert_id();
        $foo = Model\User::find($id);
        
        // Consist
        $this->assertInstanceOf('Gas\ORM', $foo);
        $this->assertInstanceOf('Gas\Data', $foo->record);
        
        // Check result
        $this->assertEquals($foo->id, '1');
        $this->assertEquals($foo->name, 'Mr. Foo');
        $this->assertEquals($foo->email, 'foo@world.com');
        $this->assertEquals($foo->username, 'foo');
    }

    public function testSaveUpdate()
    {
       // Create new resource
        $data = array('name' => 'Mr. Foo', 'username' => 'foo', 'email' => 'foo@world.com');
        Model\User::make($data)->save();

        // Get the new created resource id, then get those resource
        $id  = Model\User::insert_id();
        $foo = Model\User::find($id);

        // At this moment, resource should contain all Mr. Foo values
        $this->assertEquals($foo->name, 'Mr. Foo');
        $this->assertEquals($foo->email, 'foo@world.com');
        $this->assertEquals($foo->username, 'foo');

        // Change all Foo's attribute into Bar
        $foo->name     = 'Mr. Bar';
        $foo->email    = 'bar@world.com';
        $foo->username = 'bar';

        // Update
        $foo->save();

        // Retrive back the user using old id
        $bar = Model\User::find($id);
        
        // Consist
        $this->assertInstanceOf('Gas\ORM', $bar);
        $this->assertInstanceOf('Gas\Data', $bar->record);
        
        // Check result
        $this->assertEquals($bar->id, '1');
        $this->assertEquals($bar->name, 'Mr. Bar');
        $this->assertEquals($bar->email, 'bar@world.com');
        $this->assertEquals($bar->username, 'bar');
    }

}