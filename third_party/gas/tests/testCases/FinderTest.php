<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for various finder methods
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.1
 * @author      Taufan Aditya
 */

class FinderTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User      (./tests/dummyModels/user.php)
     * @see Model\Wife      (./tests/dummyModels/wife.php)
     * @see Model\Kid       (./tests/dummyModels/kid.php)
     * @see Model\Job       (./tests/dummyModels/job.php)
     */
    public function setUp()
    {
        Model\User::setUp();
        Model\Wife::setUp();
        Model\Kid::setUp();
        Model\Job::setUp();
        Model\Job\User::setUp();
    }

    public function testAll()
    {
        // As default all will always return an array
        $users = Model\User::all();

        $this->assertInternalType('array', $users);

        // If first parameter passed as FALSE, then it will return an object if there is only a single record
        $users = Model\User::limit(1)->all(FALSE);

        $this->assertInternalType('object', $users);
    }

    public function testFindInvalidResources()
    {
        // Test looking for invalid user
        $user10 = Model\User::find(11);

        $this->assertInternalType('null', $user10);
    }

    public function testFindValidResources()
    {
        // Test looking for valid user
        $user1 = Model\User::find(1);

        $this->assertInternalType('object', $user1);
    }

    public function testFindByCollumnInvalidResources()
    {
        // Test looking for invalid user(s)
        $lambda = Model\User::find_by_username('lambda');

        $this->assertInternalType('array', $lambda);
        $this->assertEmpty($lambda);

        // If second parameter passed as FALSE, then it will return NULL if there is no valid resouces
        $lambda = Model\User::find_by_username('lambda', FALSE);

        $this->assertInternalType('null', $lambda);
        $this->assertEmpty($lambda);
    }

    public function testFindByCollumnValidResources()
    {
        // Test looking for valid user(s)
        $derek = Model\User::find_by_username('derek');

        $this->assertInternalType('array', $derek);
        $this->assertNotEmpty($derek);

        // If second parameter passed as FALSE, then it will return an object if there is only a single record
        $derek = Model\User::find_by_username('derek', FALSE);

        $this->assertInternalType('object', $derek);
        $this->assertTrue($derek instanceof Gas\ORM);
    }

    public function testOneToOneFinder()
    {
        // Find a one-to-one record
        $wife_of_user1 = Model\User::find(1)->wife();

        $this->assertInternalType('object', $wife_of_user1);
        $this->assertTrue($wife_of_user1 instanceof Gas\ORM);
    }

    public function testBelongsToFinder()
    {
        // Find a belongs-to record
        $dad_of_kid1 = Model\Kid::find(1)->user();

        $this->assertInternalType('object', $dad_of_kid1);
        $this->assertTrue($dad_of_kid1 instanceof Gas\ORM);
    }

    public function testOneToManyFinder()
    {
        // Find a one-to-many record
        $user1_kids = Model\User::find(1)->kid();

        $this->assertInternalType('array', $user1_kids);
        $this->assertNotEmpty($user1_kids);
    }

    public function testManyToManyFinder()
    {
        // Find a many-to-many record
        $user1_jobs = Model\User::find(1)->job();

        $this->assertInternalType('array', $user1_jobs);
        $this->assertNotEmpty($user1_jobs);

        // Vice-versa
        $job1_users = Model\Job::find(1)->user();

        $this->assertInternalType('array', $job1_users);
        $this->assertNotEmpty($job1_users);
    }
}