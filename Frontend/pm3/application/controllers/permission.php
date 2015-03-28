<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';

class Permission extends Base_Controller {
	public function __construct(){
		parent::__construct();
	}
	// ------------------------------------------------------------------------------------------ //
	public function index(){
		$this->list_data();
	}
	// ------------------------------------------------------------------------------------------ //
	public function list_data(){
		$strGroupId = @$_REQUEST['gid'];
		$arrCondition = array('_id' => array('$in' => array()));
		foreach($this->oUserProfile['arrPermittedUserGroup'] as $oGroup){
			$arrCondition['_id']['$in'][] = $oGroup['_id'];
		}
		#pd($this->oUserProfile['arrPermittedUserGroup']);
		$arrUserGroups = $this->model->ListUserGroups($arrCondition);
		#pd($arrUserGroups);
		$this->loadview('Permission/index',
            array(
            	'arrUserGroups' => $arrUserGroups,
            	'strGroupId'	=> $strGroupId
            )
        );
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_load_roles_permission(){
		$strGroupId = @$_REQUEST['gid'];
		if(array_key_exists($strGroupId, $this->oUserProfile['arrPermittedUserGroupDetail'])){
			$arrPermissionName = $this->model->ListPermissionName();
			$arrPermissionNameGroups = $this->model->ListPermissionNameGroups();
			$arrRolesPermissions = $this->model->LoadRolePermission($strGroupId);

			$arrCondition = array('_id' => array('$in' => array()));
			foreach($this->oUserProfile['arrPermittedUserGroupDetail'][$strGroupId]['arrPermittedProductRole'] as $oRole){
				$arrCondition['_id']['$in'][] = $oRole['_id'];
			}

			$arrRole = $this->model->ListRole($arrCondition);
			#pd($arrRole);

			$arrPermissionNameGroups = $this->IndexPermissionNameGroups($arrPermissionNameGroups);
			$arrPermissionName = $this->GroupingPermissionName($arrPermissionName, $arrPermissionNameGroups);

			$arrSelectedGroupPermission = $this->model->LoadUserGroupPermission(array('user_group_id' => $strGroupId));

			$this->loadview('Permission/ajax_roles_permission',
	            array(
	            	'arrPermission' => $arrPermissionName,
	            	'arrRole'		=> $arrRole,
	            	'arrSelectedGroupPermission' => $arrSelectedGroupPermission
	            ), 'layout_ajax'
	        );
		} else {
			$this->loadview('error/access_denied',
	            array(), 'layout_ajax'
	        );
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function save_list(){
		$strJson        = $_POST['data'];
		$strUserGroupId = $_POST['user_group_id'];
		if(array_key_exists($strUserGroupId, $this->oUserProfile['arrPermittedUserGroupDetail'])){
			try{
				$arrRolePermission = json_decode($strJson);
				$this->model->SaveRolesPermission($strUserGroupId, $arrRolePermission);

				$this->session->set_flashdata('msg','Update successful !!!');
				$this->session->set_flashdata('type_msg', 'success');
			} catch(Exception $ex){
				$strErrorMsg = $ex->getMessage();
				$this->session->set_flashdata('msg','Update failed' . empty($strErrorMsg)?'':(': '.$strErrorMsg));
				$this->session->set_flashdata('type_msg', 'error');
			}
		} else {
			$this->loadview('error/access_denied',
	            array(), 'layout_ajax'
	        );
		}
	}
	// ------------------------------------------------------------------------------------------ //
	private function IndexPermissionNameGroups($arrPermissionNameGroups){
		$arrResult = array();
		if(!empty($arrPermissionNameGroups)){
			foreach($arrPermissionNameGroups as $oPermissionNameGroup){
				$arrResult[$oPermissionNameGroup['ci_type']] = $oPermissionNameGroup;
			}
		}
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	private function GroupingPermissionName($arrPermissionName, $arrPermissionNameGroups){
		$strGroupUnknown = 'Group Unknown';

		$arrResult = array();
		$arrTMP = array();

		if(is_array($arrPermissionNameGroups)){
			foreach($arrPermissionNameGroups as $oGroup){
				$strCIName = $oGroup['group_name'];
				$arrTMP[$strCIName] = array();
			}
		}
		$arrTMP[$strGroupUnknown] = array();

		if(!empty($arrPermissionName)){
			foreach($arrPermissionName as $oPermissionName){
				$strCIType = $oPermissionName['ci_type'];
				$strCIName = $strGroupUnknown;
				if(is_array($arrPermissionNameGroups) && array_key_exists($strCIType, $arrPermissionNameGroups)){
					$strCIName = $arrPermissionNameGroups[$strCIType]['group_name'];
				}
				$arrTMP[$strCIName][] = $oPermissionName;
			}

			foreach($arrTMP as $strGroup=>$arrPer){
				if(count($arrPer) > 0){
					$arrResult[$strGroup] = $arrPer;
				}
			}
		}
		return $arrResult;
	}
}