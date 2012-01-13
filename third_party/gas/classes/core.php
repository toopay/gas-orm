<?php namespace Gas;

/**
 * CodeIgniter Gas ORM Packages
 *
 * A lighweight and easy-to-use ORM for CodeIgniter
 * 
 * This packages intend to use as semi-native ORM for CI, 
 * based on the ActiveRecord pattern. This ORM uses CI stan-
 * dard DB utility packages also validation class.
 *
 * @package     Gas ORM
 * @category    ORM
 * @version     2.0.0
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

/**
 * Gas\Core Class.
 *
 * @package     Gas ORM
 * @version     2.0.0
 */

use Gas\Janitor;

class Core {

	/**
	 * @var  string  Global version value 
	 */
	const GAS_VERSION = '2.0.0';
	
	/**
	 * @var  object  Hold DB Instance
	 */
	public static $db;

	/**
	 * @var  object  Hold DB Util Instance
	 */
	public static $dbutil;

	/**
	 * @var  object  Hold DB Forge Instance
	 */
	public static $dbforge;

	/**
	 * @var  array  Hold DB AR properties
	 */
	public static $ar = array(
		'select'                => array(),
		'from'                  => array(),
		'join'                  => array(),
		'where'                 => array(),
		'like'                  => array(),
		'groupby'               => array(),
		'having'                => array(),
		'keys'                  => array(),
		'orderby'               => array(),
		'set'                   => array(),
		'wherein'               => array(),
		'aliased_tables'        => array(),
		'store_array'           => array(),
		'where_group_started'   => FALSE,
		'distinct'              => FALSE,
		'limit'                 => FALSE,
		'offset'                => FALSE,
		'order'                 => FALSE,
		'where_group_count'     => 0,
	);

	/**
	 * @var  array  Hold all defined action collections
	 */
	public static $dictionary = array(
		'transaction_pointer'  => array('trans_off', 
		                                'trans_start', 
		                                'trans_begin'),
		'transaction_executor' => array('trans_complete', 
		                                'trans_rollback', 
		                                'trans_commit'),
		'selector'             => array('select', 
		                                'select_max', 
		                                'select_min', 
		                                'select_avg', 
		                                'select_sum'),
		'condition'            => array('join', 
		                                'where', 
		                                'or_where', 
		                                'where_in', 
		                                'or_where_in', 
		                                'where_not_in', 
		                                'or_where_not_in', 
		                                'like', 
		                                'or_like', 
		                                'not_like', 
		                                'or_not_like', 
		                                'group_by', 
		                                'distinct', 
		                                'having', 
		                                'or_having', 
		                                'order_by', 
		                                'limit', 
		                                'set',
		                                'group_start',
		                                'or_group_start',
		                                'not_group_start',
		                                'group_end'),
		'executor'             => array('insert_string', 
		                                'update_string', 
		                                'insert', 
		                                'insert_batch', 
		                                'update', 
		                                'delete', 
		                                'get', 
		                                'empty_table', 
		                                'truncate', 
		                                'count_all', 
		                                'count_all_results', 
		                                'insert_id', 
		                                'affected_rows', 
		                                'platform', 
		                                'version', 
		                                'last_query'),
		'transaction_status'   => array('trans_status'),

	);

	/**
	 * @var  array  Hold all defined datatypes collections
	 */
	public static $datatypes = array(
		'numeric'  => array('TINYINT', 'SMALLINT',
		                    'MEDIUMINT', 'INT',
		                    'INT2', 'INT4',
		                    'INT8', 'INTEGER',
		                    'BIGINT', 'DECIMAL',
		                    'FLOAT', 'FLOAT4',
		                    'FLOAT8', 'DOUBLE',
		                    'REAL', 'BIT',
		                    'BOOL', 'SERIAL',
		                    'SERIAL8', 'BIGSERIAL',
		                    'DOUBLE PRECISION', 'NUMERIC'),
		'datetime' => array('DATE', 'DATETIME',
		                    'TIMESTAMP', 'TIMESTAMPTZ',
		                    'TIME', 'TIMETZ',
		                    'YEAR', 'INTERVAL'),
		'string'   => array('CHAR', 'BPCHAR',
		                    'CHARACTER', 'VARCHAR',
		                    'TINYTEXT', 'TEXT',
		                    'MEDIUMTEXT', 'LONGTEXT',
		                    'BINARY', 'VARBINARY',
		                    'TINYBLOB', 'MEDIUMBLOB',
		                    'LONGBLOB', 'ENUM',
		                    'SET'),
		'spatial'  => array('GEOMETRY', 'POINT',
		                    'LINESTRING', 'POLYGON',
		                    'MULTIPOINT', 'MULTILINESTRING',
		                    'MULTIPOLYGON', 'GEOMETRYCOLLECTION'),
	);

