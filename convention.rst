.. Gas ORM documentation [convention]

Convention
==========

Gas makes some assumptions about your database structure. Each table should have primary key, default to **id**. If your primary key isn't **id**, you can set **primary_key** properties. Each table should have same name with its corresponding Gas model's name, otherwise you will need to set **table** properties.

Typically your Gas model will be something like this, let say you have user.php to hold user table. ::

	<?php namespace Model;

	use \Gas\Core;
	use \Gas\ORM;

	class User extends ORM {

		function _init() 
		{
			// Relationship definition

			// Field definition
		}

	}

Notice that you will need to specify the field definition and relationship definition, then you can start using finder or doing some write operation.

Model Properties
++++++++++++++++

But if somehow, your schema didn't allow you to follow above convention, you can specify both **table** and **primary_key** properties, so it would be something like : ::

	<?php namespace Model;

	use \Gas\Core;
	use \Gas\ORM;

	class User extends ORM {

		public $table = 'person';

		public $primary_key = 'person_id';

		function _init() 
		{
			// Relationship definition

			// Field definition
		}

	}

If you have a pivot table, that has composite key, you can specify **foreign_key** properties : ::

	 <?php namespace Model\Role;

	use \Gas\Core;
	use \Gas\ORM;

	class User extends ORM {

		public $foreign_key = array('\\Model\\User' => 'user_id', '\\Model\\Role' => 'role_id');

		function _init() 
		{
			// Relationship definition

			// Field definition
		}

	}

And you are ready to go further.