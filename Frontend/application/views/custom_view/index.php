<h2 class="configration">Custom View Setting For CI <?php echo htmlentities($oCI['display_name']) ?></h2>
<form id="frmCustomViewServer" method="POST" action="">
<table width="100%" cellpadding="0" cellspacing="0" class="detail-zebra td-bordered">
	<?php for($row=0;$row<count($arrFieldList[0]);$row++){ ?>
		<tr class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
			<?php for($col=0;$col<$nNumOfCol;$col++){ ?>
				<?php $isHasValue = array_key_exists($col, $arrFieldList) && array_key_exists($row, $arrFieldList[$col]); ?>
				<?php if($isHasValue){ ?>
					<?php if(strtolower($arrFieldList[$col][$row]['type'])=='group') {?>
						<th colspan="2" class="group-field">
							<input id="<?php echo str_replace(' ','_',strtolower($arrFieldList[$col][$row]['display_name'])) ?>" type="checkbox" onclick="onClickCustomeViewGroupCheckbox(this, '<?php echo $arrFieldList[$col][$row]['display_name'] ?>')">
							<label for="<?php echo str_replace(' ','_',strtolower($arrFieldList[$col][$row]['display_name'])) ?>"><?php echo htmlentities($arrFieldList[$col][$row]['display_name']) ?></label>
						</th>
					<?php } else { ?>
						<td width="30" class="t-center" style="border-right: 0"><input id="<?php echo str_replace(' ','_',strtolower($arrFieldList[$col][$row]['display_name'])) ?>" onclick="SetGroupCheckboxValue('<?php echo $arrFieldList[$col][$row]['group_name'] ?>')" type="checkbox" name="fields[]" group_name="<?php echo $arrFieldList[$col][$row]['group_name'] ?>" display_name="<?php echo $arrFieldList[$col][$row]['display_name'] ?>" value="<?php echo ($arrFieldList[$col][$row]['field_name']) ?>" <?php if($arrFieldList[$col][$row]['selected']==1){ ?>checked="checked"<?php } ?>></td>
						<td style="border-left: 0;font-weight: bold"><label for="<?php echo str_replace(' ','_',strtolower($arrFieldList[$col][$row]['display_name'])) ?>"><?php echo htmlentities($arrFieldList[$col][$row]['display_name']) ?></label></td>
					<?php } ?>
				<?php } else { ?>
					<td colspan="2">&nbsp</td>
				<?php } ?>
			<?php } ?>
		</tr>
	<?php } ?>
</table>
<input type="hidden" name="cid" value="<?php echo $nCIType ?>">
<br />
<div class="t-center">
<input type="button" value="Save & Close" onclick="onSubmit()" style="padding: 10px 40px">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="Cancel" onclick="parent.$.fancybox.close();" style="padding: 10px 40px; background-color: #dddddd">
</div>
</form>
<script type="text/javascript">
$(document).ready(function(){
<?php foreach($arrGroup as $strGroup){ ?>
	SetGroupCheckboxValue('<?php echo $strGroup ?>');
<?php } ?>
});
function onClickCustomeViewGroupCheckbox(oSender, strGroupName){
	var strSelector = 'input[type="checkbox"][group_name="' + strGroupName + '"]';
	$(strSelector).each(function(index, obj){
		obj.checked = oSender.checked;
	});
}

function SetGroupCheckboxValue(strGroupName){
	var isCheck = true;
	var strSelector = 'input[type="checkbox"][group_name="' + strGroupName + '"]';
	$(strSelector).each(function(index, obj){
		if(obj.checked == false){
			isCheck = false;
			return;
		}
	});

	var oChkGroup = null;
	var strChkGroupID = strGroupName.toLowerCase();
	strChkGroupID = strChkGroupID.replace(" ","_");
	oChkGroup = document.getElementById(strChkGroupID);

	if(oChkGroup != null){
		oChkGroup.checked = isCheck;
	}
}

function onSubmit(){
	var arrFormData = $("#frmCustomViewServer").serialize();
	$.post(
		"<?php echo $base_url ?>custom_view/index/save_custom_view",
		arrFormData,
		function(){
			parent.location.reload();
			parent.$.fancybox.close();
		}
	);
}
</script>