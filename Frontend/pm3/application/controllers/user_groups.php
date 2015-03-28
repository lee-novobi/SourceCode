<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';

class User_groups extends Base_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('user_groups_model');
		$this->load->model('users_model');
	}
	// ------------------------------------------------------------------------------------------ //
	public function index(){

		$this->add();
	}
	// ------------------------------------------------------------------------------------------ //
	public function add(){
		if($this->oUserProfile['isAllowCreateGroup']){
			$oUserGroup       = array();
			$arrDepartment    = $this->cmdbv2_model->ListDepartment();
			$arrMemberProduct = array();
			$arrMemberUser    = array();
			$arrAllUser       = $this->users_model->ListUser();

			$this->loadview('Groups/edit',
				array(
					'arrDepartment' => $arrDepartment,
					'oUserGroup'    => $oUserGroup,
					'action'        => 'add',
					'arrMemberProduct' => $arrMemberProduct,
					'arrMemberUser' => $arrMemberUser,
					'arrAllUser'    => $arrAllUser
				)
			);
		} else {
			$this->loadview('error/access_denied');
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function edit(){
		$strUserGroupId = $_REQUEST['ugid'];
		if(array_key_exists($strUserGroupId, $this->oUserProfile['arrPermittedUserGroup'])){
			$oUserGroup = $this->user_groups_model->LoadUserGroup($strUserGroupId);
			if(!empty($oUserGroup)){
				$arrDepartment    = $this->cmdbv2_model->ListDepartment();
				$arrMemberProduct = $this->user_groups_model->ListMemberProductOfUserGroup($oUserGroup['_id']);
				$arrMemberUser    = $this->user_groups_model->ListMemberUserOfUserGroup($oUserGroup['_id']);
				$arrAllUser       = $this->users_model->ListUser();

				$this->loadview('Groups/edit',
					array(
						'arrDepartment' => $arrDepartment,
						'oUserGroup'    => $oUserGroup,
						'action'        => 'edit',
						'arrMemberProduct' => $arrMemberProduct,
						'arrMemberUser' => $arrMemberUser,
						'arrAllUser'    => $arrAllUser
					)
				);
			} else {
				$this->loadview('error/access_denied');
			}
		} else {
			$this->loadview('error/access_denied');
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function save_list_product($strCalledFrom=''){
		$arrSubmittedProduct = $this->input->post('product_list');
		if(empty($arrSubmittedProduct)) $arrSubmittedProduct = array();
		$strUserGroupId      = $this->input->post('user_group_id');

		$oUserGroup          = $this->user_groups_model->LoadUserGroup($strUserGroupId);
		if(!@empty($oUserGroup)){
			$arrMemberProduct = $this->user_groups_model->ListMemberProductOfUserGroup($oUserGroup['_id']);
			// Find new product ----------------------------------------------------------------- //
			$arrNewMember = array();
			foreach($arrSubmittedProduct as &$oProd){
				if(!array_key_exists($oProd['id'], $arrMemberProduct)){
					$arrNewMember[] = &$oProd;
				}
			}

			// Find removed product ------------------------------------------------------------- //
			$arrRemovedMember = array();
			$arrTmp = array();
			foreach($arrSubmittedProduct as &$oProd){
				$arrTmp[$oProd['id']] = &$oProd;
			}
			foreach($arrMemberProduct as $strKey=>$oValue){
				if(!array_key_exists($strKey, $arrTmp)){
					$arrRemovedMember[] = $oValue;
				}
			}
			unset($arrTmp);

			// Check permission ----------------------------------------------------------------- //
			if((count($arrNewMember) > 0 && !$this->CanAddProductToGroup($oUserGroup['_id']))||
			(count($arrRemovedMember)>0 && !$this->CanRemoveProductFromGroup($oUserGroup['_id']))){
				if(empty($strCalledFrom))
					$this->loadview_simple_ajax($this->str_json_permission_denied);
				else
					return $this->str_json_permission_denied;
			}

			// Save change ---------------------------------------------------------------------- //
			foreach($arrNewMember as $oProd){
				$this->user_groups_model->AddProductToGroup($oProd['id'], $oUserGroup['_id']);
			}
			foreach($arrRemovedMember as $oProd){
				$this->user_groups_model->RemoveProductFromGroup($oProd['_id'], $oUserGroup['_id']);
			}

			$strReport = sprintf('%s Product(s) added and %s Product(s) removed.',
				count($arrNewMember),
				count($arrRemovedMember)
			);
			if(empty($strCalledFrom)){
				$this->session->set_flashdata('msg','Update successful');
				$this->session->set_flashdata('type_msg', 'success');
				$this->loadview_simple_ajax(
					sprintf($this->str_json_success,
						sprintf(', "report":"%s"', $strReport)
					)
				);
			} else
				return sprintf($this->str_json_success, sprintf(', "report":"%s"', $strReport));
		} else {
			if(empty($strCalledFrom))
				$this->loadview_simple_ajax($this->str_json_bad_request);
			else
				return $this->str_json_bad_request;
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function save_list_user($strCalledFrom=''){
		$arrSubmittedUser = $this->input->post('user_list');
		if(empty($arrSubmittedUser)) $arrSubmittedUser = array();
		$strUserGroupId   = $this->input->post('user_group_id');
		$oUserGroup       = $this->user_groups_model->LoadUserGroup($strUserGroupId);
		if(!@empty($oUserGroup)){
			$arrMemberUser    = $this->user_groups_model->ListMemberUserOfUserGroup($oUserGroup['_id']);
			// Find new user -------------------------------------------------------------------- //
			$arrNewMember = array();
			foreach($arrSubmittedUser as &$oUsr){
				if(!array_key_exists($oUsr['id'/*username*/], $arrMemberUser)){
					$arrNewMember[] = &$oUsr;
				}
			}

			// Find removed user ---------------------------------------------------------------- //
			$arrRemovedMember = array();
			$arrTmp = array();
			foreach($arrSubmittedUser as &$oUsr){
				$arrTmp[$oUsr['id']] = &$oUsr;
			}
			foreach($arrMemberUser as $strKey=>$oValue){
				if(!array_key_exists($strKey, $arrTmp)){
					$arrRemovedMember[] = $oValue;
				}
			}
			unset($arrTmp);

			// Check permission ----------------------------------------------------------------- //
			if((count($arrNewMember) > 0 && !$this->CanAddUserToGroup($oUserGroup['_id']))||
			(count($arrRemovedMember)>0 && !$this->CanRemoveUserFromGroup($oUserGroup['_id']))){
				if(empty($strCalledFrom))
					$this->loadview_simple_ajax($this->str_json_permission_denied);
				else
					return $this->str_json_permission_denied;
			}

			// Save change ---------------------------------------------------------------------- //
			foreach($arrNewMember as $oUsr){
				$this->user_groups_model->AddUserToGroup($oUsr['id']/*username*/, $oUserGroup['_id']);
				if((string)$oUsr['_id'] != $this->session->userdata('userId')){
					$this->user_groups_model->SetRole($oUserGroup['_id'], $oUsr['id'], $this->oUserProfile['oDefaultGroup']['default_pm_role']);
				}
			}
			foreach($arrRemovedMember as $oUsr){
				$this->user_groups_model->RemoveUserFromGroup($oUsr['username'], $oUserGroup['_id']);
			}
			if(empty($strCalledFrom)){
				$this->session->set_flashdata('msg','Update successful');
				$this->session->set_flashdata('type_msg', 'success');

				$this->loadview_simple_ajax(
					sprintf($this->str_json_success,
						sprintf(', "report":"%s User(s) added and %s User(s) removed."',
							count($arrNewMember),
							count($arrRemovedMember)
						)
					)
				);
			} else {
				/*return array(
					'code' => 1,
					'msg'  => 'Update successful',
					'report' => sprintf("%s User(s) added and %s User(s) removed.",
						count($arrNewMember),
						count($arrRemovedMember)
					)
				);*/
				return sprintf($this->str_json_success,
					sprintf(', "report":"%s User(s) added and %s User(s) removed."',
						count($arrNewMember),
						count($arrRemovedMember)
					)
				);
			}
		} else {
			if(empty($strCalledFrom))
				$this->loadview_simple_ajax($this->str_json_bad_request);
			else
				return $this->str_json_bad_request;
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function save_department($strCalledFrom=''){
		global $arrDefined;
		$strDeptKey     = $this->input->post('department_key');
		$strUserGroupId = $this->input->post('user_group_id');

		$bResult = $this->user_groups_model->UpdateUserGroup(
			$strUserGroupId,
			array('department_key' => MongoSaveObj(DEPARTMENT_KEY, $strDeptKey))
		);
		if(empty($strCalledFrom))
			$this->loadview_simple_ajax(sprintf($this->str_json_success, ''));
		else
			return sprintf($this->str_json_success, '');
	}
	// ------------------------------------------------------------------------------------------ //
	public function save_group($strCalledFrom=''){
		$this->save_department(true);
		$strJsonUserResult    = $this->save_list_user(true);
		$strJsonProductResult = $this->save_list_product(true);
		$oSaveUserResult      = json_decode($strJsonUserResult, true);
		$oSaveProductResult   = json_decode($strJsonProductResult, true);
#p($oSaveUserResult);
#pd($oSaveProductResult);
		$strTotalReport = sprintf(
			'[%s][%s]',
			@empty($oSaveUserResult['report'])?$oSaveUserResult['msg']:$oSaveUserResult['report'],
			@empty($oSaveProductResult['report'])?$oSaveProductResult['msg']:$oSaveProductResult['report']
		);
		#p($strJsonUserResult);
		#vd($oSaveUserResult);
		if($oSaveUserResult['code'] && $oSaveProductResult['code']){
			if(empty($strCalledFrom)){

				if($this->isAjaxRequest){
					$this->loadview_simple_ajax(
						sprintf($this->str_json_success,
							sprintf('"report":"%s"', $strTotalReport)
						)
					);
				}
				$this->session->set_flashdata('msg','Update successful. ' . $strTotalReport);
				$this->session->set_flashdata('type_msg', 'success');
			} else {
				return array(
					'code'   => 1,
					'msg'    => 'Update successful. ' . $strTotalReport,
					'report' => $strTotalReport
				);
			}
		} else {
			$oTotalResult = array(
				'code' => 0,
				'msg'  => $strTotalReport,
				'user_result' => array(
					'code' => $oSaveUserResult['code'],
					'msg'  => $oSaveUserResult['msg']
				),
				'product_result' => array(
					'code' => $oSaveProductResult['code'],
					'msg'  => $oSaveProductResult['msg']
				)
			);
			if(empty($strCalledFrom)){
				$this->session->set_flashdata('msg','Update failed. ' . $strTotalReport);
				$this->session->set_flashdata('type_msg', 'error');
				if($this->isAjaxRequest){
					$this->loadview_simple_ajax(json_encode($oTotalResult));
				}
			} else {
				return $oTotalResult;
			}
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function insert_group(){
		$strDeptKey   = trim($_REQUEST['department_key']);
		$strGroupName = trim($_REQUEST['group_name']);
		if(!empty($strGroupName)){
			$oOldGroup = $this->user_groups_model->LoadGroupByGroupName($strGroupName);
			if(!$oOldGroup){
				$oNewGroupId = $this->user_groups_model->InsertGroup($strGroupName, $strDeptKey);
				if(!empty($oNewGroupId)){
					$oPmRoleId = $this->user_groups_model->SetRole($oNewGroupId);
					if(!empty($oPmRoleId)){
						$this->AddNewGroupToUserProfile($oNewGroupId, $oPmRoleId);

						$_POST['user_group_id'] = $oNewGroupId;
						$oRs = $this->save_group(true);
						if($oRs['code'] == 1){
							$this->session->set_flashdata('msg', $oRs['msg']);
							$this->session->set_flashdata('type_msg', 'success');
							$this->loadview_simple_ajax(json_encode($oRs));
						} else {
							$this->loadview_simple_ajax(json_encode($oRs));
						}
					} else {
						$this->loadview_simple_ajax($this->str_json_new_pm_user_role_fail);
					}
				} else {
					$this->loadview_simple_ajax($this->str_json_new_group_fail);
				}
			} else {
				$this->loadview_simple_ajax($this->str_json_group_already_exists);
			}
		}
	}
	// ------------------------------------------------------------------------------------------ //
	private function AddNewGroupToUserProfile($oGroupId, $oPmRoleId){
		$strUsername  = $this->session->userdata('username');
		$oUserProfile = $this->oUserProfile;

		$oGroup       = $this->user_groups_model->LoadUserGroup($oGroupId);
		$oPermission  = $this->user_groups_model->GetPermissionDetail($strUsername, $oGroupId, $this->oUserProfile['oPMRoleForOwnUserGroup']);

		$oUserProfile['arrPermittedUserGroup'][(string)$oGroupId] = $oGroup;
		$oUserProfile['arrUserGroupSummary'][] = array(
			'id' => (string)$oGroupId,
			'group_name' => $oGroup['group_name']
		);
		$oUserProfile['arrPermittedUserGroupDetail'][(string)$oGroupId] = $oPermission;
		if($this->session->userdata('usertype') != USERTYPE_SUPERADMIN){
			foreach($oPermission['arrMemberProduct'] as &$oProduct){
				if(!array_key_exists((string)$oProduct['_id'], $oUserProfile['arrPermittedUserGroupDetail']['arrAssignableProduct'])){
					$oUserProfile['arrPermittedUserGroupDetail']['arrAssignableProduct'][(string)$oProduct['_id']] = &$oProduct;
				}
			}
		}
		$this->oUserProfile = $oUserProfile;
		$this->session->set_userdata('oUserProfile', $this->oUserProfile);
	}
	// ------------------------------------------------------------------------------------------ //
}