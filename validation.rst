.. Gas ORM documentation [validation]

Data Types and Validation
=========================

Every Gas model could have **_init()** function. It primarily used for set up a table's fields definition and validation but it can be used as a replacement for constructor method. ::

 	class User extends Gas {

 		function _init()
 		{
 			
 		}

	}

.. note:: Dont use or put any constructor on your Gas model, use **_init()** method instead.

Field Properties
++++++++++++++++

Everytime we need to doing some write operation (insert or updating a record), we often validate the data. As default, Gas will not enforce you to using this validation feature, but if you are using it, you have to set **all** field, which you want to validate. 

Lets throw an example scenario. Suppose we are about inserting new record into our user table. Our user table holds several fields, and we only want to validate several of them, let say they are : username, email and name. And because we often running validation procedure when we update a record, we also want to validate the id and active columns. In this case, then we could set it as bellow ::

 	class User extends Gas {

 		function _init()
 		{
 			$this->_fields = array(

 				'id'       => Gas::field('auto[11]'),

 				'name'     => Gas::field('char[40]'),

 				'email'    => Gas::field('email'),

 				'username' => Gas::field('char[3,10]'),

 				'active'   => Gas::field('int[1]'),

			);
 		}
	}

This is one-time set-up, unless in the future, we need to change our table schema. So basicly, we have set several fields rules, which is : **auto** , **char** , **email** , **int** .

It just provide a generic rule for common used datatype. Also as you may already notice, we can add max length rule directly using **[n]** or specify both min length and max length using **[n,n]**, and this will usefull to define your field length since this will represent your field constrain (auto-create tables mechanism will use this value as your field constraint).

Annotation
++++++++++

Each of your field is represent actual field within your tables. The purpose of annotation was to clear this up. This property values also usefull when you use Gas ORM auto-create tables feature. So each time you set a field value within **_fields** properties, you actually already define some basic information about your table's fields. 

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
| **string**          | for any string datatpe, default to TEXT                                       |
+---------------------+-------------------------------------------------------------------------------+
| **spatial**         | for any spatial datatpe, default to GEOMETRY                                  |
+---------------------+-------------------------------------------------------------------------------+
| **numeric**         | for any numeric datatpe, default to TINYINT                                   |
+---------------------+-------------------------------------------------------------------------------+
| **datetime**        | for any datetime datatpe, default to DATETIME                                 |
+---------------------+-------------------------------------------------------------------------------+

Above is also represent each datatype category. And most-likely your field datatype is defined in above list. But let say you have **TINYBLOB** datatype within some table, how you must define its field property? You can use the third parameter within **field()** method, so in this case, you can specify it like bellow ::

	// ...

	'project_file' => Gas::field('string', array('required'), 'TINYBLOB, null'),

	// ...

Notice that because **TINYBLOB** is within **string** datatype category, then you define it as string. But you also add more annotation as third parameter, which are **TINYBLOB** and **null** (mean each record can have null value of this field). This mean if you have **FLOAT** field, you can use **numeric** then add **FLOAT** in third parameter as well.

This annotation will also included and used as a pointer, in your auto-created migration files, so if needed you can also add **auto_increment** or/and **unsigned** at third parameter separated by comma. 

Additional Rules
++++++++++++++++

As you may expected, if you need to put additional rules, which is a standard CI validation rules, you can assign it as an array, into second parameter. For example, the username is mandatory field, so we want to apply **required** rule as well, then we need to change corresponding field into : ::

	// ...

	'username' => Gas::field('char[10]', array('required')),

	// ...

And for more custom validation, we also could do that, with slightly different convention. So let say, we want to implement some custom callback into email field. We add a callback rule : ::

	// ...

	'email'    => Gas::field('email', array('callback_check_email')),

	// ...

Then you would need to set up your callback function as bellow.

Custom Rules
++++++++++++

As you may already know, CI validation system supports callbacks to your own validation functions. This permits you to extend the validation class to meet your needs. From above case, if we need to run a database query to see if the user is registering a unique email, we can create a callback function that does that. It will depend on how you want to validate the input. Let's create a example of this. ::

	public function check_email($field, $val)
	{
		if ($val == 'valid@email.com')
		{
			return TRUE;
		}
		else
		{
			self::set_message('check_email', 'The %s field should only contain \'valid@email.com\'', $field);

			return FALSE;
		}
	}

Gas has its own validation mechanism. It still rely on CI validation afterall, only with several exception in usage convention :

- Your callbacks function should located within your Gas model, instead in your controller.
- Your callbacks function should expect 2 parameter instead one. While **val** (second parameter) is containing a value to check, **field** (first parameter) will be automatically populated by Gas mechanism. You doesn't need to worrying anything, or set anything. Just put it in place.
- If you use **set_message** method, you will use static instead dynamic method, and put **field** variable as third parameter.

Thats it. Soon you feel convinient with Gas internal validation mechanism, you will realize that your codebase become much more maintanable than ever, because each callbacks is belongs to its own model/logic, instead polluted your controllers (and make it fatter).


