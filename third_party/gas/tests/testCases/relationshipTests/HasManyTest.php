<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for `has_many` relationship
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.0.0
 * @author      Taufan Aditya
 */

class HasManyTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User      (./tests/dummyModels/user.php)
     * @see Model\Kid       (./tests/dummyModels/kid.php)
     * @see Model\Job       (./tests/dummyModels/job.php)
     * @see Model\Job\User  (./tests/dummyModels/job/user.php)
     * @see Model\Role      (./tests/dummyModels/role.php)
     * @see Model\Role\User (./tests/dummyModels/role/user.php)
     */
    public function setUp()
    {
        Model\User::setUp();
        Model\Kid::setUp();
        Model\Job::setUp();
        Model\Job\User::setUp();
        Model\Role::setUp();
        Model\Role\User::setUp();
    }

    public function testHasManySimple()
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

        // Grab related kid(s)
        $user1_kids = $user1->kid();

        // Should contain 2 kids
        $this->assertCount(2, $user1_kids);

        foreach ($user1_kids as $kid)
        {
            // Consist
            $this->assertInstanceOf('Gas\ORM', $kid);
            $this->assertInstanceOf('Gas\Data', $kid->record);

            // Check results
            // This should be kid with id `1` and `2`
            switch ($kid->id)
            {
                case '1':
                    $this->assertEquals($kid->name, 'Daria Doe');

                    break;

                case '2':
                    $this->assertEquals($kid->name, 'John Doe Jr');

                    break;
            }
            
        }
    }

    public function testHasManyThroughSimple()
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

        // Grab related job(s)
        $user1_jobs = $user1->job();

        // Should contain 2 jobs
        $this->assertCount(2, $user1_jobs);

        foreach ($user1_jobs as $job)
        {
            // Consist
            $this->assertInstanceOf('Gas\ORM', $job);
            $this->assertInstanceOf('Gas\Data', $job->record);

            // Check results
            // This should be job with id `2` and `3`
            switch ($job->id)
            {
                case '2':
                    $this->assertEquals($job->name, 'Politician');

                    break;

                case '3':
                    $this->assertEquals($job->name, 'Accountant');

                    break;
            }
            
        }
    }

    public function testHasManyThroughCustom()
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

        // Grab related role(s)
        $user1_roles = $user1->role();

        // Should contain 2 roles
        $this->assertCount(2, $user1_roles);

        foreach ($user1_roles as $role)
        {
            // Consist
            $this->assertInstanceOf('Gas\ORM', $role);
            $this->assertInstanceOf('Gas\Data', $role->record);

            // Check results
            // This should be role with id `2` and `3`
            switch ($role->id)
            {
                case '2':
                    $this->assertEquals($role->name, 'Moderator');

                    break;

                case '3':
                    $this->assertEquals($role->name, 'Member');

                    break;
            }
            
        }
    }
}