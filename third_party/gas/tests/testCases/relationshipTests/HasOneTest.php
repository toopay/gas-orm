<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for `has_one` relationship
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.1
 * @author      Taufan Aditya
 */

class HasOneTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     * @see Model\Wife (./tests/dummyModels/wife.php)
     */
    public function setUp()
    {
        Model\User::setUp();
        Model\Wife::setUp();
    }

    public function testHasOneSimple()
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

        // Grab related wife
        $user1_wife = $user1->wife();

        // Consist
        $this->assertInstanceOf('Gas\ORM', $user1_wife);
        $this->assertInstanceOf('Gas\Data', $user1_wife->record);

        // Check results, this should be `Patricia Doe` with `2` as her id
        $this->assertEquals($user1_wife->id, '2');
        $this->assertEquals($user1_wife->name, 'Patricia Doe');

        // Since the third parameter for user <-> wife relationship
        // contain `select:id,name` for pre-process relation
        // Other fields should be null
        $this->assertNull($user1_wife->hair_color);
    }
}