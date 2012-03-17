.. Gas ORM documentation [quickstart]

Quick Start
===========

Gas ORM is a third-party package library, so you will need to load it first, or for for convenience you can autoload it on **autoload.php** located under **aplication/config** folder. ::

	$autoload['libraries'] = array('gas');

Otherwise, you would need to load Gas library manually.

Gas ORM at a Glance
+++++++++++++++++++

Before start using any of Gas available methods, you should have a gas model, which follow :doc:`Gas ORM standard model convention <convention>`. Then, you can start using it like bellow. ::

	// FINDER
	$all_users = Model\User::all();
	$some_user = Model\User::find_by_username('foo');
	$first_user = Model\User::first();

	// WRITE (INSERT, UPDATE, DELETE) AND VALIDATION
	$new_user = Model\User::make($_POST);
	$new_user->save(TRUE);

	// RELATIONSHIP AND EAGER LOADING
	$some_user_wife = Model\User::find(1)->wife();
	$users = Model\User::with('wife', 'kid', 'job')->all();

Thats how you will use Gas ORM, in your application.

Gas ORM Features
++++++++++++++++

- Supported databases : cubrid, mssql, mysql, oci8, odbc, postgre, sqlite, sqlsrv. (including PDO, if you keep sync with CI repo)
- Support multiple database connection.
- Support multiple relationships.
- Support composite keys (for key that define relationship).
- Auto-create models from database tables and vice versa, and auto-synchronize models-tables by creating migrations file.
- Per-request caching.
- Self-referential and adjacency column/data (hierarchical data).
- Eager Loading, to maximize your relationship queries (for performance manner).
- Various finder method (can chained with most of CI AR) and aggregates.
- Validation and auto-mapping input collection, with minimal setup.
- Hooks points, to control over your model.
- Extensions, to share your common function/library across your model.
- Transaction, and other CI AR goodness.
- Included phpunit test suite, to ensure most of API consistency.
