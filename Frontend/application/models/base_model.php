<?php
/*
 * Created on Feb 27, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class Base_model extends CI_Model
{
	var $rawMongoDBConnection          = NULL;
	var $rawMongoDBConnectionWrite     = NULL;
	var $rawMongoDBConnectionCapacityReport = NULL;
	var $rawMongoDBConnectionDependency= NULL;
	var $rawMongoDBConnectionAudit     = NULL;
	var $mongo_config_default          = NULL;
	var $mongo_config_write            = NULL;
	var $mongo_config_capacity_report  = NULL;
	var $mongo_config_dependency       = NULL;
	var $mongo_config_audit            = NULL;
	// Cac table co su dung co che master/backup
	// Name cua cac table nay duoc load len tu db luc runtime
 	var $tblAcknowledges               = '';
 	var $tblAlert                      = '';
 	var $tblApplications               = '';
 	var $tblGroups                     = '';
 	var $tblHosts                      = '';
 	var $tblHostsGroups                = '';
 	var $tblHostViewDetail             = '';
 	var $tblItems                      = '';
 	var $tblItemsApplications          = '';
	var $tblOverviewHostsStatusSummary = '';
 	var $tblOverviewServicesStatus     = '';
 	var $tblOverviewResUtil            = '';
 	var $tblOverviewHardwareStatus     = '';
 	var $tblOverviewHostsStatusDetail  = '';
 	var $tblOverviewTop10CPU           = '';
 	var $tblOverviewTop10Mem           = '';
 	var $tblOverviewTop10ServerLoad    = '';
 	var $tblOverviewTop10Disk          = '';
	var $tblProducts                   = '';
 	var $tblRights                     = '';
 	var $tblServerCategory             = '';
 	var $tblServiceCategory            = '';
 	var $tblWebServerCategory          = '';
 	var $tblAppServerCategory          = '';
 	var $tblOtherServiceCategory       = '';
 	var $tblTriggers                   = '';
 	var $tblUsers                      = '';
 	var $tblUsersGroups                = '';
 	var $tblUsrGrp                     = '';
	var $tblTopServiceReport 		   = '';
	var $tblResourceUsageReport        = '';

	// Cac table khong su dung co che master/backup
	// Name cua cac table nay duoc gan truc tiep trong constructor
 	var $tblActiveTable      = '';
	var $tblDepartments      = '';
 	var $tblEvents           = '';
 	var $tblHistoryInt       = '';
 	var $tblHistoryFloat     = '';
 	var $tblHistoryCPUUtil   = '';
 	var $tblHistoryServerLoad= '';
 	var $tblHistoryMemUsage  = '';
 	var $tblHistoryIOPrefix  = '';

 	var $tblCapacityReportCPUUtil       = '';
 	var $tblCapacityReportMEMUtil       = '';
 	var $tblCapacityReportServerLoad    = '';
 	var $tblCapacityReportDiskUsage     = '';
 	var $tblCapacityReportIORead        = '';
 	var $tblCapacityReportIOWrite       = '';
 	var $tblCapacityReportIOUtil        = '';

	var $tblRefCategory                 = '';
	var $tblRefSubcategoryItemKeys      = '';
	var $tblRefSubcategoryProcesses     = '';
 	var $tblSession                     = '';
	var $tblRefPartitionHistory         = '';
	var $tblUserProfile                 = '';
	var $tblHostNetStatService          = '';
	var $tblCapacitySummaryReport       = '';
	var $tblAuditSession                = '';
	var $tblAuditVictim                 = '';
	var $tblAuditResult                 = '';
	var $tblAuditRefServices            = '';
	var $tblApplicationDependency       = '';


 	var $eventStatusMap      = null;
 	var $hostStatusMap       = null;
 	var $hostAvailabilityMap = null;
 	var $eventAckMap         = null;

 	var $userRightGroup      = null;
 	var $userRightHost       = null;
 	var $userRightProduct    = null;
 	var $userRightService    = null;
 	var $userType            = ZABBIX_USER;
 	var $isFullRight         = false;
	// ------------------------------------------------------------------------------------------ //

 	function __construct($config = 'default')
 	{
		parent :: __construct();
		$params[] = $config;
		if(!class_exists('Mongo_db'))
		{
			$this->load->library('mongo_db', $params);
		}
		//$this->mongo_db = new Mongo_db($params);

		$this->mongo_config_default          = $this->config->item('default');
	}
	// ------------------------------------------------------------------------------------------ //
	function GetActiveTables()
	{
		$arr_activeTables = $this->mongo_db->get($this->tblActiveTable);
	}
	// ------------------------------------------------------------------------------------------ //
	protected function BuildHostPermissionCondition(&$arrGroupCondition, $arrHosts=NULL){
		$arrCondition = array('$or' => array());
		if ($arrHosts == NULL)
		{
			foreach($this->userRightHost as $serverId=>$arrHost)
			{
				$arrTmpCon = $arrGroupCondition;
				$arrTmpCon['zabbix_server_id'] = $serverId;
				$arrTmpCon['hostid'] = array('$in' => $arrHost);
				$arrCondition['$or'][] = $arrTmpCon;
			}
		}
		else
		{
			foreach($arrHosts as $serverId=>$arrHost)
			{
				$arrTmpCon = $arrGroupCondition;
				$arrTmpCon['zabbix_server_id'] = $serverId;
				$arrTmpCon['hostid'] = array('$in' => $arrHost);
				$arrCondition['$or'][] = $arrTmpCon;
			}
		}
		$arrGroupCondition = $arrCondition;
	}
	// ------------------------------------------------------------------------------------------ //
	protected function BuildHostKeyPermissionCondition(&$arrGroupCondition){
		$arrHostKey = array();
		foreach($this->userRightHost as $serverId=>$arrHost)
		{
			foreach($arrHost as $host){
				$arrHostKey[] = $host . ':' . $serverId;
			}
		}
		$arrGroupCondition['host_key'] = array('$in' => $arrHostKey);
	}
	// ------------------------------------------------------------------------------------------ //
	function ListHostByUserId_Old($userId)
	{
		$arrHosts = array();

		if($userId == $this->session->userdata('userId'))
			$arrGroups = $this->userRightGroup;
		else
			$arrGroups = $this->ListGroupByUserId($userId);

		$arrHostGroups = $this->mongo_db->select(array('hostid','groupid','zabbix_server_id'))
							->get($this->tblHostsGroups);

		$arrRawHostsInGroup = array();
		foreach($arrHostGroups as $hostGroup)
		{
			@$arrRawHostsInGroup[$hostGroup['zabbix_server_id'].':'.$hostGroup['groupid']][] = (int)$hostGroup['hostid'];
		}

		$arrTmpHosts = array();
		foreach($this->userRightGroup as $key=>$perm)
		{
			if(isset($arrRawHostsInGroup[$key]))
			{
				$arrKeyParts = explode(':', $key);
				if(isset($arrTmpHosts[$arrKeyParts[0]]))
					$arrTmpHosts[$arrKeyParts[0]] = array_merge($arrTmpHosts[$arrKeyParts[0]], $arrRawHostsInGroup[$key]);
				else
					$arrTmpHosts[$arrKeyParts[0]] = $arrRawHostsInGroup[$key];
			}
		}
		foreach($arrTmpHosts as $key=>$value)
		{
			$arrHosts[$key] = array_unique($value);
		}

		return $arrHosts;
	}
	// ------------------------------------------------------------------------------------------ //
	function ListHostByUserId($userId)
	{
		$arrHosts = array();
		$userId = (int)$userId;
		$arrUserProfile = $this->mongo_db->get_where($this->tblUserProfile, array('userid' => $userId));

		foreach($arrUserProfile as $value)
		{
			$arrHosts[$value['zabbix_server_id']] = array_unique($value['hosts']);
		}

		return $arrHosts;
	}
	// ------------------------------------------------------------------------------------------ //
	function GetHostList($strDepartment=NULL, $strProduct=NULL, $iServerType=NULL, $strKeyword=NULL, $iLimit=0, $iOffset=0)
	{
		$arrHosts = array();
		$arrCondition = array();
		$this->BuildHostPermissionCondition($arrCondition);
		if($this->CheckValidCondition($arrCondition)){
			$arrCondition['deleted'] = 0;
			if ($strProduct != NULL) {
				$strProduct = str_replace(array('(', ')'), array('[(]', '[)]'), $strProduct);
				$arrCondition['product_code'] = new MongoRegex('/^' . $strProduct . '$/i');
				//$this->mongo_db->like('product_code', $strProduct, 'i', FALSE, TRUE);
			}
			if ($strDepartment != NULL) {
				$arrCondition['department_code'] = $strDepartment;
				//$this->mongo_db->like('department_code', $strDepartment, 'i', FALSE, TRUE);
			};
			if ($iServerType != NULL) {
				$arrCondition['server_type'] = intval($iServerType);
			};
			if ($strKeyword != NULL) {
				$this->mongo_db->like('host', $strKeyword);
			}
			$this->mongo_db->order_by(array('host' => 'asc'));
			if ($iLimit > 0) {
				$this->mongo_db->limit($iLimit);
			}
			$this->mongo_db->offset($iOffset);
			$this->mongo_db->select(array('hostid', 'zabbix_server_id', 'host', 'product_code', 'department_code'));
			$arrHosts = $this->mongo_db->get_where($this->tblHosts,
										$arrCondition);
		}
		return $arrHosts;
	}
	// ------------------------------------------------------------------------------------------ //
	function CountHostList($strDepartment=NULL, $strProduct=NULL, $iServerType=NULL, $strKeyword=NULL)
	{
		$iCount = 0;
		$arrCondition = array();
		$this->BuildHostPermissionCondition($arrCondition);
		if($this->CheckValidCondition($arrCondition)){
			$arrCondition['deleted'] = 0;
			if ($strDepartment != NULL) {
				//$this->mongo_db->like('department_code', $strDepartment, 'i', FALSE, TRUE);
				$arrCondition['department_code'] = $strDepartment;
			};
			if ($strProduct != NULL) {
				//$this->mongo_db->like('product_code', $strProduct, 'i', FALSE, TRUE);
				$strProduct = str_replace(array('(', ')'), array('[(]', '[)]'), $strProduct);
				$arrCondition['product_code'] = new MongoRegex('/^' . $strProduct . '$/i');
			};
			if ($iServerType != NULL) {
				$arrCondition['server_type'] = intval($iServerType);
			};
			if ($strKeyword != NULL) {
				$this->mongo_db->like('host', $strKeyword);
			}

			$iCount = $this->mongo_db->where($arrCondition)->count($this->tblHosts);
		}
		return $iCount;
	}
	// ------------------------------------------------------------------------------------------ //
	function ListGroupByUserId($userId)
	{
		$arr_group = array();

		//if($this->userType == ZABBIX_SUPPER_ADMIN || $this->userType == ZABBIX_SDK_SUPPER_ADMIN)
		//{
		//	return $this->mongo_db->order_by(array('name' => 'asc'))->get($this->tblGroups);
		//}
		//else
		{
			$arrUsers = $this->mongo_db->get_where($this->tblUsers,
										array('userid' => $userId));

			foreach($arrUsers as $user)
			{
				foreach($user['locations'] as $location)
				{
					foreach($location['perms'] as $perms)
					{
						$key = $location['zabbix_server_id'] . ':' . $perms['groupid'];
						$arr_group[$key] = $perms['perms'];
					}
				}
			}
		}

		return $arr_group;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadHost($hostId, $zabbixId)
	{
		$hostId = intval($hostId);
		$zabbixId = intval($zabbixId);
		if(!empty($hostId))
		{
			$result = $this->mongo_db->limit(1)->get_where($this->tblHosts, array(
								'hostid' => $hostId,
								'zabbix_server_id' => (int)$zabbixId
						));
			if(count($result))
			{
				return $result[0];
			}
		}
		return NULL;
	}
	// ------------------------------------------------------------------------------------------ //
	function ListItem($hostId, $zabbixId)
	{
		$hostId   = intval($hostId);
		$zabbixId = intval($zabbixId);
		$arr_item = $this->mongo_db->order_by(array('name' => 'asc'))->get_where($this->tblItems,
						array('hostid' => $hostId, 'zabbix_server_id' => (int)$zabbixId));
		return $arr_item;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadUser($userId)
	{
		$userId = intval($userId);
		if(!empty($userId))
		{
			$result = $this->mongo_db->limit(1)->get_where($this->tblUsers,
						array('userid' => $userId));

			if(count($result))
			{
				return $result[0];
			}
		}
		return NULL;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadUserByUserName($userAlias)
	{
		if(!empty($userAlias))
		{
			$result = $this->mongo_db->limit(1)->get_where($this->tblUsers,
						array('username' => $userAlias));

			if(count($result))
			{
				return $result[0];
			}
		}
		return NULL;
	}
	// ------------------------------------------------------------------------------------------ //
	function ListApplication($hostId, $zabbixId)
	{
		$hostId   = intval($hostId);
		$zabbixId = intval($zabbixId);
		$arr_app  = $this->mongo_db->get_where($this->tblApplications,
						array(
							'hostid'           => $hostId,
							'zabbix_server_id' => (int)$zabbixId
						));
		return $arr_app;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadItemApplication($itemId, $zabbixId)
	{
		$itemId   = intval($itemId);
		$zabbixId = intval($zabbixId);
		if(!empty($itemId))
		{
			$result = $this->mongo_db->limit(1)->get_where($this->tblItemsApplications,
						array(
							'itemid'           => $itemId,
							'zabbix_server_id' => (int)$zabbixId)
						);

			if(count($result))
			{
				return $result[0];
			}
		}
		return NULL;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadGroup($groupId, $zabbixId)
	{
		$groupId = intval($groupId);
		$zabbixId = intval($zabbixId);
		if(!empty($groupId))
		{
			$result = $this->mongo_db->limit(1)->get_where($this->tblGroups,
						array(
							'groupid'          => $groupId,
							'zabbix_server_id' => (int)$zabbixId)
						);

			if(count($result))
			{
				return $result[0];
			}
		}
		return NULL;
	}
	// ------------------------------------------------------------------------------------------ //
	function ListHistoryInt($itemId, $zabbixId, $from, $to)
	{
		$result = array();
		if(!empty($itemId) && !empty($from) && !empty($to))
		{
			$cursor = $this->mongo_db->where(
							array(
								'itemid'           => (int)$itemId,
								'zabbix_server_id' => (int)$zabbixId
							))
						->where_between('clock', (int)$from, (int)$to)
						->order_by(array('clock' => 1))->select(array('clock','value'))
						->get($this->tblHistoryInt, true);
			while ($cursor->hasNext())
			{
				try
				{
					$row = $cursor->getNext();
					$result[] = array($row['clock']*1000, $row['value']+0);
				}
				catch (MongoCursorException $exception){}
			}
		}
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	function ListHistoryFloat($itemId, $zabbixId, $from, $to)
	{
		$result = array();
		if(!empty($itemId) && !empty($from) && !empty($to))
		{
			$cursor = $this->mongo_db->where(
							array(
								'itemid'           => (int)$itemId,
								'zabbix_server_id' => (int)$zabbixId
							))
			->where_between('clock', (int)$from, (int)$to)
			->order_by(array('clock' => 1))->select(array('clock','value'))
			->get($this->tblHistoryFloat, true);
			while ($cursor->hasNext())
			{
				try
				{
					$row = $cursor->getNext();
					$result[] = array($row['clock']*1000, $row['value']+0);
				}
				catch (MongoCursorException $exception){}
			}
		}

		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	function GetMaxHistoryIntValue($itemId, $zabbixId, $from, $to)
	{
		if(!empty($itemId) && !empty($from) && !empty($to))
		{
			$result = $this->mongo_db->where(
							array(
								'itemid'           => (int)$itemId,
								'zabbix_server_id' => (int)$zabbixId
							))
			->limit(1)
			->where_between('clock', (int)$from, (int)$to)
			->order_by(array('value' => -1))->select(array('value'))
			->get($this->tblHistoryInt);
			return $result[0]['value'];
		}
		return 0;
	}
	// ------------------------------------------------------------------------------------------ //
	function GetMaxHistoryFloatValue($itemId, $zabbixId, $from, $to)
	{
		if(!empty($itemId) && !empty($from) && !empty($to))
		{
			$result = $this->mongo_db->where(
							array(
								'itemid'           => (int)$itemId,
								'zabbix_server_id' => (int)$zabbixId
							))
			->limit(1)
			->where_between('clock', (int)$from, (int)$to)
			->order_by(array('value' => -1))->select(array('value'))
			->get($this->tblHistoryFloat);
			return $result[0]['value'];
		}
		return 0;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadItem($itemId, $zabbixId)
	{
		$result = array();
		if(!empty($itemId) && !empty($zabbixId))
		{
			$result = $this->mongo_db->where(
							array(
								'itemid'           => (int)$itemId,
								'zabbix_server_id' => (int)$zabbixId
							))
			->order_by(array('clock' => 1))
			->get($this->tblItems);
		}
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	function InsertAutoLoginSession($sessionId, $userId)
	{
		if(!$this->rawMongoDBConnectionWrite)
		{
			$this->ConnectRawConnectionWrite();
		}
		if($this->rawMongoDBConnectionWrite)
		{
			$db = $this->rawMongoDBConnectionWrite->selectDB($this->mongo_config_default['mongo_database']);
			$db->authenticate($this->mongo_config_default['mongo_username'], $this->mongo_config_default['mongo_password'] );
			$collection = new MongoCollection($db, $this->tblSession);
			$collection->insert(array(
				'sessionid'		=> $sessionId,
				'userid'		=> $userId,
				'created_date'  => date('Y-m-d H:i:s'),
				'ip'			=> $_SERVER['REMOTE_ADDR'],
				'agent'			=> $_SERVER['HTTP_USER_AGENT']
			));
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function LoadAutoLoginSession($sessionId){
		$session = $this->mongo_db->get_where($this->tblSession, array(
			'sessionid'	=> $sessionId
		));
		if(isset($session[0]['userid']))
		{
			$user = $this->model->LoadUser($session[0]['userid']);
			return $user;
		}

		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListEventHistory($triggerId, $zabbixId)
	{
		$result = array();
		if(!empty($triggerId) && !empty($zabbixId)){
			$raw = $this->mongo_db->order_by(array('clock'=> -1))->get_where($this->tblEvents,
				array(
					'triggerid'	       => (int)$triggerId,
					'zabbix_server_id' => (int)$zabbixId
				)
			);

			for($i=count($raw)-1;$i>1;$i--){
				$event1 = $raw[$i-1];
				$event0 = $raw[$i];

				$dateObj1   = date_create('@' . $event1['clock']);
				$dateObj0   = date_create('@' . $event0['clock']);
				$dateObjNow = date_create();

				$event0['human_clock']  = date('d M Y H:i:s', (int)$event0['clock']);
				@$event0['ack']         = isset($this->eventAckMap[$event0['acknowledged']]) ?
										$this->eventAckMap[$event0['acknowledged']] : 'Unknown';
				@$event0['status']      = isset($this->eventStatusMap[$event0['status']]) ?
										$this->eventStatusMap[$event0['status']] : 'Unknown';
				@$event0['durationObj'] = date_diff($dateObj1, $dateObj0);
				@$event0['durationObj'] = (array)$event0['durationObj'];
				@$event0['ageObj']      = date_diff($dateObjNow, $dateObj0);
				@$event0['ageObj']      = (array)$event0['ageObj'];
				$human_duration         = '';
				$human_age              = '';
				if(is_array($event0['durationObj']))
				{
					foreach($event0['durationObj'] as $key=>$value)
					{
						if($key!='invert' && $key!='days' && $value > 0)
						{
							if($key == 'm') $key = 'M';
							if($key == 'i') $key = 'm';

							$human_duration .= ($value . $key . ' ');
						}
					}
					$event0['duration'] = $human_duration=='' ? 0 : $human_duration;
				}

				if(is_array($event0['ageObj']))
				{
					foreach($event0['ageObj'] as $key=>$value)
					{
						if($key!='invert' && $key!='days' && $value > 0)
						{
							if($key == 'm') $key = 'M';
							if($key == 'i') $key = 'm';

							$human_age .= ($value . $key . ' ');
						}
					}
					$event0['age'] = $human_age=='' ? 0 : $human_age;
				}

				$result[] = $event0;
			}
		}

		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	protected function SetGroupCondition()
	{
		if(!$this->isFullRight)
		{
			foreach($this->userRightGroup as $right)
			{
				$this->mongo_db->or_where(
					array(
						'groupid'          => $right[0],
						'zabbix_server_id' => $right[1]
					)
				);
			}
		}
	}
	// ------------------------------------------------------------------------------------------ //
	protected function SetGroupCondition2()
	{
		if(!$this->isFullRight)
		{
			$arr_rightByZid = array();
			foreach($this->userRightGroup as $right)
			{
				$arr_rightByZid[$right[1]][] = $right[0];
			}
			foreach($arr_rightByZid as $zid=>$arr_group)
			{

			}
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function CheckUserRightForHost($hostId, $serverId)
	{

		if(!$this->isFullRight)
		{
			$hostId    = (int)$hostId;
	        $serverId  = (int)$serverId;

	        $hostInfo  = null;
	        $hostGroup = $this->mongo_db->get_where($this->tblHostsGroups,
	                        array('hostid' => $hostId, 'zabbix_server_id' => $serverId));
	        //p($hostGroup);
	        //pd($this->userRightGroup);
	        foreach($hostGroup as $hg)
	        {
	        	$key = $serverId . ':' . $hg['groupid'];
	        	if(isset($this->userRightGroup[$key]))
	        	{
	        		return true;
	        	}
	        }
			//pd('abc');
	        return false;
		}
		else
		{
			return true;
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function CheckPermission($raw)
	{
		$result = false;
		if(!$this->isFullRight)
		{
			if(isset($raw['zabbix_server_id'])&&isset($raw['groupid'])&&is_array($raw['groupid']))
			{
				foreach($raw['groupid'] as $groupid)
				{
					$key = $raw['zabbix_server_id'] . ':' . $groupid;
					if(isset($this->userRightGroup[$key]))
					{
						$result = true;
						break;
					}
				}
			}
		}
		else
		{
			$result = true;
		}

		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	protected function SetUserCondition()
	{
		//if(!$this->isFullRight)
		{
			$this->mongo_db->where(array('userid' => $this->session->userdata('userId')));
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListServices()
	{
		$result = array();
		$service_category_id = 0;
		$oRs = $this->mongo_db->select(array('categoryid'))
						->get_where($this->tblRefCategory, array('name' => 'Service'));
		if (count($oRs) > 0 && is_array($oRs)) {
			$service_category_id = $oRs[0]['categoryid'];
			if(isset($oRs['sub_category'])){
				foreach ($oRs['sub_category'] as $sub_category)
				{
					$result[] = $sub_category['name'];
				}
			}
			$raw = $this->mongo_db->select(array('sub_category.name'))
				->get_where($this->tblRefCategory, array('parentid' => $service_category_id));

			foreach($raw as $rs)
			{
				foreach($rs['sub_category'] as $cat)
				{
					$result[] = $cat['name'];
				}
			}
			return $result;
		}
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListServicesByName($name)
	{
		$result = array();
		$raw = $this->mongo_db->select(array('sub_category.name'))
				->get_where($this->tblRefCategory, array('name' => $name));

		foreach($raw as $rs)
		{
			foreach($rs['sub_category'] as $cat)
			{
				$result[] = $cat['name'];
			}
		}
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListDatabases()
	{
		$result = array();
		$raw = $this->mongo_db->select(array('sub_category.name'))
		->get_where($this->tblRefCategory, array('name' => 'Database'));

		foreach($raw as $rs)
		{
			foreach($rs['sub_category'] as $cat)
			{
				$result[] = $cat['name'];
			}
		}
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListProducts()
	{
		$raw = $this->mongo_db->select(array('productid', 'code'))->order_by(array('code'=> 1))
				->get($this->tblProducts);

		return $raw;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListProductsByUser($department = null)
	{
		$result = array();
		if(!$this->rawMongoDBConnection)
		{
			$this->ConnectRawConnection();
		}
		if($this->rawMongoDBConnection)
		{
			$db = $this->rawMongoDBConnection->selectDB($this->mongo_config_default['mongo_database']);
			$db->authenticate($this->mongo_config_default['mongo_username'], $this->mongo_config_default['mongo_password'] );
			$this->mongo_db->switch_db($this->mongo_config_default['mongo_database']);
			$queryCondition = array('userid' => (int)$this->session->userdata('userId'));
			if ($department !== null) {
				$queryCondition['department'] = strval($department);
			}
			$product = $db->command(array(
					'distinct'	=> $this->tblOverviewHostsStatusDetail,
					'key'		=> 'product',
					'query'		=> $queryCondition
			));

			$upper_result = array();
			foreach($product['values'] as $p)
			{
				$p = trim($p);
				$upper_p = strtoupper($p);

				if(!in_array($upper_p,$upper_result))
				{
				 	$result[] = $p;
					$upper_result[] = $upper_p;
				}
			}
			sort($result);
		}
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListDepartmentsByUser($product = null)
	{
		if(!$this->rawMongoDBConnection)
		{
			$this->ConnectRawConnection();
		}
		if($this->rawMongoDBConnection)
		{
			$db = $this->rawMongoDBConnection->selectDB($this->mongo_config_default['mongo_database']);
			$db->authenticate($this->mongo_config_default['mongo_username'], $this->mongo_config_default['mongo_password'] );
			$queryCondition = array('userid' => (int)$this->session->userdata('userId'));
			if ($product !== null) {
				$queryCondition['product'] = strval($product);
			}

			$product = $db->command(array(
					'distinct'	=> $this->tblOverviewHostsStatusDetail,
					'key'		=> 'department',
					'query'		=> $queryCondition
			));

			$arrTmpDept = $arrDepartment = array();

			foreach($product['values'] as $dept){
				$strDept = strtoupper($dept);
				if(!in_array($strDept, $arrTmpDept)){
					$arrTmpDept[] = $strDept;
					$arrDepartment[] = $dept;
				}
			}

			sort($arrDepartment);
			return $arrDepartment;
		}

		return array();
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListOSName()
	{
		$result = array();
		$raw = $this->mongo_db->select(array('os_type'), array('_id'))
				->order_by(array('os_type'=> 1))
				->get($this->tblHosts);
		foreach($raw as $rs)
		{
			if(isset($rs['os_type']) && $rs['os_type'] != '')
			{
				if(@!in_array($rs['os_type'], $result)) $result[] = @$rs['os_type'];
			}
		}
		$result[] = 'Unknown';
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListItemByItemKeyRegex($hostId, $serverId, $key)
	{
		$result = array();
		$raw = $this->mongo_db->select(
				array(
					'hostid','itemid','key_','lastvalue','normal_threshold','warning_threshold','critical_threshold'
				),
				array('_id')
			)
			->where(array(
					'hostid'			=> (int)$hostId,
					'zabbix_server_id'	=> (int)$serverId,
					'deleted' 			=> 0,
					'status'			=> 0
				)
			)
			->like('key_', $key)
			->get($this->tblItems);
		return $raw;
	}
	// ------------------------------------------------------------------------------------------ //
	protected function ConnectRawConnection()
	{
		try
		{
			$this->rawMongoDBConnection = $this->mongo_db->get_connection();
		} catch(Exception $e) {
			show_error('An error occurred when trying to connect to MongoDB (Raw)', 500);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	protected function ConnectRawConnectionWrite()
	{
		try
		{
			$this->rawMongoDBConnectionWrite = $this->mongo_db->get_connection();
		} catch(Exception $e) {
			show_error('An error occurred when trying to connect to MongoDB (RawWrite)', 500);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	protected function ConnectRawConnectionCapacityReport()
	{
		try
		{
			$this->mongo_db->load('capacity');
			$this->rawMongoDBConnectionCapacityReport = $this->mongo_db->get_connection();
		} catch(Exception $e) {
			show_error('An error occurred when trying to connect to MongoDB (RawCapacityReport)', 500);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	protected function ConnectRawConnectionDependency()
	{
		try
		{
			$this->mongo_db->load('dependency_map');
			$this->rawMongoDBConnectionDependency = $this->mongo_db->get_connection();
		} catch(Exception $e) {
			show_error('An error occurred when trying to connect to MongoDB (RawDependency)', 500);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	protected function ConnectRawConnectionAudit()
	{
		try
		{
			$this->mongo_db->load('audit');
			$this->rawMongoDBConnectionAudit = $this->mongo_db->get_connection();
		} catch(Exception $e) {
			show_error('An error occurred when trying to connect to MongoDB (RawAudit)', 500);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function LoadHostInfo($hostId, $serverId)
	{
		$hostId    = (int)$hostId;
		$serverId  = (int)$serverId;

		$hostInfo  = null;

		$hostInfo = $this->mongo_db->get_where($this->tblHosts,
				array('hostid' => (int)$hostId, 'zabbix_server_id' => (int)$serverId));

		if(!empty($hostInfo))
		{
			$hostInfo = $hostInfo[0];
			// ---------------------------------------------------------------------------------- //
			$itemKeyCondition = explode(SEPARATOR, ITEMKEY_AGENT_VERSION);
			$itemAgentVer     = $this->mongo_db->where(
					array(
							'hostid'            => $hostId,
							'zabbix_server_id'  => $serverId
					))->where_in('key_', $itemKeyCondition)->get($this->tblItems);
			@$hostInfo['agent_version'] = $itemAgentVer[0]['lastvalue'];
			// ---------------------------------------------------------------------------------- //
			if(@isset($hostInfo['last_updated_available']) && $hostInfo['last_updated_available'] != '')
			{
				// $hostInfo['duration'] = time() - $historyStatus[0]['clock'];
				// $hostInfo['duration'] = CalDuration($hostInfo['last_updated_available'], 0);
				$hostInfo['duration'] = secs_to_h($hostInfo['duration']);
			}
			// ---------------------------------------------------------------------------------- //
			@$productInfo = $this->mongo_db->where(
					array(
							'productid' => $hostInfo['productid']
					)
			)->get($this->tblProducts);
			@$hostInfo['product']    = $productInfo[0]['name'];
			// ---------------------------------------------------------------------------------- //
			@$departmentInfo = $this->mongo_db->where(
					array(
							'departmentid' => $productInfo[0]['departmentid']
					)
			)->get($this->tblDepartments);
			@$hostInfo['department'] = $departmentInfo[0]['name'];
			// ---------------------------------------------------------------------------------- //
			$interface = array('private' => array(), 'public' => array());
			foreach($hostInfo['interface'] as $if)
			{
				if ($if['type'] == IP_PRIVATE)
					$interface['private'][$if['name']][] = array(
							'ip'  => $if['ip'],
							'mac' => $if['mac_address'],
							'type'=> $if['type']
					);
				else
					$interface['public'][$if['name']][] = array(
							'ip'  => $if['ip'],
							'mac' => $if['mac_address'],
							'type'=> $if['type']
					);
			}
			$hostInfo['interface'] = $interface;
			// ---------------------------------------------------------------------------------- //
			$hostInfo['items'] = $this->ListItemOfHost($hostId, $serverId);
			// ---------------------------------------------------------------------------------- //
		}
		return $hostInfo;
	}
	// ------------------------------------------------------------------------------------------ //
	public function LoadHostNetworkInterfacesInfo($hostId, $serverId)
	{
		$hostId    = (int)$hostId;
		$serverId  = (int)$serverId;

		$hostInfo  = null;

		$hostInfo = $this->mongo_db->get_where($this->tblHosts,
				array('hostid' => (int)$hostId, 'zabbix_server_id' => (int)$serverId));

		if(!empty($hostInfo))
		{
			$hostInfo = $hostInfo[0];
			// ---------------------------------------------------------------------------------- //
			$interface = array('private' => array(), 'public' => array());
			foreach($hostInfo['interface'] as $if)
			{
				if ($if['type'] == IP_PRIVATE)
					$interface['private'][$if['name']][] = array(
							'ip'  => $if['ip'],
							'mac' => $if['mac_address'],
							'type'=> $if['type']
					);
				elseif($if['ip'] != '127.0.0.1')
					$interface['public'][$if['name']][] = array(
							'ip'  => $if['ip'],
							'mac' => $if['mac_address'],
							'type'=> $if['type']
					);
			}
			$hostInfo['interface'] = $interface;
			// ---------------------------------------------------------------------------------- //
		}
		return $hostInfo;
	}
	// ------------------------------------------------------------------------------------------ //
	public function LoadHostInfoByHostName($hostName)
	{
		$hostInfo  = null;

		$hostInfo = $this->mongo_db->like('host', $hostName)->limit(1)->get($this->tblHosts);

		if(!empty($hostInfo))
		{
			$hostInfo = $hostInfo[0];
			$hostId   = $hostInfo['hostid'];
			$serverId = $hostInfo['zabbix_server_id'];
			// ---------------------------------------------------------------------------------- //
			$itemKeyCondition = explode(SEPARATOR, ITEMKEY_AGENT_VERSION);
			$itemAgentVer     = $this->mongo_db->where(
					array(
							'hostid'            => $hostId,
							'zabbix_server_id'  => $serverId
					))->where_in('key_', $itemKeyCondition)->get($this->tblItems);
			@$hostInfo['agent_version'] = $itemAgentVer[0]['lastvalue'];
			// ---------------------------------------------------------------------------------- //
			if(@isset($hostInfo['last_updated_available']) && $hostInfo['last_updated_available'] != '')
			{
				// $hostInfo['duration'] = time() - $historyStatus[0]['clock'];
				// $hostInfo['duration'] = CalDuration($hostInfo['last_updated_available'], time());
				$hostInfo['duration'] = secs_to_h($hostInfo['duration']);
			}
			// ---------------------------------------------------------------------------------- //
			@$productInfo = $this->mongo_db->where(
					array(
							'productid' => $hostInfo['productid']
					)
			)->get($this->tblProducts);
			@$hostInfo['product']    = $productInfo[0]['name'];
			// ---------------------------------------------------------------------------------- //
			@$departmentInfo = $this->mongo_db->where(
					array(
							'departmentid' => $productInfo[0]['departmentid']
					)
			)->get($this->tblDepartments);
			@$hostInfo['department'] = $departmentInfo[0]['name'];
			// ---------------------------------------------------------------------------------- //
			$interface = array('private' => array(), 'public' => array());
			foreach($hostInfo['interface'] as $if)
			{
				if ($if['type'] == IP_PRIVATE)
					$interface['private'][$if['name']][] = array(
							'ip'  => $if['ip'],
							'mac' => $if['mac_address'],
							'type'=> $if['type']
					);
				else
					$interface['public'][$if['name']][] = array(
							'ip'  => $if['ip'],
							'mac' => $if['mac_address'],
							'type'=> $if['type']
					);
			}
			$hostInfo['interface'] = $interface;
			// ---------------------------------------------------------------------------------- //
		}
		return $hostInfo;
	}
	// ----------------------------------------------------------------------------------------- //
	public function UseRawMongoConnection()
	{
		$this->ConnectRawConnection();
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListItemOfHost($hostId, $serverId)
	{
		$hostId   = intval($hostId);
		$serverId = intval($serverId);
		$result = array();
		$rs = $this->mongo_db->where(
					array(
							'hostid'            => $hostId,
							'zabbix_server_id'  => $serverId
					))->get($this->tblItems);
		foreach($rs as $row)
		{
			$result[$row['key_']] = $row;
		}
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListApplicationOfHost($hostId, $serverId)
	{
		$hostId   = intval($hostId);
		$serverId = intval($serverId);
		$result = array();
		if($hostId > 0)
		{
			$rs = $this->mongo_db->where(
					array(
							'hostid'            => $hostId,
							'zabbix_server_id'  => $serverId
					))->get($this->tblApplications);
		} else {
			$rs = $this->mongo_db->where(
					array(
							'zabbix_server_id'  => $serverId
					))->get($this->tblApplications);
		}
		foreach($rs as $row)
		{
			$result[$row['applicationid']] = $row;
		}
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListItemApplicationOfHost($hostId, $serverId)
	{
		$hostId   = intval($hostId);
		$serverId = intval($serverId);
		$result = array();
		$rs = $this->mongo_db->where(
				array(
						'hostid'            => $hostId,
						'zabbix_server_id'  => $serverId
				))->get($this->tblItemsApplications);
		foreach($rs as $row)
		{
			$result[] = $row;
		}
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	function ListItemOfHostKeyByItemId($hostId, $zabbixId)
	{
		$hostId   = intval($hostId);
		$zabbixId = intval($zabbixId);
		$result = array();
		$rs = $this->mongo_db->where(
				array(
						'hostid' => $hostId,
						'zabbix_server_id' => $zabbixId,
						'deleted' => 0,
						'status'  => 0
		)
				)->get($this->tblItems, true);
		foreach($rs as $row){
			$result[$row['itemid']] = $row;
		}
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadTemplateByAppName($strAppName='')
	{
		if($strAppName != '')
		{
			$rs = $this->mongo_db->where(
					array(
							'item_name' => $strAppName,
							'item_type' => 1
					)
			)->get('templates_items', true);
			foreach($rs as $row){
				return $row;
			}
		}
		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadHostTemplate($hostId, $zabbixId)
	{
		$hostId   = intval($hostId);
		$zabbixId = intval($zabbixId);
		if($hostId > 0 && $zabbixId > 0)
		{
			$rs = $this->mongo_db->where(
					array(
							'hostid' => $hostId,
							'zabbix_server_id' => $zabbixId
					)
			)->get('templates', true);
			foreach($rs as $row){
				return $row;
			}
		}
		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	function ListItemWithTemplateOfHost($hostId, $zabbixId)
	{
		$hostId   = intval($hostId);
		$zabbixId = intval($zabbixId);
		$result   = array();
		if($hostId > 0 && $zabbixId > 0)
		{
			$rs = $this->mongo_db->where(
					array(
							'hostid' => $hostId,
							'zabbix_server_id' => $zabbixId
					)
			)->get('items_templates', true);
			foreach($rs as $row){
				$result[$row['itemid']] = $row;
			}
		}
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadTemplateByItemKey($strKey='')
	{
		if($strKey != '')
		{
			$rs = $this->mongo_db->where(
					array(
							'item_name' => $strKey,
							'item_type' => 0
					)
			)->get('templates_items', true);
			foreach($rs as $row){
				return $row;
			}
		}
		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	public function IsCollectionExists($strCollectionName)
	{
		$rs = $this->mongo_db->where(
        	array(
				'name' => $this->mongo_config_default['mongo_database'] . '.' . $strCollectionName
			)
		)->get('system.namespaces');
		if (count($rs) > 0)
        	return true;
		else
			return false;
    }
	// ------------------------------------------------------------------------------------------ //
	function GetPartitionCollections($iSerializeType, $from_time, $to_time)
	{
		$arrCollections = array();
		$arrCollectionInfo = $this->GetPartitionCollectionInfo($iSerializeType);
		$strCollectionPrefix = $arrCollectionInfo['collection_prefix'];

		$strCollectionSuffixOfFromTime = $this->GetPartitionSuffixByClock($from_time, $arrCollectionInfo['partition_type'], $arrCollectionInfo['partition_units']);
		$strCollectionSuffixOfToTime = $this->GetPartitionSuffixByClock($to_time, $arrCollectionInfo['partition_type'], $arrCollectionInfo['partition_units']);
		if ($strCollectionSuffixOfFromTime !== $strCollectionSuffixOfToTime)
		{
			$arrCollections = array($strCollectionPrefix.$strCollectionSuffixOfFromTime
									, $strCollectionPrefix.$strCollectionSuffixOfToTime);
		}
		else
			$arrCollections = array($strCollectionPrefix.$strCollectionSuffixOfFromTime);
		return $arrCollections;
	}
    // ------------------------------------------------------------------------------------------ //
	function GetPartitionCollectionInfo($iSerializeType)
	{
		$iSerializeType = intval($iSerializeType);

		if(!empty($iSerializeType))
		{
			$result = $this->mongo_db->limit(1)->get_where($this->tblRefPartitionHistory, array(
								'serialize_type_id' => $iSerializeType
						));

			if(count($result))
			{
				return $result[0];
			}
		}
		return NULL;
	}
	 // ------------------------------------------------------------------------------------------ //
	function GetRefPartitionHistory()
	{
		$result = $this->mongo_db->get_where($this->tblRefPartitionHistory, array());
		if(count($result))
		{
			return $result;
		}
		return NULL;
	}
	// ------------------------------------------------------------------------------------------ //
	function GetPartitionSuffixByClock($iClock, $strPartitionType, $iPartitionUnits)
	{
		$strResult = '';
		$iClock = intval($iClock);
		$iPartitionUnits = intval($iPartitionUnits);
		$strDay = date('d', $iClock);
		$strMonth = date('m', $iClock);
		$strYear = date('Y', $iClock);
		$strLastDayOfMonth = date('t', $iClock);

		if ($strPartitionType == 'day')
		{
			if ($iPartitionUnits == 1) {
				$strResult = sprintf('_%02d%02d%s', intval($strDay), intval($strMonth), $strYear);
				return $strResult;
			}
			$iStartDate = 0;
			$iEndDate = 0;

			if (intval($strDay) <= $iPartitionUnits)
			{
				$iStartDate = 1;
				$iEndDate	= $iPartitionUnits;
			}
			else
			{
				$iStartDate 	= ((intval(intval($strDay)/$iPartitionUnits)) * $iPartitionUnits) + 1;
				if (intval($strDay) % $iPartitionUnits == 0)
					$iStartDate = intval(intval($strDay)/$iPartitionUnits) * $iPartitionUnits - $iPartitionUnits + 1;
				$iEndDate 	= $iStartDate + $iPartitionUnits - 1;
				if (intval($iEndDate) > intval($strLastDayOfMonth))
					$iEndDate = intval($strLastDayOfMonth);

			}
			$strResult = sprintf('_%02d%02d%s_%02d%02d%s', $iStartDate, intval($strMonth), $strYear,
																$iEndDate, intval($strMonth), $strYear);

		}
		elseif ($strPartitionType == 'month')
		{
			if ($iPartitionUnits == 1) {
				$strResult = sprintf('_%02d%s', intval($strMonth), $strYear);
				return $strResult;
			}
			$iStartMonth = 0;
			$iEndMonth = 0;
			if (intval($strMonth) <= $iPartitionUnits)
			{
				$iStartMonth = 1;
				$iEndMonth	 = $iPartitionUnits;
			}
			else
			{
				$iStartMonth 	= ((intval(intval($strMonth)/$iPartitionUnits)) * $iPartitionUnits) + 1;
				if (intval($strMonth) % $iPartitionUnits == 0)
					$iStartMonth = intval(intval($strMonth)/$iPartitionUnits) * $iPartitionUnits - $iPartitionUnits + 1;
				$iEndMonth 	= $iStartMonth + $iPartitionUnits - 1;
			}
			$strResult = sprintf('_%02d%s_%02d%s', $iStartMonth, $strYear, $iEndMonth, $strYear);
		}
		elseif ($strPartitionType == 'year')
		{
			if ($iPartitionUnits == 1) {
				$strResult = sprintf('_%s', $strYear);
				return $strResult;
			}
			$iStartYear = 0;
			$iEndYear = 0;
			if (intval($strYear) <= $iPartitionUnits)
			{
				$iStartYear = 1;
				$iEndYear = $iPartitionUnits;
			}
			else
			{
				$iStartYear 	= ((intval(intval($strYear)/$iPartitionUnits)) * $iPartitionUnits) + 1;
				if (intval($strYear) % $iPartitionUnits == 0)
					$iStartYear = intval(intval($strYear)/$iPartitionUnits) * $iPartitionUnits - $iPartitionUnits + 1;
				$iEndYear 	= $iStartYear + $iPartitionUnits - 1;

			}
			$strResult = sprintf('_%s_%s', $iStartYear, $iEndYear);
		}
		return $strResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function GetCommonCollection($iSerializeType, $iFromClock, $iToClock)
	{
		$oPartition = $this->GetPartitionCollectionInfo($iSerializeType);

		if (isset($oPartition) && is_array($oPartition) && count($oPartition) > 0) {
			$strPrefix = $oPartition['collection_prefix'];
			$strFromSuffix 	= $this->GetPartitionSuffixByClock($iFromClock, $oPartition['partition_type'],  $oPartition['partition_units']);
			$strToSuffix 	= $this->GetPartitionSuffixByClock($iToClock, $oPartition['partition_type'],  $oPartition['partition_units']);

			while ($strFromSuffix != $strToSuffix) {
				$oPartition = $this->GetNextPartitionType($oPartition['partition_type'], $oPartition['partition_units']);
				$strFromSuffix 	= $this->GetPartitionSuffixByClock($iFromClock, $oPartition['partition_type'],  $oPartition['partition_units']);
				$strToSuffix 	= $this->GetPartitionSuffixByClock($iToClock, $oPartition['partition_type'],  $oPartition['partition_units']);
				# ensure that these collection has already existed, else continue to next partition type
				$bFromCollectionExisted = $this->IsCollectionExists($strPrefix.$strFromSuffix);
				$bToCollectionExisted = $this->IsCollectionExists($strPrefix.$strToSuffix);
				if (!$bFromCollectionExisted || !$bToCollectionExisted)
				{
					$strToSuffix = '';
				}
			}
			$strSuffix = $strFromSuffix;
			$strCommonCollection = $strPrefix.$strSuffix;
			return $strCommonCollection;
		}
		return NULL;
	}
	// ------------------------------------------------------------------------------------------ //
	function GetNextPartitionType($strPartitionType, $iPartitionUnits)
	{
		if ($strPartitionType == 'day')
		{
			return array('partition_type' => 'month', 'partition_units' => 1);
		}
		elseif ($strPartitionType == 'month' && intval($iPartitionUnits) == 1)
		{
			return array('partition_type' => 'month', 'partition_units' => 3);
		}
		elseif ($strPartitionType == 'month' && intval($iPartitionUnits) == 3)
		{
			return array('partition_type' => 'year', 'partition_units' => 1);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	protected function CheckValidCondition($arrCondition){
		$result = true;
		if(isset($arrCondition['$or']) && count($arrCondition['$or']) <= 0) $result = false;

		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
}
?>
