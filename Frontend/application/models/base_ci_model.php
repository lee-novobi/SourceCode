<?php
require_once "mongo_base_model.php";

abstract class Base_ci_model extends Mongo_base_model {
	# Các class kế thừa Base_ci_model cần phải khởi tạo $m_nCIType và $m_strCITableName
	# trong __construct của nó, sau đó gọi __construct của Base_ci_model
	var $m_nCIType;
	var $m_strCITableName;
    var $m_strTmpCITableName;

	function __construct() {
		((!empty($this->m_nCIType) && !empty($this->m_strCITableName)) or show_error('Please init ci in models.'));
		parent::__construct();
	}

	public function ListCI($arrCondition=array(), $offset=0, $limit=PAGER_SIZE){
		global $arrDefined;
		$strUsername = $this->session->userdata('username');
		list($arrSelectField, $arrCustomViewDetail) = $this->GetSelectedFieldFromCustomView($strUsername, $this->m_nCIType);
#pd($arrSelectField);
		if(!@empty($arrDefined['ignore_ci_field'][$this->m_nCIType]['private_fields'])){
			$arrSelectField = array_diff($arrSelectField, $arrDefined['ignore_ci_field'][$this->m_nCIType]['private_fields']);
		}
		$arrCondition['deleted'] = 0;
		$arrResult = $this->SelectMongoDB(
			$arrCondition, $this->m_strCITableName, $offset, $limit, array(), array_values($arrSelectField)
		);

		return array($arrResult, $arrSelectField);
	}

	public function CountCI($arrCondition=array()){
		$arrCondition['deleted'] = 0;
		$nResult = $this->CountMongoDB($arrCondition, $this->m_strCITableName);
		return $nResult;
	}

	public function LoadCIDetail($nCID=0){
		global $arrDefined;
		$oRs = null;

		if(!empty($nCID)){
			$oRs = $this->SelectOneMongoDB(array('_id' => new MongoId($nCID), 'deleted' => 0), $this->m_strCITableName);
		}

		if(!@empty($arrDefined['ignore_ci_field'][$this->m_nCIType]['private_fields'])){
			$oRs = array_diff_key($oRs, array_flip($arrDefined['ignore_ci_field'][$this->m_nCIType]['private_fields']));
		}

		return $oRs;
	}

    public function GetCIById($nCID=0)
    {
        $oRs = null;
        if(!empty($nCID)){
			$oRs = $this->SelectOneMongoDB(array('_id' => new MongoId($nCID), 'deleted' => 0), $this->m_strCITableName);
		}
        return $oRs;
    }

    public function GetCI($arrCondition=array(), $arrSort= array())
    {
        if (empty($arrSort))
        {
            $arrSort = array('alias' => 'asc');
        }
        $arrResult = $this->SelectMongoDB(
			$arrCondition, $this->m_strCITableName, 0, UNLIMITED, $arrSort
		);
		return $arrResult;
    }

    public function InsertCI($arrInsertedData, $strCITableName= "")
    {
        if ($strCITableName == "")
        {
            $oRs = $this->InsertMongoDB($arrInsertedData, $this->m_strCITableName);
        }
        else
        {
            $oRs = $this->InsertMongoDB($arrInsertedData, $strCITableName);
        }
        return $oRs;
    }

    public function UpdateCI($arrUpdatedData, $arrCondition, $strCITableName= "")
    {
        if ($strCITableName == "")
        {
            $oRs = $this->UpdateMongoDB($arrCondition, $arrUpdatedData, $this->m_strCITableName);
        }
        else
        {
            $oRs = $this->InsertMongoDB($arrCondition, $arrUpdatedData, $strCITableName);
        }
        return $oRs;
    }
    
    public function InsertTmpCI($arrInsertedData, $strCITableName= "")
    {
        if ($strCITableName == "")
        {
            $oRs = $this->InsertMongoDB($arrInsertedData, $this->m_strTmpCITableName);
        }
        else 
        {
            $oRs = $this->InsertMongoDB($arrInsertedData, $strCITableName);
        }
        return $oRs;
    }
}