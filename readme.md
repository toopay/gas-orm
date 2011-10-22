# CodeIgniter Gas ORM Library

A lighweight and easy-to-use ORM for CodeIgniter

## Installation
Put Gas.php on your libraries folder and gas.php in config folder. 

## Configuration
Gas just have three option for configuration

```php
/* here you can specify your models directory. This is relative to application folder */
$config['models_path'] = 'models';

/* by default the prefix is _gas, so typically your model file name would be something like foo_gas.php */
$config['models_suffix'] = '_gas';

/* if this set to TRUE, then all models class will automaticly loaded. If this set to FALSE, you need to manually load each model you want to use, using $this->gas->load('foo', 'bar', 'and_so_on') */
$config['autoload_models'] = TRUE;
```

## Convention
Gas makes some assumptions about your database structure. Each table should have primary key, default to **id**. You can set this dynamically in your Gas model. Each table should have same name with its corresponding Gas model's name.

Typically your Gas model will be something like this, let say you have **user_gas.php** to hold **user** model.

```php
class User extends Gas {

}
```

if you need to go more depth with your model configuration, below is the common template of your Gas model :

```php
class User extends Gas {
    
    // if your table name different with your model class's name, you can set it to $table properties, eg :
    //
    // public $table = 'people';

    // if your primary key isn't id, you can set it to $primary_key properties, eg :
    //
    // public $primary_key = 'primary_id';

    // If your table has relationship, you can assign an array that contain its corresponding model
    // Gas support 'has_one', 'has_many', 'belongs_to' and 'has_and_belongs_to', eg :
    //
    // public $relations = array(
                            'has_one' => array('wife' => array()),
                            'has_many' => array('kid' => array()),
                            'has_and_belongs_to' => array('job' => array()),
                        );

    // Every Gas model could have _init function. It primarily used for set up a table's fields validation
    // but it can be used for construct/set any model properties or loading common resources.
    //
    // Available field to use is 'auto', 'char', 'int' and 'email'. You can add max length rule directly using [n].
    // But you can assign additional validation rule as array, including your own callback to second parameter, eg :
    //
    // 'somefield' => Gas::field('char[255]', array('required', 'matches', 'callback_some_check')),
    //
    // function _init()
    // {
    //    $this->_fields = array(
    //      'id'       => Gas::field('auto'),
    //      'name'     => Gas::field('char[40]'),
    //      'email'    => Gas::field('email'),
    //      'username' => Gas::field('char'),
    //      'active'   => Gas::field('int[1]'),
    //    );
    // }

    // Suppose you set some custom callback function on your field validation rule, now you will need to
    // specify it in your model. Your callback function will slightly different with normal CI callback function, 
    // it need to have two parameter : first for accept field and second for accept value. If you want to
    // set up a custom error message, you will need to add $field (the first parameter variable) as third parameter,
    // eg :

    // public function some_check($field, $val)
    // {
    //    if($val == 'must_like_these')
    //    {
    //        return TRUE;
    //    }
    //    else
    //    {
    //        $this->set_message('some_check', 'The %s field was an invalid autoincrement field.', $field);
    //  
    //        return FALSE;
    //    }
    // }
    
}
```

Gas support for cascading directories, so you can have as many sub-level folder as you want, in your primary models directory.

## Usage
The simplest way to start using Gas as your ORM, is to autoload Gas library in your application/config/autoload.php. 

```php
$autoload['libraries'] = array('gas');
```

If you decide to manually loading it, you will need to load/include your model first. Typically, you would do that as follow (eg, in some controller's function) :

```php
$this->load->library('gas');
// to see available models :
// var_dump($this->gas->list_models());

// now, if you want to load the user model
$this->gas->load('user');
```

now you are ready to go

### Fetch records
You can do almost anything you want : find by primary key, find where arguments, join, aggregates and so on. Heres some basic :
```php
$user = new User;

// all
$users = $user->all(); // will return a set/array of user object
if($user->has_result())
{
    // get total users
    echo 'There are total : .'$user->count();
    
    foreach($users as $single_user)
    {
        // to produce an array of single user
        var_dump($single_user->to_array());

        // or just fetch the corresponding table properties
        echo $single_user->id . '<br />';
        echo $single_user->email . '<br />';
        echo '<hr />';
    }
}

// first
$firstuser = $user->first(); // will return a single user object

// last
$lastuser = $user->last(); // will return a single user object

// aggregate : max, min, avg, sum
$max = $user->max(); // will return a single user object, with max id
$min = $user->min('money'); // will return a single user object, with min money
$avg = $user->avg();
$sum = $user->sum();

// finder : find, find_by_something, find_where
$someusers = $user->find(1, 2, 3); // will return a set/array of user
$someuser = $user->find(1); // will return a single user object with id = 1

$someusers = $user->find_by_email('foo@bar.com'); // will return a set/array of user object
$someuser = $user->find_by_email('foo@bar.com', 1); // will return a single match of user object

$someusers = $user->find_where(aray('active' => 1)); // will return a set/array of user object
$someuser = $user->find_where(aray('active' => 1), 1); // will return a single match of user object
```

