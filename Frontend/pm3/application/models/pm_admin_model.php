<?php
require_once "pm_model.php";

class Pm_admin_model extends Pm_model
{
	function __construct($right=null)
 	{
		parent :: __construct();
	}
	// ------------------------------------------------------------------------------------------ //
	function ListAdminRole($arrCondition=array()){
		$arrResult = array();
		@$arrCondition['deleted'] = 0;
		$arrTMPResult = $this->SelectMongoDB($arrCondition, $this->cltAdminRoles, 0, UNLIMITED, array('order' => 1));
		foreach($arrTMPResult as $oValue){
			$arrResult[(string)$oValue['_id']] = $oValue;
		}
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function ListAdminPermissionName($arrCondition=array()){
		@$arrCondition['deleted'] = 0;
		$arrResult1 = $this->SelectMongoDB($arrCondition, $this->cltAdminPermissionName, 0, UNLIMITED, array('name' => 1));
		$arrResult2 = $this->ListAdminRole();
		$arrResult3 = $this->ListRole();

		return array($arrResult1, $arrResult2, $arrResult3);
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadAdminRolePermission($strUserGroupsId=''){
		if(!empty($strUserGroupsId)){
			$arrCondition = array();
			$arrCondition['deleted'] = 0;
			$arrCondition['user_group_id'] = new MongoId($strUserGroupsId);

			$arrResult = $this->SelectMongoDB($arrCondition, $this->cltRolePermissions);
		} else {
			return array();
		}
	}
	// ------------------------------------------------------------------------------------------ //
	function SaveAdminRolesPermission($strUserGroupId, $arrRolePermission){
		if(!empty($arrRolePermission) && is_array($arrRolePermission)){
			foreach($arrRolePermission as $oRole){
				$oOldPer = $this->SelectOneMongoDB(array(
					'pm_role_id'	=> new MongoId($oRole->role_id),
					'permission_id'	=> new MongoId($oRole->pn_id),
					'user_group_id'	=> new MongoId($strUserGroupId),
					'type'			=> $oRole->pn_type
				), $this->cltAdminRolePermissions);
				if($oOldPer){
					$this->TrackingUpdate($oOldPer, $oRole, $this->cltAdminRolePermissions);
					$this->UpdateMongoDB(
						array('_id' => $oOldPer['_id']),
						array('permission_value' => $oRole->value),
						$this->cltAdminRolePermissions
					);
				} else {
					$this->TrackingInsert($oOldPer, $oRole, $this->cltAdminRolePermissions);
					$this->InsertMongoDB($arrData = array(
						'pm_role_id'	=> new MongoId($oRole->role_id),
						'permission_id'	=> new MongoId($oRole->pn_id),
						'user_group_id'	=> new MongoId($strUserGroupId),
						'type'			=> $oRole->pn_type,
						'permission_value' => $oRole->value,
						'created_date'	=> date('Y-m-d H:i:s'),
						'deleted'		=> 0,
						'created_by'	=> @$this->session->userdata('username')
					), $this->cltAdminRolePermissions);
				}
			}
		}
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadAdminUserGroupPermission($arrCondition=array()){
		$arrResult = array();
		if(!@empty($arrCondition['user_group_id'])){
			$arrTMP = $this->SelectMongoDB(array(
				'user_group_id'	=> new MongoId($arrCondition['user_group_id'])
			), $this->cltAdminRolePermissions);
			foreach($arrTMP as $oPer){
				$strKey = $oPer['type'] . '_' . $oPer['pm_role_id'] . $oPer['permission_id'];
				$arrResult[$strKey] = $oPer['permission_value'];
			}
		}
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListPMUsersRole($oCondition=null, $arrPermittedUG, $arrPagination=null) {
		$arrCondition = array();
		$arrCondition['deleted'] = 0;
		if(isset($oCondition['search_by_name'])) {
			$arrCondition['username'] = $oCondition['search_by_name'];
		}
		if(isset($oCondition['user_group'])) {
			$arrCondition['user_group_id'] = new MongoId($oCondition['user_group']);
		} else {
			$arrCondition['user_group_id'] = array('$in' => $arrPermittedUG);
		}
		// pd($arrCondition);
		if(isset($arrPagination['limit'])) $iLimit = $arrPagination['limit'];
		if(isset($arrPagination['offset'])) $iOffset = $arrPagination['offset'];
		// p($arrCondition); pd($arrPagination);
		if(isset($iLimit) && isset($iOffset)) {
			return $this->SelectMongoDB($arrCondition, CLT_PM_USERS_ROLE, $iOffset, $iLimit);
		} else {
			return $this->SelectMongoDB($arrCondition, CLT_PM_USERS_ROLE);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function CountPMUsersRoleList($oCondition=null, $arrPermittedUG) {
		$arrCondition = array();
		$arrCondition['deleted'] = 0;
		if(isset($oCondition['search_by_name'])) {
			$arrCondition['username'] = $oCondition['search_by_name'];
		}
		if(isset($oCondition['user_group'])) {
			$arrCondition['user_group_id'] = new MongoId($oCondition['user_group']);
		} else {
			$arrCondition['user_group_id'] = array('$in' => $arrPermittedUG);
		}
		return $this->CountMongoDB($arrCondition, CLT_PM_USERS_ROLE);
	}
	// ------------------------------------------------------------------------------------------ //

}