<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Gas ORM Library
 *
 * A lighweight and easy-to-use ORM for CodeIgniter
 * 
 * This class intend to use as semi-native ORM library for CI, 
 * based on the ActiveRecord pattern. This library uses CI stan-
 * dard DB utility packages also validation class,
 *
 * @package     Gas Library
 * @category    Libraries
 * @version     1.0.1
 * @author      Taufan Aditya A.K.A Toopay
 * @license     GPL
 */

class Gas {
	
	public $table = '';
	public $relations = array();
	public $primary_key = 'id';
	
	protected $_CI;
	
	protected $_config;
	protected $_db;
	protected $_fields = array();
	protected $_set_fields = array();
	protected $_error_callbacks = array();
	protected $_has_result = FALSE;

	protected $_has_one = array();
	protected $_has_many = array();
	protected $_belongs_to = array();
	protected $_has_and_belongs_to = array();
	
	protected $_is_where = FALSE;
	protected $_is_where_in = FALSE;
	protected $_is_join = FALSE;
	protected $_is_with = array();
	
	private $_locked_table = FALSE;
	private $_locked_where = array();
	private $_locked_where_in = array();
	private $_locked_join = array();

	private $_models = array();
	private $_loaded_models = array();
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->_CI =& get_instance();
		
		$this->_CI->config->load('gas', TRUE, TRUE);
		$this->_config = $this->_CI->config->item('gas');
		
		$this->_scan_models();

		if($this->_config['autoload_models']) $this->_load_model('*');
		
		if( ! isset($this->_CI->db))
		{
			$this->_CI->load->database();
		}
		
		$this->_db = $this->_CI->db;
		
		$this->_init();

