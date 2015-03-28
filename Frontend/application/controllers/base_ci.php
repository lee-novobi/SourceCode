<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';
abstract class BaseCIController extends Base_controller {
	var $m_strCIName;
	var $m_nCIType;
	var $m_strFieldName_CIName;
	var $m_strCIDetailURL;
	var $m_strAjaxCIListURL;

	var $m_strViewFolder4List   = null;
	var $m_strViewFolder4Detail = null;

	public function __construct()
    {
    	!empty($this->m_nCIType) or show_error('Please init ci in controller.');

		parent::__construct();
        $this->load->helper('utility_functions_helper');
        
		if(empty($this->m_strViewFolder4List)){
			$this->m_strViewFolder4List = $this->m_strCIName;
		}
		if(empty($this->m_strViewFolder4Detail)){
			$this->m_strViewFolder4Detail = $this->m_strCIName;
		}
		if(empty($this->m_strAjaxCIListURL)){
			$this->m_strAjaxCIListURL = $this->moduleName . '/' . (empty($this->subModuleName)?'index':$this->subModuleName) . '/ajax_ci_list';
		}
		if(empty($this->m_strCIDetailURL)){
			$this->m_strCIDetailURL = $this->moduleName . '/' . (empty($this->subModuleName)?'index':$this->subModuleName) . '/ajax_ci_detail';
		}

        if ($this->session->userdata('filter_'.$this->m_strCIName) == false)
        {
            $oCI = $this->mongo_base_model->LoadCI($this->m_nCIType);
            if (isset($oCI['filter'])) {
                $this->session->set_userdata('filter_'.$this->m_strCIName, $oCI['filter']);
            } else {
                $this->session->set_userdata('filter_'.$this->m_strCIName, array());
            } 
        }
       

	}

	public function index()
	{
		$this->ci_list();
	}
    
    protected function GetFilterOption($arrFilterCondition)
    {
        // cheat permission
        $this->GrantPermission();
        $arrFilter = $this->session->userdata('filter_'.$this->m_strCIName);
        foreach ($arrFilter as $strKey => $oVal){
            $arrOption = array();
            switch ($strKey)
            {
                case FILTER_BY_DIVISION_ALIAS:
                    $arrOption   = $this->session->userdata('arrOwnedDivision');
                    break;
                case FILTER_BY_DEPARTMENT_ALIAS:
                    $arrOption   = $this->session->userdata('arrOwnedDepartment');
                    break;
                case FILTER_BY_PRODUCT_ALIAS:
                    $arrOption   = $this->session->userdata('arrOwnedProduct');
                    break;
                case FILTER_BY_SITE:
                    $arrOption = array();
                    $arrRs = $this->mongo_base_model->DistinctMongoDB(CLT_SERVER, 'site');
                    foreach ($arrRs as $strSite)
                    {
                        $arrOption[] = array($oVal['value_field'] => $strSite);
                    }
                    break;
                default: 
                    break;
            }
            $arrFilter[$strKey]['options']  = !empty($arrOption) ? $arrOption : array();
            $arrFilter[$strKey]['selected'] =  !empty($arrFilterCondition[$strKey])                
                                                ? $arrFilterCondition[$strKey] : array();
        }
        return $arrFilter;
    }

    protected function GetFilterCondition()
    {
        $arrFilterCondition = array();

        $arrFilter = $this->session->userdata('filter_'.$this->m_strCIName);
        $arrFilterField = array_keys($arrFilter);

        $arrFilterCondition = $this->GetParameter($arrFilterField);

        foreach ($arrFilterCondition as $strKey=>$oValue)
        {
            switch ($arrFilter[$strKey]['db_data_type'])
            {
                case DATA_TYPE_INTEGER:
                    $arrFilterCondition[$strKey] = is_array($oValue) ? ConvertToInt($oValue): intval($arrFilterCondition[$strKey]);
                    break;
                case DATA_TYPE_FLOAT:
                    $arrFilterCondition[$strKey] = is_array($oValue) ? ConvertToFloat($oValue): floatval($arrFilterCondition[$strKey]);
                    break;
                case DATA_TYPE_STRING:
                    $arrFilterCondition[$strKey] = is_array($oValue) ? ConvertToString($oValue): strval($arrFilterCondition[$strKey]);
                    break;
                case DATA_TYPE_MONGOID:
                    $arrFilterCondition[$strKey] = is_array($oValue) ? ConvertToMongoId($oValue): new MongoId($arrFilterCondition[$strKey]);
                    break;
                default: 
                    break;
            }
        }

        return $arrFilterCondition;
    }
    
