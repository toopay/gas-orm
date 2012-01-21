<?php
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file contain the settings needed to access the database,
| needed by unit testing to perform all assertions.
*/

$active_group = 'testing_mysql';
$active_record = TRUE;

$db['testing_mysql']['hostname'] = 'mysql:host=localhost';
$db['testing_mysql']['username'] = 'travis';
$db['testing_mysql']['password'] = '';
$db['testing_mysql']['database'] = 'gas_test';
$db['testing_mysql']['dbdriver'] = 'pdo';
$db['testing_mysql']['dbprefix'] = '';
$db['testing_mysql']['pconnect'] = TRUE;
$db['testing_mysql']['db_debug'] = TRUE;
$db['testing_mysql']['cache_on'] = FALSE;
$db['testing_mysql']['cachedir'] = '';
$db['testing_mysql']['char_set'] = 'utf8';
$db['testing_mysql']['dbcollat'] = 'utf8_general_ci';
$db['testing_mysql']['swap_pre'] = '';
$db['testing_mysql']['autoinit'] = TRUE;
$db['testing_mysql']['stricton'] = FALSE;
$db['testing_mysql']['failover'] = array();

$db['testing_postgre']['hostname'] = 'pgsql:host=localhost';
$db['testing_postgre']['username'] = 'postgres';
$db['testing_postgre']['password'] = '';
$db['testing_postgre']['database'] = 'gas_test';
$db['testing_postgre']['dbdriver'] = 'pdo';
$db['testing_postgre']['dbprefix'] = '';
$db['testing_postgre']['pconnect'] = TRUE;
$db['testing_postgre']['db_debug'] = TRUE;
$db['testing_postgre']['cache_on'] = FALSE;
$db['testing_postgre']['cachedir'] = '';
$db['testing_postgre']['char_set'] = 'utf8';
$db['testing_postgre']['dbcollat'] = 'utf8_general_ci';
$db['testing_postgre']['swap_pre'] = '';
$db['testing_postgre']['autoinit'] = TRUE;
$db['testing_postgre']['stricton'] = FALSE;

$db['testing_sqlite']['hostname'] = 'sqlite:'.GASPATH.'gas_test.sqlite';
$db['testing_sqlite']['username'] = '';
$db['testing_sqlite']['password'] = '';
$db['testing_sqlite']['database'] = '';
$db['testing_sqlite']['dbdriver'] = 'pdo';
$db['testing_sqlite']['dbprefix'] = '';
$db['testing_sqlite']['pconnect'] = FALSE;
$db['testing_sqlite']['db_debug'] = TRUE;
$db['testing_sqlite']['cache_on'] = FALSE;
$db['testing_sqlite']['cachedir'] = '';
$db['testing_sqlite']['char_set'] = 'utf8';
$db['testing_sqlite']['dbcollat'] = 'utf8_general_ci';
$db['testing_sqlite']['swap_pre'] = '';
$db['testing_sqlite']['autoinit'] = TRUE;
$db['testing_sqlite']['stricton'] = FALSE;