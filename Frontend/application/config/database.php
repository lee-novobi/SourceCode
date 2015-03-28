<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'root';
$db['default']['password'] = '';
$db['default']['database'] = '';
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

$db['monitoring_assistant']['hostname'] = '10.30.15.9';
$db['monitoring_assistant']['username'] = 'u_dev_20';
$db['monitoring_assistant']['password'] = 'UZBxdev@0';
$db['monitoring_assistant']['database'] = 'monitoring_assistant';
$db['monitoring_assistant']['dbdriver'] = 'mysqli';
$db['monitoring_assistant']['dbprefix'] = '';
$db['monitoring_assistant']['pconnect'] = FALSE;
$db['monitoring_assistant']['db_debug'] = TRUE;
$db['monitoring_assistant']['cache_on'] = FALSE;
$db['monitoring_assistant']['cachedir'] = '';
$db['monitoring_assistant']['char_set'] = 'utf8';
$db['monitoring_assistant']['dbcollat'] = 'utf8_general_ci';
$db['monitoring_assistant']['swap_pre'] = '';
$db['monitoring_assistant']['autoinit'] = TRUE;
$db['monitoring_assistant']['stricton'] = FALSE;


$db['mdr']['hostname'] = '10.30.15.8';
$db['mdr']['username'] = 'thanhuh';
$db['mdr']['password'] = 'pbfvgX1m';
$db['mdr']['database'] = 'mdr';
$db['mdr']['dbdriver'] = 'mysqli';
$db['mdr']['dbprefix'] = '';
$db['mdr']['pconnect'] = FALSE;
$db['mdr']['db_debug'] = TRUE;
$db['mdr']['cache_on'] = FALSE;
$db['mdr']['cachedir'] = '';
$db['mdr']['char_set'] = 'utf8';
$db['mdr']['dbcollat'] = 'utf8_general_ci';
$db['mdr']['swap_pre'] = '';
$db['mdr']['autoinit'] = TRUE;
$db['mdr']['stricton'] = FALSE;

$db['sdk_chat']['hostname'] = '10.30.15.8';
$db['sdk_chat']['username'] = 'root';
$db['sdk_chat']['password'] = 'P@ssWord123';
$db['sdk_chat']['database'] = 'monitoring_assistant';
$db['sdk_chat']['dbdriver'] = 'mysqli';
$db['sdk_chat']['dbprefix'] = '';
$db['sdk_chat']['pconnect'] = FALSE;
$db['sdk_chat']['db_debug'] = TRUE;
$db['sdk_chat']['cache_on'] = FALSE;
$db['sdk_chat']['cachedir'] = '';
$db['sdk_chat']['char_set'] = 'utf8';
$db['sdk_chat']['dbcollat'] = 'utf8_general_ci';
$db['sdk_chat']['swap_pre'] = '';
$db['sdk_chat']['autoinit'] = TRUE;
$db['sdk_chat']['stricton'] = FALSE;


/* End of file database.php */
/* Location: ./application/config/database.php */