.. Gas ORM documentation [quickstart]

Quick Start
===========

Gas ORM is a third-party package library, so you will need to load it first. For convenience you can autoload it in the **autoload.php** file located in the **aplication/config** folder. ::

	$autoload['libraries'] = array('gas');

Otherwise, you would need to load Gas library manually before it can be used.

Gas ORM at a Glance
+++++++++++++++++++

Before starting to use any of the available Gas methods you should define a gas model.  This should follow :doc:`Gas ORM standard model convention <convention>`. Then, you can start using it like below. ::

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

Thats how you will use Gas ORM in your application.

Gas ORM Features
++++++++++++++++

- Supported databases : cubrid, mssql, mysql, oci8, odbc, postgre, sqlite, sqlsrv. (including PDO, if you keep sync with CI repo)
- Support multiple database connections.
- Support multiple relationships.
- Support composite keys (for key that define relationships).
- Auto-create models from database tables and vice versa.
- Auto-synchronize models-tables by creating migrations file.
- Per-request caching.
- Self-referential and adjacency column/data (hierarchical data).
- Eager Loading to maximize your relationship queries (for performance manner).
- Various finder methods (can chained with most of CI AR) and aggregates.
- Validation and auto-mapping input collection with minimal setup.
- Hooks points to control over your model.
- Extensions to share your common function/library across your model.
- Transactions and other CI AR goodness.
- Included phpunit test suite to ensure most of API consistency.
