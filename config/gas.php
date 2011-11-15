<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| Gas Configuration
| -------------------------------------------------------------------
| This file contains an array of Gas ORM configuration
| used by the Gas ORM library.
|
*/
$config['models_path'] = array(APPPATH.'models', APPPATH.'modules', FCPATH.'modules');

$config['models_suffix'] = '_gas';

$config['autoload_models'] = TRUE;

$config['extensions'] = array('dummy');

$config['autoload_extensions'] = TRUE;