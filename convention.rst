.. Gas ORM documentation [convention]

Convention
==========

Gas makes some assumptions about your database structure. Each table should have primary key, named **id** by default. If your primary key isn't **id**, you can set **primary_key** properties. Each table should have a name corresponding to its Gas model name, otherwise you will need to set the **table** properties in the model.

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

But if for some reason your schema doesn't allow you to follow the above convention, you can specify both the **table** and **primary_key** properties, so it would be something like : ::

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

If you have a pivot table with a composite key, you can specify **foreign_key** properties : ::

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