	/**
	 * @var  array  Hold all default datatypes collections
	 */
	public static $default_datatypes = array(
		'datetime' => 'DATETIME', 
		'string'   => 'TEXT', 
		'spatial'  => 'GEOMETRY', 
		'char'     => 'VARCHAR', 
		'numeric'  => 'TINYINT', 
		'auto'     => 'INT', 
		'int'      => 'INT', 
		'email'    => 'VARCHAR'
	);

	/**
	 * @var  mixed   Hold tasks tree detail for every compile process
	 */
	public static $task_manager;

	/**
	 * @var  mixed   Hold compile result
	 */
	public static $thread_resource;

	/**
	 * @var  array   Hold monitored resorce stated
	 */
	public static $resource_state;

	/**
	 * @var  bool    Per-request cache flag
	 */
	private static $cache = TRUE;

	/**
	 * @var  mixed   Hold cached compile result collection
	 */
	private static $cached_resource;

	/**
	 * @var  array   Hold hashed recorder bundle 
	 */
	private static $cache_key;

	/**
	 * @var  bool    Core class initialization flag
	 */
	private static $init = FALSE;

	/**
	 * Constructor
	 * 
	 * @param  object Database instance
	 * @return void
	 */
	public function __construct(\CI_DB $DB)
	{
		if (self::init_status() == FALSE)
		{
			// Generate needed class name
			$forge = 'CI_DB_'.$DB->dbdriver.'_forge';
			$util  = 'CI_DB_'.$DB->dbdriver.'_utility';

			// Load the DB, DB Util and DB Forge instances
			static::$db      = $DB;
			static::$dbutil  = new $util();
			static::$dbforge = new $forge();

			// Instantiate process has done now
			self::init();
		}
	}

	/**
	 * Set core initialization status
	 * 
	 * @return void
	 */
	public function init()
	{
		static::$init = TRUE;
	}

	/**
	 * Retrieve core initialization status
	 * 
	 * @return void
	 */
	public function init_status()
	{
		return static::$init;
	}

	/**
	 * Serve static calls for core instantiation
	 * 
	 * @param  object Database instance
	 * @return object
	 */
	public static function make(\CI_DB $DB)
	{
		return new static($DB);
	}

	/**
	 * Get all records based by default table name
	 *
	 * @param   object Gas Instance
	 * @return  object Gas Instance
	 */
	final public static function all($gas)
	{
		// Set table and return the execution result
		$gas::$recorder->set('get', array($gas->validate_table()->table));

		return self::_execute($gas);
	}

	/**
	 * Save (INSERT or UPDATE) the record
	 *
	 * @param   object Gas Instance
	 * @param   bool   Whether to perform validation or not
	 * @return  bool
	 */
	final public static function save($gas, $check = FALSE)
	{
		// If `check` set to TRUE, do a validation
		if ($check)
		{
			// Run _before_check and set initial valid mark
			$gas   = call_user_func(array($gas, '_before_check'));
			$valid = TRUE;

			// Do the validation rules, if run from CI environment
			if (function_exists('get_instance') && defined('CI_VERSION'))
			{
				$valid = self::_check($gas);

				if ( ! $valid) return FALSE;
			}
			
			// Run _after_check
			$gas = call_user_func(array($gas, '_after_check'));
		}

		// Run _before_save hook
		$gas = call_user_func(array($gas, '_before_save'));

		// Get the table and entries
		$table   = $gas->validate_table()->table;
		$pk      = $gas->primary_key;
		$entries = $gas->record->get('data');

		// Determine whether to perform INSERT or UPDATE operation
		// by checking `empty` property
		if ($gas->empty)
		{
			// INSERT
			$gas::$recorder->set('insert', array($table, $entries));
		}
		else
		{
			// Extract the identifier
			$identifier = array($pk => $entries[$pk]);
			unset($entries[$pk]);

			// UPDATE
			$gas::$recorder->set('update', array($table, $entries, $identifier));
		}

		// Perform requested saving method
		$save = self::_execute($gas);

		// Run _after_save hook
		$gas = call_user_func(array($gas, '_after_save'));

		return $save;
	}

