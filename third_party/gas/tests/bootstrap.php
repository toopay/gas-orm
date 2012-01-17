<?php 
/**
 *---------------------------------------------------------------
 * Load the DB packages
 *---------------------------------------------------------------
 */
// Get the front directory
$dir     = __DIR__;
$fragdir = explode(DIRECTORY_SEPARATOR, $dir);
array_splice($fragdir, -1);
$gasdir  = implode(DIRECTORY_SEPARATOR, $fragdir);
array_splice($fragdir, -3);
$basedir = implode(DIRECTORY_SEPARATOR, $fragdir);

// Define the PATH and ENVIRONMENT
define('ENVIRONMENT', 'testing');
define('GASPATH', $gasdir.DIRECTORY_SEPARATOR);
define('APPPATH', $basedir.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR);
define('BASEPATH', $basedir.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR);

// Load everything on base gas directory and everything under dummyModels
require_once GASPATH.'classes'.DIRECTORY_SEPARATOR.'core.php';
require_once GASPATH.'classes'.DIRECTORY_SEPARATOR.'data.php';
require_once GASPATH.'classes'.DIRECTORY_SEPARATOR.'janitor.php';
require_once GASPATH.'classes'.DIRECTORY_SEPARATOR.'orm.php';
require_once GASPATH.'tests'.DIRECTORY_SEPARATOR.'dummyModels'.DIRECTORY_SEPARATOR.'user.php';
require_once GASPATH.'tests'.DIRECTORY_SEPARATOR.'dummyModels'.DIRECTORY_SEPARATOR.'wife.php';
require_once GASPATH.'tests'.DIRECTORY_SEPARATOR.'dummyModels'.DIRECTORY_SEPARATOR.'kid.php';
require_once GASPATH.'tests'.DIRECTORY_SEPARATOR.'dummyModels'.DIRECTORY_SEPARATOR.'job.php';
require_once GASPATH.'tests'.DIRECTORY_SEPARATOR.'dummyModels'.DIRECTORY_SEPARATOR.'role.php';
require_once GASPATH.'tests'.DIRECTORY_SEPARATOR.'dummyModels'.DIRECTORY_SEPARATOR.'job'.DIRECTORY_SEPARATOR.'user.php';
require_once GASPATH.'tests'.DIRECTORY_SEPARATOR.'dummyModels'.DIRECTORY_SEPARATOR.'role'.DIRECTORY_SEPARATOR.'user.php';
// Load needed DB files
require_once BASEPATH.'database'.DIRECTORY_SEPARATOR.'DB.php';
require_once BASEPATH.'database'.DIRECTORY_SEPARATOR.'DB_forge.php';
require_once BASEPATH.'database'.DIRECTORY_SEPARATOR.'DB_utility.php';

// Create a DB instance
$DB = &DB('default');
if ( ! $DB instanceof CI_DB_Driver)
{
	throw new InvalidArgumentException('db_connection_error:default');
}

// Define DB path
define('DBPATH', BASEPATH.'database'.DIRECTORY_SEPARATOR);
define('DBDRIVERSPATH', DBPATH.'drivers'.DIRECTORY_SEPARATOR);

// Load required utility files once
require_once(DBPATH.'DB_forge.php');
require_once(DBPATH.'DB_utility.php');
require_once(DBDRIVERSPATH.$DB->dbdriver.DIRECTORY_SEPARATOR.$DB->dbdriver.'_utility.php');
require_once(DBDRIVERSPATH.$DB->dbdriver.DIRECTORY_SEPARATOR.$DB->dbdriver.'_forge.php');

// Mock internal CI instance and low-level functions
class Tron {
	/**
	 * @var object Tron super-object
	 */
	private static $instance;

	/**
	 * Constructor
	 *
	 * @param   mixed   CI DB instance
	 * @return  void   
	 */
	function __construct($DB)
	{
		$this->db         = $DB;
		static::$instance = $this;
	}

	/**
	 * Get Tron super-object
	 *
	 * @return  object   
	 */
	public static function &get_instance()
	{
		return static::$instance;
	}

	/**
	 * Serve static call for TRON instantiation
	 *
	 * @return  object   
	 */
	public static function make()
	{
		return new static(NULL);
	}

	/**
	 * Serve loader
	 *
	 * @param   mixed
	 * @return  object   
	 */
	public static function load($args)
	{
		// TODO : resolve this loader to perform any necessarily
		// action, to load the real requested class.
		return static::$instance;
	}

	/**
	 * Serve other methods, to capture the error for the very least
	 *
	 * @param   string
	 * @param   mixed
	 * @throws  LogicException Serve show error method
	 * @return  mixed
	 */
	public function __call($name, $arguments)
	{
		// Only response for error
		if ($name == 'show_error')
		{
			// Get any meaning information
			$internal_error = (array_key_exists(2, $arguments)) ? $arguments[2] : 'Undefined CI Error';

			// Good bye
			throw new LogicException('CI Internal Error with message : '.$internal_error);
		}
	}
}

/**
 * global get_instance method
 */
function &get_instance() {

	$instance =& Tron::get_instance();

	return $instance;
}

/**
 * global log_message method
 */
function log_message(){}

/**
 * global load_class method
 */
function load_class(){
	// Capture argument
	$args = func_get_args();

	// Return new TRON to resolve any possible error
	return Tron::make()->load($args);
}

// MayDay!! Call TRON
$tron = new Tron($DB);

// Instantiate core class
Gas\Core::make($DB)->init();