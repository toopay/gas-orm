.. Gas ORM documentation [relationship]

Relationship
============

Gas supported three type of table relationship, **one-to-one** relationship, **one-to-many** relationship and **many-to-many** relationship. All you have to do, is to define your table relations at **relations** properties in your model.

Setting up your model relationship is one-time set-up, unless in the future, you need to change your table schema.

has_one
+++++++

First, we will talk about **has_one** relationship. For example, let say we have two table which have **one-to-one** relationship, user table and wife table, then user table should have **relations** properties as follow :

Your user model would be something like : ::

	class User extends Gas {
		
		public $relations = array(

					'has_one' => array('wife' => array()),

                        		);

	}

Thats it. Now Gas will assume that each record in user table, might have one related record in wife table.

belongs_to
++++++++++

After you declare some relationship, in some model, the corresponding model should represent those relationship as well. So, based by example above, your wife model should be something like : ::

	class Wife extends Gas {
		
		public $relations = array(

					'belongs_to' => array('user' => array()),

                        		);

	}

Now Gas will assume that each record in wife table, might have one related record in user table.


has_many
++++++++

Secondly, we will talk about **has_many** relationship. This relationship type, is similar with above, except for **one-to-many** relationship, Gas will asume that one record from parent table, is always have several records in child table. For example, lets say we have two table which have **one-to-many** relationship, user table (as parent table) and kid table (as child table), then user table now would have relations properties as follow : ::

	class User extends Gas {
		
		public $relations = array(

					'has_one' => array('wife' => array()),

					'has_many' => array('kids' => array()),

                        		);

	}

Make sure kid model represent this relationship as well, by setting up **belongs_to** values.

has_and_belongs_to
++++++++++++++++++

Last, this is the most tricky relationship in your database. This relationship type, is exist when you have a pivot table. Pivot table is an intermediate table, which links one table with another table, when each table is having many and belongs to each other : **mant-to-many** relationship.

For example, assume a user has many jobs, but a job can also belong to many users. Three tables must be created to accomplish this relationship: a user table, a job table, and a job_user table. In this critical scenario, Gas will leave you with two option :

- use **has_and_belongs_to** : mean both user and job, directly referenced each other.
- use **has_many** and **through** : mean both user and job referenced each other **through** another model.

So, let say you choose option one, what you have to do is define **has_and_belongs_to** properties in your corresponding table(s). So refer to above scenario (user and job), you will take this below step.

Your user model now would be something like : ::

	class User extends Gas {
		
		public $relations = array(

					'has_one' => array('wife' => array()),

					'has_many' => array('kids' => array()),

					'has_and_belongs_to' => array('job' => array()),

                        		);

	}

Make sure job model represent this relationship as well, by setting up **has_and_belongs_to** values.

foreign_table
+++++++++++++

While by following Gas nature convention, define a relationship(s) is almost does not require setting, in real life it may not be followed smoothly (eg : by our recent schema, its imposible to match each table with Gas naming convention). Gas alleviate this cases with several options.

First, we will look at **foreign_table** option.

In previous example, Gas assume your intermediate table were job_user or user_job. Otherwise, you can still working on it, by specify **foreign_table** option. So lets say your intermediate table name was **j_u**, now your user model would be something like : ::

	class User extends Gas {
		
		public $relations = array(

					'has_one' => array('wife' => array()),

					'has_many' => array('kids' => array()),

					'has_and_belongs_to' => array('job' => array(
							
							'foreign_table' => 'j_u',
						
							)),

                        		);

	}

Make sure job model represent this relationship as well, by setting up **has_and_belongs_to* values which also contain **foreign_table** option.

foreign_key
+++++++++++

Now, we will look at **foreign_key** option.

In previous example, you successfully define your intermediate table name to **j_u**. There is one more thing you should concern, while you define a relationship. Gas always assume that your **foreign key** is follow **table_pk** (pk for primary key) convention, so Gas was thingking there must be 'user_id' collumn in your intermediate table, to linked it with user table. Unfortunately, in some cases, your recent schema can't follow this convention too. Then you will need to add one more line  : ::

	class User extends Gas {
		
		public $relations = array(

					'has_one' => array('wife' => array()),

					'has_many' => array('kids' => array()),

					'has_and_belongs_to' => array('job' => array(
							
							'foreign_table' => 'j_u',

							'foreign_key' => 'u_id',
						
							)),

                        		);

	}

Make sure job model represent this relationship as well, by setting up **has_and_belongs_to* values which also contain **foreign_key** option.

through
+++++++

As you see in **has_and_belongs_to** example, there are another option to set-up **many-to-many** relationship. This option exists, and could be your option whether your pivot table is not only used to linked two tables, but also have its own primary key and other columns. If you are in this situation, dont panic. You still can using the second option : **through**.

So, instead having **has_and_belongs_to** values, now your user model would be something like : ::

	class User extends Gas {
		
		public $relations = array(

					'has_one' => array('wife' => array()),

					'has_many' => array('kids' => array()),

					'has_many' => array('job' => array(
							
							'through' => 'job_user',
						
							)),

                        		);

	}

Make sure job model represent this relationship as well, by setting up **has_many** values which also contain **through** option.

self
++++

The last option, **self**, is to handle self-referential and adjacency column/data (hierarchical data).

The easier way to describe or giving an example about this kind of data, is comments system. In this case, each comment can be a reply to other comment, mean they reference themself within one table. If you have this kind of table, you can working on it by specify self option in your relations properties. Self-referential works as you need, means it support all of defined relation types (but you should only define one type at a time).

So, let say we have a comment table and model already set up. And we decide to use **has_many** type, which mean, each comment can have several replies, then our comment model would be something like : ::

	class Comment extends Gas {
		
		public $relations = array(

					'has_many' => array('comment' => array(
							
							'self' => TRUE,

							'foreign_key' => 'parent_id',
						
							)),

                        		);

	}

This way, you can accessing replies comment as well.

