<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_ci.php';
class Index extends BaseCIController {
	public function __construct()
    {
    	$this->m_nCIType             = CI_PRODUCT;
    	$this->m_strCIName           = 'product';
    	$this->m_strFieldName_CIName = 'alias';

    	$this->m_strViewFolder4List    = 'base_ci';
    	$this->m_strViewFolder4Detail  = 'base_ci';

		parent::__construct();
		$this->load->helper('ci_product_helper');
		$this->load->model('ci_product_model', 'ci_model');
        $this->load->model('ci_department_model', 'ci_department_model');
        $this->load->model('ci_division_model', 'ci_division_model');
	}

	public function index()
	{
		$this->ci_list();
	}
    
    public function add()
    {
        $this->add_ci();
    }
    
    public function add_sm()
    {
        $this->add_ci_submit();
    }
    
    public function update()
    {
        $this->update_ci();
    }
    
    public function update_sm()
    {
        $this->update_ci_submit();
    }
    
    protected function GetCIInfoOptionForAdd()
    {
        $arrCIInfoOption = array();
        
        $arrDivision = $this->ci_division_model->GetCI(array('deleted' => NO, 'status' => VALUE_DIVISION_STATUS_ACTIVE));
        $arrCIInfoOption['division'] = !empty($arrDivision) ? $arrDivision : array();
        
        return $arrCIInfoOption;
    }
    
    protected function GetAddedCIInfo()
    {
        $arrCIInfo  = array();
        $arrResult  = array('data' => array(), 'error' => array(), 'is_valid' => false);
        $arrParam   = array ('code', 'alias', 'fa_alias', 'fa_code', 'type', 'division_id', 'department_id');
        
        $arrResult['data']      = $this->GetParameter($arrParam);
        $arrResult['error']     = $this->VerifyAddedCIInfo($arrResult['data']);
        $arrResult['is_valid']  = !empty( $arrResult['error'] ) ? false : true;
        
        return $arrResult;
    }
    
    protected function GetUpdatedCIInfo()
    {
        $arrCIInfo  = array();
        $arrResult  = array('data' => array(), 'error' => array(), 'is_valid' => false);
        $arrParam   = array ('cid', 'alias', 'fa_alias', 'fa_code', 'type', 'status', 'division_id', 'department_id');
        
        $arrResult['data']      = $this->GetParameter($arrParam);
        $arrResult['error']     = $this->VerifyUpdatedCIInfo($arrResult['data']);
        $arrResult['is_valid']  = !empty( $arrResult['error'] ) ? false : true;

        return $arrResult;
    }
    
    protected function VerifyAddedCIInfo($arrCIInfo)
    {
        $arrError = array();
        if ((!isset($arrCIInfo['code'])) || @$arrCIInfo['code'] == "") 
        {
            $arrError['code'] = ucfirst($this->m_strCIName)." code cannot be empty.";
        }
        else {
            $iCountCI = $this->ci_model->CountCI(array( 'deleted' => NO, 
                                                        'code' => new MongoRegex('/^'.$arrCIInfo['code'].'$/i')));
            if ($iCountCI > 0)
            {
                $arrError['code'] = ucfirst($this->m_strCIName)." code existed.";
            }
        }
        
        if ((!isset($arrCIInfo['alias'])) || @$arrCIInfo['alias'] == "") 
        {
            $arrError['alias'] = ucfirst($this->m_strCIName)." alias cannot be empty.";
        }
        else {
            $iCountCI = $this->ci_model->CountCI(array( 'deleted' => NO, 
                                                        'alias' => new MongoRegex('/^'.$arrCIInfo['alias'].'$/i')));
            if ($iCountCI > 0)
            {
                $arrError['alias'] = ucfirst($this->m_strCIName)." alias has existed.";
            }
        }
        
        if ((!isset($arrCIInfo['division_id'])) || @$arrCIInfo['division_id'] == "") 
        {
            $arrError['division_id'] = "Division cannot be empty.";
        }
        else {
            $iCountCI = $this->ci_division_model->CountCI(array('deleted' => NO, 
                                                                '_id' => new MongoId($arrCIInfo['division_id'])));
            if ($iCountCI == 0)
            {
                $arrError['division_id'] = "Division not existed.";
            }
        }
        
        if ((!isset($arrCIInfo['department_id'])) || @$arrCIInfo['department_id'] == "") 
        {
            $arrError['department_id'] = "Department cannot be empty.";
        }
        else {
            $iCountCI = $this->ci_department_model->CountCI(array('deleted' => NO,
                                                                   '_id' => new MongoId($arrCIInfo['department_id'])));
            if ($iCountCI == 0)
            {
                $arrError['department_id'] = "Department not existed.";
            }
        }
        return $arrError;
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
        
        if ((!isset($arrCIInfo['division_id'])) || @$arrCIInfo['division_id'] == "") 
        {
            $arrError['division_id'] = "Division cannot be empty.";
        }
        else {
            $iCountCI = $this->ci_division_model->CountCI(array('deleted' => NO,
                                                                '_id' => new MongoId($arrCIInfo['division_id'])));
            if ($iCountCI == 0)
            {
                $arrError['division_id'] = "Division not existed.";
            }
        }
        
        if ((!isset($arrCIInfo['department_id'])) || @$arrCIInfo['department_id'] == "") 
        {
            $arrError['department_id'] = "Department cannot be empty.";
        }
        else {
            $iCountCI = $this->ci_department_model->CountCI(array('deleted' => NO,
                                                                  '_id' => new MongoId($arrCIInfo['department_id'])));
            if ($iCountCI == 0)
            {
                $arrError['department_id'] = "Department not existed.";
            }
        }
        return $arrError;
    }
    
