<?php global $arrDefined; ?>
<?php $strKeyword = htmlentities(trim(@$_REQUEST['k']), ENT_QUOTES, "UTF-8"); ?>
<?php $strEntitiesKeyword = htmlentities(trim(@$_REQUEST['k']), ENT_QUOTES, "UTF-8"); ?>
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/bootstrap/easyui.css" />
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/icon.css" />
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<h2 class="hostinfo-hostname"><?php echo htmlentities($strCIName, ENT_QUOTES, "UTF-8") ?></h2>
<?php if(@$arrDefined['ci_detail_display_setting'][$nCIType]['display_type'] == CI_DETAIL_DISPLAY_TYPE_TABS)/* Hiển thị group theo dạng tab hay dạng tuần tự ? */ {?>
<div class="easyui-tabs">
    <?php foreach($oCI as $strGroupField=>$arrFields){ ?>
    <div title="<?php echo htmlentities($strGroupField, ENT_QUOTES, "UTF-8") ?>" style="padding:10px" data-options="iconCls:'<?php if(@!empty($arrDefined['ci_detail_display_setting'][$nCIType]['groups_icon'][$strGroupField])){ echo $arrDefined['ci_detail_display_setting'][$nCIType]['groups_icon'][$strGroupField]; ?><?php } else {?>icon-help<?php } ?>'">
    	<table width="100%" cellpadding="0" cellspacing="0" class="detail-zebra td-bordered">
    	<?php foreach($arrFields as $strField=>$arrValue){?>
		<?php $strValue = @$arrValue['value']; $strFieldName = @$arrValue['field_name']; ?>
		<tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
			<td width="150" nowrap="nowrap" class="t-right" style="font-weight: bold;border-right: 0;"><?php echo htmlentities($strField, ENT_QUOTES, "UTF-8") ?>&nbsp;:</td>
			<?php if(@isset($arrDefined['ci_field_display_filter'][$nCIType][$strFieldName])) /* Field có sử dụng hàm filter trước khi show hay không ? */{

				if(!is_null($strValue) && $strValue !== '' && function_exists($arrDefined['ci_field_display_filter'][$nCIType][$strFieldName])){
					$strValue = call_user_func(/* CI_Server_Helper */$arrDefined['ci_field_display_filter'][$nCIType][$strFieldName], $strValue);
				}
				/* Nếu có search theo keyword thì đánh dấu vị trí của keyword để highligh */
				$nHighligtPos = ($strKeyword == '')?-1:((stripos($strValue,$strKeyword)!==FALSE)?stripos($strValue,$strKeyword):-1);
	    		if($nHighligtPos >=0) $strOldValue = mb_substr($strValue, $nHighligtPos, mb_strlen($strKeyword));
			?>
			<td style="border-left: 0"><?php if($nHighligtPos < 0) echo ($strValue); else echo (str_ireplace($strKeyword, '<span class="skeyword">'.$strOldValue.'</span>', $strValue)); ?></td>
			<?php } else /* Không sử dụng hàm filter nào */{
				$strEntitiesValue = htmlentities($strValue, ENT_QUOTES, "UTF-8");
				/* Nếu có search theo keyword thì đánh dấu vị trí của keyword để highlight */
				$nHighligtPos = ($strEntitiesKeyword == '')?-1:((stripos($strEntitiesValue,$strEntitiesKeyword)!==FALSE)?stripos($strEntitiesValue,$strEntitiesKeyword):-1);
				if($nHighligtPos >=0) $strOldValue = mb_substr($strEntitiesValue, $nHighligtPos, mb_strlen($strKeyword));
			?>
			<td style="border-left: 0"><?php if($nHighligtPos < 0) echo $strEntitiesValue; else echo (str_ireplace($strEntitiesKeyword, '<span class="skeyword">'.$strOldValue.'</span>', $strEntitiesValue)); ?></td>
			<?php } ?>
		</tr>
		<?php } ?>
    	</table>
    </div>
    <?php } ?>
</div>
<?php } else /* Hiển thị các group dạng tuần tự */ { ?>
<table width="100%" cellpadding="0" cellspacing="0" class="detail-zebra td-bordered">
	<?php foreach($oCI as $strGroupField=>$arrFields){ ?>
	<tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
		<th colspan="2" class="group-field"><?php echo htmlentities($strGroupField, ENT_QUOTES, "UTF-8") ?></th>
	</tr>
	<?php foreach($arrFields as $strField=>$arrValue){?>
	<?php $strValue = @$arrValue['value']; $strFieldName = @$arrValue['field_name']; ?>
	<tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
		<td width="150" nowrap="nowrap" class="t-right" style="font-weight: bold;border-right: 0;"><?php echo htmlentities($strField, ENT_QUOTES, "UTF-8") ?>&nbsp;:</td>
		<?php if(@isset($arrDefined['ci_field_display_filter'][$nCIType][$strFieldName])){
			if(!is_null($strValue) && $strValue !== '' && function_exists($arrDefined['ci_field_display_filter'][$nCIType][$strFieldName])){
				$strValue = call_user_func(/* CI_Server_Helper */$arrDefined['ci_field_display_filter'][$nCIType][$strFieldName], $strValue);
			}
			$nHighligtPos = ($strKeyword == '')?-1:((stripos($strValue,$strKeyword)!==FALSE)?stripos($strValue,$strKeyword):-1);
    		if($nHighligtPos >=0) $strOldValue = mb_substr($strValue, $nHighligtPos, mb_strlen($strKeyword)); pd($strOldValue);
		?>
		<td style="border-left: 0"><?php if($nHighligtPos < 0) echo ($strValue); else echo (str_ireplace($strKeyword, '<span class="skeyword">'.$strOldValue.'ssssssssssssss</span>', $strValue)); ?></td>
		<?php } else {
			$strEntitiesValue = htmlentities($strValue, ENT_QUOTES, "UTF-8");
			$nHighligtPos = ($strEntitiesKeyword == '')?-1:((stripos($strEntitiesValue,$strEntitiesKeyword)!==FALSE)?stripos($strEntitiesValue,$strEntitiesKeyword):-1);
			if($nHighligtPos >=0) $strOldValue = mb_substr($strEntitiesValue, $nHighligtPos, mb_strlen($strKeyword)); pd($strOldValue);
		?>
		<td style="border-left: 0"><?php if($nHighligtPos < 0) echo $strEntitiesValue; else echo (str_ireplace($strEntitiesKeyword, '<span class="skeyword">'.$strOldValue.'ssssssssssssss</span>', $strEntitiesValue)); ?></td>
		<?php } ?>
	</tr>
	<?php } ?>
	<?php } ?>
</table>
<?php } ?>