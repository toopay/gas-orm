.. Gas ORM documentation [CRUD]

CRUD 
====

The hard part (was it?) is over. Setting up your model, field validation rules and relationship properties, once and for all (unless somehow, in the future, our recent schema not fit anymore). 

Now its time to use it in action. 

Creating record(s)
++++++++++++++++++++++

How to create a record? In a standard way, you will wrote it as bellow ::

	$user = new User;

	$user->name = 'Mr Foo';

	$user->email = 'foo@world.com';

	$user->username = 'foo';

	$user->save();

	
There is **fill** method, for your convinient, especially when you are receive a **$_POST** data. ::

	Gas::factory('user')->fill($_POST)->save();

As default, Gas not enforce you to run validation process, before create a record. But if you already set up your field validation rules, passing **TRUE** within **save** method will do the job. ::

	Gas::factory('user')->fill($_POST)->save(TRUE);

What if you want to create a record, but not from **$_POST** data? Well, natively, its impossible, because CI validation class only invoked by **$_POST** data. But with Gas ORM, it is as easy as passing **TRUE** as second parameter within **fill** method. Lets throw a full scenario here : ::

	$data = array(
		
		'name' => 'Mrs Bar',

		'email' => 'bar@otherworld.com',

		'username' => 'bar',
	);

	$new_user = new User;

	$new_user->fill($data, TRUE);

	if ( ! $new_user->save(TRUE))
	{
		echo $new_user->errors('<p class="error">', '</p>');
	}
	else
	{
		echo 'New user successfully created. And her id is '.$new_user->last_id();
	}

Notice that **fill** will auto mapped our corresponding field rules with our data collection, and because we pass **TRUE** as second parameter, it will treated the data as **$_POST** data, so CI validation will be invoked. There are several :doc:`Hooks point <callbacks>` which you may use, to fully control the creating process within your model.

Reading record(s)
+++++++++++++++++++++

You already meet the :doc:`Finder method <finder>`, also you already specify each of your models relationship. In :doc:`Finder section <finder>` you already know, how to fetch either one record or a set of records. Now, how to access its relationship? As easy as : ::
	
	$someuser = Gas::factory('user')->find(1);

	echo 'User 1 name is '.$someuser->name.' and his wife name is '.$someuser->wife->name;

For **one-to-many** relationship, you can iterate the child nodes, something like : ::

	$someuser = Gas::factory('user')->find(1);

	echo 'User 1 name is '.$someuser->name.' and he seems have several kids, with these details :';

	foreach ($someuser->kid as $kid)
	{
		echo 'Kid '.$kid->id.' name is '.$kid->name;

		echo "\n";
	}

This applied to **many-to-many** relationship as well.

Gas support eager loading, so you can improve your relationship queries, especially when you retrieve a child node from a set of parent instance. Eager loading works for all **relations** properties that you defined. You can eager loading any types of relationship tables, using **with()** method.

In short, instead doing this : ::

	$all_users = Gas::factory('user')->all(); 

	foreach ($all_users as $some_user)
	{

		echo 'User 1 name is '.$someuser->name.' and he seems have several kids, with these details :';

		foreach ($someuser->kid as $kid)
		{
			echo 'Kid '.$kid->id.' name is '.$kid->name;

			echo "\n";
		}

	}

Above, you actually will doing **SELECT** as many as your user counts, and this bad for either you or your mother health, especially for your grandfather. Eager loading alleviate this N+1 problem, and if you used it wisely, will tremendously increase your application performance (both for execution time and memory usage). How to do eager load my related model? ::


	$all_users = Gas::factory('user')->with('kid')->all(); 

	foreach ($all_users as $some_user)
	{

		echo 'User 1 name is '.$someuser->name.' and he seems have several kids, with these details :';

		foreach ($someuser->kid as $kid)
		{
			echo 'Kid '.$kid->id.' name is '.$kid->name;

			echo "\n";
		}

	}

Now you just only doing two queries, one to **SELECT** all users and one to **SELECT** all kid with **WHERE IN** clause and corresponding user's condition.

Updating record(s)
++++++++++++++++++++++

This section is actually much same, with **create record** section, unless instead doing INSERT, we are about UPDATE a record(s) : ::
	
	$data = array(
		
		'name' => 'New Name',

		'email' => 'newbar@otherworld.com',
	);

	$recent_user = Gas::factory('user')->find(1);

	$recent_user->fill($data, TRUE);

	$recent_user->time_update = time();

	if ( ! $recent_user->save(TRUE))
	{
		echo $recent_user->errors('<p class="error">', '</p>');
	}
	else
	{
		echo 'User 1 successfully updated.';
	}

The beautiful part of using Gas ORM, is you can also update your model relation as well, look at this example : ::

	$someuser = Gas::factory('user')->find(1);

	$related_wife = $someuser->wife;

	$related_wife->hair_colour = 'black';

	$related_wife->save();

Its remove all hassle and will seriously reduce your development time, and this is indeed good for your health.

Deleting record(s)
++++++++++++++++++

To delete a single record : ::
	
	$someuser = Gas::factory('user')->find(1);

	$someuser->delete();

Or you can explicitly specify the **id** : ::

	Gas::factory('user')->delete(1);

Passing **ids** is accepted as well : ::

	Gas::factory('user')->delete(1, 2, 3, 4, 5, 1000);

One thing to notice, that all writes operation (**INSERT**, **UPDATE** and **DELETE**) will always return **affected_rows** if sucess and **FALSE** if operation fail.