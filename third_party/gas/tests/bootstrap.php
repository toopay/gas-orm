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
require_once GASPATH.'tests'.DIRECTORY_SEPARATOR.'dummyModels'.DIRECTORY_SEPARATOR.'job_user.php';
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
	 */
	function __construct($DB)
	{
		$this->db         = $DB;
		static::$instance = $this;
	}

	/**
	 * Get Tron super-object
	 */
	public static function &get_instance()
	{
		return static::$instance;
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
function load_class(){}

// MayDay!! Call TRON
$tron = new Tron($DB);

// Instantiate core class
Gas\Core::make($DB)->init();