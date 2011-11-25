<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| Gas Configuration
| -------------------------------------------------------------------
| This file contains an array of Gas ORM configuration
| used by the Gas ORM library.
|
*/

/*
| -------------------------------------------------------------------
|  Models path
| -------------------------------------------------------------------
| Prototype:
|
|  $config['models_path'] = array(APPPATH.'models', APPPATH.'modules', FCPATH.'modules');
|
*/

$config['models_path'] = array(APPPATH.'models', APPPATH.'modules', FCPATH.'modules');

/*
| -------------------------------------------------------------------
|  Models suffix
| -------------------------------------------------------------------
| Instruction:
|
| Each Gas model should have suffix, and its better to have suffix
| other   than '_model',   because ussually that suffix is used by
| CI native models. For example, if you have 'User' class then it
| should named as 'user_gas.php'.
|
| Prototype:
|
|  $config['models_suffix'] = '_gas';
|
*/

$config['models_suffix'] = '_gas';

/*
| -------------------------------------------------------------------
|  Auto-load models
| -------------------------------------------------------------------
| Prototype:
|
|  $config['autoload_models'] = TRUE;
|
*/

$config['autoload_models'] = TRUE;

/*
| -------------------------------------------------------------------
|  Extensions list
| -------------------------------------------------------------------
| Prototype:
|
|  $config['extensions'] = array('dummy', 'html', 'jquery');
|
*/

$config['extensions'] = array('dummy', 'html', 'jquery');

/*
| -------------------------------------------------------------------
|  Auto-load extensions
| -------------------------------------------------------------------
| Prototype:
|
|  $config['autoload_extensions'] = TRUE;
|
*/

$config['autoload_extensions'] = TRUE;

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
