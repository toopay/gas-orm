<?php

$lang['db_connection_error']       = 'Cannot connect to database with these connection group or dsn string : %s';
$lang['models_not_found']          = 'Unable to locate the models path you have specified: %s';
$lang['models_found_no_relations'] = 'Model %s located, but missing relationship properties.';
$lang['extensions_not_found']      = 'Unable to locate the extensions path you have specified: %s';
$lang['empty_arguments']           = 'Cannot continue executing %s without any passed parameter.';
$lang['cannot_create_model']       = 'Gas ORM cannot create a model(s) file at: %s';
$lang['cannot_create_migration']   = 'Gas ORM cannot create a migration(s) file at: %s';
$lang['migration_no_setting']      = 'Gas ORM auto-migrate was stopped, because no settings are found.';
$lang['migration_no_dir']          = 'Gas ORM auto-migrate was stopped, because no valid directory is found.';
$lang['migration_no_initial']      = 'Gas ORM auto-migrate was stopped, because migration version is above \'0\'.';
$lang['migration_disabled']        = 'Gas ORM auto-migrate was stopped, because migration library is disabled.';
$lang['both_auto_error']           = 'Gas ORM cannot execute both auto-created tables and models at same time,'
                                    .' disabled one of them.';

$lang['auto_check']                = 'The %s field was an invalid autoincrement field.';
$lang['char_check']                = 'The %s field was an invalid char field.';
$lang['date_check']                = 'The %s field was an invalid datetime field.';