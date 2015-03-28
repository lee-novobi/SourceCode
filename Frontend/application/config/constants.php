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

define('PAGER_SIZE', 15);

/* End of file constants.php */
/* Location: ./application/config/constants.php */
define('SEPARATOR', ';');

//define('TBL_PREFIX',            'z_');
define('TBL_PREFIX',            '');

define('CLT_CI',          	TBL_PREFIX . 'ci');
define('CLT_SERVER',        TBL_PREFIX . 'server');
define('CLT_PRODUCT',       TBL_PREFIX . 'product');
define('CLT_DEPARTMENT',    TBL_PREFIX . 'department');
define('CLT_DIVISION',      TBL_PREFIX . 'division');

define('CLT_TMP_PHYSICAL_SERVER',       TBL_PREFIX . 'tmp_physical_server');
define('CLT_TMP_VIRTUAL_SERVER',        TBL_PREFIX . 'tmp_virtual_server');
define('CLT_TMP_DEPARTMENT',            TBL_PREFIX . 'tmp_department');
define('CLT_TMP_DIVISION',              TBL_PREFIX . 'tmp_division');
define('CLT_TMP_PRODUCT',               TBL_PREFIX . 'tmp_product');

define('CLT_CUSTOM_VIEW',   TBL_PREFIX . 'custom_view');

define('CLT_INVERTED_INDEX_SERVER',     TBL_PREFIX . 'inverted_index_server');
define('CLT_INVERTED_INDEX_PRODUCT',    TBL_PREFIX . 'inverted_index_product');
define('CLT_INVERTED_INDEX_DEPARTMENT', TBL_PREFIX . 'inverted_index_department');
define('CLT_INVERTED_INDEX_DIVISION',   TBL_PREFIX . 'inverted_index_division');

define('SERVER_ICON_PATH',  'asset/images/icons/servers/');
define('SERVICE_ICON_PATH', 'asset/images/icons/services/');
define('DB_ICON_PATH',      'asset/images/icons/dbs/');

define('COLOR_RED', 	'#FF0000');
define('COLOR_GREEN', 	'#00FF00');
define('COLOR_BLUE', 	'#0000FF');

define('YES', 1);
define('NO',  0);
define('UNLIMITED', 1000000);

define('DATA_TYPE_INTEGER', 1);
define('DATA_TYPE_STRING',  2);
define('DATA_TYPE_FLOAT',   3);
define('DATA_TYPE_MONGOID', 4);

define('ACTION_TYPE_INSERT', 1);
define('ACTION_TYPE_UPDATE', 2);
define('ACTION_TYPE_DELETE', 3);

define('CI_SERVER', 1);
define('CI_PRODUCT', 2);
define('CI_DEPARTMENT', 3);
define('CI_DIVISION', 4);
define('FIELD_INTERFACE', 'interface');
define('CUSTOM_VIEW_COL_MARGIN', 10);

define('CUSTOM_VIEW_SERVER_COL',     5);
define('CUSTOM_VIEW_PRODUCT_COL',    1);
define('CUSTOM_VIEW_DEPARTMENT_COL', 1);
define('CUSTOM_VIEW_DIVISION_COL',   1);

define('DEFAULT_CI_KEY_SEARCH', 'server');
define('DEFAULT_CI_NAME_SEARCH', 'Server');
define('SDK_DEPARTMENT_NAME', 'sdk');

define('STR_UNKNOWN', 'Unknown');

define('VALUE_SERVER_STATUS_UNUSED',   0);
define('VALUE_SERVER_STATUS_IN_USED',  1);
define('VALUE_SERVER_STATUS_BORROW',   2);
define('VALUE_SERVER_STATUS_TRANSFER', 3);
define('VALUE_SERVER_STATUS_ERROR',    4);

define('VALUE_SERVER_POWER_STATUS_UNKNOWN', -1);
define('VALUE_SERVER_POWER_STATUS_OFF',     0);
define('VALUE_SERVER_POWER_STATUS_ON',      1);

define('VALUE_SERVER_TYPE_UNKNOWN', -1);
define('VALUE_SERVER_TYPE_VIRTUAL', 1);
define('VALUE_SERVER_TYPE_U',       2);
define('VALUE_SERVER_TYPE_CHASSIS', 3);

define('STR_SERVER_STATUS_UNUSED',   'Unused');
define('STR_SERVER_STATUS_IN_USED',  'In Used');
define('STR_SERVER_STATUS_BORROW',   'Borrow');
define('STR_SERVER_STATUS_TRANSFER', 'Transfer');
define('STR_SERVER_STATUS_ERROR',    'Error');

define('STR_SERVER_POWER_STATUS_ON',      'On');
define('STR_SERVER_POWER_STATUS_OFF',     'Off');
define('STR_SERVER_POWER_STATUS_UNKNOWN', STR_UNKNOWN);

define('STR_SERVER_TYPE_VIRTUAL', 'Virtual');
define('STR_SERVER_TYPE_U',       'Server U');
define('STR_SERVER_TYPE_CHASSIS', 'Server Chassis');
define('STR_SERVER_TYPE_UNKNOWN', STR_UNKNOWN);

define('STR_SERVER_GROUP_BASIC_INFO',         'Basic Information');
define('STR_SERVER_GROUP_OPERATION_INFO',     'Operation Information');
define('STR_SERVER_GROUP_LOCATION_INFO',      'Location Information');
define('STR_SERVER_GROUP_CONFIGURATION_INFO', 'Configuration Information');
define('STR_SERVER_GROUP_NETWORK_INFO',       'Network Information');
define('STR_SERVER_GROUP_SECURITY_INFO',      'Security Information');

define('VALUE_PRODUCT_STATUS_NEW',         0);
define('VALUE_PRODUCT_STATUS_IN_USED',     1);
define('VALUE_PRODUCT_STATUS_TRANSFERRING', 2);
define('VALUE_PRODUCT_STATUS_REMOVED',     3);
define('VALUE_PRODUCT_STATUS_CLOSED',      4);

define('STR_PRODUCT_STATUS_NEW',           'New');
define('STR_PRODUCT_STATUS_IN_USED',       'In Used');
define('STR_PRODUCT_STATUS_TRANSFERRING',  'Transferring');
define('STR_PRODUCT_STATUS_REMOVED',       'Remove');
define('STR_PRODUCT_STATUS_CLOSED',        'Close');

define('VALUE_DEPARTMENT_STATUS_INACTIVE', 0);
define('VALUE_DEPARTMENT_STATUS_ACTIVE',   1);

define('STR_DEPARTMENT_STATUS_INACTIVE',   'Inactive');
define('STR_DEPARTMENT_STATUS_ACTIVE',     'Active');

define('VALUE_DIVISION_STATUS_INACTIVE',   0);
define('VALUE_DIVISION_STATUS_ACTIVE',     1);

define('STR_DIVISION_STATUS_INACTIVE',     'Inactive');
define('STR_DIVISION_STATUS_ACTIVE',       'Active');

define('CI_DETAIL_DISPLAY_TYPE_CONTINUOUS', 1);
define('CI_DETAIL_DISPLAY_TYPE_TABS',       2);
define('CI_DETAIL_DISPLAY_TYPE_COLUMNS',    3);

define('TMP_SOURCE_DEFAULT', 0);
define('TMP_SOURCE_FRONT_END', 1);

define('FILTER_BY_DIVISION_ALIAS',          'division_alias');
define('FILTER_BY_DEPARTMENT_ALIAS',        'department_alias');
define('FILTER_BY_PRODUCT_ALIAS',           'product_alias');
define('FILTER_BY_SITE',                    'site');
?>