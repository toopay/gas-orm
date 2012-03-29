.. Gas ORM documentation [CRUD]

CRUD 
====

The hard part (was it?) is over. Setting up your model, field validation rules and relationship properties, once and for all (unless somehow, in the future, our recent schema not fit anymore). 

Now its time to use it in action. 

Creating record(s)
++++++++++++++++++++++

How to create a record? In a standard way, you will wrote it as bellow ::

	$user = new Model\User();
	$user->name = 'Mr Foo';
	$user->email = 'foo@world.com';
	$user->username = 'foo';
	$user->save();

	
There is **create** method, for your convinient, especially when you are receive a **$_POST** data. ::

	Model\User::make($_POST)->save();

As default, Gas not enforce you to run validation process, before create a record. But if you already set up your field validation rules, passing **TRUE** within **save** method will do the job. ::

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

Notice that you could immediately using the last created resource, by using **last_created** method. If you want to use the last inserted id you can do so by inherit CI query builder by using **insert_id** method.

Now, how about working with relationship within save operation?

Let say you have a database with schema as emulated on :doc:`Relationship section <relationship>` and you have all corresponding model set up. You want to create a **job** entity record, then linking it to some user. You can do so by writing : ::

	$job = Model\Job::make(array(
		'name' => 'teacher',
		'description' => 'We make people smarter',
	));

	$job->related->set('entities.user.pivot', array(
		'user_id' => 1,
	));

	$job->save();

You can do this **many-to-many** insertion vice versa. Since Gas ORM require PHP 5.3, which mean there are **anonymous function** available, you can also passing a closure function as the value. This usefull if you are having uncertain situation. For example, like above case, you want to create a job entity and linking it to some user. But, instead linking it to existed user, you want to also create the user entity as well. All you need to do, is write something like : ::

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

If you want to insert some one-to-many record, you can set the **entities.relationship.child** within your Gas instance related data.

Reading record(s)
+++++++++++++++++++++

You already meet the :doc:`Finder method <finder>`, also you already specify each of your models relationship. In :doc:`Finder section <finder>` you already know, how to fetch either one record or a set of records. Now, how to access its relationship? As easy as : ::
	
	$someuser = Model\User::find(1);
	echo 'User 1 name is '.$someuser->name.' and his wife name is '.$someuser->wife()->name;

As you can see, you can directly get the user's wife, by accessing **wife** method from user instance. This method is refer to **relationship name** , not a table name. So even the real table was **wifes** or **wf** or whateer it is, only **related relationship to model's class name** that you specicy in user's relations properties which really matter.

For **one-to-many** relationship, you can iterate the child nodes, something like : ::

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
	
This applied to **many-to-many** relationship as well.

Gas support eager loading, so you can improve your relationship queries, especially when you retrieve a child node from a set of parent instance. Eager loading works for all **relations** properties that you defined. You can eager loading any types of relationship tables, using **with()** method.

In short, instead doing this : ::

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

Above, you actually will doing **SELECT** as many as your user counts, and this bad for either you or your mother health, especially for your grandfather. Eager loading alleviate this N+1 problem, and if you used it wisely, will tremendously increase your application performance (both for execution time and memory usage). How to do eager load my related model? ::


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

This section is actually much same, with **create record** section, unless instead doing INSERT, we are about UPDATE a record(s) : ::
	
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

The beautiful part of using Gas ORM, is you can also update your model relation as well, look at this example : ::

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

When you working with relational entity, cascading delete are supported, this mean you could **unlink** (for example) user entity from role entity, without deleting both user record and role record. Instead, you could only delete some record on the pivot table, which linking the record, eg : ::

	$someuser = Model\User::with('role')->find(1);
	$someuser->delete();

This will only delete the record within the pivot table, since above relationship was many-to-many.