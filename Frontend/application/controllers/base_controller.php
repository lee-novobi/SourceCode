<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Base_controller extends CI_Controller {
	var $moduleName    = '';
	var $subModuleName = '';
	var $funcName	   = '';
	var $isAjaxRequest = FALSE;
	var $incidentDirectory = 'incident/';
	var $alertDirectory    = 'alert/';
	var $arrAutoRefreshList = array();

	public function __construct(){
		ini_set('memory_limit', -1);
		set_time_limit(0);

		date_default_timezone_set('Asia/Ho_Chi_Minh');
		parent::__construct();
		error_reporting($this->config->item('error_reporting'));
		//error_reporting(0);
		error_reporting(E_ALL);
        $this->load->model('mongo_base_model', 'mongo_base_model');

		$this->load->library('parser');
		$this->load->library('my_session','','session');

		$this->load->helper('url');

		$this->moduleName    = $this->uri->segment(1);
		$this->subModuleName = $this->uri->segment(2);
		$this->funcName 	 = $this->uri->segment(3);

		if(empty($this->moduleName))
		{
			$this->moduleName = $this->router->routes['default_controller'];
		}

		$base_url = $this->config->item('base_url');
		if($this->session->userdata('username')=='') $this->session->set_userdata('username', 'guest');
		/*if($this->session->userdata('userId')==''|| $this->session->userdata('username')==null || $this->session->userdata('username')===false ) {
			if ($this->input->is_ajax_request()) {
				$isAjaxRequest = TRUE;
				exit('error');
			}
			else {
				header('Location: ' . $base_url . 'login?re=' . base64_encode($_SERVER['REQUEST_URI']));
				exit;
			}
		}*/
	}

	public function index(){
		$this->loadview('welcome_message', array());
	}



	public function loadview($_template, $_data=array(), $layout='layout', $fetch=false){
		$_data['base_url']      = base_url();
		$_data['current_url']   = current_url();
		$_data['message']       = $this->load_message();

		$_data['userfullname']  = $this->session->userdata('userfullname');
		$_data['incident_directory'] = $this->incidentDirectory;
		$_data['alert_directory']    = $this->alertDirectory;
		$_data['row_alternate']      = false;
		$_data['arrAutoRefreshList'] = $this->arrAutoRefreshList;
		$_data['moduleName']    = $this->moduleName;
		$_data['subModuleName'] = $this->subModuleName;
		$_data['funcName']      = $this->funcName;
        $_data['active_CI']     = $this->load_ci_by_name($this->moduleName);
#vd($_data);
        $header = $this->load_header($_data);
        $horizontalMenu = $this->load_horizontal_menu($_data);
        $mainContent = $this->load->view($_template, $_data, true);

		$this->load->view($layout, array(
			'_content'  	  => $mainContent,
            '_horizontal_menu'=> $horizontalMenu,
            '_header'         => $header,
			'base_url'		  => $this->config->item('base_url'),
			'moduleName'	  => $this->moduleName,
			'subModuleName'   => $this->subModuleName,
			'funcName'		  => $this->funcName
		));
	}

    protected function load_ci_by_name($strCIName)
    {
        $oCI = $this->mongo_base_model->LoadCIByName($strCIName);
        if (!empty($oCI)) {
            return $oCI;
        }
        else {
            return NULL;
        }
    }

	// luu thong bao vao bien session de load vao trang tiep theo
	public function load_message() {
		$message = '';
		if ($this->session->flashdata('msg')!='' && $this->session->flashdata('type_msg')!='') {
			$_message_template_data = array(
				'msg' 		=> $this->session->flashdata('msg'),
				'type_msg' 	=> $this->session->flashdata('type_msg'),
				'base_url'	=> $this->config->item('base_url')
			);
			$message = $this->parser->parse('message', $_message_template_data, TRUE);
		}
		else {
			$message = '';
		}
		return $message;
	}

    // ------------------------------------------------------------------------------------------ //
	protected function load_horizontal_menu($arrData) {
		$strHtmlMenu = $this->parser->parse('horizontal_menu', $arrData, TRUE);
		return $strHtmlMenu;
	}

    // ------------------------------------------------------------------------------------------ //
	protected function load_header($arrData) {
		$strHtmlMenu = $this->parser->parse('header', $arrData, TRUE);
		return $strHtmlMenu;
	}

    // ------------------------------------------------------------------------------------------ //
	protected function getUserHostKeyPermission()
	{
		$userHostkey = array();
		foreach ($this->session->userdata('userRightHost') as $iZabbixServerId=>$arrHostId)
		{
			foreach ($arrHostId as $iHostId)
			{
				$userHostkey[] = $iHostId.':'.$iZabbixServerId;
			}
		}
		return $userHostkey;
	}

	// ------------------------------------------------------------------------------------------ //
	protected function GetPaginationRequest($strLimitParam=NULL, $strPageParam)
	{
		$iLimit		= isset($_REQUEST[$strLimitParam]) ? (int)$_REQUEST[$strLimitParam] : PAGER_SIZE;
		$iPage 		= isset($_REQUEST[$strPageParam]) ? (int)$_REQUEST[$strPageParam] : 1;
		if ($iPage < 1) $iPage = 1;
		$iOffset 	= ($iPage - 1) * $iLimit;
		return array('limit' => $iLimit, 'page' => $iPage, 'offset' => $iOffset);
	}

	// ------------------------------------------------------------------------------------------ //
	protected function GetSortRequest($strSortFieldParam=NULL, $strOrderParam=NULL)
	{
		$arrSort = array();
		if(isset($_REQUEST[$strSortFieldParam])){
			$strSortField = $_REQUEST[$strSortFieldParam];
			$arrSort[strtolower($strSortField)] = (isset($_REQUEST[$strOrderParam])) ? (int)$_REQUEST[$strOrderParam] : 1;
		} else {
			$arrSort['host_name'] = 1;
		}
		return $arrSort;
	}

	// ------------------------------------------------------------------------------------------ //
	protected function GetParameter($arrParams)
	{
		$strRes = array();
		foreach ($arrParams as $strParam)
		{
			$strValue = $this->input->get_post($strParam, TRUE);
			if ($strValue !== false && $strValue !== "")
			{
				$strRes[$strParam] = $strValue;
			}
		}
		return $strRes;
	}

	// ------------------------------------------------------------------------------------------ //
	public function getBaseUrl() {
		return $this->config->item('base_url');
	}

	// ------------------------------------------------------------------------------------------ //
	function get_ip() {
		$strIP = '';
		if (getenv("HTTP_CLIENT_IP"))
			$strIP = getenv("HTTP_CLIENT_IP");
		else if(getenv("HTTP_X_FORWARDED_FOR"))
			$strIP = getenv("HTTP_X_FORWARDED_FOR");
		else if(getenv("REMOTE_ADDR"))
			$strIP = getenv("REMOTE_ADDR");
		else
			$strIP = "UNKNOWN";
		return $strIP;
	}
}
