.. Gas ORM documentation [relationship]

Relationship
============

Gas supports three types of table relationships - **one-to-one**, **one-to-many** and **many-to-many**. Use the **relations** properties array in your model to define relationships.

Setting up your model relationships is a one-time set-up, unless in the future you need to change your table schema.

Define Relationship
+++++++++++++++++++

To define some entity association, you need to perform one of the available relationship methods : 

+---------------------+-------------------------------------------------------------------------------+
| Relationship option | Description                                                                   |
+=====================+===============================================================================+
| **has_one**         | for one-to-one relationship                                                   |
+---------------------+-------------------------------------------------------------------------------+
| **has_many**        | for one-to-many and many-to-many relationship                                 |
+---------------------+-------------------------------------------------------------------------------+
| **belongs_to**      | for representing the relationships above in the target model                  |
+---------------------+-------------------------------------------------------------------------------+

All relationship methods accept two parameters. The first parameter contains the 'path' and the second parameter contains optional additional queries to run, eg : ::

	'wife' => ORM::has_one('\\Model\\Wife'),
	'job'  => ORM::has_many('\\Model\\Job\\User => \\Model\\Job', array('select:id,name')),

If the 'path' contains more than one model, you need to add a separator which explain the ownership status of each models. For example in the above **job** relationship, the separator was **=>**. That simply mean **Model\\Job\\User** (which hold the job_user table) is OWNED (or belongs to) both **Model\\User** and **Model\\Job**.

The second parameter contains an array of additional query options. Available tags for the second parameter are : 

+---------------------+-------------------------------------------------------------------------------+
| Additional option   | Syntax example                                                                |
+=====================+===============================================================================+
| **select**          | 'kid' => ORM::has_many('\\Model\\Kid', array('select:id,name'))               |
+---------------------+-------------------------------------------------------------------------------+
| **limit**           | 'kid' => ORM::has_many('\\Model\\Kid', array('select:id,name', limit:2))      |
+---------------------+-------------------------------------------------------------------------------+
| **order_by**        | 'kid' => ORM::has_many('\\Model\\Kid', array('order_by:name[asc]'))           |
+---------------------+-------------------------------------------------------------------------------+

You can have one, some, or all of those option(s) in each of your relationship definition.

has_one
+++++++

For example, let us say we have two tables with a **one-to-one** relationship - a **user** table and a **wife** table.  The user table should have the **relations** properties defined as follows in the user model : ::

	class User extends ORM {

		function _init() 
		{
			// Relationship definition
			self::$relationships = array(
				'wife' => ORM::has_one('\\Model\\Wife'),
			);

			// Field definition
			self::$fields = array(
				'id'       => ORM::field('auto[3]'),
				'name'     => ORM::field('char[40]'),
				'email'    => ORM::field('email[40]'),
				'username' => ORM::field('char[5,10]'),
				'active'   => ORM::field('int[1]'),
			);
		}
	}

Once you have defined the corresponding **belongs_to** relationship in the Wife model you are all set! Now Gas will assume that each record in user table, might have one related record in wife table.  The wife property is then accessed as follows: ::

	$wives = Model\User::find($id)->wife();


belongs_to
++++++++++

After you declare a relationship in one 'parent' model, the corresponding 'child' model **must represent those relationships as well**. Based on the example above, your wife model should be something like : ::

	class Wife extends ORM {

		function _init()
		{
			// Define relationships
			self::$relationships = array(
				'user' => ORM::belongs_to('\\Model\\User'),
			);

			// Define fields definition
			self::$fields = array(
				'id'         => ORM::field('auto[3]'),
				'user_id'    => ORM::field('int[3]'),
				'name'       => ORM::field('char[40]'),
				'hair_color' => ORM::field('email[20]'),
			);
		}

	}

Now Gas will assume that each record in wife table, might have one related record in user table.


has_many
++++++++

