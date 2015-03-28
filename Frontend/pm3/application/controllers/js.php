<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';

class Js extends Base_Controller {
	public function __construct(){
		parent::__construct();
		$this->output->set_header("content-type: application/x-javascript");
	}
	// ------------------------------------------------------------------------------------------ //
	public function index(){
		$this->constants();
	}
	// ------------------------------------------------------------------------------------------ //
	public function constants(){
		$this->loadview('JS/constants', array(), 'layout_ajax');
	}
}
?>