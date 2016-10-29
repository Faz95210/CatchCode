<?php

//Database
define('CONFIG_DATABASE', 'database-config.ini');
$databaseConfig = parse_ini_file(CONFIG_DATABASE, true)["database_metadata"];



// Tables name
define('ACCOUNTS_TABLE', 'accounts');
define('USERS_TABLE', 'users');
define('CONTACTS_TABLE', 'contacts');
define('SOCIAL_NETWORKS_TABLE', '_social_networks');
