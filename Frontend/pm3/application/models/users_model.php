<?php
require_once "pm_model.php";

class Users_model extends Pm_model
{
	function __construct($right=null)
 	{
		parent :: __construct();
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListBelongToUserGroups($arrCondition){
		$arrResult = array();
		$arrTMP = $this->SelectMongoDB($arrCondition, $this->cltAdminUsersRole);

		foreach($arrTMP as $oTmp){
			$arrTmpCond = array(
				'_id' => $oTmp['user_group_id'],
				'deleted' => 0
			);
			$oUserGroup = $this->SelectOneMongoDB($arrTmpCond, $this->cltUserGroups);
			// ---------------------------------------------------------------------------------- //
			if($oUserGroup){
				$oUserGroup['pm_user_role_id'] = (string)$oTmp['_id'];
				$strGroupId = (string)$oUserGroup['_id'];
				$arrTmpCond = array(
					'_id' => $oTmp['pm_role_id'],
					'deleted' => 0
				);
				$oPmRole = $this->SelectOneMongoDB($arrTmpCond, $this->cltAdminRoles);
				if($oPmRole){
					$oUserProfile = $this->session->userdata('oUserProfile');
					if(array_key_exists($strGroupId, $oUserProfile['arrPermittedUserGroupDetail'])
						&& array_key_exists((string)$oPmRole['_id'], $oUserProfile['arrPermittedUserGroupDetail'][$strGroupId]['arrPMRoleAssignable'])){
							$oPmRole['assignable'] = 1;
					} else {
						$oPmRole['assignable'] = 0;
					}
				}
				$oUserGroup['pm_role'] = $oPmRole;

				if(in_array(ADD_PRODUCT, $oUserProfile['arrPermittedUserGroupDetail'][$strGroupId]['arrRightOnGroup'])
					|| in_array(DELETE_PRODUCT, $oUserProfile['arrPermittedUserGroupDetail'][$strGroupId]['arrRightOnGroup'])
					|| in_array(ADD_USER, $oUserProfile['arrPermittedUserGroupDetail'][$strGroupId]['arrRightOnGroup'])
					|| in_array(DELETE_USER, $oUserProfile['arrPermittedUserGroupDetail'][$strGroupId]['arrRightOnGroup'])){
						$oUserGroup['editable'] = 1;
				} else {
					$oUserGroup['editable'] = 0;
				}

				if(in_array(DELETE_USER, $oUserProfile['arrPermittedUserGroupDetail'][$strGroupId]['arrRightOnGroup'])){
						$oUserGroup['deletable_user'] = 1;
				} else {
					$oUserGroup['deletable_user'] = 0;
				}

				if(in_array(ADD_USER, $oUserProfile['arrPermittedUserGroupDetail'][$strGroupId]['arrRightOnGroup'])){
						$oUserGroup['addable_user'] = 1;
				} else {
					$oUserGroup['addable_user'] = 0;
				}

				if(in_array(DELETE_PRODUCT, $oUserProfile['arrPermittedUserGroupDetail'][$strGroupId]['arrRightOnGroup'])){
						$oUserGroup['deletable_product'] = 1;
				} else {
					$oUserGroup['deletable_product'] = 0;
				}
				if(in_array(ADD_PRODUCT, $oUserProfile['arrPermittedUserGroupDetail'][$strGroupId]['arrRightOnGroup'])){
						$oUserGroup['addable_product'] = 1;
				} else {
					$oUserGroup['addable_product'] = 0;
				}

				/*$arrProductOwning = $this->ListProductOwningByUser($oUserGroup['_id'], $arrCondition['username']);
				foreach($arrProductOwning as &$oProduct){
					$strRoleId = (string)$oProduct['role']['_id'];
					$oProduct['role']['assignable'] = 0;
					if(array_key_exists($strGroupId, $oUserProfile['arrPermittedUserGroupDetail']) && array_key_exists($strRoleId, $oUserProfile['arrPermittedUserGroupDetail'][$strGroupId]['arrPermittedProductRole'])){
						$oProduct['role']['assignable'] = 1;
					}
				}
				$oUserGroup['arrProductOwning'] = $arrProductOwning;*/
				//$oUserGroup['arrProductAssignable'] = $this->ListProductOfUserGroup($oUserGroup['_id']);


				$arrResult[(string)$oUserGroup['_id']] = $oUserGroup;
			}
			// ---------------------------------------------------------------------------------- //
		}

		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListProductInUserGroupOwnedByUser($strUsername, $oProductKey=null, $oRoleId=null){
		$arrResult = array();
		$arrCondition = array(
			'deleted' => 0,
			'username' => new MongoRegex('/^'.$strUsername.'$/i')
		);

		if(!empty($oProductKey)) $arrCondition[PRODUCT_KEY] = MongoCondObj(PRODUCT_KEY, $oProductKey);
		if(!empty($oRoleId)) $arrCondition['role_id'] = $oRoleId;
		$arrResult = $this->SelectMongoDB($arrCondition, CLT_PRODUCT_OWNER);
		foreach($arrResult as &$oTmp){
			$oRole = $this->SelectOneMongoDB(array(
				'deleted' => 0,
				'_id' => $oTmp['role_id']
			), CLT_ROLES);
			$oTmp['role'] = $oRole;
		}
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListProductOwnedByUser($arrCondition){
		$arrResult = array();
		$oUserProfile = $this->session->userdata('oUserProfile');

		@$arrCondition['deleted'] = 0;
		#pd($arrCondition);
		$arrTMP = $this->SelectMongoDB($arrCondition, CLT_PRODUCT_OWNER);
		foreach($arrTMP as $oTMPProduct){
			$oProduct = $this->cmdbv2_model->LoadProductByObjectId($oTMPProduct['product_key']);
			if($oProduct){
				$oProduct['product_owner_id'] = (string)$oTMPProduct['_id'];
				$oProductRole = $this->SelectOneMongoDB(array('_id' => $oTMPProduct['role_id'], 'deleted' => 0), CLT_ROLES);
				$oProductRole['user_group_id'] = (string)$oTMPProduct['user_group_id'];

				if(array_key_exists((string)$oTMPProduct['role_id'], $oUserProfile['arrPermittedUserGroupDetail'][$oProductRole['user_group_id']]['arrPermittedProductRole'])){
					$oProductRole['is_editable'] = 1;
				} else {
					$oProductRole['is_editable'] = 0;
				}
				$oProduct['role'] = $oProductRole;
				$arrResult[(string)$oProduct['_id']][] = $oProduct;
			}
		}
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function UpdateUserToGroup($strUsername, $strPmUserRoleId, $strUserGroupId, $strPmRoleId){
		$arrCondition = array(
			'_id'     => new MongoId($strPmUserRoleId),
			'deleted' => 0
		);
		$arrData = array(
			'pm_role_id'    => new MongoId($strPmRoleId)
		);
		return $this->UpdateMongoDB($arrCondition, $arrData, CLT_PM_USERS_ROLE);
	}
	// ------------------------------------------------------------------------------------------ //
	public function InsertUserToGroup($strUsername, $strUserGroupId, $strPmRoleId){
		$arrData = array(
			'username'      => trim($strUsername),
			'user_group_id' => new MongoId($strUserGroupId),
			'pm_role_id'    => new MongoId($strPmRoleId),
			'deleted'       => 0,
			'created_date'  => date('Y-m-d H:i:s'),
			'created_by'    => $this->session->userdata('username')
		);
		$oRs = $this->InsertMongoDB($arrData, CLT_PM_USERS_ROLE);
		return ($oRs === false)?$oRs:$arrData['_id'];
	}
	// ------------------------------------------------------------------------------------------ //
	public function LoadPmUserRole($strUsername, $strUserGroupId){
		$arrCondition = array(
			'username'      => new MongoRegex('/^' . trim($strUsername) . '$/i'),
			'user_group_id' => new MongoId($strUserGroupId),
			'deleted' => 0
		);

		return $this->SelectOneMongoDB($arrCondition, CLT_PM_USERS_ROLE);
	}
	// ------------------------------------------------------------------------------------------ //
	public function InsertNewUser($strUsername, $strDepartmentKey){
		if(!empty($strUsername)){
			$arrData = array(
				'username'       => preg_quote(trim($strUsername)),
				'department_key' => MongoSaveObj(DEPARTMENT_KEY, $strDepartmentKey),
				'deleted'        => 0,
				'created_date'   => date('Y-m-d H:i:s'),
				'created_by'     => $this->session->userdata('username')
			);
			$oRs = $this->InsertMongoDB($arrData, CLT_USERS);
			if($oRs){
				return $arrData['_id'];
			}
		}
		return false;
	}
	// ------------------------------------------------------------------------------------------ //
	public function RemoveUserFromProduct($strUsername, $strProductOwnerId, $strUserGroupId, $strPmRoleId){
		$arrCondition = array(
			'_id'     => new MongoId($strProductOwnerId),
			'deleted' => 0
		);
		$arrData = array(
			'deleted' => 1
		);
		return $this->UpdateMongoDB($arrCondition, $arrData, CLT_PRODUCT_OWNER);
	}
	// ------------------------------------------------------------------------------------------ //
	public function LoadProductOwnedByUser($strUsername, $strProductKey, $strUserGroupId, $strRoleId){
		$arrCondition = array(
			'username'      => new MongoRegex('/^' . trim($strUsername) . '$/i'),
			'user_group_id' => new MongoId($strUserGroupId),
			'role_id'       => new MongoId($strRoleId),
			'product_key'   => MongoCondObj(PRODUCT_KEY, $strProductKey),
			'deleted' => 0
		);

		return $this->SelectOneMongoDB($arrCondition, CLT_PRODUCT_OWNER);
	}
	// ------------------------------------------------------------------------------------------ //
	public function InsertUserToProduct($strUsername, $strProductKey, $strUserGroupId, $strRoleId){
		$arrData = array(
			'username'      => trim($strUsername),
			'user_group_id' => new MongoId($strUserGroupId),
			'role_id'       => new MongoId($strRoleId),
			'product_key'   => MongoSaveObj(PRODUCT_KEY, $strProductKey),
			'deleted'       => 0,
			'created_date'  => date('Y-m-d H:i:s'),
			'created_by'    => $this->session->userdata('username')
		);
		$oRs = $this->InsertMongoDB($arrData, CLT_PRODUCT_OWNER);
		return ($oRs === false)?$oRs:$arrData['_id'];
	}
	// ------------------------------------------------------------------------------------------ //
	public function UpdateUserToProduct($strUsername, $strProductOwnerId, $strProductKey, $strUserGroupId, $strRoleId){
		$arrCondition = array(
			'_id'     => new MongoId($strProductOwnerId),
			'deleted' => 0
		);
		$arrData = array(
			'user_group_id' => new MongoId($strUserGroupId),
			'role_id'       => new MongoId($strRoleId),
			'product_key'   => MongoSaveObj(PRODUCT_KEY, $strProductKey),
		);
		return $this->UpdateMongoDB($arrCondition, $arrData, CLT_PRODUCT_OWNER);
	}
	// ------------------------------------------------------------------------------------------ //
	public function UpdateUserInfo($strUsername, $arrInfo){
		$arrCondition = array(
			'username' => new MongoRegex('/^' . trim($strUsername) . '$/i'),
			'deleted'  => 0
		);
		return $this->UpdateMongoDB($arrCondition, $arrInfo, CLT_USERS);
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListUser($arrCondition=array()){
		$arrCondition['deleted'] = 0;
		return $this->SelectMongoDB($arrCondition, CLT_USERS);
	}
}