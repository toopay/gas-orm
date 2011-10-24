# CodeIgniter Gas ORM Library

A lighweight and easy-to-use ORM for CodeIgniter

## Installation

Put Gas.php on your libraries folder and gas.php in config folder. 

## Overview

Gas was built specifically for CodeIgniter app. It uses standard CI DB packages, also take anvantages of its validator class. Gas provide methods that will map your database table and its relation, into accesible object.

### Convention

An example model of user :

```php

class User extends Gas {
    
    public $relations = array(
                            'has_one' => array('wife' => array()),
                            'has_many' => array('kid' => array()),
                            'has_and_belong_to' => array('job' => array()),
                        );

    function _init()
    {
        $this->_fields = array(
          'id'       => Gas::field('auto'),
          'name'     => Gas::field('char[40]'),
          'email'    => Gas::field('email'),
          'username' => Gas::field('char', array('callback_username_check')),
          'active'   => Gas::field('int[1]'),
        );
    }

}

```

### Usage example

Below is some of sample of Gas implementation.

#### Fetch record (finder)

```php

$user = new User;

$users = $user->all();

if($user->has_result())
{
    foreach($users as $single_user)
    {
        var_dump($single_user->to_array());
    }
}

$someuser = $user->find_by_email('foo@bar.com', 1);

$join_user = $user->left_join_phone()->find(35);

```

#### Write Operations (Insert, Update, Delete)

```php

$user = new User;

$user->id = $_POST['id'];
$user->name = $_POST['name'];
$user->email = $_POST['email'];
$user->username = $_POST['username'];

if( ! $user->save(TRUE))
{
    var_dump($user->errors());
}
else 
{
    $created_id = $user->last_id();
}

$user_update = $user->find($created_id);

if($user->has_result())
{
    $user_update->email = 'changed@world.com';
    $user->save();
    var_dump($user_update->errors());
}

var_dump($user->delete(1, 2, 3));

```

#### Relationship (one-to-one, one-to-many, many-to-many)

```php

$user1 = $user->find(1);

if($user->has_result())
{
    var_dump($user1->wife->to_array());
    var_dump($user1->wife->delete());
}

$user1 = $user->find(1);

if($user->has_result())
{
    foreach($user1->kid as $kid)
    {
        var_dump($kid->to_array());
    }
}

```

#### Eager Loading

```php

$user = new User;

$all_users = $user->with('wife', 'kid', 'job')->all(); 

if($user->has_result())
{
    foreach($all_users as $single_user)
    {
        echo $single_user->name.' has these details :';

        echo $single_user->name.' has one wife :';
        var_dump($single_user->wife->to_array()); 

        echo $single_user->name.' has many kids :';
        foreach($single_user->kid as $kid) 
        {
            var_dump($kid->to_array()); 
        }

        echo $single_user->name.' has several jobs :';
        foreach($single_user->job as $job) 
        {
            var_dump($job->to_array()); 
        }
    }
}

```
Comments on those libraries should self explanatory, but if you need to go more depth about Gas, read the full post about its functionality available methods and convention at [my blog post](http://taufanaditya.com/gas-orm "Gas ORM").





