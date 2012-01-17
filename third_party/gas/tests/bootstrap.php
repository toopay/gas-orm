<?php 

/**
 *---------------------------------------------------------------
 * Load Gas ORM Bootstrap 
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
define('APPPATH', $basedir.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR);
define('BASEPATH', $basedir.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR);

// Define Gas ORM configuration for unit testing
$config = array('models_path'        => array('Model' => __DIR__.DIRECTORY_SEPARATOR.'dummyModels'),
                'cache_request'      => TRUE,
                'auto_create_models' => FALSE,
                'auto_create_tables' => FALSE);

// Include Gas ORM configuration and Bootstrap
include_once $gasdir.DIRECTORY_SEPARATOR.'bootstrap.php';