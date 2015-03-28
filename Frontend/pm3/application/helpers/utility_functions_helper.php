<?php
function StartWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}
// ---------------------------------------------------------------------------------------------- //
function EndWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}
// ---------------------------------------------------------------------------------------------- //
/**
 * case-insensitive startswith
 */
function IStartWith($haystack, $needle)
{
    return $needle === "" || stripos($haystack, $needle) === 0;
}
// ---------------------------------------------------------------------------------------------- //
/**
 * case-insensitive endswith
 */
function IEndWith($haystack, $needle)
{
    return $needle === "" || strcasecmp(substr($haystack, -strlen($needle)), $needle) === 0;
}
// ---------------------------------------------------------------------------------------------- //
function Empty2StrEmpty($strNeeded){
	return empty($strNeeded) ? '' : $strNeeded;
}
// ---------------------------------------------------------------------------------------------- //
function MongoCondObj($strKeyName, $oValue){
	global $arrDefined;

	if($strKeyName == '_id' && !is_object($oValue))
		$oValue = new MongoId($oValue);
	elseif(in_array($strKeyName, $arrDefined['key_type_text']))
		$oValue = new MongoRegex('/^' . preg_quote($oValue) . '$/i');

	return $oValue;
}
// ---------------------------------------------------------------------------------------------- //
function MongoSaveObj($strKeyName, $oValue){
	global $arrDefined;

	if($strKeyName == '_id' && !is_object($oValue))
		$oValue = new MongoId(trim($oValue));

	return $oValue;
}