<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';

class Index extends Base_controller {
	public function __construct()
    {
		parent::__construct();
		$this->load->model('custom_view_model', 'model');
	}

	public function index()
	{
		global $arrDefined;
		$nCIType = $_REQUEST['cid'];
		$oCI = $this->model->LoadCI($nCIType);

		$arrFieldList = array();
		$arrGroupList = array();
		if(!is_null($oCI)){
			$strUserName = $this->session->userdata('username');
			list($arrSelectedFieldName, $arrCustomViewDetail) = $this->model->GetSelectedFieldFromCustomView($strUserName, $nCIType);

			#pd($arrSelectedField);
			$this->index_table($oCI, $arrSelectedFieldName, $arrCustomViewDetail);
		}
	}

	public function save_custom_view(){
		$nCIType = $_POST['cid'];
		#pd($_POST['data']);
		$arrCIField = $this->model->ListFieldDisplayNameOfCI($nCIType);
		if(count($arrCIField)>0){
			$arrSelectedField = array();
			$arrGroupPos = array();

			$strUserName = $this->session->userdata('username');
			$arrGroupField = array();

			foreach($_POST['fields'] as $arrData){
				$arrOrderAndName = explode('|', $arrData['val']);
				if(array_key_exists($arrOrderAndName[1], $arrCIField)){
					$arrGroupField[$arrCIField[$arrOrderAndName[1]]['group_name']][] = array(
						'field_id'     => $arrCIField[$arrOrderAndName[1]]['field_id'],
						'field_name'   => $arrOrderAndName[1],
						'display_name' => $arrCIField[$arrOrderAndName[1]]['display_name'],
						'order'        => $arrOrderAndName[0],
						'is_show'      => (int)$arrData["checked"]
					);
				}
			}
			foreach(@$_POST['groups'] as $arrData){
				$arrGroupPos[$arrData['group']] = $arrData;
			}
			foreach($arrGroupField as $strGroupName=>$arrField){
				$arrTmp = array('group_name' => $strGroupName, 'fields' => $arrField);
				if(array_key_exists($strGroupName, $arrGroupPos)){
					$arrTmp['group_index'] = $arrGroupPos[$strGroupName]['index'];
					$arrTmp['group_col'] = $arrGroupPos[$strGroupName]['col'];
					$arrTmp['group_row'] = $arrGroupPos[$strGroupName]['row'];
				}
				$arrSelectedField[] = $arrTmp;
			}
			$this->model->SaveCustomView($nCIType, $strUserName, $arrSelectedField);
		}
	}

	private function index_table($oCI, $arrSelectedFieldName, $arrCustomeViewDetail){
		global $arrDefined;
		$nCIType = $_REQUEST['cid'];

		$arrGroupList = array();
		$arrDisplaySetting = $arrDefined['custom_view']['gui'][$oCI['ci_type']]['group_order'];
		$arrCol = array();
		foreach($oCI['group_fields'] as &$oGroupFields){
			$arrGroupList[] = $oGroupFields['group_name'];
			if(@$arrCustomeViewDetail['arrGroups'] && array_key_exists($oGroupFields['group_name'], $arrCustomeViewDetail['arrGroups'])){
				$oGroupFields['col'] = $arrCustomeViewDetail['arrGroups'][$oGroupFields['group_name']]['group_col'];
				$oGroupFields['row'] = $arrCustomeViewDetail['arrGroups'][$oGroupFields['group_name']]['group_row'];
			} else {
				$oGroupFields['col'] = $arrDisplaySetting[$oGroupFields['group_name']]['group_col'];
				$oGroupFields['row'] = $arrDisplaySetting[$oGroupFields['group_name']]['group_row'];
			}
			$arrCol[$oGroupFields['col']][$oGroupFields['row']] = $oGroupFields;
		}

		foreach($arrCol as &$arrRow){
			ksort($arrRow);
			foreach($arrRow as &$oGroup){
				$arrTMP = array();
				foreach($oGroup['fields'] as &$oField){
					$oField['selected'] = in_array($oField['field_name'], $arrSelectedFieldName)?1:0;
					$oField['order'] = (@$arrCustomeViewDetail['arrFields'] && array_key_exists($oField['field_name'], $arrCustomeViewDetail['arrFields'])) ? $arrCustomeViewDetail['arrFields'][$oField['field_name']]['order']:UNLIMITED;
					$arrTMP[$oField['display_name']] = $oField;
				}
				uasort($arrTMP, 'SortByOrder');
				$oGroup['fields'] = $arrTMP;
			}
		}

		foreach($arrCol as &$arrRow){
			ksort($arrRow);
		}
		ksort($arrCol);
		$nNumOfCol   = !@empty($oCI['customview_num_of_col'])?$oCI['customview_num_of_col']:$arrDefined['custom_view']['gui'][$nCIType]['col'];
		$this->loadview('custom_view/index_table',
			array(
				'nCIType'       => $nCIType,
				'oCI'           => $oCI,
				'nNumOfCol'     => $nNumOfCol,
				'arrCol'        => $arrCol,
				'arrGroup'      => $arrGroupList
			), 'layout_popup'
		);
	}
}
?>
