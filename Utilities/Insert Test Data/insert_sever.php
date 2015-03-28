<?php
require_once('config.php');
require_once('mongo_conn.php');
include_once('debug.php');

$nID = date('His');
$oClt = new MongoCollection($oCMDB2DB, 'server');
$oData = array(
	'deleted'				=> 0,
	'code'					=> $nID . '_Code',
	'asset_code'			=> $nID . '_AssetCode',
	'server_name'			=> $nID . '_ServerName',
	'site'					=> $nID . '_Site',
	'rack_id'				=> $nID . '_RackID',
	'chassis_id'			=> $nID . '_ChassisID',
	'rack'					=> $nID . '_Rack',
	'u'						=> $nID . '_U',
	'bay'					=> $nID . '_Bay',
	'chassis'				=> $nID . '_Chassis',
	'bucket'				=> $nID . '_Bucket',
	'server_type'			=> $nID . '_ServerType',
	'purpose_use'			=> $nID . '_PurposeUse',
	'product_id'			=> $nID . '_ProductID',
	'product_alias'			=> $nID . '_ProductAlias',
	'department_alias'		=> $nID . '_DepartmentAlias',
	'division_alias'		=> $nID . '_DivisionAlias',
	'server_model'			=> $nID . '_ServerModel',
	'cpu_config'			=> $nID . '_CPUConfig',
	'memory_size'			=> $nID . '_MemorySize',
	'ram_config'			=> $nID . '_RAMConfig',
	'hdd_size'				=> $nID . '_HDDSize',
	'ip_console'			=> $nID . '_IPConsole',
	'os'					=> $nID . '_OS',
	'software_list'			=> $nID . '_SoftwareList',
	'status'				=> $nID . '_Status',
	'note'					=> $nID . '_Note',
	'created_date'			=> $nID . '_CreatedDate',
	'last_updated'			=> $nID . '_LastUpdated',
	'vm_center'				=> $nID . '_VMCenter',
	'vm_key'				=> $nID . '_VMKey',
	'physical_ip'			=> $nID . '_PhysicalIP',
	'physical_SN'			=> $nID . '_PhysicalSN',
	'vmtool'				=> $nID . '_VMTool',
	'technical_group_id'	=> $nID . '_TechnicalGroupID',
	'technical_group_name'	=> $nID . '_TechnicalGroupName',
	'interface'				=> array(
								array(
									'ip'			=> $nID . '_IP1',
									'mac_address'	=> $nID . '_MAC1'
								),
								array(
									'ip'			=> $nID . '_IP2',
									'mac_address'	=> $nID . '_MAC2'
								),
								array(
									'ip'			=> $nID . '_IP3',
									'mac_address'	=> $nID . '_MAC3'
								),
								array(
									'ip'			=> $nID . '_IP4',
									'mac_address'	=> $nID . '_MAC4'
								),
							)
);
$oRs = $oClt->insert($oData);
p($oRs);
var_dump($oCMDB2DB->lastError());
include_once('close_mongo_conn.php');
?>