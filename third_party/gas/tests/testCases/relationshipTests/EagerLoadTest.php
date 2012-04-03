<?php

/**
 * Gas ORM Unit Test
 *
 * Test case for eager loading relationship
 *
 * @package     Gas ORM
 * @category    Unit Test
 * @version     2.1.1
 * @author      Taufan Aditya
 */

class EagerLoadTest extends PHPUnit_Framework_TestCase {

    /**
     * @see Model\User      (./tests/dummyModels/user.php)
     * @see Model\Wife      (./tests/dummyModels/wife.php)
     * @see Model\Kid       (./tests/dummyModels/kid.php)
     * @see Model\Job\User  (./tests/dummyModels/job/user.php)
     * @see Model\Job       (./tests/dummyModels/job.php)
     * @see Model\Role      (./tests/dummyModels/role.php)
     * @see Model\Role\User (./tests/dummyModels/role/user.php)
     */
    public function setUp()
    {
        Model\User::setUp();
        Model\Wife::setUp();
        Model\Kid::setUp();
        Model\Job\User::setUp();
        Model\Job::setUp();
        Model\Role::setUp();
        Model\Role\User::setUp();
    }

    public function testEagerLoadBelongsToSimpleOne()
    {
        // Find wife with id `1`
        $wife1 = Model\Wife::with('user')->find(1);

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

    public function testEagerLoadBelongsToSimpleMany()
    {
        // Find kid with id `3`
        $kid3 = Model\Kid::with('user')->find(3);

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

    public function testEagerLoadHasOneSimple()
    {
        // Find user with id `1`
        $user1 = Model\User::with('wife')->find(1);

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

    public function testEagerLoadHasManySimple()
    {
        // Find user with id `1`
        $user1 = Model\User::with('kid')->find(1);

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

    public function testEagerLoadHasManyThroughSimple()
    {
        // Find user with id `1`
        $user1 = Model\User::with('job')->find(1);

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

    public function testEagerLoadHasManyThroughCustom()
    {
        // Find user with id `1`
        $user1 = Model\User::with('role')->find(1);

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

    public function testEagerLoadIntermixedRelationType()
    {
        // Find user with id `1` with his wife kid, job and role
        $user1 = Model\User::with('wife', 'kid', 'job', 'role')->find(1);

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