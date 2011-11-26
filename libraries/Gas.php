<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Gas ORM Library
 *
 * A lighweight and easy-to-use ORM for CodeIgniter
 * 
 * This class intend to use as semi-native ORM library for CI, 
 * based on the ActiveRecord pattern. This library uses CI stan-
 * dard DB utility packages also validation class.
 *
 * @package     Gas Library
 * @category    Libraries
 * @version     1.4.1
 * @author      Taufan Aditya A.K.A Toopay
 * @link        http://gasorm-doc.taufanaditya.com/
 * @license     BSD(http://gasorm-doc.taufanaditya.com/what_is_gas_orm.html#bsd)
 */

 /* ------------------------------------------------------------------------------------------------- */
 /* ------------------------------------------------------------------------------------------------- */

/**
 * Gas Core Class.
 *
 * @package     Gas Library
 * @subpackage	Gas Core
 * @category    Libraries
 * @version     1.4.1
 */

class Gas_core {

	const GAS_VERSION = '1.4.1';

	public $table = '';

	public $primary_key = 'id';

	public $relations = array();

	public $empty = TRUE;

	public $errors = array();

	public $locked = FALSE;

	public $single = FALSE;

	public $extensions = array();


	protected $_fields = array();

	protected $_unique_fields = array();

	protected $_ts_fields = array();

	protected $_unix_ts_fields = array();

	protected $_set_fields = array();

	protected $_get_fields = array();

	protected $_get_child_fields = array();

	protected $_get_child_nodes = array();

	protected $_get_reflection_fields = array();


	public static $loaded_models = array();

	public static $childs = array();

	public static $childs_resource = array();

	public static $init = FALSE;

	public static $bureau;

	public static $ar_recorder = array();

	public static $post = FALSE;

	public static $join = FALSE;

	public static $with = FALSE;

	public static $with_models = array();

	public static $config;

	public static $transaction_pointer = FALSE;

	public static $selector = FALSE;

	public static $condition = FALSE;

	public static $executor = FALSE;

	public static $transaction_status = FALSE;

	public static $transaction_executor = FALSE;

	public static $timestamps = array();

	public static $old_input = array();
	

	protected static $_models;

	protected static $_models_fields;

	protected static $_extensions;

	protected static $_rules = array();

	protected static $_error_callbacks = array();

	protected static $_errors_validation = array();

	protected static $_migrated = FALSE;

	
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

			$CI->config->load('migration', TRUE, TRUE);

			self::$config = $CI->config->item('gas');

			self::$config['migration_config'] = $CI->config->item('migration');

			Gas_core::$bureau = new Gas_bureau($CI);
			
			$this->_scan_models();

			$this->_scan_extensions();

			if (is_callable(array($this, '_init'), TRUE)) $this->_init();

			$init = TRUE;

			Gas_core::$old_input = (isset($_POST) and count($_POST) > 0) ? $CI->input->post() : $CI->input->get();

			log_message('debug', 'Gas ORM Core Class Initialized');
		}

		self::$bureau =& Gas_core::recruit_bureau(); 

		self::$bureau->_models = self::$_models;

		self::$bureau->_extensions = self::$_extensions;

		self::$bureau->_config = Gas_core::$config;

		if (Gas_core::config('autoload_models')) self::$bureau->load_item('*', 'models');

		if (Gas_core::config('autoload_extensions')) self::$bureau->load_item(Gas_core::config('extensions'), 'extensions');

		if (self::is_migrated() == FALSE and self::is_initialize() == FALSE)
		{
			$this->check_migration(self::$_models);
		}

		if (func_num_args() == 1)
		{
			$args = func_get_arg(0);

			if (isset($args['record']))
			{
				$this->_get_fields = Gas_janitor::get_input(__METHOD__, $args['record'], FALSE, array());

				$this->empty = (bool) (count($this->_get_fields) == 0);
			}
		}

		if ($init) self::$init = TRUE;
	}

	/**
	 * version
	 * 
	 * Gas Version
	 * 
	 * @access public
	 * @return string
	 */
	public static function version()
	{
		return Gas_core::GAS_VERSION;
	}

	/**
	 * Produce an empty Gas model instance.
	 * 
	 * @access  public 
	 * @param   string 
	 * @param   array 
	 * @param   bool 
	 * @return  object  Gas Instance
	 */
	public static function factory($name, $records = array(), $init = TRUE)
	{
		$model = $name;

		if ( ! class_exists($model)) show_error(Gas_core::tell('models_not_found', $model));

		$gas = new $model($records);

		if (is_callable(array($gas, '_init'), TRUE) and $init == TRUE)
		{
			if ( ! isset(self::$_models_fields[$name]))
			{
				$gas->_init();

				self::$loaded_models[$name] = TRUE;

				self::$_models_fields[$name]['fields'] = $gas->_fields;

				self::$_models_fields[$name]['unique_fields'] = $gas->_unique_fields;

				self::$_models_fields[$name]['ts_fields'] = $gas->_ts_fields;

				self::$_models_fields[$name]['unix_ts_fields'] = $gas->_unix_ts_fields;
			}
			else
			{
				$gas->_fields = self::$_models_fields[$name]['fields'];

				$gas->_unique_fields = self::$_models_fields[$name]['unique_fields'];

				$gas->_ts_fields = self::$_models_fields[$name]['ts_fields'];

				$gas->_unix_ts_fields = self::$_models_fields[$name]['unix_ts_fields'];
			}
		}

		return $gas;
	}

	/**
	 * Database connection 
	 * 
	 * @access  public 
	 * @param   mixed 
	 * @return  void 
	 */
	public static function connect($dsn = null)
	{
		$dsn = Gas_janitor::get_input(__METHOD__, $dsn, TRUE);

		return self::$bureau->connect($dsn);
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
		return Gas_core::$bureau;
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
	 * is_migrated
	 * 
	 * Check Migration State
	 * 
	 * @access public
	 * @return bool
	 */
	public static function is_migrated()
	{
		return Gas_core::$_migrated;
	}

	/**
	 * Load model(s).
	 * 
	 * @access  public 
	 * @param   mixed
	 * @return  void
	 */
	public static function load_model($model)
	{
		return self::$bureau->load_item($model, 'models');
	}

	/**
	 * Load extension(s).
	 * 
	 * @access  public 
	 * @param   mixed
	 * @return  void
	 */
	public static function load_extension($extension)
	{
		return self::$bureau->load_item($extension, 'extensions');
	}

	/**
	 * Generate resource reports
	 * 
	 * @access  public 
	 * @return  array
	 */
	public static function reports()
	{
		return self::$bureau->log_resource();
	}

	/**
	 * Stop and flush all caching process/resources
	 * 
	 * @access  public 
	 * @return  void
	 */
	public static function flush_cache()
	{
		return self::$bureau->reset_cache();
	}

	/**
	 * field
	 * 
	 * Creates a validation rule for each used field(s)
	 * 
	 * @access public
	 * @param  string
	 * @param  array
	 * @param  string
	 * @return array
	 */
	public static function field($type = '', $args = array(), $schema = '')
	{
		$rules = array();

		$args = is_array($args) ? $args : (array) $args;

		$annotations = array();

		if (preg_match('/^([^)]+)\[(.*?)\]$/', $type, $m) AND count($m) == 3)
		{
			$type = $m[1];

			$constraint = explode(',', $m[2]);

			if (count($constraint) == 2)
			{
				$rules[] = 'min_length['.trim($constraint[0]).']';

				$rules[] = 'max_length['.trim($constraint[1]).']';

				$annotations[] = trim($constraint[1]);
			}
			else
			{
				$rules[] = 'max_length['.$constraint[0].']';

				$annotations[] = $constraint[0];
			}
		}
		
		switch ($type) 
		{
			case 'auto':

				$rules[] = 'callback_auto_check'; 

				$annotations[] = 'INT';

				$annotations[] = 'unsigned';

				$annotations[] = 'auto_increment';

				break;

			case 'datetime':

				$rules[] = 'callback_date_check'; 

				$annotations[] = 'DATETIME';

				break;
			
			case 'string':

				$rules[] = 'callback_char_check'; 

				$annotations[] = 'TEXT';

				break;

			case 'spatial':
				
				$rules[] = 'callback_char_check'; 

				$annotations[] = 'GEOMETRY';

				break;

			case 'char':

				$rules[] = 'callback_char_check'; 

				$annotations[] = 'VARCHAR';

				break;

			case 'numeric':

				$rules[] = 'numeric'; 

				$annotations[] = 'TINYINT';

				break;
				
			case 'int':

				$rules[] = 'integer';

				$annotations[] = 'INT';

				break;
			
			case 'email':

				$rules[] = 'valid_email';

				$annotations[] = 'VARCHAR';

				break;
		}

		$other_annotations = explode(',', $schema);

		if ( ! empty($other_annotations))
		{
			$other_annotations = Gas_janitor::arr_trim($other_annotations);

			$annotations = array_merge($annotations, $other_annotations);
		}
		
		return array('rules' => implode('|', array_merge($rules, $args)), 'annotations' => $annotations);
	}

	public function check_migration()
	{
		if ( ! self::is_migrated())
		{
			self::$_migrated = TRUE;

			return self::$bureau->check_auto_migrate(self::$_models);
		}

		return;
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
		return Gas_bureau::engine();
	}

	/**
	 * list_all_models
	 * 
	 * Get list of available models with its fields
	 * 
	 * @access public
	 * @param  string
	 * @return array
	 */
	public static function list_all_models($model = '')
	{
		if (isset(self::$_models_fields[$model])) return self::$_models_fields[$model];

		return self::$_models_fields;
	}

	/**
	 * set_type
	 * 
	 * Get recent type value
	 * 
	 * @access public
	 * @return array
	 */
	public function set_type($type, $val)
	{
		return self::$$type = $val;
	}

	/**
	 * get_type
	 * 
	 * Get recent type value
	 * 
	 * @access public
	 * @return array
	 */
	public function get_type($type)
	{
		return self::$$type;
	}

	/**
	 * get_with
	 * 
	 * Get eager loading flag
	 * 
	 * @access public
	 * @return bool
	 */
	public function get_with()
	{
		return self::$with;
	}

	/**
	 * get_with_models
	 * 
	 * Get eager loading models
	 * 
	 * @access public
	 * @return array
	 */
	public function get_with_models()
	{
		return self::$with_models;
	}

	/**
	 * set_with
	 * 
	 * Set eager loading flag
	 * 
	 * @access public
	 * @param  bool
	 * @return void
	 */
	public function set_with($pointer = FALSE)
	{
		self::$with = $pointer;

		return;
	}

	/**
	 * set_with_models
	 * 
	 * Set eager loading models
	 * 
	 * @access  public
	 * @param   array
	 * @return  array
	 */
	public function set_with_models($models = array())
	{
		self::$with_models = $models;

		return; 
	}

	/**
	 * config
	 * 
	 * Get Gas configuration
	 * 
	 * @access  public
	 * @param   string
	 * @return  array
	 */
	public function config($key = '')
	{
		if ( ! empty($key))
		{
			if (isset(Gas_core::$config[$key])) return Gas_core::$config[$key];

			return FALSE;
		}

		return self::$config;
	}

	/**
	 * with 
	 * 
	 * Eager loading pointer
	 * 
	 * @access  public
	 * @return  void
	 */
	public function with()
	{
		$args = func_get_args();

		$eager_load_models = Gas_janitor::get_input(__METHOD__, $args, FALSE, array());

		if ( ! empty($eager_load_models))
		{
			self::$with = TRUE;

			self::$with_models = $eager_load_models;
		}

		return $this;
	}

	/**
	 * produce
	 * 
	 * Compile record and return result
	 * 
	 * @access  public
	 * @return  mixed
	 */
	public function produce()
	{
		$bureau = self::$bureau;

		$is_extension = (bool) ! empty($this->extensions);

		$args = array($this->model(), self::$ar_recorder, $this->single, $is_extension);

		return Gas_janitor::force_and_get($bureau, 'compile', $args);
	}

	/**
	 * all
	 * 
	 * Fetch records
	 * 
	 * @access  public
	 * @return  object Gas Instance
	 */
	public function all()
	{
		$is_extension = (bool) ! empty($this->extensions);

		$this->validate_table();
		
		$recorder = array('get' => array($this->table));

		Gas_janitor::tape_record($this->model(), $recorder);

		$this->validate_join();

		$res = $this->produce();

		if ($is_extension)
		{
			$this->set_reflection_record($res);

			$this->set_record(array());

			return $this;
		}

		return $res;
	}
	
	/**
	 * find
	 * 
	 * Get record based by given primary key arguments
	 *
	 * @access  public
	 * @param   mixed
	 * @return  object Gas Instance
	 */
	public function find()
	{
		$args = func_get_args();

		$in = Gas_janitor::get_input(__METHOD__, $args, TRUE);

		$this->single = count($in) == 1;

		return $this->find_where_in(array($this->primary_key, $in));
	}

	/**
	 * find_where_in
	 * 
	 * Get record based by given arguments
	 *
	 * @access  public
	 * @param   array
	 * @param   string
	 * @return  object Gas Instance
	 */
	public function find_where_in($args, $type = '')
	{
		$args = Gas_janitor::get_input(__METHOD__, $args, TRUE);

		$this->where_in($args, $type);
		
		return $this->all();
	}

	/**
	 * find_where
	 * 
	 * Get record based by given arguments
	 *
	 * @access  public
	 * @param   array
	 * @param   int
	 * @param   int
	 * @param   string
	 * @return  object Gas Instance
	 */
	public function find_where($args, $limit = null, $offset = null, $type = '')
	{
		$args = Gas_janitor::get_input(__METHOD__, $args, TRUE);

		if (is_int($limit))
		{
			if($limit == 1)  $this->single = TRUE;

			$recorder = array('limit' => array($limit, $offset));

			Gas_janitor::tape_record($this->model(), $recorder);
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
	 * @access  public
	 * @param   mixed
	 * @return  void
	 */
	public function where()
	{
		$args = func_get_args();

		$args = Gas_janitor::get_input(__METHOD__, $args, TRUE);

		$recorder = array('where' => $args);
		
		Gas_janitor::tape_record($this->model(), $recorder);

		return $this;
	}

	/**
	 * or_where
	 * 
	 * OR WHERE statement for conditional query
	 *
	 * @access  public
	 * @param   mixed
	 * @return  void
	 */
	public function or_where()
	{
		$args = func_get_args();

		$args = Gas_janitor::get_input(__METHOD__, $args, TRUE);

		$recorder = array('or_where' => $args);
		
		Gas_janitor::tape_record($this->model(), $recorder);

		return $this;
	}

	/**
	 * where_in
	 * 
	 * WHERE IN statement for conditional query
	 *
	 * @access  public
	 * @param   array
	 * @param   string
	 * @return  void
	 */
	public function where_in($args, $type = '')
	{
		$args = Gas_janitor::get_input(__METHOD__, $args, TRUE);

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
		
		Gas_janitor::tape_record($this->model(), $recorder);

		return $this;
	}

	/**
	 * save
	 * 
	 * Save or Update a table
	 *
	 * @access  public
	 * @param   bool    whether to skip or not the validation process
	 * @param   bool    whether to return affected rows or not
	 * @return  int     affected rows
	 */
	public function save($check = FALSE, $skip_affected_rows = FALSE)
	{
		$created_ts_fields = array();

		$updated_ts_fields = array();

		$bureau = self::$bureau;

		$this->validate_table();

		if ($check)
		{
			if (is_callable(array($this, '_init'), TRUE) and empty($this->_fields)) $this->_init();

			$entries = $this->entries();

			if (is_callable(array($this, '_before_check'), TRUE)) $this->_before_check();

			$valid = $bureau->validate($this->model(), $entries, $this->_fields);

			$this->errors = self::$_errors_validation;

			self::$_errors_validation = array();

			if ( ! $valid) return FALSE;
		}

		if (is_callable(array($this, '_after_check'), TRUE)) $this->_after_check();

		if (is_callable(array($this, '_before_save'), TRUE)) $this->_before_save();

		if ( ! empty($this->_ts_fields) or ! empty($this->_unix_ts_fields))
		{
			$old_entries = $this->entries();

			if ( ! empty($this->_ts_fields))
			{
				$created_ts_fields[] = Gas_janitor::arr_timestamp($this->_ts_fields, TRUE);

				$updated_ts_fields[] = Gas_janitor::arr_timestamp($this->_ts_fields);
			}

			if ( ! empty($this->_unix_ts_fields))
			{
				$created_ts_fields[] = Gas_janitor::arr_unix_timestamp($this->_unix_ts_fields, TRUE);

				$updated_ts_fields[] = Gas_janitor::arr_unix_timestamp($this->_unix_ts_fields);
			}

			array_walk_recursive($created_ts_fields, 'Gas_janitor::extract_timestamp', 'created_ts');

			array_walk_recursive($updated_ts_fields, 'Gas_janitor::extract_timestamp', 'updated_ts');
		}

		if (empty($this->_get_fields))
		{
			if ( ! empty($this->_unique_fields))
			{
				$unique = $bureau->validate_unique($this->model(), $this->entries(), $this->_unique_fields);

				if ( ! $unique)
				{
					$this->errors = self::$_errors_validation;

					self::$_errors_validation = array();

					return FALSE;
				}
			}

			$this->_add_timestamps(TRUE);

			$recorder = array('insert' => array($this->table, $this->entries()));
		}
		else
		{
			$identifier = $this->identifier();

			self::$ar_recorder = array();

			$recorder = array('where' => array($this->primary_key, $identifier));

			Gas_janitor::tape_record($this->model(), $recorder);

			$this->_add_timestamps();

			$recorder = array('update' => array($this->table, $this->entries()));
		}

		Gas_janitor::tape_record($this->model(), $recorder);

		$res = $bureau->compile($this->model(), self::$ar_recorder, $skip_affected_rows);

		if (is_callable(array($this, '_after_save'), TRUE)) $this->_after_save();

		Gas_janitor::flush_post();

		$this->errors = array();

		self::$_errors_validation = array();

		$this->_set_fields = array();

		self::$_error_callbacks = array();

		return $res;
		
	}

	/**
	 * delete
	 * 
	 * Delete record(s) based by given arguments
	 *
	 * @access  public
	 * @param   mixed
	 * @return  int     affected rows
	 */
	public function delete()
	{
		$bureau = self::$bureau;

		$this->validate_table();

		$args = func_get_args();

		$in = Gas_janitor::get_input(__METHOD__, $args, FALSE, null);

		if (is_null($in)) 
		{
			$identifier = Gas_janitor::get_input(__METHOD__, $this->identifier(), TRUE);

			$this->_set_fields = array($this->primary_key => $identifier);

			if (is_callable(array($this, '_before_delete'), TRUE)) $this->_before_delete();

			$recorder = array('delete' => array($this->table, $this->_set_fields));
		}
		else
		{
			$this->_set_fields = array($this->primary_key, $in);

			if (is_callable(array($this, '_before_delete'), TRUE)) $this->_before_delete();

			$this->where_in($this->_set_fields);

			$recorder = array('delete' => array($this->table));
		}

		Gas_janitor::tape_record($this->model(), $recorder);

		Gas_janitor::flush_post();

		$this->_set_fields = array();

		$res = $bureau->compile($this->model(), self::$ar_recorder);

		if (is_callable(array($this, '_after_delete'), TRUE)) $this->_after_delete();

		return $res;
	}

	/**
	 * tell
	 * 
	 * Gas Languange file utilizer.
	 * 
	 * @access  public
	 * @param   string
	 * @param   string
	 * @return  string
	 */
	public static function tell($point, $parser_value = null)
	{
		if (is_object(self::$bureau))
		{
			$speaker = self::$bureau->lang();
		}
		else
		{
			$CI =& get_instance();

			$speaker = $CI->lang;
		}

		$speaker->load('gas');

		if (FALSE === ($msg = $speaker->line($point)))
		{
			$msg = '';
		}
		
		return (is_string($parser_value)) ? str_replace('%s', $parser_value, $msg) : $msg;
	}

	/**
	 * entries
	 * 
	 * Return mixed entries for saving data
	 *
	 * @access  public
	 * @return  array
	 */
	public function entries()
	{
		return is_array($this->_set_fields) ? array_merge($this->_get_fields, $this->_set_fields) : $this->_get_fields;
	}

	/**
	 * set_message
	 * 
	 * Creates a message for custom callback function
	 * Note: The key name has to match the  function name that it corresponds to.
	 * 
	 * @access  public
	 * @param   string
	 * @param   string
	 * @param   string
	 * @return  void
	 */
	public static function set_message($key, $msg, $field = null)
	{
		$field = Gas_janitor::get_input(__METHOD__, $field, TRUE);
		
		self::$bureau->lang()->load('form_validation');
		
		if (FALSE === ($line = self::$bureau->lang()->line($key)))
		{
			$line = $msg;
		}

		$str_error = str_replace('%s', Gas_janitor::set_label($field), $line);

		self::$_error_callbacks[] = $str_error;

		self::set_error($field, $str_error);
	}

	/**
	 * set_error
	 * 
	 * Stack any errors
	 * 
	 * @access  public
	 * @param   string
	 * @param   string
	 * @return  void
	 */
	public static function set_error($field = null, $error = null)
	{
		$field = Gas_janitor::get_input(__METHOD__, $field, TRUE);

		$error = Gas_janitor::get_input(__METHOD__, $error, TRUE);

		self::$_errors_validation[$field] = $error;
	}
	
	/**
	 * errors
	 *
	 * Gets the error(s) message associated with validation process
	 *
	 * @access  public
	 * @param   string
	 * @param   string
	 * @return  string
	 */
	public function errors($prefix = '', $suffix = '')
	{
		$prefix = ($prefix == '') ? '<p>' : $prefix;

		$suffix = ($suffix == '') ? '</p>' : $suffix;
		
		$errors = '';

		foreach ($this->errors as $error)
		{
			$errors .= $prefix.$error.$suffix."\n";
		}

		return $errors;
	}
	
	/**
	 * unique_check (used by validate_unique method)
	 *
	 * @access  public
	 * @param   string
	 * @param   mixed
	 * @return  bool
	 */
	public function unique_check($raw_fields, $val)
	{
		if (empty($val)) return TRUE;

		$fields = Gas_janitor::arr_trim(explode(',', $raw_fields));

		foreach ($fields as $field)
		{
			self::set_message('unique_check', 'The %s field should contain unique value(s).', $field);
		}
		
		return FALSE;
	}

	/**
	 * auto_check (custom callback function for checking auto field)
	 *
	 * @access  public
	 * @param   string
	 * @param   mixed
	 * @return  bool
	 */
	public function auto_check($field, $val)
	{
		if (empty($val) or is_integer($val) or is_numeric($val)) return TRUE;

		self::set_message('auto_check', 'The %s field was an invalid autoincrement field.', $field);
		
		return FALSE;
	}
	
	/**
	 * char_check (custom callback function for checking string field)
	 *
	 * @access  public
	 * @param   string
	 * @param   mixed
	 * @return  bool
	 */
	public function char_check($field, $val)
	{
		if (is_string($val) or $val === '') return TRUE;
		
		self::set_message('char_check', 'The %s field was an invalid char field.', $field);
		
		return FALSE;
	}

	/**
	 * date_check (custom callback function for checking datetime field)
	 *
	 * @access  public
	 * @param   string
	 * @param   mixed
	 * @return  bool
	 */
	public function date_check($field, $val)
	{
		if (strtotime($val) !== FALSE) return TRUE;
		
		self::set_message('date_check', 'The %s field was an invalid datetime field.', $field);
		
		return FALSE;
	}

	/**
	 * to_array
	 * 
	 * Output array of model attributes
	 *
	 * @access  public
	 * @return  string
	 *
	 */
	public function to_array()
	{
		return Gas_janitor::to_array($this->_get_fields);
	}

	/**
	 * to_json
	 * 
	 * Output json of model attributes
	 *
	 * @access  public
	 * @return  string
	 *
	 */
	public function to_json()
	{
		return Gas_janitor::to_json($this->_get_fields);
	}

	/**
	 * set_fields
	 * 
	 * Set model attributes
	 *
	 * @access  public
	 * @param   mixed
	 * @return  array
	 *
	 */
	public function set_fields($resource)
	{
		$this->_set_fields = (array) $resource;

		if (self::$post == TRUE)
		{
			$_POST = (array) $resource;
		}
		
		return $this;
	}

	/**
	 * set_record
	 * 
	 * Set CI records into model attributes
	 *
	 * @access  public
	 * @param   mixed
	 * @return  void
	 */
	public function set_record($resource)
	{
		$this->_get_fields = (array) $resource;
		
		return $this;
	}

	/**
	 * set_reflection_record
	 * 
	 * Set CI records into marked extension attributes
	 *
	 * @access  public
	 * @param   mixed
	 * @return  void
	 */
	public function set_reflection_record($resource)
	{
		$this->_get_reflection_fields = (array) $resource;
		
		return $this;
	}

	/**
	 * get_raw_record
	 * 
	 * Return raw record (mostly for extension)
	 *
	 * @access  public
	 * @param   mixed
	 * @return  void
	 */
	public function get_raw_record()
	{
		return $this->_get_reflection_fields;
	}

	/**
	 * set_child
	 * 
	 * Set Gas child properties
	 *
	 * @access  public
	 * @param   string
	 * @return  void
	 */
	public function set_child($name, $node)
	{
		array_push($this->_get_child_fields, $name);

		array_push($this->_get_child_nodes, array($name => $node));
		
		return $this;
	}

	/**
	 * add_ar_record
	 * 
	 * Push CI AR attributes to a Gas model
	 *
	 * @access  public
	 * @param   mixed
	 * @return  void
	 */
	public function add_ar_record($recorder)
	{
		array_push(self::$ar_recorder, $recorder);

		return $this;
	}


	/**
	 * set_ar_record
	 * 
	 * Set CI AR attributes to a Gas model
	 *
	 * @access  public
	 * @param   mixed
	 * @return  void
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
	 * @access  public
	 * @return  array
	 */
	public function get_ar_record()
	{
		return self::$ar_recorder;
	}

	/**
	 * get_extensions
	 * 
	 * Get all loaded and marked extensions
	 *
	 * @access  public
	 * @return  array
	 */
	public function get_extensions()
	{
		return $this->extensions;
	}

	/**
	 * model
	 * 
	 * Get Gas model name
	 *
	 * @access  public
	 * @return  string
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
	 * @access  public
	 * @return  array
	 *
	 */
	public function identifier($column = null)
	{
		if (is_null($column)) $column = $this->primary_key;
		
		if (isset($this->_get_fields[$column]))
		{
			return $this->_get_fields[$column];	
		} 
		else
		{
			return;
		}
	}

	/**
	 * validate_table
	 * 
	 * Validating whether current table is valid 
	 *
	 * @access  public
	 * @param   sring
	 * @return  void
	 */
	public function validate_table($table = null)
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
	 * @access  protected
	 * @return  void
	 */
	protected function validate_join()
	{
		$this->validate_table();

		if (self::$join == TRUE)
		{
			self::$ar_recorder = Gas_janitor::where_to_join(self::$ar_recorder, $this->table);
		}
		
		return $this;
	}

	/**
	 * _add_timestamps
	 * 
	 * Adding timestamps
	 *
	 * @access  private
	 * @param   bool
	 * @return  array
	 */
	public function _add_timestamps($new = FALSE)
	{
		$entries = $this->entries();

		$timestamps = array();

		$type = $new ? 'created_ts' : 'updated_ts';

		isset(Gas_core::$timestamps[$type]) and $timestamps = Gas_core::$timestamps[$type];

		foreach ($timestamps as $timestamp)
		{
			$entries = array_merge($entries, $timestamp);
		}

		unset(Gas_core::$timestamps[$type]);
		
		return $this->set_fields($entries);
	}

 	/**
	 * _scan_model
	 * 
	 * Scan model directories recursively and set global models collections
	 *
	 * @access  private
	 * @return  void
	 *
	 */
	private function _scan_models()
	{
		$models = array();

		$models_path = Gas_core::config('models_path');
		
		if (is_string($models_path))
	 	{
	 		$models[] = APPPATH.$models_path;
		}
		elseif (is_array($models_path))
		{
			$models = $models_path;
		}

		$model_type = 'models';

		$model_identifier = Gas_core::config('models_suffix').'.php';

		foreach ($models as $model)
		{
			if (is_dir($model)) $this->_scan_files(null, $model, $model_type, $model_identifier);
		}

		return $this;
	}

	/**
	 * _scan_extension
	 * 
	 * Scan all needed extensions and set global extensions collections
	 *
	 * @access  private
	 * @return  void
	 *
	 */
	private function _scan_extensions()
	{
		if (defined('FCPATH') and is_dir(FCPATH.'sparks'.DIRECTORY_SEPARATOR.'Gas-ORM'))
		{
			$extension_path = FCPATH.'sparks'.DIRECTORY_SEPARATOR.'Gas-ORM'.DIRECTORY_SEPARATOR.Gas_core::version().DIRECTORY_SEPARATOR.'libraries';
		}
		else
		{
			$extension_path = APPPATH.'libraries';
		}
		
		$extension_type = 'extensions';

		$extension_identifier = 'Gas_extension_';
		
		$this->_scan_files(null, $extension_path, $extension_type, $extension_identifier);

		return $this;
	}

	/**
	 * _scan_files
	 * 
	 * Scan files and set class collections
	 *
	 * @access  private
	 * @return  void
	 *
	 */
	private function _scan_files($path = null, $root_path, $type, $identifier)
	{
		$dir = (is_null($path)) ? $root_path : $path;

		if( ! is_dir($dir)) show_error(Gas_core::tell($type.'_not_found', $dir));
		
		$files = scandir($dir);

		foreach ($files as $file) 
		{
		    if ($file == '.' OR $file == '..' OR $file == '.svn') continue;

		    $file = $dir.DIRECTORY_SEPARATOR.$file;

		    if (is_dir($file))  $this->_scan_files($file, $root_path, $type, $identifier);

		    if(strpos($file, $identifier) !== FALSE) 
			{
				$item = explode(DIRECTORY_SEPARATOR, $file);

				if ($type == 'models')
				{
					self::$_models[str_replace($identifier, '', $item[count($item)-1])] = $file;
				}
				elseif ($type == 'extensions')
				{
					self::$_extensions[str_replace(array($identifier, '.php'), '', $item[count($item)-1])] = $file;
				}
			}
		}
		
		return $this;
	}

	/**
	 * __set
	 * 
	 * Overloading method to writing data to inaccessible properties.
	 *
	 * @access  public
	 * @param   string
	 * @param   array
	 * @return  mixed
	 */
	function __set($name, $args)
	{
		$this->_set_fields[$name] = $args;

		if (self::$post == TRUE)
		{
			$_POST[$name] = $args;
		}
	}

	/**
	 * __get
	 * 
	 * Overloading method utilized for reading data from inaccessible properties.
	 *
	 * @access  public
	 * @param   string
	 * @return  mixed
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
 					$type = Gas_janitor::identify_relations($this->relations, $child);

 					if($type == 'has_one' or $type == 'belongs_to')
 					{
		 				return $this->_get_child_nodes[$index][$child];
 					}
 					else
 					{
 						return ( ! $this->_get_child_nodes[$index][$child]) ? array() : $this->_get_child_nodes[$index][$child];
 					}
	 			}
 			}
 		}

 		if (isset(self::$_models[$name]) and ! isset($this->_set_fields[$name]))
 		{
 			$link = array();

 			list($parent_table, $parent_key, $parent_relations) = Gas_janitor::identify_meta($this->model());

 			list($child_table, $child_key, $child_relations) = Gas_janitor::identify_meta($name);

 			$peer_relation = Gas_janitor::get_input('Gas_core::__get', Gas_janitor::identify_relations($parent_relations, $name), FALSE, '');

			list($through, $custom_table, $custom_key, $self_ref) = Gas_janitor::identify_custom_setting($parent_relations, $peer_relation, $name);

 			$foreign_table = $child_table;

 			$foreign_key = $child_key;

 			$identifier = ($custom_key !== '') ? $custom_key : $foreign_table.'_'.$foreign_key;

 			$key = ($peer_relation == 'belongs_to') ? $identifier : $parent_key;

 			if($through !== '')
 			{
 				$tree = array(

 					'relations' => $child_relations,

 					'table' => $foreign_table,

 					'key' => $foreign_key,

 					'child' => $this->model(),
	 			);

 				$link = Gas_janitor::identify_link($through, $identifier, $tree);

 				if ($peer_relations = 'belongs_to') $identifier = $parent_key;
 			}

 			return Gas_bureau::generate_child($this->model(), $name, $link, array($this->identifier($key)), $this->identifier($identifier));
	 	}
	 	elseif (isset(self::$bureau->_loaded_components['extensions']) and ($extensions = self::$bureau->_loaded_components['extensions']))
 		{
 			if (in_array($name, $extensions))
 			{
 				$this->extensions[$name] = 'Gas_extension_'.$name;

	 			return $this;
	 		}
 		}
 	}

 	/**
	 * __call
	 * 
	 * Overloading method triggered when invoking special method.
	 *
	 * @access  public
	 * @param   string
	 * @param   array
	 * @return  void
	 */
	function __call($name, $args)
	{
		$this->validate_table();

		$engine = $this->db();
		
		$extensions = $this->get_extensions();

		if (empty($this->table)) $this->table = $this->model();
		
		if ($name == 'list_models')
		{
			return self::$_models;
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

				$this->_set_fields = $raw_input;
			}
			elseif (isset($_POST))
			{
				if ($_POST == $raw_input)
				{
					self::$post = TRUE;

					$this->_set_fields = $_POST;
				}
				else
				{
					$this->_set_fields = $raw_input;
				}
			}
			else
			{
				$this->_set_fields = $raw_input;
			}

			return $this;
		}
		elseif ($name == 'filled_fields')
		{
			return $this->_set_fields;
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
			
			Gas_janitor::tape_record($this->model(), $recorder);

			$this->single = TRUE;

			return $this->all();
		}
		elseif (preg_match('/^(first|last)$/', $name, $m) AND count($m) == 2)
		{
			$column = array_shift($args);

			$order = is_null($column) ? $this->primary_key : $column;

			$by = ($m[1] == 'first') ? 'asc' : 'desc';

			$recorder = array('order_by' => array($order, $by));

			Gas_janitor::tape_record($this->model(), $recorder);

			$this->single = TRUE;

			return $this->all();
		}
		elseif (preg_match('/^join_([^)]+)$/', $name, $m) AND count($m) == 2)
		{
			$joined_field = $m[1];

			$on = array_shift($args);
			
			$join_args = (is_string($on)) ? array($joined_field, $on) : array($joined_field);

			$recorder = array('join' => $join_args);

			Gas_janitor::tape_record($this->model(), $recorder);

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

			Gas_janitor::tape_record($this->model(), $recorder);

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
		elseif ($name == 'field_exist')
		{
			$field = array_shift($args);

			return $engine->field_exist($field, $this->table);
		}
		elseif ($name == 'field_data')
		{
			return $engine->field_data($this->table);
		}
		elseif ($name == 'last_sql')
		{
			return $engine->last_query();
		}
		elseif ($name == 'all_sql')
		{
			return $engine->queries;
		}
		elseif ($name == 'group_by')
		{
			$recorder = array('group_by' => $args);

			Gas_janitor::tape_record($this->model(), $recorder);

			$this->single = FALSE;

			return $this;
		}
		elseif ($name == 'like')
		{
			$recorder = array('like' => $args);

			Gas_janitor::tape_record($this->model(), $recorder);

			$this->single = FALSE;

			return $this;
		}
		elseif (method_exists($engine, $name))
		{
			$executor = Gas_janitor::$dictionary['executor'];

			$direct = array_splice($executor, -5);

			$tables = array_splice($executor, -3);

			$writes = array_splice($executor, -4);

			$argumental = $executor;

			if (in_array($name, $tables))
			{
				$args = array($this->table);
			}
			elseif (in_array($name, $argumental) or in_array($name, $writes))
			{
				$args = Gas_janitor::get_input('Gas_core::__call', $args, TRUE);
			}

			$recorder = array($name => $args);

			Gas_janitor::tape_record($this->model(), $recorder);

			$medical_history = Gas_janitor::diagnostic($name);

			if ($medical_history == 'executor' or $medical_history == 'transaction_status')
			{
				return $this->produce();
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
		elseif ( ! empty($extensions))
		{
			foreach ($extensions as $extension => $extension_class)
			{
				if (is_callable(array($extension_class, $name), TRUE))
				{
					$ext = new $extension_class;

					if($ext instanceof Gas_extension) 
					{
						$args = func_get_args();

						$ext->__init($this);

						return Gas_janitor::force_and_get($ext, $name, $args[1]);
					}
				}
			}
		}
		
		return FALSE;
	}

}

 /* ------------------------------------------------------------------------------------------------- */
 /* ------------------------------------------------------------------------------------------------- */

/**
 * Gas Bureau Class.
 *
 * @package     Gas Library
 * @subpackage	Gas Bureau
 * @category    Libraries
 * @version     1.4.1
 */

class Gas_bureau {

	public $_models = array();

	public $_extensions = array();

	public $_loaded_components = array();
	
	protected $_CI;

	protected $_engine;

	protected static $auto_models_success = FALSE;

	protected static $auto_modelling = FALSE;

	protected static $auto_migrate = FALSE;

	protected static $execute_migrate = FALSE;

	protected static $count_migrate = 0;

	protected static $db;

	protected static $validator;

	protected static $task_manager;

	protected static $thread_resource;

	protected static $empty_executor = array('writes' => FALSE, 'operation' => FALSE);

	protected static $cache_resource;

	protected static $cache_key;

	protected static $resource_state;
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->_CI = func_get_arg(0);

		if ( ! isset($this->_CI->db))
		{
			$this->_engine = $this->_CI->load->database('default', TRUE);
		}
		else
		{
			$this->_engine = $this->_CI->db;
		}

		self::$db = $this->_engine;

		$this->_CI->Gas_DB = self::$db;

		if ( ! class_exists('CI_Form_validation')) $this->_CI->load->library('form_validation');

		self::$validator = $this->_CI->form_validation;

		$auto_create_models = FALSE;

		$auto_create_tables = FALSE;

		if (defined('CI_VERSION') and strpos(CI_VERSION, '2.1') === 0)
		{
			if (Gas_core::config('auto_create_models'))
			{
				$auto_create_models = Gas_core::config('auto_create_models');
			}
			if (Gas_core::config('auto_create_tables'))
			{
				$auto_create_tables = Gas_core::config('auto_create_tables');

				if ($auto_create_models and $auto_create_tables)
				{
					show_error(Gas_core::tell('both_auto_error'));
				}
			}

			if ( ! function_exists('write_file')) $this->_CI->load->helper('file');

			if ($auto_create_models)
			{
				self::$auto_migrate = TRUE;

				self::generate_models();

				self::$auto_models_success = TRUE;
			}

			if ($auto_create_tables)
			{
				self::$auto_migrate = TRUE;

				self::$execute_migrate = TRUE;
			}
		}
		
		log_message('debug', 'Gas ORM Bureau Class Initialized');
	}

	/**
	 * do_compile
	 * 
	 * Compile AR
	 *
	 * @access  public
	 * @param   string
	 * @param   array
	 * @param   bool
	 * @param   bool
	 * @return  response
	 */
	public static function do_compile($gas, $recorder, $limit = FALSE, $raw = FALSE)
	{
		$tasks = Gas_janitor::play_record($recorder);

		self::cache_start($tasks);

		$motor = get_class(self::$db);

		$bundle = array(

			'engine' => $motor,

			'compiler' => array(

				'gas' => $gas,

				'limit' => $limit,

				'raw' => $raw,

			),

			'flag' => array('condition', 'selector')

		);

		if ( ! empty ($tasks['executor']))
		{
			$executor = Gas_janitor::$dictionary['executor'];

			$operations = array_splice($executor, -8);

			$writes = array_splice($executor, -4);

			$bundle['executor_list'] = array(

				'operation' => $operations, 

				'writes' => $writes
			
			);
		}

		self::$task_manager = $bundle;

		array_walk($tasks, 'Gas_bureau::sort_task');

		$resource = self::$thread_resource and self::$thread_resource = null;
		
		return $resource;
	}

	/**
	 * sort_task
	 * 
	 * Sort record compilation
	 *
	 * @access  public
	 * @param   array
	 * @param   int
	 * @return  response
	 */
	public static function sort_task($tasks, $key)
	{
		if (empty($tasks) or empty(self::$task_manager)) return;

		array_walk($tasks, 'Gas_bureau::do_task', $key);

		return;
	}

	/**
	 * do_task
	 * 
	 * Do each record task
	 *
	 * @access  public
	 * @param   array
	 * @param   string
	 * @param   string
	 * @return  response
	 */
	public static function do_task($arguments, $key, $task)
	{
		if (empty(self::$task_manager)) return;

		$flag = ! in_array($task, self::$task_manager['flag']);

		$action = key($arguments);

		$args = array_shift($arguments);

		if ($flag)
		{
			$compiler = self::$task_manager['compiler'];

			$is_transaction = Gas::factory($compiler['gas'], array(), FALSE)->get_type('transaction_pointer');

			Gas::factory($compiler['gas'], array(), FALSE)->set_ar_record(array());

			if ($action == 'get' and ($read_arguments = $args))
			{
				$resource_name = array_shift($read_arguments);

				if (self::validate_cache() and self::changed_resource($resource_name) == FALSE)
				{
					$result = self::fetch_cache();
				}
				else
				{
					$result = Gas_janitor::force_and_get(self::$db, $action, $args);

					self::cache_end($result);
				}
				
				if ($compiler['raw'] === TRUE) 
				{
					$res = $result->result_array();
				}
				else
				{
					$with = Gas::factory($compiler['gas'], array(), FALSE)->get_with();

					$res = self::generator($compiler['gas'], $result->result(), __FUNCTION__, $compiler['limit'], $with);
				}

				self::$task_manager = array();

				self::$thread_resource = $res;

				return;
			}
			
			if (isset(self::$task_manager['executor_list']))
			{
				$executor_list = self::$task_manager['executor_list'];
			}
			else
			{
				$executor_list = self::$empty_executor;
			}

			$writes = Gas_janitor::get_input(__METHOD__, $executor_list['writes'], FALSE, '');

			$operations = Gas_janitor::get_input(__METHOD__, $executor_list['operation'], FALSE, '');

			if ($task == 'transaction_status')
			{
				$res = Gas_janitor::force_and_get(self::$db, $action, $args);
			}
			elseif ($task == 'transaction_executor')
			{
				$res = Gas_janitor::force(self::$db, $action, $args);

				Gas::factory($compiler['gas'], array(), FALSE)->set_type('transaction_pointer', FALSE);

				Gas::factory($compiler['gas'], array(), FALSE)->set_type('transaction_executor', FALSE);
			}
			elseif ( ! $is_transaction and in_array($action, $writes) and ($write_arguments = $args))
			{
				$resource_name = array_shift($write_arguments);
				
				self::track_resource($resource_name, $action);

				Gas_janitor::force(self::$db, $action, $args);
				
				$res = ($compiler['limit']) ? TRUE : self::$db->affected_rows();

				self::$task_manager = array();
			}
			elseif ( ! $is_transaction and in_array($action, $operations))
			{
				array_splice($operations, 2);

				$non_explicit = $operations;

				if (in_array($action, $non_explicit))
				{
					$res = Gas_janitor::force(self::$db, $action, $args);
				}
				else
				{
					$res = Gas_janitor::force_and_get(self::$db, $action, $args);
				}
			}
			else
			{
				$return = FALSE;

				if ($action == 'query' and ($sample_args = $args))
				{
					$return = (strpos(strtolower(array_shift($sample_args)), 'select') === 0);
				}

				if ($return)
				{
					$result = Gas_janitor::force_and_get(self::$db, $action, $args);

					if ($compiler['raw'] === TRUE) 
					{
						$res = $result->result_array();
					}
					else
					{
						$with = Gas::factory($compiler['gas'], array(), FALSE)->get_with();

						$res = self::generator($compiler['gas'], $result->result(), __FUNCTION__, $compiler['limit'], $with);
					}

					self::$task_manager = array();

					self::$thread_resource = $res;

					return;
				}
				else
				{
					$res = Gas_janitor::force(self::$db, $action, $args);
				}
			}

			self::$thread_resource = $res;

			return;
		}
		else
		{
			return Gas_janitor::force(self::$db, $action, $args);
		}
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
	 * @access  public
	 * @param   string
	 * @param   mixed
	 * @param   string
	 * @param   bool
	 * @param   bool
	 * @param   bool
	 * @return  mixed
	 */
	public static function generator($gas, $resource, $method, $limit = FALSE, $with = FALSE, $locked = FALSE)
	{
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
			$eager_load_results = self::prepare_with($gas, $resource);
			
			$primary_key = array_shift($eager_load_results);
		}

		$self = (bool) count($resource) == 1;

		foreach ($resource as $record)
		{
			$instance = Gas::factory($gas, array('record' => (array) $record));

			if ($with)
			{
				foreach ($eager_load_results as $child => $results)
				{
					$childs = self::populate_with($child, $results, $record, $primary_key);

					$instance->set_child($child, $childs);
				}

				Gas::factory($gas, array(), FALSE)->set_with();

				Gas::factory($gas, array(), FALSE)->set_with_models();
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
	 * @access  public
	 * @param   string
	 * @param   string
	 * @param   array
	 * @param   array
	 * @param   mixed
	 * @param   bool
	 * @return  mixed
	 */
	public static function generate_child($gas, $child, $link = array(), $identifiers = array(), $foreign_value = null, $eager_load = FALSE)
	{
		$relationship_limitation = array(

			'has_one' => TRUE,

			'belongs_to' => TRUE,

			'has_many' => FALSE,

			'has_and_belongs_to' => FALSE,

			'has_many_through' => FALSE,
		
		);

		$many_to_many = array(

			'has_and_belongs_to',

			'has_many_through',

		);

		$raw_records = array();

		list($table, $primary_key, $relations) = Gas_janitor::identify_meta($gas);

		list($foreign_table, $foreign_key, $foreign_relations) = Gas_janitor::identify_meta($child);

		if (empty($relations) or ! is_array($relations))
		{
			show_error(Gas_core::tell('models_found_no_relations', $gas));
		}

		$parent_relation = Gas_janitor::identify_relations($relations, $child);

		$peer_relation = Gas_janitor::get_input(__METHOD__, $parent_relation, FALSE, '');

		$child_relation = Gas_janitor::identify_relations($foreign_relations, $gas);

		$foreign_peer_relation = Gas_janitor::get_input(__METHOD__, $child_relation, FALSE, '');

		if (empty($peer_relation) or empty($foreign_peer_relation))
		{
			show_error(Gas_core::tell('models_found_no_relations', $gas));
		}

		$parent_identity = Gas_janitor::identify_custom_setting($relations, $peer_relation, $child);

		list($through, $custom_table, $custom_key, $self_ref) = $parent_identity;

		$child_identity = Gas_janitor::identify_custom_setting($foreign_relations, $foreign_peer_relation, $gas);

		list($foreign_through, $foreign_custom_table, $foreign_custom_key, $foreign_self_ref) = $child_identity;

		$identifier = ($custom_key !== '') ? $custom_key : $table.'_'.$primary_key;

		$foreign_identifier = ($foreign_custom_key !== '') ? $foreign_custom_key : $foreign_table.'_'.$foreign_key;

		if ( ! empty($through) and $peer_relation == 'has_many') $peer_relation = 'has_many_through';

		if (in_array($peer_relation, $many_to_many))
		{
			if (empty($custom_table))
			{
				$guess_table = Gas_janitor::combine($table, $foreign_table);

				foreach ($guess_table as $link_table)
				{
					if (self::$db->table_exists($link_table))
					{
						$pivot_table = $link_table;

						continue;
					}
				}
			}
			else
			{
				$pivot_table = $custom_table;
			}

			$recorder = Gas_janitor::tape_track($identifier, $identifiers, $pivot_table);

			$raw_intermediate_records = self::do_compile($gas, $recorder, FALSE, TRUE);
			
			$raw_ids = array();	

			foreach ($raw_intermediate_records as $intermediate_records)
			{
				$raw_ids[] = $intermediate_records[$foreign_identifier];
			}

			$raw_ids = array_unique($raw_ids);
			
			if ( ! empty($raw_ids))
			{
				$recorder = Gas_janitor::tape_track($foreign_key, $raw_ids, $foreign_table);

				$raw_records = self::do_compile($gas, $recorder, FALSE, TRUE);
			}
		}
		else
		{
			$identifier = ($peer_relation == 'belongs_to') ? $foreign_key : $identifier;
			
			$recorder = Gas_janitor::tape_track($identifier, $identifiers, $foreign_table);

			$raw_records = self::do_compile($gas, $recorder, FALSE, TRUE);
		}

		$limitation = $relationship_limitation[$peer_relation];

		$records = ($limitation and ! $eager_load) ? array_shift($raw_records) : $raw_records;

		if ($limitation and ! $eager_load)
		{
			$node = empty($records) ? FALSE : Gas::factory($child, array('record' => $records));
		}
		else
		{
			$node = array();

			if ( ! empty($records))
			{
				if ($eager_load)
				{
					$identifier = ($peer_relation == 'belongs_to') ? $foreign_key : $identifier;

					$node[] = array('identifier' => $identifier, 'self' => $limitation, 'raw' => $records);
				}
				else
				{
					foreach ($records as $record)
					{
						$node[] = Gas::factory($child, array('record' => $record));
					}
				}
			}
		}

		return $node;
	}

	/**
	 * prepare_with
	 * 
	 * Prepare for eager loading
	 *
	 * @access  public
	 * @param   string
	 * @param   mixed
	 * @return  array
	 */
	public static function prepare_with($gas, $resource)
	{
		list($table, $primary_key, $relations) = Gas_janitor::identify_meta($gas);

		$childs = array();

		$eager_load_models = Gas::factory($gas, array(), FALSE)->get_with_models();

		foreach ($eager_load_models as $child)
		{
			list($t, $pk, $r) = Gas_janitor::identify_meta($child);

			$childs[$child] = array(

				'foreign_table' => $t,

				'foreign_key' => $pk,

				'foreign_relations' => $r,
			);
		}

		$ids = array();

		$fids = array();

		$eager_load_results[] = $primary_key;

		foreach ($resource as $single)
		{
			foreach ($childs as $child_model => $child)
			{
				$link = array();

				$peer_relations = Gas_janitor::get_input(__METHOD__, Gas_janitor::identify_relations($relations, $child_model), FALSE, '');

				list($through, $custom_table, $custom_key, $self_ref) = Gas_janitor::identify_custom_setting($relations, $peer_relations, $child_model);

				$foreign_key = ($custom_key !== '') ? $custom_key : $child['foreign_table'].'_'.$child['foreign_key'];

				$key = ($peer_relations == 'belongs_to') ? $foreign_key : $primary_key;
				
				if ($through !== '')
	 			{
	 				$tree = array(

	 					'relations' => $child['foreign_relations'],

	 					'table' => $child['foreign_table'],

	 					'key' => $child['foreign_key'],

	 					'child' => $gas,
		 			);

	 				$link = Gas_janitor::identify_link($through, $foreign_key, $tree);
		 			
		 			if ($peer_relations == 'belongs_to') $foreign_key = $primary_key;
	 			}

				if (isset($single->$foreign_key)) $fids[] = $single->$foreign_key;

				if (isset($single->$key)) $ids[] = $single->$key;

				$eager_load_results[$child_model] = self:: generate_child($gas, $child_model, $link, $ids, array_unique($fids), TRUE);
			}
		}

		return $eager_load_results;
	}

	/**
	 * populate_with
	 * 
	 * Populate all eager loaded resource
	 *
	 * @access  public
	 * @param   string
	 * @param   mixed
	 * @param   object
	 * @param   string
	 * @return  mixed
	 */
	public static function populate_with($child, $results, $record, $primary_key)
	{
		$childs = array();

		foreach ($results as $result)
		{
			$identifier = $result['identifier'];

			$many = ! $result['self'];
			
			foreach ($result['raw'] as $raw_child)
			{
				if (isset($raw_child[$identifier]) and $raw_child[$identifier] == $record->$primary_key)
				{
					$child_node = Gas::factory($child, array('record' => $raw_child));

					if ($many)
					{
						$childs[] = $child_node;
					}
					else
					{
						$childs = $child_node;

						continue;
					}
				}
			}
		}

		return $childs;
	}

	/**
	 * Database connection 
	 * 
	 * @param   mixed 
	 * @return  bool
	 */
	public function connect($dsn = null)
	{
		$this->_engine = $this->_CI->load->database($dsn, TRUE);
		
		if ( ! is_resource($this->_engine->simple_query("SHOW TABLES")))
		{
			show_error(Gas_core::tell('db_connection_error', $dsn));
		}
	
		self::$db = $this->_engine;

		return;
	}

	/**
	 * compile
	 * 
	 * Dynamic function for ompile AR
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @param	bool
	 * @return	response
	 */
	public function compile($gas, $recorder, $limit = FALSE, $raw = FALSE)
	{
		$result = self::do_compile($gas, $recorder, $limit, $raw);
		
		return $result;
	}

	/**
	 * log_resource
	 * 
	 * Create a report for all "in-operation" resource
	 *
	 * @access	public
	 * @return	array
	 */
	public function log_resource()
	{
		return self::$resource_state;
	}

	/**
	 * check_auto_migrate
	 * 
	 * Create whether should doing some migration tasks or not.
	 *
	 * @access	public
	 * @return	void
	 */
	public function check_auto_migrate($models)
	{
		if (self::$auto_migrate == TRUE)
		{
			$all_models = array_keys($models);
			
			$this->load_item($all_models, 'models');

			self::sync_migrate($all_models);

			$this->_CI->db = self::$db;

			if ( ! class_exists('CI_Migration')) $this->_CI->load->library('migration');

			if (self::$execute_migrate)
			{
				if ( ! $this->_CI->migration->latest())
				{
					show_error($this->_CI->migration->error_string());
				}
				else
				{
					log_message('debug', 'Gas ORM Auto-generate tables succesfully migrate your Gas models schema into database and executing migrations to version '.self::$count_migrate);
				}
			}

			return;
		}

		return;
	}

	/**
	 * load_item
	 * 
	 * Load Gas item
	 *
	 * @access  public
	 * @param   mixed
	 * @return  void
	 */
	public function load_item($identifier = null, $type)
	{
		$items = array();

		switch ($type)
		{
			case 'models':
				
				$items = $this->_models;
				
				break;

			case 'extensions' :

				$items = $this->_extensions;

				break;
		}

		if ($identifier == '*')
		{
			if (empty($items)) return $this;
			
			foreach ($items as $item => $item_path)
			{
				$this->_loaded_components[$type][] = $item;

				require_once $item_path;
			}
		}
		elseif (is_array($identifier))
		{
			foreach ($identifier as $item)
			{
				if ( ! array_key_exists($item, $items)) show_error(Gas_core::tell($type.'_not_found', $item));

				$this->_loaded_components[$type][] = $item;

				require_once $items[$item];
			}
		}
		elseif (is_string($identifier))
		{
			if ( ! array_key_exists($identifier, $items)) show_error(Gas_core::tell($type.'_not_found', $identifier));
			
			$this->_loaded_components[$type][] = $identifier;

			require_once $items[$identifier];
		}
		
		return $this;
	}

	/**
	 * validate
	 * 
	 * Validation handler
	 *
	 * @access  public
	 * @param   string
	 * @param   array
	 * @param   array
	 * @return  bool
	 */
	public function validate($gas, $entries, $rules)
	{
		$gas = Gas_janitor::get_input(__METHOD__, $gas, TRUE);

		$entries = Gas_janitor::get_input(__METHOD__, $entries, TRUE);

		$rules = Gas_janitor::get_input(__METHOD__, $rules, TRUE);

		$validator = self::$validator;

		$callbacks = array();

		$callback_success = array();

		$is_post = (bool) count($_POST) > 0;

		if ( ! $is_post) $_POST = $entries;

		foreach ($rules as $field => $rule)
		{
			$old_rule = explode('|', $rule['rules']);

			foreach ($old_rule as $rule_index => $rule)
			{
				if (strpos($rule, 'callback') !== FALSE) 
				{
					$callbacks[$field][] = $rule;

					unset($old_rule[$rule_index]);
				}
			}

			$new_rule = implode('|', $old_rule);

			if ( ! empty($new_rule)) $validator->set_rules($field, Gas_janitor::set_label($field), $new_rule);
		}
		

		if ($validator->run() == FALSE)
		{
			foreach ($entries as $field => $data)
			{
				if (($error = $validator->error($field, '  ', '  ')) and $error != '')
				{
					$error = str_replace('  ', '', $error);

					Gas::set_error($field, $error);
				}
			}

			$success = FALSE;
		}
		else
		{
			$success = TRUE;
		}

		if ( ! $is_post) Gas_janitor::flush_post();
		
		foreach ($callbacks as $field => $callback_rules)
		{
			foreach ($callback_rules as $callback_rule)
			{

				$rule = substr($callback_rule, 9);
				
				if ( ! method_exists($gas, $rule))	continue;

				$entries_value = isset($entries[$field]) ? $entries[$field] : FALSE; 

				if ($entries_value !== FALSE) $callback_success[] = Gas::factory($gas, array(), FALSE)->$rule($field, $entries_value);
			}
		}

		if ( ! empty($callback_success) and $success == TRUE)
		{
			foreach ($callback_success as $single_result)
			{
				if ($single_result == FALSE )
				{
					$success = FALSE;

					continue;
				}
			}
		}

		return $success;
	}

	/**
	 * validate_unique
	 * 
	 * Validate unique fields
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	array
	 * @return	object
	 */
	public function validate_unique($model, $entries, $unique_fields)
	{
		$unique_success = array();

		$table = Gas::factory($model, array(), FALSE)->validate_table()->table;

		foreach ($unique_fields as $unique)
		{
			$res = null;

			$check_fields = Gas_janitor::arr_trim(explode(',', $unique));

			$where = array();

			foreach ($check_fields as $check_field)
			{
				if (isset($entries[$check_field])) $where[$check_field] = $entries[$check_field];
			}

			if ( ! empty($where))
			{
				$recorder = array(

					array('where' => array($where)),

					array('get' => array($table)),

				);

				$res = self::do_compile($model, $recorder, TRUE, TRUE);
			}

			if ( ! is_null($res))
			{
				$callback_unique = Gas::factory($model, array(), FALSE)->unique_check($unique, $res);
				
				if ( ! $callback_unique) $unique_success[] = $unique;
			}
		}

		return (bool) empty($unique_success);
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
	 * reset_cache
	 * 
	 * Stop caching
	 *
	 * @access	public
	 * @return	void
	 */
	public function reset_cache()
	{
		self::$cache_resource = null;

		return;
	}

	/**
	 * sync_migrate
	 * 
	 * Check and generate each gas model's migration sibling
	 *
	 * @access  public
	 * @param   array
	 * @return  void
	 */
	public static function sync_migrate($all_models)
	{
		$path = '';

		if (FALSE !== ($migration_config = Gas_core::config('migration_config')))
		{
			if ($migration_config['migration_enabled'] === FALSE)
			{
				show_error(Gas_core::tell('migration_disabled'));
			}
			elseif ($migration_config['migration_version'] !== 0)
			{
				show_error(Gas_core::tell('migration_no_initial'));
			}
			else
			{
				$path = $migration_config['migration_path'];

				if ( ! is_dir($path))
				{
					if ( ! mkdir($path)) 
					{
						show_error(Gas_core::tell('migration_no_dir'));
					}
				}
			}
		}
		else
		{
			show_error(Gas_core::tell('migration_no_setting'));
		}

		foreach ($all_models as $model)
		{
			self::generate_migration($model, $path);
		}

		self::$auto_migrate = FALSE;

		return;
	}

	/**
	 * generate_migration
	 * 
	 * Generate and create migration file
	 *
	 * @access  public
	 * @param   string
	 * @param   string
	 * @return  void
	 */
	public static function generate_migration($model, $path)
	{
		self::$count_migrate++;

		$counter = self::$count_migrate;

		$counter_prefix = '';

		$key_state = '';

		$forge_fields = array();

		if ($counter < 10)
		{
			$counter_prefix = '00';
		}
		elseif ($counter >= 10 and $counter < 100)
		{
			$counter_prefix = '0';
		}

		$gas = Gas::factory($model);

		$primary_key = $gas->primary_key;

		$model_meta = Gas::list_all_models($model);

		$model_fields = $model_meta['fields'];

		$has_primary_key = (bool) in_array($primary_key, array_keys($model_fields));

		if ($has_primary_key) $key_state = "\t\t".'$this->dbforge->add_key(\''.$primary_key.'\', TRUE);'."\n\n";

		foreach ($model_fields as $field => $properties)
		{
			$forge_fields[$field] = Gas_janitor::identify_annotation($properties['annotations']);
		}

		$fields = array();

		foreach ($forge_fields as $forge_field => $forge_conf)
		{
			$forge_item = "\t\t\t".'\''.$forge_field.'\' => array('."\n\n";

			foreach ($forge_conf as $forge_key => $forge_val)
			{
				$forge_item .= "\t\t\t\t".'\''.$forge_key.'\' => '.var_export($forge_val, TRUE).', '."\n\n";
			}

			$forge_item .= "\t\t\t".'), '."\n\n";

			$fields[] = $forge_item;
		}

		$header = self::generate_file_header('Migration class');

		$create_table = "\t\t".'$this->dbforge->add_field(array('."\n\n"
						.implode("\n", $fields)
						."\t\t".'));'."\n\n"
						.$key_state
						."\t\t".'$this->dbforge->create_table(\''.$model.'\');'."\n";

		$migration_convention = array(

				$header,

				'class Migration_'.ucfirst($model).' extends CI_Migration {',

				'',

				"\t".'function up()',

				"\t".'{',

				$create_table,

				"\t".'}',

				'',

				"\t".'function down()',

				"\t".'{',

				"\t\t".'$this->dbforge->drop_table(\''.$model.'\');',

				"\t".'}',

				'}',

		);
		
		$migration_file = $counter_prefix.self::$count_migrate.'_'.$model.'.php';

		$created = self::create_file($path, $migration_file, $migration_convention);

		if ($created !== TRUE)
		{
			show_error(Gas_core::tell('cannot_create_migration', $path.$migration_file));
		}
		else
		{
			log_message('debug', 'Gas ORM auto-create migration executed: '.$path.$migration_file);
		}

		return;
	}

	/**
	 * generate_models
	 * 
	 * Migrate all availabe and existed tables schema into gas models
	 *
	 * @access  public
	 * @return  void
	 */
	public static function generate_models()
	{
		if (Gas_bureau::$auto_modelling == TRUE) return;

		$tables = array();

		$tables = self::$db->list_tables();

		foreach ($tables as $table)
		{
			$fields = self::$db->field_data($table);

			if ($table !== 'migrations') self::generate_model($table, $fields);
		}

		Gas_bureau::$auto_modelling = TRUE;

		log_message('debug', 'Gas ORM Auto-generate models succesfully migrate your database schema into Gas models.');

		return;
	}

	/**
	 * generate_model
	 * 
	 * Generate and create gas model
	 *
	 * @access  public
	 * @param   string
	 * @param   object
	 * @return  void
	 */
	public static function generate_model($model, $meta_fields)
	{
		$meta_fields = Gas_janitor::get_input(__METHOD__, $meta_fields, TRUE);

		$fields = array();

		$key = 'id';

		foreach ($meta_fields as $meta_field)
		{
			list($field_name, $field_type, $field_length, $is_key) = Gas_janitor::define_field($meta_field);

			list($forge_name, $forge_type, $forge_length, $forge_key) = Gas_janitor::define_field($meta_field, 'forge_field');

			$field_annotation = '';

			$field_annotation = $forge_type;

			$fields[] = "\t\t\t".'\''.$field_name.'\' => Gas::field(\''.$field_type.$field_length.'\','
						.' array(), \''.$field_annotation.'\'),';
		}

		$model = Gas_janitor::get_input(__METHOD__, $model, TRUE) and $model = strtolower($model);

		$header = self::generate_file_header();

		$table = "\n\t".'public $table = \''.$model.'\';'."\n";

		$primary_key = "\n\t".'public $primary_key = \''.$key.'\';'."\n";

		$validation = "\t\t".'$this->_fields = array('."\n\n"
				.implode("\n\n", $fields)
				."\n\n"
				."\t\t".');';

		$callback = "\n\n"
					."\t".'function _before_check() {}'."\n"
					."\n\n"
					."\t".'function _after_check() {}'."\n"
					."\n\n"
					."\t".'function _before_save() {}'."\n"
					."\n\n"
					."\t".'function _after_save() {}'."\n"
					."\n\n"
					."\t".'function _before_delete() {}'."\n"
					."\n\n"
					."\t".'function _after_delete() {}'."\n";

		$model_convention = array(

				$header,

				'class '.ucfirst($model).' extends Gas {',

				$table.$primary_key,

				'',

				"\t".'function _init()',

				"\t".'{',

				$validation,

				"\t".'}',

				''.$callback,

				'}',

		);

		if (is_string(Gas_core::config('models_path')))
		{
			$model_dir = APPPATH.Gas_core::config('models_path');
		}
		else
		{
			$model_dir = APPPATH.'models';
		}

		$model_file = $model.Gas_core::config('models_suffix').'.php';

		$created = self::create_file($model_dir, $model_file, $model_convention);

		if ($created !== TRUE)
		{
			show_error(Gas_core::tell('cannot_create_model', $model_dir.DIRECTORY_SEPARATOR.$model_file));
		}
		else
		{
			log_message('debug', 'Gas ORM auto-create model executed: '.$model_dir.DIRECTORY_SEPARATOR.$model_file);
		}

		return;
	}

	/**
	 * generate_file_header
	 * 
	 * Generate file header portion
	 *
	 * @access  public
	 * @param   string
	 * @return  string
	 */
	public static function generate_file_header($type = 'Gas model')
	{
		$header = '<?php if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');';

		$header .= "\n\n".'/*'
					.' This basic '.$type.' has been auto-generated by the Gas ORM '
					.'*/'."\n";

		return $header;
	}

	/**
	 * track_resource
	 * 
	 * Tracking resource state
	 *
	 * @access  public
	 * @param   string
	 * @param   string
	 * @return  void
	 */
	protected static function track_resource($resource, $action)
	{
		if ( ! isset(self::$resource_state[$resource])) self::$resource_state[$resource] = array();

		$action = strtoupper($action);

		if ( ! isset(self::$resource_state[$resource][$action]))
		{
			self::$resource_state[$resource][$action] = 1;
		}
		else
		{
			$action_count = self::$resource_state[$resource][$action];

			$action_count++;

			self::$resource_state[$resource][$action] = $action_count;
		}

		return;
	}

	/**
	 * changed_resource
	 * 
	 * Monitoring resource state
	 *
	 * @access  public
	 * @param   string
	 * @return  bool
	 */
	protected static function changed_resource($resource)
	{
		return isset(self::$resource_state[$resource]);
	}

	/**
	 * cache_start
	 * 
	 * Writes cache pointer for each compile tasks
	 *
	 * @access  public
	 * @param   array
	 * @return  void
	 */
	protected static function cache_start($task)
	{
		if ( ! self::get_cache_schema()) return;

		self::$cache_key = md5(serialize($task));

		return;
	}
	
	/**
	 * cache_end
	 * 
	 * Writes sibling hash for each resource's records
	 *
	 * @access  public
	 * @param   string
	 * @return  void
	 */
	protected static function cache_end($resource)
	{
		if ( ! self::get_cache_schema()) return;

		$key = self::$cache_key;

		self::$cache_resource[$key] = $resource;

		return;
	}

	/**
	 * validate_cache
	 * 
	 * Validate cache state
	 *
	 * @access  public
	 * @return  bool
	 */
	protected static function validate_cache()
	{
		if ( ! self::get_cache_schema()) return;

		return isset(self::$cache_resource[self::$cache_key]);
	}

	/**
	 * fetch_cache
	 * 
	 * Fetching cache collections
	 *
	 * @access  public
	 * @return  mixed
	 */
	protected static function fetch_cache()
	{
		if ( ! self::get_cache_schema()) return;

		return self::$cache_resource[self::$cache_key];
	}

	/**
	 * get_cache_schema
	 * 
	 * Get cache base configuration
	 *
	 * @access  public
	 * @return  bool
	 */
	private static function get_cache_schema()
	{
		return Gas_core::config('cache_request');
	}

	/**
	 * create_file
	 * 
	 * Create a file
	 *
	 * @access  public
	 * @param   string
	 * @param   string
	 * @param   array
	 * @return  bool
	 */
	private static function create_file($dir, $file, $content)
	{
		if (is_dir($dir))
		{
			return write_file($dir.DIRECTORY_SEPARATOR.$file, implode("\n", $content));
		}
		else
		{
			return write_file($dir.$file, implode("\n", $content));
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
 * @package     Gas Library
 * @subpackage	Gas Janitor
 * @category    Libraries
 * @version     1.4.1
 */

class Gas_janitor {

	public static $dictionary = array(

		'transaction_pointer' => array('trans_off', 'trans_start', 'trans_begin'),

		'transaction_executor' => array('trans_complete', 'trans_rollback', 'trans_commit'),

		'selector' => array('select', 'select_max', 'select_min', 'select_avg', 'select_sum'),

		'condition' => array('join', 'where', 'or_where', 'where_in', 'or_where_in', 'where_not_in', 'or_where_not_in', 'like', 'or_like', 'not_like', 'or_not_like', 'group_by', 'distinct', 'having', 'or_having', 'order_by', 'limit', 'set'),

		'executor' => array('get', 'count_all_results', 'insert_string', 'update_string', 'query', 'insert', 'insert_batch', 'update', 'delete', 'empty_table', 'truncate', 'count_all', 'insert_id', 'affected_rows', 'platform', 'version', 'last_query'),

		'transaction_status' => array('trans_status'),

	);

	public static $datatypes = array(

		'numeric' => array('TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'DECIMAL', 'FLOAT', 'DOUBLE', 'REAL', 'BIT', 'BOOL', 'SERIAL'),

		'datetime' => array('DATE', 'DATETIME', 'TIMESTAMP', 'TIME', 'YEAR'),

		'string' => array('CHAR', 'VARCHAR', 'TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT', 'BINARY', 'VARBINARY', 'TINYBLOB', 'MEDIUMBLOB', 'LONGBLOB', 'ENUM', 'SET'),

		'spatial' => array('GEOMETRY', 'POINT', 'LINESTRING', 'POLYGON', 'MULTIPOINT', 'MULTILINESTRING', 'MULTIPOLYGON', 'GEOMETRYCOLLECTION'),

	);

	public static $default_datatypes = array('datetime' => 'DATETIME', 'string' => 'TEXT', 'spatial' => 'GEOMETRY', 'char' => 'VARCHAR', 'numeric' => 'TINYINT', 'auto' => 'INT', 'int' => 'INT', 'email' => 'VARCHAR');

	public static $hidden_keys;

	public static $num_keys;

	/**
	 * define_field
	 *
	 * @access  public
	 * @param   object
	 * @param   string
	 * @return  array
	 */
	public static function define_field($meta_data, $type = 'gas_field')
	{
		$field_name = $meta_data->name;

		$field_raw_type = strtoupper($meta_data->type);

		$field_gas_type = '';

		$is_key = (bool) $meta_data->primary_key;

		foreach (self::$default_datatypes as $gas_type => $default)
		{
			if ($field_raw_type == $default)
			{
				$field_gas_type = $gas_type;

				continue;
			}
		}

		if ($field_gas_type == '')
		{
			$field_gas_type = self::diagnostic($field_raw_type, 'datatypes');
		}

		if ($is_key and $field_gas_type == 'int') $field_gas_type = 'auto';

		if ( ! strpos($field_name, 'email') and $field_gas_type == 'email') $field_gas_type = 'char';
		
		if ($type == 'gas_field')
		{
			$field_type = $field_gas_type;

			$field_length = is_null($meta_data->max_length) ? '' : '['.$meta_data->max_length.']';
		}
		elseif ($type == 'forge_field')
		{
			if (self::$default_datatypes[$field_gas_type] != $field_raw_type)
			{
				$field_type = $field_raw_type;
			}
			else
			{
				$field_type = '';
			}

			$field_length = $meta_data->max_length;
		}
		else
		{
			$field_type = '';

			$field_length = 0;
		}

		return array($field_name, $field_type, $field_length, $is_key);
	}

	/**
	 * diagnostic
	 *
	 * @access  public
	 * @param   string
	 * @param   string
	 * @return  string
	 */
	public static function diagnostic($name, $source = 'dictionary')
	{
		foreach (self::$$source as $type => $nodes)
		{
			if (in_array($name, $nodes)) return $type;
		}

		return '';
	}

	/**
	 * identify_meta
	 *
	 * @access  public
	 * @param   string
	 * @return  array
	 */
	public static function identify_meta($gas)
	{
		$root = Gas::factory($gas, array(), FALSE);

		$table = self::get_input(__METHOD__, $root->validate_table()->table, TRUE);

		$primary_key = self::get_input(__METHOD__, $root->primary_key, TRUE);

		$relations = self::get_input(__METHOD__, $root->relations, FALSE, array());

		return array($table, $primary_key, $relations);
	}
	/**
	 * identify_link
	 *
	 * @access  public
	 * @param   string
	 * @param   string
	 * @param   array
	 * @return  array
	 */
	public static function identify_link($through, $identifier, $tree = array())
	{
		$through_table = Gas::factory($through, array(), FALSE)->validate_table()->table;

		$peer_relation = self::get_input(__METHOD__, self::identify_relations($tree['relations'], $tree['child']), TRUE);

		list($child_through, $child_custom_table, $child_custom_key, $child_self_ref) = self::identify_custom_setting($tree['relations'], $peer_relation, $tree['child']);

		$child_identifier = ($child_custom_key !== '') ? $child_custom_key : $tree['table'].'_'.$tree['key'];

		return array(

			'intermediate' => $through_table,

			'parent_identifier' => $identifier,

			'child_identifier' => $child_identifier,

			'child_key' => $tree['key'],

		);
	}

	/**
	 * identify_relations
	 *
	 * @access  public
	 * @param   array
	 * @param   string
	 * @return  string
	 */
	public static function identify_relations($relations, $child)
	{
		$peer_relation = null;

		foreach ($relations as $type => $relation)
		{
			foreach ($relation as $model => $config)
			{
				if ($model == $child)
				{
	 				$peer_relation = $type;

	 				continue;
				}
			}
		}

		return $peer_relation;
	}

	/**
	 * identify_custom_setting
	 *
	 * @access  public
	 * @param   array
	 * @param   string
	 * @return  array
	 */
	public static function identify_custom_setting($relations = array(), $type = '', $model = '')
	{
		$through = '';

		$custom_table = '';

		$custom_key = '';

		$self = FALSE;

		if ( ! empty ($relations[$type][$model]) and ($custom_setting = $relations[$type][$model]))
		{
			isset($custom_setting['through']) and $through = $custom_setting['through'];

			isset($custom_setting['foreign_table']) and $custom_table = $custom_setting['foreign_table'];

			isset($custom_setting['foreign_key']) and $custom_key = $custom_setting['foreign_key'];

			isset($custom_setting['self']) and $self = $custom_setting['self'];
		}

		return array($through, $custom_table, $custom_key, $self);
	}

	/**
	 * identify_annotation
	 *
	 * @access  public
	 * @param   array
	 * @return  array
	 */
	public static function identify_annotation($annotation)
	{
		$boolean = array('unsigned', 'null', 'auto_increment');

		$new_annotation = array();

		foreach ($annotation as $type)
		{
			if (in_array($type, $boolean))
			{
				$new_annotation[$type] = TRUE;
			}
			elseif (self::diagnostic($type, 'datatypes') != '')
			{
				$new_annotation['type'] = $type;
			}
			elseif (is_numeric($type))
			{
				$new_annotation['constraint'] = (int) $type;
			}
		}

		return $new_annotation;
	}

	/**
	 * combine
	 *
	 * @access  public
	 * @param   array
	 * @return  array
	 */
	public static function combine($a, $b)
	{
		return array($a.'_'.$b, $b.'_'.$a);
	}

	/**
	 * intersect
	 *
	 * @access  public
	 * @param   array
	 * @return  array
	 */
	public static function intersect($a, $b)
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
	 * arr_trim
	 *
	 * @access  public
	 * @param   array
	 * @return  array
	 */
	public static function arr_trim($arrays)
	{
    	if ( ! is_array($arrays)) return trim($arrays);
 
    	return array_map('Gas_janitor::arr_trim', $arrays);
	}

	/**
	 * arr_timestamp
	 *
	 * @access  public
	 * @param   array
	 * @param   bool
	 * @return  array
	 */
	public static function arr_timestamp($arrays, $new = FALSE)
	{
    	if ( ! is_array($arrays))
    	{
    		if ($new)
    		{
    			if (strpos($arrays, '[') === 0)
    			{
    				return str_replace(array('[',']'), '', $arrays).'%'.date('Y-m-d H:i:s');
    			}

    			return FALSE;
    		}
    		else
    		{
    			return (strpos($arrays, '[') === 0) ? FALSE : $arrays.'%'.date('Y-m-d H:i:s');
    		}
 		}

    	return array_map('Gas_janitor::arr_timestamp', $arrays, array($new));
	}

	/**
	 * arr_unix_timestamp
	 *
	 * @access  public
	 * @param   array
	 * @param   bool
	 * @return  array
	 */
	public static function arr_unix_timestamp($arrays, $new = FALSE)
	{
    	if ( ! is_array($arrays))
    	{
    		if ($new)
    		{
    			return (strpos($arrays, '[') === 0) ? str_replace(array('[',']'), '', $arrays).'%'.time() : FALSE;
    		}
    		else
    		{
    			return (strpos($arrays, '[') === 0) ? FALSE : $arrays.'%'.time();
    		}
	    	
 		}

    	return array_map('Gas_janitor::arr_unix_timestamp', $arrays, array($new));
	}



	/**
	 * arr_hide
	 *
	 * @access  public
	 * @param   array
	 * @return  array
	 */
	public static function arr_hide($val)
	{
    	return ( ! in_array($val, self::$hidden_keys));
	}

	/**
	 * hide_key
	 *
	 * @access  public
	 * @param   array
	 * @param   mixed
	 * @return  void
	 */
	public static function hide_key(&$arrays, $index)
	{
		$hidden_keys = self::$hidden_keys;

		foreach ($hidden_keys as $key)
		{
			if(isset($arrays[$key])) 
			{
				unset($arrays[$key]);
			}
		}

		return;
	}

	/**
	 * num_to_bool
	 *
	 * @access  public
	 * @param   array
	 * @param   mixed
	 * @return  void
	 */
	public static function num_to_bool(&$arrays, $index)
	{
		$num_keys = self::$num_keys;

		foreach ($num_keys as $key)
		{
			if(isset($arrays[$key])) 
			{
				$bool = (bool) ((int) $arrays[$key]);

				$arrays[$key] = var_export($bool, TRUE);
			}
		}
		
		return;
	}

	/**
	 * arr_ucfirst
	 *
	 * @access  public
	 * @param   string
	 * @param   mixed
	 * @return  void
	 */
	public static function arr_ucfirst(&$val, $index)
	{
		$val = ucfirst($val);

		return;
	}

	/**
	 * new_record
	 *
	 * @access  public
	 * @return  array
	 */
	public static function new_record()
	{
		return array_fill(0, count(self::$dictionary), array());
	}

	/**
	 * tape_record
	 *
	 * @access  public
	 * @param   string
	 * @param   array
	 * @return  void
	 */
	public static function tape_record($gas, $recorder)
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

					Gas::factory($gas, array(), FALSE)->set_type($type, TRUE);

					Gas::factory($gas, array(), FALSE)->add_ar_record($recorder);
				}
			}
		}

		return;
	}

	/**
	 * tape_track
	 *
	 * @access  public
	 * @param   string
	 * @param   string
	 * @param   string
	 * @return  array
	 */
	public static function tape_track($identifier, $identifiers, $table)
	{
		return array(

			array('where_in' => array($identifier, $identifiers)),

			array('get' => array($table)),

		);
	}

	/**
	 * play_record
	 *
	 * @access  public
	 * @param   array
	 * @return  array
	 */
	public static function play_record($recorder)
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
	 * @access  public
	 * @param   string
	 * @param   string
	 * @param   array
	 * @return  void
	 */
	public static function force($class, $method, $args, $return = FALSE)
	{
		$total_args = count($args);
			
		if ($total_args == 4)
		{
			if ($return === TRUE) return $class->$method($args[0], $args[1], $args[2], $args[3]);

			$class->$method($args[0], $args[1], $args[2], $args[3]);
		}
		elseif ($total_args == 3)
		{
			if ($return === TRUE) return $class->$method($args[0], $args[1], $args[2]);

			$class->$method($args[0], $args[1], $args[2]);
		}
		elseif ($total_args == 2)
		{
			if ($return === TRUE) return $class->$method($args[0], $args[1]);

			$class->$method($args[0], $args[1]);
		}
		elseif ($total_args == 1)
		{
			if ($return === TRUE) return $class->$method($args[0]);

			$class->$method($args[0]);
		}
		else
		{
			if ($return === TRUE) return $class->$method();

			$class->$method();
		}

		return;
	}

	/**
	 * force_and_get
	 *
	 * @access  public
	 * @param   string
	 * @param   string
	 * @param   array
	 * @return  mixed
	 */
	public static function force_and_get($class, $method, $args)
	{
		return self::force($class, $method, $args, TRUE);
	}

	/**
	 * flush_post
	 *
	 * @access  public
	 * @return  void
	 */
	public static function flush_post()
	{
		if (isset($_POST)) $_POST = array();
	}

	/**
	 * get_input
	 *
	 * @access  public
	 * @param   mixed
	 * @param   bool
	 * @param   bool
	 * @return  mixed
	 */
	public static function get_input($method, $input, $die = FALSE, $default = FALSE)
	{
		if ( ! isset($input) or empty($input))
		{
			if ($die) show_error(Gas_core::tell('empty_arguments', $method));

			$input = $default;
		}

		return $input;
	}

	/**
	 * extract_timestamp
	 *
	 * @access  public
	 * @param   array
	 * @return  array
	 */
	public static function extract_timestamp($timestamp, $index, $type)
	{
    	if (is_string($timestamp) and strpos($timestamp, '%') !== FALSE)
    	{
    		list($field, $ts) = explode('%', $timestamp);

    		if (is_numeric($ts)) $ts = (int) $ts;

    		$time = array($field => $ts);

    		Gas_core::$timestamps[$type][] = $time;
    	}
	}
	
	/**
	 * to_array
	 *
	 * @access  public
	 * @param   mixed
	 * @return  array
	 */
	public static function to_array($var)
	{
		return (array) $var;
	}

	/**
	 * to_json
	 *
	 * @access  public
	 * @param   array
	 * @return  string
	 */
	public static function to_json($var)
	{
		return json_encode($var);
	}

	/**
	 * where_to_join
	 *
	 * @access  public
	 * @param   array
	 * @param   string
	 * @return  array
	 */
	public static function where_to_join($recorder, $table)
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
	 * @access  public
	 * @param   string
	 * @return  string
	 */
	public static function set_label($field)
	{
		return str_replace(array('-', '_'), ' ', ucfirst($field));
	}

}

 /* ------------------------------------------------------------------------------------------------- */
 /* ------------------------------------------------------------------------------------------------- */

/**
 * Gas Extension.
 *
 * @package     Gas Library
 * @subpackage	Gas
 * @category    Libraries
 * @version     1.4.1
 */
interface Gas_extension {
	
	public function __init($gas);

}

 /* ------------------------------------------------------------------------------------------------- */
 /* ------------------------------------------------------------------------------------------------- */

/**
 * Gas Class.
 *
 * @package     Gas Library
 * @subpackage	Gas
 * @category    Libraries
 * @version     1.4.1
 */

class Gas extends Gas_core {}