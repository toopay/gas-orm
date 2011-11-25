.. Gas ORM documentation [extension]

Extension
=========

The purpose of an **extension** is to become a standard interface which you can use, to share common function that utilize either CI Library or your own library, across your Gas models/instances.

We use Interface instead Class, in extension implementation, because :

- Interfaces allow you to define/create a common structure for your classes – to set a standard for objects.
- Interfaces solves the problem of single inheritance – they allow you to inject 'qualities' from multiple sources.
- Interfaces provide a flexible base/root structure that you don't get with classes.

Here you can found some default extension which bundled within Gas ORM.

Dummy Extension
+++++++++++++++

I include this **dummy** extension within the repo as well. Lets look how it works : ::

	class Gas_extension_dummy implements Gas_extension { 

		public $gas;

		protected $CI;

		public function __init($gas)
		{
			$this->gas = $gas;

			$this->CI =& get_instance();

			if ( ! class_exists('CI_Typography')) $this->CI->load->library('typography');

			if ( ! class_exists('CI_Table')) $this->CI->load->library('table');
		}

		public function explain($args = null)
		{
			$arguments = var_export($args, TRUE);

			$nickname = key($this->gas->extensions);

			$fullname = $this->gas->extensions[$nickname];

			$path = __FILE__;

			$model = ucfirst($this->gas->model());

			$structure = $this->gas->list_fields();

			$relationships = array_keys($this->gas->relations);

			$records = $this->gas->get_raw_record();

			$this->CI->table->set_heading($structure);

			foreach ($records as $record)
			{

				$this->CI->table->add_row(array_values($record));

			}

			$table = $this->CI->table->generate();

			$this->CI->table->clear();


			$explanation = 'Hello, i am an extension. ';

			$explanation .= 'My nickname is '.$nickname.' and my fullname is '.$fullname.'.'."\n";

			$explanation .= 'You can found me at '.$path.'.'."\n";

			$explanation .= 'You call me through '.$model.' instance, and passing bellow arguments : '."\n";

			$explanation .= $arguments ."\n";

			$explanation .= 'to processed, and to explain what '.$model.' model looks like.'."\n";

			$explanation .= $model.' model have table structure : ';

			$explanation .= implode(', ', $structure)."\n";

			$explanation .= $model.' model have defined relationship : ';

			$explanation .= implode(', ', $relationships)."\n";

			$explanation .= $model.' instance now is holding : '.count($records).' record(s)'."\n";

			$explanation .= 'With little help from Table and Typography library,'."\n";

			$explanation .= 'I can create this paragraph, also output the record into this table : '."\n";

			$explanation .= $table;

			$explanation .= 'So basicly, my purpose is to become a standard interface which you can use,'."\n";

			$explanation .= 'to share common function which utilize either CI Library or your own library, '."\n";

			$explanation .= 'across your Gas models/instances.'."\n";

			$explanation .= 'This is all I can say.'."\n";

			$formatted_explanation = $this->CI->typography->auto_typography($explanation);

			return '<pre>'.$formatted_explanation.'</pre>';
		}

	}

If you put **dummy** on your extension list, and enable the **extension autoload** option, then from any of your Gas model, you can directly use it. ::

	$user = new User;

	echo $user->dummy->all()->explain();

This should be a simple way, to describe how **extension** works in Gas ORM.

HTML Extension
++++++++++++++

If you did not autoload html extension, load it first ::

	Gas::load_extension('html');

Generate HTML table from Gas model records ::
	
	// execute some Gas finder
	$users = Gas::factory('user')->html->all();
	
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
	$user = Gas::factory('user')->html->find(1);

	// simple usage
	echo $user->form('controller/function');

	// define entity type
	$entities = array();

	$entities['email'] = array('hidden' => array('id' => 'email'));

	echo $user->definition($entities)->form('controller/function');

There are option for setting **submit**, **separator**, **entity** and **hide** as well.

jQuery Extension
++++++++++++++++

If you did not autoload jquery extension, load it first ::

	Gas::load_extension('jquery');

This extension will be a good place to sharing common handler for any similar jQuery data processor plugin (eg : flot [#flot]_for outputing graph or chart).

For now, it provide a method to handle and generate response for datatable. [#datatable]_ 

Assume you have download and put it into your application directory, and set it properly, point it to some controller as ajax source, then within your controller (which receive the ajax request), you only need to put ::

	if ($_POST)
	{
		echo Gas::factory('user')->jquery->datatable($_POST);
	}
	else
	{
		echo Gas::factory('user')->jquery->datatable($_GET);
	}

That will serve datatable for browsing **user** table.

Write your own Gas ORM extension
++++++++++++++++++++++++++++++++

From above extension example, if you are ready to create your own, here litlle note you should remember :

- Your extension, should prefixed with **Gas_extension_** , then you can adding your extension name after it. 
- Your extension, should implements **Gas_extension** interface.
- Your extension, should have **__init($gas)** method (notice the double underscore, distungished it from your model init method).
- Your extension, should be under **application/libraries**.

Thats all about extension.

.. [#datatable] http://datatables.net/
.. [#flot] http://code.google.com/p/flot

