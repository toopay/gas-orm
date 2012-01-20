<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for `select` method
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.0.0
 * @author      Taufan Aditya
 */

class SelectTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     */
    public function setUp()
    {
        Model\User::setUp();
    }

    public function testSelectSimple()
    {
        // Get all users, but only for `id`, `username` and `name` fields
        $users = Model\User::select('id, username, name')->all();

        // Should be an array, contain 4 user object
        $this->assertCount(4, $users);

        foreach ($users as $user)
        {
            // Consist
            $this->assertInstanceOf('Gas\ORM', $user);
            $this->assertInstanceOf('Gas\Data', $user->record);

            //`id`, `username` and `name` field should contain something
            $this->assertInternalType('string', $user->id);
            $this->assertInternalType('string', $user->username);
            $this->assertInternalType('string', $user->name);
            
            // Other than `id`, `username` and `name`, other field should be empty
            $this->assertEmpty($user->email);
        }
    }
}