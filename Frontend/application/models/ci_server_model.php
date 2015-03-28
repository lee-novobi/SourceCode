<?php
require_once "base_ci_model.php";

class Ci_server_model extends Base_ci_model
{
	function __construct() {
		$this->m_nCIType        = CI_SERVER;
		$this->m_strCITableName = $this->cltServer;
		parent::__construct();
	}
}
