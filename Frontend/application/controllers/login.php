<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->library('my_session','','session');
		$this->load->model('mysql_base_model','model');
	}

	public function index(){
		$strBaseUrl = $this->config->item('base_url');
		$strError = "";
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			//$strUsername = 'tg.servicedesk';
			$strUsername = $this->input->post('username');
			$strPassword = $this->input->post('password');
			
	
			if(!empty($strUsername) && !empty($strPassword)){
				$LDAP_SERVER = $this->config->item('ldap_server');
				$LDAP_PORT   = $this->config->item('ldap_port');
				$LDAP_DOMAIN = $this->config->item('ldap_domain');
				$LDAP_DN     = $this->config->item('ldap_dn');
				
				//$ds la ldap_connect de kiem tra username password
				//$ds = ldap_connect( $LDAP_SERVER, $LDAP_PORT );
				$ds = 1;
				
				if($ds){
					//@$login = ldap_bind( $ds, "$username@" . $LDAP_DOMAIN, $password );
					$login = 1;
					if($login) {
						//die('OK');
						$oUser = $this->model->LoadUserByUsername($strUsername);
						if($oUser){
							$arrUser = explode('@', $oUser['email']);
							$this->session->set_userdata('userId', $oUser['userid']);
							$this->session->set_userdata('username', $arrUser[0]);
							$this->session->set_userdata('userfullname', $oUser['full_name']);
				
							$strRedirectUrl = isset($_GET['re']) ? base64_decode($_GET['re']): $strBaseUrl;
							header('Location: ' . $strRedirectUrl);
							exit;
						} else {
							$strError = 'Whoops! We didn\'t recognise your username or password. Please try again.';
						}
					}
				} else {
					$strError = 'Whoops! Error connect to server.';
				}
			} else {
				$strError = 'Whoops! We didn\'t recognise your username or password. Please try again.';
			}
		} else {
			/*if(isset($_COOKIE[COOKIE_SESSION_KEY]))
			{
				$user = $this->model->LoadAutoLoginSession($_COOKIE[COOKIE_SESSION_KEY]);

				if(isset($user['userid']) && !empty($user['userid']))
				{
					$this->session->set_userdata('userId', $user['userid']);
					$this->session->set_userdata('username', $user['alias']);
					$this->session->set_userdata('userfullname',
							$user['surname'] .' '. $user['name']);

					$redirectUrl = isset($_GET['re']) ? base64_decode($_GET['re'])
															: $base_url;
					header('Location: ' . $redirectUrl);
				}
			}*/
		}

		$this->load->view('login', array(
			'base_url'	=> $this->config->item('base_url'),
			'strError'	=> $strError));
	}
}
?>
