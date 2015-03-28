<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_ci.php';
class Index extends BaseCIController {
   
	public function __construct()
    {
    	$this->m_nCIType             = CI_SERVER;
    	$this->m_strCIName           = 'server';
    	$this->m_strFieldName_CIName = 'server_name';

    	$this->m_strViewFolder4List    = 'base_ci';
    	$this->m_strViewFolder4Detail  = 'base_ci';

		parent::__construct();
        
		$this->load->helper('ci_server_helper');
		$this->load->model('ci_server_model', 'ci_model');
        $this->load->model('ci_division_model', 'ci_division_model');
        $this->load->model('ci_department_model', 'ci_department_model');
        $this->load->model('ci_product_model', 'ci_product_model');
	}

	public function index()
	{
		$this->ci_list();
	}
    
      /* cheat function */  
    protected function GrantPermission()
    {
        $arrDivision = $this->ci_division_model->GetCI(array('status'=>1, 'deleted'=>0));
        $this->session->set_userdata('arrOwnedDivision', $arrDivision);
        
        $arrDepartment = $this->ci_department_model->GetCI(array('status'=>1, 'deleted'=>0));
        $this->session->set_userdata('arrOwnedDepartment', $arrDepartment);
        
        $arrProduct = $this->ci_product_model->GetCI(array('deleted'=>0));
        $this->session->set_userdata('arrOwnedProduct', $arrProduct);
    }
}
