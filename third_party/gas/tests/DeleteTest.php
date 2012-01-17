<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for `delete` method
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.0.0
 * @author      Taufan Aditya
 */

class DeleteTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     */
    public function setUp()
    {
        // Set Up the user table
        Model\User::setUp();
    }

    public function testDeleteExisted()
    {
        // Find user id `1`
        $user1 = Model\User::find(1);

        // Consist
        $this->assertInstanceOf('Gas\ORM', $user1);
        $this->assertInstanceOf('Gas\Data', $user1->record);

        // Delete this user
        $user1->delete();
        
        // Now try fetch it again
        $user1 = Model\User::find(1);
        
        // Check result
        $this->assertNull($user1);
    }

    public function testDeleteByReference()
    {
        // Find user `2` and `3` and make sure they exists
        $user2 = Model\User::find(2);
        $user3 = Model\User::find(3);

        // Consist
        $this->assertInstanceOf('Gas\ORM', $user2);
        $this->assertInstanceOf('Gas\Data', $user2->record);
        $this->assertInstanceOf('Gas\ORM', $user3);
        $this->assertInstanceOf('Gas\Data', $user3->record);

        // Unset
        unset($user2, $user3);

        // Delete above users
        Model\User::delete(2, 3);

        // Find user `2` and `3` once more time
        $user2 = Model\User::find(2);
        $user3 = Model\User::find(3);

        // At this moment, user `2` and `3` should not exists anymore
        $this->assertNull($user2);
        $this->assertNull($user3);
    }
}