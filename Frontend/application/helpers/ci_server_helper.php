<?php
function DisplayFilter_ServerInterface($arrInterface){
	$arrTMP = array();
	if(is_array($arrInterface)){
		foreach($arrInterface as $oInf){
			$arrTMP[] = sprintf('[MAC:%s;IP:%s;VLAN:%s]', htmlentities(@$oInf['mac_address']), htmlentities(@$oInf['ip']), htmlentities(@$oInf['vlan']));
		}
	}

	return implode('<br />', $arrTMP);
}

function DisplayFilter_ServerType($oValue)
{
	global $arrDefined;
	$strResult = STR_UNKNOWN;
	if(@isset($arrDefined['field_value_to_text']['ci'][CI_SERVER]['server_type'][$oValue])){
		$strResult = $arrDefined['field_value_to_text']['ci'][CI_SERVER]['server_type'][$oValue];
	}
	return $strResult;
}
function DisplayFilter_ServerStatus($oValue)
{
	global $arrDefined;
	$strResult = STR_UNKNOWN;
	if(@isset($arrDefined['field_value_to_text']['ci'][CI_SERVER]['status'][$oValue])){
		$strResult = $arrDefined['field_value_to_text']['ci'][CI_SERVER]['status'][$oValue];
	}
	return $strResult;
}
function DisplayFilter_ServerPowerStatus($oValue)
{
	global $arrDefined;
	$strResult = STR_UNKNOWN;
	if(@isset($arrDefined['field_value_to_text']['ci'][CI_SERVER]['power_status'][$oValue])){
		$strResult = $arrDefined['field_value_to_text']['ci'][CI_SERVER]['power_status'][$oValue];
	}
	return $strResult;
}
function DisplayFilter_ServerMemorySize($oValue)
{
	if($oValue != '' && $oValue > 0){
		if(is_numeric($oValue)){
			$oValue = $oValue . 'GB';
		}
	}

	return $oValue;
}
function DisplayFilter_ServerFromUnixTime($oValue)
{
	if($oValue != '' && $oValue > 0){
		if(is_numeric($oValue)){
			#$oValue = date('Y-m-d', $oValue) . '<br />' . date('H:i:s', $oValue);
			$oValue = date('Y-m-d H:i:s', $oValue);
		}
	} else {
		$oValue = '';
	}

	return $oValue;
}
function DisplayFilter_ServerNote($oValue){
	if($oValue != ''){
		//$oValue = utf8_decode($oValue);
		//if(mb_detect_encoding($oValue) != 'UTF-8'){
			//$oValue = iconv("UTF-8", "CP1252//IGNORE", $oValue);
			//$oValue = iconv("CP1252", "UTF-8//IGNORE", $oValue);
		//}
		//$oValue = utf8_encode($oValue);
		//return mb_detect_encoding($oValue);
		//pd(mb_detect_order(array("CP1252")));
	}
	return $oValue;
}
?>
