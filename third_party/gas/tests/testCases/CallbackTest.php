<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for callbacks method(s)
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.2
 * @author      Taufan Aditya
 */

class CallbackTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     */
    public function setUp()
    {
        // Set Up then truncate the user and job table
        Model\User::setUp();
        Model\User::truncate();
        Model\Job::setUp();
        Model\Job::truncate();
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

        // 'hourly' is not a valid field
        $data = array('id' => 9, 'name' => 'Developer', 'description' => 'Code!', 'hourly' => 50);
        $developer = Model\Job::make($data);

        // In corresponding model, there is `_before_save` callback
        // to filtered the data, so only matching fields will be used
        // @see : third_party/gas/tests/dummyModels/job.php
        $developer->save();
        unset($developer);

        // Get the last created entry
        $developer = Model\Job::find(9);
        
        // Consist
        $this->assertInstanceOf('Gas\ORM', $developer);
        $this->assertInstanceOf('Gas\Data', $developer->record);
        $this->assertEquals('Developer', $developer->name);
        $this->assertEquals('Code!', $developer->description);

        // 'nickname' is not a valid field
        $data = array('description' => 'Code and Heinekken!', 'nickname' => 'Developah...');
        $developer->record->set('data', $data);

        // Update
        $developer->save();

        $this->assertEquals('Code and Heinekken!', $developer->description);
    }
}