<?php
require_once('config.php');
require_once('mongo_conn.php');
include_once('debug.php');

define('MAX_LOOP', 10000);
define('FIELD_KEY', 'key');
define('FIELD_INFO', 'info');

#p($arrIgnoreField[1]);
#var_dump(in_array((string)0, $arrIgnoreField[1]));
#die();

DropOldCollection(CI_TYPE_SERVER);
CreateNewCollection(CI_TYPE_SERVER);
$arrIndexData = Indexing(CI_TYPE_SERVER, ListServer());
#pd($arrIndexData);
InsertIndexToDB(CI_TYPE_SERVER, $arrIndexData);
// ---------------------------------------------------------------------------------------------------------------------- //
function CreateNewCollection($nCIType){
	global $oCMDB2DB, $arrCltIndexMap;
	$oClt = new MongoCollection($oCMDB2DB, $arrCltIndexMap[$nCIType]);
	$oClt->ensureIndex(array(FIELD_KEY => 1));
}
// ---------------------------------------------------------------------------------------------------------------------- //
function DropOldCollection($nCIType){
	global $oCMDB2DB, $arrCltIndexMap;
	$oClt = new MongoCollection($oCMDB2DB, $arrCltIndexMap[$nCIType]);
	$oClt->drop();
}
// ---------------------------------------------------------------------------------------------------------------------- //
function ListServer(){
	global $oCMDB2DB;

	$arrServer = array();
	$oClt = new MongoCollection($oCMDB2DB, 'server');
	$oRs = $oClt->find(array('deleted' => 0));
	foreach($oRs as $oDoc){
		$arrServer[] = $oDoc;
	}

	return $arrServer;
}
// ---------------------------------------------------------------------------------------------------------------------- //
function Indexing($nCIType, $arrCI){
	global $arrIgnoreField;

	$arrIndex = array();
	foreach($arrCI as $oCI){
		$nDeadLoopKeeper = 0;
		$arrStack = array();
		$arrStack[] = array(
			'id'    => $oCI['_id'],
			'field' => '',
			'value'	=> $oCI
		);

		while(count($arrStack) > 0 && $nDeadLoopKeeper < MAX_LOOP){
			$oObject = array_pop($arrStack);
			foreach($oObject['value'] as $strField=>$oValue){
				#if($nDeadLoopKeeper == 1) p($strField);
				if(!in_array((string)$strField, $arrIgnoreField[$nCIType])){
					#if($nDeadLoopKeeper == 1) pd($strField);
					if(is_object($oValue)) $oValue = (array)$oValue;
					$field = (empty($oObject['field']))?$strField:($oObject['field'] . '.' . $strField);
					if(is_array($oValue)){
						$arrStack[] = array(
							'id'    => $oObject['id'],
							'field' => $field,
							'value'	=> $oValue
						);
					} else {
						$arrIndex[$oValue][] = array('id' => $oObject['id'], 'field' => $field);
					}
				}

				#if($strField == 'interface') pd($arrStack);
				
			}
			#p($arrStack);
			$nDeadLoopKeeper++;
		}
	}

	$arrInsertToDB = array();
	foreach($arrIndex as $strKey=>$arrInfo){
		$arrInsertToDB[] = array(
			FIELD_KEY  => $strKey,
			FIELD_INFO => $arrInfo
		);
	}

	return $arrInsertToDB;
}
// ---------------------------------------------------------------------------------------------------------------------- //
function InsertIndexToDB($nCIType, $arrData){
	global $oCMDB2DB, $arrCltIndexMap;
	$oClt = new MongoCollection($oCMDB2DB, $arrCltIndexMap[$nCIType]);
	$oClt->batchInsert($arrData);
}
// ---------------------------------------------------------------------------------------------------------------------- //
var_dump($oCMDB2DB->lastError());
include_once('close_mongo_conn.php');
?>