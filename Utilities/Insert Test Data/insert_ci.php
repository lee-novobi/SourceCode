<?php
require_once('config.php');
require_once('mongo_conn.php');
include_once('debug.php');

$nID = date('YmdHis');
$oClt = new MongoCollection($oCMDB2DB, 'ci');
$oData = array(
	'ci_type'			=> 1,
	'ci_name'			=> 'server',
	'display_name'		=> 'Server',
	'description'		=> 'Server',
	'fields'			=> array(
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
									'field_id'		=> 'rack_id',
									'field_name'	=> 'rack_id',
									'display_name'	=> 'Rack ID'
								),
								array(
									'field_id'		=> 'chassis_id',
									'field_name'	=> 'chassis_id',
									'display_name'	=> 'Chassis ID'
								),
								array(
									'field_id'		=> 'rack',
									'field_name'	=> 'rack',
									'display_name'	=> 'Rack'
								),
								array(
									'field_id'		=> 'u',
									'field_name'	=> 'u',
									'display_name'	=> 'U'
								),
								array(
									'field_id'		=> 'bay',
									'field_name'	=> 'bay',
									'display_name'	=> 'Bay'
								),
								array(
									'field_id'		=> 'chassis',
									'field_name'	=> 'chassis',
									'display_name'	=> 'Chassis'
								),
								array(
									'field_id'		=> 'bucket',
									'field_name'	=> 'bucket',
									'display_name'	=> 'Bucket'
								),
								array(
									'field_id'		=> 'server_type',
									'field_name'	=> 'server_type',
									'display_name'	=> 'Server Type'
								),
								array(
									'field_id'		=> 'purpose_use',
									'field_name'	=> 'purpose_use',
									'display_name'	=> 'Purpose Use'
								),
								array(
									'field_id'		=> 'product_id',
									'field_name'	=> 'product_id',
									'display_name'	=> 'Product ID'
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
									'field_id'		=> 'division_alias',
									'field_name'	=> 'division_alias',
									'display_name'	=> 'Division Alias'
								),
								array(
									'field_id'		=> 'server_model',
									'field_name'	=> 'server_model',
									'display_name'	=> 'Server Model'
								),
								array(
									'field_id'		=> 'cpu_config',
									'field_name'	=> 'cpu_config',
									'display_name'	=> 'CPU Config'
								),
								array(
									'field_id'		=> 'memory_size',
									'field_name'	=> 'memory_size',
									'display_name'	=> 'Memory Size'
								),
								array(
									'field_id'		=> 'ram_config',
									'field_name'	=> 'ram_config',
									'display_name'	=> 'RAM Config'
								),
								array(
									'field_id'		=> 'hdd_size',
									'field_name'	=> 'hdd_size',
									'display_name'	=> 'HDD Size'
								),
								array(
									'field_id'		=> 'ip_console',
									'field_name'	=> 'ip_console',
									'display_name'	=> 'IP Console'
								),
								array(
									'field_id'		=> 'os',
									'field_name'	=> 'os',
									'display_name'	=> 'OS'
								),
								array(
									'field_id'		=> 'software_list',
									'field_name'	=> 'software_list',
									'display_name'	=> 'Software List'
								),
								array(
									'field_id'		=> 'status',
									'field_name'	=> 'status',
									'display_name'	=> 'Status'
								),
								array(
									'field_id'		=> 'note',
									'field_name'	=> 'note',
									'display_name'	=> 'Note'
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
									'field_id'		=> 'vm_center',
									'field_name'	=> 'vm_center',
									'display_name'	=> 'VM Center'
								),
								array(
									'field_id'		=> 'vm_key',
									'field_name'	=> 'vm_key',
									'display_name'	=> 'VM Key'
								),
								array(
									'field_id'		=> 'physical_ip',
									'field_name'	=> 'physical_ip',
									'display_name'	=> 'Physical IP'
								),
								array(
									'field_id'		=> 'physical_SN',
									'field_name'	=> 'physical_SN',
									'display_name'	=> 'Physical SN'
								),
								array(
									'field_id'		=> 'vmtool',
									'field_name'	=> 'vmtool',
									'display_name'	=> 'VM Tool'
								),
								array(
									'field_id'		=> 'technical_group_id',
									'field_name'	=> 'technical_group_id',
									'display_name'	=> 'Technical Group ID'
								),
								array(
									'field_id'		=> 'technical_group_name',
									'field_name'	=> 'technical_group_name',
									'display_name'	=> 'Technical Group Name'
								),
								array(
									'field_id'		=> 'interface',
									'field_name'	=> 'interface',
									'display_name'	=> 'Interface'
								)
							)
);
$oRs = $oClt->insert($oData);
p($oRs);
var_dump($oCMDB2DB->lastError());
include_once('close_mongo_conn.php');
?>