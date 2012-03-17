.. Gas ORM documentation [callbacks]

Hooks a.k.a Callbacks
=====================

You can define hooks/callbacks for some of the model's explicit lifecycle events:

+---------------------+----------------------------------------------+
| Callback method     | Description                                  |
+=====================+==============================================+
| **_before_check**   | hook point before validation process         |
+---------------------+----------------------------------------------+
| **_after_check**    | hook point before after validation process   |
+---------------------+----------------------------------------------+
| **_before_save**    | hook point before save process               |
+---------------------+----------------------------------------------+
| **_after_save**     | hook point before after save process         |
+---------------------+----------------------------------------------+
| **_before_delete**  | hook point before before deletion process    |
+---------------------+----------------------------------------------+
| **_after_delete**   | hook point before after deletion process     |
+---------------------+----------------------------------------------+

This is a convinient way to hook into the life cycle of a Gas object. You can control the state of your object by declaring certain methods to be called before or after methods are invoked on your object within Gas mechanism.

Lets throw another scenario. You are just submit a form, which contain some data. And in your validation rules, you only want to check several input. So, the flow will be...

_before_check()
+++++++++++++++

This allow you to intercept **_check** method. You can place this method within your Gas model. ::

	public $preserve_key = array('hobbies', 'bio');

	public $preserve_fields = array();

	function _before_check()
	{
		$post_data = $this->record->get('data');

		foreach ($post_data as $key => $value)
		{
			if (in_array($key, $this->preserve_key))
			{
				$this->preserve_fields[$key] = $value;

				unset($post_data[$key]);
			}
		}

		$this->record->set('data', $post_data);

		return $this;
	}

Notice that to get all input which have set, we could use ::

	$this->record->get('data');

And to re-set the input, we didnt modify any **$_POST** data, instead we use ::

	$this->record->set('data', $post_data);

So in this example, we are preserve (and remove for temporary) some fields for being validated.

_after_check()
+++++++++++++++

You can place this method within your Gas model. ::

	function _after_check()
	{
		$post_data = $this->record->get('data');

		$full_data = array_merge($post_data, $this->preserve_fields);

		$this->record->set('data', $full_data);

		return $this;
	}

So in this example, we are rebuild our data, for next process.

_before_save()
+++++++++++++++

You can place this method within your Gas model. Let say, we want adding some **active** value. ::

	function _before_save()
	{
		$this->record->set('data.active', 1);

		return $this;
	}

So in this example, we are adding some fields into our data, for next process. In Gas ORM, saving process can be an INSERT event, or UPDATE event. How to know which event at a time? ::

	function _before_save()
	{
		$is_new = $this->empty;

		if ($is_new == TRUE)
		{
			echo 'I have to do something before INSERT';
		}
		else
		{
			echo 'I have to do something before UPDATE';
		}
	}

By checking **empty** property, we actually checked is there a record being hold by some Gas instance. If yes, then above **is_new** variable will set to FALSE, mean UPDATE process is the next event. Otherwise, it will be an INSERT event.

_after_save()
+++++++++++++

You can place this method within your Gas model. ::

	function _after_save()
	{
		
	}

This is a convinient way, to do something after INSERT or UPDATE operation.

_before_delete()
++++++++++++++++

You can place this method within your Gas model. ::

	function _before_delete()
	{
		// Do some stuff in record


		return $this;
	}

Here you can sort some stuff, before delete a record(s).

_after_delete()
+++++++++++++++

You can place this method within your Gas model. ::

	function _after_delete()
	{
		
	}

This is a convinient way, to do something after DELETE operation.