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
	 * @var  bool  Core class initialization flag
	 */
	private static $init = FALSE;

	/**
	 * Constructor
	 * 
	 * @param  object Database instance
	 * @return void
	 */
	public function __construct($DB)
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
	public static function make($DB)
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
		$internal_method = array('Gas\Core', $method);
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
		//self::cache_start($tasks);

		// Prepare tasks bundle
		$engine   = get_class(self::$db);
		$compiler = array('gas' => $gas);
		$flag     = array('condition', 'selector');
		$bundle   = array('engine'   => $engine,
		                  'compiler' => $compiler,
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
						$flag   = ! in_array($task, \Gas\Core::$task_manager['flag']);
						$action = key($arguments);
						$args   = array_shift($arguments);

						if ($flag)
						{
							$res = call_user_func_array(array(\Gas\Core::$db, $action), $args);

							if ($action == 'get')
							{
								$instances = array();
								$gas       = \Gas\Core::$task_manager['compiler']['gas'];
								$model     = $gas->model();

								foreach ($res->result_array() as $result)
								{
									$instance = new $model();
									$instance->set_record('result', $result);
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
	 * Overloading static method triggered when invoking special method.
	 *
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	public static function __callStatic($name, $args)
    {
		// Defined DBAL
		$dbal = array('forge', 'util');

		if (in_array($name, $dbal))
		{
			// Return corresponding component (DB Forge or DB Util)
			$dbal_component = 'db'.$name;

			return static::$$dbal_component;

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
			// Good bye
			throw new \BadMethodCallException('['.$name.']Unknown method.');
		}
    }
}