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

    public function testWhereOrSimple()
    {
        // Where username is 'johndoe' or name is 'Frank Sinatra'
        $users = Model\User::where('username', 'johndoe')
                           ->or_where('name', 'Frank Sinatra')
                           ->all();

        // Should be an array, contain 2 user object
        $this->assertCount(2, $users);

        foreach ($users as $user)
        {
            // Consist
            $this->assertInstanceOf('Gas\ORM', $user);
            $this->assertInstanceOf('Gas\Data', $user->record);

            // Should contain John Doe and Frank Sinatra
            if ($user->username == 'johndoe')
            {
                $this->assertEquals($user->name, 'John Doe');
            }
            elseif ($user->name == 'Frank Sinatra')
            {
                $this->assertEquals($user->username, 'fsinatra');
            }
           
        }
    }

    public function testWhereAndSimple()
    {
        // Where username is 'johndoe' and name is 'John Doe'
        $user = Model\User::where('username', 'johndoe')
                          ->or_where('name', 'John Doe')
                          ->all();

        // Consist
        $this->assertInstanceOf('Gas\ORM', $user);
        $this->assertInstanceOf('Gas\Data', $user->record);
        
        // Should be resulted John Doe details
        $this->assertEquals($user->username, 'johndoe');
        $this->assertEquals($user->name, 'John Doe');
    }
}