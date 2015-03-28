<?php
/*
 * Created on Feb 27, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
abstract class Mongo_base_model extends CI_Model
{
	var $m_strLastErrorMsg = null;
	var $m_isLastError     = FALSE;

 	var $cltRoles                = CLT_ROLES;
 	var $cltUserGroups           = CLT_USER_GROUPS;
 	var $cltPermissionName       = CLT_PERMISSION_NAME;
 	var $cltPermissionNameGroups = CLT_PERMISSION_NAME_GROUPS;
 	var $cltRolePermissions      = CLT_ROLE_PERMISSIONS;

 	var $cltAdminRoles           = CLT_PM_ROLES;
 	var $cltAdminRolePermissions = CLT_PM_ROLE_PERMISSIONS;
	var $cltAdminPermissionName  = CLT_PM_PERMISSION_NAME;
	var $cltAdminUsersRole       = CLT_PM_USERS_ROLE;
	// ------------------------------------------------------------------------------------------ //
 	function __construct($config = 'default')
 	{
		parent :: __construct($config);
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
	function SearchKeyword($nCIType, $strKeyword){
		$arrResult = array();
		@$strCltName = $this->arrCltIndexMap[$nCIType];

		if(!empty($strCltName)){
			$arrRs = $this->SelectMongoDB(array('key' => new MongoRegex('/' . preg_quote ($strKeyword) . '/i')), $strCltName);
			foreach($arrRs as $oMatched){
				#pd($oMatched);
				foreach($oMatched['info'] as $oInfo){
					#pd((string)$oInfo['id']);
					$arrResult[(string)$oInfo['id']][] = $oInfo['field'];
				}
			}
		}
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function IsCollectionExists($strCollectionName)
	{
		$rs = $this->mongo_db->where(
        	array(
				'name' => $this->mongo_config_pm3['mongo_database'] . '.' . $strCollectionName
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
		$this->ResetLastError();
		$nResult = 0;

		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_pm3['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				$nResult = $oCollection->find($arrCondition)->count();
			}
		}

		return $nResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function SelectOneMongoDB($arrCondition, $strCollectionName, $arrSelectField=array()){
		$this->ResetLastError();
		$oResult = null;

		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_pm3['mongo_database'],
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
		$this->ResetLastError();
		$arrResult = array();

		$oMgConn = $this->mongo_db->get_connection();
		if(!is_array($arrSort)) $arrSort = array();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_pm3['mongo_database'],
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

	// ------------------------------------------------------------------------------------------ //
	public function UpdateMongoDB($arrCondition, $arrNewData, $strCollectionName, $arrOptions=array('multiple' => true)){
		$this->ResetLastError();
		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_pm3['mongo_database'],
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

	// ------------------------------------------------------------------------------------------ //
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

	public function InsertMongoDB(&$arrNewData, $strCollectionName){
		$this->ResetLastError();
		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_pm3['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				$arrTMP = $arrNewData;
				$arrRes = $oCollection->insert($arrTMP);

				if (isset($arrRes['ok']) && intval($arrRes['ok']) == 1)
				{
					$arrNewData['_id'] = $arrTMP['_id'];
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
		$this->ResetLastError();
		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_pm3['mongo_database'],
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
	// ------------------------------------------------------------------------------------------ //
	/* -------------------------------------------------------------------------
	 * RemoveMongoDB
	 * -------------------------------------------------------------------------
	 * Function to remove documents that matches conditions in MongoDB
	 *
	 *
	 */
	public function RemoveMongoDB($arrConditions, $strCollectionName) {
		$this->ResetLastError();
		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_pm3['mongo_database'],
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
	// ------------------------------------------------------------------------------------------ //
	private function ResetLastError(){
		$this->m_isLastError     = false;
		$this->m_strLastErrorMsg = null;
	}
	// ------------------------------------------------------------------------------------------ //
	protected function TrackingUpdate($oOldObject, $oNewObject, $strCollectionName){

	}
	// ------------------------------------------------------------------------------------------ //
	protected function TrackingInsert($oOldObject, $oNewObject, $strCollectionName){

	}
}