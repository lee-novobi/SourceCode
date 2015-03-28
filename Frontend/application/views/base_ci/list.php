<?php global $arrDefined; ?>
<?php $strKeyword = htmlentities(trim(@$_REQUEST['k']), ENT_QUOTES, "UTF-8"); ?>
<?php $strEntitiesKeyword = htmlentities(trim(@$_REQUEST['k']), ENT_QUOTES, "UTF-8");?>
<table id="tbCIList" width="100%" cellpadding="0" cellspacing="0" class="list-zebra td-bordered">
    <thead>
        <tr class="table-title">
            <th class="wp40 t-center">Detail</th>
            <?php foreach($arrSelectField as $strDisplayName=>$strFieldName){ ?>
            <th class="t-center"><?php echo htmlentities($strDisplayName, ENT_QUOTES, "UTF-8") ?></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
    	<?php foreach($arrCI as $oCI){ ?>
        <tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
            <td class="t-center" nowrap="nowrap">
                <a title="Detail" class="server_detail_link" onclick="PopUpCIDetail('<?php echo $base_url . $strCIDetailURL ?>?cid=<?php echo $oCI['_id'] ?>&k=<?php echo urlencode($strKeyword) ?>')" href="#">
                    <span class="bullet-icon icon-view"></span>
                </a>
                <a title="Edit" class="server_detail_link" href="<?php echo $base_url.$strCIName ?>/index/update?cid=<?php echo $oCI['_id'] ?>">
                    <span class="bullet-icon icon-edit"></span>
                </a>
            </td>
    		<?php foreach($arrSelectField as $strFieldName){ ?>
    		<?php if(@isset($arrDefined['ci_field_display_filter'][$nCIType][$strFieldName])){
    				if(!is_null(@$oCI[$strFieldName]) && @$oCI[$strFieldName] !== '' && function_exists($arrDefined['ci_field_display_filter'][$nCIType][$strFieldName]))/* Field có sử dụng hàm filter trước khi show hay không ? */{
    					$oCI[$strFieldName] = call_user_func(/* CI_<CI>_Helper */$arrDefined['ci_field_display_filter'][$nCIType][$strFieldName], $oCI[$strFieldName]);
    				}
    				/* Nếu có search theo keyword thì đánh dấu vị trí của keyword để highligh */
    				$nHighligtPos = ($strKeyword == '')?-1:((stripos(@$oCI[$strFieldName],$strKeyword)!==FALSE)?mb_stripos($oCI[$strFieldName],$strKeyword):-1);
    				if($nHighligtPos >=0) $strOldValue = mb_substr($oCI[$strFieldName], $nHighligtPos, mb_strlen($strKeyword));
    		?>
    		<td class="t-left" nowrap="nowrap"><?php if($nHighligtPos < 0) echo (@$oCI[$strFieldName]); else echo (str_ireplace($strKeyword, '<span class="skeyword">'.$strOldValue.'</span>', @$oCI[$strFieldName])); ?></td>
    		<?php } else /* Field không sử dụng hàm filter nào trước khi show ? */ {
    				$strEntitiesValue = htmlentities(@$oCI[$strFieldName], ENT_QUOTES, "UTF-8");
    				/* Nếu có search theo keyword thì đánh dấu vị trí của keyword để highligh */
    				$nHighligtPos = ($strEntitiesKeyword == '')?-1:((stripos($strEntitiesValue,$strEntitiesKeyword)!==FALSE)?stripos($strEntitiesValue,$strEntitiesKeyword):-1);
    				if($nHighligtPos >=0) $strOldValue = mb_substr($strEntitiesValue, $nHighligtPos, mb_strlen($strKeyword));
    		?>
    		<td class="t-left" nowrap="nowrap"><?php if($nHighligtPos < 0) echo $strEntitiesValue; else echo (str_ireplace($strEntitiesKeyword, '<span class="skeyword">'.$strOldValue.'</span>', $strEntitiesValue)); ?></td>
        	<?php } ?><?php } ?>
        </tr>
        <?php } ?>
    </tbody>
</table>
