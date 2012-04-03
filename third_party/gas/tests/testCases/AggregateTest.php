<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for `avg`, `max`, `min` and `sum` method
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.1
 * @author      Taufan Aditya
 */

class AggregateTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User (./tests/dummyModels/user.php)
     */
    public function setUp()
    {
        Model\User::setUp();
    }

    public function testAggregateMax()
    {
        // Get user with the max of `id`
        $user_max = Model\User::max();

        // Consist
        $this->assertInstanceOf('Gas\ORM', $user_max);
        $this->assertInstanceOf('Gas\Data', $user_max->record);

        // The max id would be '4'
        $this->assertEquals($user_max->id, '4');
    }

    public function testAggregateMin()
    {
        // Get user with the min of `id`
        $user_min = Model\User::min();

        // Consist
        $this->assertInstanceOf('Gas\ORM', $user_min);
        $this->assertInstanceOf('Gas\Data', $user_min->record);

        // The min id would be '1'
        $this->assertEquals($user_min->id, '1');
    }

    public function testAggregateAvg()
    {
        // Get average of all users `id`
        $user_avg = Model\User::avg();

        // Consist
        $this->assertInstanceOf('Gas\ORM', $user_avg);
        $this->assertInstanceOf('Gas\Data', $user_avg->record);

        // The average id would be (1+2+3+4)/4 = 2.5
        $this->assertEquals($user_avg->id, '2.5');
    }

    public function testAggregateSum()
    {
        // Get summed of all users `id`
        $user_sum = Model\User::sum();

        // Consist
        $this->assertInstanceOf('Gas\ORM', $user_sum);
        $this->assertInstanceOf('Gas\Data', $user_sum->record);

        // The summed id would be (1+2+3+4) = 10
        $this->assertEquals($user_sum->id, '10');
    }
}

