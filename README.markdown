# CodeIgniter Gas ORM Library

A lighweight and easy-to-use ORM for CodeIgniter

## Installation

Put Gas.php on your libraries folder and gas.php in config folder. Optionally, there was unit testing packages included as a controller, named gasunittest.php, which will auto-created neccesaryy stuff (files and tables) to performing test to evaluate all available implementation to determine if it is producing the correct data type and result. Copy gasunittest.php into your controllers folder, and run it.

## About Gas

Gas was built specifically for CodeIgniter app. It uses standard CI DB packages, also take anvantages of its validator class. Gas provide methods that will map your database table and its relation, into accesible object.

## Usage Example

Before start using any of Gas available methods, you should have a gas model, which follow Gas standard model convention. Then, you can start using it either by instantiate new Gas object or by using factory interface.

### FINDER

```php
// all : will return an array of user's object
$users = Gas::factory('user')->all();

$firstuser = Gas::factory('user')->first();

$lastuser = Gas::factory('user')->last();

$max = Gas::factory('user')->max();

$min = Gas::factory('user')->min();

$avg = Gas::factory('user')->avg('id', 'average_id');

$sum = Gas::factory('user')->sum('id', 'sum_of_id');

$someuser = Gas::factory('user')->find(1);

$someusers = Gas::factory('user')->find(1, 2, 3);

$someusers = Gas::factory('user')->find_by_email('johndoe@yahoo.com');

$someusers = Gas::factory('user')->group_by('email')->all();

$someusers = $user->like('email', 'yahoo.com')->all();

$somejoinedusers = $user->left_join_job('job.id = user.id')->all();
```

### WRITE OPERATION (CREATE, UPDATE, DELETE)

```php
$_POST = array('id' => null, 'name' => 'Mr. Foo', 'email' => 'foo@world.com', 'username' => 'foo');

$new_user = new User;

$new_user->fill($_POST);

// If something goes wrong in validation process, you can retrieve error via 'errors' method
if ( FALSE == ($affected_rows = $new_user->save(TRUE))) die($new_user->errors());

// From last created record, using 'last_id' method, eg : will return '1', because above is first record
$new_id = $new_user->last_id();

$recent_user = Gas::factory('user')->find($new_id);

$_POST = array('name' => 'Mr. Bar', 'email' => 'bar@world.com');

$recent_user->fill($_POST);

$recent_user->username = 'bar';

if ( ! $recent_user->save(TRUE)) die($recent_user->errors());

// To delete something, you can directly assign id, or 'delete' will see through your recorded logic, eg : 
$now_user = Gas::factory('user')->find($new_id);

// Just ensure that data has been updated 
if ($now_user->username != 'bar') die('Gas update was unsuccessfully executed!');

// This will delete user 1 
$now_user->delete();
```

### RELATIONSHIP (ONE-TO-ONE, ONE-TO-MANY, MANY-TO-MANY)

```php
// One-To-One : Will return an object of wife, which have user_id = 1
$somewife = Gas::factory('user')->find(1)->wife;

// One-To-Many : Will return an array of kid object, which have user_id = 1
$somekids = Gas::factory('user')->find(1)->kid;

// Many-To-Many : Will return an array of job object, based by pivot table (job_user), which have user_id = 4
$somejobs = Gas::factory('user')->find(4)->job;
```

### EAGER LOADING

```php
// Eager Loading : Will return an array of user object, alongside with each relational table with WHERE IN(N+)
$allinone = Gas::factory('user')->with('wife', 'kid', 'job')->all();
```

Comments on those libraries should self explanatory, but if you need to go more depth about Gas, use **gasunittest.php** or read the full post about its functionality available methods and convention at [my blog post](http://taufanaditya.com/gas-orm "Gas ORM").
