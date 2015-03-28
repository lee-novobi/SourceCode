<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';

class Users extends Base_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('users_model');
		$this->load->model('user_groups_model');
		$this->load->model('cmdbv2_model', 'cmdb_model');
		$this->load->model('pm_admin_model', 'pm3ad_model');
	}
	// ------------------------------------------------------------------------------------------ //
	public function index(){
		$this->list_data();
	}
	// ------------------------------------------------------------------------------------------ //
	public function list_data() {
		// pd($this->session->userdata('username'));
		$arrRolePermissionsFilter = $arrPermittedUGIn = $arrUsers = array();
		$arrCondition = $this->GetListFilter();
		$arrDepartment = $this->cmdb_model->ListDepartment();
		$arrProduct = $this->cmdb_model->ListProduct();
		$oDefaultUserGroup = $this->users_model->LoadDefaultUserGroup();
		// $arrUserGroupList = $this->users_model->ListUserGroups();
		$arrUserGroupList = $this->oUserProfile['arrPermittedUserGroup'];
		#if(empty($arrUserGroupList) || array_key_exists((string)$oDefaultUserGroup['_id'], $arrUserGroupList)) {
		#	$arrUserGroupList = $this->users_model->ListUserGroups();
		#}
		$arrRoles = $this->users_model->ListRole();
		$arrPermissionName = $this->users_model->ListPermissionName();
		$arrPermissionNameGroups = $this->users_model->ListPermissionNameGroups();
		$arrPermissionNameGroups = $this->IndexPermissionNameGroups($arrPermissionNameGroups);
		$arrPermissionName = $this->GroupingPermissionName($arrPermissionName, $arrPermissionNameGroups);
		if(isset($arrCondition['permission'])) {
			$arrRolePermissionsFilter = $this->users_model->ListRolePermissionsFilter(trim($arrCondition['permission']));
		}
		// pd($arrRolePermissionsFilter);

		$arrURPagination = array();
		$arrURPagination = $this->GetPaginationRequest('limit_user_role', 'page_user_role');

		foreach($arrUserGroupList as $oPermittedUG) {
			$arrPermittedUGIn[] = $oPermittedUG['_id'];
		}
		$arrPmUsersRole = $this->pm3ad_model->ListPMUsersRole($arrCondition, $arrPermittedUGIn/* , $arrURPagination */);
		#pd($arrPmUsersRole);
		$arrPmRoles = $this->pm3ad_model->ListAdminRole();
		#pd($arrPmRoles);
		$arrUserGroups = $this->users_model->ListUserGroups();
		$arrUsers = $this->OptimizeUserRoleInfoResult($arrPmUsersRole);
		#pd($arrUsers);
		if($this->oUserProfile['isPermittedOnDefault']){
			$arrAllUser = $this->users_model->ListUser();
			foreach($arrAllUser as $oUsr){
				if(!array_key_exists($oUsr['username'], $arrUsers)){
					$arrUsers[$oUsr['username']] = array('user_group_id' => array(), 'pm_role_id' => array(), 'username' => $oUsr['username']);
				}
			}
		}
		$this->FillDefaultRole($arrUsers);
		#pd($arrUsers);
		foreach($arrUsers as $strIdentifier=>&$oUser) {
			/* get role tool of each User */
			foreach($oUser['pm_role_id'] as $strUserGroupId=>$oRole) {
				if(!empty($oRole) && array_key_exists((string)$oRole, $arrPmRoles)) {
					$arrUsers[$strIdentifier]['pm_role_name'][$strUserGroupId] = $arrPmRoles[(string)$oRole]['role_name'];
				} else {
					$arrUsers[$strIdentifier]['pm_role_name'][$strUserGroupId] = "Member";
				}
			}

			/* get user group of each User */
			foreach($oUser['user_group_id'] as $strUserGroupId=>$oUGroup) {
				if(array_key_exists((string)$oUGroup, $arrUserGroups)) {
					$arrUsers[$strIdentifier]['user_group'][$strUserGroupId] = $arrUserGroups[$strUserGroupId]['group_name'];
				}
			}

			$oProductKey = $oRoleId = $oPermission = null;
			if(isset($arrCondition['product'])) 	$oProductKey = $arrCondition['product'];
			if(isset($arrCondition['role'])) 		$oRoleId    = new MongoId($arrCondition['role']);

			/* get user_role_product */
			$oUser['user_role_product'] = array();

			$arrProdOwn = $this->users_model->ListProductInUserGroupOwnedByUser($oUser['username'], $oProductKey, $oRoleId);
#pd($oProdOwner);
			foreach($arrProdOwn as &$oProdOwn){
				$oProduct = $this->cmdb_model->LoadProductByProductKey($oProdOwn['product_key']);
				if(!empty($oProduct)) {
					$oProdOwn['product']         = $oProduct;
					$oProdOwn['user_group_name'] = @$arrUserGroups[(string)$oProdOwn['user_group_id']]['group_name'];
				}
				$oUser['user_role_product'][] = $oProdOwn;
			}
			#}

			/* get department info of each User */
			$oDept = $this->LoadDepartment4User($strIdentifier);
			if(!empty($oDept))  {
				$oUser['department'] = $oDept['alias'];
				$oUser['department_key'] = (string)$oDept[DEPARTMENT_KEY];
			}
			/* user not in product filter, remove it */
			if(!isset($oUser['user_role_product']) && isset($arrCondition['product']))
				unset($arrUsers[$strIdentifier]);

			/* user not in Role filter, remove it */
			if(!isset($oUser['user_role_product']) && isset($arrCondition['role']))
				unset($arrUsers[$strIdentifier]);

			/* department filter */
			if(isset($arrCondition['department']) && isset($oUser['department_key'])) {
				if($oUser['department_key'] !== $arrCondition['department']) {
					unset($arrUsers[$strIdentifier]);
				}
			}

			/* filter by permission */
			/* if user has role of product, you'll get these users filtered by permission */
			if(isset($arrCondition['permission'])) {
				if(isset($oUser['user_role_product'])) {
					if(array_key_exists(0, $oUser['user_role_product'])) {
						foreach($oUser['user_role_product'] as $idx=>$oURP) {
							$oRolePermissionFilter = $this->users_model->ListRolePermissionsFilter($arrCondition['permission'], $oURP['user_group_id'], $oURP['role_id']);
							if(empty($oRolePermissionFilter)) {
								unset($oUser['user_role_product'][$idx]);
							}
						}

					} else {
						$oRolePermissionFilter = $this->users_model->ListRolePermissionsFilter($arrCondition['permission'], $oUser['user_role_product']['user_group_id'], $oUser['user_role_product']['role_id']);
						if(empty($oRolePermissionFilter)) {
							unset($oUser['user_role_product']);
						}
					}
					if(empty($oUser['user_role_product'])) {
						unset($arrUsers[$strIdentifier]);
					}
				} else {
					unset($arrUsers[$strIdentifier]);
				}
			}
		}

		$arrCountTotal = $this->pm3ad_model->ListPMUsersRole($arrCondition, $arrPermittedUGIn);
		$arrCountTotalOpt = $this->OptimizeUserRoleInfoResult($arrCountTotal);
		foreach($arrCountTotalOpt as $strUser=>&$oUser) {
			$arrCountTotalOpt = $this->FilterUserRoleForEachUser($arrCondition, $oUser, $arrCountTotalOpt, $strUser);
		}
		$iTotal = count($arrCountTotalOpt);
		// p($iTotal);
		// pd($arrUsers);
		$strUserRoleView = $this->load->view('Users/ajax_user_role', array('base_url' => $this->getBaseUrl(), 'arrUsers' => $arrUsers), true);
		$strQueryString = $this->ParseQueryString();
		$this->loadview('Users/index',
            array(
            	'arrDepartment'   => $arrDepartment,
            	'arrProduct'      => $arrProduct,
            	'arrUserGroups'   => $arrUserGroupList,
            	'arrRoles'        => $arrRoles,
            	'arrPermission'   => $arrPermissionName,
            	'strUserRoleView' => $strUserRoleView,
            	'strQueryString'  => $strQueryString,
            	'iTotal'          => $iTotal,
            	'iPageUR'         => $arrURPagination['page'],
            	'iPageSizeUR'     => $arrURPagination['limit']
            )
        );
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_load_roles_permission(){
		$strGroupId = @$_REQUEST['gid'];
		/*$strToken = md5(SECKEY.date('YmdHis'));
		$this->session->set_userdata('roles_permission_user_group_id', $strGroupId);
		$this->session->set_userdata('roles_permission_token', $strToken);*/

		$arrPermissionName = $this->model->ListPermissionName();
		$arrPermissionNameGroups = $this->model->ListPermissionNameGroups();
		$arrRolesPermissions = $this->model->LoadRolePermission($strGroupId);

		$arrCondition = array('_id' => array('$in' => $this->oUserProfile['arrPermittedUserGroupDetail'][$strGroupId]['arrPermittedProductRole']));
		$arrRole = $this->model->ListRole($arrCondition);

		$arrPermissionNameGroups = $this->IndexPermissionNameGroups($arrPermissionNameGroups);
		$arrPermissionName = $this->GroupingPermissionName($arrPermissionName, $arrPermissionNameGroups);

		$arrSelectedGroupPermission = $this->model->LoadUserGroupPermission(array('user_group_id' => $strGroupId));
#pd($arrSelectedGroupPermission);
		$this->loadview('Permission/ajax_roles_permission',
            array(
            	'arrPermission' => $arrPermissionName,
            	'arrRole'		=> $arrRole,
            	'arrSelectedGroupPermission' => $arrSelectedGroupPermission
            ), 'layout_ajax'
        );
	}
	// ------------------------------------------------------------------------------------------ //
	public function save_list(){
		$strJson        = $_POST['data'];
		$strUserGroupId = $_POST['user_group_id'];
		/*$strUserGroupId = $this->session->userdata('roles_permission_user_group_id');
		$strToken       = $this->session->userdata('roles_permission_token');
		$strPostToken   = $_POST['token'];*/

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
	}
	// ------------------------------------------------------------------------------------------ //
	public function add(){
		$isAllowAddUser = false;
		$oUserProfile = $this->session->userdata('oUserProfile');
		$arrDepartment = $this->cmdb_model->ListDepartment();

		foreach($this->oUserProfile['arrPermittedUserGroupDetail'] as $oGroup){
			if(in_array(ADD_USER, $oGroup['arrRightOnGroup'])){
				$isAllowAddUser = true;
				break;
			}
		}
		if($isAllowAddUser){
			$this->loadview('Users/edit',
	            array(
	            	'oUserProfile'          => $oUserProfile,
	            	'arrBelongToUserGroups'	=> array(),
	            	'arrProductOwn'			=> array(),
	            	'arrAssignableProduct'  => $this->oUserProfile['arrAssignableProduct'],
	            	'arrDepartment'			=> $arrDepartment,
	            	'action'				=> 'add'
	            )
	        );
		} else {
			$this->loadview('error/access_denied',
	            array()
	        );
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function edit(){
		$strUserName = $_REQUEST['uid'];
		$oUser = $this->users_model->LoadUserByUserName($strUserName);
		if($oUser){
			$arrCondition = array(
				'username' => new MongoRegex('/^' . trim($strUserName) . '$/i'),
				'deleted' => 0
			);
			foreach($this->oUserProfile['arrPermittedUserGroup'] as $oGroup){
				$arrCondition['user_group_id']['$in'][] = $oGroup['_id'];
			}

			$arrBelongToUserGroups = $this->users_model->ListBelongToUserGroups($arrCondition);
			if($this->oUserProfile['isPermittedOnDefault']){
				$arrBelongToUserGroups = array((string)$this->oUserProfile['defaultRole']['group']['_id'] => $this->oUserProfile['defaultRole']['group'] + array('editable' => 0, 'deletable_user' => 0, 'addable_user' => 0, 'deletable_product' => 0, 'addable_product' => 0, 'arrProductAssignable' => array(), 'pm_role' => array('_id' => '', 'role_name' => 'Member', 'assignable' => 0))) + $arrBelongToUserGroups;
				#$arrBelongToUserGroups[] =
			}
#pd($arrBelongToUserGroups);
			$oUserProfile  = $this->session->userdata('oUserProfile');
			$arrDepartment = $this->cmdb_model->ListDepartment();
			$arrProductOwn = $this->users_model->ListProductOwnedByUser($arrCondition);

			if(@$_REQUEST['debug']==1){
				p($arrProductOwn);
				p('------------------------------------------------------------------------------');
				pd($arrBelongToUserGroups);
			}

			$this->loadview('Users/edit',
	            array(
	            	'oUser'                 => $oUser,
	            	'arrBelongToUserGroups'	=> $arrBelongToUserGroups,
	            	'arrProductOwn'         => $arrProductOwn,
	            	'arrAssignableProduct'  => $this->oUserProfile['arrAssignableProduct'],
	            	'oUserProfile'          => $oUserProfile,
	            	'arrDepartment'			=> $arrDepartment,
	            	'action'				=> 'edit'
	            )
	        );
		} else {
			$this->loadview('error/access_denied',
	            array()
	        );
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function save_to_group(){
		$arrGroupAndRole = explode('|', @$_REQUEST['key']);
		$strUsername     = @$_REQUEST['username'];
		$strPmUserRoleId = @$_REQUEST['pm_user_role_id'];

		if(is_array($arrGroupAndRole) && count($arrGroupAndRole) == 2 && !empty($strUsername)){
			if(array_key_exists($arrGroupAndRole[0], $this->oUserProfile['arrPermittedUserGroupDetail'])
			&& array_key_exists($arrGroupAndRole[1], $this->oUserProfile['arrPermittedUserGroupDetail'][$arrGroupAndRole[0]]['arrPMRoleAssignable'])){
				if(!empty($strPmUserRoleId)){
					$oRs = $this->users_model->UpdateUserToGroup($strUsername, $strPmUserRoleId, $arrGroupAndRole[0], $arrGroupAndRole[1]);
					if($oRs !== FALSE)
						$this->loadview_simple_ajax(sprintf($this->str_json_success, ''));
					else
						$this->loadview_simple_ajax($this->str_json_db_error);
				} else {
					if(in_array(ADD_USER, $this->oUserProfile['arrPermittedUserGroupDetail'][$arrGroupAndRole[0]]['arrRightOnGroup'])){
						$oExitsted = $this->users_model->LoadPmUserRole($strUsername, $arrGroupAndRole[0]);
						if(!$oExitsted){
							$oRs = $this->users_model->InsertUserToGroup($strUsername, $arrGroupAndRole[0], $arrGroupAndRole[1]);
							if($oRs !== false)
								$this->loadview_simple_ajax(sprintf($this->str_json_success, ', "new_id": "' . $oRs . '"'));
							else
								$this->loadview_simple_ajax($this->str_json_db_error);
						} else {
							$this->loadview_simple_ajax($this->str_json_only_1_pm_role);
						}
					} else {
						$this->loadview_simple_ajax($this->str_json_permission_denied);
					}
				}
			} else {
				$this->loadview_simple_ajax($this->str_json_permission_denied);
			}
		} else {
			$this->loadview_simple_ajax($this->str_json_bad_request);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	// ------------------------------------------------------------------------------------------ //
	public function remove_from_group(){
		$arrGroupAndRole = explode('|', @$_REQUEST['key']);
		$strUsername     = @$_REQUEST['username'];
		$strPmUserRoleId = @$_REQUEST['pm_user_role_id'];

		if(is_array($arrGroupAndRole) && count($arrGroupAndRole) == 2 && !empty($strUsername)){
			if(array_key_exists($arrGroupAndRole[0], $this->oUserProfile['arrPermittedUserGroupDetail'])){
				if(in_array(DELETE_USER, $this->oUserProfile['arrPermittedUserGroupDetail'][$arrGroupAndRole[0]]['arrRightOnGroup'])){
					$this->user_groups_model->RemoveUserFromGroup($strUsername, $strPmUserRoleId, $arrGroupAndRole[0], $arrGroupAndRole[1]);
					$this->loadview_simple_ajax(sprintf($this->str_json_success, ''));
				} else {
					$this->loadview_simple_ajax($this->str_json_permission_denied);
				}
			} else {
				$this->loadview_simple_ajax($this->str_json_permission_denied);
			}
		} else {
			$this->loadview_simple_ajax($this->str_json_bad_request);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_list_pm_role_of_usergroup(){
		$arrResult = array();

		$strUserGroupId = @$_REQUEST['gid'];
		if(!empty($strUserGroupId)){
			if(!@empty($this->oUserProfile['arrPermittedUserGroupDetail'][$strUserGroupId]['arrPMRoleAssignable'])){
				foreach($this->oUserProfile['arrPermittedUserGroupDetail'][$strUserGroupId]['arrPMRoleAssignable'] as $strId=>$strName){
					$arrResult[] = array('id' => $strUserGroupId . '|' . $strId, 'name' => strtoupper($strName));
				}
			}
		}
		$this->loadview_simple_ajax(json_encode($arrResult));
	}

	private function GetListFilter() {
		$arrResult = array();

		if(!empty($_REQUEST['search_by_name'])){
			$strKeySearch = trim($this->input->get_post('search_by_name'));
			if(strpos($strKeySearch, '@') !== false) {
				$strKeySearch = substr($strKeySearch, 0, strpos($strKeySearch, '@'));
			}
			$arrResult['search_by_name'] = new MongoRegex('/^' . preg_quote($strKeySearch) . '/i');
		}
		if(!empty($_REQUEST['department'])){
			$arrResult['department'] = $this->input->get_post('department');
		}
		if(!empty($_REQUEST['product'])) {
			$arrResult['product'] = $this->input->get_post('product');
		}
		if(!empty($_REQUEST['user_group'])) {
			$arrResult['user_group'] = $this->input->get_post('user_group');
		}
		if(!empty($_REQUEST['role'])) {
			$arrResult['role'] = $this->input->get_post('role');
		}
		if(!empty($_REQUEST['permission'])) {
			$arrResult['permission'] = $this->input->get_post('permission');
		}

		return $arrResult;
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
	// ------------------------------------------------------------------------------------------ //
	public function ajax_list_role_of_product_old(){
		$arrResult = array();

		list($strUserGroupId, $strProductKey) = explode('|', @$_REQUEST['pid']);
		#pd($strUserGroupId);
		#pd($this->oUserProfile['arrPermittedUserGroupDetail'][$strUserGroupId]['arrPMRoleAssignable']);
		if(!empty($strUserGroupId)){
			if(!@empty($this->oUserProfile['arrPermittedUserGroupDetail'][$strUserGroupId]['arrPermittedProductRole'])){
				foreach($this->oUserProfile['arrPermittedUserGroupDetail'][$strUserGroupId]['arrPermittedProductRole'] as $strId=>$oRole){
					$arrResult[] = array('id' => $strId, 'name' => strtoupper($oRole['role_name']));
				}
			}
		}
		$this->loadview_simple_ajax(json_encode($arrResult));
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_list_role_of_product(){
		$arrResult = array();

		$strProductKey = @$_REQUEST['pid'];
		#pd($strUserGroupId);
		#pd($this->oUserProfile['arrPermittedUserGroupDetail'][$strUserGroupId]['arrPMRoleAssignable']);
		foreach($this->oUserProfile['arrPermittedUserGroupDetail'] as $strUserGroupId => $oGroupDetail){
			foreach($oGroupDetail['arrPermittedProductRole'] as $strId=>$oRole){
				$arrResult[] = array('id' => $strUserGroupId . '|' . $strId, 'name' => strtoupper($this->oUserProfile['arrPermittedUserGroup'][$strUserGroupId]['group_name'] . ' - ' . $oRole['role_name']));
			}
		}

		$this->loadview_simple_ajax(json_encode($arrResult));
	}
	// ------------------------------------------------------------------------------------------ //
	public function save_user_department(){
		if($this->session->userdata('usertype')==USERTYPE_SUPERADMIN){
			$strUserName = $_REQUEST['username'];
			$strDepartmentKey = $_REQUEST['department_key'];

			$oUser = $this->users_model->LoadUserByUserName($strUserName);
			if($oUser){
				$oRs = $this->users_model->UpdateUserInfo(
					$strUserName,
					array('department_key' => MongoSaveObj(DEPARTMENT_KEY, $strDepartmentKey))
				);
				if($oRs !== false)
					$this->loadview_simple_ajax(sprintf($this->str_json_success, ''));
				else
					$this->loadview_simple_ajax($this->str_json_db_error);
			} else {
				$this->loadview_simple_ajax($this->str_json_user_not_found);
			}
		} else {
			$this->loadview_simple_ajax($this->str_json_permission_denied);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function save_new_user(){
		$strUserName = $_REQUEST['username'];
		$strDepartmentKey = $_REQUEST['department_key'];
		$oUser = $this->users_model->LoadUserByUserName($strUserName);
		if(!$oUser){
			$oRs = $this->users_model->InsertNewUser($strUserName, $strDepartmentKey);
			if($oRs !== false)
				$this->loadview_simple_ajax(sprintf($this->str_json_success, ', "new_id": "' . $oRs . '"'));
			else
				$this->loadview_simple_ajax($this->str_json_db_error);
		} else {
			$this->loadview_simple_ajax($this->str_json_user_already_exists);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	private function LoadDepartment4User($strUser) {
		$oDepartment = null;
		$oUser = $this->users_model->LoadUserByUserName($strUser);
		if(!empty($oUser['department_key'])) {
			$oDepartment = $this->cmdb_model->LoadDepartmentByDepartmentKey($oUser['department_key']);
		}
		return $oDepartment;
	}
	// ------------------------------------------------------------------------------------------ //
	private function OptimizeUserRoleInfoResult($arrPmUsersRole) {
		#pd($arrPmUsersRole);
		$arrUsers = array();
		foreach($arrPmUsersRole as $oPmUR) {
			if(!array_key_exists($oPmUR['username'], $arrUsers)) {
				$arrUsers[$oPmUR['username']] = $oPmUR;
				$arrUsers[$oPmUR['username']]['user_group_id']   = array((string)$oPmUR['user_group_id'] => $oPmUR['user_group_id']);
				$arrUsers[$oPmUR['username']]['pm_role_id']      = array((string)$oPmUR['user_group_id'] => $oPmUR['pm_role_id']);
			} else {
				$arrUsers[$oPmUR['username']]['user_group_id'][(string)$oPmUR['user_group_id']] = $oPmUR['user_group_id'];
				$arrUsers[$oPmUR['username']]['pm_role_id'][(string)$oPmUR['user_group_id']]    = $oPmUR['pm_role_id'];
			}
		}
		return $arrUsers;
	}
	// ------------------------------------------------------------------------------------------ //
	private function FillDefaultRole(&$arrUser){
		foreach($arrUser as &$oUser){
			$strGroupId = (string)$this->oUserProfile['defaultRole']['group']['_id'];
			if(!array_key_exists($strGroupId, $oUser['user_group_id'])){
				$oUser['user_group_id'][$strGroupId] = $this->oUserProfile['defaultRole']['group']['_id'];
				$oUser['pm_role_id'][$strGroupId]    = null;
			}
		}
	}
	// ------------------------------------------------------------------------------------------ //
	private function ParseQueryString(){
		$arrParam = array();
		parse_str($_SERVER['QUERY_STRING'], $arrParam);

		unset($arrParam['page_user_role']);
		unset($arrParam['limit_user_role']);

		return http_build_query($arrParam);
	}
	// ------------------------------------------------------------------------------------------ //
	public function remove_from_product_role(){
		$arrGroupAndRole   = explode('|', @$_REQUEST['role_key']);
		$strUsername       = @$_REQUEST['username'];
		$strProductOwnerId = @$_REQUEST['product_owner_id'];
		$strProductKey     = @$_REQUEST['product_key'];

		if(is_array($arrGroupAndRole) && count($arrGroupAndRole) == 2 && !empty($strUsername)){
			if(array_key_exists($arrGroupAndRole[0], $this->oUserProfile['arrPermittedUserGroupDetail'])){
				if(array_key_exists($arrGroupAndRole[1], $this->oUserProfile['arrPermittedUserGroupDetail'][$arrGroupAndRole[0]]['arrPermittedProductRole'])){
					$this->users_model->RemoveUserFromProduct($strUsername, $strProductOwnerId, $arrGroupAndRole[0], $arrGroupAndRole[1]);
					$this->loadview_simple_ajax(sprintf($this->str_json_success, ''));
				} else {
					$this->loadview_simple_ajax($this->str_json_permission_denied);
				}
			} else {
				$this->loadview_simple_ajax($this->str_json_permission_denied);
			}
		} else {
			$this->loadview_simple_ajax($this->str_json_bad_request);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function save_to_product(){
		#$this->loadview_simple_ajax($this->str_json_permission_denied);
		$arrGroupAndRole   = explode('|', @$_REQUEST['role_key']);
		$strUsername       = @$_REQUEST['username'];
		$strProductOwnerId = @$_REQUEST['product_owner_id'];
		$strProductKey     = @$_REQUEST['product_key'];

		if(is_array($arrGroupAndRole) && count($arrGroupAndRole) == 2 && !empty($strUsername) && !empty($strProductKey)){
			if(array_key_exists($arrGroupAndRole[0], $this->oUserProfile['arrPermittedUserGroupDetail'])){
				if(array_key_exists($arrGroupAndRole[1], $this->oUserProfile['arrPermittedUserGroupDetail'][$arrGroupAndRole[0]]['arrPermittedProductRole'])){
					$oExitsted = $this->users_model->LoadProductOwnedByUser($strUsername, $strProductKey, $arrGroupAndRole[0], $arrGroupAndRole[1]);
					if(!$oExitsted){
						if(!empty($strProductOwnerId)){
							$oRs = $this->users_model->UpdateUserToProduct($strUsername, $strProductOwnerId, $strProductKey, $arrGroupAndRole[0], $arrGroupAndRole[1]);
							if($oRs !== FALSE)
								$this->loadview_simple_ajax(sprintf($this->str_json_success, ''));
							else
								$this->loadview_simple_ajax($this->str_json_db_error);
						} else {
							$oRs = $this->users_model->InsertUserToProduct($strUsername, $strProductKey, $arrGroupAndRole[0], $arrGroupAndRole[1]);
							if($oRs !== false)
								$this->loadview_simple_ajax(sprintf($this->str_json_success, ', "new_id": "' . $oRs . '"'));
							else
								$this->loadview_simple_ajax($this->str_json_db_error);
						}
					} else {
						$this->loadview_simple_ajax($this->str_json_duplicated_product_owner);
					}
				} else {
					$this->loadview_simple_ajax($this->str_json_permission_denied);
				}
			} else {
				$this->loadview_simple_ajax($this->str_json_permission_denied);
			}
		} else {
			$this->loadview_simple_ajax($this->str_json_bad_request);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	private function FilterUserRoleForEachUser($arrCondition, $oUser, $arrUsers, $strIdentifier) {
		/* user not in product filter, remove it */
		if(!isset($oUser['user_role_product']) && isset($arrCondition['product']))
			unset($arrUsers[$strIdentifier]);

		/* user not in Role filter, remove it */
		if(!isset($oUser['user_role_product']) && isset($arrCondition['role']))
			unset($arrUsers[$strIdentifier]);

		/* department filter */
		if(isset($arrCondition['department']) && isset($oUser['department_key'])) {
			if($oUser['department_key'] !== $arrCondition['department']) {
				unset($arrUsers[$strIdentifier]);
			}
		}

		/* filter by permission */
		if(isset($arrCondition['permission'])) {
			if(isset($oUser['user_role_product'])) {
				if(array_key_exists(0, $oUser['user_role_product'])) {
					foreach($oUser['user_role_product'] as $idx=>$oURP) {
						$oRolePermissionFilter = $this->users_model->ListRolePermissionsFilter($arrCondition['permission'], $oURP['user_group_id'], $oURP['role_id']);
						if(empty($oRolePermissionFilter)) {
							unset($oUser['user_role_product'][$idx]);
						}
					}

				} else {
					$oRolePermissionFilter = $this->users_model->ListRolePermissionsFilter($arrCondition['permission'], $oUser['user_role_product']['user_group_id'], $oUser['user_role_product']['role_id']);
					if(empty($oRolePermissionFilter)) {
						unset($oUser['user_role_product']);
					}
				}
				if(empty($oUser['user_role_product'])) {
					unset($arrUsers[$strIdentifier]);
				}
			}
		}

		return $arrUsers;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_product_by_department($strTagId, $strTagName, $strTagClass) {
		$arrProduct = array();
		$strDepartmentKey = $this->input->get('department');
		if(!empty($strDepartmentKey)) {
			$arrProduct = $this->cmdb_model->ListProduct($strDepartmentKey);
		} else {
			$arrProduct = $this->cmdb_model->ListProduct();
		}
		$strHTML = '';
		$arrData = array(
			'arrProduct' 		=> $arrProduct
			, 'strTagName' 		=> $strTagName
			, 'strTagId' 		=> $strTagId
			, 'strTagClass' 	=> $strTagClass
			, 'base_url'		=> $this->getBaseUrl()
		);

		$strHTML = $this->parser->parse('Users/slt_product', $arrData, TRUE);
		echo $strHTML;
		exit;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_user_by_department($strTagId, $strTagName, $strTagClass) {
		$arrProduct = array();
		$strDepartmentKey = $this->input->get('department');
		if(!empty($strDepartmentKey)) {
			$arrUser = $this->users_model->ListUser(array(USER_DEPARTMENT_KEY => MongoCondObj(USER_DEPARTMENT_KEY, $strDepartmentKey)));
		} else {
			$arrUser = $this->users_model->ListUser();
		}
		$strHTML = '';
		$arrData = array(
			'arrUser' 			=> $arrUser
			, 'strTagName' 		=> $strTagName
			, 'strTagId' 		=> $strTagId
			, 'strTagClass' 	=> $strTagClass
			, 'base_url'		=> $this->getBaseUrl()
		);

		$strHTML = $this->parser->parse('Users/slt_user', $arrData, TRUE);
		echo $strHTML;
		exit;
	}
	// ------------------------------------------------------------------------------------------ //
}