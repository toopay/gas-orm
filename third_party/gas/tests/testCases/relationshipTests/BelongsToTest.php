<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for `belongs_to` relationship
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.0
 * @author      Taufan Aditya
 */

class BelongsToTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     * @see Model\Wife (./tests/dummyModels/wife.php)
     * @see Model\Kid  (./tests/dummyModels/kid.php)
     */
    public function setUp()
    {
        Model\User::setUp();
        Model\Wife::setUp();
        Model\Kid::setUp();
    }

    public function testBelongsToSimpleOne()
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

        // Grab related husband (user)
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

    public function testBelongsToSimpleMany()
    {
        // Find kid with id `3`
        $kid3 = Model\Kid::find(3);

        // Consist
        $this->assertInstanceOf('Gas\ORM', $kid3);
        $this->assertInstanceOf('Gas\Data', $kid3->record);
        
        // Check result
        $this->assertEquals($kid3->id, '3');
        $this->assertEquals($kid3->name, 'Abraham Jones');
        $this->assertEquals($kid3->age, '3');

        // Grab related father (user)
        $kid3_father = $kid3->user();

        // Consist
        $this->assertInstanceOf('Gas\ORM', $kid3_father);
        $this->assertInstanceOf('Gas\Data', $kid3_father->record);

        // Check results, this should be `Derek Jones` with `2` as his id
        // and `derek` as his username
        $this->assertEquals($kid3_father->id, '2');
        $this->assertEquals($kid3_father->name, 'Derek Jones');
        $this->assertEquals($kid3_father->username, 'derek');

        // Since the third parameter for kid <-> user relationship
        // contain `select:id,name,username` for pre-process relation
        // Other fields should be null
        $this->assertNull($kid3_father->email);
    }
}