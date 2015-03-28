<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('PAGER_SIZE', 10);

define('SECONDS_1_HOUR',  3600);
define('SECONDS_2_HOUR',  7200);
define('SECONDS_3_HOUR',  10800);
define('SECONDS_6_HOUR',  21600);
define('SECONDS_12_HOUR', 43200);
define('SECONDS_1_DAY',   86400);
define('SECONDS_2_DAY',   172800);
define('SECONDS_3_DAY',   259200);
define('SECONDS_1_WEEK',  604800);
define('SECONDS_2_WEEK',  1209600);
define('SECONDS_1_MONTH', 2592000);
define('SECONDS_3_MONTH', 7776000);

define('DEFAULT_HISTORY_LENGTH', SECONDS_1_WEEK);
define('DEFAULT_HISTORY_LENGTH_CHART', SECONDS_1_DAY);

define('COOKIE_SESSION_KEY',  'sdk_session_');
define('LOGON_SESSION_ERROR', 'session expired');

define('SEPARATOR', ';');

define('COLOR_RED', 	'#FF0000');
define('COLOR_GREEN', 	'#00FF00');
define('COLOR_BLUE', 	'#0000FF');

define('YES', TRUE);
define('NO',  FALSE);

define('UNLIMITED', 1000000000);
define('SECKEY', '226190d94b21d1b0c7b1a42d855e419d');
// ---------------------------------------------------------------------------------------------- //
// Define Collection name
// ---------------------------------------------------------------------------------------------- //
define('CLT_ROLES',                 'product_roles');
define('CLT_USER_GROUPS',           'user_groups');
define('CLT_PERMISSION_NAME',       'product_permissions_name');
define('CLT_PERMISSION_NAME_GROUPS','product_permissions_name_group');
define('CLT_ROLE_PERMISSIONS',      'product_roles_permissions');

define('CLT_PM_ROLES',              'pm_roles');
define('CLT_PM_PERMISSION_NAME',    'pm_permission_name');
define('CLT_PM_ROLE_PERMISSIONS',   'pm_role_permissions');
define('CLT_PM_USERS_ROLE',         'pm_users_role');
define('CLT_DEPARTMENT',            'department');
define('CLT_PRODUCT',               'product');
define('CLT_USERS',                 'users');
define('CLT_USER_GROUPS_PRODUCTS',  'user_groups_products');
define('CLT_USER_GROUPS_USERS',     'user_groups_users');
define('CLT_PRODUCT_OWNER',         'product_owner');
define('CLT_PRODUCT_MEMBER',        'product_member');

define('USERTYPE_SUPERADMIN', 1);
define('USERTYPE_NORMAL',     2);

define('RES_TYPE_PN_ATTR',       1);
define('RES_TYPE_PRODUCT_ROLE',  2);
define('RES_TYPE_PN_ASSIGNABLE', 3);

define('ADD_PRODUCT',     'add product');
define('DELETE_PRODUCT',  'delete product');
define('ADD_USER',        'add user');
define('DELETE_USER',     'delete user');

define('DEPARTMENT_KEY', 'alias');
define('PRODUCT_KEY',    'alias');
define('PRODUCT_DEPARTMENT_KEY', 'department_alias');
define('USER_DEPARTMENT_KEY', 'department_key');
?>