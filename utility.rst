.. Gas ORM documentation [utility]

Utility
=======

Here you may found some useful methods to use within your models.


truncate()
++++++++++

This method is inherited from CI AR, so it should be straight forward. ::

	Model\User::truncate();

It will drop all of your table records.

reports()
+++++++++

Sometimes you want to monitor the changes that have been made in a single HTTP request.  You can do so by using this method. ::

	// Place this in the very last of some controller method
	var_dump(Model\User::reports());

That will give you information about the state of all corresponding resources in an array for user models.

Multiple Database
+++++++++++++++++

Gas supports multiple connections, so you can switch to another database. By default, Gas will use the 'default' group specified in your **database.php** file under the **application/config** folder. Switching to other database just needs a dsn string or group connection name (which you specified in **database.php** ). ::

		Gas\Core::connect('mysql://root:password@localhost/my_db');

		var_dump(Model\User::find(1));

		Gas\Core::connect('develop');

		var_dump(Model\User::find(1));


.. note:: Most of the time, you don't need to load/autoload the database library when working with Gas as Gas automatically connects to your **default** connection defined in the **database.php** configuration.
