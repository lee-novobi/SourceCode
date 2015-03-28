<?php
require_once('config.php');
require_once('mongo_conn.php');
include_once('debug.php');

$nID = date('YmdHis');
$oClt = new MongoCollection($oCMDB2DB, 'custom_view');
$oData = array(
	'username'				=> 'quangtm3',
	'ci_type'				=> 1,
	'ci_name'				=> 'server',
	'deleted'				=> 0,
	'fields'				=> array(
								array(
									'field_id'		=> 'code',
									'field_name'	=> 'code',
									'display_name'	=> 'Code'
								),
								array(
									'field_id'		=> 'asset_code',
									'field_name'	=> 'asset_code',
									'display_name'	=> 'Asset Code'
								),
								array(
									'field_id'		=> 'server_name',
									'field_name'	=> 'server_name',
									'display_name'	=> 'Server Name'
								),
								array(
									'field_id'		=> 'site',
									'field_name'	=> 'site',
									'display_name'	=> 'Site'
								),
								array(
									'field_id'		=> 'server_type',
									'field_name'	=> 'server_type',
									'display_name'	=> 'Server Type'
								),
								array(
									'field_id'		=> 'product_alias',
									'field_name'	=> 'product_alias',
									'display_name'	=> 'Product Alias'
								),
								array(
									'field_id'		=> 'department_alias',
									'field_name'	=> 'department_alias',
									'display_name'	=> 'Department Alias'
								),
								array(
									'field_id'		=> 'status',
									'field_name'	=> 'status',
									'display_name'	=> 'Status'
								),
								array(
									'field_id'		=> 'created_date',
									'field_name'	=> 'created_date',
									'display_name'	=> 'Created_date'
								),
								array(
									'field_id'		=> 'last_updated',
									'field_name'	=> 'last_updated',
									'display_name'	=> 'Last Updated'
								),								
								array(
									'field_id'		=> 'interface',
									'field_name'	=> 'interface',
									'display_name'	=> 'Interface'
								),
							)
);
$oRs = $oClt->insert($oData);
p($oRs);
var_dump($oCMDB2DB->lastError());
include_once('close_mongo_conn.php');
?>