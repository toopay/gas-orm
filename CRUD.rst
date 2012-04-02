.. Gas ORM documentation [CRUD]

CRUD 
====

The hard part (was it?) is over! Setting up your model, field validation rules and relationship properties once and for all (unless somehow, in the future our schema doesn't fit anymore). 

Now its time to use it in action. 

Creating record(s)
++++++++++++++++++++++

How do you create a record? The standard method for creating a record is : ::

	$user = new Model\User();
	$user->name = 'Mr Foo';
	$user->email = 'foo@world.com';
	$user->username = 'foo';
	$user->save();

	
There is also a **make** method for your convenience, especially when you are receiving **$_POST** data. ::

	Model\User::make($_POST)->save();

By default, Gas does not enforce you to run validation process before creating a record. But if you have already set up your field validation rules in the model, passing **TRUE** within the **save** method will do the job. ::

	Model\User::make($_POST)->save(TRUE);

What if you want to create a record, but not from **$_POST** data? . Lets throw a full scenario here : ::

	$data = array(
		'name' => 'Mrs Bar',
		'email' => 'bar@otherworld.com',
		'username' => 'bar',
	);

	$new_user = Model\User::make($data);

	if ( ! $new_user->save(TRUE))
	{
		echo 'The raw errors were : ';
		print_r($new_user->errors);
	}
	else
	{
		echo 'New user successfully created. And her id is '.Model\User::last_created()->id;
	}

Notice that you could immediately using the last created resource, by using **last_created** method. If you want to use the last inserted id you can do so by inheriting the CI query builder and using the **insert_id** method.

Now, how about working with relationships within a save operation?

Let say you have a database with schema as shown in the :doc:`Relationship section <relationship>` and you have all the corresponding models set up. You want to create a **job** entity record and link it to some user. You can do so by writing : ::

	$job = Model\Job::make(array(
		'name' => 'teacher',
		'description' => 'We make people smarter',
	));

	$job->related->set('entities.user.pivot', array(
		'user_id' => 1,
	));

	$job->save();

You can do this **many-to-many** insertion as well. Gas ORM requires PHP 5.3, which means there are **anonymous functions** available.  This means you can pass a closure function as the value, which can be useful in some situations. For example in the above case, you want to create a job entity and link it to some user. Instead of linking it to an existing user, you want to also create the user entity as well. All you need to do is write something like : ::

	$job = Model\Job::make(array(
		'name' => 'teacher',
		'description' => 'We make people smarter',
	));

	$job->related->set('entities.user.pivot', array(
		'user_id' => function() {
			$new_user = \Model\User::make(array(
				'name' => 'Bob',
				'email' => 'bob@school.com',
				'username' => 'fifth_grade_teacher',
			));

			$new_user->save();

			return \Model\User::insert_id();
		},
	));

	$job->save();

If you want to insert a one-to-many record, you can set the **entities.relationship.child** within your Gas instance related data.

Reading record(s)
+++++++++++++++++++++

You have already meet the :doc:`Finder method <finder>` and have already specified each of your model relationships. In :doc:`Finder section <finder>` you already know how to fetch either one record or a set of records. Now, how to access its relationships? As easy as : ::
	
	$someuser = Model\User::find(1);
	echo 'User 1 name is '.$someuser->name.' and his wife name is '.$someuser->wife()->name;

As you can see you can directly get the user's wife by accessing the **wife** method from the user instance. This method refers to the **relationship name** , not the table name. So even if the real table was **wifes** or **wf** or whatever it is, only the **related relationship to model's class name** that you specify in the user model's relations properties really matters.

For **one-to-many** relationships, you can iterate the child nodes as follows : ::

	$someuser = Model\User::find(1);

	echo 'User 1 name is '.$someuser->name.' and he seems have several kids, with these details :';

	if ( ! empty($someuser->kid()))
	{
		foreach ($someuser->kid() as $kid)
		{
			echo 'Kid '.$kid->id.' name is '.$kid->name;
			echo "\n";
		}
	}
	
This applies to **many-to-many** relationships as well.

Gas supports eager loading so you can improve your relationship queries especially when you retrieve a child node from a set of parent instances. Eager loading works for all **relations** properties that you define. You can eager load any types of relationship tables, using the **with** method.

The code below is one way of finding all of the children for a set of users.  It performs a **SELECT** statement for each user, which can be bad for either you or your mother's health, especially for your grandfather : ::

	$all_users = Model\user::all(); 

	foreach ($all_users as $some_user)
	{

		echo 'User 1 name is '.$someuser->name.' and he seems have several kids, with these details :';

		foreach ($someuser->kid() as $kid)
		{
			echo 'Kid '.$kid->id.' name is '.$kid->name;

			echo "\n";
		}

	}

Eager loading alleviates this N+1 problem and if you used it wisely will tremendously increase your application performance (both for execution time and memory usage). How do you eager load a related model? ::


	$all_users = Model\User::with('kid')->all(); 

	foreach ($all_users as $some_user)
	{
		echo 'User 1 name is '.$someuser->name.' and he seems have several kids, with these details :';

		foreach ($someuser->kid() as $kid)
		{
			echo 'Kid '.$kid->id.' name is '.$kid->name;
			echo "\n";
		}

	}

Now you just only doing two queries, one to **SELECT** all users and one to **SELECT** all kid with **WHERE IN** clause and corresponding user's condition.

Updating record(s)
++++++++++++++++++++++

This section is actually much the same as the **create record** section, however instead of performing an INSERT we are about to UPDATE a record(s) : ::
	
	$recent_user = Model\User::find(1);
	$recent_user->name = 'New name';

	if ( ! $recent_user->save(TRUE))
	{
		echo 'Something wrong';
	}
	else
	{
		echo 'User 1 successfully updated.';
	}

The beautiful part of using Gas ORM is you can also update your model relationships as well, look at this example : ::

	$someuser = Model\User::find(1);

	$related_wife = $someuser->wife();
	$related_wife->hair_colour = 'black';
	$related_wife->save();

Its remove all hassle and will seriously reduce your development time, and this is indeed good for your health.

Deleting record(s)
++++++++++++++++++

To delete a single record : ::
	
	$someuser = Model\User::find(1);
	$someuser->delete();

Or you can explicitly specify the **id** : ::

	Model\User::delete(1);

Passing **ids** is accepted as well : ::

	Model\User::delete(1, 2, 3, 4, 5, 1000);

When you working with relational entity, cascading delete are supported, this mean you could **unlink** (for example) user entity from role entity, without deleting both user record and role record. Instead you could only delete some record on the pivot table, which linking the record, eg : ::

	$someuser = Model\User::with('role')->find(1);
	$someuser->delete();

This will only delete the record within the pivot table, since above relationship was many-to-many.
