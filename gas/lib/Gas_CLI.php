<?php defined('CI_APPPATH') or die('No access allowed except via Gas CLI');

/**
 * CodeIgniter Gas ORM Console package
 *
 * CLI package for Gas ORM.
 *
 * @package     Gas Library
 * @author      Taufan Aditya A.K.A Toopay
 * @link        http://gasorm-doc.taufanaditya.com/
 * @license     BSD(http://gasorm-doc.taufanaditya.com/what_is_gas_orm.html#bsd)
 */

 /* ------------------------------------------------------------------------------------------------- */
 /* ------------------------------------------------------------------------------------------------- */

/**
 * Gas CLI Class.
 *
 * @package     Gas Library
 * @subpackage	Gas Console
 */

class Gas_CLI {

	public static $DNS = '';

	public static $DB = null;

	public static $DB_RES = array();

	public static $DB_supported = array('cubrid', 'mssql', 'mysql', 'mysqli', 'oci8', 'odbc', 'pdo', 'postgre', 'sqlite', 'sqlsrv');

	public static $VERSION;

	public static $flash_message;

	public static $gas_bureau;

	public static $vars = array();

	public static $childs;

	/**
	 * register_console
	 * 
	 * Register all needed files
	 * 
	 * @access public
	 * @return void
	 */
	final public function register_console()
	{
		$GAS_CLI_FILES = unserialize(GAS_CLI_FILES);

		foreach ($GAS_CLI_FILES as $file)
		{
			if ( ! is_file($file)) die('Gas CLI cannot load mandatory file: '.$file);

			require_once($file);
		}

		$db_file = CI_SYSPATH.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'DB.php';

		$gas_file = CI_APPPATH.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'Gas.php';

		if ( ! is_file($gas_file) and ! is_file($db_file)) die('Gas CLI cannot load mandatory files');

		require_once($db_file);

		require_once($gas_file);

		$gas = new Gas;

		self::$gas_bureau =& Gas_core::recruit_bureau();

		self::$VERSION = Gas_core::version();

		return;
	}

	/**
	 * load
	 * 
	 * Override load, to avoid trouble when something goes really wrong
	 * 
	 * @access public
	 * @return void
	 */
	public static function load()
	{
		show_error('DB Driver cannot works with those queries');
	}

