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
function SortByOrder($oField1, $oField2){
	$s1 = is_numeric($oField1['order'])?(int)$oField1['order']:$oField1['order'];
	$s2 = is_numeric($oField2['order'])?(int)$oField2['order']:$oField2['order'];

	if ($s1 == $s2) {
        return 0;
    }
    return ($s1 < $s2) ? -1 : 1;
}
// ---------------------------------------------------------------------------------------------- //
function ConvertToInt($arrValue){
    if (is_array($arrValue)) {
        foreach ($arrValue as $k=>$v) {
            $arrValue[$k] = intval($v);
        }
    }
    return $arrValue;
}
// ---------------------------------------------------------------------------------------------- //
function ConvertToFloat($arrValue){
    if (is_array($arrValue)) {
        foreach ($arrValue as $k=>$v) {
            $arrValue[$k] = floatval($v);
        }
    }
    return $arrValue;
}
// ---------------------------------------------------------------------------------------------- //
function ConvertToString($arrValue){
    if (is_array($arrValue)) {
        foreach ($arrValue as $k=>$v) {
            $arrValue[$k] = strval($v);
        }
    }
    return $arrValue;
}
// ---------------------------------------------------------------------------------------------- //
function ConvertToMongoId($arrValue){
    if (is_array($arrValue)) {
        foreach ($arrValue as $k=>$v) {
            $arrValue[$k] = new MongoId($v);
        }
    }
    return $arrValue;
}

