<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for `all` method
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.1
 * @author      Taufan Aditya
 */

class AllTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     */
    public function setUp()
    {
        Model\User::setUp();
    }

    public function testAll()
    {
        // Populate all users
        $allusers = Model\User::all();

        // Should be an array, contain 4 user object
        $this->assertCount(4, $allusers);

        foreach ($allusers as $user)
        {
            // Consist
            $this->assertInstanceOf('Gas\ORM', $user);
            $this->assertInstanceOf('Gas\Data', $user->record);

            // Check results
            switch ($user->id)
            {
                case '1':
                    $this->assertEquals($user->name, 'John Doe');
                    $this->assertEquals($user->username, 'johndoe');

                    break;

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