    protected function BuildHTMLFilter($arrFilterOption)
    {
    	$strFilterData = array(
			'arrFilter'       	  => $arrFilterOption,
			'base_url'	          => $this->config->item('base_url')
		);
		$strHTML = $this->parser->parse('template/filter', $strFilterData, TRUE);
        return $strHTML;
    }
    
    /* cheat function */
    protected function GrantPermission()
    {   
        return;
    }
    /* cheat function */
    
	public function ci_list(){
		#pd($_SERVER);
		$strKeyword             = trim(@$_REQUEST['k']);

		list($strHTMLCIList, $strHTMLFilter, $nCorrectedPage, $nTotalRecord, $nPageSize) = $this->BuilHTMLTableCIList();
       

		$this->loadview($this->m_strViewFolder4List . '/index',
			array(
				'nTotal'         => $nTotalRecord,
                'strHTMLFilter'  => $strHTMLFilter,
				'strHTMLCIList'  => $strHTMLCIList,
				'iPageSize'      => $nPageSize,
				'iTotalRow'      => $nTotalRecord,
				'iCurrentPage'   => $nCorrectedPage,
				'isAjax'         => false,
				'strKeyword'     => $strKeyword,
				'strURL_CI_Detail' => $this->m_strCIDetailURL,
				'nCIType'        => $this->m_nCIType,
				'strCIName'      => $this->m_strCIName,
				'strURLListData' => $this->m_strAjaxCIListURL
			)
		);
	}
    
    protected function GetConditionByKeywordAndFilter($strKeyword, $arrFilterCondition){
        $arrCondition = array();
   	    $arrKeyworCondition = array();
    	$arrID = array();
        #vd($arrFilterCondition);
		$arrMatched = $this->ci_model->SearchKeyword($this->m_nCIType, $strKeyword);
        #vd($arrMatched);
		foreach($arrMatched as $strKey=>$oMatched){
			$arrID[] = new MongoId($strKey);
		}
		$arrKeyworCondition['_id'] = array('$in' => $arrID);
        
        $arrCondition = array_merge($arrKeyworCondition, $arrFilterCondition);
      #  pd($arrCondition);
        return $arrCondition;
    }

	private function BuilHTMLTableCIList(){
		$nPageSize = @(isset($_REQUEST['ps']) && $_REQUEST['ps'] > 0)?(int)$_REQUEST['ps']:PAGER_SIZE;
		$nPage     = @(isset($_REQUEST['p']) && $_REQUEST['p'] > 0)?(int)$_REQUEST['p']:1;
		$arrCondition = array();
		$strKeyword = trim(@$_REQUEST['k']);
        
               // $this->GrantPermission();
        $arrFilterCondition     = $this->GetFilterCondition();
        $arrFilterOption        = $this->GetFilterOption($arrFilterCondition);
        $strHTMLFilter          = $this->BuildHTMLFilter($arrFilterOption);
        
       
        foreach ($arrFilterCondition as $strKey=>$arrValue) 
        {
            if (is_array($arrValue))
            {
                $arrCondition[$strKey] = array('$in' => $arrValue);
            }
        }
#vd($arrCondition);
		if($strKeyword != ''){
            $arrCondition = $this->GetConditionByKeywordAndFilter($strKeyword, $arrCondition);
		}
        
      // pd($arrCondition);
		$nTotal    = $this->ci_model->CountCI($arrCondition);
		$nMaxPage  = (int)ceil($nTotal/$nPageSize);
		$nPage     = ($nPage <= $nMaxPage)?$nPage:$nMaxPage;
		$nPage     = ($nPage>0)?$nPage:1;

		#pd($arrServerID);
#pd();
		list($arrCI, $arrSelectField) = $this->ci_model->ListCI($arrCondition, ($nPage-1)*$nPageSize, $nPageSize);
		#pd($arrCI);

		$strHTMLCIList = $this->load->view($this->m_strViewFolder4List . '/list',
				array(
					'arrCI'				=> $arrCI
					, 'arrSelectField'  => $arrSelectField
					, 'row_alternate'	=> false
					, 'base_url'		=> base_url()
					, 'strCIDetailURL'  => $this->m_strCIDetailURL
					, 'nCIType'         => $this->m_nCIType
                    , 'strCIName'       => $this->m_strCIName
				), true
		);

		return array($strHTMLCIList, $strHTMLFilter, $nPage, $nTotal, $nPageSize);
	}

