.. Gas ORM documentation [extension]

Extension
=========

The purpose of an **extension** is to become a standard interface which you can use, to share common function that utilize either CI Library or your own library, across your Gas models/instances.

We use Interface instead Class, in extension implementation, because :

- Interfaces allow us to define/create a common structure for our classes – to set a standard for objects.
- Interfaces solves the problem of single inheritance – they allow us to inject 'qualities' from multiple sources.
- Interfaces provide a flexible base/root structure that we don't get with classes.

Here you can found some default extension which bundled within Gas ORM.

Dummy Extension
+++++++++++++++

I include this **dummy** extension within the repo as well. Lets look how it works : ::

	
	use \Gas\Extension;

	class Dummy implements Extension {

		/**
		 * @var mixed Gas instance(s)
		 */
		public $gas;
		
		/**
		 * Extension initialization method
		 * 
		 * @param  object
		 * @return void
		 */
		function __init($gas)
		{
			// Here, Gas will transport your instance
			$this->gas = $gas;

			return $this;
		}

		/**
		 * Simple example of Extension usage
		 *
		 * @param  string Argument
		 * @return string Explanation
		 */
		public function explain($arg = NULL)
		{
			// Load CI libraries
			$CI =& get_instance();

			if ( ! class_exists('CI_Typography')) $CI->load->library('typography');

			if ( ! class_exists('CI_Table')) $CI->load->library('table');

			// Build the Extension information
			$argument = var_export($arg, TRUE);
			$fullname = __CLASS__;
			$fragment = explode('\\', $fullname);
			$nickname = strtolower(end($fragment));
			$path     = __FILE__;

			// Determine the caller condition
			if (is_object($this->gas))
			{
				// This for single Gas instance
				$model         = ucfirst($this->gas->model());
				$structure     = $this->gas->meta->get('collumns');
				$relationships = array_keys($this->gas->meta->get('entities'));

				// Get all the records and start build the record table
				$records = $this->gas->record->get();
				$CI->table->set_heading($structure);

				foreach ($records as $record)
				{
					$CI->table->add_row(array_values($record));
				}

				$table = $CI->table->generate();
				$CI->table->clear();
			}
			elseif (is_array($this->gas) && ! empty($this->gas))
			{
				// This for a collection of Gas instance(s)
				$sample        = $this->gas;
				$gas           = array_shift($sample);
				$model         = ucfirst($gas->model());
				$structure     = $gas->meta->get('collumns');
				$relationships = array_keys($gas->meta->get('entities'));

				// Get all the records and start build the record table
				$records = $this->gas;
				$CI->table->set_heading($structure);

				foreach ($records as $record)
				{
					$CI->table->add_row(array_values($record->record->get('data')));
				}

				$table = $CI->table->generate();
				$CI->table->clear();
			}
			else
			{
				// This for nothing
				$model         = 'NULL';
				$structure     = array('undefined');
				$relationships = array('undefined');
				$records       = array();
				$table         = '<strong>Empty Record</strong>'."\n";
			}
			
			// Now build the explanation
			$explanation  = 'Hello, i am an extension. ';
			$explanation .= 'My nickname is <strong>'.$nickname.'</strong> and my fullname is <strong>'.$fullname.'</strong>.';
			$explanation .= "\n";
			$explanation .= 'You can found me at '.$path.'.'."\n";
			$explanation .= 'You call me through '.$model.' instance, and passing bellow arguments : '."\n";
			$explanation .= $argument ."\n";
			$explanation .= 'to processed, and to explain what '.$model.' model looks like.'."\n";
			$explanation .= $model.' have table structure : ';
			$explanation .= implode(', ', $structure)."\n";
			$explanation .= $model.' have defined relationships : ';
			$explanation .= implode(', ', $relationships)."\n";
			$explanation .= $model.' instance now is holding : '.count($records).' record(s)'."\n";
			$explanation .= 'With little help from CodeIgniter\'s Table and Typography library,'."\n";
			$explanation .= 'I can create this paragraph, also output the record into this table : '."\n";
			$explanation .= $table;
			$explanation .= 'So basicly, my purpose is to become a standard interface which you can use,'."\n";
			$explanation .= 'to share common function which utilize either CI Library or your own library, '."\n";
			$explanation .= 'across your Gas models/instances.'."\n";
			$explanation .= 'This is all I can say.'."\n";

			// Format the explanation, then output it
			$formatted_explanation = $CI->typography->auto_typography($explanation);

			return '<pre>'.$formatted_explanation.'</pre>';
		}
	}

