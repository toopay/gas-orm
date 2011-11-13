# CodeIgniter Gas ORM Library

A lighweight and easy-to-use ORM for CodeIgniter

## Requirement

* PHP v.5.2.x
* CodeIgniter v.2.x.x

## Installation

There is only one simple step, to start working with Gas ORM in your CodeIgniter application : copy all Gas ORM files respectively. 

Optionally, there was unit testing packages included as a controller, named gasunittest.php, which will auto-created neccesaryy stuff (files and tables) to performing test to evaluate all available implementation to determine if it is producing the correct data type and result. Copy gasunittest.php into your controllers folder, and run it.

**NOTE : you didnt need to load database when working with Gas, Gas will automatically doing that.**

## About Gas

Gas was built specifically for CodeIgniter app. It uses standard CI DB packages, also take anvantages of its validator class. Gas provide methods that will map your database table and its relation, into accesible object.

## Features

- Supported databases : cubrid, mssql, mysql, oci8, odbc, postgre, sqlite, sqlsrv.
- Support multiple database connection.
- Multiple relationship (has_one, has_many, belongs_to, has_and_belongs_to) with custom relationship setting (through, foreign_key, foreign_table, self)
- Self-referential and adjacency column/data (hierarchical data).
- Various finder method (can chained with most of CI AR) and aggregates.
- Validation and auto-mapping input collection, with minimal setup.
- Hooks points, to control over your model.
- Extensions, to share your common function/library across your model.
- Transaction, cache, and other CI AR goodness.

## Planned Features

- Auto-create and auto-synchronize tables (utilize Migration).
- Support for tree traversal.

More useful features, but keep both size and performance for a good use.

## Convention and usage

Before start using any of Gas available methods, you should have a gas model, which follow Gas standard model convention. Then, you can start using it either by instantiate new Gas object or by using factory interface.

You can see Gas convention and examples in these public gist : https://gist.github.com/1339003

```php
// FINDER
$all = Gas::factory('user')->all());
$some_user = Gas::factory('user')->find_by_username('foo');

// WRITE (INSERT, UPDATE, DELETE) AND VALIDATION
$new_user = new User;
$new_user->fill($_POST)->save(TRUE);

// RELATIONSHIP & EAGER LOADING
$some_user = Gas::factory('user')->with('wife', 'kid', 'job')->find(1));
$some_user_wife = $some_user->wife;
```

Comments on those libraries should self explanatory, but if you need to go more depth about Gas, use **gasunittest.php** or read the full post about its functionality available methods and convention at [my blog post](http://taufanaditya.com/gas-orm "Gas ORM").
