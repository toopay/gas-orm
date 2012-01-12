<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| Gas Configuration
| -------------------------------------------------------------------
| This file contains an array of Gas ORM configuration.
|
*/

/*
| -------------------------------------------------------------------
|  Models path
| -------------------------------------------------------------------
| Prototype:
|
|  key for namespace, value for path
|  eg, the default was :
|   
|  $config['models_path'] = array('Model' => APPPATH.'models');
|
|  Above mean, if within your script you requesting something like :
|
|  $user = Model\User::all();
|
|  Then Gas autoloader will find 'user.php' within APPPATH.'models'  
|
*/

$config['models_path'] = array('Model' => APPPATH.'models');


/*
| -------------------------------------------------------------------
|  Cache request
| -------------------------------------------------------------------
| Prototype:
|
|  $config['cache_request'] = TRUE;
|
*/

$config['cache_request'] = TRUE;

/*
| -------------------------------------------------------------------
|  Auto-create models and migration files
| -------------------------------------------------------------------
| Instruction:
|
| Before you enable this configuration option, make sure you already
| have some exists tables within your database.
| If you enable this option, Gas ORM will create a basic Gas model
| based by your database schema, and its sibling Migration files.
|
| Auto-create models are disabled by default for security reasons.
| You should enable this only whenever you intend to do a schema migration
| from database to your Gas model, and disable it back when you're done.
|
| NOTE : 
|
| 1. If you already have some Gas model files, back-up first.
| 2. You have to enable migration library.
| 3. To avoid unexpected results, backup any exists migration files.
|
| Prototype:
|
|  $config['auto_create_models'] = TRUE;
|
*/

$config['auto_create_models'] = FALSE;

/*
| -------------------------------------------------------------------
|  Auto-create tables and migration files
| -------------------------------------------------------------------
| Instruction:
|
| Before you enable this configuration option, make sure you already
| have some exists Gas model within your model directories.
| If you enable this option, Gas ORM will create sibling Migration files
| for based by each of your models and then run it via Migration mechanism. 
|
| Auto-create tables are disabled by default for security reasons.
| You should enable this only whenever you intend to do a schema migration
| from your Gas model to database, and disable it back when you're done.
|
| NOTE : 
|
| 1. You have to enable migration library.
| 2. To avoid unexpected results, backup any exists migration files.
| 3. Gas ORM will ignore this option, if your migration version not '0'.
|
| Prototype:
|
|  config['auto_create_tables'] = TRUE;
|
*/

$config['auto_create_tables'] = FALSE;
