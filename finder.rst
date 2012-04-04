.. Gas ORM documentation [finder]

Finder
======

Gas provides a set of methods by which you can find records including via primary key and dynamic field name finders. It has the ability to fetch all the records in a table with a simple call, or you can make use of options like order, limit, select, and group (which you may already have used when working on your native CI models using CI AR).

There are two groups of finders you will be working with: a single record result and multiple records result. For some methods you can pass a variable to that method to signify which type of result you will fetch.

all()
++++++

You can use **all** to fetch all records within your table. Let say we want to scan our table to dump out all the user's names : ::

	$users = Model\user::all();

	foreach ($users as $user)
	{
		echo 'User '.$user->id.' name is '.$user->name;
		echo "\n";
	}

All **will always return an array of object** (in this case, user instance) if the record(s) exists, and an empty array if there are no records at all. 

However, if you passed **FALSE** as a parameter, it will return an object if there is only one record, and NULL if there is no records at all. See the difference here : ::
	
	$users = Model\user::all(); 		// Returning an array, regardless whatever record(s) found
	$users = Model\user::all(FALSE); 	// Returning an array if contain more than one record, an object on one record, NULL if fails

This behaviour will help, if you know exactly how your records a held in your database.

find()
++++++

You can use **find** to fetch a record(s) based on your primary key. ::

	$user_1 = Model\User::find(1);
	echo $user_1->name;

The code above returns a user's record where **id** (or your defined primary key) is equivalent with 1, and echo the name.

Each Gas instance holds a **Gas\\Data** instance as a record holder. This allow you to do useful things, for example setting the default value of some property which may or may not be set : ::

	$user = Model\User::find(1);
	echo $user->record->get('data.biodata', 'No biodata yet');

In above example, if the **biodata** is NULL, then the second parameter will be returned instead.

Normally, if you use **find** method and passed a single id as above, Gas will return one user instance at a time.  However to find several **ids**, you can do that by : ::

	$admin_users = Model\User::find(1, 2, 3);

	foreach ($admin_users as $admin)
	{
		echo 'User '.$admin->id.' is an admin, and his name is '.$admin->name;
		echo "\n";
	}

Notice when you passing several ids, Gas will return an array of object instead. If there are no records found, NULL will returned.

find_by_column()
++++++++++++++++

You can use **find_by_column** to fetch a record(s) based by some column. For example your user table has : id, name, username, email columns and so on. Then you can use those column name like below ::

	$active_users = Model\User::find_by_active('1');

	foreach ($active_users as $active_user)
	{
		echo 'User '.$active_user->id.' is active, and his name is '.$active_user->name;
		echo "\n";
	}

By default, **find_by_column** will return an array of object (in this case, user instance) if the record(s) exists, and empty array if there are no records at all. When you just need to match and return one or a specific number of record(s), you can do so by chaining this method with **limit** method from CI query builder, eg ::

	$moderators = Model\User::limit(3)->find_by_role('moderator');

	foreach ($moderators as $moderator)
	{
		echo 'User '.$moderator_user->id.' is a moderator, and her name is '.$moderator->name;
		echo "\n";
	}

Passing **FALSE** as a second parameter matches the behaviour found with the **all** method.  This will force Gas to return the records as is - either an array of objects for many results, a single object for one result, or null if there were no results. This useful for the following situation : ::

	// You want to find a record, and immediately use that
	$me = Model\User::limit(1)->find_by_role('administrator', FALSE);
	echo 'My name is '.$me->name;


first() and last()
++++++++++++++++++

Will return an instance with the **first** or **last** record of your primary key (default to **id**). You can pass a column name as well. ::

	$res = Model\User::first();
	echo 'The first user id is ' . $res->id;
	
Where your primary key is **id**, this is the same as: ::

	$res = Model\User::first('id');
	echo 'The first user id is ' . $res->id;


max(), min(), sum() and avg()
+++++++++++++++++++++++++++++

Will return an instance with **max**, **min**, **sum** or **avg** of your primary key (default to **id**). You can passing a column name as well. If you need to aliasing column, pass it as second argument : ::

	$res = Model\User::max('id','top_user');
	echo 'The highest user id is ' . $res->top_user;


Chaining Finder with CI AR
++++++++++++++++++++++++++

You will soon realize, that when using Gas ORM, you have not to lose all of your habbit to chaining several method together. Almost all CI query builder method are chainable with Gas ORM method(s). Here some basic implementation examples : ::

	$someusers = Model\User::group_by('email')->all();
	$someusers = Model\User::like('email', 'yahoo.com')->all();
