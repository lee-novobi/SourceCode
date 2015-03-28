<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';

class Roles extends Base_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('pm_model', 'model');
	}
	// ------------------------------------------------------------------------------------------ //
	public function index(){
		$this->list_data();
	}
	public function list_data(){
		$arrRole = $this->model->ListRole();
		$this->loadview('Roles/list',
            array(
            	'arrRole' 		=> $arrRole,
            )
        );
	}
	// ------------------------------------------------------------------------------------------ //
	public function save_list(){
		$strJsonRoleList = $_REQUEST['data'];
		try{
			$arrRole = json_decode($strJsonRoleList);
			$this->model->SaveRolesOrder($arrRole);

			$this->session->set_flashdata('msg','Update successful !!!');
			$this->session->set_flashdata('type_msg', 'success');
		} catch(Exception $ex){
			$strErrorMsg = $ex->getMessage();
			$this->session->set_flashdata('msg','Update failed' . empty($strErrorMsg)?'':(': '.$strErrorMsg));
			$this->session->set_flashdata('type_msg', 'error');
		}
	}
}