	public function ajax_ci_list(){
		list($strHTMLCIList, $nCorrectedPage, $nTotalRecord, $nPageSize) = $this->BuilHTMLTableCIList();
		$this->loadview($this->m_strViewFolder4List . '/index',
			array(
				'nTotal'           => $nTotalRecord,
				'strHTMLCIList'    => $strHTMLCIList,
				'iPageSize'        => $nPageSize,
				'iTotalRow'        => $nTotalRecord,
				'iCurrentPage'     => $nCorrectedPage,
				'isAjax'           => true,
				'strURL_CI_Detail' => $this->m_strCIDetailURL,
				'nCIType'          => $this->m_nCIType,
                'strCIName'       => $this->m_strCIName
			), 'layout_ajax'
		);
	}

	public function ajax_ci_detail(){
		global $arrDefined;
		$nCID = @$_REQUEST['cid'];

		$strCIName = '';

		$oResult = array();
		if(!empty($nCID)){
			$oCIRaw = $this->ci_model->LoadCIDetail($nCID);

			$strCIName = @$oCIRaw[$this->m_strFieldName_CIName];
			if(!is_null($oCIRaw)){
				$oCI = $this->ci_model->LoadCI($this->m_nCIType);

				if(!is_null($oCI)){
					foreach($oCI['group_fields'] as $oGroupFields){
						foreach($oGroupFields['fields'] as $oField){
							if(array_key_exists($oField['field_name'], $oCIRaw)){
								$oResult[$oGroupFields['group_name']][$oField['display_name']] = array('value' => $oCIRaw[$oField['field_name']], 'field_name' => $oField['field_name']);
								unset($oCIRaw[$oField['field_name']]);
							}
						}
					}
					ksort($oResult);
					if(count($oCIRaw) > 0){
						foreach($oCIRaw as $strFieldName=>$oValue){
							if(!@empty($arrDefined['ignore_ci_field'][$this->m_nCIType]['meta_fields'])
							&& !in_array($strFieldName, $arrDefined['ignore_ci_field'][$this->m_nCIType]['meta_fields'])){
								$oResult['Group Unknown'][$strFieldName] = array('value' => $oValue, 'field_name' => $strFieldName);
							}
						}
					}
					foreach($oResult as &$arrFields){
						ksort($arrFields);
					}
				}
			}
		}

		$this->loadview($this->m_strViewFolder4Detail . '/detail',
			array(
				'oCI'              => $oResult,
				'strCIName'        => $strCIName,
				'isAjax'           => true,
				'strURL_CI_Detail' => $this->m_strCIDetailURL,
				'nCIType'          => $this->m_nCIType
			), 'layout_popup'
		);
	}

    protected function add_ci()
    {
        $arrCIInfoOption        = $this->GetCIInfoOptionForAdd();
        $arrPreviousInputData   = $this->session->flashdata('previous_input_data');
        $arrError               = $this->session->flashdata('error');
        if ($arrPreviousInputData && !empty($arrPreviousInputData))
        {
            $this->GetDependentCIInfoOption($arrPreviousInputData, $arrCIInfoOption);
        }
        //vd($arrCIInfoOption);
        $this->loadview($this->m_strCIName . '/add',
            array(
                    'arrCIInfoOption'       => $arrCIInfoOption
                , 'arrPreviousInputData'    => $arrPreviousInputData
                , 'arrError'                => $arrError
                , 'strCIName'               => $this->m_strCIName
            )
        );
    }

    protected function add_ci_submit()
    {
        $oResult = $this->GetAddedCIInfo();
        $this->session->set_flashdata('previous_input_data', $oResult['data']);
        $this->session->set_flashdata('error', $oResult['error']);
        if ($oResult['is_valid'])
        {
            $arrCIInfo = $oResult['data'];

            $oRs = $this->InsertCI($arrCIInfo);
            if ($oRs)
			{
                $oInsertedCI = $this->ci_model->GetCI($arrCIInfo);
                $this->InsertTMPCI(array(), $oInsertedCI[0], ACTION_TYPE_INSERT);
                $this->session->set_flashdata('msg','Success! New '.$this->m_strCIName.' has been added!');
				$this->session->set_flashdata('type_msg','success');
			}
			else
			{
				$this->session->set_flashdata('msg','Error! Can\'t add new '.$this->m_strCIName.' due to database error!');
				$this->session->set_flashdata('type_msg','error');
			}

			if ($this->input->post('save_and_exit')) {
				header('Location: '.base_url().$this->m_strCIName.'/index');
				exit;
			}
        }
        else
        {
            $this->session->set_flashdata('msg','Error! Can\'t add new '.$this->m_strCIName.' due to following errors!');
			$this->session->set_flashdata('type_msg','error');
        }
        header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
    }

    protected function GetCIInfoOptionForAdd()
    {
        return array();
    }

    protected function GetAddedCIInfo()
    {
        return array();
    }

