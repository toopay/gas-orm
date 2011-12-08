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
 * @version     1.4.3
 * @author      Taufan Aditya A.K.A Toopay
 * @link        http://gasorm-doc.taufanaditya.com/
 * @license     BSD
 *
 * =================================================================================================
 * =================================================================================================
 * Copyright 2011 Taufan Aditya a.k.a toopay. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 * 
 * 1. Redistributions of source code must retain the above copyright notice, this list of
 * conditions and the following disclaimer.
 * 
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other materials
 * provided with the distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY Taufan Aditya a.k.a toopay ‘’AS IS’’ AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL Taufan Aditya a.k.a toopay OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * The views and conclusions contained in the software and documentation are those of the
 * authors and should not be interpreted as representing official policies, either expressed
 * or implied, of Taufan Aditya a.k.a toopay.
 * =================================================================================================
 * =================================================================================================
 */

 /* ------------------------------------------------------------------------------------------------- */
 /* ------------------------------------------------------------------------------------------------- */

/**
 * Gas Core Class.
 *
 * @package     Gas Library
 * @subpackage	Gas Core
 * @category    Libraries
 * @version     1.4.3
 */

class Gas_core {

	/**
	 * @var  string  Global version value 
	 */
	const GAS_VERSION = '1.4.3';

	/**
	 * @var  string  Hold table name
	 */
	public $table = '';

	/**
	 * @var  string  Hold primary key collumn name
	 */
	public $primary_key = 'id';

	/**
	 * @var  array   Hold relationship definition
	 */
	public $relations = array();

	/**
	 * @var  bool    Pointer to determine wheter an instance hold a record or not
	 */
	public $empty = TRUE;

	/**
	 * @var  array   Raw errors from validation procedure
	 */
	public $errors = array();

	/**
	 * @var  bool    Pointer to determine whether there are running operation or not
	 */
	public $locked = FALSE;

	/**
	 * @var  bool    Pointer to determine whether some finder should return an object or an array
	 */
	public $single = FALSE;

	/**
	 * @var  array   Used extension(s) flag
	 */
	public $extensions = array();


	/**
	 * @var  array   Hold field definitions
	 */
	protected $_fields = array();

	/**
	 * @var  array   Hold unique field(s)
	 */
	protected $_unique_fields = array();

	/**
	 * @var  array   Hold timestamp field(s)
	 */
	protected $_ts_fields = array();

	/**
	 * @var  array   Hold unix timestamp field(s)
	 */
	protected $_unix_ts_fields = array();

	/**
	 * @var  array   Hold setter field(s)
	 */
	protected $_set_fields = array();

	/**
	 * @var  array   Hold getter field(s)
	 */
	protected $_get_fields = array();

	/**
	 * @var  array   Hold child relations field(s)
	 */
	protected $_get_child_fields = array();

	/**
	 * @var  array   Hold child relations tree
	 */
	protected $_get_child_nodes = array();

	/**
	 * @var  array   Hold raw records
	 */
	protected $_get_reflection_fields = array();


	/**
	 * @var  bool    Initialization flag of Core class
	 */
	public static $init = FALSE;

	/**
	 * @var  mixed   Global configuration value(s)
	 */
	public static $config;

	/**
	 * @var  array   List of loaded models
	 */
	public static $loaded_models = array();

	/**
	 * @var  object  Hold primary Gas_bureau singleton
	 */
	public static $bureau;

	/**
	 * @var  array   Hold activerecord for any gas instance(s)
	 */
	public static $ar_recorder = array();

	/**
	 * @var  bool    Pointer to determine whether a gas instance receive $_POST data or not
	 */
	public static $post = FALSE;

	/**
	 * @var  bool    Pointer to determine whether a gas instance using JOIN statement or not
	 */
	public static $join = FALSE;

	/**
	 * @var  bool    Pointer to determine whether a gas instance perform eager loading or not
	 */
	public static $with = FALSE;

	/**
	 * @var  array   Hold eager loaded model(s)
	 */
	public static $with_models = array();

	/**
	 * @var  bool    Pointer to determine whether a gas instance perform some of transactional method or not
	 */
	public static $transaction_pointer = FALSE;

	/**
	 * @var  bool    Pointer to determine whether a gas instance perform some of selector method or not
	 */
	public static $selector = FALSE;

	/**
	 * @var  bool    Pointer to determine whether a gas instance perform some of conditional method or not
	 */
	public static $condition = FALSE;

	/**
	 * @var  bool    Pointer to determine whether a gas instance perform some of executor method or not
	 */
	public static $executor = FALSE;

	/**
	 * @var  bool    Pointer to determine whether a gas instance perform some of transactional method or not
	 */
	public static $transaction_status = FALSE;

	/**
	 * @var  bool    Pointer to determine whether a gas instance perform some of transactional method or not
	 */
	public static $transaction_executor = FALSE;

	/**
	 * @var  array   Hold auto timestamps fields
	 */
	public static $timestamps = array();

	/**
	 * @var  array   Hold all received input
	 */
	public static $old_input = array();

	/**
	 * @var  bool    Pointer to determine whether a method running via CLI or not
	 */
	public static $cli = FALSE;
	

	/**
	 * @var  array   Holds model(s) name and path
	 */
	protected static $_models;

	/**
	 * @var  mixed   Holds model(s) collections
	 */
	protected static $_models_fields;

	/**
	 * @var  array   Holds extension(s) name and path
	 */
	protected static $_extensions;

	/**
	 * @var  array   Holds model's rule(s)
	 */
	protected static $_rules = array();

	/**
	 * @var  array   Holds default custom rule callback validation error(s) 
	 */
	protected static $_error_callbacks = array();

	/**
	 * @var  array   Holds CI rule validation error(s) 
	 */
	protected static $_errors_validation = array();

