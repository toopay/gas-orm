.. Gas ORM documentation [utility]

Utility
=======

Here you may found some usefull method, to use within your models.


truncate()
++++++++++

This method is actually inherit from CI AR, so it should be straight forward. ::

	Model\User::truncate();

It will drop all of your table records.

reports()
+++++++++

Sometime, you want to monitoring what changes has been done in a single HTTP request, you can do so by using this method. ::

	// Place this in the very last of some controller method
	var_dump(Model\User::reports());

That will give you information about all corresponding resources state in an array for user models.

Multiple Database
+++++++++++++++++

Gas support multiple connection, so you can switch to other database by default, Gas will use 'default' group, specified in your **database.php** under **application/config** folder. Switching to other database just need a dsn string or group connection name (which you specified in **database.php** ). ::

		Gas\Core::connect('mysql://root:password@localhost/my_db');

		var_dump(Model\User::find(1));

		Gas\Core::connect('develop');

		var_dump(Model\User::find(1));


.. note:: Most of the time, you didnt need to load/autoload database when working with Gas, Gas will automatically connect to your **default** connection in **database.php** configuration.