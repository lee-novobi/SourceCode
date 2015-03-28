<?php
function DisplayFilter_DivisionStatus($oValue)
{
	global $arrDefined;
	$strResult = STR_UNKNOWN;
	if(@isset($arrDefined['field_value_to_text']['ci'][CI_DIVISION]['status'][$oValue])){
		$strResult = $arrDefined['field_value_to_text']['ci'][CI_DIVISION]['status'][$oValue];
	}
	return $strResult;
}
?>