	/**
	 * @var  bool    Pointer to determine whether auto-migrate should be executed
	 */
	protected static $_migrated = FALSE;

	
	/**
	 * Constructor
	 */
	function __construct()
	{
		// Initialization flag
		$init = FALSE;

		// Get the gas instance name
		$gas = $this->model();

		// Core class should only instantiated once time
		if (self::is_initialize() == FALSE)
		{
			// Get the CLI pointer
			self::$cli = defined('STDIN');

			if (self::$cli)
			{
				// Run an initial preparation method on CLI environment
				Gas_CLI::reflection_engine();
			}
			else
			{
				// Load CI singleton
				$CI =& get_instance();

				// Get Gas configuration
				$CI->config->load('gas', TRUE, TRUE);

				// Get Migration configuration, for auto-migrate stuff
				$CI->config->load('migration', TRUE, TRUE);
			}

			// Load proper configuration based by environment
			self::$config = (self::$cli) ? Gas_CLI::load_config() : $CI->config->item('gas');

			// Auto-migrate (models and tables) cannot executed via CLI, for security reason
			self::$config['migration_config'] = (self::$cli) ? FALSE : $CI->config->item('migration');

			// Instantiated new Gas_bureau instance, and assign it into a global singleton
			Gas_core::$bureau = (self::$cli) ? new Gas_bureau : new Gas_bureau($CI);
			
			// Scan all possible models file	
			$this->_scan_models();

			// Scan all possible extensions file	
			$this->_scan_extensions();

			// Run _init method if availabe. This will be usefull hooks point to overide Core behaviour
			if (is_callable(array($this, '_init'), TRUE)) $this->_init();

			// Tell that Core class already done with its business
			$init = TRUE;

			if ( ! self::$cli)
			{
				// Determine the request method
				$http_method = $CI->input->server('REQUEST_METHOD');

				// Save any http request into global variable
				switch ($http_method)
				{
					case 'GET':

						Gas_core::$old_input = $CI->input->get();

						break;

					case 'POST':

						Gas_core::$old_input = $CI->input->post();

						break;

					default:

						parse_str(file_get_contents('php://input'), Gas_core::$old_input);

						break;
				}
			}

			log_message('debug', 'Gas ORM Core Class Initialized');
		}

		// Any gas instance(s) will re-use Gas_bureau singleton holds by Core class
		self::$bureau =& Gas_core::recruit_bureau(); 

		// Set models
		self::$bureau->_models = self::$_models;

		// Set extensions
		self::$bureau->_extensions = self::$_extensions;

		// Set global configuration
		self::$bureau->_config = Gas_core::$config;

		// Auto-load models if we enable its configuration
		Gas_core::config('autoload_models') AND self::$bureau->load_item('*', 'models');

		// Auto-load extensions if we enable its configuration
		Gas_core::config('autoload_extensions') AND self::$bureau->load_item(Gas_core::config('extensions'), 'extensions');

		if (self::is_migrated() == FALSE and self::is_initialize() == FALSE)
		{
			// Only do this if we are not running under CLI environment
			if ( ! self::$cli) $this->check_migration(self::$_models);
		}

		if (func_num_args() == 1)
		{
			$args = func_get_arg(0);

			if (isset($args['record']))
			{
				// Is there are valid records passed, assign it respectively
				$this->_get_fields = Gas_janitor::get_input(__METHOD__, $args['record'], FALSE, array());

				// Set empty pointer
				$this->empty = (bool) (count($this->_get_fields) == 0);
			}
		}

		// Tell that we were really initialized
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
		// Get the global version
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

		// Only valid class or quit
		if ( ! class_exists($model)) show_error(Gas_core::tell('models_not_found', $model));

		// Instantiate new gas instance and set the records if exists
		$gas = new $model($records);

		// Execute its _init method if necessary
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
				// If its already executed elsewhere, just get each properties repectively
				$gas->_fields = self::$_models_fields[$name]['fields'];

				$gas->_unique_fields = self::$_models_fields[$name]['unique_fields'];

				$gas->_ts_fields = self::$_models_fields[$name]['ts_fields'];

				$gas->_unix_ts_fields = self::$_models_fields[$name]['unix_ts_fields'];
			}
		}

		// Its ready
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

		// Connect via provided dsn string
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
		// Return bureau singleton
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
		// Return initialized state
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
		// Return migration pointer state
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
		// Tell bureau class to load provided model(s)
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
		// Tell bureau class to load provided extension(s)
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
		// Tell bureau class to dump out the reports
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
		// Tell bureau class to reset all cached resource(s)
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

		// arguments should be an array
		$args = is_array($args) ? $args : (array) $args;

		$annotations = array();

		// Diagnose the type pattern
		if (preg_match('/^([^)]+)\[(.*?)\]$/', $type, $m) AND count($m) == 3)
		{
			$type = $m[1];

			// Parsing [n,n] 
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
		
		// Determine each type into its validation rule respectively
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

		// Are there other annotation?
		$other_annotations = explode(',', $schema);

		// If yes, then merge it with above 
		if ( ! empty($other_annotations))
		{
			$other_annotations = Gas_janitor::arr_trim($other_annotations);

			$annotations = array_merge($annotations, $other_annotations);
		}
		
		// We now have define all rules and annotations
		return array('rules' => implode('|', array_merge($rules, $args)), 'annotations' => $annotations);
	}

	public function check_migration()
	{
		if ( ! self::is_migrated())
		{
			self::$_migrated = TRUE;

			// Execute auto-migrate to flagged models
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
		// Return bureau DB instance
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
		// If key defined and exists, return it
		if (isset(self::$_models_fields[$model])) return self::$_models_fields[$model];

		// Otherwise, return the full collections
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
		// Set needed model(s) to eager load
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
	public static function config($key = '')
	{
		if ( ! empty($key))
		{
			// If key defined and exists, return it
			if (isset(Gas_core::$config[$key])) return Gas_core::$config[$key];

			return FALSE;
		}

		// Otherwise, return all configuration values
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

		// If there are valid arguments, record it
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

		// Are there some active extensions?
		$is_extension = (bool) ! empty($this->extensions);

		// Bundle everyting
		$args = array($this->model(), self::$ar_recorder, $this->single, $is_extension);

		// Produce it
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
		// Are there some active extensions?
		$is_extension = (bool) ! empty($this->extensions);

		// Get valid table name
		$this->validate_table();
		
		// Prepare a recorder
		$recorder = array('get' => array($this->table));

		// Tape it
		Gas_janitor::tape_record($this->model(), $recorder);

		// Validate JOIN portion if necessary
		$this->validate_join();

		// Get the result
		$res = $this->produce();

		// If we run this via an extension pointer...
		if ($is_extension)
		{
			// Set the raw records, so its fully uniformal on extension(s) method
			$this->set_reflection_record($res);

			// Make the original records empty
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

		// Do we need to return an object?
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

		// Is there a limit pointer?
		if (is_int($limit))
		{
			if ($limit == 1)  $this->single = TRUE;

			$recorder = array('limit' => array($limit, $offset));

			Gas_janitor::tape_record($this->model(), $recorder);
		}

		// just WHERE %s or WHERE %s OR %s
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

		// Determine the WHERE IN clause type
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
			// Is there _init method?
			if (is_callable(array($this, '_init'), TRUE) and empty($this->_fields)) $this->_init();

			// Is there _before_check callback?
			if (is_callable(array($this, '_before_check'), TRUE)) $this->_before_check();

			// Run internal validation procedure and get the result
			$valid = $bureau->validate($this->model(), $this->entries(), $this->_fields);

			// Collect all errors
			$this->errors = self::$_errors_validation;

			// Flush temporary error storage
			self::$_errors_validation = array();

			// Not valid? 
			if ( ! $valid) return FALSE;
		}

		// Is there _after_check callback?
		if (is_callable(array($this, '_after_check'), TRUE)) $this->_after_check();

		// Is there _before_save callback?
		if (is_callable(array($this, '_before_save'), TRUE)) $this->_before_save();

		// Are there any auto-timestamp fields has been set?
		if ( ! empty($this->_ts_fields) or ! empty($this->_unix_ts_fields))
		{
			// Save temporary entries
			$old_entries = $this->entries();

			// Create datetime timestamps
			if ( ! empty($this->_ts_fields))
			{
				$created_ts_fields[] = Gas_janitor::arr_timestamp($this->_ts_fields, TRUE);

				$updated_ts_fields[] = Gas_janitor::arr_timestamp($this->_ts_fields);
			}

			// Create unix timestamps
			if ( ! empty($this->_unix_ts_fields))
			{
				$created_ts_fields[] = Gas_janitor::arr_unix_timestamp($this->_unix_ts_fields, TRUE);

				$updated_ts_fields[] = Gas_janitor::arr_unix_timestamp($this->_unix_ts_fields);
			}

			// Sort all timestamps fields to match with Gas data structure
			array_walk_recursive($created_ts_fields, 'Gas_janitor::extract_timestamp', 'created_ts');

			array_walk_recursive($updated_ts_fields, 'Gas_janitor::extract_timestamp', 'updated_ts');
		}

		// Determine whether to perform INSERT or UPDATE operation
		if ($this->empty)
		{
			// Do we have unique fields to validate?
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

			// Adding timestamp
			$this->_add_timestamps(TRUE);

			// Prepare the recorder
			$recorder = array('insert' => array($this->table, $this->entries()));
		}
		else
		{
			// Get the identifier value
			$identifier = $this->identifier();

			// Flush all available recorder
			self::$ar_recorder = array();

			// Prepare condition recorder for UPDATE
			$recorder = array('where' => array($this->primary_key, $identifier));

			// Tape it
			Gas_janitor::tape_record($this->model(), $recorder);

			// Adding timestamp
			$this->_add_timestamps();

			// Prepare the recorder
			$recorder = array('update' => array($this->table, $this->entries()));
		}

		// Tape proper save operation recorder
		Gas_janitor::tape_record($this->model(), $recorder);

		// Compile the recorder and get the result
		$res = $bureau->compile($this->model(), self::$ar_recorder, $skip_affected_rows);

		// Is there _after_save callback?
		if (is_callable(array($this, '_after_save'), TRUE)) $this->_after_save();

		// Flush all post, if exists
		Gas_janitor::flush_post();

		// Flush all errors, if exists
		$this->errors = array();

		// Flush all validation errors, if exists
		self::$_errors_validation = array();

		// Flush all setter values, if exists
		$this->_set_fields = array();

		// Flush all temporary callbacks errors, if exists
		self::$_error_callbacks = array();

		// Return the operation results
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

		// There are no identifier has been passed, there must be a records
		if (is_null($in)) 
		{
			$identifier = Gas_janitor::get_input(__METHOD__, $this->identifier(), TRUE);

			$this->_set_fields = array($this->primary_key => $identifier);

			if (is_callable(array($this, '_before_delete'), TRUE)) $this->_before_delete();

			$recorder = array('delete' => array($this->table, $this->_set_fields));
		}
		else
		{
			// If identifiers found, use it
			$this->_set_fields = array($this->primary_key, $in);

			// Is there _before_delete callback?
			if (is_callable(array($this, '_before_delete'), TRUE)) $this->_before_delete();

			$this->where_in($this->_set_fields);

			$recorder = array('delete' => array($this->table));
		}

		Gas_janitor::tape_record($this->model(), $recorder);

		Gas_janitor::flush_post();

		$this->_set_fields = array();

		$res = $bureau->compile($this->model(), self::$ar_recorder);

		// Is there _after_delete callback?
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
		if (self::$cli)
		{
			// If the request came from CLI, instantiate new Linguist class
			$speaker = new Gas_linguist;
		}
		else
		{
			// Otherwise, use Lang class from CI singleton
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
		}
		
		// Is there something to parse?
		if (FALSE === ($msg = $speaker->line($point)))
		{
			$msg = '';
		}
		
		return (is_string($parser_value)) ? sprintf($msg, $parser_value) : $msg;
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
		// Merge the records with setter values
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
		
		// Load validation lang file
		self::$bureau->lang()->load('form_validation');
		
		if (FALSE === ($line = self::$bureau->lang()->line($key)))
		{
			$line = $msg;
		}

		// Replace label
		$str_error = sprintf($line, Gas_janitor::set_label($field));

		// Assign into temporary errors
		self::$_error_callbacks[] = $str_error;

		// Assign into errors stack
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
		// Use passed prefix if exists, otherwise use paragraph tag
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
		// Merge passed recorder with exists recorder(s)
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
		// If there is no model name passed, use recent class
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
		// If there is no collumn passed, use primary key collumn
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
		// If there is no table name passed, use table name
		$table = (is_null($table)) ? $this->table : $table;
		
		if (empty($table))
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
		// Make sure the table was defined
		$this->validate_table();

		// If JOIN pointer used, replace all related condition to match with JOIN condition
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
	private function _add_timestamps($new = FALSE)
	{
		// Get model entries
		$entries = $this->entries();

		$timestamps = array();

		// Sort the timestamp mode, whether INSERT or UPDATE was choosed
		$type = $new ? 'created_ts' : 'updated_ts';

		isset(Gas_core::$timestamps[$type]) and $timestamps = Gas_core::$timestamps[$type];

		foreach ($timestamps as $timestamp)
		{
			$entries = array_merge($entries, $timestamp);
		}

		unset(Gas_core::$timestamps[$type]);
		
		// Merge the timestamp with overall entries
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

		// Get the models path from config
		$models_path = Gas_core::config('models_path');
		
		// For backward compatibility
		if (is_string($models_path))
	 	{
	 		$models[] = APPPATH.$models_path;
		}
		elseif (is_array($models_path))
		{
			$models = $models_path;
		}

		$model_type = 'models';

		// Define model identifier
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
		// Determine whether we run Gas as Sparks or not
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
		// If there is no path being passed, use root path
		$dir = (is_null($path)) ? $root_path : $path;

		// If the directory was not a valid one, tell right now
		if ( ! is_dir($dir)) show_error(Gas_core::tell($type.'_not_found', $dir));
		
		// Scan all files
		$files = scandir($dir);

		// Sort all scanned files with specific identifier
		foreach ($files as $file) 
		{
		    if ($file == '.' OR $file == '..' OR $file == '.svn') continue;

		    $file = $dir.DIRECTORY_SEPARATOR.$file;

		    if (is_dir($file))  $this->_scan_files($file, $root_path, $type, $identifier);

		    if (strpos($file, $identifier) !== FALSE) 
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
		// Set the setter value
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
 		// Make sure table name was defined
		$this->validate_table();

		// Is there matched records collumn?
 		if (isset($this->_get_fields[$name])) return $this->_get_fields[$name];

 		// Are there matched eager loaded models?
 		if ( ! empty($this->_get_child_fields))
 		{
 			foreach ($this->_get_child_fields as $index => $child)
 			{
 				if ($name == $child)
 				{
 					$type = Gas_janitor::identify_relations($this->relations, $child);

 					if ($type == 'has_one' or $type == 'belongs_to')
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

 		// Are there matched relationship models?
 		if (isset(self::$_models[$name]) and ! isset($this->_set_fields[$name]))
 		{
 			$link = array();

 			// Define table, key and relationship for parent model
 			list($parent_table, $parent_key, $parent_relations) = Gas_janitor::identify_meta($this->model());

 			// Define table, key and relationship for child model
 			list($child_table, $child_key, $child_relations) = Gas_janitor::identify_meta($name);

 			// Determine the relation between parent and child model(s)
 			$peer_relation = Gas_janitor::get_input('Gas_core::__get', Gas_janitor::identify_relations($parent_relations, $name), FALSE, '');

 			// Define any custom relationship setting
			list($through, $custom_table, $custom_key, $self_ref) = Gas_janitor::identify_custom_setting($parent_relations, $peer_relation, $name);

 			$foreign_table = $child_table;

 			$foreign_key = $child_key;

 			$identifier = ($custom_key !== '') ? $custom_key : $foreign_table.'_'.$foreign_key;

 			$key = ($peer_relation == 'belongs_to') ? $identifier : $parent_key;

 			if ($through !== '')
 			{
 				// Build the necessary identifiers
 				$tree = array(

 					'relations' => $child_relations,

 					'table' => $foreign_table,

 					'key' => $foreign_key,

 					'child' => $this->model(),
	 			);

 				$link = Gas_janitor::identify_link($through, $identifier, $tree);

 				if ($peer_relation = 'belongs_to') $identifier = $parent_key;
 			}

 			// Hydrate the child instances
 			return Gas_bureau::generate_child($this->model(), $name, $link, array($this->identifier($key)), $this->identifier($identifier));
	 	}
	 	elseif (isset(self::$bureau->_loaded_components['extensions']) and ($extensions = self::$bureau->_loaded_components['extensions']))
 		{
 			// If there is valid extension name choosed, mark it
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
 		// Make sure table name was defined
		$this->validate_table();

		// Define the DB object
		$engine = $this->db();
		
		// Get all marked extension(s)
		$extensions = $this->get_extensions();

		if (empty($this->table)) $this->table = $this->model();
		
		if ($name == 'list_models')
		{
			return self::$_models;
		}
		elseif ($name == 'fill')
		{
			$input = array();

			// Get the passed input
			$raw_input = array_shift($args);

			// Get the second parameter
			$post = array_shift($args);

			if ($post)
			{
				// Treat all input as $_POST
				self::$post = TRUE;

				$_POST = $raw_input;

				$this->_set_fields = $raw_input;
			}
			elseif (isset($_POST))
			{
				// If $_POST data passed, mark it
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

			// Get the passed value for WHERE condition
			$value = array_shift($args);

			// Is there LIMIT clause passed?
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
			// Get all possible executor method(s)
			$executor = Gas_janitor::$dictionary['executor'];

			// Separate the direct executor method(s)
			$direct = array_splice($executor, -5);

			// Separate the table executor method(s)
			$tables = array_splice($executor, -3);

			// Separate the operation executor method(s)
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

			// Diagnostic the passed method
			$medical_history = Gas_janitor::diagnostic($name);

			// If it was one of executor method or transactional pointer, produce it now
			if ($medical_history == 'executor' or $medical_history == 'transaction_status')
			{
				return $this->produce();
			}
			elseif (strpos($name, 'join') !== FALSE)
			{
				// If any `join` term found, mark it
				self::$join = TRUE;
			}
			elseif ($name == 'limit')
			{
				// If LIMIT clause found, mark it
				if (isset($args[0]) and $args[0] == 1) $this->single = TRUE;
			}
			
			return $this;
		}
		elseif ( ! empty($extensions))
		{
			// Search through marked extension(s)
			foreach ($extensions as $extension => $extension_class)
			{
				// If the method was valid extension method, instantiate related extension
				if (is_callable(array($extension_class, $name), TRUE))
				{
					$ext = new $extension_class;

					if ($ext instanceof Gas_extension) 
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
 * @version     1.4.3
 */

class Gas_bureau {

	/**
	 * @var  array   Hold models collection
	 */
	public $_models = array();

	/**
	 * @var  array   Hold extensions collection
	 */
	public $_extensions = array();

	/**
	 * @var  array   Hold components collection
	 */
	public $_loaded_components = array();
	
	/**
	 * @var  mixed   Hold CI singleton
	 */
	protected $_CI;

	/**
	 * @var  mixed   Hold DB object
	 */
	protected $_engine;


	/**
	 * @var  bool    Pointer for auto-migrate models process
	 */
	protected static $auto_models_success = FALSE;

	/**
	 * @var  bool    Pointer for auto-migrate models configuration
	 */
	protected static $auto_modelling = FALSE;

	/**
	 * @var  bool    Pointer for auto-migrate tables configuration
	 */
	protected static $auto_migrate = FALSE;

	/**
	 * @var  bool    Pointer to determine wheter to execute migration or not
	 */
	protected static $execute_migrate = FALSE;

	/**
	 * @var  int     Migrate counter
	 */
	protected static $count_migrate = 0;

	/**
	 * @var  mixed   Hold DB object
	 */
	protected static $db;

	/**
	 * @var  string  Hold DB Driver name
	 */
	protected static $db_driver;

	/**
	 * @var  mixed   Hold Validator object
	 */
	protected static $validator;

	/**
	 * @var  mixed   Hold tasks tree detail for every compile process
	 */
	protected static $task_manager;

	/**
	 * @var  mixed   Hold compile result
	 */
	protected static $thread_resource;

	/**
	 * @var  array   Hold some special operation pointer
	 */
	protected static $empty_executor = array('writes' => FALSE, 'operation' => FALSE);

	/**
	 * @var  mixed   Hold cached compile result collection
	 */
	protected static $cache_resource;

	/**
	 * @var  array   Hold hashed recorder bundle 
	 */
	protected static $cache_key;

	/**
	 * @var  array   Hold monitored resorce stated
	 */
	protected static $resource_state;
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		// Determine whether request came from CLI or not
		if ( ! Gas_core::$cli)
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
		}
		else
		{
			$this->_engine = Gas_CLI::$DB;
		}

		// Define the DB object
		self::$db = $this->_engine;

		// Define the DB Driver name
		self::$db_driver = $this->_engine->dbdriver;

		if ( ! Gas_core::$cli)
		{
			// Assign new properties into CI singleton, so profiler would monitor it
			$this->_CI->Gas_DB = self::$db;

			// Load validator class
			if ( ! class_exists('CI_Form_validation')) $this->_CI->load->library('form_validation');

			self::$validator = $this->_CI->form_validation;

			$auto_create_models = FALSE;

			$auto_create_tables = FALSE;

			// Auto-migrate only works for for CI version 2.1 or above
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
		// Build the tasks tree
		$tasks = Gas_janitor::play_record($recorder);

		// Mark every compile process into our caching pool
		self::cache_start($tasks);

		// Ger DB object name
		$motor = get_class(self::$db);

		// Generate the task bundle
		$bundle = array(

			'engine' => $motor,

			'compiler' => array(

				'gas' => $gas,

				'limit' => $limit,

				'raw' => $raw,

			),

			'flag' => array('condition', 'selector')

		);

		// Is there any executor method in task list?
		if ( ! empty ($tasks['executor']))
		{
			$executor = Gas_janitor::$dictionary['executor'];

			$operations = array_splice($executor, -8);

			$writes = array_splice($executor, -4);

			// Adding more details into it
			$bundle['executor_list'] = array(

				'operation' => $operations, 

				'writes' => $writes
			
			);
		}

		// Assign the task to the right person
		self::$task_manager = $bundle;

		// Lets dance...
		array_walk($tasks, 'Gas_bureau::sort_task');

		// Get the result and immediately flush the temporary resource holder
		$resource = self::$thread_resource and self::$thread_resource = null;
		
		// The compilation is done, send the song to listen
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
		// Only sort if there are valid task and the task manager hold its task list
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
		// Only do each task if the task manager hold its task list
		if (empty(self::$task_manager)) return;

		// Diagnose wheter to just execute the method or return the result
		$flag = ! in_array($task, self::$task_manager['flag']);

		// Get the method
		$action = key($arguments);

		// Get the arguments
		$args = array_shift($arguments);

		if ($flag)
		{
			// Get compiler collection
			$compiler = self::$task_manager['compiler'];

			// Determine whether the task is a part of transactional process or not
			$is_transaction = Gas::factory($compiler['gas'], array(), FALSE)->get_type('transaction_pointer');

			// Flush the recent recorder
			Gas::factory($compiler['gas'], array(), FALSE)->set_ar_record(array());

			if ($action == 'get' and ($read_arguments = $args))
			{
				// Get resource name
				$resource_name = array_shift($read_arguments);

				// If resource state unchanged and the task already executed elsewhere, fetch from cache
				if (self::validate_cache() and self::changed_resource($resource_name) == FALSE)
				{	
					if (method_exists(self::$db, 'reset_query'))
					{
						self::$db->reset_query();
					}
					else
					{
						array_walk(Gas_janitor::$ar, 'Gas_bureau::reset_select');
					}

					$result = self::fetch_cache();

					if (Gas_core::$cli) Gas_CLI::flash('info', 'Resource state not changed, fetch from cache.'."\n");
				}
				else
				{
					// Execute the queries
					$result = Gas_janitor::force_and_get(self::$db, $action, $args);

					// Cache it
					self::cache_end($result);
				}
				
				if ($compiler['raw'] === TRUE) 
				{
					// If the `raw` flag found, dont cook it
					$res = $result->result_array();
				}
				else
				{
					// Get the eager load marker
					$with = Gas::factory($compiler['gas'], array(), FALSE)->get_with();

					// Hydrate the gas instance
					$res = self::generator($compiler['gas'], $result->result(), __FUNCTION__, $compiler['limit'], $with);
				}

				// Tell task manager to take a break
				self::$task_manager = array();

				// Fill the temporary resource holder
				self::$thread_resource = $res;

				return;
			}
			
			// Do we have executor methods in task list?
			if (isset(self::$task_manager['executor_list']))
			{
				$executor_list = self::$task_manager['executor_list'];
			}
			else
			{
				$executor_list = self::$empty_executor;
			}

			// Sort all write operations
			$writes = Gas_janitor::get_input(__METHOD__, $executor_list['writes'], FALSE, '');

			// Sort other executor operations
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
				// Get resource name
				$resource_name = array_shift($write_arguments);
				
				// Log into tracker pool
				self::track_resource($resource_name, $action);

				Gas_janitor::force(self::$db, $action, $args);
				
				// Do we need to return the affected row?
				$res = ($compiler['limit']) ? TRUE : self::$db->affected_rows();

				self::$task_manager = array();
			}
			elseif ( ! $is_transaction and in_array($action, $operations))
			{
				// Separate operations and get the explicit methods which need to return the result
				array_splice($operations, 2);

				// Hold the non-explicit operations
				$non_explicit = $operations;

				if (in_array($action, $non_explicit))
				{
					// If there is no explicit flag, just execute it
					$res = Gas_janitor::force(self::$db, $action, $args);
				}
				else
				{
					// If there is explicit flag, execute and return the result
					$res = Gas_janitor::force_and_get(self::$db, $action, $args);
				}
			}
			else
			{
				$return = FALSE;

				if ($action == 'query' and ($sample_args = $args))
				{
					// Inspect whether queries contain SELECT clause
					$return = (strpos(strtolower(array_shift($sample_args)), 'select') === 0);
				}

				// If SELECT clause found, either return raw records or hydrate related object
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

					// Tell task manager to take a break
					self::$task_manager = array();

					// Fill the temporary resource holder
					self::$thread_resource = $res;

					return;
				}
				else
				{
					$res = Gas_janitor::force(self::$db, $action, $args);
				}
			}

			// Fill the temporary resource holder
			self::$thread_resource = $res;

			return;
		}
		else
		{
			// Execute it without returning the result
			return Gas_janitor::force(self::$db, $action, $args);
		}
	}

	/**
	 * reset_select
	 * 
	 * Reset Select properties within query builder instance
	 *
	 * @access  public
	 * @param   mixed
	 * @param   string
	 * @return  void
	 */
	public static function reset_select($default, $key)
	{
		$property = 'ar_'.$key;

		// Set AR property to default value
		self::$db->$property = $default;

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
	 * engine_driver
	 * 
	 * return used driver
	 *
	 * @access	public
	 * @return	string
	 */
	public static function engine_driver()
	{
		return self::$db_driver;
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

		// If the method returning empty resource, we are done
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

		// Do we have eager load queue?
		if ($with)
		{
			// Get the child result
			$eager_load_results = self::prepare_with($gas, $resource);
			
			// Get primary key
			$primary_key = array_shift($eager_load_results);
		}

		// Do we need to return an instance or an array of instance?
		$self = (bool) count($resource) == 1;

		// Iterate resource item
		foreach ($resource as $record)
		{
			// Hydrate new gas instance and assign each record
			$instance = Gas::factory($gas, array('record' => (array) $record));

			// If eager load mark found, assign each child instance respectively
			if ($with)
			{
				// Iterate the child instance
				foreach ($eager_load_results as $child => $results)
				{
					// Get the child records
					$childs = self::populate_with($child, $results, $record, $primary_key);

					// Hydrate new gas instance and assign each record
					$instance->set_child($child, $childs);
				}

				// Flush the eager load marker
				Gas::factory($gas, array(), FALSE)->set_with();

				// Flush the eager load models queue
				Gas::factory($gas, array(), FALSE)->set_with_models();
			}

			// If limit marker found then we done
			if ($limit) return $instance;

			// Assign the generates instance into an array
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
		// Set each relations type's limit
		$relationship_limitation = array(

			'has_one' => TRUE,

			'belongs_to' => TRUE,

			'has_many' => FALSE,

			'has_and_belongs_to' => FALSE,

			'has_many_through' => FALSE,
		
		);

		// Define many-to-many relationship
		$many_to_many = array(

			'has_and_belongs_to',

			'has_many_through',

		);

		$raw_records = array();

		// Define table, key and relationship for parent model
		list($table, $primary_key, $relations) = Gas_janitor::identify_meta($gas);

		// Define table, key and relationship for child model
		list($foreign_table, $foreign_key, $foreign_relations) = Gas_janitor::identify_meta($child);

		if (empty($relations) or ! is_array($relations))
		{
			show_error(Gas_core::tell('models_found_no_relations', $gas));
		}

		$parent_relation = Gas_janitor::identify_relations($relations, $child);

		// Determine the relation between parent and child model(s)
		$peer_relation = Gas_janitor::get_input(__METHOD__, $parent_relation, FALSE, '');

		$child_relation = Gas_janitor::identify_relations($foreign_relations, $gas);

		$foreign_peer_relation = Gas_janitor::get_input(__METHOD__, $child_relation, FALSE, '');

		if (empty($peer_relation) or empty($foreign_peer_relation))
		{
			show_error(Gas_core::tell('models_found_no_relations', $gas));
		}

		// Get parent custom setting
		$parent_identity = Gas_janitor::identify_custom_setting($relations, $peer_relation, $child);

		list($through, $custom_table, $custom_key, $self_ref) = $parent_identity;

		// Get child custom setting
		$child_identity = Gas_janitor::identify_custom_setting($foreign_relations, $foreign_peer_relation, $gas);

		list($foreign_through, $foreign_custom_table, $foreign_custom_key, $foreign_self_ref) = $child_identity;

		$identifier = ($custom_key !== '') ? $custom_key : $table.'_'.$primary_key;

		$foreign_identifier = ($foreign_custom_key !== '') ? $foreign_custom_key : $foreign_table.'_'.$foreign_key;

		// Do we have `through` option on?
		if ( ! empty($through) and $peer_relation == 'has_many') $peer_relation = 'has_many_through';

		if (in_array($peer_relation, $many_to_many))
		{
			// Set the pivot table
			$intermediate_table = (empty($custom_table)) ? $foreign_table.'_'.$table : $custom_table;

			$pivot_table = ( ! empty($through)) ? $through : $intermediate_table;

			$recorder = Gas_janitor::tape_track($identifier, $identifiers, $pivot_table);

			// Get intermediate records
			$raw_intermediate_records = self::do_compile($gas, $recorder, FALSE, TRUE);

			$raw_ids = array();	

			$many_identifier = $raw_intermediate_records;

			foreach ($raw_intermediate_records as $intermediate_records)
			{
				$raw_ids[] = $intermediate_records[$foreign_identifier];
			}

			$raw_ids = array_unique($raw_ids);
			
			if ( ! empty($raw_ids))
			{
				$recorder = Gas_janitor::tape_track($foreign_key, $raw_ids, $foreign_table);

				// If we have intermediate records, get the childs records
				$raw_records = self::do_compile($gas, $recorder, FALSE, TRUE);
			}
		}
		else
		{
			$identifier = ($peer_relation == 'belongs_to') ? $foreign_key : $identifier;
			
			$recorder = Gas_janitor::tape_track($identifier, $identifiers, $foreign_table);

			// Get the childs records
			$raw_records = self::do_compile($gas, $recorder, FALSE, TRUE);
		}

		// Define limitation
		$limitation = $relationship_limitation[$peer_relation];

		// Get the records
		$records = ($limitation and ! $eager_load) ? array_shift($raw_records) : $raw_records;

		// For one-to-one relationship
		if ($limitation and ! $eager_load)
		{
			$node = empty($records) ? FALSE : Gas::factory($child, array('record' => $records));
		}
		else
		{
			$node = array();

			// Do we have some records to process in many-to-many relationship?
			if ( ! empty($records))
			{
				// Do we need to return an eager load collection?
				if ($eager_load)
				{
					$identifier = ($peer_relation == 'belongs_to') ? $foreign_key : $identifier;

					if ($peer_relation == 'has_and_belongs_to' or $peer_relation == 'has_many_through')
					{
						$many_records = array();

						$identifier = '';

						foreach ($records as $record)
						{
							foreach ($many_identifier as $many)
							{
								if ($record[$foreign_key] == $many[$foreign_identifier])
								{
									unset($many[$foreign_identifier]);

									if (empty($identifier)) $identifier = key($many);

									$many_records[] = array_merge($record, $many);	
								}
							}
						}
						
						$records = $many_records;
					}

					$node[] = array('identifier' => $identifier, 'foreign_identifier' => $foreign_identifier, 'self' => $limitation, 'raw' => $records);
				}
				else
				{
					foreach ($records as $record)
					{
						// Hydrate new child instance and assign each record respectively
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
		// Define table, key and relationship for parent model
		list($table, $primary_key, $relations) = Gas_janitor::identify_meta($gas);

		$childs = array();

		// Get all eager load models queue
		$eager_load_models = Gas::factory($gas, array(), FALSE)->get_with_models();

		foreach ($eager_load_models as $child)
		{
			// Define table, key and relationship for child model
			list($t, $pk, $r) = Gas_janitor::identify_meta($child);

			$childs[$child] = array(

				'foreign_table' => $t,

				'foreign_key' => $pk,

				'foreign_relations' => $r,
			);
		}

		$eager_load_results[] = $primary_key;

		foreach ($childs as $child_model => $child)
		{
			
				$link = array();

				$peer_relations = Gas_janitor::get_input(__METHOD__, Gas_janitor::identify_relations($relations, $child_model), FALSE, '');

				// Get custom relationship setting
				list($through, $custom_table, $custom_key, $self_ref) = Gas_janitor::identify_custom_setting($relations, $peer_relations, $child_model);

				$foreign_key = ($custom_key !== '') ? $custom_key : $child['foreign_table'].'_'.$child['foreign_key'];

				$key = ($peer_relations == 'belongs_to') ? $foreign_key : $primary_key;
				
				if ($through !== '')
	 			{
	 				// Build the necessary identifiers
	 				$tree = array(

	 					'relations' => $child['foreign_relations'],

	 					'table' => $child['foreign_table'],

	 					'key' => $child['foreign_key'],

	 					'child' => $gas,
		 			);

	 				$link = Gas_janitor::identify_link($through, $foreign_key, $tree);
		 			
		 			if ($peer_relations == 'belongs_to') $foreign_key = $primary_key;
	 			}

	 			$ids = array();

				$fids = array();

	 			foreach ($resource as $single)
				{
					if (isset($single->$foreign_key)) $fids[] = $single->$foreign_key;

					if (isset($single->$key)) $ids[] = $single->$key;
				}

				// Hydrate each child instance respectively
				$eager_load_results[$child_model] = self:: generate_child($gas, $child_model, $link, array_unique($ids), array_unique($fids), TRUE);
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

		$belongs = NULL;
		
		$result = array_shift($results);
		
		// Get the original identifier
		$identifier = $result['identifier'];

		// Get the remote identifier
		$foreign_identifier = $result['foreign_identifier'];

		// Determine the relationship limitation
		$many = ! $result['self'];

		// Belongs to is speacial case, we need to revert the identifier
		if (is_null($belongs)) $belongs = isset($record->$foreign_identifier);

		foreach ($result['raw'] as $raw_child)
		{
			if ($belongs)
			{
				// Do the child record identifier match with original identifier?
				if ($raw_child[$identifier] == $record->$foreign_identifier)
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
			else
			{
				// Do the child record identifier match with original identifier?
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
		if (Gas_core::$cli)
		{
			// If the request came from CLI, get the engine from there
			Gas_CLI::reflection_engine();
		}
		else
		{
			// Otherwise, use CI loader to create new DB object
			$this->_engine = $this->_CI->load->database($dsn, TRUE);
			
			if ( ! is_resource($this->_engine->simple_query("SHOW TABLES")))
			{
				// If debug is on and connection resource was invalid, quit
				show_error(Gas_core::tell('db_connection_error', $dsn));
			}
		
		}

		// Assign the DB object
		self::$db = $this->_engine;

		// Assign the DB Driver name
		self::$db_driver = $this->_engine->dbdriver;

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
		// Execute compile process
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
		// Return all monitoring resource status
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
		// Only run if auto-migrate flag found
		if (self::$auto_migrate == TRUE)
		{
			// Extract all models
			$all_models = array_keys($models);
			
			// Load necessary models
			$this->load_item($all_models, 'models');

			// Synchronize all models with its migration siblings
			self::sync_migrate($all_models);

			// Assign DB object into CI singleton, so both Migration and Forge class didnt get lost
			$this->_CI->db = self::$db;

			// Load migration class if not loaded yet
			if ( ! class_exists('CI_Migration')) $this->_CI->load->library('migration');

			// If we found migration executor marker, run the migration process
			if (self::$execute_migrate)
			{
				// Do migration fails?
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
	 * set_cli_engine
	 * 
	 * Return DB object for CLI
	 *
	 * @access	public
	 * @return	object
	 */
	public function set_cli_engine()
	{
		return self::$db;
	}

	/**
	 * get_cli_engine
	 * 
	 * Set DB object from CLI
	 *
	 * @access	public
	 * @return	void
	 */
	public function get_cli_engine($engine)
	{
		$this->_engine = $engine;

		self::$db = $this->_engine;

		self::$db_driver = $this->_engine->dbdriver;

		return ;
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

		// Determine the type of item
		switch ($type)
		{
			case 'models':
				
				$items = $this->_models;
				
				break;

			case 'extensions' :

				$items = $this->_extensions;

				break;
		}

		// `*` always mean : all
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
			// Iterate the array and load each item
			foreach ($identifier as $item)
			{
				if ( ! array_key_exists($item, $items)) show_error(Gas_core::tell($type.'_not_found', $item));

				$this->_loaded_components[$type][] = $item;

				require_once $items[$item];
			}
		}
		elseif (is_string($identifier))
		{
			// Make sure the assigned string is a valid one
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

		// Get validator object
		$validator = self::$validator;

		$callbacks = array();

		$old_post = array();

		$callback_success = array();

		// Do we have synchronized $_POST data?
		$is_post = (bool) (count($_POST) > 0 and count($_POST) == count($entries));

		if ( ! $is_post)
		{
			// Save the $_POST data into temporary data
			$old_post = $_POST;

			// If $_POST data was not set or not synchrone with models entries, sync it
			$_POST = $entries;
		}

		// Iterate each rules and build the validation rules collection
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
		
		// Execute the CI validator
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

		// Flush the $_POST data
		if ( ! $is_post) Gas_janitor::flush_post();
		
		// Run the custom rule callbacks
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

		// Inspect the custom rule callbacks process
		if ( ! empty($callback_success) and $success == TRUE)
		{
			foreach ($callback_success as $single_result)
			{
				if ($single_result == FALSE )
				{
					// If any rule fails, all fails
					$success = FALSE;

					continue;
				}
			}
		}

		// Build the original $_POST data
		if ( ! $is_post) $_POST = $old_post;

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

		// Get table name
		$table = Gas::factory($model, array(), FALSE)->validate_table()->table;

		// Iterate all unique fields and validate
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
		if (Gas_core::$cli)
		{
			// If the request came from CLI, instantiate new Linguist class
			$linguist = new Gas_linguist;
		}
		else
		{
			// Otherwise, use Lang class from CI singleton
			$linguist = $this->_CI->lang;
		}

		return $linguist;
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
		// Flush the cached resources
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

		// Make sure migration configuration is the right one
		if (FALSE !== ($migration_config = Gas_core::config('migration_config')))
		{
			if ($migration_config['migration_enabled'] === FALSE)
			{
				// Migration config is off
				show_error(Gas_core::tell('migration_disabled'));
			}
			elseif ($migration_config['migration_version'] !== 0)
			{
				// Migration config already have some version
				show_error(Gas_core::tell('migration_no_initial'));
			}
			else
			{
				$path = $migration_config['migration_path'];

				// If there is no migration folder, create one
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
			// Migration config not found
			show_error(Gas_core::tell('migration_no_setting'));
		}

		foreach ($all_models as $model)
		{
			// Generate all model's sibling
			self::generate_migration($model, $path);
		}

		// Turn off auto-migrate marker
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
		// Count each migration file
		self::$count_migrate++;

		$counter = self::$count_migrate;

		$counter_prefix = '';

		$key_state = '';

		$forge_fields = array();

		// Build the numeric prefix
		if ($counter < 10)
		{
			$counter_prefix = '00';
		}
		elseif ($counter >= 10 and $counter < 100)
		{
			$counter_prefix = '0';
		}

		$gas = Gas::factory($model);

		// Get the primary key
		$primary_key = $gas->primary_key;

		// Get fields meta data
		$model_meta = Gas::list_all_models($model);

		$model_fields = $model_meta['fields'];

		// Do we have a primary key?
		$has_primary_key = (bool) in_array($primary_key, array_keys($model_fields));

		// If primary key found, add it
		if ($has_primary_key) $key_state = "\t\t".'$this->dbforge->add_key(\''.$primary_key.'\', TRUE);'."\n\n";

		// Iterate each model fields
		foreach ($model_fields as $field => $properties)
		{
			// Get all defined annotation and assign it into Forge fields collection
			$forge_fields[$field] = Gas_janitor::identify_annotation($properties['annotations']);
		}

		$fields = array();

		// Iterate the Forge collection
		foreach ($forge_fields as $forge_field => $forge_conf)
		{
			// Build the Forge spec
			$forge_item = "\t\t\t".'\''.$forge_field.'\' => array('."\n\n";

			foreach ($forge_conf as $forge_key => $forge_val)
			{
				$forge_item .= "\t\t\t\t".'\''.$forge_key.'\' => '.var_export($forge_val, TRUE).', '."\n\n";
			}

			$forge_item .= "\t\t\t".'), '."\n\n";

			$fields[] = $forge_item;
		}

		// Generate the header portion
		$header = self::generate_file_header('Migration class');

		// Generate the forge spec
		$create_table = "\t\t".'$this->dbforge->add_field(array('."\n\n"
						.implode("\n", $fields)
						."\t\t".'));'."\n\n"
						.$key_state
						."\t\t".'$this->dbforge->create_table(\''.$model.'\');'."\n";

		// Generate the migration file spec
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
		
		// Build the migration file name
		$migration_file = $counter_prefix.self::$count_migrate.'_'.$model.'.php';

		// Create the migration file
		$created = self::create_file($path, $migration_file, $migration_convention);

		// Log the process result
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

		// Get the tables
		$tables = self::$db->list_tables(TRUE);

		// Iterate the tables
		foreach ($tables as $table)
		{
			$fields = self::$db->field_data($table);

			// Generate each model respectively
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
		// Get tables meta data
		$meta_fields = Gas_janitor::get_input(__METHOD__, $meta_fields, TRUE);

		$fields = array();

		$key = 'id';

		// Iterate the tables meta data
		foreach ($meta_fields as $meta_field)
		{
			// Define field based by Gas spec
			list($field_name, $field_type, $field_length, $is_key) = Gas_janitor::define_field($meta_field);

			// Define field based by Forge spec
			list($forge_name, $forge_type, $forge_length, $forge_key) = Gas_janitor::define_field($meta_field, 'forge_field', self::engine_driver());

			$field_annotation = '';

			$field_annotation = $forge_type;

			// Build the fields collection
			$fields[] = "\t\t\t".'\''.$field_name.'\' => Gas::field(\''.$field_type.$field_length.'\','
						.' array(), \''.$field_annotation.'\'),';
		}

		// Get model name
		$model = Gas_janitor::get_input(__METHOD__, $model, TRUE) and $model = strtolower($model);

		// Generate the header portion
		$header = self::generate_file_header();

		// Generate the table property portion
		$table = "\n\t".'public $table = \''.$model.'\';'."\n";

		// Generate the primary key portion
		$primary_key = "\n\t".'public $primary_key = \''.$key.'\';'."\n";

		// Generate the validation and field definition portion
		$validation = "\t\t".'$this->_fields = array('."\n\n"
				.implode("\n\n", $fields)
				."\n\n"
				."\t\t".');';

		// Generate the initial callbacks
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

		// Generate the gas model file spec
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

		// Determine models directory
		if (is_string(Gas_core::config('models_path')))
		{
			$model_dir = APPPATH.Gas_core::config('models_path');
		}
		else
		{
			$model_dir = APPPATH.'models';
		}

		// Build the model file name
		$model_file = $model.Gas_core::config('models_suffix').'.php';

		// Create the model file
		$created = self::create_file($model_dir, $model_file, $model_convention);

		// Log the process result
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
		// Generate the header portion for auto-created files
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

		// Set the action name
		$action = strtoupper($action);

		if ( ! isset(self::$resource_state[$resource][$action]))
		{
			// If the resource has not been monitored, create one
			self::$resource_state[$resource][$action] = 1;
		}
		else
		{
			// Otherwise, increase the counter number
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
		// Return the resource state
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

		// Hash the task, and assign it into cache key collection
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

		// Assign it into cache resource collection
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

		// Determine whether a resource is a valid cached 
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

		// Return the cached resource
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
		// Get the global caching schema configuration
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
 * @version     1.4.3
 */

class Gas_janitor {

	/**
	 * @var  array  Hold DB AR properties
	 */
	public static $ar = array('select' => array(), 'from' => array(), 'join' => array(), 'where' => array(), 'like' => array(), 'groupby' => array(), 'having' => array(), 'orderby' => array(), 'wherein' => array(), 'aliased_tables' => array(), 'no_escape' => array(), 'distinct' => FALSE, 'limit' => FALSE, 'offset' => FALSE, 'order' => FALSE);

	/**
	 * @var  array  Hold all defined action collections
	 */
	public static $dictionary = array(

		'transaction_pointer' => array('trans_off', 'trans_start', 'trans_begin'),

		'transaction_executor' => array('trans_complete', 'trans_rollback', 'trans_commit'),

		'selector' => array('select', 'select_max', 'select_min', 'select_avg', 'select_sum'),

		'condition' => array('join', 'where', 'or_where', 'where_in', 'or_where_in', 'where_not_in', 'or_where_not_in', 'like', 'or_like', 'not_like', 'or_not_like', 'group_by', 'distinct', 'having', 'or_having', 'order_by', 'limit', 'set'),

		'executor' => array('get', 'count_all_results', 'insert_string', 'update_string', 'query', 'insert', 'insert_batch', 'update', 'delete', 'empty_table', 'truncate', 'count_all', 'insert_id', 'affected_rows', 'platform', 'version', 'last_query'),

		'transaction_status' => array('trans_status'),

	);

	/**
	 * @var  array  Hold all defined datatypes collections
	 */
	public static $datatypes = array(

		'numeric' => array('TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'INT2', 'INT4', 'INT8', 'INTEGER', 'BIGINT', 'DECIMAL', 'FLOAT', 'FLOAT4', 'FLOAT8', 'DOUBLE', 'REAL', 'BIT', 'BOOL', 'SERIAL', 'SERIAL8', 'BIGSERIAL', 'DOUBLE PRECISION', 'NUMERIC'),

		'datetime' => array('DATE', 'DATETIME', 'TIMESTAMP', 'TIMESTAMPTZ', 'TIME', 'TIMETZ', 'YEAR', 'INTERVAL'),

		'string' => array('CHAR', 'BPCHAR', 'CHARACTER', 'VARCHAR', 'TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT', 'BINARY', 'VARBINARY', 'TINYBLOB', 'MEDIUMBLOB', 'LONGBLOB', 'ENUM', 'SET'),

		'spatial' => array('GEOMETRY', 'POINT', 'LINESTRING', 'POLYGON', 'MULTIPOINT', 'MULTILINESTRING', 'MULTIPOLYGON', 'GEOMETRYCOLLECTION'),

	);

	/**
	 * @var  array  Hold all default datatypes collections
	 */
	public static $default_datatypes = array('datetime' => 'DATETIME', 'string' => 'TEXT', 'spatial' => 'GEOMETRY', 'char' => 'VARCHAR', 'numeric' => 'TINYINT', 'auto' => 'INT', 'int' => 'INT', 'email' => 'VARCHAR');

	/**
	 * @var  array  Hold hidden keys
	 */
	public static $hidden_keys;

	/**
	 * @var  array  Hold numeric keys
	 */
	public static $num_keys;

	/**
	 * define_field
	 *
	 * @access  public
	 * @param   object
	 * @param   string
	 * @param   string
	 * @return  array
	 */
	public static function define_field($meta_data, $type = 'gas_field', $driver = '')
	{
		// Get field name
		$field_name = $meta_data->name;

		// Get field raw type
		$field_raw_type = strtoupper($meta_data->type);

		$field_gas_type = '';

		// Determine whether this field is a primary key or not
		$is_key = (bool) $meta_data->primary_key;

		// Determine the global datatype
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
			// Determine the gas spec datatype
			$field_gas_type = self::diagnostic($field_raw_type, 'datatypes');
		}

		// Set the `auto` annotation
		if ($is_key and $field_gas_type == 'int') $field_gas_type = 'auto';

		// Set the `char` annotation
		if ( ! strpos($field_name, 'email') and $field_gas_type == 'email') $field_gas_type = 'char';
		
		if ($type == 'gas_field')
		{
			// Set gas type spec
			$field_type = $field_gas_type;

			// Set gas constraint spec
			$field_length = ($meta_data->max_length > 0) ? '['.$meta_data->max_length.']' : '';
		}
		elseif ($type == 'forge_field')
		{
			if (self::$default_datatypes[$field_gas_type] != $field_raw_type)
			{
				// Set Forge type spec
				$field_type = $field_raw_type;
			}
			else
			{
				$field_type = '';
			}

			// Set Forge constraint spec
			$field_length = ($meta_data->max_length > 0) ? $meta_data->max_length : 0;
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
		// Determine an item based by selected collection
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
		// Instantiate new gas instance
		$root = Gas::factory($gas, array(), FALSE);

		// Get the table name
		$table = self::get_input(__METHOD__, $root->validate_table()->table, TRUE);

		// Get the primary key collumn name
		$primary_key = self::get_input(__METHOD__, $root->primary_key, TRUE);

		// Get the relationship setting
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
		// Get `through` table name
		$through_table = Gas::factory($through, array(), FALSE)->validate_table()->table;

		// Determine the relationship
		$peer_relation = self::get_input(__METHOD__, self::identify_relations($tree['relations'], $tree['child']), TRUE);

		// Get table, primary key and relations for child model
		list($child_through, $child_custom_table, $child_custom_key, $child_self_ref) = self::identify_custom_setting($tree['relations'], $peer_relation, $tree['child']);

		// Get the identifier
		$child_identifier = ($child_custom_key !== '') ? $child_custom_key : $tree['table'].'_'.$tree['key'];

		// Return the necessary information
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

		// Iterate the relationships and return the matched one
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

		// Determine the custom relationship setting
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

		// Iterate the annotation and diagnose it based by datatypes collection
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
		// Build the timestamp spec
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
		// Build the unix timestamp spec
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
			if (isset($arrays[$key])) 
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
			if (isset($arrays[$key])) 
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

		// Determine the recorder type based by action collection
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

		// Build the action list for compile process based by passed recorder
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

		// Iterate all condition record, and make it fit with JOIN portion
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
 * @version     1.4.3
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
 * @version     1.4.3
 */

class Gas extends Gas_core {}