Secondly, we will talk about **has_many** relationships. This relationship type is similar to those above, except for **one-to-many** relationship, Gas will asume that one record from the parent table has several records in child table. For example lets say we have two tables which have a **one-to-many** relationship between them. The user table (the parent table) and the kid table (the child table), then user table should have relationship properties as follows : ::

	class User extends ORM {

		function _init() 
		{
			// Relationship definition
			self::$relationships = array(
				'wife' => ORM::has_one('\\Model\\Wife'),
				'kid'  => ORM::has_many('\\Model\\Kid'),
			);

			// Field definition
			self::$fields = array(
				'id'       => ORM::field('auto[3]'),
				'name'     => ORM::field('char[40]'),
				'email'    => ORM::field('email[40]'),
				'username' => ORM::field('char[5,10]'),
				'active'   => ORM::field('int[1]'),
			);
		}
	}

.. note:: Don't forget that you must set up a corresponding **belongs_to** relationship in the child table - in this case, **kid** must have a ORM::belongs_to('\\Model\\Wife') set.

**Has many** also could be a **many-to-many** relationship. This is the most tricky relationship in your database. This relationship type, exists when you have a **pivot table**. A pivot table is an intermediate table that links one table with another table.  When each table has many and belongs to each other a **many-to-many** relationship exists.

For example, assume a user has many jobs, but a job can also belong to many users. Three tables must be created to accomplish this relationship: a user table, a job table, and a job_user table. How do we set up this type of relationship?

First, set up both **user** and **job** model. Our user model now may look like : ::

	<?php namespace Model;

	use \Gas\Core;
	use \Gas\ORM;

	class User extends ORM {

		function _init() 
		{
			// Relationship definition
			self::$relationships = array(
				'wife' => ORM::has_one('\\Model\\Wife'),
				'kid'  => ORM::has_many('\\Model\\Kid'),
				'job'  => ORM::has_many('\\Model\\Job\\User => \\Model\\Job'),
			);

			// Field definition
			self::$fields = array(
				'id'       => ORM::field('auto[3]'),
				'name'     => ORM::field('char[40]'),
				'email'    => ORM::field('email[40]'),
				'username' => ORM::field('char[5,10]'),
				'active'   => ORM::field('int[1]'),
			);
		}
	}

Then the **job** model would be something like : ::

	<?php namespace Model;

	use \Gas\Core;
	use \Gas\ORM;

	class Job extends ORM {

		function _init() 
		{
			// Define relationships
			self::$relationships = array(
				'user'  => ORM::has_many('\\Model\\Job\\User => \\Model\\User'),
			);

			// Define fields definition
			self::$fields = array(
				'id'          => ORM::field('auto[3]'),
				'name'        => ORM::field('char[40]'),
				'description' => ORM::field('string[100]'),
			);
		}
	}

Last, you will need to create a subfolder on your model directory, called **job**. Then create a **user.php** file to handle **job_user** entity, which contain : ::


	<?php namespace Model\Job;

	use \Gas\Core;
	use \Gas\ORM;
	
	class User extends ORM {

		function _init() 
		{
			// Define relationships
			self::$relationships = array(
				'user' => ORM::belongs_to('\\Model\\User'),
				'job'  => ORM::belongs_to('\\Model\\Job'),
			);

			// Define fields definition
			self::$fields = array(
				'id'         => ORM::field('auto[3]'),
				'user_id'    => ORM::field('int[3]'),
				'job_id'     => ORM::field('int[3]'),
			);
		}
	}

When following Gas conventions, defining a relationship(s) is pretty simple. However in real life conventions are not always followed smoothly (eg : by our recent schema, its imposible to match each table with Gas field-naming convention). Gas alleviate this cases with several options.

In the previous example, when you define a relationship Gas always assume that your **foreign key** follows the **table_pk** (pk for primary key) convention, so Gas assumes there must be a 'user_id' column in your intermediate table linked with the user table. If your schema can't follow this convention you will need to add a **foreign_key** variable definition to your pivot table.  : ::

	<?php namespace Model\Job;

	use \Gas\Core;
	use \Gas\ORM;
	
	class User extends ORM {

		public $foreign_key = array('\\model\\user' => 'u_id', '\\model\\job' => 'j_id');

		function _init() 
		{
			// Define relationships
			self::$relationships = array(
				'user' => ORM::belongs_to('\\Model\\User'),
				'job'  => ORM::belongs_to('\\Model\\Job'),
			);

			// Define fields definition
			self::$fields = array(
				'u_id'    => ORM::field('int[3]'),
				'j_id'     => ORM::field('int[3]'),
			);
		}
	}
