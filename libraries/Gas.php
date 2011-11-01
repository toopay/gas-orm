<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Gas ORM Library
 *
 * A lighweight and scalable ORM for CodeIgniter
 * 
 * This class intend to use as semi-native ORM library for CI, 
 * based on the ActiveRecord pattern. This library uses CI stan-
 * dard DB utility packages also validation class.
 *
 * @package     Gas Library
 * @category    Libraries
 * @version     1.0.3
 * @author      Taufan Aditya A.K.A Toopay
 * @link        http://taufanaditya.com/gas-orm
 * @license     GPL
 */

 /* ------------------------------------------------------------------------------------------------- */
 /* ------------------------------------------------------------------------------------------------- */

/**
 * Gas Core Class.
 *
 * @package		Gas Library
 * @subpackage	Gas Core
 * @category    Libraries
 * @version     1.0.3
 */

class Gas_Core {

	public $table = '';

	public $primary_key = 'id';

	public $relations = array();

	public $errors = array();

	public $locked = FALSE;

	public $single = FALSE;


	protected $_config;

	protected $_fields;

	protected $_get_fields = array();

	protected $_get_child_fields = array();

	protected $_get_child_nodes = array();


	public static $childs = array();

	public static $childs_resource = array();

	public static $init = FALSE;

	public static $bureau;

	public static $ar_recorder = array();

	public static $post = FALSE;

	public static $join = FALSE;

	public static $with = FALSE;

	public static $with_models = array();


	static $transaction_pointer = FALSE;

	static $selector = FALSE;

	static $condition = FALSE;

	static $executor = FALSE;

	static $transaction_status = FALSE;

	static $transaction_executor = FALSE;
	

	protected static $_models;

	protected static $_rules = array();

	protected static $_set_fields = array();

	protected static $_error_callbacks = array();
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		$init = FALSE;

		$gas = $this->model();

		if (self::is_initialize() == FALSE)
		{
			$CI =& get_instance();

			$CI->config->load('gas', TRUE, TRUE);

			$this->_config = $CI->config->item('gas');
			
			$this->_scan_models();

			$this->_init();

			$init = TRUE;

			Gas_Core::$bureau = new Gas_Bureau($CI);
		}

		self::$bureau =& Gas_Core::recruit_bureau(); 

		self::$bureau->_models = self::$_models;

		self::$bureau->_config = $this->_config;

		self::$bureau->_set_fields = self::$_set_fields;

		if ($this->_config['autoload_models']) self::$bureau->load_model('*');

		if ($init) self::$init = TRUE;

		if (func_num_args() == 1)
		{
			$args = func_get_arg(0);

			if (isset($args['record'])) $this->_get_fields = Gas_Janitor::get_input(__METHOD__, $args['record'], FALSE, array());
		}

