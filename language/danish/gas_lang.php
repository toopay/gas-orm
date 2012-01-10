<?php

$lang['db_connection_error']       = 'Kunne ikke forbinde til databasen med denne forbindelses gruppe eller dsn streng: %s';
$lang['models_not_found']          = 'Kunne ikke finde den speciferet model sti: %s';
$lang['models_found_no_relations'] = 'Model %s fundet, men relations attributterne er ikke sat.';
$lang['extensions_not_found']      = 'Kunne ikke den speciferet extension sti: %s';
$lang['empty_arguments']           = 'Kan ikke fortsætte med at eksekvere %s uden nogle parametre.';
$lang['cannot_create_model']       = 'Gas ORM kunne oprette en model(s) fil i stien: %s';
$lang['cannot_create_migration']   = 'Gas ORM kunne ikke oprette en migration(s) fil i stien: %s';
$lang['migration_no_setting']      = 'Gas ORM auto-migrate er stoppet, da der ingen ingestillinger er fundet.';
$lang['migration_no_dir']          = 'Gas ORM auto-migrate er stoppet, da der ingen gyldig sti er fundet.';
$lang['migration_no_initial']      = 'Gas ORM auto-migrate er stoppet, da migration versionen er over \'0\'.';
$lang['migration_disabled']        = 'Gas ORM auto-migrate er stoppet, da migration biblioteket er inaktivt.';
$lang['both_auto_error']           = 'Gas ORM kunne ikke køre både auto-created tables og models på samme tid,'
                                    .' har deaktiveret en af dem.';

$lang['auto_check']                = 'Feltet %s er ikke et gyldigt autoincrement felt.';
$lang['char_check']                = 'Feltet %s er ikke et gyldigt char felt.';
$lang['date_check']                = 'Feltet %s er ikke et gyldigt datetime felt.';
