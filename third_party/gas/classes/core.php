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

use Gas\Data;
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
		                    'DOUBLE PRECISION', 'NUMERIC', 
		                    'LONG'),
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
		                    'SET', 'VAR_STRING',
		                    'BLOB'),
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
	 * @var  array  Hold all common datatypes collections
	 */
	public static $common_datatypes = array(
		'datetime' => 'VARCHAR', 
		'string'   => 'VARCHAR', 
		'spatial'  => 'VARCHAR', 
		'char'     => 'VARCHAR', 
		'numeric'  => 'INTEGER', 
		'auto'     => 'INTEGER', 
		'int'      => 'INTEGER', 
		'email'    => 'VARCHAR'
	);

	/**
	 * @var  array   Paths for included files
	 */
	public static $path;

	/**
	 * @var  array   Migration configuration
	 */
	public static $migration;

	/**
	 * @var  array   Entity meta data repositories
	 */
	public static $entity_repository;

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
	 * @var  array   Auto-generate options
	 */
	private static $auto = array();

	/**
	 * @var  bool    Per-request cache flag
	 */
	private static $cache = TRUE;

	/**
	 * @var  mixed   Hold cached compile result collection
	 */
	private static $cached_resource = array();

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
	 * @param  array  Configuration
	 * @return void
	 */
	public function __construct(\CI_DB $DB, $config = array())
	{
		if (self::init_status() == FALSE)
		{
			// Get configuration up
			$this->_configure($config);

			// Generate needed class name
			$forge = 'CI_DB_'.$DB->dbdriver.'_forge';
			$util  = 'CI_DB_'.$DB->dbdriver.'_utility';

			// Load the DB, DB Util and DB Forge instances
			static::$db      = $DB;
			static::$dbutil  = new $util();
			static::$dbforge = new $forge();

			// Generate new collection of needed properties
			static::$entity_repository = new Data();

			// Check auto-models and tables
			if (static::$auto['models'] == TRUE && static::$auto['tables'] == TRUE)
			{
				throw new \InvalidArgumentException('both_auto_error');
			}
			elseif (static::$auto['models'] == TRUE)
			{
				$this->_generate_models();
			}
			elseif (static::$auto['tables'] == TRUE)
			{
				$this->_generate_tables();
			}

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
	 * @param  array  Configuration
	 * @return object
	 */
	public static function make(\CI_DB $DB, $config = array())
	{
		return new static($DB, $config);
	}

	/**
	 * Use a dsn connection
	 * 
	 * @param  string DSN
	 * @return void
	 */
	public static function connect($dsn)
	{
		$DB =& DB($dsn, TRUE);

		// Consist the DB object
		if ( ! $DB instanceof \CI_DB_Driver)
		{
			throw new \InvalidArgumentException('db_connection_error:'.$dsn);
		}

		// Generate needed class name
		$forge = 'CI_DB_'.$DB->dbdriver.'_forge';
		$util  = 'CI_DB_'.$DB->dbdriver.'_utility';

		// Load the necessary driver classes
		require_once(DBDRIVERSPATH.$DB->dbdriver.DIRECTORY_SEPARATOR.$DB->dbdriver.'_utility.php');
		require_once(DBDRIVERSPATH.$DB->dbdriver.DIRECTORY_SEPARATOR.$DB->dbdriver.'_forge.php');

		// Load the DB, DB Util and DB Forge instances
		static::$db      = $DB;
		static::$dbutil  = new $util();
		static::$dbforge = new $forge();

		// Remove any resource cache
		self::cache_flush();
		self::$resource_state = array();
	}

	/**
	 * Perform callback function within a Gas instance
	 *
	 * @param   object    Gas Instance
	 * @param   string    Hook points
	 * @param   mixed     Argument
	 * @throws  Exception If the callback returned non-ORM instance
	 * @return  object    Gas Instance
	 */
	final public static function callback($gas, $point, $arg = NULL)
	{
		// Get the model name
		$model = $gas->model();

		// Call the corresponding hook point method
		$gas = call_user_func(array($gas, $point), $arg);

		// Make sure the callback always returning a Gas instance
		// Except within `after_save` and `after_delete` points
		if ( ! in_array($point, array('_after_save', '_after_delete')) && ! $gas instanceof ORM)
		{
			throw new \LogicException('Callback '.$point.' within '.$model.' should return an object.');
		}

		return $gas;
	}

	/**
	 * Perform auto-timestamp within a Gas instance
	 *
	 * @param   object    Gas Instance
	 * @return  object    Gas Instance
	 */
	final public static function timestamp($gas)
	{
		$fields = array('ts_fields', 'unix_ts_fields');

		// Check for datetime fields
		foreach ($fields as $field)
		{
			if ( ! empty($gas->$field))
			{
				foreach ($gas->$field as $ts_field)
				{
					if (strpos($ts_field, '[') === 0)
					{
						// Only for new created record
						if ($gas->empty)
						{
							$ts_field = str_replace(array('[',']'), array('',''), $ts_field);
							$gas->$ts_field = ($field == 'ts_fields') ? date('Y-m-d h:i:s') : time();
						}
					}
					else
					{
						$gas->$ts_field = ($field == 'ts_fields') ? date('Y-m-d h:i:s') : time();
					}
				}
			}
		}

		return $gas;
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
		$gas->recorder->set('get', array($gas->validate_table()->table));

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
		$in = Janitor::get_input(__METHOD__, $args, TRUE);

		// Are we deal with composite keys?
		if (is_null($gas->primary_key) && is_array($gas->foreign_key))
		{
			// Build the identifier
			$keys = array_values($gas->foreign_key);

			foreach ($keys as $index => $key)
			{
				foreach ($in as $ids)
				{
					$identifier[$key][] = $ids[$index];
				}
			}

			unset($keys, $index, $key, $ids);
		}
		elseif ( ! empty($gas->primary_key))
		{
			// Sort the ids and remove same id
			$in = array_unique($in);
			sort($in);

			// Set the identifier
			$identifier = array($gas->primary_key, $in);
		}
		else
		{
			// We're lost!
			throw new \InvalidArgumentException('[find]Could not find entity identifier');
		}

		// Determine the identifier
		if (($sample = $identifier) && is_array(array_shift($sample)))
		{
			// We deal with composite table
			foreach ($identifier as $key => $id)
			{
				// Set the identifier
				$unique = array($key, array_values($id));

				// Call the method directly
				call_user_func_array(array(static::$db, 'where_in'), $unique);
			}
		}
		else
		{
			// Easy one, it a standard entity with single key
			$gas = self::compile($gas, 'where_in', $identifier);
		}

		return self::all($gas);
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
			$gas = self::callback($gas, '_before_check');
			$valid = TRUE;

			// Do the validation rules, if run from CI environment
			if (function_exists('get_instance') && defined('CI_VERSION'))
			{
				$valid = self::_check($gas);

				if ( ! $valid) return FALSE;
			}
			
			// Run _after_check
			$gas = self::callback($gas, '_after_check');
		}

		// Run _before_save hook
		$gas = self::callback($gas, '_before_save');

		// Check for timestamp properties
		$gas = self::timestamp($gas);

		// Get the table and entries
		$table   = $gas->validate_table()->table;
		$pk      = $gas->primary_key;
		$fk      = $gas->foreign_key;
		$entries = $gas->record->get('data');

		// Determine whether to perform INSERT or UPDATE operation
		// by checking `empty` property
		if ($gas->empty)
		{
			// Check key integrity
			if (is_null($pk))
			{
				if (empty($fk))
				{
					// We're lost!
					throw new \InvalidArgumentException('[save]Could not save an entity which define relationship');
				}

				// Handle composite keys
				foreach ($fk as $key)
				{
					if ( ! array_key_exists($key, $entries))
					{
						$gas->errors[$key] = sprintf('Could not save a composite entity without valid key : %s', $key);

						return FALSE;
					}
				}
			}

			// INSERT
			$gas->recorder->set('insert', array($table, $entries));
		}
		else
		{
			// Check key integrity
			if (is_null($pk))
			{
				if (empty($fk))
				{
					// We're lost!
					throw new \InvalidArgumentException('[save]Could not save an entity which define relationship');
				}

				// Handle composite keys
				$gas->errors[array_shift($fk)] = 'Could not update a composite entity without parent instance';
				
				return FALSE;
			}
			else
			{
				// Extract the identifier
				$identifier = array($pk => $entries[$pk]);
				unset($entries[$pk]);
			}

			// UPDATE
			$gas->recorder->set('update', array($table, $entries, $identifier));
		}

		// Perform requested saving method
		$save = self::_execute($gas);

		// Run _after_save hook, and passed the SAVE result process
		self::callback($gas, '_after_save', $save);

		return $save;
	}

	/**
	 * Destroy (DELETE) the record
	 *
	 * @param   object Gas Instance
	 * @param   array  Identifier ids
	 * @return  bool
	 */
	final public static function delete($gas, $ids = array())
	{
		// Run _before_delete hook
		$gas = self::callback($gas, '_before_delete');

		// Get the table and entries
		$table   = $gas->validate_table()->table;
		$pk      = $gas->primary_key;
		$fk      = $gas->foreign_key;

		// Do we have ids passed ?
		if ( ! empty($ids))
		{
			// Are we deal with composite keys?
			if (is_null($pk) && is_array($fk))
			{
				// Composite key was read-only and only could deleted via its parent
				$gas->errors[array_shift($fk)] = 'Could not update a composite entity without parent instance';
					
				return FALSE;
			}

			// Set the WHERE IN Clause
			$identifier = array($pk, $ids);
			$gas->recorder->set('where_in', $identifier);
		}

		// DELETE
		$gas->recorder->set('delete', array($table));

		// Perform requested delete method
		$delete = self::_execute($gas);
	
		// Contain relationship to cascade delete ?
		if (($related = $gas->related->get('include')) && is_array($related))
		{
			foreach ($related as $entity)
			{
				// Get tuple and other relationship information
				$tuple         = $gas->meta->get('entities.'.$entity);
				$path          = strpbrk($tuple['path'], '<=');
				$fragments     = explode('=', $path);
				$intermediate  = str_replace('>', '', $fragments[1]);

				// Build the child instance
				$child         = $intermediate::make();
				$child_table   = $child->table;
				$child_key     = $gas->table.'_'.$gas->primary_key;
				$sibling_tuple = $child->meta->get('entities');

				foreach ($sibling_tuple as $root => $family)
				{
					if (strpos(strtolower($family['child']), $gas->model()) !== FALSE
					    && array_key_exists('\\'.$gas->model(), $child->foreign_key))
					{
						$child_key = $child->foreign_key['\\'.$gas->model()];

						break(1);
					}
				}
				
				$child->recorder->set('where_in', array($child_key, $ids));
				$child->recorder->set('delete', array($child_table));

				// Perform cascade delete 
				$delete = self::_execute($child);
			}
		}

		// Run _after_delete hook, and passed the result process
		self::callback($gas, '_after_delete', $delete);

		return $delete;
	}

	/**
	 * Serve `query` for ORM
	 *
	 * @param  string SQL statement
	 * @param  bool   Whether to do `query` or `simple_query` 
	 * @return mixed
	 */
	public static function query($sql, $simple = FALSE)
	{
		if (preg_match('/^SELECT([^)]+)(.*?)$/', $sql, $m) and count($m) == 3)
		{
			// Initial properties
			$result  = NULL;
			$tables  = array();
			$cached  = TRUE;

			// Split into each subquery
			$queries = array_filter(explode('SELECT', $sql));

			// Find corresponding resource name(s)
			foreach ($queries as $query)
			{
				if (preg_match('/FROM([^(]+)WHERE/', $query, $match) and count($match) == 2)
				{
					$tables[] = str_replace(array('`', ' '), '', $match[1]);
					
				}
			}

			// Start cache process
			$token = md5(serialize(array($sql)));
			self::cache_start(array($sql), FALSE);

			// Validate cache
			if (self::validate_cache($token))
			{
				foreach ($tables as $table)
				{
					if (self::changed_resource($table))
					{
						// If any of corresponding table involve, has been modified
						// Clear cached flag
						$cached = FALSE;

						break;
					}
				}
			}
			else
			{
				// No valid cache 
				$cached = FALSE;
			}

			// Determine to fetch the cache of perform fresh query onto DB
			if ($cached == TRUE)
			{
				$result = self::fetch_cache($token);
			}
			else
			{
				$result = static::$db->query($sql);
				self::cache_end($result, $token);
			}

			return $result;
		}

		// No need to process anything, 
		// Just forward the query into DB instance
		return ($simple) ? static::$db->simple_query($sql) : static::$db->query($sql);
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
		$query           = array('query', 'simple_query');

		if (in_array($method, $query))
		{
			$query_method = array(static::$db, $method);
			$query_arg    = array(array_pop($args));
			$query_result = call_user_func_array($query_method, $query_arg);

			return $query_result;
		}
		elseif (is_callable($internal_method, TRUE))
		{
			if ($method == 'delete')
			{
				// Check whether the entity already hold some id
				// or is it passed by arguments
				if ( ! $gas->empty)
				{
					$identifier = $gas->primary_key;

					// Check key integrity
					if (is_null($identifier))
					{
						// Are we deal with composite keys?
						if (is_array($gas->foreign_key))
						{
							// Composite key was read-only and only could deleted via its parent
							$fk = $gas->foreign_key;
							$gas->errors[array_shift($fk)] = 'Could not delete a composite entity without parent instance';
								
							return FALSE;
						}
						
						// We're lost!
						throw new \InvalidArgumentException('[delete]Could not delete an entity which define relationship');
					}

					// Re-merge the arguments
					$args      = array($gas->$identifier);
					$arguments = array($gas, $args);
				}
				else
				{
					// Just bundle the passed identifier
					$arguments = array_merge(array($gas), array($args));
				}
			}

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
			if (self::$default_datatypes[$field_gas_type] != $field_raw_type or ! isset(self::$db->pdodriver))
			{
				if (in_array($field_raw_type, array('LONG', 'BLOB', 'VAR_STRING')) && isset(self::$db->pdodriver))
				{
					$field_type = self::$common_datatypes[$field_gas_type];
				} 
				else
				{
					$field_type = $field_raw_type;
				}
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
	public static function cache_flush()
	{
		// Flush the cached resources
		self::$cached_resource = array();

		return;
	}

	/**
	 * Writes cache pointer for each compile tasks
	 *
	 * @param   array
	 * @param   bool   Whether to save into global cache key or not
	 * @return  void
	 */
	public static function cache_start($task, $global = TRUE)
	{
		if ( ! self::cache_status()) return;

		// Hash the task, and assign it into cache key collection
		$key = md5(serialize($task));

		if ($global)
		{
			self::$cache_key = $key;
		}

		if ( ! array_key_exists($key, self::$cached_resource))
		{
			// Generate empty cache holder
			self::$cached_resource[$key] = NULL;
		}

		return;
	}
	
	/**
	 * Writes sibling hash for each resource's records
	 *
	 * @param   mixed    DB resource or any data
	 * @param   string   Cache key
	 * @return  void
	 */
	public static function cache_end($resource, $key = NULL)
	{
		if ( ! self::cache_status()) return;

		// Assign it into cache resource collection
		if (empty($key))
		{
			$key = self::$cache_key;
		}
	
		self::$cached_resource[$key] = $resource;

		return;
	}

	/**
	 * Validate cache state
	 * 
	 * @param   string  Cache key
	 * @return  bool
	 */
	public static function validate_cache($key = NULL)
	{
		if ( ! self::cache_status()) return;

		if (empty($key))
		{
			$key = self::$cache_key;
		}

		// Determine whether a resource is a valid cached 
		if (array_key_exists($key, self::$cached_resource) && ! empty(self::$cached_resource[$key]))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Fetching cache collections
	 * 
	 * @param   string  Cache key
	 * @return  mixed
	 */
	public static function fetch_cache($key = NULL)
	{
		if ( ! self::cache_status()) return;

		if (empty($key))
		{
			$key = self::$cache_key;
		}

		// Return the cached resource
		return self::$cached_resource[$key];
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
	 * Reports resource state
	 *
	 * @param   object Gas instance
	 * @return  mixed  All resource state
	 */
	public static function reports($gas)
	{
		// Return the resource state
		return isset(self::$resource_state[$gas->table]) ? self::$resource_state[$gas->table] : array();
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
		if (method_exists(static::$db, 'reset_query'))
		{
			static::$db->reset_query();
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
	 * Generate the related entities of model/instance
	 *
	 * @param  object Gas instance
	 * @param  mixed  Gas relationship spec
	 * @param  array  Resource collection
	 * @param  bool   Whether to return the SQL statement or execute then send its result
	 * @return object Child Gas 
	 */
	public static function generate_entity($gas, $relationship, $resources = array(), $raw = FALSE)
	{
		// Get the relationship properties
		$path    = $relationship['path'];
		$child   = $relationship['child'];
		$single  = $relationship['single'];
		$options = $relationship['options'];
		$roadmap = explode('=', $path);

		// Now we are in serious business
		if ( ! empty($resources))
		{
			// Generate original identifier and entities holder
			$holder         = new Data();
			$original_table = $gas->table;
			$original_pk    = $gas->primary_key;
			$original_ids   = array();

			foreach ($resources as $resource)
			{
				// Populate the ids
				$original_ids[] = $resource[$original_pk];

				// Generate new token and empty holder for each original identifier
				$token = $original_table.':'.$original_pk.'.';
				$index = $resource[$original_pk];
				$holder->set("$token$index", array($index));
			}
		}

		// Generate the tuple
		$tuples = array();
		$index  = 0;
		$max    = count($roadmap) - 1;

		// The goal is to parse full path :
		//		Model\Foo=>Model\Bar<=Model\Lorem
		//
		// Into paired tuples like :
		//		Model\Foo>Model\Bar
		//		Model\Bar<Model\Lorem
		//
		// `>` or `<`, thus identify entity ownership
		do {
			$dirty_tuple = $roadmap[$index].$roadmap[$index+1];

			if (in_array(substr($dirty_tuple, 0, 1), array('>', '<')))
			{
				$tuples[] = substr($dirty_tuple, 1);
			}
			elseif (in_array(substr($dirty_tuple, -1), array('>', '<')))
			{
				$tuples[] = substr($dirty_tuple, 0, -1);
			}
			else
			{
				$tuples[] = $dirty_tuple;
			}

			$index++;
		} while ($index < $max);

		// Query holder
		$queries = array();
			
		// Then generate nested query to fetch each record entity
		foreach ($tuples as $level => $tuple)
		{
			list($domain, $key, $identifier) = self::generate_identifier($tuple);

			if ($level == 0)
			{
				if (isset($holder))
				{
					// This mean we really have a business
					$ids = $original_ids;
				}
				else
				{
					// We handle a single instance here
					$ids[] = $gas->record->get('data.'.$identifier);
				}

				$queries[] = array($domain, $key, '');
			}
			else
			{
				// Get previous tier index
				$lower_level = $queries[$level-1];

				if (isset($holder))
				{
					// If holder exists we need to also adding corresponding collumn
					$paired_cols = array_unique(array($identifier, $lower_level[1]));
					$lower_query = self::generate_clause($lower_level[0], $paired_cols, $lower_level[1], '');
					$queries[]   = array($domain, $key, $lower_query);
				}
				else
				{
					// Straight forward sub-query
					$lower_query = self::generate_clause($lower_level[0], $identifier, $lower_level[1], $lower_level[2]);
					$queries[]   = array($domain, $key, $lower_query);
				}
			}
		}

		// Parse the ids into string
		$ids = implode(', ', $ids);

		// Finalize entity generator
		if (count($queries) == 1)
		{
			// We handle one level of relationship, easy...
			$query     = array_shift($queries);
			$subquery  = $ids;
			$domain    = $query[0];
			$candidate = $query[1];
		}
		else
		{
			// If there was a holder, we have to do something first 
			if (isset($holder))
			{
				// Before doing anything, get as much info as possible
				$original_queries = $queries;

				// Parse necessary info
				$query     = array_pop($queries);
				$subquery  = sprintf(array_pop($query), $ids);
				$domain    = $query[0];
				$candidate = $query[1];

				// Doing effective sub-queries for `with` marked records
				foreach ($original_queries as $level => $original_query)
				{
					if (empty($original_query[2]))
					{
						// Take the identifier for further use
						$holder->set('identifier', $original_query[1]);
						$holder->set('ids', $original_ids);
					}
					else
					{
						$sql         = sprintf($original_query[2], implode(',', $holder->get('ids')));
						$subresults  = self::query($sql)->result_array();
						
						$identifier  = $original_query[1];
						$matched_id  = array();
						$subids      = array();

						foreach ($subresults as $index => $subresult)
						{
							$all_identifier = array_keys($subresult);
							$old_identifier = $holder->get('identifier');

							if (count($all_identifier) == 1)
							{
								$new_identifier = array_shift($all_identifier);
							}
							else
							{
								$new_identifier = array_diff($all_identifier, array($old_identifier));
								$new_identifier = array_shift($new_identifier);
							}

							$matcher_id     = $subresult[$old_identifier];
							$identifier_id  = $subresult[$new_identifier];

							foreach ($original_ids as $original_id)
							{
								if ( ! is_array($holder->get($token.$original_id)))
								{
									// Do nothing
								}
								elseif (is_array($holder->get($token.$original_id)))
								{
									// we have assoc ids
									if (in_array($matcher_id, $holder->get($token.$original_id)))
									{
										// Found matched identifier, save it to holder
										$matched_id[$original_id][] = $identifier_id;
									}
									else
									{
										// Generate empty values
										$matched_id[$original_id][] = NULL;
									}
								}
								else
								{
									// We've lost!
									throw new \InvalidArgumentException('empty_arguments:'. __METHOD__);
								}
							}

							// Save the identifier ids for further use
							$subids[] = $identifier_id;
						}

						// Make sure we have unique ids
						$subids = array_unique($subids);
						sort($subids);

						// Save above process into holder Data
						$holder->set('ids', $subids);
						$holder->set('identifier', $identifier);
						
						// Perform checking to assign each new identifier id
						// For further process, into each original ids
						foreach($matched_id as $id => $matched)
						{
							$holder->set($token.$id, array_filter($matched));
						}
					}
				}

				// Build the subquery
				$subquery = implode(', ', $holder->get('ids'));
			}
			else
			{
				// We have more than one tiers level, get the last...
				$query     = array_pop($queries);
				$subquery  = sprintf(array_pop($query), $ids);
				$domain    = $query[0];
				$candidate = $query[1];
			}
		}

		// Initiate empty additional queries
		$order_by = '';
		$limit    = '';
		
		// Initial select would be SELECT *
		// unless there are pre-query option to overide it
		$key = '*';

		// Do we have pre-process query options ?
		if (count($options) > 0)
		{
			$additional_queries = self::generate_options($options);

			// Do we need to overide the default key for SELECT clause ?
			if (array_key_exists('select', $additional_queries))
			{
				$key = $additional_queries['select'];

				// Lets make sure the identifier was included
				if ( ! in_array($candidate, $key)) $key[] = $candidate;
			}

			// Do we have ORDER BY clause ?
			if (array_key_exists('order_by', $additional_queries))
			{
				$order_by = ' ORDER BY `$domain`'.$additional_queries['order_by'];
			}

			// Do we have LIMIT clause ?
			if (array_key_exists('limit', $additional_queries))
			{
				$limit = ' LIMIT '.$additional_queries['limit'];
			}
		}

		// Finalize the SQL statement
		$sql = self::generate_clause($domain, $key, $candidate, $subquery);

		// Do we need to continue, or just return the full SQL statement ?
		if ($raw) return $sql;

		// By now, we could generate the result
		$childs = array();
		$res    = self::query($sql)->result_array();

		// In case we handle a holder...
		$matched_id = array();

		foreach ($res as $row)
		{
			// Hydrate child entities
			$child_instance        = new $child($row);
			$child_instance->empty = FALSE;

			// We have associative ids to check
			if (isset($holder))
			{
				foreach ($original_ids as $original_id)
				{
					// Get the identifier to check
					$matcher_id = $row[$holder->get('identifier')];

					// We have assoc ids to check against it
					if (in_array($matcher_id, $holder->get($token.$original_id)))
					{
						$matched_id[$original_id][] = $child_instance;
					}
					else
					{
						$matched_id[$original_id][] = NULL;
					}
				}
			}

			$childs[] = $child_instance;
		}
		
		// All done
		if (isset($holder))
		{
			$final_key = substr($token,0,-1);

			list($table, $identifier) = explode(':', $final_key);

			// Build the holder
			$holder->set('data', array_filter($matched_id));
			$holder->set('identifier', $identifier);
			$holder->set('ids', array_keys($matched_id));

			// Transfer into save place, then unset the holder
			$final_entities = $holder;

			unset($holder);

			return $final_entities;
		}
		else
		{
			return ($single) ? array_shift($childs) : $childs;
		}
		
	}

	/**
	 * Generate the all necessary identifier based a tuple
	 *
	 * @param  string  Tuple
	 * @return array   Domain, key and identifier
	 */
	public static function generate_identifier($tuple)
	{
		if ( ! self::$entity_repository->get('tuples.'.$tuple))
		{
			// Initial empty
			$direction = '';

			if (strpos($tuple, '<') !== FALSE)
			{
				// We found this pattern direction :
				// 		Model\Foo<Model\Bar
				// This mean Model\Bar is OWNED by Model\Foo
				list($left, $right) = explode('<', $tuple);
				$direction = '<=';
			}
			elseif  (strpos($tuple, '>') !== FALSE)
			{
				// We found this pattern direction :
				// 		Model\Foo>Model\Bar
				// This mean Model\Foo is OWNED by Model\Bar
				list($left, $right) = explode('>', $tuple);
				$direction = '=>';
			}
			else
			{
				// We dont know this one, for sure
				throw new \LogicException('models_found_no_relations:'.$tuple);
			}

			// Build parent information
			$parent_model = $left::make();
			$parent_name  = '\\'.$parent_model->model();
			$parent_table = $parent_model->table;
			$parent_pk    = $parent_model->primary_key;

			// Build child information
			$child_model  = $right::make();
			$child_name   = '\\'.$child_model->model();
			$child_table  = $child_model->table;
			$child_pk     = $child_model->primary_key;

			// Generate `key` and `identifier` information for query processing
			switch ($direction)
			{
				case '<=':
					if (array_key_exists($parent_name, $child_model->foreign_key))
					{
						$key = $child_model->foreign_key[$parent_name];
					}
					else
					{
						$key = $parent_table.'_'.$parent_pk;
					}

					$identifier = $parent_pk;

					break;

				case '=>':
					$key = $child_pk;

					if (array_key_exists($child_name, $parent_model->foreign_key))
					{
						$identifier = $parent_model->foreign_key[$child_name];
					}
					else
					{
						$identifier = $child_table.'_'.$child_pk;
					}


					break;
			}

			// Build the tuple information
			$tuple_information = array($child_table, $key, $identifier);

			// Save onto entity repositories
			self::$entity_repository->set('tuples.'.$tuple, $tuple_information);
		}
		else
		{
			// Build the tuple information from entity repositories
			$tuple_information = self::$entity_repository->get('tuples.'.$tuple);
		}

		// Give them final tuple information
		return $tuple_information;
	}

	/**
	 * Generate the relationship option for pre-process queries
	 *
	 * @param  array  Gas relationship option spec
	 * @return array  Formatted option
	 */
	public static function generate_options($options)
	{
		// Initiate new queries holder, and define allowable options
		$queries = array();
		$allowed = array('select', 'order_by', 'limit');

		// Loop over it
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
						$select_statement = explode(',', $args);
						$queries[$method] = Janitor::arr_trim($select_statement);

						break;

					case 'limit':
						$queries[$method] = " 0, $args";

						break;
					
					case 'order_by':
						if (preg_match('/^([^\n]+)\[(.*?)\]$/', $args, $m) AND count($m) == 3)
						{
							$queries[$method] = "`$m[1]` strtoupper($m[2])";
						}

						break;
				}
			}
		}

		// Return the formatted queries options
		return $queries;
	}

	/**
	 * Generate SELECT %s FROM %s WHERE & IN (%s) clauses
	 * This is used by entity generator only (internal usage).
	 *
	 * @param  string  Table name
	 * @param  string  Key collumn name
	 * @param  string  Identifier collumn name
	 * @param  string  Either ids or subquery
	 * @return array   Formatted SQL clause
	 */
	public static function generate_clause($domain, $key, $identifier, $ids = '')
	{
		// Define the BACKTICKS part
		if (static::$db->dbdriver == 'postgre')
		{
			$bt = '"';
		}
		elseif (static::$db->dbdriver == 'sqlite')
		{
			$bt = '';
		}
		else
		{
			$bt = '`';
		}

		// Generate subquery
		if ($key == '*')
		{
			// Do we have special selector char
			$pattern = "SELECT * FROM $bt$domain$bt WHERE $bt$domain$bt.$bt$identifier$bt IN (%s)";
		}
		elseif (is_array($key))
		{
			// Initial empty select
			$select = array();

			// We need to add protector and identifier
			foreach ($key as $collumn)
			{
				$select[] = "$bt$domain$bt.$bt$collumn$bt";
			}

			$key = implode(', ', $select);
			
			$pattern = "SELECT $key FROM $bt$domain$bt WHERE $bt$domain$bt.$bt$identifier$bt IN (%s)";
		}
		else
		{
			// Default pattern
			$pattern = "SELECT $bt$domain$bt.$bt$key$bt FROM $bt$domain$bt WHERE $bt$domain$bt.$bt$identifier$bt IN (%s)";
		}

		// Do we need to replace the string identifier
		// Either into sub-query or the real COLUMN value(s) ?
		if ( ! empty($ids))
		{
			$pattern = sprintf($pattern, $ids);
		}

		// Statement is ready
		return $pattern;
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
		$tasks = self::_play_record($gas->recorder);

		// Mark every compile process into our caching pool
		self::cache_start($tasks);

		// Prepare tasks bundle
		$engine    = get_class(static::$db);
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
								$dbal_method = array(\Gas\Core::$db, $action);
								$res         = call_user_func_array($dbal_method, $args);
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
								$entities  = array();
								$ids       = array();
								$model     = $gas->model();
								$extension = $gas->extension;
								$includes  = $gas->related->get('include', array());
								$relation  = $gas->meta->get('entities');

								// Do we have entities to eagerly-loaded?
								if (count($includes))
								{
									// Then generate new colleciton holder for it
									$tuples = new \Gas\Data();
								}

								// Get the array of fetched rows
								$results   = $res->result_array();

								// Generate the entitiy records
								foreach ($results as $result)
								{
									// Passed the result as record
									$instance        = new $model($result);
									$instance->empty = FALSE;

									foreach ($includes as $include)
									{
										if (array_key_exists($include, $relation))
										{
											$table      = $instance->table;
											$pk         = $instance->primary_key;
											$identifier = $instance->record->get('data.'.$pk);
											$concenate  = $table.':'.$pk.':'.$identifier;
											$tuple      = $relation[$include];

											if ($tuples->get('entities.'.$include))
											{
												// Retrieve this user entity
												$assoc_entities = $tuples->get('entities.'.$include);
											}
											else
											{
												$assoc_entities = \Gas\Core::generate_entity($gas, $tuple, $results);
												$tuples->set('entities.'.$include, $assoc_entities);
											}

											// Assign the included entity, respectively
											$entity = array_values(array_filter($assoc_entities->get('data.'.$identifier, array())));
											$instance->related->set('entities.'.$include, $entity);
										}
									}
								
									// Pool to instance holder and unset the instance
									$instances[] = $instance;
									unset($instance);
								}
								
								// Determine whether to return an instance or a collection of instance(s)
								$res = count($instances) > 1 ? $instances : array_shift($instances);

								// Do we need to return the result, or passed into some extension?
								if ( ! empty($extension) && $extension instanceof Extension)
								{
									$res = $extension->__init($res);
								}
							}

							// Tell task manager to take a break, and fill the resource holder
							\Gas\Core::$task_manager    = array();
							\Gas\Core::$thread_resource = $res;
						}
						else
						{
							// Return the native DB driver method execution
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
			$rules     = $gas->meta->get('fields.'.$field.'.rules', '');
			$callbacks = $gas->meta->get('fields.'.$field.'.callbacks', array());

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
						throw new \BadMethodCallException('['.$callback.'] Invalid callback method');
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
	 * Handle configuration
	 *
	 * @param	array
	 * @return	void
	 */
	private function _configure($config = array())
	{
		// Validate configuration
		$keys = array('models_path', 'cache_request', 'auto_create_models', 'auto_create_tables');

		foreach ($keys as $key)
		{
			if ( ! array_key_exists($key, $config))
			{
				throw new \RuntimeException('Invalid runtime configuration.');
			}
		}

		// Set global configuration
		static::$cache = $config['cache_request'];
		static::$auto  = array('models' => $config['auto_create_models'],
		                       'tables' => $config['auto_create_tables']);

		// Populate possible paths
		if (is_array($config['models_path']))
		{
			$paths = $config['models_path'];
		}
		else
		{
			// New convention require a paired of namespace - path, sorry...
			throw new \InvalidArgumentException('models_not_found:'.$config['models_path']);
		}

		// Set `models` directories look-up
		static::$path['model'] = $paths;

		// Get migration config
		static::$migration = $config['migration'];

		// Register autoloader
		spl_autoload_register(array($this, '_autoloader'));
	}

	/**
	 * Serve autoloader
	 *
	 * @param	string
	 * @return	void
	 */
	private function _autoloader($class) 
	{
		// Prepare autoload mechanism
		if (($fragments = explode('\\', $class))
		    && count($fragments) > 1
		    && is_array(static::$path))
		{
			// Parse the slash
			$class = ltrim($class, '\\');
			$fragments = explode('\\', $class);
			
			// Parse the namespace spec for further process
			$namespace = strtolower(array_shift($fragments));
			$filename  = strtolower(array_pop($fragments));
			$ori_path  = strtolower(implode(DIRECTORY_SEPARATOR, $fragments));

			// Finalize the path
			$full_namespace = (empty($ori_path)) ? $namespace : $namespace.'\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $ori_path);
			$path = (empty($ori_path)) ? DIRECTORY_SEPARATOR : DIRECTORY_SEPARATOR.$ori_path.DIRECTORY_SEPARATOR;

			// Check for extension first
			if (strpos($class, 'Gas\\Extension') !== FALSE)
			{
				// There are only two target directories for this :
				// 1. GASPATH.'classes/extension'
				// 2. APPPATH.'libraries/gas/extension'
				$extension_paths = array(GASPATH.'classes',
				                         APPPATH.'libraries'.DIRECTORY_SEPARATOR.'gas');

				// Loop over the paths
				foreach ($extension_paths as $extension_path)
				{
					if (file_exists($extension_path.$path.$filename.'.php'))
					{
						// Gotcha
						include_once $extension_path.$path.$filename.'.php';

						return TRUE;
					}
				}
			}

			// Process matched directory
			if (array_key_exists($namespace, static::$path)
			    && ($directories = static::$path[$namespace]))
			{
				// Walk through files and possible path
				foreach ($directories as $ns => $dir)
				{
					$orm_path = str_replace(strtolower($ns), '', $full_namespace);
					$orm_path = str_replace('\\', DIRECTORY_SEPARATOR, $orm_path).DIRECTORY_SEPARATOR;

					if (file_exists($dir.$orm_path.$filename.'.php'))
					{
						include_once($dir.$orm_path.$filename.'.php');
						break;
					}
				}
			}
		}
	}

	/**
	 * Generate models based by curent schema
	 * This is used by config only (internal usage).
	 *
	 * @return void   
	 */
	private function _generate_models()
	{
		// Get the tables
		$tables = self::$db->list_tables(TRUE);
		$counter = 0;

		// Generate models
		foreach ($tables as $table) 
		{
			//Avoid migration table
			if($table != self::$migration['migration_table'])
			{
				// Build table and field definition
				$key = array();
				$forge_key = '';
				$primary_key = '';
				$field_meta = self::$db->field_data($table);
				$field_definition = 'self::$fields = array('."\n";
				$field_migration = '$this->dbforge->add_field(array('."\n";

				foreach ($field_meta as $meta)
				{
					// Build field definition
					$definition = self::identify_field($meta);
					$field_definition .= "\t\t\t".'\''.$definition[0].'\' => ORM::field(\''.$definition[1].$definition[2].'\'),'."\n";

					if ($definition[3] == TRUE && empty($primary_key))
					{
						$primary_key = "\n\t".'public $primary_key = \''.$definition[0].'\';'."\n";
					}

					// Build field migration
					$migration = self::identify_field($meta, 'forge_field');
					$field_migration .= "\t\t\t".'\''.$migration[0].'\' => array('."\n"
					                   ."\t\t\t\t".'\'type\' => \''.$migration[1].'\','."\n"
					                   ."\t\t\t\t".'\'constraint\' => '.$migration[2].','."\n"
					                   ."\t\t\t".'),'."\n";

					if ($migration[3] == TRUE) $key[] = $migration[0];
				}

				$field_definition .= "\t\t".');'."\n";
				$field_migration .= "\t\t".'));'."\n";

				if (count($key) > 0)
				{
					foreach ($key as $pk)
					{
						$forge_key .= "\n\t\t".'$this->dbforge->add_key(\''.$pk.'\', TRUE);'."\n";
					}
				}

				$field_migration .= $forge_key;

				// Build model component
				$fragment = explode('_', $table);
				$namespace = key(static::$path['model']);
				$path = static::$path['model'][$namespace];

				if (count($fragment) == 1)
				{
					$model = ucfirst(current($fragment));
				}
				else
				{
					$model = ucfirst(array_pop($fragment));
					$namespace .= '\\'.implode('\\', array_map('ucfirst', $fragment));
					$path .= DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $fragment);

					if ( ! is_dir($path)) mkdir($path, DIR_WRITE_MODE);
				}

				// Build the model
				$model_convention = read_file(GASPATH.'template'.DIRECTORY_SEPARATOR.'model.tpl');
				$model_convention = sprintf($model_convention, $namespace, $model, $primary_key, $field_definition);
				
				// Write the model
				if ( ! write_file($path.DIRECTORY_SEPARATOR.strtolower($model).'.php', $model_convention))
				{
					throw new \RuntimeException('cannot_create_model:'.$path.DIRECTORY_SEPARATOR.strtolower($model).'.php');
				}

				// Build the migration
				$migration_path = self::$migration['migration_path'];
				
				if ( ! is_dir($migration_path)) mkdir($migration_path, DIR_WRITE_MODE);

				$migration_name = (($counter+1) < 10) ? '00'.($counter+1).'_'.$table : 
				                  ((($counter+1) < 100) ? '0'.($counter+1).'_'.$table : ($counter+1).'_'.$table);

				$field_migration_up = $field_migration."\n\t\t".'$this->dbforge->create_table(\''.$table.'\', TRUE);';
				$field_migration_down = '$this->dbforge->drop_table(\''.$table.'\');';
				$migration_convention = read_file(GASPATH.'template'.DIRECTORY_SEPARATOR.'migration.tpl');
				$migration_convention = sprintf($migration_convention, 'Migration_'.$table, $field_migration_up, $field_migration_down);

				// Write the migration
				if ( ! write_file($migration_path.strtolower($migration_name).'.php', $migration_convention))
				{
					throw new \RuntimeException('cannot_create_migration:'.$migration_path.strtolower($migration_name).'.php');
				}

				// Increment the counter
				$counter++;
			}
		}
	}

	/**
	 * Generate tables based by existed models
	 * This is used by config only (internal usage).
	 *
	 * @return void   
	 */
	private function _generate_tables()
	{
		// Get all models
		$model_paths = self::$path['model'];

		foreach ($model_paths as $namespace => $model_path)
		{
			if (is_dir($model_path))
			{
				$models = get_filenames($model_path, TRUE);
				
				foreach ($models as $model)
				{
					// Sort only php file
					if (strpos($model, 'php') === FALSE) continue;

					// Instantiate all models to collect all information
					$raw_model_name = str_replace($model_path, '<PATH>', $model);
					$model_name = end(explode('<PATH>', $raw_model_name));

					// Parse directory separator
					if (strpos($model_name, DIRECTORY_SEPARATOR) === 0)
					{
						$model_name = substr($model_name, 1);
					}

					$model_name = str_replace(array(DIRECTORY_SEPARATOR, '.php'), array('\\', ''), $model_name);
					$model_name = $namespace.'\\'.$model_name;

					// Collect all model(s) info, by instantiate it
					$model_name::make();
				}
			}
		}

		$counter = 0;
		$entity_repository = self::$entity_repository->get('models');

		// Now, itterate over entity repository to generate migration files
		foreach ($entity_repository as $entity)
		{
			$table = $entity['table'];
			$fields = $entity['fields'];
			$field_annotation = array();

			foreach ($fields as $field => $prop)
			{
				$field_annotation[$field] = self::identify_annotation($prop['annotations']);
			}

			$field_migration = '$this->dbforge->add_field(array('."\n";

			foreach ($field_annotation as $field_name => $meta)
			{
				// Build field migration
				$field_migration .= "\t\t\t".'\''.$field_name.'\' => array('."\n";

				foreach ($meta as $type => $value) $field_migration .= "\t\t\t\t".'\''.$type.'\' => \''.$value.'\','."\n";

				$field_migration .= "\t\t\t".'),'."\n";
			}

			$field_migration .= "\t\t".'));'."\n";

			// Build the migration
			$migration_path = self::$migration['migration_path'];
			
			if ( ! is_dir($migration_path)) mkdir($migration_path, DIR_WRITE_MODE);

			$migration_name = (($counter+1) < 10) ? '00'.($counter+1).'_'.$table : 
			                  ((($counter+1) < 100) ? '0'.($counter+1).'_'.$table : ($counter+1).'_'.$table);

			$field_migration_up = $field_migration."\n\t\t".'$this->dbforge->create_table(\''.$table.'\', TRUE);';
			$field_migration_down = '$this->dbforge->drop_table(\''.$table.'\');';
			$migration_convention = read_file(GASPATH.'template'.DIRECTORY_SEPARATOR.'migration.tpl');
			$migration_convention = sprintf($migration_convention, 'Migration_'.$table, $field_migration_up, $field_migration_down);

			// Write the migration
			if ( ! write_file($migration_path.strtolower($migration_name).'.php', $migration_convention))
			{
				throw new \RuntimeException('cannot_create_migration:'.$migration_path.strtolower($migration_name).'.php');
			}

			// Increment the counter
			$counter++;
		}

		// Late binding to flagged auto-migration process
		static::$migration['auto'] = TRUE;
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
		// Defined DBAL component
		$dbal  = array('forge', 'util');

		if (in_array($name, $dbal))
		{
			// Return corresponding component (DB Forge or DB Util)
			$dbal_component = 'db'.$name;

			return static::$$dbal_component;

		}
		elseif ($name == 'last_created')
		{
			// Get last created entry
			if (($last_id = static::$db->insert_id()) && empty($last_id))
			{
				// Nothing
				return NULL;
			}

			// Return the corresponding model instance with last id
			$gas = array_shift($args);

			return self::find($gas, array($last_id));
		}
		elseif (preg_match('/^find_by_([^)]+)$/', $name, $m) && count($m) == 2)
		{
			// Get the instance, passed field and value for WHERE condition
			$gas   = array_shift($args);
			$field = $m[1];
			$value = array_shift($args);
			
			// Build the task onto the Gas instance
			$gas->recorder->set('where', array($field, $value));
			
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
			$gas->recorder->set('select_'.$type, array($value));
			
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
			$gas->recorder->set('order_by', array($collumn, $order));
			$gas->recorder->set('limit', array('1'));
			
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
				if ( ! is_callable(array(static::$db, $name)))
				{
					throw new \BadMethodCallException('['.$name.']Unknown method.');
				}
		    		
				// Build the task onto the Gas instance
				$gas = array_shift($args);
				$gas->recorder->set($name, $args);

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
					return static::$db->$name();
				}
				else
				{
					// Always, sanitize arguments
					$args = Janitor::get_input($name, $args, TRUE);

					// Ensure once, in case there are some deprecated method
					if ( ! is_callable(array(static::$db, $name)))
					{
						throw new \BadMethodCallException('['.$name.']Unknown method.');
					}
			    		
					// Build the task onto the Gas instance
					$gas = array_shift($args);

					// Merge the table alongside with sent arguments
					$table    = $gas->validate_table()->table;
					$argument = array_unshift($args, $table);
					$gas->recorder->set($name, $args);

					return self::_execute($gas);
				}
			}
		}
		else
		{
			// Last try check relationships
			$gas = array_shift($args);

			if (FALSE != ($relationship = $gas->meta->get('entities.'.$name)))
			{
				// Gotcha!
				// Check for any pre-process options
				if ( ! empty($args))
				{
					$relationship['options'] = array_merge($args, $relationship['options']);
				}

				return self::generate_entity($gas, $relationship);
			}
			
			// Good bye
			throw new \BadMethodCallException('['.$name.']Unknown method.');
		}
	}
}