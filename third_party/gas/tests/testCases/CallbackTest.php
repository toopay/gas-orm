<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for callbacks method(s)
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.0
 * @author      Taufan Aditya
 */

class CallbackTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     */
    public function setUp()
    {
        // Set Up then truncate the user table
        Model\User::setUp();
        Model\User::truncate();
    }

    public function testCallbackSave()
    {
        // Create new resource with preserved value for username 
        $data = array('id' => 10, 'name' => 'Mr. Foo', 'username' => 'administrator', 'email' => 'foo@world.com');
        $foo  = Model\User::make($data);

        // In corresponding model, there is `_before_save` callback
        // to prevent new users register using `administrator` as their username
        $foo->save();
        unset($foo);

        // Get the last created entry
        $foo = Model\User::find(10);
        
        // Consist
        $this->assertInstanceOf('Gas\ORM', $foo);
        $this->assertInstanceOf('Gas\Data', $foo->record);
        
        // Since the before_save already make sure that username can not contain
        // `administrator`, it turn into `member`
        $this->assertEquals($foo->username, 'member');
    }
}