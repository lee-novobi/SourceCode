<?php
require_once "mongo_base_model.php";

class Pm_model extends Mongo_base_model
{
	function __construct($right=null)
 	{
		parent :: __construct();
		if(!class_exists('Mongo_db'))
		{
			$this->load->library('mongo_db', array('pm'));
		}
		$this->load->model('cmdbv2_model');
		$this->mongo_config_pm3  = $this->config->item('pm');
	}

	// ------------------------------------------------------------------------------------------ //
	function LoadDefaultGroup(){
		return $this->SelectOneMongoDB(
			array(
				'deleted'    => 0,
				'is_default' => 1
			), CLT_USER_GROUPS
		);
	}
	// ------------------------------------------------------------------------------------------ //
	function ListRole($arrCondition=array()){
		@$arrCondition['deleted'] = 0;
		#pd($arrCondition);
		$arrResult = $this->SelectMongoDB($arrCondition, $this->cltRoles, 0, UNLIMITED, array('order' => 1));
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function SaveRolesOrder($arrRole){
		if(!empty($arrRole) && is_array($arrRole)){
			foreach($arrRole as $oRole){
				$this->UpdateMongoDB(
					array('_id' => new MongoId($oRole->_id)),
					array('order' => $oRole->order),
					$this->cltRoles
				);
			}
		}
	}
	// ------------------------------------------------------------------------------------------ //
	function ListUserGroups($arrCondition=array()){
		@$arrCondition['deleted'] = 0;
		$arrResult = array();
		$arrTMPResult = $this->SelectMongoDB($arrCondition, $this->cltUserGroups, 0, UNLIMITED, array('is_default' => -1, 'group_name' => 1));

		foreach($arrTMPResult as $oValue){
			$arrResult[(string)$oValue['_id']] = $oValue;
		}
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function ListRoleOfUserGroups($arrCondition=array()){
		@$arrCondition['deleted'] = 0;
		$arrResult = $this->SelectMongoDB($arrCondition, CLT_PM_ROLES, 0, UNLIMITED, array('order' => 1, 'role_name' => 1));
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function ListPermissionName($arrCondition=array()){
		@$arrCondition['deleted'] = 0;
		$arrResult = $this->SelectMongoDB($arrCondition, $this->cltPermissionName, 0, UNLIMITED, array('name' => 1));
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function ListPermissionNameGroups($arrCondition=array()){
		@$arrCondition['deleted'] = 0;
		$arrResult = $this->SelectMongoDB($arrCondition, $this->cltPermissionNameGroups, 0, UNLIMITED, array('order' => 1));
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadRolePermission($strUserGroupsId=''){
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
	function LoadDefaultUserGroup($arrCondition=array()){
		@$arrCondition['is_default'] = 1;
		@$arrCondition['deleted'] = 0;

		$oResult = $this->SelectOneMongoDB($arrCondition, $this->cltUserGroups);

		return $oResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function SaveRolesPermission($strUserGroupId, $arrRolePermission){
		if(!empty($arrRolePermission) && is_array($arrRolePermission)){
			foreach($arrRolePermission as $oRole){
				if(array_key_exists($oRole->role_id, $this->oUserProfile['arrPermittedUserGroupDetail'][$strUserGroupId]['arrPermittedProductRole'])){
					$oOldPer = $this->SelectOneMongoDB(array(
						'role_id'		=> new MongoId($oRole->role_id),
						'permission_id'	=> new MongoId($oRole->pn_id),
						'user_group_id'	=> new MongoId($strUserGroupId)
					), $this->cltRolePermissions);
					if($oOldPer){
						$this->TrackingUpdate($oOldPer, $oRole, $this->cltRolePermissions);
						$this->UpdateMongoDB(
							array('_id' => $oOldPer['_id']),
							array('permission_value' => $oRole->value),
							$this->cltRolePermissions
						);
					} else {
						$this->TrackingInsert($oOldPer, $oRole, $this->cltRolePermissions);
						$this->InsertMongoDB($arrData = array(
							'role_id'		=> new MongoId($oRole->role_id),
							'permission_id'	=> new MongoId($oRole->pn_id),
							'user_group_id'	=> new MongoId($strUserGroupId),
							'permission_value' => $oRole->value,
							'created_date'	=> date('Y-m-d H:i:s'),
							'deleted'		=> 0,
							'created_by'	=> @$this->session->userdata('username')
						), $this->cltRolePermissions);
					}
				}
			}
		}
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadUserGroupPermission($arrCondition=array()){
		$arrResult = array();
		if(!@empty($arrCondition['user_group_id'])){
			$arrTMP = $this->SelectMongoDB(array(
				'user_group_id'	=> new MongoId($arrCondition['user_group_id'])
			), $this->cltRolePermissions);
			foreach($arrTMP as $oPer){
				$strKey = $oPer['role_id'] . $oPer['permission_id'];
				$arrResult[$strKey] = $oPer['permission_value'];
			}
		}
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadUserProfileSuperAdmin(){
		$arrResult = array(
			'oDefaultGroup'               => $this->LoadDefaultGroup(),
			'oPMRoleForOwnUserGroup'      => $this->session->userdata('userNewGroupRole'),
			'isAllowCreateGroup'          => true,
			'isPermittedOnDefault'        => true,
			'defaultRole'                 => array(),
			'arrPermittedUserGroup'       => array(),
			'arrUserGroupSummary'         => array(),
			'arrPermittedUserGroupDetail' => array(),
			'arrAssignableProduct'        => array()
		);

		$arrUserGroup     = $this->ListUserGroups();
		$arrUserGroupRole = $this->ListRoleOfUserGroups();
		$arrProductRole   = $this->ListRole();
		$arrAllProduct    = $this->cmdbv2_model->ListProduct();
		$arrAdminPermissionName = $this->SelectMongoDB(array('deleted' => 0), $this->cltAdminPermissionName, 0, UNLIMITED, array('name' => 1));
#pd($arrUserGroupRole);
		$arrPermittedProductRole = array();
		foreach($arrProductRole as &$oProductRole){
			$arrPermittedProductRole[(string)$oProductRole['_id']] = &$oProductRole;
		}

		$arrRightOnGroup = array();
		foreach($arrAdminPermissionName as $oAdminPermissionName){
			$arrRightOnGroup[(string)$oAdminPermissionName['_id']] = strtolower($oAdminPermissionName['name']);
		}

		$arrPMRoleAssignable = array();
		foreach($arrUserGroupRole as $oUserGroupRole){
			$arrPMRoleAssignable[(string)$oUserGroupRole['_id']] = strtolower($oUserGroupRole['role_name']);
		}

		$arrAssignableProduct = array();
		foreach($arrAllProduct as $oProduct){
			$strProductKey = (string)$oProduct[PRODUCT_KEY];
			$oProductSummary = array(
				'product_key'   => (string)$oProduct[PRODUCT_KEY],
				'user_group_id' => '',
				'name'          => $oProduct['alias'],
				'is_addable'    => 1,
				'is_deletable'  => 1
			);
			$arrAssignableProduct[$strProductKey] = $oProductSummary;
		}
		unset($arrAllProduct);

#pd($arrPermittedProductRole);
		$oPermittedUserGroupDetail = array();
		foreach($arrUserGroup as $oUserGroup){
			$strUserGroupId = (string)$oUserGroup['_id'];

			$arrResult['arrPermittedUserGroup'][$strUserGroupId] = $oUserGroup;
			$arrResult['arrUserGroupSummary'][] = array(
				'id'         => (string)$oUserGroup['_id'],
				'group_name' => $oUserGroup['group_name']
			);

			$oPermittedUserGroupDetail[$strUserGroupId]['arrPermittedProductRole'] = &$arrPermittedProductRole;
			$oPermittedUserGroupDetail[$strUserGroupId]['arrRightOnGroup']         = &$arrRightOnGroup;
			$oPermittedUserGroupDetail[$strUserGroupId]['arrPMRoleAssignable']     = &$arrPMRoleAssignable;
			if($oUserGroup['is_default']==1){
				$arrResult['defaultRole']['group'] = $oUserGroup;
				$arrResult['defaultRole']['role']  = $this->SelectOneMongoDB(array('deleted' => 0, '_id' => $oUserGroup['default_product_role']), CLT_ROLES);
			}
			#$arrResult['arrPermittedUserGroupDetail']
		}
		$arrResult['arrPermittedUserGroupDetail'] = $oPermittedUserGroupDetail;
		$arrResult['arrAssignableProduct'] = &$arrAssignableProduct;
#$arrProductRole[0]['role_name'] = "QuangTM3";
#pd($arrResult);
		return $arrResult;
	}

	// ------------------------------------------------------------------------------------------ //
	function GetPermissionDetail($strUsername, $oUserGroupId, $oPmRoleId=''){
		$nUserType = $this->session->userdata('usertype');
		$oDefaultGroup = $this->oUserProfile['oDefaultGroup'];
		$oUserRightOnGroup = array(
			'arrPermittedProductRole' => array(), /* Danh sách ProductRole mà user có quyền */
			'arrRightOnGroup'         => array(), /* Danh sách quyền (vd: Add/Remove User trong group) mà user có thể action trên group */
			'arrPMRoleAssignable'     => array(), /* Danh sách AdminRole mà user có thể assign/remove cho user khác */
			'arrMemberProduct'        => array(),
			'arrMemberUser'           => array()
		);
		if(!empty($oUserGroupId)){
			if(empty($oPmRoleId)){
				$oPmUserRole = $this->SelectOneMongoDB(array(
					'username'         => new MongoRegex('/^' . trim($strUsername) . '$/i'),
					'user_group_id'	   => $oUserGroupId,
					'deleted'          => 0
				));
				$oPmRoleId = @$oPmUserRole['pm_role_id'];
			}

			if(!empty($oPmRoleId)){
				if(!is_object($oPmRoleId)) $oPmRoleId = new MongoId($oPmRoleId);

				$arrPermission = $this->SelectMongoDB($arrCondition = array(
					'user_group_id'	   => $oUserGroupId,
					'pm_role_id'	   => $oPmRoleId,
					'deleted'	       => 0
				), $this->cltAdminRolePermissions);

				if(count($arrPermission) == 0){
					$arrPermission = $this->SelectMongoDB($arrCondition = array(
						'user_group_id'	   => $oDefaultGroup['_id'],
						'pm_role_id'	   => $oPmRoleId,
						'deleted'	       => 0
					), $this->cltAdminRolePermissions);
				}
				#pd($arrCondition);
				foreach($arrPermission as $oPm){
					if($oPm['permission_value'] == 1){
						$strPermissionId = (string)$oPm['permission_id'];
						if($oPm['type'] == RES_TYPE_PRODUCT_ROLE){
							$oRole = $this->SelectOneMongoDB(array('_id' => $oPm['permission_id'], 'deleted' => 0), CLT_ROLES);
							if($oRole){
								$oUserRightOnGroup['arrPermittedProductRole'][$strPermissionId] = $oRole;
							}
						} elseif($oPm['type'] == RES_TYPE_PN_ATTR){
							$oTMP = $this->SelectOneMongoDB(array('_id' => $oPm['permission_id'], 'deleted' => 0), $this->cltAdminPermissionName);
							if(!empty($oTMP)){
								$oUserRightOnGroup['arrRightOnGroup'][$strPermissionId] = strtolower($oTMP['name']);
							}
						} elseif($oPm['type'] == RES_TYPE_PN_ASSIGNABLE){
							$oTMP = $this->SelectOneMongoDB(array('_id' => $oPm['permission_id'], 'deleted' => 0), CLT_PM_ROLES);
							if(!empty($oTMP)){
								$oUserRightOnGroup['arrPMRoleAssignable'][$strPermissionId] = strtolower($oTMP['role_name']);
							}
						}
					}
				}
				$arrMemberProduct = $this->ListMemberProductOfUserGroup($oUserGroupId);
				foreach($arrMemberProduct as &$oProduct){
					if($nUserType == USERTYPE_SUPERADMIN){
						$oProduct['is_addable']   = 1;
						$oProduct['is_deletable'] = 1;
					} else {
						$oProduct['is_addable']   = 0;
						$oProduct['is_deletable'] = 0;

						if(in_array(ADD_PRODUCT, $oUserRightOnGroup['arrRightOnGroup'])){
							$oProduct['is_addable'] = 1;
						}
						if(in_array(DELETE_PRODUCT, $oUserRightOnGroup['arrRightOnGroup'])){
							$oProduct['is_deletable'] = 1;
						}
					}
				}
				$oUserRightOnGroup['arrMemberUser']    = $this->ListMemberUserOfUserGroup($oUserGroupId);
				$oUserRightOnGroup['arrMemberProduct'] = $arrMemberProduct;
			}
		}
		return $oUserRightOnGroup;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadUserProfile($strUsername=''){
		if(empty($strUsername)) $strUsername = trim($this->session->userdata('username'));
		$oDefaultGroup = $this->LoadDefaultGroup();
		$arrResult = array(
			'oDefaultGroup'               => $oDefaultGroup,
			'oPMRoleForOwnUserGroup'      => $this->session->userdata('userNewGroupRole'),
			'isAllowCreateGroup'          => false,
			'isPermittedOnDefault'        => false,
			'defaultRole'                 => array(),
			'arrPermittedUserGroup'       => array(),
			'arrUserGroupSummary'         => array(),
			'arrPermittedUserGroupDetail' => array(),
			'arrAssignableProduct'        => array()
		);
		// Load admin roles of user ------------------------------------------------------------- //
		// User có thể thuộc nhiều user_group và có role khác nhau trên mỗi user_group
		$arrAdminUsersRole = $this->SelectMongoDB(array(
			'username'	=> new MongoRegex('/' . $strUsername . '/i'),
			'deleted'	=> 0
		), $this->cltAdminUsersRole);

		foreach($arrAdminUsersRole as $oAdminUserRole){
			$strUserGroupId = (string)$oAdminUserRole['user_group_id'];
			$oUserRightOnGroup = array(
						'arrPermittedProductRole' => array(), /* Danh sách ProductRole mà user có quyền */
						'arrRightOnGroup'         => array(), /* Danh sách quyền (vd: Add/Remove User trong group) mà user có thể action trên group */
						'arrPMRoleAssignable'     => array(), /* Danh sách AdminRole mà user có thể assign/remove cho user khác */
						'arrMemberProduct'        => array(),
						'arrMemberUser'           => array()
			);

			$arrPermission = $this->SelectMongoDB($arrCondition = array(
				'user_group_id'	   => $oAdminUserRole['user_group_id'],
				'pm_role_id'	   => $oAdminUserRole['pm_role_id'],
				'deleted'	       => 0
			), $this->cltAdminRolePermissions);
			if(count($arrPermission) == 0){
				$arrPermission = $this->SelectMongoDB($arrCondition = array(
					'user_group_id'	   => $oDefaultGroup['_id'],
					'pm_role_id'	   => $oAdminUserRole['pm_role_id'],
					'deleted'	       => 0
				), $this->cltAdminRolePermissions);
			}

			foreach($arrPermission as $oPm){
				if($oPm['permission_value'] == 1){
					$strPermissionId = (string)$oPm['permission_id'];
					if($oPm['type'] == RES_TYPE_PRODUCT_ROLE){
						$oRole = $this->SelectOneMongoDB(array('_id' => $oPm['permission_id'], 'deleted' => 0), CLT_ROLES);
						if($oRole){
							$oUserRightOnGroup['arrPermittedProductRole'][$strPermissionId] = $oRole;
						}
					} elseif($oPm['type'] == RES_TYPE_PN_ATTR){
						$oTMP = $this->SelectOneMongoDB(array('_id' => $oPm['permission_id'], 'deleted' => 0), $this->cltAdminPermissionName);
						if(!empty($oTMP)){
							$oUserRightOnGroup['arrRightOnGroup'][$strPermissionId] = strtolower($oTMP['name']);
						}
					} elseif($oPm['type'] == RES_TYPE_PN_ASSIGNABLE){
						$oTMP = $this->SelectOneMongoDB(array('_id' => $oPm['permission_id'], 'deleted' => 0), CLT_PM_ROLES);
						if(!empty($oTMP)){
							$oUserRightOnGroup['arrPMRoleAssignable'][$strPermissionId] = strtolower($oTMP['role_name']);
						}
					}
				}
			}
			$oGroup = $this->SelectOneMongoDB(array('_id' => $oAdminUserRole['user_group_id'], 'deleted' => 0), CLT_USER_GROUPS);
			$arrResult['arrPermittedUserGroup'][$strUserGroupId] = $oGroup;
			if($oGroup['is_default'] == 1){
				$arrResult['isPermittedOnDefault'] = true;
				$arrResult['defaultRole']['group'] = $oGroup;
				$arrResult['defaultRole']['role']  = $this->SelectOneMongoDB(array('deleted' => 0, '_id' => $oGroup['default_product_role']), CLT_ROLES);
			}
			#pd($oUserRightOnGroup['arrProduct']);
			$oUserRightOnGroup['arrMemberProduct'] = $this->ListMemberProductOfUserGroup($oAdminUserRole['user_group_id']);
			#p($oAdminUserRole['user_group_id']);
			#p($oUserRightOnGroup['arrProduct']);
			foreach($oUserRightOnGroup['arrProduct'] as $oProduct){
				$strProductKey = (string)$oProduct[PRODUCT_KEY];
				if(!array_key_exists($strProductKey, $arrResult['arrAssignableProduct'])){
					$oProductSummary = array('product_key' => $strProductKey, 'user_group_id'=>$strUserGroupId, 'name' => $oProduct['alias'], 'is_addable' => 0, 'is_deletable' => 0);
					if(in_array(ADD_PRODUCT, $oUserRightOnGroup['arrRightOnGroup'])){
						$oProductSummary['is_addable'] = 1;
					}
					if(in_array(DELETE_PRODUCT, $oUserRightOnGroup['arrRightOnGroup'])){
						$oProductSummary['is_deletable'] = 1;
					}
					$arrResult['arrAssignableProduct'][$strProductKey] = $oProductSummary;
				}
			}
			#pd($oUserRightOnGroup['arrProduct']);
			$arrResult['arrPermittedUserGroupDetail'][$strUserGroupId] = $oUserRightOnGroup;
			$arrResult['arrUserGroupSummary'][] = array('id' => $strUserGroupId, 'group_name' => $oGroup['group_name']);

			#pd($arrPermission);
		}
		#pd($arrResult);
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadProductByObjectId($oObjId){
		return $this->cmdbv2_model->LoadProductByObjectId($oObjId);
	}
	// ------------------------------------------------------------------------------------------ //
	function ListRolePermissionsFilter($strPermissionId, $oUserGroupId=null, $oRoleId=null) {
		$arrResult = array();
		$arrCondition = array('deleted' => 0, 'permission_value' => 1);
		if(!empty($strPermissionId)) {
			$arrCondition['permission_id'] = new MongoId($strPermissionId);
		}
		if(!empty($oUserGroupId)) {
			$arrCondition['user_group_id'] = $oUserGroupId;
		}
		if(!empty($oRoleId)) {
			$arrCondition['role_id'] = $oRoleId;
		}
		$arrResult = $this->SelectMongoDB($arrCondition, $this->cltRolePermissions);
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadUserGroup($oObjId){
		if(!empty($oObjId)){
			if(!is_object($oObjId)){
				$oObjId = new MongoId($oObjId);
			}
			$arrCondition = array(
				"deleted" => 0,
				"_id"     => $oObjId
			);
			return $this->SelectOneMongoDB($arrCondition, CLT_USER_GROUPS);
		}
		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	public function LoadUserByUserName($strUserName){
		$arrCondition = array(
			'username' => new MongoRegex('/^' . preg_quote(trim($strUserName)) . '$/i'),
			'deleted' => 0
		);

		return $this->SelectOneMongoDB($arrCondition, CLT_USERS);
	}
	// ------------------------------------------------------------------------------------------ //
	function ListMemberProductOfUserGroup($oUserGroupId){
		$arrResult = array();
		$arrTmpCond = array(
			'user_group_id' => $oUserGroupId,
			'deleted'       => 0
		);
		$arrTMP = $this->SelectMongoDB($arrTmpCond, CLT_USER_GROUPS_PRODUCTS);
		#pd($arrTMP);
		foreach($arrTMP as $oTMPProduct){
			$oProduct = $this->cmdbv2_model->LoadProductByProductKey($oTMPProduct['product_key']);
			if($oProduct){
				$arrResult[(string)$oProduct['_id']] = $oProduct;
			}
		}
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function ListMemberUserOfUserGroup($oUserGroupId){
		$arrResult = array();
		$arrTmpCond = array(
			'user_group_id' => $oUserGroupId,
			'deleted' => 0
		);
		$arrTMP = $this->SelectMongoDB($arrTmpCond, CLT_USER_GROUPS_USERS);
		#pd($arrTMP);
		foreach($arrTMP as $oTMPUser){
			$oUsr = $this->LoadUserByUserName($oTMPUser['username']);
			if($oUsr){
				$arrResult[(string)$oUsr['username']] = $oUsr;
			}
		}
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
}