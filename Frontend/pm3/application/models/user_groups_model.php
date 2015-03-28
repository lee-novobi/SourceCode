<?php
require_once "pm_model.php";

class User_groups_model extends Pm_model
{
	function __construct($right=null)
 	{
		parent :: __construct();
	}
	// ------------------------------------------------------------------------------------------ //
	public function AddProductToGroup($oProductKey, $oUserGroupId){
		if(!empty($oProductKey) && !empty($oUserGroupId)){
			if(!is_object($oUserGroupId)) $oUserGroupId = new MongoId($oUserGroupId);
			$arrData = array(
				'user_group_id' => $oUserGroupId,
				'product_key'   => MongoSaveObj(PRODUCT_KEY, $oProductKey),
				'deleted'       => 0,
				'created_date'  => date('Y-m-d H:i:s'),
				'created_by'    => $this->session->userdata('username')
			);
			return $this->InsertMongoDB($arrData, CLT_USER_GROUPS_PRODUCTS);
		}
		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	public function RemoveProductFromGroup($oProductKey, $oUserGroupId){
		if(!empty($oProductKey) && !empty($oUserGroupId)){
			if(!is_object($oUserGroupId)) $oUserGroupId = new MongoId($oUserGroupId);
			$arrCond = array(
				'product_key'   => MongoCondObj(PRODUCT_KEY, $oProductKey),
				'user_group_id' => $oUserGroupId
			);
			#pd($arrCond);
			$arrData = array(
				'deleted'       => 1,
				'deleted_date'  => date('Y-m-d H:i:s'),
				'deleted_by'    => $this->session->userdata('username')
			);
			return $this->UpdateMongoDB($arrCond, $arrData, CLT_USER_GROUPS_PRODUCTS);
		}
		return true;
	}
	// ------------------------------------------------------------------------------------------ //
	public function AddUserToGroup($strUsername, $oUserGroupId){
		if(!empty($strUsername) && !empty($oUserGroupId)){
			if(!is_object($oUserGroupId)) $oUserGroupId = new MongoId($oUserGroupId);
			$arrData = array(
				'user_group_id' => $oUserGroupId,
				'username'      => trim($strUsername),
				'deleted'       => 0,
				'created_date'  => date('Y-m-d H:i:s'),
				'created_by'    => $this->session->userdata('username')
			);
			return $this->InsertMongoDB($arrData, CLT_USER_GROUPS_USERS);
		}
		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	public function RemoveUserFromGroup($strUsername, $oUserGroupId){
		if(!empty($strUsername) && !empty($oUserGroupId)){
			if(!is_object($oUserGroupId)) $oUserGroupId = new MongoId($oUserGroupId);
			RemoveUserFromPMRole($strUsername, $oUserGroupId);

			$arrCond = array(
				'username'      => new MongoRegex('/^' . trim($strUsername) . '$/i'),
				'user_group_id' => $oUserGroupId,
				'deleted'       => 0
			);
			$arrData = array(
				'deleted'       => 1,
				'deleted_date'  => date('Y-m-d H:i:s'),
				'deleted_by'    => $this->session->userdata('username')
			);
			return $this->UpdateMongoDB($arrCond, $arrData, CLT_USER_GROUPS_USERS);
		}
		return true;
	}
	// ------------------------------------------------------------------------------------------ //
	public function RemoveUserFromPMRole($strUsername, $oUserGroupId){
		if(!empty($strUsername) && !empty($oUserGroupId)){
			if(!is_object($oUserGroupId)) $oUserGroupId = new MongoId($oUserGroupId);
			$arrCond = array(
				'username'      => new MongoRegex('/^' . trim($strUsername) . '$/i'),
				'user_group_id' => $oUserGroupId,
				'deleted'       => 0
			);
			$arrData = array(
				'deleted'       => 1,
				'deleted_date'  => date('Y-m-d H:i:s'),
				'deleted_by'    => $this->session->userdata('username')
			);
			return $this->UpdateMongoDB($arrCond, $arrData, CLT_PM_USERS_ROLE);
		}
		return true;
	}
	// ------------------------------------------------------------------------------------------ //
	public function UpdateUserGroup($oUserGroupId, $arrData){
		if(!empty($oUserGroupId) && count($arrData)>0){
			if(!is_object($oUserGroupId)) $oUserGroupId = new MongoId($oUserGroupId);
			$arrCond = array(
				'_id'     => $oUserGroupId,
				'deleted' => 0
			);
			return $this->UpdateMongoDB($arrCond, $arrData, CLT_USER_GROUPS);
		}
		return true;
	}
	// ------------------------------------------------------------------------------------------ //
	public function LoadGroupByGroupName($strGroupName){
		if(!empty($strGroupName)){
			$arrCond = array(
				'group_name' => new MongoRegex('/^' . preg_quote(trim($strGroupName)) . '$/i'),
				'deleted'    => 0
			);
			return $this->SelectOneMongoDB($arrCond, CLT_USER_GROUPS);
		}
		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	public function InsertGroup($strGroupName, $strDepartmentKey){
		if(!empty($strGroupName)){
			$arrData = array(
				'group_name'     => preg_quote(trim($strGroupName)),
				'department_key' => MongoSaveObj(DEPARTMENT_KEY, $strDepartmentKey),
				'is_default'     => 0,
				'deleted'        => 0,
				'created_date'   => date('Y-m-d H:i:s'),
				'created_by'     => $this->session->userdata('username')
			);
			$oRs = $this->InsertMongoDB($arrData, CLT_USER_GROUPS);
			if($oRs){
				return $arrData['_id'];
			}
		}
		return false;
	}
	// ------------------------------------------------------------------------------------------ //
	public function SetRole($oUserGroupId, $strUsername='', $oPMRoleId=''){
		if(!empty($oUserGroupId)){
			if(!is_object($oUserGroupId)) $oUserGroupId = new MongoId($oUserGroupId);
			if(empty($strUsername) && empty($oPMRoleId)){
				$strUsername = $this->session->userdata('username');
				$oPMRoleId   = @$this->oUserProfile['oPMRoleForOwnUserGroup'];
			}
			if(!empty($strUsername) && !empty($oPMRoleId)){
				if(!is_object($oPMRoleId)) $oPMRoleId = new MongoId($oPMRoleId);
				$arrData = array(
					'username'      => preg_quote(trim($strUsername)),
					'user_group_id' => $oUserGroupId,
					'pm_role_id'    => $oPMRoleId,
					'deleted'       => 0,
					'created_date'  => date('Y-m-d H:i:s'),
					'created_by'    => $this->session->userdata('username')
				);
				$oRs = $this->InsertMongoDB($arrData, CLT_PM_USERS_ROLE);
				if($oRs){
					return $arrData['_id'];
				}
			}
		}
		return false;
	}
}