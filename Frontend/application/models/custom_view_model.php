<?php
require_once "mongo_base_model.php";

class Custom_view_model extends Mongo_base_model
{
	function __construct() {
		parent::__construct();
	}
	// ------------------------------------------------------------------------------------------ //
	function LoadCustomView($nCIType, $strUserName){
		$oRs = null;
		if(!empty($strUserName) && !is_null($nCIType)){
			$oRs = $this->SelectOneMongoDB(
				array(
					'ci_type'	=> (int)$nCIType,
					'username'	=> new MongoRegex('/^' . $strUserName . '$/i'),
					'deleted'	=> 0
				), $this->cltCustomView
			);
		}
		#pd();
		return $oRs;
	}
	// ------------------------------------------------------------------------------------------ //
	function SaveCustomView($nCIType, $strUserName, $arrData){
		$this->DeleteCustomView($nCIType, $strUserName);
		$this->InsertCustomView($nCIType, $strUserName, $arrData);
	}
	// ------------------------------------------------------------------------------------------ //
	function InsertCustomView($nCIType, $strUserName, $arrData){
		if(!empty($strUserName) && !is_null($nCIType)){
			$oCI = $this->LoadCI($nCIType);
			if(!is_null($oCI)){
				$this->InsertMongoDB(
					array(
						'ci_type'	=> (int)$nCIType,
						'ci_name'	=> $oCI['ci_name'],
						'username'	=> $strUserName,
						'deleted'	=> 0,
						'group_fields'	=> $arrData
					),
					$this->cltCustomView
				);
			}
		}
	}
	// ------------------------------------------------------------------------------------------ //
	function DeleteCustomView($nCIType, $strUserName){
		if(!empty($strUserName) && !is_null($nCIType)){
			$this->UpdateMongoDB(
				array(
					'ci_type'	=> (int)$nCIType,
					'username'	=> new MongoRegex('/^' . $strUserName . '$/i'),
					'deleted'	=> 0
				),
				array('deleted' => 1),
				$this->cltCustomView
			);
		}
	}
	// ------------------------------------------------------------------------------------------ //
}
?>
