<?php
require_once "base_ci_model.php";

class Ci_product_model extends Base_ci_model
{
	function __construct() {
		$this->m_nCIType        = CI_PRODUCT;
		$this->m_strCITableName = $this->cltProduct;
        $this->m_strTmpCITableName = $this->cltTmpProduct;
		parent::__construct();
	}
}
