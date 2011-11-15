.. Gas ORM documentation [finder]

Finder
======

Gas provide a set of methods by which you can find records such as: via primary key, dynamic field name finders. It has the ability to fetch all the records in a table with a simple call, or you can make use of options like order, limit, select, and group (which you may used to be, when you working on your native CI model using CI AR).

There are mainly two groups of finders you will be working with: a single record result and multiple records result. Sometimes there will be little transparency for the method calls, meaning you may use the same method to get either multiple records, but you will pass an option to that method to signify which type of result you will fetch.

all()
++++++

You can use **all** to fetch all record within your table. Let say we want to scan our table to dump out all user's name : ::

	$users = Gas::factory('user')->all();

	foreach ($users as $user)
	{
		echo 'User '.$user->id.' name is '.$user->name;

		echo "\n";
	}

All will always return an array of object (in this case, user instance).

find()
++++++

You can use **find** to fetch a record(s) based by your primary key. ::

	$user_1 = Gas::factory('user')->find(1);

Above will returning a user's record where **id** (or your defined primary key) equivalent with 1.

Using factory method, give you flexibility. But if you are working with a set of finder method, you may want to reduce keystroke, by instantiate once, then use it several times. ::

	$user = new User;

	$user_1 = $user->find(1);

	echo 'User 1 name is '.$user_1->name;

	echo "\n";

	$user_2 = $user->find(2);

	echo 'User 2 name is '.$user_2->name;

	echo "\n";

Normally, if you use **find** method and passed a single id as above, Gas will return an user instance at a time.

To find several **ids**, you can do that by : ::

	$admin_users = Gas::factory('user')->find(1, 2, 3);

	foreach ($admin_users as $admin)
	{
		echo 'User '.$admin->id.' is an admin, and his name is '.$admin->name;

		echo "\n";
	}

Notice when you passing several ids, Gas will return an array of object instead.

find_by_collumn()
+++++++++++++++++

You can use **find_by_collumn** to fetch a record(s) based by some collumn. For example, your user table have : id, name, username, email and so on. Then you can use those column name like bellow ::

	$active_users = Gas::factory('user')->find_by_active('1');

	foreach ($active_users as $active_user)
	{
		echo 'User '.$active_user->id.' is active, and his name is '.$active_user->name;

		echo "\n";
	}

By default, **find_by_column** will returning an array of object. While you just need to match and returned one record, as an instance, passing second parameter (that is **limit**) to 1 will do that. ::

	$user = new User;

	$moderators = $user->find_by_role('moderator', 3, 1);

	foreach ($moderators as $moderator)
	{
		echo 'User '.$moderator_user->id.' is a moderator, and her name is '.$moderator->name;

		echo "\n";
	}

	$me = $user->find_by_role('administrator', 1);

	echo 'My name is '.$me->name;

You can passing **offset** as third parameter as well. Notice that unless you passing 1 as second parameter, **find_by_column** will returning an array of object/instance.

find_where()
+++++++++++++++++

If **find_by_collumn** doesn't enough, you can use **find_where** to fetch a set of record(s) based by several collumns. For example ::

	$bad_condition = array('behaviour' => 'bad', 'attitude' => 'bad');

	$bad_user = Gas::factory('user')->find_where($bad_condition, 20, 10);

	foreach ($active_users as $active_user)
	{
		echo 'User '.$active_user->id.' is active, and his name is '.$active_user->name;

		echo "\n";
	}

You can passing **limit** as second parameter and **offset** as third parameter as well. Notice that unless you passing 1 as second parameter, **find_where** will returning an array of object/instance.

first() and last()
++++++++++++++++++

Will return an instance with **first** or **last** of your primary key (default to **id**). You can passing a collumn name as well.

max(), min(), sum() and avg()
+++++++++++++++++++++++++++++

Will return an instance with **max**, **min**, **sum** or **avg** of your primary key (default to **id**). You can passing a collumn name as well. If you need to aliasing collumn, pass it as second argument.

Chaining Finder with CI AR
++++++++++++++++++++++++++

You will soon realize, that when using Gas ORM, you have not to lose all of your habbit to chaining several method together. Gas ORM even adding some additional shorthand to make wrote your syntax easier. Here some basic implementation examples : ::

	$someusers = Gas::factory('user')->group_by('email')->all();

	$someusers = Gas::factory('user')->like('email', 'yahoo.com')->all();

	$someusers = Gas::factory('user')->left_join_phone('phone.user_id = user.id')->all();

	$someusers = Gas::factory('user')->left_outer_join_sandals('sandals.id = user.sandal_id')->all();