You can directly use it. ::

	echo Model\User::dummy()->all()->explain();

This should be a simple way, to describe how **extension** works in Gas ORM.

Result Extension
++++++++++++++++

This extension provide convinience way to work with Gas ORM instance and records, usage :  ::

	$result = Model\User::result()->all();

	// Easy way to debug over your result
	echo $result;

	// Convert the instances into an array of instance
	$users = $result->as_array();

	foreach ($users as $user)
	{
	  // $user will be a typical Gas Instance
	}

	// Convert all instances record into various format
	$records_array = $result->to_array(); // assoc array
	$records_json = $result->to_json();   // JSON 
	$records_xml = $result->to_xml();     // XML

	// Convert all instance's record into Gas\Data object
	$records_data = $result->to_data();

	$first_user = $records_data->get('data.0'); // Return the first index
	$first_user_name = $records_data->get('data.0.name'); // Return the first index name
	$some_user_name = $records_data->get('data.100.name', 'Default Name');

If you need more functionality, simply extend it on your **application/libraries/gas/extension/result.php**.

HTML Extension
++++++++++++++

You can see the demo for table on my sandbox [#html1_sandbox]_ .
Generate HTML table from Gas model records  ::
	
	// execute some Gas finder
	$users = Model\User::html()->all();
	
	// simple usage
	echo $users->table();

	// hide some collumn
	$hidden = array('username', 'email');

	echo $users->hide($hidden)->table();

	// set table heading
	$headings = array('collumn id', 'collumn name', 'collumn username', 'collumn email');

	echo $users->heading($headings)->table();

	// set table template
	$template = array( 'table_open' => '<table border="1" cellpadding="4" cellspacing="0">');

	echo $users->template($template)->table();

	// hide some collumn, set table heading, set template
	$hidden = array('email');

	$headings = array('collumn id', 'collumn name', 'collumn username');

	$template = array( 'table_open' => '<table border="1" cellpadding="4" cellspacing="0">');

	echo $users->hide($hidden)->heading($headings)->template($template)->table();


Generate HTML form from Gas model records ::
	
	// execute some Gas finder
	$user = Model\User::html()->find(1);

	// simple usage
	echo $user->form('controller/function');

	// define entity type
	$entities = array();

	$entities['email'] = array('hidden' => array('id' => 'email'));

	echo $user->definition($entities)->form('controller/function');

There are option for setting **submit**, **separator**, **entity** and **hide** as well. You can see the demo on my sandbox [#html2_sandbox]_

jQuery Extension
++++++++++++++++

This extension will be a good place to sharing common handler for any similar jQuery data processor plugin (eg : flot [#flot]_ for outputing graph or chart).

For now, it provide a method to handle and generate response for datatable. [#datatable]_ 

Assume you have download and put it into your application directory, and set it properly, point it to some controller as ajax source, then within your controller (which receive the ajax request), you only need to put ::

	if ($_POST)
	{
		echo Model\User::jquery()->datatable($_POST);
	}
	else
	{
		echo Model\User::jquery()->datatable($_GET);
	}

That will serve datatable for browsing **user** table. You can see the demo on my sandbox [#jquery1_sandbox]_ .

Write your own Gas ORM extension
++++++++++++++++++++++++++++++++

From above extension example, if you are ready to create your own, here litlle note you should remember :

- Your extension, should have namespace **Gas\\Extension**.
- Your extension, should implements **Gas\\Extension** interface.
- Your extension, should have **__init($gas)** method (notice the double underscore, distungished it from your model init method).
- Your extension, should located under **libraries/gas/extension** folder within your application 

Thats all about extension.

.. [#datatable] http://datatables.net/
.. [#flot] http://code.google.com/p/flot
.. [#html1_sandbox] http://taufanaditya.com/sandbox/to_table
.. [#html2_sandbox] http://taufanaditya.com/sandbox/to_form
.. [#jquery1_sandbox] http://taufanaditya.com/sandbox/datatable
