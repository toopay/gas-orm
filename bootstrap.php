<?php 

/**
 *---------------------------------------------------------------
 * Travis Bootstrap 
 *---------------------------------------------------------------
 */

// Define the PATH and ENVIRONMENT
define('ENVIRONMENT', 'testing');
define('APPPATH',  __DIR__.DIRECTORY_SEPARATOR);
define('BASEPATH', 'vendor'.DIRECTORY_SEPARATOR.'CodeIgniter'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR);
define('SRCPATH',  APPPATH.'third_party'.DIRECTORY_SEPARATOR.'gas'.DIRECTORY_SEPARATOR);

// Define Gas ORM configuration for unit testing
$config = array('models_path'        => array('Model' => SRCPATH.'tests'.DIRECTORY_SEPARATOR.'dummyModels'),
                'cache_request'      => TRUE,
                'auto_create_models' => FALSE,
                'auto_create_tables' => FALSE);


// Include Gas ORM configuration and Bootstrap
include_once SRCPATH.'bootstrap.php';