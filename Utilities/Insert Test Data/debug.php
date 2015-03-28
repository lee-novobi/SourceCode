<?php
define("CLI", 'cli');
define("WEB", 'web');
define("ENV_MODE", CLI);

$strOpenPre  = ENV_MODE == WEB ? '<pre>' : '';
$strClosePre = ENV_MODE == WEB ? '</pre>' : "\n";

function pd($v){
	global $strOpenPre, $strClosePre;
	echo $strOpenPre;
	print_r($v);
	echo $strClosePre;
	die('debugging');
}
function p($v){
	global $strOpenPre, $strClosePre;
	echo $strOpenPre;
	print_r($v);
	echo $strClosePre;
}
function vd($v){
	global $strOpenPre, $strClosePre;
	echo $strOpenPre;
	var_dump($v);
	echo $strClosePre;
	die('debugging');
}
function d($v){
	$valid_debug_ip = array(
		'10.199.76.24',
		'127.0.0.1'
	);
	if(in_array($_SERVER['REMOTE_ADDR'],$valid_debug_ip)){
		error_log('--> ' . str_pad($_SERVER['REMOTE_ADDR'],15) . ' ' . date('Y-m-d H:i:s') . ' ' . print_r($v, true) . "\n", 3, DEBUG_LOG_PATH);
		//var_dump(debug_backtrace());
		//die();
	}
}
?>