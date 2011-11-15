.. Gas ORM documentation [quickstart]

Quick Start
===========

Gas ORM is a library, so you will need to load it first, or for for convenience you can autoload it on **autoload.php** located under **aplication/config** folder. ::

	$autoload['libraries'] = array('gas');

Otherwise, you would need to load Gas library manually.

Gas ORM at a Glance
+++++++++++++++++++

Before start using any of Gas available methods, you should have a gas model, which follow :doc:`Gas ORM standard model convention <convention>`. Then, you can start using it either by instantiate new Gas object or by using factory interface. ::

	// FINDER
	$all = Gas::factory('user')->all();
	$user = new User;
	$some_user = $user->find_by_username('foo');
	$first_user = $user->first();

	// WRITE (INSERT, UPDATE, DELETE) AND VALIDATION
	$new_user = new User;
	$new_user->fill($_POST)->save(TRUE);

	// RELATIONSHIP AND EAGER LOADING
	$some_user = Gas::factory('user')->with('wife', 'kid', 'job')->find(1);
	$some_user_wife = $some_user->wife;

Thats how you will use Gas ORM, in your application.

Gas ORM Features
++++++++++++++++

- Supported databases : cubrid, mssql, mysql, oci8, odbc, postgre, sqlite, sqlsrv.
- Support multiple database connection.
- Support modular models directories.
- Multiple relationship (has_one, has_many, belongs_to, has_and_belongs_to) with custom relationship setting (through, foreign_key, foreign_table, self)
- Self-referential and adjacency column/data (hierarchical data).
- Various finder method (can chained with most of CI AR) and aggregates.
- Validation and auto-mapping input collection, with minimal setup.
- Hooks points, to control over your model.
- Extensions, to share your common function/library across your model.
- Transaction, cache, and other CI AR goodness.