	/**
	 * Get record based by given primary key arguments
	 *
	 * @param   object Gas Instance
	 * @param   mixed
	 * @return  object Gas Instance
	 */
	final public static function find($gas, $args)
	{
		// Get WHERE IN clause and execute `find_where_in` method,
		// with appropriate arguments.
		$in   = Janitor::get_input(__METHOD__, $args, TRUE);

		// Sort and remove duplicate id
		// Sort the ids and remove same id
		$in = array_unique($in);
		sort($in);

		$gas  = self::compile($gas, 'where_in', array($gas->primary_key, $in));

		return self::all($gas);
	}

	/**
	 * Serve compile method for ORM
	 *
	 * @param  object Gas instance
	 * @param  string 
	 * @param  mixed 
	 * @return mixed
	 */
	public static function compile($gas, $method, $args)
	{
		// Interpret the method and merge argument, for internal method calls
		$internal_method = array('\\Gas\\Core', $method);
		$arguments       = array_merge(array($gas), $args);
		
		if (is_callable($internal_method, TRUE))
		{
			return call_user_func_array($internal_method, $arguments);
		}
	}

	/**
	 * Identify meta-data field spec from various type
	 *
	 * @param   object
	 * @param   string
	 * @param   string
	 * @return  array
	 */
	public static function identify_field($meta_data, $type = 'gas_field', $driver = '')
	{
		// Get name and raw type
		$field_gas_type = '';
		$field_name     = $meta_data->name;
		$field_raw_type = strtoupper($meta_data->type);

		// Determine whether this field is a primary key or not
		$is_key = (bool) $meta_data->primary_key;

		// Determine the global datatype
		foreach (self::$default_datatypes as $gas_type => $default)
		{
			if ($field_raw_type == $default)
			{
				$field_gas_type = $gas_type;

				break;
			}
		}

		// Determine the gas spec datatype
		if ($field_gas_type == '')
		{
			$field_gas_type = self::diagnostic($field_raw_type, 'datatypes');
		}

		// Set the `auto` annotation
		if ($is_key && $field_gas_type == 'int') $field_gas_type = 'auto';

		// Set the `char` annotation
		if ( ! strpos($field_name, 'email') && $field_gas_type == 'email') 
		{
			$field_gas_type = 'char';
		}
		
		if ($type == 'gas_field')
		{
			// Set Gas type and constraint spec
			$field_type   = $field_gas_type;
			$field_length = ($meta_data->max_length > 0) ? '['.$meta_data->max_length.']' : '';
		}
		elseif ($type == 'forge_field')
		{
			// Set Forge type and constraint spec
			if (self::$default_datatypes[$field_gas_type] != $field_raw_type)
			{
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
			$field_type   = '';
			$field_length = 0;
		}

		return array($field_name, $field_type, $field_length, $is_key);
	}

	/**
	 * Identify annotation
	 *
	 * @param   array
	 * @return  array
	 */
	public static function identify_annotation($annotation)
	{
		$boolean        = array('unsigned', 'null', 'auto_increment');
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
	 * Diagnostic an item, against Core dictionary or datatypes
	 *
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
	 * Stop caching
	 *
	 * @return	void
	 */
	public function cache_flush()
	{
		// Flush the cached resources
		self::$cached_resource = NULL;

		return;
	}

	/**
	 * Writes cache pointer for each compile tasks
	 *
	 * @param   array
	 * @return  void
	 */
	public static function cache_start($task)
	{
		if ( ! self::cache_status()) return;

		// Hash the task, and assign it into cache key collection
		self::$cache_key = md5(serialize($task));

		return;
	}
	
	/**
	 * Writes sibling hash for each resource's records
	 *
	 * @param   mixed
	 * @return  void
	 */
	public static function cache_end($resource)
	{
		if ( ! self::cache_status()) return;

		// Assign it into cache resource collection
		$key = self::$cache_key;
		self::$cached_resource[$key] = $resource;

		return;
	}

	/**
	 * Validate cache state
	 * 
	 * @return  bool
	 */
	public static function validate_cache()
	{
		if ( ! self::cache_status()) return;

		// Determine whether a resource is a valid cached 
		return isset(self::$cached_resource[self::$cache_key]);
	}

	/**
	 * Fetching cache collections
	 * 
	 * @return  mixed
	 */
	public static function fetch_cache()
	{
		if ( ! self::cache_status()) return;

		// Return the cached resource
		return self::$cached_resource[self::$cache_key];
	}

	/**
	 * Get cache base configuration
	 *
	 * @access  public
	 * @return  bool
	 */
	public static function cache_status()
	{
		// Get the global caching flag
		return self::$cache;
	}

	/**
	 * Tracking resource state
	 *
	 * @param   string
	 * @param   string
	 * @return  void
	 */
	public static function track_resource($resource, $action)
	{
		// If it not exists, create an empty ones
		if ( ! isset(self::$resource_state[$resource]))
		{
			self::$resource_state[$resource] = array();
		} 

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
	 * Monitoring resource state
	 *
	 * @param   string
	 * @return  bool
	 */
	public static function changed_resource($resource)
	{
		// Return the resource state
		return isset(self::$resource_state[$resource]);
	}

	/**
	 * Reset Select properties within query builder instance
	 *
	 * @param   mixed
	 * @param   string
	 * @return  void
	 */
	public static function reset_query()
	{
		// Reset query and get the cached resource
		if (method_exists(self::$db, 'reset_query'))
		{
			self::$db->reset_query();
		}
		else
		{
			// Get all corresponding AR properties
			$ar = static::$ar;

			array_walk($ar, function ($default, $prop) use(&$ar) { 
				// Set AR property to default value
				$property            = 'ar_'.$prop;
				\Gas\Core::$db->$property = $default;
			});
		}

		return;
	}

	/**
	 * Generate the child model/instance
	 *
	 * @param  object Gas instance
	 * @param  mixed  Gas relationship spec
	 * @param  mixed  Gas parent ids
	 * @return object Child Gas 
	 */
	protected static function _generate_child($gas, $relationship, $ids = array())
	{
		// Define allowed options
		$allowed = array('select', 'order_by', 'limit');

		// Extract relationship properties
		$type    = $relationship['type'];
		$model   = $relationship['model'];
		$key     = $relationship['key'];
		$options = $relationship['options'];
		$through = count($model) > 1;

		// Child information
		$foreign_model = $model[0]::make();
		$fk            = $foreign_model->primary_key;
		$foreign_table = $foreign_model->table;

		// Parent information
		if (is_array($gas))
		{
			// Get a sample instance
			$sample   = $gas;
			$instance = array_shift($sample);

			$pk       = $instance->primary_key;
			$table    = $instance->table;
		}
		else
		{
			$pk      = $gas->primary_key;
			$table   = $gas->table;
		}

		// Generate key and identifier based by relationship type
		switch ($type)
		{
			case 'belongs_to' :
				// check for key
				if (empty($key))
				{
					$key = $foreign_table.'_'.$fk;
				}

				// Generate the identifier
				$identifier = $key;

				// If through not detected, changed back key to pk
				if ( ! $through or $relationship['revert']) $key = $pk;

				break;

			case 'has_one' :
			case 'has_many':
				// check for key
				if (empty($key))
				{
					$key = $table.'_'.$pk;
				}

				// Generate the identifier
				$identifier = $pk;

				break;
		}


		// Grab the ids with the identifier
		if (empty($ids))
		{
			if (is_array($gas))
			{
				foreach ($gas as $entity)
				{
					$ids[] = $entity->$identifier;
				}
			}
			else
			{
				$ids = array($gas->$identifier);
			}
			
		}

		// Unser the first tier model, since its now has been used
		unset($relationship['model'][0]);
		unset($model[0]);

		// Initial instances
		$instances    = NULL;

		// Build the options if exists
		if ( ! empty($options) && ! $through)
		{
			foreach ($options as $option)
			{
				// Parse option annotation
				list($method, $args) = explode(':', $option);

				if ( ! in_array($method, $allowed))
				{
					// No valid method found
					continue;
				}
				else
				{
					// Casting the argument annotation
					// and do the pre-process 
					switch ($method)
					{
						case 'select':
						case 'limit':
							$foreign_model->$method($args);

							break;
						
						case 'order_by':
							if (preg_match('/^([^\n]+)\[(.*?)\]$/', $args, $m) AND count($m) == 3)
							{
								$foreign_model->$method($m[1], $m[2]);
							}

							break;
					}
				}
			}
		}

		// Sort the ids and remove same id
		$ids = array_unique($ids);
		sort($ids);

		// Passed the ids and fetch the child instances
		$foreign_model->where_in($key, $ids);
		$instances = $foreign_model->get();

		// Recursive call for more than one tier model stack
		if ($through)
		{
			// Build tier information from recent level
			$tier_relation   = NULL;
			$sample          = $instances;
			$instance        = is_array($sample) ? array_shift($sample) : $sample;
			$relationships   = $instance::$relationships;
			$path            = array_values($model);

			// Is there match model to run after this one?
			foreach ($relationships as $index => $relation)
			{
				// Get the bottom and up path, as diff identifier
				$rel    = $relation['model'];
				$up     = (count($rel) > count($path)) ? count($rel) : count($path);
				$bottom = (count($rel) < count($path)) ? count($rel) : count($path);

				// Compare the relationship path
				$diff = array_diff($path, $rel);
				$intersection = array_intersect($path, $rel);

				// Gotcha
				if ($up > count($diff) and count($intersection) == count($bottom))
				{
					// Merge the passed options
					if ( ! empty($options))
					{
						$relation['options'] = array_merge($options, $relation['options']);
					}

					// Include the diff onto the courier model
					$relation['model']  = array_merge($rel, $diff);
					$relation['revert'] = TRUE;

					// Set tier index
					$tier_relation = $relation;

					break;
				}
			}

			// Diagnose the next tier relation entity
			if ( ! empty($tier_relation))
			{
				// Valid entity found, make a recursive call
				return self::_generate_child($instances, $tier_relation);
			}
			else
			{
				// Non valid or broke path models relationships occurs
				throw new \LogicException('models_found_no_relations:'.implode(' => ', $path));
			}
		}

		return $instances;
	}

	/**
	 * Execute the compilation command
	 *
	 * @param  object Gas instance
	 * @return object Finished Gas 
	 */
	protected static function _execute($gas)
	{
		// Build the tasks tree
		$tasks = self::_play_record($gas::$recorder);

		// Mark every compile process into our caching pool
		self::cache_start($tasks);

		// Prepare tasks bundle
		$engine    = get_class(self::$db);
		$compiler  = array('gas' => $gas);
		$executor  = static::$dictionary['executor'];
		$write     = array_slice($executor, 0, 6);
		$flag      = array('condition', 'selector');
		$bundle    = array('engine'   => $engine,
		                   'compiler' => $compiler,
		                   'write'    => $write,
		                   'flag'     => $flag);

		// Assign the task to the right person
		self::$task_manager = $bundle;

		// Lets dance...
		array_walk($tasks, function ($task_list, $key) use(&$tasks) { 
			// Only sort if there are valid task and the task manager hold its task list
			if ( ! empty($task_list) or ! empty(\Gas\Core::$task_manager))
			{
				array_walk($task_list, function ($arguments, $key, $task) use(&$task_list) {
					// Only do each task if the task manager hold its task list
					if ( ! empty(\Gas\Core::$task_manager)) 
					{
						// Diagnose the task
						$action = key($arguments);
						$args   = array_shift($arguments);
						$flag   = in_array($task, \Gas\Core::$task_manager['flag']);
						$write  = in_array($action, \Gas\Core::$task_manager['write']);
						$gas    = \Gas\Core::$task_manager['compiler']['gas'];
						$table  = $gas->table;

						if ( ! $flag)
						{
							// Find within cache resource collection
							if ($action == 'get' 
							    && \Gas\Core::validate_cache() 
							    && ! \Gas\Core::changed_resource($table))
							{
								$res = \Gas\Core::fetch_cache();
								\Gas\Core::reset_query();
							}
							else
							{
								$res = call_user_func_array(array(\Gas\Core::$db, $action), $args);
								\Gas\Core::cache_end($res);
							}

							// Post-processing query
							if ($write)
							{
								// Track the resource for any write operations
								\Gas\Core::track_resource($table, $action);
							}
							elseif ($action == 'get')
							{
								// Hydrate the gas instance
								$instances = array();
								$model     = $gas->model();

								foreach ($res->result_array() as $result)
								{
									// Passed the result as record
									$instance = new $model($result);
									$instance->empty = FALSE;

									// Pool to instance holder
									$instances[] = $instance;
									unset($instance);
								}

								$res = count($instances) > 1 ? $instances : array_shift($instances);
							}

							// Tell task manager to take a break, and fill the resource holder
							\Gas\Core::$task_manager    = array();
							\Gas\Core::$thread_resource = $res;
						}
						else
						{
							return call_user_func_array(array(\Gas\Core::$db, $action), $args);
						}
					}
				}, $key);
			}
		});

		// Get the result and immediately flush the temporary resource holder
		$resource = self::$thread_resource and self::$thread_resource = NULL;
		
		// The compilation is done, send the song to listen
		return $resource;
	}

	/**
	 * Generate the Gas tasks spec
	 *
	 * @param  Data  the recorder
	 * @return array task spec
	 */
	protected static function _play_record(Data $recorder)
	{
		// Prepare the tree and set recorder cursor
		$tasks      = array();
		$blank_disc = array_fill(0, count(self::$dictionary), array());
		$tasks      = array_combine(array_keys(self::$dictionary), $blank_disc);
		$recorder->rewind();

		// Iterate over the recorder and match against task dictionary
		while ($recorder->valid())
		{
			foreach (self::$dictionary as $type => $nodes)
			{
				if (in_array($recorder->key(), $nodes))
				{
					$arguments = array($recorder->key() => $recorder->current());
					array_push($tasks[$type], $arguments);
				}
			}

			$recorder->next();
		}

		return $tasks;
	}

	/**
	 * Check for validation process
	 *
	 * @param  object  Gas Instance
	 * @return bool 
	 */
	private static function _check($gas)
	{
		// Initial valid mark
		$valid  = TRUE;
		$errors = array();

		// Grab CI super object and load form validation
		$CI =& get_instance();
		$CI->load->library('form_validation');

		// Grab all necessary lang files
		$CI->lang->load('gas');
		$CI->lang->load('form_validation');

		// Grab the instance records, and set the POST (since CI validator only invoked by it)
		// if there are any POST data, save it temporarily
		$entries  = $gas->record->get('data');
		$old_post = $_POST;
		$_POST    = $entries;
		
		// Extract the rules, and separate beetween,
		// internal callback and CI validation rule
		foreach ($entries as $field => $entry)
		{
			// Get all necessary property for perform validation
			$label     = ucfirst(str_replace('_', ' ', $field));
			$rules     = $gas::$fields[$field]['rules'];
			$callbacks = $gas::$fields[$field]['callbacks'];

			// Set each field's rule respectively	
			$CI->form_validation->set_rules($field, $label, $rules);

			// First we will perform internal callbacks
			if ( ! empty($callbacks))
			{
				foreach ($callbacks as $callback)
				{
					// If defined callback not exists, show error
					if ( ! is_callable(array($gas, $callback)))
					{
						throw new \InvalidArgumentException($callback.' was invalid callback method');
					}

					// Check the callback result
					$success = call_user_func_array(array($gas, $callback), array($entry));
					$method  = substr($callback, 1);

					// If not success, grab the error message
					if ( ! $success)
					{
						// Default callbacks
						$datatype_errors = array('auto_check',
						                         'char_check',
						                         'date_check');

						// If it was default internal error, grab
						// corresponding Gas lang line
						if (in_array($method, $datatype_errors))
						{
							$error = $CI->lang->line($method);
						}
						else
						{
							if (FALSE === ($error = $CI->lang->line($callback)))
							{
								if (FALSE === ($error = $CI->lang->line($method)))
								{
									$error = $callback.' method error with no explanation for %s';
								}
							}
						}

						// Set callback error
						$errors[] = $callback;
						$gas->errors[$field] = sprintf($error, $label);
					}
				}
			}
		}

		// Perform CI validation
		if ($CI->form_validation->run() == FALSE)
		{
			// Set an error boundary
			$boundary = '<ERROR>';

			// Get each error 
			foreach ($entries as $field => $entry)
			{
				if (($error = $CI->form_validation->error($field, $boundary, $boundary)) and $error != '')
				{
					// Parse the error and put it into appropriate field
					$error               = str_replace($boundary, '', $error);
					$gas->errors[$field] = $error;
				}
			}

			$valid = FALSE;
		}

		// Combine internal callback result with CI validation result
		if (count($errors) > 0 or ! $valid)
		{
			$valid = FALSE;
		}

		// Validation has been done, set back the old post and return the validation result
		$_POST = $old_post;

		return $valid;
	}

	
	/**
	 * Overloading static method triggered when invoking special method.
	 *
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	public static function __callStatic($name, $args)
	{
		// Defined DBAL and low-level query function
		$dbal  = array('forge', 'util');
		$query = array('query', 'simple_query');

		if (in_array($name, $dbal))
		{
			// Return corresponding component (DB Forge or DB Util)
			$dbal_component = 'db'.$name;

			return static::$$dbal_component;

		}
		elseif (in_array($name, $query))
		{
			return call_user_func_array(array(static::$db, $name), array(array_pop($args)));
			
		}
		elseif (preg_match('/^find_by_([^)]+)$/', $name, $m) && count($m) == 2)
		{
			// Get the instance, passed field and value for WHERE condition
			$gas   = array_shift($args);
			$field = $m[1];
			$value = array_shift($args);
			
			// Build the task onto the Gas instance
			$gas::$recorder->set('where', array($field, $value));
			
			return self::all($gas);
		}
		elseif (preg_match('/^(min|max|avg|sum)$/', $name, $m) && count($m) == 2)
		{
			// Get the instance, passed arguments for SELECT condition
			$gas   = array_shift($args);
			$type  = $m[1];
			$value = array_shift($args);
			$value = (empty($value)) ? $gas->primary_key : $value;
			
			// Build the task onto the Gas instance
			$gas::$recorder->set('select_'.$type, array($value));
			
			return self::all($gas);
		}
		elseif (preg_match('/^(first|last)$/', $name, $m) && count($m) == 2)
		{
			// Get the instance, passed arguments for ORDER BY condition
			$gas     = array_shift($args);
			$order   = ($m[1] == 'first') ? 'asc' : 'desc';
			$collumn = array_shift($args);
			$collumn = is_null($collumn) ? $gas->primary_key : $collumn;

			// Build the task onto the Gas instance
			$gas::$recorder->set('order_by', array($collumn, $order));
			$gas::$recorder->set('limit', array('1'));
			
			return self::all($gas);
		}
		elseif (($method_type = self::diagnostic($name)) && ! empty($method_type))
		{
			// Give appropriate return, based by each task node needs
			if ($method_type == 'condition' or $method_type == 'selector')
			{
				// Always, sanitize arguments
				$args = Janitor::get_input($name, $args, TRUE);

				// Ensure once, in case there are some deprecated method
				if ( ! is_callable(array(self::$db, $name)))
				{
					throw new \BadMethodCallException('['.$name.']Unknown method.');
				}
		    		
				// Build the task onto the Gas instance
				$gas = array_shift($args);
				$gas::$recorder->set($name, $args);

				return $gas;
			}
			elseif ($method_type == 'executor')
			{
				$executor  = static::$dictionary['executor'];
				$write     = array_slice($executor, 0, 6);
				$operation = array_slice($executor, 6, 4);
				$utility   = array_slice($executor, 10, 6);
				
				if (in_array($name, $utility))
				{
					// This not affected any row or any record
					return self::$db->$name();
				}
				else
				{
					// Always, sanitize arguments
					$args = Janitor::get_input($name, $args, TRUE);

					// Ensure once, in case there are some deprecated method
					if ( ! is_callable(array(self::$db, $name)))
					{
						throw new \BadMethodCallException('['.$name.']Unknown method.');
					}
			    		
					// Build the task onto the Gas instance
					$gas = array_shift($args);

					// Merge the table alongside with sent arguments
					$table    = $gas->validate_table()->table;
					$argument = array_unshift($args, $table);
					$gas::$recorder->set($name, $args);

					return self::_execute($gas);
				}
			}
		}
		else
		{
			// Last try check relationships
			$gas           = array_shift($args);
			$relationships = $gas::$relationships;

			// Iterate over relationship
			foreach ($relationships as $patron => $props)
			{
				// Gotcha
				if ($name == $patron && $gas->empty == FALSE)
				{
					// Check for any pre-process options
					if ( ! empty($args))
					{
						$props['options'] = array_merge($args, $props['options']);
					}

					// Generate the child model
					return self::_generate_child($gas, $props);
				}
			}

			// Good bye
			throw new \BadMethodCallException('['.$name.']Unknown method.');
		}
	}
}