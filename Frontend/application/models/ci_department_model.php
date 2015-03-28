<?php
require_once "base_ci_model.php";

class Ci_department_model extends Base_ci_model
{
	function __construct() {
		$this->m_nCIType        = CI_DEPARTMENT;
		$this->m_strCITableName = $this->cltDepartment;
        $this->m_strTmpCITableName = $this->cltTmpDepartment;
        
		parent::__construct();
	}
}
