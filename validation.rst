.. Gas ORM documentation [validation]

Data Types and Validation
=========================

Every Gas model could have an **_init()** function. It primarily used for set up a fields definition and relationship but it can be used as a replacement for a constructor method. ::

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

When we need to perform a write operation (insert or updating a record), we often want to validate the data. By default, Gas will not enforce you to use its validation features, but if you choose to, you have to set **all** fields that you want to validate. 

Lets throw an example scenario. Suppose we want to insert a new record into our user table. Our user table holds several fields, and we only want to validate several of them, let say they are : username, email and name. As we often run validation procedures when we update a record, we also want to validate the id and active columns. In this case we could set it as below ::

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

This is one-time set-up, unless in the future we need to change our table schema. So basicly, we have set several fields rules, which are : **auto** , **char** , **email** , **int** .

These provide a generic rule for commonly used datatypes. Also as you may have already noticed, we can add a "max length" rule directly using **[n]** or specify both min length and max length using **[n,n]**. This can be useful to define your field length since this will represent your field constraint (auto-create tables mechanism will use this value as your field constraint). More explanation about field definitions is described below.


Data Type
++++++++++

Each of your **self::$fields** array members represents an actual field within your table. The purpose of the **field** method was to clear this up. The value of the **fields** property is also useful when you use Gas ORM to auto-create tables. Each time you set a field value within the **fields** propertiy, you have already defined some basic information about your table's fields. 

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

The table above represents each datatype category. Most likely your field datatype is defined in the list above. But let say you want to use a **TINYBLOB** datatype within some table, how do you define its field property? You can use the third parameter within the **field()** method. In this case, you can specify it as shown below ::

	// ...

	'project_file' => ORM::field('string', array('required'), 'TINYBLOB, null'),

	// ...

Notice that because **TINYBLOB** is within **string** datatype category you define it as string. But you can also add more annotation in the third parameter, in this case **TINYBLOB** and **null** (this means each record can have a null value for this field). As another example, if you want a **FLOAT** field, you can use the **numeric** category and then add **FLOAT** in the third parameter.

This annotation will also included and used as a pointer in your auto-created migration files, so if needed you can also add **auto_increment** or/and **unsigned** as the third parameter separated by commas. 


Additional Rules
++++++++++++++++

As you may expected, if you need to put additional rules, which is a standard CI validation rules, you can assign it as an array, into second parameter. For example, if **username** is a mandatory field and we want to apply the **required** CI validation rule as well, then we need to change the corresponding field into : ::

	// ...

	'username' => ORM::field('char[10]', array('required')),

	// ...

This allows highly customised validation rules. If we want to implement some custom callback into username field we can add a callback rule : ::

	// ...

	'username' => ORM::field('char[10]', array('required', 'callback_username_check')),

	// ...

Then you would need to set up your callback function as below  : ::

	function _username_check($str)
	{
		if ($str == 'test')
		{
			return FALSE;
		}

		return TRUE;
	}

If you want to set a custom error on the above method, you just need to add a line (with 'username_check' as the key) to your gas language file: ::

	$lang['username_check']                = 'The username supplied was invalid!';


Timestamp Fields
++++++++++++++++

When we save some records, we often want to record the insert or update time. Gas ORM makes this very easy, just define your **datetime** field into the **ts_fields** properties array within the **_init** function.  For example: ::

	function _init()
	{
		self::$fields = array(

				// ...

		);

		$this->ts_fields = array('time_updated');
	}

Then whenever you perform an update operation, those field will be automatically filled with the current timestamp. If you prefer to have a UNIX timestamp (integer) value in your field, use **unix_ts_fields** instead.

If you use a field to store the created timestamp, then this can also be added to the ts_fields array, with the field name within square brackets: ::

	 // ...

	$this->unix_ts_fields = array('time_updated', '[time_created]');

	// ...

You could have both unix or normal datatime properties within your model's **_init** method.