	/**
	 * connect
	 * 
	 * Connect to a database, for query usage
	 * 
	 * @access public
	 * @param  string
	 * @return void
	 */
	public static function connect($str_dns)
	{
		try 
		{
			if (strpos($str_dns, ':') === FALSE)
			{
				Gas_CLI::$DB =& DB($str_dns, TRUE);
			}
			else
			{
				if (is_string($str_dns) and ($dns = @parse_url($str_dns)) !== FALSE)
				{
					$params = array(

						'dbdriver' => (isset($dns['host'])) ? $dns['scheme'] : '',

						'hostname' => (isset($dns['host'])) ? rawurldecode($dns['host']) : '',

						'username' => (isset($dns['user'])) ? rawurldecode($dns['user']) : '',

						'password' => (isset($dns['pass'])) ? rawurldecode($dns['pass']) : '',

						'database' => (isset($dns['path'])) ? rawurldecode(substr($dns['path'], 1)) : ''
						
					);

					if ( ! in_array($params['dbdriver'], Gas_CLI::$DB_supported))
					{
						show_error('Database '.$params['dbdriver'].' not supported.');

						return FALSE;
					}

					foreach($params as $name => $param)
					{
						if (empty($param)) 
						{
							show_error('Database parameter ,'.$name.', can not be empty.');

							return FALSE;
						}
					}

					Gas_CLI::$DB =& DB($str_dns, TRUE);
				}
				else
				{
					show_error('Unknown database connection parameter.');
				}
			}
		} 
		catch (Exception $e) 
		{
    		show_error($e->getMessage());
		}
		
		if (is_object(Gas_CLI::$DB))
		{
			if ( ! Gas_CLI::$DB->conn_id)
			{
				Gas_CLI::$DB = null;

				return FALSE;
			}

			if (is_object(Gas_CLI::$gas_bureau)) Gas_CLI::$gas_bureau->get_cli_engine(Gas_CLI::$DB);

			Gas_CLI::$DNS = $str_dns;

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * load_config
	 * 
	 * Manually load Gas configuration
	 * 
	 * @access public
	 * @return array
	 */
	public static function load_config()
	{
		require_once(APPPATH.'config'.DIRECTORY_SEPARATOR.'gas.php');

		return $config;
	}

	/**
	 * reflection_engine
	 * 
	 * Create a Database instance for Gas_bureau
	 * 
	 * @access public
	 * @return void
	 */
	public static function reflection_engine()
	{
		if ( ! is_null(Gas_CLI::$DB)) return Gas_CLI::$DB;

		if (empty(Gas_CLI::$DNS))
		{
			Gas_CLI::$DNS = 'default';
		}

		Gas_CLI::connect(Gas_CLI::$DNS);

		return;
	}

	/**
	 * query
	 * 
	 * Run a SQL query
	 * 
	 * @access public
	 * @param  string
	 * @return mixed
	 */
	public static function query($sql)
	{
		if (preg_match('/^select([^)]+)from([^)]+)$/', strtolower($sql), $m) and count($m) == 3)
		{
			$res = self::$DB->query($sql);

			if (is_object($res)) return $res->result_array();

			return array();
		}

		return FALSE;
	}

	/**
	 * flash
	 * 
	 * Saving a flash message
	 * 
	 * @access public
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public static function flash($type, $message)
	{
		self::$flash_message = array($type => $message);
	}

	/**
	 * check_flash
	 * 
	 * Display any flash message
	 * 
	 * @access public
	 * @return void
	 */
	public static function check_flash()
	{
		if ( ! empty(self::$flash_message))
		{
			if (count(self::$flash_message) == 1)
			{
				log_message(key(self::$flash_message), array_shift(self::$flash_message));
			}
			else
			{
				foreach (self::$flash_message as $messages)
				{
					log_message(is_array($messages) ? key($messages) : 'INFO', is_array($messages) ? array_shift($messages) : $messages);
				}
			}

			self::$flash_message = null;
		}
	}

	/**
	 * record_set
	 * 
	 * Response when a variable succesfully recorded
	 * 
	 * @access public
	 * @param  string
	 * @param  bool
	 * @return string
	 */
	public static function records_set($var, $exists = FALSE)
	{
		if ( ! $exists) self::$flash_message[] = array('INFO' => '`$'.$var.'` has been recorded.'."\n");

		self::$flash_message[] = array('INFO' => '`$'.$var.'->show` to show the record(s).'."\n");

		self::$flash_message[] = array('INFO' => '`$'.$var.'->child_name` to show child(s) instance record(s).'."\n");

		self::$flash_message[] = array('INFO' => '`$'.$var.'->destroy` to unset.'."\n\n");

		return 'var_used';
	}

	/**
	 * compile_command
	 * 
	 * Generating instance from some command
	 * 
	 * @access public
	 * @param  object
	 * @param  string  command
	 * @param  mixed   arguments
	 * @param  int     counter
	 * @param  mixed   commands collection
	 * @return mixed
	 */
	public static function compile_command($instance, $k, $v, $counter, $valid_command)
	{
		$result = null;

		if (is_object($instance) and is_callable(array($instance, $k), TRUE))
		{
			if (($k == 'find' and is_array($v)) or $k == 'all')
			{
				if (is_array($v))
				{
					$primary_key = $instance->primary_key;

					$result = $instance->find_where_in(array($primary_key, $v));
				}
				else
				{
					$result = empty($v) ? $instance->$k() : $instance->$k($v);
				}
			}
			elseif ($k == 'find')
			{
				$result = $instance->$k($v);
			}
			elseif (strpos($k, 'find_by') !== FALSE)
			{
				$result = $instance->$k($v);
			}
			else
			{
				if (strpos($k, '(array') !== FALSE)
				{
					$k = trim(str_replace('(array', '', $k));
				}

				if ($counter == count($valid_command))
				{
					if(empty($v))
					{
						$result = $instance->$k();
					}
					else
					{
						$result = $instance->$k($v);
					}
				}
				else
				{
					if (empty($v))
					{
						return $instance->$k();
					}
					else
					{
						return $instance->$k($v);
					}
				}
			}
		}

		return is_null($result) ? array() : array($result);
	}

	/**
	 * extract
	 * 
	 * Generate assoc array from some instance
	 * 
	 * @access public
	 * @param  mixed   instance
	 * @param  bool    whether to return or set the static property
	 * @return mixed
	 */
	public static function extract($result, $return = FALSE)
	{
		$resources = array();

		if (is_object($result))
		{
			$record = $result->to_array();

			$collumns = array_keys($record);

			$records[] = array_values($record);

			$resources = array('collumns' => $collumns, 'records' => $records);
		}
		elseif (is_array($result) and ! empty($result))
		{
			$raw_records = array();

			foreach ($result as $single_instance)
			{
				$record = $single_instance->to_array();

				if ( ! isset($raw_records['collumns']))
				{
					$raw_records['collumns'] = array_keys($record);
				}

				$row = array_values($record);

				$raw_records['records'][] = $row;
			}

			$resources = array('collumns' => $raw_records['collumns'], 'records' => $raw_records['records']);
		}
		else
		{
			return FALSE;
		}

		if ($return) return $resources;

		self::$DB_RES = $resources;

		return TRUE ;
	}

	/**
	 * extract_child
	 * 
	 * Generating child/relations instance from some parent instance
	 * 
	 * @access public
	 * @param  string  parent instance name
	 * @param  string  child instance name
	 * @param  mixed   resource
	 * @return string
	 */
	public static function extract_child($parent, $child, $resource)
	{
		self::$childs['parent_name'] = $parent;

		self::$childs['child_name'] = $child;

		self::$childs['childs'] = $resource;

		return 'method_has_child';
	}
	
	/**
	 * console
	 * 
	 * Handle input and translate it into proper response
	 * 
	 * @access public
	 * @param  mixed   arguments
	 * @param  string  title
	 * @return mixed
	 */
	public static function console($default = FALSE, $title = 'GAS-ORM') 
	{
		while (true) 
		{
			fwrite(STDOUT, sprintf('%s> ', $title));

			$line = self::input();

			if (is_string($line)) 
			{
				$vars = FALSE;

				$line = strtolower($line);

				if (preg_match('/([^\=]+)\((.*?)\)/', $line, $m) and count($m) == 3)
				{
					$exec = explode('=', $line, 2);

					$exec = Gas_janitor::arr_trim($exec);

					if (count($exec) == 2 and strpos($exec[0], '$') === 0)
					{
						$vars = str_replace('$', '', $exec[0]);

						self::$vars[$vars] = TRUE;

						$line = $exec[1];
					}
				}

				if (strpos($line, '$') !== FALSE)
				{
					$var_exec = explode('->', $line, 2);

					$var = trim(str_replace('$', '', $var_exec[0]));

					if (isset(self::$vars[$var]))
					{
						if (count($var_exec) == 1) return self::records_set($var, TRUE);
		
						if (strpos($var_exec[1], 'show') === 0)
						{
							$records = self::extract(self::$vars[$var]);
						
							if ($records)
							{
								return 'method_has_records';
							}
							else
							{
								return 'method_empty_records';
							}
						}
						elseif (strpos($var_exec[1], 'destroy') === 0)
						{
							unset(self::$vars[$var]);

							self::flash('INFO', '$'.$var.' has been destroyed.'."\n\n");

							return 'var_unused';
						}
						else
						{
							if (is_object(self::$vars[$var]))
							{
								$child = array();

								$identifier = self::$vars[$var]->primary_key;

								$model = self::$vars[$var]->model();

								list($table, $pk, $r) = Gas_janitor::identify_meta($model);

 								$peer_relation = Gas_janitor::identify_relations($r, $var_exec[1]);

 								if (empty($peer_relation))
 								{
 									log_message( "\n\t".'WARNING', 'Trying to fetch non-exists child node.');

									return 'syntax_error';
 								}
 								else
 								{
 									if (($child_node = self::$vars[$var]->$var_exec[1]) and empty($child_node))
									{
										$child[self::$vars[$var]->$identifier.':'.$peer_relation] = FALSE;
									}
									else
									{
										$child_resource = self::extract($child_node, TRUE);

										$child[self::$vars[$var]->$identifier.':'.$peer_relation] = $child_resource;
									}
 								}

								return self::extract_child($model, $var_exec[1], $child);
							}
							elseif(is_array(self::$vars[$var]) and ! empty(self::$vars[$var]))
							{
								$child = array();

								foreach(self::$vars[$var] as $var_instance)
								{
									isset($identifier) or $identifier = $var_instance->primary_key;

									isset($model) or $model = $var_instance->model();

									list($table, $pk, $r) = Gas_janitor::identify_meta($model);

	 								$peer_relation = Gas_janitor::identify_relations($r, $var_exec[1]);

	 								if (empty($peer_relation))
	 								{
	 									log_message( "\n\t".'WARNING', 'Trying to fetch non-exists child node.');

										return 'syntax_error';
									}
									else
									{
										if (($child_node = $var_instance->$var_exec[1]) and empty($child_node))
										{
											$child[$var_instance->$identifier.':'.$peer_relation] = FALSE;
										}
										else
										{
											$child_resource = self::extract($child_node, TRUE);

											$child[$var_instance->$identifier.':'.$peer_relation] = $child_resource;
										}
									}
								}

								return self::extract_child($model, $var_exec[1], $child);
							}
							else
							{
								log_message( "\n\t".'WARNING', 'Trying to fetch non-exists child node.');

								return 'syntax_error';
							}
						}
						
					}
					else
					{
						log_message( "\n\t".'WARNING', 'Trying to use undefined variable.');

						return 'syntax_error';
					}
				}
				elseif (strpos($line, 'gas::connect') !== FALSE)
				{

					$command = new Gas_command('connect', $line);

					if($command->argument)
					{
						$connect = self::connect($command->argument);

						return ($connect) ? 'connection_success' : 'connection_fail';
					}

					return 'syntax_error';
				}
				elseif (strpos($line, 'gas::query') !== FALSE)
				{
					$command = new Gas_command('query', $line);

					if($command->argument)
					{
						$results = self::query($command->argument);

						if ( ! empty($results))
						{
							$collumns = array();

							$records = array();

							foreach ($results as $result)
							{
								if (empty($collumns)) $collumns = array_keys($result);

								$records[] = array_values($result);
							}

							self::$DB_RES = array('collumns' => $collumns, 'records' => $records);

							return 'method_has_records';
						}
						elseif ($results === FALSE)
						{
							return 'method_not_allowed';
						}

						return 'method_empty_records';
					}

					return 'syntax_error';
				}
				elseif (strpos($line, 'gas::factory') !== FALSE)
				{
					$command = new Gas_command('factory', $line);

					if ($command->argument)
					{
						$valid_command = array();

						$near = '';

						foreach ($command->argument as $method)
						{
							$valid_method = new Gas_command('method', $method);

							if ($valid_method->argument)
							{
								$valid_command[] = $valid_method->argument;
							}
							else
							{
								$valid_command = FALSE;

								$near = $method;

								continue;
							}
						}

						if ($valid_command == FALSE)
						{
							log_message( "\n\t".'WARNING', 'syntax error near: '.$near);
						}
						else
						{
							$instance = null;

							$result = array();

							$counter = 0;

							foreach ($valid_command as $methods)
							{
								$counter++;

								foreach ($methods as $k => $v)
								{
									if ($k == 'factory')
									{
										if ($vars !== FALSE)
										{
											self::$vars[$vars] = Gas::factory($v);

											$instance = self::$vars[$vars];
										}
										else
										{
											$instance = Gas::factory($v);
										}
									}
									else
									{
										if (is_array($res = self::compile_command($instance, $k, $v, $counter, $valid_command)))
										{
											$result = array_shift($res);
											
										}
									}
								}
							}

							if ($vars !== FALSE)
							{
								self::$vars[$vars] = $result;

								return self::records_set($vars);
							}
							else
							{
								$records = self::extract($result);
						
								if ($records)
								{
									return 'method_has_records';
								}
								else
								{
									return 'method_empty_records';
								}
							}
						}
					}
					
					return 'syntax_error';
				}
				elseif (strpos($line, 'help') !== FALSE)
				{
					return 'help';
				}
				elseif (strpos($line, 'exit') !== FALSE or strpos($line, 'logout') !== FALSE)
				{
					return 'logout';
				}

				log_message( "\n\t".'WARNING', 'Unknown command.');

				return 'syntax_error';
			}
			elseif (isset($default)) 
			{
				log_message( "\n\t".'WARNING','Unknown command.');

				return $default;
			}
		}
	}

	/**
	 * welcome
	 * 
	 * Welcome message
	 * 
	 * @access public
	 * @return string
	 */
	public static function welcome()
	{
		self::line();

		self::line('	====================================');

		self::line('	       Welcome to Gas ORM CLI');

		self::line('	       Gas ORM version '.self::$VERSION);

		self::line(date('		d M Y H:i:s'));

		self::line('	====================================');

		self::line();

		self::line('Type (without backticks) `help` to get helped and `exit` to exiting.');

		self::line();
	}

	/**
	 * goodbye
	 * 
	 * Goodbye message
	 * 
	 * @access public
	 * @return string
	 */
	public static function goodbye()
	{
		self::line();

		self::line('	====================================');

		self::line('		      GOOD BYE');

		self::line('	====================================');

		self::line();
	}

	/**
	 * help
	 * 
	 * Help message
	 * 
	 * @access public
	 * @return string
	 */
	public static function help()
	{
		self::line();

		self::line('	====================================');

		self::line('		    Gas ORM CLI `help`');

		self::line('	====================================');

		self::line();

		self::line('Usage: '."\n\t".'[variable[ = ]]Gas::[command]([argument])[method][argument]'."\n");

		self::line('Available command are:'."\n");

		self::line("\t".'connect'."\t\t".'Use a database connection either by your database.php'."\n\t\t\t".'or dns string.'."\n");

		self::line("\t".'query'."\t\t".'Execute a SQL query.'."\n");

		self::line("\t".'factory'."\t\t".'Use a Gas model and retrieve a records from it '."\n\t\t\t".'with available Gas ORM method.'."\n");

		self::line('Example: '."\n"

					."\t".'Gas::connect(\'default\')'."\n"

					."\t".'Gas::query(\'SELECT * FROM table\')'."\n"

					."\t".'Gas::factory(\'foo\')->find(1)'."\n\n"

					."\t".'$users = Gas::factory(\'user\')->all()'."\n"

					."\t".'$users->show'."\n"

					."\t".'$users->kid'."\n"

					."\t".'$users->destroy'."\n"

					."\n");

		self::line('Type `help` (without backticks) to see this message again.'."\n");
	}

	/**
	 * line
	 * 
	 * Output a line message
	 * 
	 * @access public
	 * @param  string
	 * @param  bool
	 * @return string
	 */
	public static function line($msg = '', $error = FALSE) 
	{
		$args = array_merge(func_get_args(), array(''));
		
		$args[0] .= "\n";
		
		if ($error)
		{
			fwrite(STDERR, call_user_func_array(array('Gas_CLI', 'render'), $args));
		}
		else
		{
			call_user_func_array(array('Gas_CLI', 'out'), $args);
		}
	}

	/**
	 * input
	 * 
	 * Catch the input
	 * 
	 * @access public
	 * @param  mixed
	 * @return mixed
	 */
	public static function input($format = null) 
	{
		if ($format) 
		{
			fscanf(STDIN, $format . "\n", $line);
		}
		else 
		{
			$line = fgets(STDIN);
		}

		return ($line === FALSE) ? show_error('Caught ^C during input') : trim($line);
	}

	/**
	 * out
	 * 
	 * Output the message
	 * 
	 * @access public
	 * @param  string
	 * @return void
	 */
	public static function out($msg) 
	{
		$args = func_get_args();

		fwrite(STDOUT, call_user_func_array(array('Gas_CLI', 'render'), $args));
	}

	/**
	 * render
	 * 
	 * Render the message
	 * 
	 * @access public
	 * @param  string
	 * @return string
	 */
	public static function render($msg) 
	{
		$args = func_get_args();

		if (count($args) == 1) 
		{
			return $msg;
		}
		elseif ( ! is_array($args[1])) 
	    {
	        $args[0] = $args[0];
			
			return call_user_func_array('sprintf', $args);
		}
		else
		{
			foreach ($args[1] as $key => $value) 
			{
				$msg = str_replace('{:' . $key . '}', $value, $msg);
			}

			return $msg;
		}
	}
}
