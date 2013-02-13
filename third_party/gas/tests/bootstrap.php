<?php 

/**
 *---------------------------------------------------------------
 * Load Gas ORM Bootstrap 
 *---------------------------------------------------------------
 */
 
// Get the front directory
$dir     = __DIR__;
$fragdir = explode(DIRECTORY_SEPARATOR, $dir);

// Catch Gas Directory
array_splice($fragdir, -1);
$gasdir  = implode(DIRECTORY_SEPARATOR, $fragdir);

// Define the ENVIRONMENT
define('ENVIRONMENT', 'testing');

// Define Gas ORM configuration for unit testing
$config = array('models_path'        => array('Model' => __DIR__.DIRECTORY_SEPARATOR.'dummyModels'),
                'cache_request'      => FALSE,
                'auto_create_models' => FALSE,
                'auto_create_tables' => FALSE);

// Include Gas ORM configuration and Bootstrap
include_once $gasdir.DIRECTORY_SEPARATOR.'bootstrap.php';

// Initialize all Model's setup
Model\Job\User::setUp();
Model\Role\User::setUp();
Model\Job::setUp();
Model\Kid::setUp();
Model\Role::setUp();
Model\User::setUp();
Model\Wife::setUp();

// Reconnect
Gas\Core::connect(DB_GROUP);