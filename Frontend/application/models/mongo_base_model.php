<?php
/*
 * Created on Feb 27, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class Mongo_base_model extends CI_Model
{
 	var $cltCI                      = CLT_CI;
	var $cltServer                  = CLT_SERVER;
	var $cltProduct                 = CLT_PRODUCT;
	var $cltDepartment              = CLT_DEPARTMENT;
	var $cltDivision	            = CLT_DIVISION;
	var $cltCustomView              = CLT_CUSTOM_VIEW;
	var $cltInvertedIndexServer     = CLT_INVERTED_INDEX_SERVER;
	var $cltInvertedIndexProduct    = CLT_INVERTED_INDEX_PRODUCT;
	var $cltInvertedIndexDepartment = CLT_INVERTED_INDEX_DEPARTMENT;
	var $cltInvertedIndexDivision   = CLT_INVERTED_INDEX_DIVISION;
    var $cltTmpPhysicalServer       = CLT_TMP_PHYSICAL_SERVER;
    var $cltTmpVirtualServer        = CLT_TMP_VIRTUAL_SERVER;
    var $cltTmpProduct              = CLT_TMP_PRODUCT;
    var $cltTmpDepartment           = CLT_TMP_DEPARTMENT;
    var $cltTmpDivision             = CLT_TMP_DIVISION;

	var $oRawMongoConn              = null;
	var $arrCltIndexMap             = null;
	// ------------------------------------------------------------------------------------------ //
 	function __construct()
 	{
		parent :: __construct();

		if(!class_exists('Mongo_db'))
		{
			$this->load->library('mongo_db');
		}
		$this->mongo_db = new Mongo_db();
		$this->mongo_config_default  = $this->config->item('default');
		if(!is_null($this->mongo_db)){
			$this->oRawMongoConn = $this->mongo_db->get_connection();
		}
		$this->arrCltIndexMap = array(
			CI_SERVER	   => $this->cltInvertedIndexServer,
			CI_PRODUCT     => $this->cltInvertedIndexProduct,
			CI_DEPARTMENT  => $this->cltInvertedIndexDepartment,
			CI_DIVISION    => $this->cltInvertedIndexDivision
		);
	}
	// ------------------------------------------------------------------------------------------ //
	protected function BuildQueryConditions($arrCondition=null, $arrPagination=null, $arrSort=null)
	{
		if (!is_null($arrCondition) && is_array($arrCondition) && count($arrCondition) > 0)
		{
			$this->mongo_db->where($arrCondition);
		}
		if (!is_null($arrSort) && is_array($arrSort) && count($arrSort) > 0)
		{
			$this->mongo_db->order_by($arrSort);
		}
		if (isset($arrPagination['limit']))
		{
			$this->mongo_db->limit($arrPagination['limit']);
		}
		if (isset($arrPagination['offset']))
		{
			$this->mongo_db->offset($arrPagination['offset']);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	function GetActiveTables()
	{
		$arr_activeTables = $this->mongo_db->get($this->tblActiveTable);
		$arr_tmp = array();
		foreach($arr_activeTables as &$tbl)
		{
			$arr_tmp[$tbl['name']] = $tbl;
		}
		$arr_activeTables = $arr_tmp;
		ksort($arr_activeTables);


		$this->tblHosts        = $arr_activeTables[TBL_HOSTS]['active'];
		$this->tblItems        = $arr_activeTables[TBL_ITEMS]['active'];
	}
	// ------------------------------------------------------------------------------------------ //
	public function LoadCI($nCIType)
	{
		global $arrDefined;
		$oRs = $this->SelectOneMongoDB(array('ci_type' => (int)$nCIType, 'deleted' => 0), $this->cltCI);

		if(!empty($oRs)){
			if(!@empty($arrDefined['ignore_ci_field'][$nCIType]['private_fields'])){
				$oFilteredResult = $oRs;
				$oFilteredResult['group_fields'] = array();
				foreach($oRs['group_fields'] as $oGroupField){
					$arrFields = array();
					foreach($oGroupField['fields'] as $oField){
						if(!in_array($oField['field_name'], $arrDefined['ignore_ci_field'][$nCIType]['private_fields'])){
							$arrFields[] = $oField;
						}
					}
					$oGroupField['fields'] = $arrFields;
					$oFilteredResult['group_fields'][] = $oGroupField;
				}
				$oRs = $oFilteredResult;
			}
		}

		return $oRs;
	}
    // ------------------------------------------------------------------------------------------ //
	public function LoadCIByName($nCIName)
	{
		$oRs = $this->SelectOneMongoDB(array('ci_name' => $nCIName, 'deleted' => 0), $this->cltCI);
		return $oRs;
	}
	// ------------------------------------------------------------------------------------------ //
	function GetSelectedFieldFromCustomView($strUserName, $nCIType){
		global $arrDefined;
		$arrFieldName = array();
		$arrCustomViewDetail = array();
		$arrGroupPositionSetting = array();
		$isNoCustome = false;

		$oRs = $this->SelectOneMongoDB(
			array('username' => new MongoRegex('/^' . $strUserName . '$/i'), 'ci_type' => (int)$nCIType, 'deleted' => 0),
			$this->cltCustomView
		);
		#pd($oRs);
		if(is_null($oRs)){
			$oRs = $this->LoadCI($nCIType);
			$isNoCustome = true;
		}
		if(!@empty($oRs['group_fields'])){
			foreach($oRs['group_fields'] as $oGroupFields){
				if(!array_key_exists('group_index', $oGroupFields)){
					$oDefaultGroupPos = $arrDefined['custom_view']['gui'][$nCIType]['group_order'];
					$oGroupPos = $oDefaultGroupPos[$oGroupFields['group_name']];
				} else {
					$oGroupPos = $oGroupFields;
					unset($oGroupPos['fields']);
				}
				$arrCustomViewDetail['arrGroups'][$oGroupFields['group_name']] = $oGroupPos;

				if (!@empty($oGroupFields['fields'])) {
					foreach($oGroupFields['fields'] as $oField){
						$oField['group_name'] = $oGroupFields['group_name'];
						if($isNoCustome){
							$oField['order'] = $oField['display_name'];
						}
						if($isNoCustome){
							$oField['is_show'] = 1;
						} else {
							if(!array_key_exists('is_show', $oField)){
								$oField['is_show'] = 0;
							}
						}
						if($oField['is_show'] == 1){
							$arrFieldName[$oField['display_name']] = $oField['field_name'];
						}
						$arrCustomViewDetail['arrFields'][$oField['field_name']] = $oField;
					}
					#usort($arrFieldDetail[$oGroupFields['group_name']], 'SortByOrder');
				}

			}
		}
		/*foreach($arrFieldDetail as $strGroupName => $arrField){
			foreach($arrField as $oField){
				if($oField['is_show'] == 1){
					$arrFieldName[$oField['display_name']] = $oField['field_name'];
				}
				$arrFieldDetail[$oField['display_name']] = $oField;
			}
		}*/
		#if($oGroupFields['group_name'] == 'Location Information')
			#pd($arrFieldDetail[$oGroupFields['group_name']]);
		#pd($arrCustomViewDetail);
		#ksort($arrResult);
		#pd($arrResult);
		return array($arrFieldName, $arrCustomViewDetail);
	}
	// ------------------------------------------------------------------------------------------ //
	function ListFieldDisplayNameOfCI($nCIType){
		$arrResult = array();

		$oCI = $this->LoadCI($nCIType);
		#pd($oRs);
		if(!is_null($oCI)){
			foreach($oCI['group_fields'] as $oGroupFields){
				foreach($oGroupFields['fields'] as $oField){
					$arrResult[$oField['field_name']] = array('group_name' => $oGroupFields['group_name'], 'display_name' => $oField['display_name'], 'field_id' => $oField['field_id']);
				}
			}
		}

		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function SearchKeyword($nCIType, $strKeyword, $arrFilterCondition=array()){

	    $arrRs = array();
		$arrResult = array();
		@$strCltName = $this->arrCltIndexMap[$nCIType];
		if(!empty($strCltName)){
		  if (!empty($arrFilterCondition)) {
		      $arrKeywordCondition = array('key' => new MongoRegex('/' . preg_quote ($strKeyword) . '/i'));
              $arrTotalCondition = array_merge($arrKeywordCondition, $arrFilterCondition);
                                   vd($arrTotalCondition);
		      $arrRs = $this->SelectMongoDB(array($arrTotalCondition), $strCltName);
		  }
          else {
            $arrRs = $this->SelectMongoDB(array('key' => new MongoRegex('/' . preg_quote ($strKeyword) . '/i')), $strCltName);
          }

			foreach($arrRs as $oMatched){
				#pd($oMatched);
				foreach($oMatched['info'] as $oInfo){
					#pd((string)$oInfo['id']);
					$arrResult[(string)$oInfo['id']][] = $oInfo['field'];
				}
			}
		}
        
        //vd($arrResult);
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListProducts()
	{
		$raw = $this->mongo_db->select(array('productid', 'code'))->order_by(array('code'=> 1))
				->get($this->tblProducts);

		return $raw;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListOSName()
	{
		$result = array();
		$raw = $this->mongo_db->select(array('os_type'), array('_id'))
				->order_by(array('os_type'=> 1))
				->get($this->tblHosts);
		foreach($raw as $rs)
		{
			if(isset($rs['os_type']) && $rs['os_type'] != '')
			{
				if(@!in_array($rs['os_type'], $result)) $result[] = @$rs['os_type'];
			}
		}
		$result[] = 'Unknown';
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	public function IsCollectionExists($strCollectionName)
	{
		$rs = $this->mongo_db->where(
        	array(
				'name' => $this->mongo_config_default['mongo_database'] . '.' . $strCollectionName
			)
		)->get('system.namespaces');
		if (count($rs) > 0)
        	return true;
		else
			return false;
    }
	// ------------------------------------------------------------------------------------------ //
	protected function CheckValidCondition($arrCondition){
		$result = true;
		if(isset($arrCondition['$or']) && count($arrCondition['$or']) <= 0) $result = false;

		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	public function CountMongoDB($arrCondition, $strCollectionName){
		$nResult = 0;

		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				$nResult = $oCollection->find($arrCondition)->count();
			}
		}

		return $nResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function SelectOneMongoDB($arrCondition, $strCollectionName, $arrSelectField=array()){
		$oResult = null;

		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				if(empty($arrSelectField)){
					$oResult = $oCollection->findOne($arrCondition);
				} else {
					$oResult = $oCollection->findOne($arrCondition, $arrSelectField);
				}
			}
		}

		return $oResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function SelectMongoDB($arrCondition, $strCollectionName, $offset=0, $limit=UNLIMITED, $arrSort=array(), $arrSelectField=array()){
		$arrResult = array();

		$oMgConn = $this->mongo_db->get_connection();
		if(!is_array($arrSort)) $arrSort = array();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				if(empty($arrSelectField)){
					$oCursor = $oCollection->find($arrCondition)->skip($offset)->limit($limit)->sort($arrSort);
				} else {
					$oCursor = $oCollection->find($arrCondition, $arrSelectField)->skip($offset)->limit($limit)->sort($arrSort);
				}
				foreach($oCursor as $oDoc){
					$arrResult[] = $oDoc;
				}
			}
		}

		return $arrResult;
	}

	/* -------------------------------------------------------------------------
	 * UpdateMongoDB
	 * -------------------------------------------------------------------------
	 * Function to update MongoDB
	 *
	 * @param: $arrCondition, $arrNewData, $strCollectionName, $arrOptions
	 * @return: boolean (true if success, false if error)
	 *
	 * Note: $arrRes contains:
	 * 	'updatedExisting' => boolean false
	 * 	'n' => int 1
	 * 	'connectionId' => int 1299857
	 * 	'err' => null
	 * 	'ok' => float 1
	 * 	'upserted' =>
	 * 		object(MongoId)[28]
	 * 		public '$id' => string '5269e6bab10d137e80248d45' (length=24)
	 *
	 */


	public function UpdateMongoDB($arrCondition, $arrNewData, $strCollectionName, $arrOptions=array('multiple' => true)){
		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				$arrRes = $oCollection->update($arrCondition, array('$set' => $arrNewData), $arrOptions);
				if (isset($arrRes['ok']) && intval($arrRes['ok']) == 1 &&  intval($arrRes['n']) != 0)
				{
					return true;
				}
				else
				{
					$errMsg = (!is_null($arrRes['err'] )) ? $arrRes['err'] : 'Error update MongoDB';
					log_message('error', sprintf('Error Message: %s', $errMsg));
					return false;
				}
			}
		}
		return false;
	}


	/* -------------------------------------------------------------------------
	 * InsertMongoDB
	 * -------------------------------------------------------------------------
	 * Function to insert data into MongoDB
	 *
	 * @param: $arrNewData, $strCollectionName
	 * @return: boolean (true if success, false if error)
	 *
	 * Note: $arrRes contains:
	 * 	'n' => int 0
	 * 	'connectionId' => int 1299857
	 * 	'err' => null
	 * 	'ok' => float 1
	 *
	 */

	public function InsertMongoDB($arrNewData, $strCollectionName){
		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				$arrRes = $oCollection->insert($arrNewData);
				if (isset($arrRes['ok']) && intval($arrRes['ok']) == 1)
				{
					return true;
				}
				else
				{
					$errMsg = (!is_null($arrRes['err'] )) ? $arrRes['err'] : 'Error insert MongoDB';
					log_message('error', sprintf('Error Message: %s', $errMsg));
					return false;
				}
			}
		}
		return false;
	}
	// ------------------------------------------------------------------------------------------ //

	/* -------------------------------------------------------------------------
	 * BatchInsertMongoDB
	 * -------------------------------------------------------------------------
	 * Function to insert multiple documents into MongoDB
	 *
	 *
	 */
	public function BatchInsertMongoDB($arrData, $strCollectionName) {
		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				$arrRes = $oCollection->batchInsert($arrData);
				if (isset($arrRes['ok']) && intval($arrRes['ok']) == 1)
				{
					return true;
				}
				else
				{
					$errMsg = (!is_null($arrRes['err'] )) ? $arrRes['err'] : 'Error batchinsert MongoDB';
					log_message('error', sprintf('Error Message: %s', $errMsg));
					return false;
				}
			}
		}
		return false;
	}
	/* -------------------------------------------------------------------------
	 * RemoveMongoDB
	 * -------------------------------------------------------------------------
	 * Function to remove documents that matches conditions in MongoDB
	 *
	 *
	 */
	public function RemoveMongoDB($arrConditions, $strCollectionName) {
		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				$arrRes = $oCollection->remove($arrConditions);
				#var_dump($arrRes);
				if (isset($arrRes['ok']) && intval($arrRes['ok']) == 1)
				{
					return true;
				}
				else
				{
					$errMsg = (!is_null($arrRes['err'] )) ? $arrRes['err'] : 'Error batchinsert MongoDB';
					log_message('error', sprintf('Error Message: %s', $errMsg));
					return false;
				}
			}
		}
		return false;
	}
    
    /* -------------------------------------------------------------------------
	 * DistinctMongoDB
	 * -------------------------------------------------------------------------
	 * Function to select distinct in MongoDB
	 *
	 *
	 */
    public function DistinctMongoDB($strCollectionName, $strSelectField, $arrCondition=array()){
		$oResult = null;

		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				if(!empty($arrCondition)){
					$oResult = $oCollection->distinct($strSelectField, $arrCondition);
				} 
                else {
                    $oResult = $oCollection->distinct($strSelectField);
                }
			}
		}

		return $oResult;
	}
}
?>
