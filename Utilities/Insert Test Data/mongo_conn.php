<?php
if(!defined('MONGO_USER')||!defined('MONGO_PASS')||!defined('MONGO_DB')||!defined('MONGO_HOST')){
	die("MongoDB Config not found !!!\n");
}
global $oCMDB2Conn, $oCMDB2DB;
$strConnString = sprintf('mongodb://%s', MONGO_HOST);
$oCMDB2Conn = new Mongo($strConnString, array(
	'username' => MONGO_USER,
    'password' => MONGO_PASS,
    'db'       => MONGO_DB
));
$oCMDB2DB   = $oCMDB2Conn->selectDB(MONGO_DB);
?>