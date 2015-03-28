<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';

class Pm_permission extends Base_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('pm_admin_model', 'admin_model');
	}
	// ------------------------------------------------------------------------------------------ //
	public function user_profile(){
		pd($this->oUserProfile);
	}
	// ------------------------------------------------------------------------------------------ //
	public function super_user_profile(){
		pd($this->model->LoadUserProfileSuperAdmin());
	}
	// ------------------------------------------------------------------------------------------ //
	public function index(){
		$this->list_data();
	}
	// ------------------------------------------------------------------------------------------ //
	public function list_data(){
		$strGroupId = @$_REQUEST['gid'];

		$arrUserGroups = $this->model->ListUserGroups();

		$this->loadview('Admin/index',
            array(
            	'arrUserGroups' => $arrUserGroups,
            	'strGroupId'	=> $strGroupId
            )
        );
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_load_roles_permission(){
		if($this->session->userdata('usertype')==USERTYPE_SUPERADMIN){
			$strGroupId = @$_REQUEST['gid'];

			list($arrAdminPermissionName, $arrAdminRole, $arrProductRole) = $this->admin_model->ListAdminPermissionName();
			$arrRolesPermissions = $this->admin_model->LoadAdminRolePermission($strGroupId);
			$arrRole             = $this->admin_model->ListAdminRole();
			$arrPermissionName   = $this->GroupingPermissionName($arrAdminPermissionName, $arrAdminRole, $arrProductRole);

			$arrSelectedGroupPermission = $this->admin_model->LoadAdminUserGroupPermission(array('user_group_id' => $strGroupId));
	#pd($arrSelectedGroupPermission);
			$this->loadview('Admin/ajax_roles_permission',
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
		if($this->session->userdata('usertype')==USERTYPE_SUPERADMIN){
			$strJson        = $_POST['data'];
			$strUserGroupId = $_POST['user_group_id'];

			try{
				$arrRolePermission = json_decode($strJson);
				$this->admin_model->SaveAdminRolesPermission($strUserGroupId, $arrRolePermission);

				$this->session->set_flashdata('msg','Update successful !!!');
				$this->session->set_flashdata('type_msg', 'success');
			} catch(Exception $ex){
				$strErrorMsg = $ex->getMessage();
				$this->session->set_flashdata('msg','Update failed' . empty($strErrorMsg)?'':(': '.$strErrorMsg));
				$this->session->set_flashdata('type_msg', 'error');
			}
		} else {
			$this->session->set_flashdata('msg','Update failed. Permission denied !');
			$this->session->set_flashdata('type_msg', 'error');
		}
	}
	// ------------------------------------------------------------------------------------------ //
	private function GroupingPermissionName($arrAdminPermissionName, $arrAdminRole, $arrProductRole){
		$strGroup1 = 'Permission On User & Group Config';
		$strGroup2 = 'Can Assign & Modify Permission Of ProductRole';
#pd($arrAdminRole);
		$arrResult = array($strGroup1 => array(), $strGroup2 => array());

		foreach($arrAdminPermissionName as $oPermissionName){
			$arrResult[$strGroup1][] = array('type' => RES_TYPE_PN_ATTR, '_id' => (string)$oPermissionName['_id'], 'name' => $oPermissionName['name']);
		}
		foreach($arrAdminRole as $oPermissionName){
			$arrResult[$strGroup1][] = array('type' => RES_TYPE_PN_ASSIGNABLE, '_id' => (string)$oPermissionName['_id'], 'name' => 'Assign Role ' . $oPermissionName['role_name']);
		}
		foreach($arrProductRole as $oPermissionName){
			$arrResult[$strGroup2][] = array('type' => RES_TYPE_PRODUCT_ROLE, '_id' => (string)$oPermissionName['_id'], 'name' => $oPermissionName['role_name']);
		}
		return $arrResult;
	}
}