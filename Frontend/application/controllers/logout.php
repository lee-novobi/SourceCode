<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Logout extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->library('my_session','','session');
	}

	public function index(){
		
		$base_url = $this->config->item('base_url');
		unset($_SESSION['username']);
		unset($_SESSION['userfullname']);
		unset($_SESSION['userId']);
		
		setcookie(COOKIE_SESSION_KEY, "", time()-3600);

		header('Location: ' . $base_url . 'login');
		exit;
	}
}
?>
