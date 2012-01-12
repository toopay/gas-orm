<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for `find` and `find_by` method
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.0.0
 * @author      Taufan Aditya
 */

class FinderTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     */
    public function setUp()
    {
        Model\User::setUp();
    }

    public function testFindInvalidResources()
    {
        // Test looking for invalid user
        $user10  = Model\User::find(10);
        $userfoo = Model\User::find_by_username('foo');
        $this->assertEmpty($user10);
        $this->assertEmpty($userfoo);
    }

    public function testFindSingle()
    {
        // Find user with id `1`
        $user1 = Model\User::find(1);

        // Consist
        $this->assertInstanceOf('Gas\ORM', $user1);
        $this->assertInstanceOf('Gas\Data', $user1->record);
        
        // Check result
        $this->assertEquals($user1->id, '1');
        $this->assertEquals($user1->name, 'John Doe');
        $this->assertEquals($user1->email, 'johndoe@john.com');
        $this->assertEquals($user1->username, 'johndoe');
    }

    public function testFindSeveral()
    {
        $users = Model\User::find(2, 3, 4);

        // Should be an array, contain 3 user object
        $this->assertCount(3, $users);

        foreach ($users as $user)
        {
            // Consist
            $this->assertInstanceOf('Gas\ORM', $user);
            $this->assertInstanceOf('Gas\Data', $user->record);

            // Check results
            switch ($user->id)
            {
                case '2':
                    $this->assertEquals($user->name, 'Derek Jones');
                    $this->assertEquals($user->username, 'derek');

                    break;

                case '3':
                    $this->assertEquals($user->name, 'Frank Sinatra');
                    $this->assertEquals($user->username, 'fsinatra');

                    break;

                case '4':
                    $this->assertEquals($user->name, 'Chris Martin');
                    $this->assertEquals($user->username, 'cmartin');

                    break;
            }
           
        }
    }
}