		log_message('debug', 'Gas ORM Core Class Initialized');
	}

	/**
	 * Produce an empty Gas model instance.
	 * 
	 * @param   string 
	 * @return  object  Gas Instance
	 */
	public static function factory($name, $records = array())
	{
		$model = $name;

		$gas = new $model($records);

		return $gas;
	}

	/**
	 * recruit_bureau
	 * 
	 * Calling Gas Bureau instance
	 * 
	 * @access public
	 * @return object  Gas Bureau Instance
	 */
	public static function &recruit_bureau()
	{
		return Gas_Core::$bureau;
	}

	/**
	 * is_initialize
	 * 
	 * Check Gas Core State
	 * 
	 * @access public
	 * @return bool
	 */
	public static function is_initialize()
	{
		return self::$init;
	}

	/**
	 * field
	 * 
	 * Creates a validation rule for each used field(s)
	 * 
	 * @access public
	 * @param  string
	 * @param  array
	 * @return array
	 */
	public static function field($type = '', $args = array())
	{
		$rules = array();
		
		if (preg_match('/^([^)]+)\[(.*?)\]$/', $type, $m) AND count($m) == 3)
		{
			$type = $m[1];

			$rules[] = 'max_length['.$m[2].']';
		}
		
		switch ($type) 
		{
			case 'auto':

				$rules[] = 'callback_auto_check'; 

				break;
			
			case 'char':

				$rules[] = 'callback_char_check'; 

				break;
				
			case 'int':

				$rules[] = 'integer';

				break;
			
			case 'email':

				$rules[] = 'valid_email';

				break;
		}
		
		return array('rules' => implode('|', array_merge($rules, $args)));
	}

	/**
	 * db
	 * 
	 * Creates a temporary DB instance
	 * 
	 * @access public
	 * @return void
	 */
	public function db()
	{
		$bureau = self::$bureau;

		return $bureau::engine();
	}

	/**
	 * list_models
	 * 
	 * Get list of available models
	 * 
	 * @access public
	 * @return array
	 */
	public function list_models()
	{
		return self::$_models;
	}

	/**
	 * with 
	 * 
	 * Eager loading pointer
	 * 
	 * @access public
	 * @return void
	 */
	public function with()
	{
		$args = func_get_args();

		$eager_load_models = Gas_Janitor::get_input(__METHOD__, $args, FALSE, array());

		if ( ! empty($eager_load_models))
		{
			self::$with = TRUE;

			self::$with_models = $eager_load_models;
		}

		return $this;
	}

	/**
	 * all
	 * 
	 * Fetch records
	 * 
	 * @access public
	 * @return object Gas Instance
	 */
	public function all()
	{
		$bureau = self::$bureau;

		$this->validate_table();
		
		$recorder = array('get' => array($this->table));

		Gas_Janitor::tape_record($this->model(), $recorder);

		$this->validate_join();

		return $bureau::compile($this->model(), self::$ar_recorder, $this->single);
	}
	
	/**
	 * find
	 * 
	 * Get record based by given primary key arguments
	 *
	 * @access	public
	 * @param   mixed
	 * @return	object Gas Instance
	 */
	public function find()
	{
		$args = func_get_args();

		$in = Gas_Janitor::get_input(__METHOD__, $args, TRUE);

		$this->single = count($in) == 1;

		return $this->find_where_in(array($this->primary_key, $in));
	}

	/**
	 * find_where_in
	 * 
	 * Get record based by given arguments
	 *
	 * @access	public
	 * @param   array
	 * @param   string
	 * @return	object Gas Instance
	 */
	public function find_where_in($args, $type = '')
	{
		$args = Gas_Janitor::get_input(__METHOD__, $args, TRUE);

		$this->where_in($args, $type);
		
		return $this->all();
	}

	/**
	 * find_where
	 * 
	 * Get record based by given arguments
	 *
	 * @access	public
	 *
	 */
	public function find_where($args, $limit = null, $offset = null, $type = '')
	{
		$args = Gas_Janitor::get_input(__METHOD__, $args, TRUE);

		if (is_int($limit))
		{
			if($limit == 1)  $this->single = TRUE;

			$recorder = array('limit' => array($limit, $offset));

			Gas_Janitor::tape_record($this->model(), $recorder);
			
		}

		if ($type == 'or')
		{
			$this->or_where($args);
		}
		else
		{
			$this->where($args);
		}

		return $this->all();
	}

	/**
	 * where
	 * 
	 * WHERE statement for conditional query
	 *
	 * @access	public
	 * @param   mixed
	 * @return	void
	 */
	public function where()
	{
		$args = func_get_args();

		$args = Gas_Janitor::get_input(__METHOD__, $args, TRUE);

		$recorder = array('where' => $args);
		
		Gas_Janitor::tape_record($this->model(), $recorder);

		return $this;
	}

	/**
	 * or_where
	 * 
	 * OR WHERE statement for conditional query
	 *
	 * @access	public
	 * @param   mixed
	 * @return	void
	 */
	public function or_where()
	{
		$args = func_get_args();

		$args = Gas_Janitor::get_input(__METHOD__, $args, TRUE);

		$recorder = array('or_where' => $args);
		
		Gas_Janitor::tape_record($this->model(), $recorder);

		return $this;
	}

	/**
	 * where_in
	 * 
	 * WHERE IN statement for conditional query
	 *
	 * @access	public
	 * @param   array
	 * @param   string
	 * @return	void
	 */
	public function where_in($args, $type = '')
	{
		$args = Gas_Janitor::get_input(__METHOD__, $args, TRUE);

		switch ($type)
		{
			case 'or':

				$recorder = array('or_where_in' => $args);

				break;

			case 'not';

				$recorder = array('where_not_in' => $args);

				break;

			case 'or_not';

				$recorder = array('or_where_not_in' => $args);

				break;

			default:

				$recorder = array('where_in' => $args);

				break;
		}
		
		Gas_Janitor::tape_record($this->model(), $recorder);

		return $this;
	}

	/**
	 * save
	 * 
	 * Save or Update a table
	 *
	 * @access	public
	 * @param   bool    whether to skip or not the validation process
	 * @return	int     affected rows
	 */
	public function save($check = FALSE)
	{
		$bureau = self::$bureau;

		$this->validate_table();

		if ($check)
		{
			$this->_init();

			$valid = $bureau->validate($this->model(), array_merge($this->_get_fields, self::$_set_fields), $this->_fields);

			if ( ! $valid) return FALSE;
		}

		if (empty($this->_get_fields))
		{
			$recorder = array('insert' => array($this->table, self::$_set_fields));
		}
		else
		{
			$identifier = $this->identifier();

			self::$ar_recorder = array();

			$recorder = array('where' => array($this->primary_key, $identifier));

			Gas_Janitor::tape_record($this->model(), $recorder);

			self::$_set_fields = array_merge($this->_get_fields, self::$_set_fields);

			$recorder = array('update' => array($this->table, self::$_set_fields));
		}

		Gas_Janitor::tape_record($this->model(), $recorder);

		Gas_Janitor::flush_post();

		self::$_set_fields = array();

		$bureau::compile($this->model(), self::$ar_recorder);

		return $this->db()->affected_rows();
		
	}

	/**
	 * delete
	 * 
	 * Delete record(s) based by given arguments
	 *
	 * @access	public
	 * @param   mixed
	 * @return	int     affected rows
	 */
	public function delete()
	{
		$bureau = self::$bureau;

		$this->validate_table();

		$args = func_get_args();

		$in = Gas_Janitor::get_input(__METHOD__, $args, FALSE, null);

		if (is_null($in)) 
		{
			$identifier = Gas_Janitor::get_input(__METHOD__, $this->identifier(), TRUE);

			$recorder = array('delete' => array($this->table, array($this->primary_key => $identifier)));
		}
		else
		{
			$this->where_in(array($this->primary_key, $in));

			$recorder = array('delete' => array($this->table));
		}

		Gas_Janitor::tape_record($this->model(), $recorder);

		Gas_Janitor::flush_post();

		self::$_set_fields = array();

		$bureau::compile($this->model(), self::$ar_recorder);

		return $this->db()->affected_rows();
	}

	/**
	 * set_message
	 * 
	 * Creates a message for custom callback function
	 * Note: The key name has to match the  function name that it corresponds to.
	 * 
	 * @access public
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public static function set_message($key, $msg, $field = null)
	{
		$field = Gas_Janitor::get_input(__METHOD__, $field, TRUE);
		
		self::$bureau->lang()->load('form_validation');
		
		if (FALSE === ($line = self::$bureau->lang()->line($key)))
		{
			$line = $msg;
		}

		self::$_error_callbacks[] = str_replace('%s', Gas_Janitor::set_label($field), $line);
	}
	
	/**
	 * errors
	 *
	 * Gets the error(s) message associated with validation process
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function errors($prefix = '', $suffix = '')
	{
		$validator = self::$bureau->validator();
		
		$prefix = ($prefix == '') ? '<p>' : $prefix;

		$suffix = ($suffix == '') ? '</p>' : $suffix;
		
		$errors = '';

		foreach (self::$_error_callbacks as $error)
		{
			$errors .= $prefix.$error.$suffix."\n";
		}

		$str_errors = $errors.$validator->error_string($prefix, $suffix);

		return $str_errors;
	}
	
	/**
	 * auto_check (custom callback function for checking auto field)
	 *
	 * @access	public
	 * @param	string
	 * @param	mixed
	 * @return	bool
	 */
	public function auto_check($field, $val)
	{
		if (is_null($val) or is_integer($val) or is_numeric($val)) return TRUE;
		
		static::set_message('auto_check', 'The %s field was an invalid autoincrement field.', $field);
		
		return FALSE;
	}
	
	/**
	 * char_check (custom callback function for checking varchar/char field)
	 *
	 * @access	public
	 * @param	string
	 * @param	mixed
	 * @return	bool
	 */
	public function char_check($field, $val)
	{
		if (is_string($val) or $val === '') return TRUE;
		
		static::set_message('char_check', 'The %s field was an invalid char field.', $field);
		
		return FALSE;
	}

	/**
	 * to_array
	 * 
	 * Output array of model attributes
	 *
	 * @access	private
	 * @return	string
	 *
	 */
	public function to_array()
	{
		return Gas_Janitor::to_array($this->_get_fields);
	}

	/**
	 * to_json
	 * 
	 * Output json of model attributes
	 *
	 * @access	private
	 * @return	string
	 *
	 */
	public function to_json()
	{
		return Gas_Janitor::to_json($this->_get_fields);
	}

	/**
	 * set_fields
	 * 
	 * Set model attributes
	 *
	 * @access	private
	 * @param   mixed
	 * @return	array
	 *
	 */
	public function set_fields($resource)
	{
		self::$_set_fields = (array) $resource;
		
		return $this;
	}

	/**
	 * set_record
	 * 
	 * Set CI records into model attributes
	 *
	 * @access	public
	 * @param   mixed
	 * @return	void
	 */
	public function set_record($resource)
	{
		$this->_get_fields = (array) $resource;
		
		return $this;
	}

	/**
	 * set_child
	 * 
	 * Set Gas child properties
	 *
	 * @access	public
	 * @param   string
	 * @return	void
	 */
	public function set_child($name, $node)
	{
		array_push($this->_get_child_fields, $name);

		array_push($this->_get_child_nodes, array($name => $node));
		
		return $this;
	}

	/**
	 * set_ar_record
	 * 
	 * Set CI AR attributes to a Gas model
	 *
	 * @access	public
	 * @param   mixed
	 * @return	void
	 */
	public function set_ar_record($condition)
	{
		self::$ar_recorder = $condition;

		return $this;
	}

	/**
	 * get_ar_record
	 * 
	 * Get CI AR attributes of a Gas model
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_ar_record()
	{
		return self::$ar_recorder;
	}

	/**
	 * model
	 * 
	 * Get Gas model name
	 *
	 * @access	public
	 * @return	string
	 *
	 */
	public function model($gas = null)
	{
		return is_null($gas) ? strtolower(get_class($this)) : strtolower(get_class($gas));
	}

	/**
	 * identifier
	 * 
	 * Get identifier key array
	 *
	 * @access	public
	 * @return	array
	 *
	 */
	public function identifier($column = null)
	{
		if ( ! is_null($column))
		{
			if (isset($this->_get_fields[$column]))
			{
				return $this->_get_fields[$column];	
			} 
			else
			{
				return;
			}
		}

		return $this->_get_fields[$this->primary_key];
	}

	/**
	 * validate_table
	 * 
	 * Validating whether current table is valid 
	 *
	 * @access	protected
	 * @param   sring
	 * @return	void
	 */
	protected function validate_table($table = null)
	{
		$table = (is_null($table)) ? $this->table : $table;
		
		if(empty($table))
		{
			$table = strtolower($this->model());
			
			$this->table = $table;
		}
		
		return $this;
	}

	/**
	 * validate_join
	 * 
	 * Validating whether JOIN statement has been declared
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function validate_join()
	{
		$this->validate_table();

		if (self::$join == TRUE)
		{
			self::$ar_recorder = Gas_Janitor::where_to_join(self::$ar_recorder, $this->table);
		}
		
		return $this;
	}


 	/**
	 * _scan_model
	 * 
	 * Scan model directories recursively and set global models collections
	 *
	 * @access	private
	 * @return	void
	 *
	 */
	private function _scan_models($path = null)
	{
		$models_dir = (is_null($path)) ? APPPATH.$this->_config['models_path'] : $path;

		if( ! is_dir($models_dir)) show_error('Unable to locate the models path you have specified: '.$models_dir);
		
		$files = scandir($models_dir);

		foreach ($files as $file) 
		{
		    if ($file == '.' OR $file == '..') continue;

		    $file = "$models_dir/$file";

		    if (is_dir($file))  $this->_scan_models($file);

		    if(strpos($file, $this->_config['models_suffix'].'.php') !== FALSE) 
			{
				$model = explode('/', $file);

				self::$_models[str_replace($this->_config['models_suffix'].'.php', '', $model[count($model)-1])] = $file;
			}
		}
		
		return $this;
	}

	/**
	 * __set
	 * 
	 * Overloading method to writing data to inaccessible properties.
	 *
	 * @access	public
	 * @param	string
	 * @param   array
	 * @return	mixed
	 */
	function __set($name, $args)
	{
		self::$_set_fields[$name] = $args;
	}

	/**
	 * __get
	 * 
	 * Overloading method utilized for reading data from inaccessible properties.
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 *
	 */
	function __get($name) 
 	{
 		$this->validate_table();

 		if (isset($this->_get_fields[$name])) return $this->_get_fields[$name];

 		if ( ! empty($this->_get_child_fields))
 		{
 			foreach ($this->_get_child_fields as $index => $child)
 			{
 				if ($name == $child)
 				{
 					$type = Gas_Janitor::identify_relations($this->relations, $child);

 					if($type == 'has_one' or $type == 'belongs_to')
 					{
		 				return $this->_get_child_nodes[$index][$child];
 					}

	 				return $this->_get_child_nodes[$index];
	 			}
 			}
 		}

 		if (isset(self::$_models[$name]) and ! isset($this->_set_fields[$name]))
 		{

 			$foreign_table = Gas_Janitor::get_input('Gas_Core::__get', Gas::factory($name)->table, FALSE, $name);

 			$foreign_key = Gas::factory($name)->primary_key;

 			return Gas_Bureau::generate_child($this->model(), $name, array($this->identifier()), $this->identifier($foreign_table.'_'.$foreign_key));
	 	}
 	}

 	/**
	 * __call
	 * 
	 * Overloading method triggered when invoking special method.
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	void
	 */
	function __call($name, $args)
	{
		$this->validate_table();

		$bureau = self::$bureau;

		$engine = $bureau::engine();

		if (empty($this->table)) $this->table = $this->model();
		
		if ($name == 'has_result')
		{
			return (bool) count($this->_get_fields) > 0;
		}
		
		elseif ($name == 'fill')
		{

			$input = array();

			$raw_input = array_shift($args);

			$post = array_shift($args);

			if ($post)
			{
				self::$post = TRUE;

				$_POST = $raw_input;

				self::$_set_fields = $raw_input;

			}
			elseif (isset($_POST))
			{
				if ($_POST == $raw_input)
				{
					self::$post = TRUE;

					self::$_set_fields = $_POST;
				}
				else
				{
					self::$_set_fields = $raw_input;
				}
			}
			else
			{
				self::$_set_fields = $raw_input;
			}

			return $this;

		}
		elseif ($name == 'filled_fields')
		{

			return self::$_set_fields;

		}
		elseif (preg_match('/^find_by_([^)]+)$/', $name, $m) AND count($m) == 2)
		{

			$field = $m[1];

			$value = array_shift($args);

			$limit = array_shift($args);

			$offset = array_shift($args);
			
			return $this->find_where(array($field => $value), $limit, $offset);

		}
		elseif (preg_match('/^(min|max|avg|sum)$/', $name, $m) AND count($m) == 2)
		{

			if (empty($args)) $args = array($this->primary_key);
			
			$recorder = array('select_'.$m[1] => $args);
			
			Gas_Janitor::tape_record($this->model(), $recorder);

			$this->single = TRUE;

			return $this->all();

		}
		elseif (preg_match('/^(first|last)$/', $name, $m) AND count($m) == 2)
		{

			$column = array_shift($args);

			$order = is_null($column) ? $this->primary_key : $column;

			$by = ($m[1] == 'first') ? 'asc' : 'desc';

			$recorder = array('order_by' => array($order, $by));

			Gas_Janitor::tape_record($this->model(), $recorder);

			$this->single = TRUE;

			return $this->all();

		}
		elseif (preg_match('/^join_([^)]+)$/', $name, $m) AND count($m) == 2)
		{

			$joined_field = $m[1];

			$on = array_shift($args);
			
			$join_args = (is_string($on)) ? array($joined_field, $on) : array($joined_field);

			$recorder = array('join' => $join_args);

			Gas_Janitor::tape_record($this->model(), $recorder);

			self::$join = TRUE;
			
			return $this;

		}
		elseif (preg_match('/^([^)]+)_join_([^)]+)$/', $name, $m) AND count($m) == 3)
		{

			$allowed_type = array('left', 'right', 'outer', 'inner', 'left outer', 'right outer');

			$join_type = str_replace('_', ' ', $m[1]);

			$joined_field = $m[2];

			$on = array_shift($args);

			if (in_array($join_type, $allowed_type))
			{
				$join_args = (is_string($on)) ? array($joined_field, $on, $join_type) : array($joined_field, '', $join_type);
			}
			else
			{
				$join_args = (is_string($on)) ? array($joined_field, $on) : array($joined_field);
			}

			$recorder = array('join' => $join_args);

			Gas_Janitor::tape_record($this->model(), $recorder);

			self::$join = TRUE;
			
			return $this;

		}
		elseif ($name == 'last_id')
		{
			
			return $engine->insert_id();

		}
		elseif ($name == 'list_fields')
		{
			return $engine->list_fields($this->table);
		}
		elseif ($name == 'sql')
		{
			
			return $engine->last_query();

		}
		elseif ($name == 'group_by')
		{

			$recorder = array('group_by' => $args);

			Gas_Janitor::tape_record($this->model(), $recorder);

			$this->single = FALSE;

			return $this;

		}
		elseif ($name == 'like')
		{

			$recorder = array('like' => $args);

			Gas_Janitor::tape_record($this->model(), $recorder);

			$this->single = FALSE;

			return $this;

		}
		elseif (method_exists($engine, $name))
		{
			$executor = Gas_Janitor::$dictionary['executor'];

			$direct = array_splice($executor, -6);

			$tables = array_splice($executor, -2);

			$writes = array_splice($executor, -4);

			$argumental = $executor;

			if (in_array($name, $tables))
			{
				$args = array($this->table);
			}
			elseif (in_array($name, $argumental) or in_array($name, $writes))
			{
				$args = Gas_Janitor::get_input('Gas_Core::__call', $args, TRUE);
			}

			$recorder = array($name => $args);

			Gas_Janitor::tape_record($this->model(), $recorder);

			$medical_history = Gas_Janitor::diagnostic($name);

			if ($medical_history == 'executor' or $medical_history == 'transaction_status')
			{
				return $bureau::compile($this->model(), self::$ar_recorder);
			}
			elseif (strpos($name, 'join') !== FALSE)
			{
				self::$join = TRUE;
			}
			elseif ($name == 'limit')
			{
				if (isset($args[0]) and $args[0] == 1) $this->single = TRUE;
			}
			
			return $this;
		}
		
		return FALSE;
	}

}

 /* ------------------------------------------------------------------------------------------------- */
 /* ------------------------------------------------------------------------------------------------- */

