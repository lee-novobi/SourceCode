<?php
// require_once "mongo_base_model.php";

class Cmdbv2_model extends Mongo_base_model
{
	var $oCMDB2Conn = null;
	var $oCMDB2DB = null;

	function __construct($right=null)
 	{
		parent :: __construct();
		$this->mongo_config_cmdbv2  = $this->config->item('cmdbv2');

		$strConnString = sprintf('mongodb://%s', $this->mongo_config_cmdbv2['mongo_hostbase']);
		try {
			$this->oCMDB2Conn = new Mongo($strConnString, array(
				'username' => $this->mongo_config_cmdbv2['mongo_username'],
			    'password' => $this->mongo_config_cmdbv2['mongo_password'],
			    'db'       => $this->mongo_config_cmdbv2['mongo_database']
			));
			$this->oCMDB2DB = $this->oCMDB2Conn->selectDB($this->mongo_config_cmdbv2['mongo_database']);
		} catch(MongoConnectionException $e) {
			// log the exception
			die('Error connecting to MongoDB server ' . $e->getMessage());
		}

	}
	// ------------------------------------------------------------------------------------------ //
	public function LoadDepartmentByDepartmentKey($oDepartmentKey){
		$oResult = null;
		if(!empty($oDepartmentKey)){
			$arrCondition = array(
				'deleted' => 0,
				DEPARTMENT_KEY => MongoCondObj(DEPARTMENT_KEY, $oDepartmentKey)
			);
			$oClt = new MongoCollection($this->oCMDB2DB, CLT_DEPARTMENT);
			$oResult = $oClt->findOne($arrCondition);
		}
		return $oResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListDepartment($arrCondition=array('deleted' => 0)){
		$arrResult = array();
		$arrSort   = array('alias' => 1);
		$oClt      = new MongoCollection($this->oCMDB2DB, CLT_DEPARTMENT);
		$oCursor   = $oClt->find($arrCondition)->sort($arrSort);

		foreach ($oCursor as $oDoc) {
		    $arrResult[] = $oDoc;
		}

		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListProduct($oDepartmentKey=null){
		$arrResult = array();

		$arrCondition = array('deleted' => 0);
		if(!empty($oDepartmentKey)){
			$arrCondition[PRODUCT_DEPARTMENT_KEY] = MongoCondObj(PRODUCT_DEPARTMENT_KEY, $oDepartmentKey);
		}
		$oClt = new MongoCollection($this->oCMDB2DB, CLT_PRODUCT);
		$oCursor = $oClt->find($arrCondition);

		foreach ($oCursor as $oDoc) {
		    $arrResult[] = $oDoc;
		}

		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function LoadProductByObjectId($oObjectId){
		$oResult = null;
		if(!empty($oObjectId)){
			$arrCondition = array('deleted' => 0, '_id' => $oObjectId);
			$oClt = new MongoCollection($this->oCMDB2DB, CLT_PRODUCT);
			$oResult = $oClt->findOne($arrCondition);
		}
		return $oResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function LoadProductByProductKey($oProductKey){
		$oResult = null;
		if(!empty($oProductKey)){
			$arrCondition = array('deleted' => 0, PRODUCT_KEY => MongoCondObj(PRODUCT_KEY, $oProductKey));
			$oClt = new MongoCollection($this->oCMDB2DB, CLT_PRODUCT);
			$oResult = $oClt->findOne($arrCondition);
		}
		return $oResult;
	}
}