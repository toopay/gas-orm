<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for `belongs_to` relationship
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.0.0
 * @author      Taufan Aditya
 */

class BelongsToTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     * @see Model\Wife (./tests/dummyModels/wife.php)
     */
    public function setUp()
    {
        Model\User::setUp();
        Model\Wife::setUp();
    }

    public function testBelongsToSimple()
    {
        // Find wife with id `1`
        $wife1 = Model\Wife::find(1);

        // Consist
        $this->assertInstanceOf('Gas\ORM', $wife1);
        $this->assertInstanceOf('Gas\Data', $wife1->record);
        
        // Check result
        $this->assertEquals($wife1->id, '1');
        $this->assertEquals($wife1->name, 'Lourie Jones');
        $this->assertEquals($wife1->hair_color, 'black');

        // Grab related husbanc (user)
        $wife1_husband = $wife1->user();

        // Consist
        $this->assertInstanceOf('Gas\ORM', $wife1_husband);
        $this->assertInstanceOf('Gas\Data', $wife1_husband->record);

        // Check results, this should be `Derek Jones` with `2` as his id
        // `derekjones@world.com` as his email and `derek` as his username
        $this->assertEquals($wife1_husband->id, '2');
        $this->assertEquals($wife1_husband->name, 'Derek Jones');
        $this->assertEquals($wife1_husband->email, 'derekjones@world.com');
        $this->assertEquals($wife1_husband->username, 'derek');
    }
}