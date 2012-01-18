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

    public function testDeleteCompositeSingle()
    {
        // Re-init user model
        Model\User::setUp();

        // Find WHERE IN u_id = 1 and r_id = 2 (sequece was follow its composite keys order)
        //$role_user = Model\Role\User::find(array(1, 2));

        // Consist
        //$this->assertInstanceOf('Gas\ORM', $role_user);
        //$this->assertInstanceOf('Gas\Data', $role_user->record);
        
        // No way we allow composite table, which define entities, delete itself
        //$this->assertFalse($role_user->delete());
    }

    /*
    public function testDeleteCompositeViaParent()
    {
        // Re-init user model
        Model\User::setUp();

        // At this moment, this should result 2 records
        $exists = Gas\Core::query("SELECT * FROM `r_u` WHERE `r_u`.`u_id` = 1")->result_object();
        $this->assertCount(2, $exists);

        // In this case, it should not delete any of the role entry
        // Instead, it just delete the intermediate table (if it was a composite table)
        $this->assertTrue( Model\User::with('role')->delete(1));

        // Now, this should contain empty array
        $exists = Gas\Core::query("SELECT * FROM `r_u` WHERE `r_u`.`u_id` = 1")->result_object();
        $this->assertEmpty($exists);
    }
    */
}