		log_message('debug', 'Gas ORM Class Initialized');
	}

	/**
	 * _init 
	 * 
	 * Initialize method
	 * 
	 */
	function _init() {}

	
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
	 * all
	 * 
	 * Get all records
	 *
	 * @access	public
	 * @return	array  array of result(s) object
	 */
	public function all()
	{
		$this->_validate_table();
		$q = $this->_db->get($this->table);
		
		$res = $this->_generate($q->result());
		
		return ( ! is_array($res)) ? array($res) : $res;
	}
	
	/**
	 * find
	 * 
	 * Get a record based by id or defined primary key
	 *
	 * @access	public
	 * @param   mixed
	 * @return	object 
	 */
	public function find()
	{
		if(func_num_args() == 1) 
		{
			$key_value = func_get_arg(0);
			return $this->find_where(array($this->primary_key => $key_value), 1);
		}

		$in = func_get_args();

		return $this->find_where_in(array($this->primary_key => $in));
	}
	
	/**
	 * find_where
	 * 
	 * Get record based by given arguments
	 *
	 * @access	public
	 * @param   array
	 * @param   int
	 * @param   int
	 * @param   bool
	 * @return	array  array of result(s) object
	 */
	public function find_where($args, $limit = null, $offset = null, $locked = TRUE)
	{
		$this->where($args);
		
		if(is_int($limit)) $this->_db->limit($limit, $offset);
		
		$this->_validate_table();
		$q = $this->_db->get($this->table);
		
		$res = $this->_generate($q->result(), $locked);
		
		return ( ! is_array($res) and $limit !== 1) ? array($res) : $res;
	}

	/**
	 * find_where_in
	 * 
	 * Get record based by given arguments
	 *
	 * @access	public
	 * @param   array
	 * @param   string
	 * @param   bool
	 * @return	array  array of result(s) object
	 */
	public function find_where_in($args, $type = '', $locked = TRUE, $eager_load = FALSE)
	{
		$this->where_in($type, $args);
		
		$this->_validate_table();
		$q = $this->_db->get($this->table);
			
		$res = $this->_generate($q->result(), $locked);

		if($eager_load)
		{
			$res = (is_array($res)) ? $res :  array($res);

			$res['eager_load_key'] = key($args);
			$res['eager_load_result'] = $q->result_array();
		}

		return ( ! is_array($res)) ? array($res) : $res;
	}

	/**
	 * first
	 * 
	 * Get first record
	 *
	 * @access	public
	 * @param   string
	 * @return	array  array of result(s) object
	 */
	public function first($column = null)
	{
		$this->_db->order_by((is_null($column)) ? $this->primary_key : $column, 'asc'); 
		$this->_db->limit(1);
		
		$this->_validate_table();
		$q = $this->_db->get($this->table);
		
		return $this->_generate($q->result(), TRUE);
	}

	/**
	 * last
	 * 
	 * Get last record
	 *
	 * @access	public
	 * @param   string
	 * @return	array  array of result(s) object
	 */
	public function last($column = null)
	{
		$this->_db->order_by((is_null($column)) ? $this->primary_key : $column, 'desc'); 
		$this->_db->limit(1);
		
		$this->_validate_table();
		$q = $this->_db->get($this->table);
		
		return $this->_generate($q->result(), TRUE);
	}

	
	/**
	 * save
	 * 
	 * Save or Update a table
	 *
	 * @access	public
	 * @return	mixed
	 */
	public function save($check = FALSE)
	{
		if($check == TRUE and $this->_validate_post() == FALSE) return FALSE;
		
		$this->_validate_table();

		if($this->_locked_table)
		{
			$this->_db->ar_where = $this->_locked_where;
			$this->_db->ar_join = $this->_locked_join;
			
			if(isset($this->_set_fields[$this->primary_key]) and ! empty($this->_set_fields[$this->primary_key]))
			{
				$this->_db->where($this->primary_key, $this->_set_fields[$this->primary_key]);
			}
			
			$this->_db->update($this->table, $this->_set_fields); 
		}
		elseif($this->_is_join)
		{
			list($identifier, $pivot_table, $parent_table_fields) = $this->_join_to_write($this->_locked_join, $this->table);

			$fields = array();
			foreach($this->_set_fields as $column => $val)
			{
				if(in_array($column, $parent_table_fields)) $fields[] = '`'.$this->table.'`.`'.$column.'` = \''.$val.'\'';
			}

			$fields = implode(', ', $fields);

			if($this->_is_where)
			{
				$where = implode("\n", $this->_locked_where);
				$where = 'WHERE ('.$where.' AND '.$identifier.')';
			}

			$update = 'UPDATE `'.$this->table.'`, `'.$pivot_table.'` SET '.$fields.' '.$where;
			
			$this->_db->query($update);
		}
		else 
		{
			$this->_db->insert($this->table, $this->_set_fields);
		}
		
		return $this->_db->affected_rows();
	}

	/**
	 * delete
	 * 
	 * Delete record(s)
	 *
	 * @access	public
	 * @return	mixed
	 */
	public function delete()
	{
		$this->_validate_table();

		if(func_num_args() > 0)
		{
				if(func_num_args() == 1)
				{
					$key_value = func_get_arg(0);
					$this->_db->where(array($this->primary_key => $key_value), 1);
				}
				else
				{
					$in = func_get_args();
					$this->_db->where_in($this->primary_key, $in);
				}
		}
		elseif($this->_locked_table)
		{
			$this->_db->ar_where = $this->_locked_where;
			$this->_db->ar_wherein = $this->_locked_where_in;
		}
		elseif(isset($this->_set_fields[$this->primary_key]))
		{
			$this->_db->where(array($this->primary_key => $this->_set_fields[$this->primary_key]));
		}
		else
		{
			return FALSE;
		}

		$this->_db->delete($this->table); 

		return $this->_db->affected_rows();
	}
	
	/**
	 * join
	 * 
	 * Join statement for joining table query
	 *
	 * @access	public
	 * @param   mixed
	 * @return	void
	 */
	public function join()
	{
		if(func_num_args() == 0) show_error('Using JOIN statement, without passing any parameter(s).');

		$this->_is_join = TRUE;
		
		if(func_num_args() == 1)
		{
			$join_clause = func_get_arg(0);
			$this->_db->join($join_clause, $join_clause.'.id = '.$this->table.'.id');
		}
		else
		{
			$join_args = func_get_args();
			call_user_func_array(array($this->_db, 'join'), $join_args);
		}
		$this->_locked_join = $this->_db->ar_join;

		return $this;
	}

	/**
	 * where
	 * 
	 * Where statement for conditional query
	 *
	 * @access	public
	 * @param   mixed
	 * @return	void
	 */
	public function where()
	{
		if(func_num_args() == 0) return show_error('Using WHERE statement, without passing any parameter(s).');

		$this->_is_where = TRUE;
		
		$where_args = func_get_args();
		call_user_func_array(array($this->_db, 'where'), $where_args);
		
		if($this->_is_join) array_walk_recursive($this->_db->ar_where, 'Gas::_set_join');

		$this->_locked_where = $this->_db->ar_where;

		return $this;
	}

	/**
	 * where_in
	 * 
	 * Where IN statement for conditional query
	 *
	 * @access	public
	 * @param   string
	 * @param   array
	 * @return	void
	 */
	public function where_in($type = '', $args = array())
	{
		$this->_is_where_in = TRUE;

		$where_field = key($args);
		$in = $args[$where_field];

		switch($type)
		{
			case 'or':
				$this->_db->or_where_in($where_field, $in);
				break;

			case 'not';
				$this->_db->where_not_in($where_field, $in);
				break;

			case 'or_not';
				$this->_db->or_where_not_in($where_field, $in);
				break;

			default:
				$this->_db->where_in($where_field, $in);
				break;
		}
		
		if($this->_is_join) array_walk_recursive($this->_db->ar_wherein, 'Gas::_set_join');

		$this->_locked_where_in = $this->_db->ar_wherein;

		return $this;
	}

	/**
	 * count
	 * 
	 * Get current rows/record(s)'s count
	 *
	 * @access	public
	 * @return	int
	 */
	public function count()
	{
		if($this->_is_where) $this->_db->ar_where = $this->_locked_where;
		if($this->_is_where_in) $this->_db->ar_wherein = $this->_locked_where_in;
		
		$this->_validate_table();

		$this->_db->from($this->table);
			
		return $this->_db->count_all_results();
	}

	/**
	 * last_id
	 * 
	 * Get last inserted id by save process
	 *
	 * @access	public
	 * @return	int
	 */
	public function last_id()
	{
		return $this->_db->insert_id();
	}

	/**
	 * to_array
	 * 
	 * Convert result to array
	 *
	 * @access	public
	 * @return	array
	 */
	public function to_array()
	{
		return (array) $this->_set_fields;
	}

	/**
	 * to_json
	 * 
	 * Convert result to json
	 *
	 * @access	public
	 * @return	array
	 */
	public function to_json()
	{
		return json_encode((array) $this->_set_fields);
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
	public function set_message($key, $msg, $field = null)
	{
		if(is_null($field)) show_error('Using set_message within callback function : '.$key.', without passing third parameter. Add $field as third parameter!');
		
		$this->_CI->lang->load('form_validation');
		
		if (FALSE === ($line = $this->_CI->lang->line($key)))
		{
			$line = $msg;
		}
		
		$this->_error_callbacks[] = str_replace('%s', $this->_label($field), $line);
		
		return $this;
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
		$prefix = ($prefix == '') ? '<p>' : $prefix;
		$suffix = ($suffix == '') ? '</p>' : $suffix;
		
		$errors = '';
		
		foreach ($this->_error_callbacks as $error)
		{
			$errors .= $prefix.$error.$suffix."\n";
		}
		
		return $errors.$this->_CI->form_validation->error_string($prefix, $suffix);
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
		if (empty($val) or is_integer($val)) return TRUE;
		
		$this->set_message('auto_check', 'The %s field was an invalid autoincrement field.', $field);
		
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
		
		$this->set_message('char_check', 'The %s field was an invalid char field.', $field);
		
		return FALSE;
	}

	/**
	 * load
	 *
	 * Load model(s) class
	 *
	 * @access	public
	 * @param   mixed
	 * @return	array
	 */
	public function load()
	{
		if(func_num_args() == 0) return show_error('Try to load model(s), without passing any parameter(s).');

		$models = func_get_args();
		$this->_load_model($models);
	}

	/**
	 * list_models
	 *
	 * Return all available models based by provided configuration
	 *
	 * @access	public
	 * @return	array
	 */
	public function list_models()
	{
		return $this->_models;
	}

	/**
	 * loaded_models
	 *
	 * Return all loaded models
	 *
	 * @access	public
	 * @return	array
	 */
	public function loaded_models()
	{
		return $this->_loaded_models;
	}
	
	/**
	 * _scan_model
	 * 
	 * Validate and sets model(s)'s directories
	 *
	 * @access	private
	 * @return	void
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

				$this->_models[str_replace($this->_config['models_suffix'].'.php', '', $model[count($model)-1])] = $file;
			}
		}
		
		return $this;
	}

	/**
	 * _load_model
	 * 
	 * Get model(s)'s classes
	 *
	 * @access	private
	 * @param   mixed
	 * @return	void
	 */
	private function _load_model($models = null)
	{
		if($models == '*')
		{
			foreach ($this->_models as $model => $model_path)
			{
				$this->_loaded_models[] = $model;
				require_once $model_path;
			}
		}
		elseif(is_array($models))
		{
			foreach ($models as $model)
			{
				if( ! array_key_exists($model, $this->_models)) show_error('Unable to locate the models name you have specifieds: '.$model);

				$this->_loaded_models[] = $model;
				require_once $this->_models[$model];
			}
		}
		elseif(is_string($models))
		{
			if( ! array_key_exists($models, $this->_models)) show_error('Unable to locate the models name you have specified: '.$models);

			$this->_loaded_models[] = $models;
			require_once $this->_models[$models];
		}
		
		return $this;
	}
	
	/**
	 * _validate_table
	 * 
	 * Validating whether current table is valid 
	 *
	 * @access	private
	 * @param   sring
	 * @return	void
	 */
	private function _validate_table($table = null)
	{
		$this->_has_result = FALSE;
		$table = (is_null($table)) ? $this->table : $table;
		
		if(empty($table))
		{
			$table = strtolower(get_class($this));
			$this->table = $table;
			
			if ( ! $this->_db->table_exists($table)) show_error('Unable to locate the table name you have specified: '.$table);
		}
		
		return $this;
	}
	
	/**
	 * _validate_post
	 * 
	 * Validating post data or data sets by save method
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _validate_post()
	{
		if($this->_CI->form_validation->run() == FALSE) return FALSE;
		
		foreach ($this->_fields as $k => $field)
		{
			if(strpos($field['rules'], 'callback'))
			{
				foreach (explode('call', $field['rules']) as $callback_rule)
				{
					if (substr($callback_rule, 0, 5) == 'back_')
					{
						$rule = substr($callback_rule, 5);
					
						if ( ! method_exists($this, $rule))	continue;
						
						if($this->$rule($k, $this->_set_fields[$k]) == FALSE) return FALSE;
					}
				}
			}
		}
		
		return TRUE;
	}
	
	/**
	 * _set_fields
	 * 
	 * Set the ORM object properties, alongside with its validation rule
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	private function _set_fields($fields = array())
	{
		if( ! isset($this->_CI->form_validation)) $this->_CI->load->library('form_validation');
		
		foreach ($fields as $field => $val)
		{
			if(isset($this->_fields[$field])) $this->_CI->form_validation->set_rules($field, $this->_label($field), $this->_fields[$field]['rules']);
				
			$this->_set_fields[$field] = $val;
		}
		
		return $this;
	}

	/**
	 * _set_join
	 * 
	 * Set the where arguments if join declared
	 *
	 * @access	private
	 * @param	string
	 * @param   string
	 * @return	void
	 */
	private function _set_join(&$v, $k)
	{
		$v = str_replace('`'.$this->primary_key.'`', '`'.$this->table.'`.`'.$this->primary_key.'`', $v);
	}

	/**
	 * _join_to_write
	 * 
	 * Return all neccesary stuff from write operation from JOIN portion
	 *
	 * @access	private
	 * @param	array
	 * @param   string
	 * @return	array
	 */
	private function _join_to_write($join_argument, $parent_table)
	{
		array_walk_recursive($join_argument, 'Gas::_set_join');
			
		$join = array_shift($join_argument);
		
		list($pivot_table, $identifier) = explode('ON', $join);

		$pivot_table = str_replace(array('JOIN', ' ', '`'), '', $pivot_table);
		$pivot_table_meta = $this->_db->field_data($pivot_table);

		$pivot_table_fields = array();
		foreach($pivot_table_meta as $meta) $pivot_table_fields[] = $meta->name; 
		
		if(preg_match('/('.implode('|', $pivot_table_fields).')/', $identifier, $m) and count($m) == 2)
		{
			$new_identifier = str_replace('`'.$m[1].'`', '`'.$pivot_table.'`.`'.$m[1].'`', $identifier);
			$join = str_replace($identifier, $new_identifier, $join);
		}

		$parent_table_meta = $this->_db->field_data($parent_table);

		$parent_table_fields = array();
		foreach($parent_table_meta as $meta) $parent_table_fields[] = $meta->name; 


		return array($new_identifier, $pivot_table, $parent_table_fields);
	}

	/**
	 * _set_with
	 * 
	 * Set the eager load properties if with declared
	 *
	 * @access	private
	 * @param	mixed
	 * @return	void
	 */
	private function _set_with($obj)
	{
		$load_with = array();
		$in = array();

		$identifier_key = $this->primary_key;
		
		foreach ($obj as $result)
		{
			if($result->$identifier_key)
			{
				$in[] = (is_numeric($result->$identifier_key)) ? (int) $result->$identifier_key : $result->$identifier_key;
			}
		}

		foreach ($this->_is_with as $attachment)
		{
			$load_with[$attachment] = $this->_generate_relation($attachment, TRUE, $in);
		}


		return $load_with;
	}

	/**
	 * _generate
	 * 
	 * Set/generate the ORM object 
	 *
	 * @access	private
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	private function _generate($results = array(), $locked_table = FALSE)
	{
		$eager_loaded_models = array();

		$this->_has_result = (bool) count($results);

		if(count($results) > 1)
		{
			$instances = array();

			$eager_loaded_models = $this->_set_with($results);

			$is_with = (bool) (count($eager_loaded_models) > 0);

			foreach ($results as $result)
			{

				$gas = get_class($this);
				$instance = new $gas;
				
				if($locked_table == TRUE)
				{
					$instance->_locked_table = TRUE;
					$instance->_is_where = $this->_is_where;
					$instance->_locked_where = $this->_locked_where;
					$instance->_is_where_in = $this->_is_where_in;
					$instance->_locked_where_in = $this->_locked_where_in;
					$instance->_is_join = $this->_is_join;
					$instance->_locked_join = $this->_locked_join;
				}
				
				$instance->_set_fields((array) $result);

				if($is_with)
				{
					foreach($eager_loaded_models as $foreign_model => $eager_model)
					{
						$is_one = isset($this->_has_one[$foreign_model]);
						$is_many = isset($this->_has_many[$foreign_model]);
						$is_many_to_many = isset($this->_has_and_belongs_to[$foreign_model]);
						
						$key = $eager_model['eager_load_key'];
						$res = $eager_model['eager_load_result'];

						unset($eager_model['eager_load_key']);
						unset($eager_model['eager_load_result']);

						$set_fields = array();

						foreach($eager_model as $index => $model)
						{
							if($is_many_to_many)
							{
								$pivot_key = (explode('.', $key));

								$key = $pivot_key[count($pivot_key)-1];
							}

							if($instance->_set_fields[$instance->primary_key] == $model->_set_fields[$key])
							{
								if($is_one)
								{
									$instance->_set_fields(array($foreign_model => $eager_model[$index]));
								}
								elseif($is_many or $is_many_to_many)
								{
									$set_fields[] = $eager_model[$index];
								}

								continue;
							}
							
							$belongs_to = FALSE;

							foreach($res as $eager_key => $fields)
							{
								if($instance->_set_fields[$instance->primary_key] == $fields[$key])
								{
									$belongs_to = TRUE;
									continue;
								}
							}

							if( ! $belongs_to)
							{
								$instance->_set_fields(array($foreign_model => new $foreign_model));
								continue;
							}
						}

						if($is_many or $is_many_to_many)
						{
							if(empty($set_fields)) $set_fields[] = new $foreign_model;
							$instance->_set_fields(array($foreign_model => $set_fields));
						}
					}
				}
				
				$instances[] = $instance;
			}
			
			return $instances;
		}
		
		if($locked_table == TRUE) $this->_locked_table = TRUE;
		
		if( ! empty($results))  $this->_set_fields((array) $results[0]);

		return $this;
	}

	/**
	 * _generate_relation
	 * 
	 * Set/generate the ORM relationnal table(s) object 
	 *
	 * @access	private
	 * @param	mixed
	 * @param	bool
	 * @param   array
	 * @return	void
	 */
	private function _generate_relation($relation_table, $eager_load = FALSE, $n = null)
	{
		if( ! empty($this->relations));
		{
			foreach ($this->relations as $relation_type => $relation)
			{
				switch($relation_type)
				{
					case 'has_one':
						$this->_has_one = array_merge($relation, $this->_has_one);
						break;

					case 'has_many':
						$this->_has_many = array_merge($relation, $this->_has_many);
						break;

					case 'belongs_to':
						$this->_belongs_to = array_merge($relation, $this->_belongs_to);
						break;

					case 'has_and_belongs_to':
						$this->_has_and_belongs_to = array_merge($relation, $this->_has_and_belongs_to);
						break;
				}
				
			}
		}

		if( ! class_exists ($relation_table)) $this->_load_model($relation_table);

		if(isset($this->_has_one[$relation_table]))
		{
			$has = new $relation_table;

			if($eager_load and count($n) > 0)
			{
				$has_one = $has->find_where_in(array($this->table.'_'.$this->primary_key => $n), '', FALSE, TRUE);
			}
			else
			{
				$has_one = $has->find_where(array(
						$this->table.'_'.$this->primary_key => $this->_set_fields[$this->primary_key],
		 		), 1);
			}
			
			return $has_one;
		}
		elseif(isset($this->_has_many[$relation_table]))
		{
			$has = new $relation_table;

			if($eager_load and count($n) > 0)
			{
				$has_many = $has->find_where_in(array($this->table.'_'.$this->primary_key => $n), '', FALSE, TRUE);
			}
			else
			{
				$has_many = $has->find_where(array(
						$this->table.'_'.$this->primary_key => $this->_set_fields[$this->primary_key],
		 		), null, null, FALSE);
			}
			
			return $has_many;
		}
		elseif(isset($this->_belongs_to[$relation_table]))
		{
			$belongs_to = new $relation_table;

			$foreign_key = $relation_table.'_'.$belongs_to->primary_key;

			if($eager_load and count($n) > 0)
			{
				$belongs = $belongs_to->find_where_in(array(str_replace($relation_table.'_', '', $foreign_key) => $n), '', FALSE, TRUE);
			}
			else
			{
				$belongs = $belongs_to->find_where(array(
						str_replace($relation_table.'_', '', $foreign_key) => $this->_set_fields[$foreign_key],
		 		), 1);
		 	}
			
			return $belongs;
		}
		elseif(isset($this->_has_and_belongs_to[$relation_table]))
		{
			
			$combination_table = array(
									$relation_table.'_'.$this->table,
									$this->table.'_'.$relation_table
								);
			
			foreach($combination_table as $guessed_table)
			{
				if($this->_db->table_exists($guessed_table))
				{
					$pivot_table = $guessed_table;
					continue;
				}
				
			}

			if( ! isset($pivot_table))  show_error('Unable to locate the pivot table between '.$this->table.' and '.$relation_table);

			$many_to_many = new $relation_table;

			$identifier_key = $many_to_many->primary_key;

			$many_to_many->join($pivot_table, $relation_table.'_'.$identifier_key.' = '.$identifier_key);

			if($eager_load and count($n) > 0)
			{
				$many = $many_to_many->find_where_in(array($pivot_table.'.'.$this->table.'_'.$this->primary_key => $n), '', FALSE, TRUE);
			}
			else
			{
				$many = $many_to_many->find_where(array(
		 				$pivot_table.'.'.$this->table.'_'.$this->primary_key => $this->_set_fields[$this->primary_key],
			 		 ), null, null, FALSE);
			}
			
			return $many;
		}
		
		return FALSE;
	}
			
	/**
	 * _label
	 * 
	 * Set/generate human named for fields
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	private function _label($field)
	{
		return str_replace(array('-', '_'), ' ', ucfirst($field));
	}
	
	/**
	 * __get
	 * 
	 * Overloading method utilized for reading data from inaccessible properties.
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
 	function __get($name) 
 	{
 		if(isset($this->$name)) return $this->$name;

 		if(isset($this->_models[$name]) and ! isset($this->_set_fields[$name]))  return $this->_generate_relation($name);
 		
 		switch ($name) 
 		{
 			case 'validation_rules':
 				return $this->_fields;
 				break;
 			
 			default:
 				return $this->_set_fields[$name];
 				break;
 		}
 	}
 	
	/**
	 * __set
	 * 
	 * Overloading method to writing data to inaccessible properties.
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	function __set($name, $args)
	{
		$this->_set_fields(array($name => $args));
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
		if (preg_match('/^find_by_([^)]+)$/', $name, $m) AND count($m) == 2)
		{
			$field = $m[1];
			$value = array_shift($args);
			$limit = array_shift($args);
			$offset = array_shift($args);
			
			return $this->find_where(array($field => $value), $limit, $offset);
		}
		elseif (preg_match('/^join_([^)]+)$/', $name, $m) AND count($m) == 2)
		{
			$joined_field = $m[1];
			$on = array_shift($args);
			
			return (is_string($on)) ? $this->join($joined_field, $on) : $this->join($joined_field);
		}
		elseif (preg_match('/^([^)]+)_join_([^)]+)$/', $name, $m) AND count($m) == 3)
		{
			$allowed_type = array('left', 'right', 'outer', 'inner', 'left outer', 'right outer');

			$join_type = str_replace('_', ' ', $m[1]);
			$joined_field = $m[2];
			$on = array_shift($args);
			$on = (is_string($on)) ? $on : $joined_field.'.id = '.$this->table.'.id';

			return (in_array($join_type, $allowed_type)) ? $this->join($joined_field, $on, $join_type) : $this->join($joined_field, $on);
		}
		elseif (preg_match('/^(min|max|avg|sum)$/', $name, $m) AND count($m) == 2)
		{
			if(empty($args)) $args = array($this->primary_key);
			
			call_user_func_array(array($this->_db, 'select_'.$m[1]), $args);
			
			$this->_validate_table();
			$q = $this->_db->get($this->table);
			
			return $this->_generate($q->result());
		}
		elseif ($name == 'with' and count($args) > 0)
		{
			$this->_is_with = $args;

			return $this;
		}
		elseif ($name == 'has_result')
		{
			return (bool) (count($this->_set_fields) > 0) ? TRUE : $this->_has_result;
		}
		elseif (method_exists($this->_db, $name))
		{
			if($name == 'last_query') return $this->_db->queries;

			$get = array('get', 'get_where', 'truncate');
			$where = array('where', 'or_where');
			$where_in = array('where_in', 'or_where_in', 'where_not_in', 'or_where_not_in');

			$is_get = FALSE;

			if(in_array($name, $get))
			{
				if($name !== 'truncate') $is_get = TRUE;
				
				$this->_validate_table();
				$this->_db->from($this->table);
			}
			
			if( ! $is_get) call_user_func_array(array($this->_db, $name), $args);

			if(in_array($name, $where))
			{
				$this->_locked_table = TRUE;
				$this->_is_where = TRUE;
				$this->_locked_where = $this->_db->ar_where;
			}
			elseif(in_array($name, $where_in))
			{
				$this->_locked_table = TRUE;
				$this->_is_where_in = TRUE;
				$this->_locked_where_in = $this->_db->ar_wherein;
			}

			if($is_get)
			{
				$q = $this->_db->get();
				return $this->_generate($q->result(), $this->_locked_table);
			}
			
			return $this;
		}
		
		return FALSE;
	}
}