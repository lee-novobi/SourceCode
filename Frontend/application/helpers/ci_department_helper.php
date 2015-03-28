<?php
function DisplayFilter_DepartmentStatus($oValue)
{
	global $arrDefined;
	$strResult = STR_UNKNOWN;
	if(@isset($arrDefined['field_value_to_text']['ci'][CI_DEPARTMENT]['status'][$oValue])){
		$strResult = $arrDefined['field_value_to_text']['ci'][CI_DEPARTMENT]['status'][$oValue];
	}
	return $strResult;
}
?>
