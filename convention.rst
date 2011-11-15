.. Gas ORM documentation [convention]

Convention
==========

Gas makes some assumptions about your database structure. Each table should have primary key, default to **id**. If your primary key isn't **id**, you can set **primary_key** properties. Each table should have same name with its corresponding Gas model's name, otherwise you will need to set **table** properties.

Typically your Gas model will be something like this, let say you have user_gas.php to hold user table. ::

	class User extends Gas {

	}

As simple as that, then you can start using finder or doing some write operation.

Model Properties
++++++++++++++++

But if somehow, your schema didn't allow you to follow above convention, you can specify both **table** and **primary_key** properties, so it would be something like : ::

	class User extends Gas {

		public $table = 'peoples';

		public $primary_key = 'people_id';

	}

And you are ready to go further.