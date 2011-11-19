.. Gas ORM documentation [utility]

Utility
=======

Here you may found some usefull method, to use within your models.


truncate()
++++++++++

This method is actually inherit from CI AR, so it should be straight forward. ::

	Gas::factory('user')->truncate();

It will drop all of your table records.

count_all()
+++++++++++

You may often need to just retrieve the table total records, use this. This method is actually inherit from CI AR, so it should be straight forward. ::

	Gas::factory('user')->count_all();

It return total records from your table.

last_sql() and all_sql()
++++++++++++++++++++++++

This also inherit from CI AR, so it should be straight forward. ::

	$user = new User;

	$someusers = $user->group_by('email')->all();

	$last_sql = Gas::factory('user')->last_sql();

	var_dump($last_sql);

	$someusers = $user->like('email', 'yahoo.com')->all();

	$someusers = $user->left_join_phone('phone.user_id = user.id')->all();

	$someusers = $user->left_outer_join_sandals('sandals.id = user.sandal_id')->all();

	$all_sql = Gas::factory('user')->all_sql();

	var_dump($all_sql);

This often usefull while you debugging, benchmarking or profiling your application.

Transaction
+++++++++++

Gas give extra intellegent into your transaction. Transaction feature was inherit from CI AR, so it should be straight forward. ::

	$user = Gas::factory('user');

	$user->trans_start();

	for ($i = 5;$i < 10;$i++)
	{
    	$user->query('INSERT INTO `'.$this->model().'` (`id`, `name`) VALUES ('.$i.', \'user_'.$i.'\')');
	}

	for ($i = 5;$i < 10;$i++)
	{
	    $new_user = Gas::factory('user')->find($i);

	    $new_user->fill(array('name' => 'person_'.$i))->save();
	}


	if ($user->trans_status() === FALSE)
	{
	    $user->trans_rollback();
	}
	else
	{
	    $user->trans_commit();
	}

	$user9 = Gas::factory('user')->find(9);

	echo $user9->name;

From above example, if everything ok, it should echoing 'person_9'.

Multiple Database
+++++++++++++++++

Gas support multiple connection, so you can switch to other database by default, Gas will use 'default' group, specified in your **database.php** under **application/config** folder. Switching to other database just need a dsn string or group connection name (which you specified in **database.php** ). ::

		Gas::connect('mysql://root:password@localhost/my_db');

		var_dump(Gas::factory('user')->find(1));

		Gas::connect('develop');

		var_dump(Gas::factory('user')->find(1));


.. note:: Most of the time, you didnt need to load/autoload database when working with Gas, Gas will automatically connect to your **default** connection in **database.php** configuration.