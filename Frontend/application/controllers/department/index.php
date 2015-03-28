<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_ci.php';
class Index extends BaseCIController {
	public function __construct()
    {
    	$this->m_nCIType             = CI_DEPARTMENT;
    	$this->m_strCIName           = 'department';
    	$this->m_strFieldName_CIName = 'alias';

    	$this->m_strViewFolder4List    = 'base_ci';
    	$this->m_strViewFolder4Detail  = 'base_ci';

		parent::__construct();
		$this->load->helper('ci_department_helper');
		$this->load->model('ci_department_model', 'ci_model');
        $this->load->model('ci_division_model', 'ci_division_model');
	}

	public function index()
	{
		$this->ci_list();
	}
    
    public function update()
    {
        $this->update_ci();
    }
    
    public function update_sm()
    {
        $this->update_ci_submit();
    }
    
    protected function GetUpdatedCIInfo()
    {
        $arrCIInfo  = array();
        $arrResult  = array('data' => array(), 'error' => array(), 'is_valid' => false);
        $arrParam   = array ('cid', 'code', 'alias', 'fa_alias', 'fa_code');
        
        $arrResult['data']      = $this->GetParameter($arrParam);
        $arrResult['error']     = $this->VerifyUpdatedCIInfo($arrResult['data']);
        $arrResult['is_valid']  = !empty( $arrResult['error'] ) ? false : true;

        return $arrResult;
    }
    
    protected function VerifyUpdatedCIInfo($arrCIInfo)
    {
        $arrError = array();
        
        if ((!isset($arrCIInfo['alias'])) || @$arrCIInfo['alias'] == "") 
        {
            $arrError['alias'] = ucfirst($this->m_strCIName)." alias cannot be empty.";
        }
        else {
            $iCountCI = $this->ci_model->CountCI(
                                                    array('deleted' => NO
                                                        , 'alias' => new MongoRegex('/^'.$arrCIInfo['alias'].'$/i') 
                                                        , '_id' => array('$ne' => new MongoId($arrCIInfo['cid'])))
                                        );
            if ($iCountCI > 0)
            {
                $arrError['alias'] = ucfirst($this->m_strCIName)." alias <b>".$arrCIInfo['alias']."</b> has existed.";
            }
        }
        
         if ((!isset($arrCIInfo['code'])) || @$arrCIInfo['code'] == "") 
        {
            $arrError['code'] = ucfirst($this->m_strCIName)." code cannot be empty.";
        }
        else {
            $iCountCI = $this->ci_model->CountCI(
                                                    array('deleted' => NO
                                                        , 'code' => new MongoRegex('/^'.$arrCIInfo['code'].'$/i') 
                                                        , '_id' => array('$ne' => new MongoId($arrCIInfo['cid'])))
                                        );
            if ($iCountCI > 0)
            {
                $arrError['code'] = ucfirst($this->m_strCIName)." code <b>".$arrCIInfo['code']."</b> has existed.";
            }
        }

        return $arrError;
    }
    
    protected function GetCIInfoOptionForUpdate($oCI)
    {
        $arrCIInfoOption = array();
        
        $arrDivision = $this->ci_division_model->GetCI(array('deleted' => NO));
        $arrCIInfoOption['division'] = !empty($arrDivision) ? $arrDivision : array();

        return $arrCIInfoOption;
    }
    
    protected function UpdateCI($arrOldCIInfo, &$arrNewCIInfo, $arrCondition)
    {
        if (!empty($arrNewCIInfo))
        {   
            $arrNewCIInfo['status'] = intval($arrNewCIInfo['status']);
            if (isset($arrNewCIInfo['cid'])) 
            {
                unset ($arrNewCIInfo['cid']);
            }
            $oRs = $this->ci_model->UpdateCI($arrNewCIInfo, $arrCondition);
            return $oRs;
        }
        return false;
    }
    
    public function ajax_get_department_by_division($strTagName, $strTagId, $strTagClass)
    {
        $strHTML = '';
        $arrDepartment = array();
        $strDivisionId = $this->input->get('division_id');
        if ($strDivisionId !== false && $strDivisionId !== "")
        {
            $arrCondition = array(
                            'division_id' => new MongoId($strDivisionId)
                            , 'deleted' => NO
                            , 'status' => VALUE_DEPARTMENT_STATUS_ACTIVE
                            );
            $arrDepartment = $this->ci_model->GetCI($arrCondition);
        }
       
		$arrData = array(
			'arrDepartment' 			=> $arrDepartment
			, 'strTagName' 				=> $strTagName
			, 'strTagId' 				=> $strTagId
			, 'strTagClass' 			=> $strTagClass
			, 'base_url'				=> $this->config->item('base_url')
		);

		$strHTML = $this->parser->parse('department/ajax_templates/slt_department', $arrData, TRUE);
		echo $strHTML;
		exit;
    }
    
    public function ajax_get_department_code_by_department_id()
    {
        $strDepartmentCode = "";
        $strDepartmentId = $this->input->get('department_id');
        if ($strDepartmentId !== false && $strDepartmentId !== "")
        {
            $oDepartment = $this->ci_model->LoadCIDetail($strDepartmentId);
            if ($oDepartment)
            {
                $strDepartmentCode = @$oDepartment['code'];
            }
        }
        echo $strDepartmentCode;
        exit;
    }
}
