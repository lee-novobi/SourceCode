<?php
function DisplayFilter_ProductStatus($oValue)
{
	global $arrDefined;
	$strResult = STR_UNKNOWN;
	if(@isset($arrDefined['field_value_to_text']['ci'][CI_PRODUCT]['status'][$oValue])){
		$strResult = $arrDefined['field_value_to_text']['ci'][CI_PRODUCT]['status'][$oValue];
	}
	return $strResult;
}
?>