/**
 * Gas Bureau Class.
 *
 * @package		Gas Library
 * @subpackage	Gas Bureau
 * @category    Libraries
 * @version     1.0.3
 */

class Gas_Bureau {

	public $_models = array();

	public $_loaded_models = array();
	
	protected $_CI;

	protected static $db;

	protected static $validator;
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->_CI = func_get_arg(0);
		
		if ( ! isset($this->_CI->db)) $this->_CI->load->database();

		self::$db = $this->_CI->db;

		if ( ! isset($this->_CI->form_validation)) $this->_CI->load->library('form_validation');

		self::$validator = $this->_CI->form_validation;

		log_message('debug', 'Gas ORM Bureau Class Initialized');
	}

	/**
	 * compile
	 * 
	 * Compile AR
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	response
	 */
	public static function compile($gas, $recorder, $limit = FALSE)
	{
		$tasks = Gas_Janitor::play_record($recorder);

		foreach ($tasks as $type => $task)
		{
			if ($gas::$$type == TRUE)
			{
				foreach($task as $action)
				{
					$motor = get_class(self::$db);

					if (is_callable($motor.'::'.key($action)))
					{
						$method = key($action);

						$args = array_shift($action);

						if ($type == 'executor' or $type == 'transaction_status')
						{
							$executor = Gas_Janitor::$dictionary['executor'];

							$operations = array_splice($executor, -8);

							$writes = array_splice($executor, -4);

							if ($method == 'get')
							{
								$result = Gas_Janitor::force_and_get(self::$db, $method, $args);

								$gas::$ar_recorder = array();

								return self::generator($gas, $result->result(), __FUNCTION__, $limit, $gas::$with);
							}
							elseif (in_array($method, $writes))
							{
								Gas_Janitor::force(self::$db, $method, $args);

								$gas::$ar_recorder = array();
								
								return self::$db->affected_rows();
							}

							Gas_Janitor::force(self::$db, $method, $args);

							$gas::$ar_recorder = array();
						}
						else
						{
							Gas_Janitor::force(self::$db, $method, $args);
						}
					}
				}

			}
		}

		return;
	}

	/**
	 * engine
	 * 
	 * return CI DB Object
	 *
	 * @access	public
	 * @return	object
	 */
	public static function engine()
	{
		return self::$db;
	}

	/**
	 * generator
	 * 
	 * Generate Gas based by AR
	 *
	 * @access	public
	 * @param	string
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @param	bool
	 * @param	bool
	 * @return	mixed
	 */
	public static function generator($gas, $resource, $method, $limit = FALSE, $with = FALSE, $locked = FALSE)
	{
		$primary_key = Gas::factory($gas)->primary_key;

		$instances = array();

		$eager_load_models = array();

		$eager_load_results = array();

		if (empty($resource)) 
		{
			if ($limit == TRUE)
			{
				return  FALSE;
			}
			else
			{
				return  $instances;
			}
		}

		if ($with)
		{
			$childs = array();

			$eager_load_models = $gas::$with_models;

			foreach ($eager_load_models as $child)
			{
				$childs[$child]['foreign_table'] = Gas_Janitor::get_input(__METHOD__, Gas::factory($child)->table, FALSE, $child);

				$childs[$child]['foreign_key'] = Gas::factory($child)->primary_key;
			}

			$ids = array();

			$fids = array();

			foreach ($resource as $single)
			{
				if (isset($single->$primary_key)) $ids[] = $single->$primary_key;

				foreach ($childs as $child)
				{
					$foreign_key = $child['foreign_table'].'_'.$child['foreign_key'];

					if (isset($single->$foreign_key)) $fids[] = $single->$foreign_key;
				}

			}

			foreach ($eager_load_models as $model)
			{
				$eager_load_results[$model] = self:: generate_child($gas, $model, $ids, $fids, TRUE);
			}

		}

		foreach ($resource as $record)
		{
			$model = ucfirst($gas);

			$instance = Gas::factory($model, array('record' => (array) $record));

			if ($with)
			{
				foreach ($eager_load_results as $child => $results)
				{
					$success = FALSE;

					$all_results = $results;

					$sample = is_array($results) ? array_shift($results) : array();

					if (empty($sample))
					{
						$instance->set_child($child, FALSE);
					}
					else
					{

						$identifier = $sample['identifier'];

						$self = $sample['self'];

						$eager_records = FALSE;

						foreach ($all_results as $result)
						{
							$eager_record = Gas_Janitor::get_input(__METHOD__, $result['record'], FALSE, array());

							if (isset($result['raw']) and ! empty($result['raw']))
							{
								if (isset($result['raw'][$identifier]))
								{
									$id = Gas_Janitor::get_input(__METHOD__, $result['raw'][$identifier], FALSE, '');
								}
								else
								{
									$id = FALSE;
								}
								
							}
							else
							{
								$id = Gas_Janitor::get_input(__METHOD__, $eager_record[$identifier], FALSE, '');
							}
							
							if ($id == $record->$primary_key)
							{
								if ($self)
								{
									$eager_records = Gas::factory($child, array('record' => $eager_record));

									continue;
								}
								else
								{
									$eager_records[] = Gas::factory($child, array('record' => $eager_record));
								}
							}
						}

						$instance->set_child($child, $eager_records);
					}
				}
			}

			if ($limit) return $instance;

			$instances[] = $instance;
		}

		return $instances;
	}

	/**
	 * generate_child
	 * 
	 * Generate Relationship Nodes
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	array
	 * @param	mixed
	 * @param	bool
	 * @return	mixed
	 */
	public static function generate_child($gas, $child, $identifiers = array(), $foreign_value = null, $eager_load = FALSE)
	{
		$global_identifier = '';

		$instance = Gas::factory($gas);

		$relations = $instance->relations;

		$table = Gas_Janitor::get_input(__METHOD__, $instance->table, FALSE, $gas);

		$primary_key = $instance->primary_key;

		$foreign_key = Gas::factory($child)->primary_key; 

		if (empty($relations) or ! is_array($relations)) show_error('Model founds, but missing relationship properties.');

		$peer_relation = Gas_Janitor::get_input(__METHOD__, Gas_Janitor::identify_relations($relations, $child), FALSE, '');

		if (empty($peer_relation)) show_error('Model founds, but missing relationship properties.');

		$self = FALSE;

		if ($peer_relation == 'has_one' or $peer_relation == 'has_many')
		{
			$new_identifier = $table.'_'.$primary_key;

			$global_identifier = $new_identifier;

			if (count($identifiers) == 1)
			{
				self::$db->where(array($new_identifier => $identifiers[0]))->from($child);
			}
			elseif (count($identifiers) > 1)
			{
				self::$db->where_in($new_identifier, $identifiers)->from($child);
			}	

			if ($peer_relation == 'has_one') $self = TRUE;
		}
		elseif ($peer_relation == 'belongs_to')
		{
			$global_identifier = $foreign_key;

			if (is_string($foreign_value) or is_numeric($foreign_value))
			{
				self::$db->where(array($foreign_key => $foreign_value))->limit(1)->from($child);
				
			}
			elseif (is_array($foreign_value))
			{
				self::$db->where_in($foreign_key, $foreign_value)->from($child);
			}

			$self = TRUE;
		}
		elseif ($peer_relation == 'has_and_belongs_to')
		{
			$guess_table = Gas_Janitor::combine($table, $child);

			foreach ($guess_table as $link_table)
			{
				if (self::$db->table_exists($link_table))
				{
					$pivot_table = $link_table;

					continue;
				}
			}

			$pivot_table = Gas_Janitor::get_input(__METHOD__, $pivot_table, TRUE);

			$origin_fields = self::$db->list_fields($child);

			$new_identifier = $table.'_'.$primary_key;

			$global_identifier = $new_identifier;

			self::$db->join($pivot_table, $child.'_'.$foreign_key.' = '.$foreign_key);

			if (count($identifiers) == 1)
			{
				self::$db->where(array($new_identifier => $identifiers[0]))->from($child);
			}
			elseif (count($identifiers) > 1)
			{
				self::$db->where_in($new_identifier, $identifiers)->from($child);
			}	
		}

		$q = self::$db->get();

		$res = $q->result_array();

		if (count($res) > 0) 
		{

			$many = array();

			foreach ($res as $one)
			{
				$raw = array();

				if($self and ! $eager_load) 
				{

					return Gas::factory($child, array('record' => $one));
				}

				if (isset($origin_fields))
				{
					if ($eager_load) $raw = $one;

					$keys = array_values($origin_fields);

					$values = array_keys($origin_fields);
					
					$levenshtein = array_combine($keys, $values);
					
					$one = array_intersect_ukey($one, $levenshtein, 'Gas_Janitor::intersect');
				}

				if ($eager_load)
				{
					$many[] = array('identifier' => $global_identifier, 'self' => $self, 'type' => $peer_relation, 'record' => $one, 'raw' => $raw);
				}
				else
				{
					$many[] = Gas::factory($child, array('record' => $one));
				}
				
			}

			return $many;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * load_model
	 * 
	 * Get model(s)'s public
	 *
	 * @access	public
	 * @param   mixed
	 * @return	void
	 */
	public function load_model($models = null)
	{
		if ($models == '*')
		{
			foreach ($this->_models as $model => $model_path)
			{
				$this->_loaded_models[] = $model;

				require_once $model_path;
			}
		}
		elseif (is_array($models))
		{
			foreach ($models as $model)
			{
				if( ! array_key_exists($model, $this->_models)) show_error('Unable to locate the models name you have specifieds: '.$model);

				$this->_loaded_models[] = $model;

				require_once $this->_models[$model];
			}
		}
		elseif (is_string($models))
		{
			if ( ! array_key_exists($models, $this->_models)) show_error('Unable to locate the models name you have specified: '.$models);

			$this->_loaded_models[] = $models;

			require_once $this->_models[$models];
		}
		
		return $this;
	}

	/**
	 * validate
	 * 
	 * Validation handler
	 *
	 * @access	public
	 * @param   string
	 * @param   array
	 * @param   array
	 * @return	bool
	 */
	public function validate($gas, $entries, $rules)
	{
		$gas = Gas_Janitor::get_input(__METHOD__, $gas, TRUE);

		$entries = Gas_Janitor::get_input(__METHOD__, $entries, TRUE);

		$rules = Gas_Janitor::get_input(__METHOD__, $rules, TRUE);

		$validator = self::$validator;

		foreach ($rules as $field => $rule)
		{
			$validator->set_rules($field, Gas_Janitor::set_label($field), $rule['rules']);
		}

		if($validator->run() === TRUE)
		{
			$success = TRUE;
		}
		else
		{
			$success = FALSE;
		}

		foreach ($rules as $field => $rule)
		{
			if(strpos($rule['rules'], 'callback'))
			{
				foreach (explode('call', $rule['rules']) as $callback_rule)
				{
					if (substr($callback_rule, 0, 5) == 'back_')
					{
						$rule = substr($callback_rule, 5);
					
						if ( ! method_exists($gas, $rule))	continue;
						
						if (call_user_func_array(array($gas, $rule), array($field, $entries[$field])) == FALSE)
						{
							$success = FALSE;
						}
					}
				}
			}
		}

		return $success;
	}

	/**
	 * validator
	 * 
	 * CI Form validation class object
	 *
	 * @access	public
	 * @return	object
	 */
	public function validator()
	{
		return self::$validator;
	}

	/**
	 * lang
	 * 
	 * CI Language class object
	 *
	 * @access	public
	 * @return	object
	 */
	public function lang()
	{
		return $this->_CI->lang;
	}
	
	/**
	 * __call
	 * 
	 * Overloading method triggered when invoking special method.
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	void
	 */
	function __call($name, $args)
	{
		$db = get_class(self::$db);

		if (is_callable($db.'::'.$name))
		{
			call_user_func_array(array(self::$db, $name), $args);
		}

		return $this;
	}

}

 /* ------------------------------------------------------------------------------------------------- */
 /* ------------------------------------------------------------------------------------------------- */

/**
 * Gas Janitor Class.
 *
 * @package		Gas Library
 * @subpackage	Gas Janitor
 * @category    Libraries
 * @version     1.0.3
 */

class Gas_Janitor {

	static $dictionary = array(

		'transaction_pointer' => array('trans_off', 'trans_start', 'trans_begin'),

		'selector' => array('select', 'select_max', 'select_min', 'select_avg', 'select_sum'),

		'condition' => array('join', 'where', 'or_where', 'where_in', 'or_where_in', 'where_not_in', 'or_where_not_in', 'like', 'or_like', 'not_like', 'or_not_like', 'group_by', 'distinct', 'having', 'or_having', 'order_by', 'limit', 'set'),

		'executor' => array('get', 'count_all_results', 'insert_string', 'update_string', 'query', 'insert', 'insert_batch', 'update', 'delete', 'empty_table', 'truncate', 'insert_id', 'count_all', 'affected_rows', 'platform', 'version', 'last_query'),

		'transaction_status' => array('trans_status'),

		'transaction_executor' => array('trans_complete', 'trans_rollback', 'trans_commit'),

	);

	/**
	 * diagnostic
	 *
	 * @access	public
	 * @param   string
	 * @return	string
	 */
	static function diagnostic($name)
	{
		foreach (self::$dictionary as $type => $nodes)
		{
			if (in_array($name, $nodes)) return $type;
		}

		return '';
	}

	/**
	 * identify_relations
	 *
	 * @access	public
	 * @param   array
	 * @param   string
	 * @return	string
	 */
	static function identify_relations($relations, $child)
	{
		$peer_relation = null;

		foreach ($relations as $type => $relation)
		{
			if (key($relation) == $child)
			{
 				$peer_relation = $type;
			}
		}

		return $peer_relation;
	}

	/**
	 * combine
	 *
	 * @access	public
	 * @param   array
	 * @return	array
	 */
	static function combine($a, $b)
	{
		return array($a.'_'.$b, $b.'_'.$a);
	}

	/**
	 * intersect
	 *
	 * @access	public
	 * @param   array
	 * @return	array
	 */
	static function intersect($a, $b)
	{
	    if ($a == $b)
	    {
	    	return 0;
	    }  
	    elseif ($a > $b)
	    {
	    	return 1;
	    }
	    
	    return -1;
	}

	/**
	 * new_record
	 *
	 * @access	public
	 * @return	array
	 */
	static function new_record()
	{
		return array_fill(0, count(self::$dictionary), array());
	}

	/**
	 * tape_record
	 *
	 * @access	public
	 * @param   string
	 * @param   array
	 * @return	void
	 */
	static function tape_record($gas, $recorder)
	{
		$success = FALSE;

		$recorder = self::get_input(__METHOD__, $recorder, FALSE, array());

		foreach (self::$dictionary as $type => $nodes)
		{
			foreach ($nodes as $node)
			{
				if (key($recorder) == $node)
				{
					$success = TRUE;

					$gas::$$type = TRUE;

					array_push($gas::$ar_recorder, $recorder);
				}
			}
		}

		return;
	}

	/**
	 * play_record
	 *
	 * @access	public
	 * @param   array
	 * @return	array
	 */
	static function play_record($recorder)
	{
		$blank_disc = self::new_record();

		$tasks = array_combine(array_keys(self::$dictionary), $blank_disc);

		foreach ($recorder as $task)
		{
			foreach (self::$dictionary as $type => $nodes)
			{
				foreach ($nodes as $node)
				{
					if (key($task) == $node)  array_push($tasks[$type], $task);
				}
			}
		}

		return $tasks;
	}

	/**
	 * force
	 *
	 * @access	public
	 * @param   string
	 * @param   string
	 * @param   array
	 * @return	void
	 */
	static function force($class, $method, $args)
	{
		$total_args = count($args);
			
		if ($total_args == 4)
		{
			$class->$method($args[0], $args[1], $args[2], $args[3]);
		}
		elseif ($total_args == 3)
		{
			$class->$method($args[0], $args[1], $args[2]);
		}
		elseif ($total_args == 2)
		{
			$class->$method($args[0], $args[1]);
		}
		elseif ($total_args == 1)
		{
			$class->$method($args[0]);
		}
		else
		{
			$class->$method();
		}

		return;
	}

	/**
	 * force_and_get
	 *
	 * @access	public
	 * @param   string
	 * @param   string
	 * @param   array
	 * @return	mixed
	 */
	static function force_and_get($class, $method, $args)
	{
		$total_args = count($args);
			
		if ($total_args == 4)
		{
			return $class->$method($args[0], $args[1], $args[2], $args[3]);
		}
		elseif ($total_args == 3)
		{
			return $class->$method($args[0], $args[1], $args[2]);
		}
		elseif ($total_args == 2)
		{
			return $class->$method($args[0], $args[1]);
		}
		elseif ($total_args == 1)
		{
			return $class->$method($args[0]);
		}
		else
		{
			return $class->$method();
		}
	}

	/**
	 * flush_post
	 *
	 * @access	public
	 * @return	void
	 */
	static function flush_post()
	{
		if (isset($_POST)) $_POST = array();
	}

	/**
	 * get_input
	 *
	 * @access	public
	 * @param   mixed
	 * @param   bool
	 * @param   bool
	 * @return	mixed
	 */
	static function get_input($method, $input, $die = FALSE, $default = FALSE)
	{
		if ( ! isset($input) or empty($input))
		{
			if ($die) show_error('Cannot continue executing '.$method.' without any passed parameter.');

			$input = $default;
		}

		return $input;
	}
	
	/**
	 * to_array
	 *
	 * @access	public
	 * @param   mixed
	 * @return	array
	 */
	static function to_array($var)
	{
		return (array) $var;
	}

	/**
	 * to_json
	 *
	 * @access	public
	 * @param   array
	 * @return	string
	 */
	static function to_json($var)
	{
		return json_encode($var);
	}

	/**
	 * where_to_join
	 *
	 * @access	public
	 * @param   array
	 * @param   string
	 * @return	array
	 */
	static function where_to_join($recorder, $table)
	{
		$condition = self::$dictionary['condition'];

		foreach ($condition as $node)
		{
			foreach ($recorder as $index => $statement)
			{
				$preserve = array('join', 'select');

				$type = key($statement);

				if ( ! in_array($type, $preserve) and $type == $node)
				{
					$recorder[$index][$node][0] = $table.'.'.$recorder[$index][$node][0];
				}
			}
		}

		return $recorder;
	}

	/**
	 * to_json
	 *
	 * @access	public
	 * @param   string
	 * @return	string
	 */
	static function set_label($field)
	{
		return str_replace(array('-', '_'), ' ', ucfirst($field));
	}

}

 /* ------------------------------------------------------------------------------------------------- */
 /* ------------------------------------------------------------------------------------------------- */

/**
 * Gas Class.
 *
 * @package		Gas Library
 * @subpackage	Gas
 * @category    Libraries
 * @version     1.0.3
 */

class Gas extends Gas_Core {

	/**
	 * _init 
	 * 
	 * Initialize method
	 * 
	 */
	function _init() {}

}