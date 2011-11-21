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
|  $config['extensions'] = array('dummy');
|
*/

$config['extensions'] = array('dummy');

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
|  Auto-create models
| -------------------------------------------------------------------
| Prototype:
|
|  $config['auto_create_models'] = TRUE;
|
*/

$config['auto_create_models'] = TRUE;

/*
| -------------------------------------------------------------------
|  Auto-create tables
| -------------------------------------------------------------------
| Prototype:
|
|  config['auto_create_tables'] = TRUE;
|
*/

$config['auto_create_tables'] = TRUE;
