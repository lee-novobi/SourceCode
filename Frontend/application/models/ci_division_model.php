<?php
require_once "base_ci_model.php";

class Ci_division_model extends Base_ci_model
{
	function __construct() {
		$this->m_nCIType        = CI_DIVISION;
		$this->m_strCITableName = $this->cltDivision;
    	$this->m_strTmpCITableName = $this->cltTmpDivision;
		parent::__construct();
	}
}
