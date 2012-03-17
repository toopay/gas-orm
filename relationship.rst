.. Gas ORM documentation [relationship]

Relationship
============

Gas supported three type of table relationship, **one-to-one** relationship, **one-to-many** relationship and **many-to-many** relationship. All you have to do, is to define your table relations at **relations** properties in your model.

Setting up your model relationship is one-time set-up, unless in the future, you need to change your table schema.

Define Relationship
+++++++++++++++++++

To define some entity association, you need to perform one of available relationship methods : 

+---------------------+-------------------------------------------------------------------------------+
| Relationship option | Description                                                                   |
+=====================+===============================================================================+
| **has_one**         | for one-to-one relationship                                                   |
+---------------------+-------------------------------------------------------------------------------+
| **has_many**        | for one-to-many and many-to-many relationship                                 |
+---------------------+-------------------------------------------------------------------------------+
| **belongs_to**      | for represent the relationships above in the target model                     |
+---------------------+-------------------------------------------------------------------------------+

All relationship method could accept two parameter. The first parameter is contain the 'path' and the second parameter could contain additional query to run, eg : ::

	'wife' => ORM::has_one('\\Model\\Wife'),
	'job'  => ORM::has_many('\\Model\\Job\\User => \\Model\\Job', array('select:id,name')),

If the 'path' are more than one model, you need to add a separator which explain the ownership status of each models. In above **job** relationship for example, the separator was **=>**. That simply mean **Model\\Job\\User** (which hold the job_user table) is OWNED (or belongs to) both **Model\\User** and **Model\\Job**.

The second parameter, contain an array of additional queries method. Available option for second parameter are : 

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

First, we will talk about **has_one** relationship. For example, let say we have two table which have **one-to-one** relationship, user table and wife table, then user table should have **relations** properties as follow :

Your user model would be something like : ::

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

Thats it. Now Gas will assume that each record in user table, might have one related record in wife table.

belongs_to
++++++++++

After you declare some relationship, in some model, the corresponding model **should represent those relationship as well**. So, based by example above, your wife model should be something like : ::

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

Secondly, we will talk about **has_many** relationship. This relationship type, is similar with above, except for **one-to-many** relationship, Gas will asume that one record from parent table, is always have several records in child table. For example, lets say we have two table which have **one-to-many** relationship, user table (as parent table) and kid table (as child table), then user table now would have relations properties as follow : ::

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

.. note:: Always ensure that the related model, represented the relationship as well everytime you set up some relationship. For example, in above case, make sure kid model represent this relationship as well, by setting up **belongs_to** values.

**Has many** also could be a **many-to-many** relationship. This is the most tricky relationship in your database. This relationship type, is exist when you have a pivot table. Pivot table is an intermediate table, which links one table with another table, when each table is having many and belongs to each other : **mant-to-many** relationship.

For example, assume a user has many jobs, but a job can also belong to many users. Three tables must be created to accomplish this relationship: a user table, a job table, and a job_user table. How to set up this type of relationship?

First, set up both **user** and **job** model. Our user model now may looks like : ::

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

While by following Gas nature convention, define a relationship(s) is pretty simple, in real life it may not be followed smoothly (eg : by our recent schema, its imposible to match each table with Gas field-naming convention). Gas alleviate this cases with several options.

In previous example, when you define a relationship. Gas always assume that your **foreign key** is follow **table_pk** (pk for primary key) convention, so Gas was thingking there must be 'user_id' collumn in your intermediate table, to linked it with user table. Unfortunately, in some cases, your recent schema can't follow this convention. Then you will need to add **foreign_key** to your pivot table.  : ::

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



