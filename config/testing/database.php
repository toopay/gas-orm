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

$db['testing_mysql']['hostname'] = 'localhost';
$db['testing_mysql']['username'] = 'travis';
$db['testing_mysql']['password'] = '';
$db['testing_mysql']['database'] = 'gas_test';
$db['testing_mysql']['dbdriver'] = 'mysql';
$db['testing_mysql']['dbprefix'] = '';
$db['testing_mysql']['pconnect'] = FALSE;
$db['testing_mysql']['db_debug'] = FALSE;
$db['testing_mysql']['cache_on'] = FALSE;
$db['testing_mysql']['cachedir'] = '';
$db['testing_mysql']['char_set'] = 'utf8';
$db['testing_mysql']['dbcollat'] = 'utf8_general_ci';
$db['testing_mysql']['swap_pre'] = '';
$db['testing_mysql']['autoinit'] = TRUE;
$db['testing_mysql']['stricton'] = FALSE;
$db['testing_mysql']['failover'] = array();

$db['testing_sqlite']['hostname'] = 'sqlite:/third_party/gas/gas_test.sqlite';
$db['testing_sqlite']['username'] = '';
$db['testing_sqlite']['password'] = '';
$db['testing_sqlite']['database'] = '';
$db['testing_sqlite']['dbdriver'] = 'pdo';
$db['testing_sqlite']['dbprefix'] = '';
$db['testing_sqlite']['pconnect'] = TRUE;
$db['testing_sqlite']['db_debug'] = FALSE;
$db['testing_sqlite']['cache_on'] = FALSE;
$db['testing_sqlite']['cachedir'] = '';
$db['testing_sqlite']['char_set'] = 'utf8';
$db['testing_sqlite']['dbcollat'] = 'utf8_general_ci';
$db['testing_sqlite']['swap_pre'] = '';
$db['testing_sqlite']['autoinit'] = TRUE;
$db['testing_sqlite']['stricton'] = FALSE;