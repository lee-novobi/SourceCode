<?php
define('MONGO_USER', 'u_cmdbv2');
define('MONGO_PASS', '@md4v2');
define('MONGO_DB', 'cmdbv2');
define('MONGO_HOST', '10.30.15.8');
define('MONGO_PORT', 27017);

define('CI_TYPE_SERVER', 1);

$arrCltIndexMap = array(
	1	=> 'inverted_index_server'
);
$arrIgnoreField = array(
	CI_TYPE_SERVER => array('deleted', '_id')
);
?>