.. Gas ORM documentation [finder]

Finder
======

Gas provide a set of methods by which you can find records such as: via primary key, dynamic field name finders. It has the ability to fetch all the records in a table with a simple call, or you can make use of options like order, limit, select, and group (which you may used to be, when you working on your native CI model using CI AR).

There are mainly two groups of finders you will be working with: a single record result and multiple records result. Sometimes there will be little transparency for the method calls, meaning you may use the same method to get either multiple records, but you will pass an option to that method to signify which type of result you will fetch.

all()
++++++

You can use **all** to fetch all record within your table. Let say we want to scan our table to dump out all user's name : ::

	$users = Model\user::all();

	foreach ($users as $user)
	{
		echo 'User '.$user->id.' name is '.$user->name;
		echo "\n";
	}

All **will always return an array of object** (in this case, user instance) if the record(s) exists, and empty array if there is no records at all. 

However, if you passed **FALSE** as parameter, it will return an object if there is only one record, and NULL if there is no records at all. See the different here : ::
	
	$users = Model\user::all(); 		// Returning an array, regardless whatever record(s) found
	$users = Model\user::all(FALSE); 	// Returning an array if contain more than one record, an object on one record, NULL if fails

This behaviour will help, if you know exactly how your records condition in your database.

find()
++++++

You can use **find** to fetch a record(s) based by your primary key. ::

	$user_1 = Model\User::find(1);
	echo $user_1->name;

Above will returning a user's record where **id** (or your defined primary key) equivalent with 1, and echo the name.

Each Gas instance, hold a **Gas\\Data** instance as a record holder. This allow you to do useful thing, for example to set the default value of some property which you unsure : ::

	$user = Model\User::find(1);
	echo $user->record->get('data.biodata', 'No biodata yet');

In above example, if the **biodata** is NULL, then the second parameter will apply.

Normally, if you use **find** method and passed a single id as above, Gas will return an user instance at a time.

To find several **ids**, you can do that by : ::

	$admin_users = Model\User::find(1, 2, 3);

	foreach ($admin_users as $admin)
	{
		echo 'User '.$admin->id.' is an admin, and his name is '.$admin->name;
		echo "\n";
	}

Notice when you passing several ids, Gas will return an array of object instead. If there is no records, NULL will returned.

find_by_collumn()
+++++++++++++++++

You can use **find_by_collumn** to fetch a record(s) based by some collumn. For example, your user table have : id, name, username, email and so on. Then you can use those column name like bellow ::

	$active_users = Model\User::find_by_active('1');

	foreach ($active_users as $active_user)
	{
		echo 'User '.$active_user->id.' is active, and his name is '.$active_user->name;
		echo "\n";
	}

By default, **find_by_column** will return an array of object (in this case, user instance) if the record(s) exists, and empty array if there is no records at all. When you just need to match and returned one or specific number of record(s), you can do so by chaining this method with **limit** method from CI query builder, eg ::

	$moderators = Model\User::limit(3)->find_by_role('moderator');

	foreach ($moderators as $moderator)
	{
		echo 'User '.$moderator_user->id.' is a moderator, and her name is '.$moderator->name;
		echo "\n";
	}

However, passing **FALSE** as second parameter will force Gas to return the records as is, as you can also found at **all** method. This useful for following situation : ::

	// You want to find a record, and immediately use that
	$me = Model\User::limit(1)->find_by_role('administrator', FALSE);
	echo 'My name is '.$me->name;

first() and last()
++++++++++++++++++

Will return an instance with **first** or **last** of your primary key (default to **id**). You can passing a collumn name as well.

max(), min(), sum() and avg()
+++++++++++++++++++++++++++++

Will return an instance with **max**, **min**, **sum** or **avg** of your primary key (default to **id**). You can passing a collumn name as well. If you need to aliasing collumn, pass it as second argument.

Chaining Finder with CI AR
++++++++++++++++++++++++++

You will soon realize, that when using Gas ORM, you have not to lose all of your habbit to chaining several method together. Almost all CI query builder method are chainable with Gas ORM method(s). Here some basic implementation examples : ::

	$someusers = Model\User::group_by('email')->all();
	$someusers = Model\User::like('email', 'yahoo.com')->all();