    protected function GetDependentCIInfoOption($arrPreviousInputData, &$arrCIInfoOption)
    {
        if (isset($arrPreviousInputData['division_id']) && $arrPreviousInputData['division_id'] != "")
        {
            $arrCondition = array(
                            'division_id'   => new MongoId($arrPreviousInputData['division_id'])
                            , 'deleted'     => NO
                            , 'status'      => VALUE_DEPARTMENT_STATUS_ACTIVE
                            );
  //  vd($arrCondition);
            $arrDepartment = $this->ci_department_model->GetCI($arrCondition);

            if (!empty($arrDepartment))
            {
                $arrCIInfoOption['department'] = $arrDepartment; 
            }
        }
    }
    
    protected function InsertCI(&$arrCIInfo)
    {
        if (!empty($arrCIInfo))
        {
            $arrCIInfo['status']    = VALUE_PRODUCT_STATUS_NEW;
            $arrCIInfo['deleted']   = NO;
                
            $oDivision = $this->ci_division_model->LoadCIDetail($arrCIInfo['division_id']);
            if ($oDivision) 
            {
                $arrCIInfo['division_id'] = $oDivision['_id'];
                $arrCIInfo['division_alias'] = $oDivision['alias'];
            }
            $oDepartment = $this->ci_department_model->LoadCIDetail($arrCIInfo['department_id']);
            if ($oDepartment)
            {
                $arrCIInfo['department_id'] = $oDepartment['_id'];
                $arrCIInfo['department_alias'] = $oDepartment['alias'];
            }

            $oRs = $this->ci_model->InsertCI($arrCIInfo);
            return $oRs;
        }
        return false;
    }
    
    protected function GetCIInfoOptionForUpdate($oCI)
    {
        $arrCIInfoOption = array();
        
        $arrDivision = $this->ci_division_model->GetCI(array('deleted' => NO));
        $arrCIInfoOption['division'] = !empty($arrDivision) ? $arrDivision : array();
        
        $arrCondition = array(
                            'division_id'   => $oCI['division_id']
                            , 'deleted'     => NO
                            , 'status'      => VALUE_DEPARTMENT_STATUS_ACTIVE
                       );
        $arrDepartment = $this->ci_department_model->GetCI($arrCondition);
        $arrCIInfoOption['department'] = !empty($arrDepartment) ? $arrDepartment : array();

        return $arrCIInfoOption;
    }
    
    protected function UpdateCI($arrOldCIInfo, &$arrNewCIInfo, $arrCondition)
    {
        if (!empty($arrNewCIInfo))
        {
            $oDivision = $this->ci_division_model->LoadCIDetail($arrNewCIInfo['division_id']);
            if ($oDivision) 
            {
                $arrNewCIInfo['division_id'] = $oDivision['_id'];
                $arrNewCIInfo['division_alias'] = $oDivision['alias'];
            }
            $oDepartment = $this->ci_department_model->LoadCIDetail($arrNewCIInfo['department_id']);
            if ($oDepartment)
            {
                $arrNewCIInfo['department_id'] = $oDepartment['_id'];
                $arrNewCIInfo['department_alias'] = $oDepartment['alias'];
            }
            
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
}
