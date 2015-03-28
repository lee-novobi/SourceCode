<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-ui/no-theme/jquery-ui-1.10.4.custom.min.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/custom_view.css" media="screen" />
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-ui/jquery-ui-1.10.4.custom.min.js"></script>
<h2 class="configration">Custom View Setting For CI <?php echo htmlentities($oCI['display_name']) ?></h2>
<div id="topControl">
<input type="button" value="Save & Close" onclick="onSubmit()" style="padding: 8px 20px">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="Cancel" onclick="parent.$.fancybox.close();" style="padding: 8px 25px; background-color: #dddddd">
</div>
<div style="overflow: auto;zoom:1;margin-top: 10px" id="mainCustomView">
	<?php for($col=0;$col<$nNumOfCol;$col++){ ?>
	<?php if(array_key_exists($col, $arrCol)){ ?>
		<div style="width: <?php echo 96/$nNumOfCol ?>%" class="divCustomViewCol" col="<?php echo $col ?>">
			<?php $nRowOfCol = count($arrCol[$col]); ?>
			<?php for($row=0;$row<$nRowOfCol;$row++){ ?>
			<?php if(array_key_exists($row, $arrCol[$col])){ ?>
			<div class="divGroupFields" group="<?php echo $arrCol[$col][$row]['group_name'] ?>">
				<div class="title">
					<input id="<?php echo str_replace(' ','_',strtolower($arrCol[$col][$row]['group_name'])) ?>" type="checkbox" onclick="onClickCustomeViewGroupCheckbox(this, '<?php echo $arrCol[$col][$row]['group_name'] ?>')">
					<label for="<?php echo str_replace(' ','_',strtolower($arrCol[$col][$row]['group_name'])) ?>"><?php echo htmlentities($arrCol[$col][$row]['group_name']) ?></label>
				</div>
				<div class="portlet-content">
					<ul class="sortable">
						<?php $i = 0; ?>
						<?php foreach($arrCol[$col][$row]['fields'] as $oField){ ?>
						<li class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
							<input id="<?php echo str_replace(' ','_',strtolower($oField['display_name'])) ?>" onclick="SetGroupCheckboxValue('<?php echo $arrCol[$col][$row]['group_name'] ?>')" type="checkbox" name="fields[]" group_name="<?php echo $arrCol[$col][$row]['group_name'] ?>" display_name="<?php echo $arrCol[$col][$row]['group_name'] ?>" value="<?php echo ($i.'|'.$oField['field_name']) ?>" <?php if($oField['selected']==1){ ?>checked="checked"<?php } ?>>
							<label for="<?php echo str_replace(' ','_',strtolower($oField['display_name'])) ?>"><?php echo htmlentities($oField['display_name']) ?></label>
						</li>
						<?php $i++; ?>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php } ?>
			<?php } ?>
		</div>
	<?php } else { ?>
		<div style="width: <?php echo 96/$nNumOfCol ?>%" class="divCustomViewCol" col="<?php echo $col ?>"></div>
	<?php } ?>
	<?php } ?>
</div>
<input type="hidden" id="cid" name="cid" value="<?php echo $nCIType ?>">
<br />
<div id="bottomControl">
<input type="button" value="Save & Close" onclick="onSubmit()" style="padding: 8px 20px">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="Cancel" onclick="parent.$.fancybox.close();" style="padding: 8px 25px; background-color: #dddddd">
</div>
</form>
<script type="text/javascript">
$(document).ready(function(){
<?php foreach($arrGroup as $strGroup){ ?>
	SetGroupCheckboxValue('<?php echo $strGroup ?>');
<?php } ?>
	$(".divCustomViewCol").css("width", ((parseInt($("#mainCustomView").css("width")) - <?php echo CUSTOM_VIEW_COL_MARGIN*$nNumOfCol ?>)/<?php echo $nNumOfCol ?>) + "px");
	$( ".divCustomViewCol" ).sortable({
		connectWith: ".divCustomViewCol",
		handle: ".title",
		cancel: ".portlet-toggle",
		placeholder: "portlet-placeholder"
	});

	$( ".divGroupFields" )
	.find( ".title" )
	.prepend( "<span class='ui-icon ui-icon-minusthick portlet-toggle'></span>");

	$( ".portlet-toggle" ).click(function() {
		var icon = $( this );
		icon.toggleClass( "ui-icon-minusthick ui-icon-plusthick" );
		icon.closest( ".divGroupFields" ).find( ".portlet-content" ).toggle();
	});

	$(".sortable").sortable({
		placeholder: "ui-state-highlight",
		stop: function( event, ui ) {
			var that = this;
			$(that).find("li").each(function(index){
				if(!(index % 2)){
					$(this).attr("class", "odd");
				} else {
					$(this).attr("class", "even");
				}

				var oCheckBox = $(this).find("input[type=checkbox]");
				if(oCheckBox){
					var arrData = oCheckBox.val().split("|");
					oCheckBox.val(index+"|"+arrData[1]);
				}
			})
		}
	});
    $( ".sortable" ).disableSelection();
    // $(".divCustomViewCol").css("max-height", (getHeight()-200)+"px");
    $("#mainCustomView .divCustomViewCol .divGroupFields .title").dblclick(function(){
    	$(this).find(".portlet-toggle").trigger("click");
    })
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
	var groupIndex = 0;
	var arrGroup = new Array();
	$(".divCustomViewCol").each(function(col){
		var that = this;
		$(that).find(".divGroupFields").each(function(row){
			var strGroupName = $(this).attr("group");
			arrGroup.push({"group": strGroupName, "index": groupIndex++, "col": col, "row": row});
		});
	});
	//alert(JSON.stringify(arrGroupPos));
	//return ;
	// var arrFormData = $("#frmCustomViewServer").serialize();
	// alert(arrFormData);
	var arrField = [];
	$(".sortable li input[type=checkbox]").each(function(){
		var val = $(this).val();
		var isChecked = ($(this).attr("checked")=="checked"?1:0);
		arrField.push({"val": val, "checked": isChecked});
	})
	$.post(
		"<?php echo $base_url ?>custom_view/index/save_custom_view",
		{groups: arrGroup, fields: arrField, "cid": $("#cid").val()},
		function(){
			parent.location.reload();
			parent.$.fancybox.close();
		}
	);
}
</script>