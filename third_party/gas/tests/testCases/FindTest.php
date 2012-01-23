<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for `find` method
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.0.0
 * @author      Taufan Aditya
 */

class FindTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User      (./tests/dummyModels/user.php)
     * @see Model\Wife      (./tests/dummyModels/wife.php)
     * @see Model\Role\User (./tests/dummyModels/user.php)
     */
    public function setUp()
    {
        Model\User::setUp();
        Model\Wife::setUp();
        Model\Role\User::setUp();
    }

    public function testFindInvalidResources()
    {
        // Test looking for invalid user
        $user10 = Model\User::find(11);
        $this->assertEmpty($user10);
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
        $wifes = Model\Wife::find(1, 2, 3);

        // Should be an array, contain 3 wife objects
        $this->assertCount(3, $wifes);

        foreach ($wifes as $wife)
        {
            // Consist
            $this->assertInstanceOf('Gas\ORM', $wife);
            $this->assertInstanceOf('Gas\Data', $wife->record);

            // Check results
            switch ($wife->id)
            {
                case '1':
                    $this->assertEquals($wife->name, 'Lourie Jones');

                    break;

                case '2':
                    $this->assertEquals($wife->name, 'Patricia Doe');

                    break;

                case '3':
                    $this->assertEquals($wife->name, 'Lily Sinatra');

                    break;
            }
           
        }
    }

    public function testFindCompositeSingle()
    {
        // Find WHERE IN u_id = 1 and r_id = 2 (sequece was follow its composite keys order)
        $role_user = Model\Role\User::find(array(1, 2));

        // Consist
        $this->assertInstanceOf('Gas\ORM', $role_user);
        $this->assertInstanceOf('Gas\Data', $role_user->record);
        
        // Check result
        $this->assertEquals($role_user->u_id, '1');
        $this->assertEquals($role_user->r_id, '2');
    }

    public function testFindCompositeSeveral()
    {
        // Find sequenced of records (which its paired ids follow its composite keys order)
        $role_users = Model\Role\User::find(array(1, 2), array(3, 2));

        // Should be an array, contain 2 role_user object
        $this->assertCount(2, $role_users);

        foreach ($role_users as $role_user)
        {
            // Consist
            $this->assertInstanceOf('Gas\ORM', $role_user);
            $this->assertInstanceOf('Gas\Data', $role_user->record);

            // Check results
            switch ($role_user->u_id)
            {
                case '1':
                    $this->assertEquals($role_user->r_id, '2');

                    break;

                case '3':
                    $this->assertEquals($role_user->r_id, '2');

                    break;
            }
           
        }
    }
}