.. Gas ORM documentation [validation]

Data Types and Validation
=========================

Every Gas model could have **_init()** function. It primarily used for set up a fields definition and relationship but it can be used as a replacement for constructor method. ::

 	<?php namespace Model;

	use \Gas\Core;
	use \Gas\ORM;

	class User extends ORM {

		function _init() 
		{
			// Relationship definition

			// Field definition

			// Do your stuff here..
		}

	}

.. note:: Dont use or put any constructor on your Gas model, use **_init()** method instead.

Field Properties
++++++++++++++++

Everytime we need to doing some write operation (insert or updating a record), we often validate the data. As default, Gas will not enforce you to using this validation feature, but if you are using it, you have to set **all** field, which you want to validate. 

Lets throw an example scenario. Suppose we are about inserting new record into our user table. Our user table holds several fields, and we only want to validate several of them, let say they are : username, email and name. And because we often running validation procedure when we update a record, we also want to validate the id and active columns. In this case, then we could set it as bellow ::

 	class User extends ORM {

		function _init() 
		{
			// Relationship definition

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

This is one-time set-up, unless in the future, we need to change our table schema. So basicly, we have set several fields rules, which is : **auto** , **char** , **email** , **int** .

It just provide a generic rule for common used datatype. Also as you may already notice, we can add max length rule directly using **[n]** or specify both min length and max length using **[n,n]**, and this will usefull to define your field length since this will represent your field constrain (auto-create tables mechanism will use this value as your field constraint). More explanation about field definition, described in bellow section.

Data Type
++++++++++

Each of your field is represent actual field within your tables. The purpose of **field** method was to clear this up. This **fields** property values also usefull when you use Gas ORM auto-create tables feature. So each time you set a field value within **fields** propertiy, you actually already define some basic information about your table's fields. 

The most common datatype are :

+---------------------+-------------------------------------------------------------------------------+
| Available option    | Description                                                                   |
+=====================+===============================================================================+
| **auto**            | for autoincrement datatype                                                    |
+---------------------+-------------------------------------------------------------------------------+
| **char**            | for VARCHAR datatype                                                          |
+---------------------+-------------------------------------------------------------------------------+
| **email**           | for email data , also represent VARCHAR datatype                              |
+---------------------+-------------------------------------------------------------------------------+
| **int**             | for INT datatype                                                              |
+---------------------+-------------------------------------------------------------------------------+

But if your table fields is outside those list, you can use the general category of datatypes :

+---------------------+-------------------------------------------------------------------------------+
| Available option    | Description                                                                   |
+=====================+===============================================================================+
| **string**          | for any string datatypes, default to TEXT                                     |
+---------------------+-------------------------------------------------------------------------------+
| **spatial**         | for any spatial datatypes, default to GEOMETRY                                |
+---------------------+-------------------------------------------------------------------------------+
| **numeric**         | for any numeric datatypes, default to TINYINT                                 |
+---------------------+-------------------------------------------------------------------------------+
| **datetime**        | for any datetime datatypes, default to DATETIME                               |
+---------------------+-------------------------------------------------------------------------------+

Above is also represent each datatype category. And most-likely your field datatype is defined in above list. But let say you have **TINYBLOB** datatype within some table, how you must define its field property? You can use the third parameter within **field()** method, so in this case, you can specify it like bellow ::

	// ...

	'project_file' => ORM::field('string', array('required'), 'TINYBLOB, null'),

	// ...

Notice that because **TINYBLOB** is within **string** datatype category, then you define it as string. But you also add more annotation as third parameter, which are **TINYBLOB** and **null** (mean each record can have null value of this field). This mean if you have **FLOAT** field, you can use **numeric** then add **FLOAT** in third parameter as well.

This annotation will also included and used as a pointer, in your auto-created migration files, so if needed you can also add **auto_increment** or/and **unsigned** at third parameter separated by comma. 

Additional Rules
++++++++++++++++

As you may expected, if you need to put additional rules, which is a standard CI validation rules, you can assign it as an array, into second parameter. For example, the username is mandatory field, so we want to apply **required** rule as well, then we need to change corresponding field into : ::

	// ...

	'username' => ORM::field('char[10]', array('required')),

	// ...

And for more custom validation, we also could do that. So let say, we want to implement some custom callback into username field. We add a callback rule : ::

	// ...

	'username' => ORM::field('char[10]', array('required', 'callback_username_check')),

	// ...

Then you would need to set up your callback function as bellow  : ::

	function _username_check($str)
	{
		if ($str == 'test')
		{
			return FALSE;
		}

		return TRUE;
	}

If you want to set custom error on above method, you just need to add a line (with 'username_check' as key) to your gas language file.

Timestamp Fields
++++++++++++++++

Anytime we save some record(s), we often need to record the insert or update time. Gas ORM help you to avoid those repeatable process. Just define your **datetime** field into **ts_fields** properties within **_init** function ::

	function _init()
	{
		self::$fields = array(

				// ...

		);

		$this->ts_fields = array('time_updated');
	}

Then everytime you perform update operation, those field will be automatically filled with current timestamp. If you prefer to have UNIX timestamp (integer) value in your field, use **unix_ts_fields** instead.

If you have some field to store the created timestamp, put those field within bracket ::

	 // ...

	$this->unix_ts_fields = array('time_updated', '[time_created]');

	// ...

You could have both unix or normal datatime properties within your model's **_init** method.

