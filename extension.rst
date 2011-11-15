.. Gas ORM documentation [extension]

Extension
=========

The purpose of an **extension** is to become a standard interface which you can use, to share common function that utilize either CI Library or your own library, across your Gas models/instances.

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

From above extension example, if you are ready to create your own, here litlle note you should remember :

- Your extension, should prefixed with **Gas_extension_** , then you can adding your extension name after it. 
- Your extension, should implements **Gas_extension** interface.
- Your extension, should have **__init($gas)** method (notice the double underscore, distungished it from your model init method).
- Your extension, should be under **application/libraries**.

Thats all about extension.