    protected function GetUpdatedCIInfo()
    {
        return array();
    }

    protected function VerifyAddedCIInfo($arrCIInfo)
    {
        return array();
    }

    protected function GetDependentCIInfoOption($arrPreviousInputData, &$arrCIInfoOption)
    {
        return;
    }

    protected function InsertCI(&$arrCIInfo)
    {
        return true;
    }

    protected function UpdateCI($arrOldCIInfo, &$arrNewCIInfo, $arrCondition)
    {
        return true;
    }

    protected function GetCIInfoOptionForUpdate()
    {
        return array();
    }

    protected function update_ci()
    {
        $oCI = null;
        $arrCIInfoOption = array();
        $arrPreviousInputData   = $this->session->flashdata('previous_input_data');
        $arrError               = $this->session->flashdata('error');
        $strCIId                = $this->input->get('cid');

        if ($strCIId != "")
        {
            $oCI = $this->ci_model->GetCIById($strCIId);
            if ($oCI)
            {
                $arrCIInfoOption        = $this->GetCIInfoOptionForUpdate($oCI);
                #vd($arrCIInfoOption);
                $this->loadview($this->m_strCIName . '/update',
                    array(
                            'arrPreviousInputData'  => $arrPreviousInputData
                            , 'arrCIInfoOption'     => $arrCIInfoOption
                            , 'oCI'                 => $oCI
                            , 'arrError'            => $arrError
                            , 'strCIName'           => $this->m_strCIName
                    )
                );
            }
        }
    }

    protected function update_ci_submit()
    {
        $oResult = $this->GetUpdatedCIInfo();
       // vd($oResult);
        $this->session->set_flashdata('previous_input_data', $oResult['data']);
        $this->session->set_flashdata('error', $oResult['error']);
        if ($oResult['is_valid'])
        {
            $arrNewCIInfo = $oResult['data'];
            $arrCondition = array('_id' => new MongoId($arrNewCIInfo['cid']));
            $arrOldCIInfo = $this->ci_model->GetCIById($arrNewCIInfo['cid']);
            
            if (!empty($arrOldCIInfo))
            {

                $oRs = $this->UpdateCI($arrOldCIInfo, $arrNewCIInfo, $arrCondition);
                if ($oRs)
    			{
    			     $this->InsertTMPCI($arrOldCIInfo, $arrNewCIInfo, ACTION_TYPE_UPDATE);
    				 $this->session->set_flashdata('msg','Success! '.ucfirst($this->m_strCIName).' has been updated!');
    				 $this->session->set_flashdata('type_msg','success');
    			}
    			else
    			{
    				$this->session->set_flashdata('msg','Error! Can\'t update '.($this->m_strCIName).' due to database error!');
    				$this->session->set_flashdata('type_msg','error');
    			}

    			if ($this->input->post('save_and_exit')) {
    				header('Location: '.base_url().$this->m_strCIName.'/index');
    				exit;
    			}
            }

        }
        else
        {
            $this->session->set_flashdata('msg','Error! Can\'t update '.$this->m_strCIName.' due to following errors!');
			$this->session->set_flashdata('type_msg','error');
        }
        header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
    }
    
    protected function InsertTMPCI($arrOldCIInfo, $arrNewCIInfo, $iActionType)
    {
        $oTmpCIInfo = array();
        if ($iActionType == ACTION_TYPE_UPDATE)
        {
            $oTmpCIInfo                 = $arrNewCIInfo;
            $oTmpCIInfo['ci_id']        = $arrOldCIInfo['_id'];
            $oTmpCIInfo['action_type']  = $iActionType;
            $oTmpCIInfo['change_by']    = $this->session->userdata('username');
            $oTmpCIInfo['clock']        = time();
            $oTmpCIInfo['old']          = $arrOldCIInfo;
            $oTmpCIInfo['old_src']      = TMP_SOURCE_FRONT_END;
            $oTmpCIInfo['deleted']      = NO;
        }
        elseif ($iActionType == ACTION_TYPE_INSERT)
        {
            $oTmpCIInfo                 = $arrNewCIInfo;
            $oTmpCIInfo['ci_id']        = $arrNewCIInfo['_id'];
            $oTmpCIInfo['action_type']  = $iActionType;
            $oTmpCIInfo['change_by']    = $this->session->userdata('username');
            $oTmpCIInfo['clock']        = time();
            $oTmpCIInfo['old_src']      = TMP_SOURCE_DEFAULT;
            $oTmpCIInfo['deleted']      = NO;
        }
        
        $oRs = $this->ci_model->InsertTmpCI($oTmpCIInfo);
        return $oRs;
    }
}
?>
