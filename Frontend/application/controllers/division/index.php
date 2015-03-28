<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_ci.php';
class Index extends BaseCIController {
	public function __construct()
    {
    	$this->m_nCIType             = CI_DIVISION;
    	$this->m_strCIName           = 'division';
    	$this->m_strFieldName_CIName = 'alias';

    	$this->m_strViewFolder4List    = 'base_ci';
    	$this->m_strViewFolder4Detail  = 'base_ci';

		parent::__construct();
		$this->load->helper('ci_division_helper');
		$this->load->model('ci_division_model', 'ci_model');
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
        $arrParam   = array ('cid', 'code', 'alias');
        
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
}
