<?php global $arrDefined ?>
<?php $strKeyword = (trim(@$_REQUEST['k'])); ?>
<?php $strEntitiesKeyword = htmlentities(trim(@$_REQUEST['k'])); ?>
<table id="tbCIList" width="100%" cellpadding="0" cellspacing="0" class="list-zebra td-bordered">
    <thead>
        <tr class="table-title">
            <th class="wp40 t-center">Detail</th>
            <?php foreach($arrSelectField as $strDisplayName=>$strFieldName){ ?>
            <th class="t-center"><?php echo htmlentities($strDisplayName) ?></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
    	<?php foreach($arrCI as $oCI){ ?>
        <tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
            <td class="t-center" nowrap="nowrap">
                <a title="Detail" class="server_detail_link btn btn-info" onclick="PopUpCIDetail('<?php echo $base_url . $strCIDetailURL ?>?cid=<?php echo $oCI['_id'] ?>')" href="#">
                    <img width="20px" src="<?php echo $base_url?>asset/images/icons/metro/dark/appbar.magnify.png" />
                </a>
                <!-- <a title="Edit" class="btn btn-success" href="#">
                    <img width="18px" src="<?php echo $base_url?>asset/images/icons/metro/dark/appbar.edit.png" />
                </a>
                <a title="Delete" class="btn btn-danger" href="#">
                    <img width="18px" src="<?php echo $base_url?>asset/images/icons/metro/dark/appbar.delete.png" />
                </a> -->
            </td>
    		<?php foreach($arrSelectField as $strFieldName){ ?>
    		<?php if(@isset($arrDefined['ci_field_display_filter'][$nCIType][$strFieldName])){
    				if(@$oCI[$strFieldName] != '' && function_exists($arrDefined['ci_field_display_filter'][$nCIType][$strFieldName])){
    					$oCI[$strFieldName] = call_user_func(/* CI_<CI>_Helper */$arrDefined['ci_field_display_filter'][$nCIType][$strFieldName], $oCI[$strFieldName]);
    				}
    				$nHighligtPos = ($strKeyword == '')?-1:((stripos(@$oCI[$strFieldName],$strKeyword)!==FALSE)?stripos($oCI[$strFieldName],$strKeyword):-1);
    				if($nHighligtPos >=0) $strOldValue = substr($oCI[$strFieldName], $nHighligtPos, mb_strlen($strKeyword));
    		?>
    		<td class="t-left" nowrap="nowrap"><?php if($nHighligtPos < 0) echo (@$oCI[$strFieldName]); else echo (str_ireplace($strKeyword, '<span class="skeyword">'.$strOldValue.'</span>', @$oCI[$strFieldName])); ?></td>
    		<?php } else {
    				$strEntitiesValue = htmlentities(@$oCI[$strFieldName]);
    				$nHighligtPos = ($strEntitiesKeyword == '')?-1:((stripos($strEntitiesValue,$strEntitiesKeyword)!==FALSE)?stripos($strEntitiesValue,$strEntitiesKeyword):-1);
    				if($nHighligtPos >=0) $strOldValue = substr($strEntitiesValue, $nHighligtPos, mb_strlen($strKeyword));
    		?>
    		<td class="t-left" nowrap="nowrap"><?php if($nHighligtPos < 0) echo $strEntitiesValue; else echo (str_ireplace($strEntitiesKeyword, '<span class="skeyword">'.$strOldValue.'</span>', $strEntitiesValue)); ?></td>
        	<?php } ?><?php } ?>
        </tr>
        <?php } ?>
    </tbody>
</table>
