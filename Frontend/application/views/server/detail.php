<?php global $arrDefined; ?>
<h2 class="hostinfo-hostname"><?php echo htmlentities($strServerName) ?></h2>
<table width="100%" cellpadding="0" cellspacing="0" class="detail-zebra td-bordered">
	<?php foreach($oServer as $strGroupField=>$arrFields){ ?>
	<tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
		<th colspan="2" class="group-field"><?php echo htmlentities($strGroupField) ?></th>
	</tr>
	<?php foreach($arrFields as $strField=>$arrValue){?>
	<?php $strValue = @$arrValue['value']; $strFieldName = @$arrValue['field_name']; ?>
	<tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
		<td width="150" nowrap="nowrap" class="t-right" style="font-weight: bold;border-right: 0;"><?php echo htmlentities($strField) ?>&nbsp;:</td>
		<?php if(@isset($arrDefined['ci_field_display_filter'][CI_SERVER][$strFieldName])){
			if($strValue != '' && function_exists($arrDefined['ci_field_display_filter'][CI_SERVER][$strFieldName])){
				$strValue = call_user_func(/* CI_Server_Helper */$arrDefined['ci_field_display_filter'][CI_SERVER][$strFieldName], $strValue);
			}
		?>
		<td style="border-left: 0"><?php echo $strValue ?></td>
		<?php } else {?>
		<td style="border-left: 0"><?php echo htmlentities($strValue) ?></td>
		<?php } ?>
	</tr>
	<?php } ?>
	<?php } ?>
</table>