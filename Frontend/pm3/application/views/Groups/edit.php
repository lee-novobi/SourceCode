<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery.ui/no-theme/jquery-ui-1.10.4.min.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/override.jquery-ui.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/user-group.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/sdk.multiselect.css" media="screen" />

<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery.ui/jquery-ui-1.10.4.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery.ui/jquery-ui.autocomplete.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/sdk.multiselect.js"></script>

<table class="tbl-edit-user-group-data" width="100%" celpadding="3" celspacing="3">
	<tr>
		<td><strong>Department</strong></td>
		<td><strong>User Group</strong></td>
	</tr>
	<tr>
		<td>
			<select id="sltDepartment" class="wp180">
				<option value="">All</option>
				<?php if(!empty($arrDepartment)) { foreach($arrDepartment as $oDepartment) { ?>
				<option value="<?php echo (string)$oDepartment[DEPARTMENT_KEY] ?>"<?php if($action=='edit' && $oUserGroup['department_key']==(string)$oDepartment[DEPARTMENT_KEY]) echo ' selected="selected"' ?>><?php echo $oDepartment['alias'] ?></option>
				<?php } } ?>
			</select>&nbsp;<span class="msg" id="spDeptMsg"></span>
		</td>
		<td>
			<?php if($action=='edit'){ ?>
			<div class="input-disabled wp160"><?php echo htmlentities(trim($oUserGroup['group_name']), ENT_QUOTES, "UTF-8") ?></div>
			<input type="hidden" id="txtGroupName" value="<?php echo $oUserGroup['group_name'] ?>">
			<input type="hidden" id="txtGroupId" value="<?php echo $oUserGroup['_id'] ?>">
			<?php } else { ?>
			<input type="text" id="txtGroupname" value="">&nbsp;<span class="msg" id="spGroupNameMsg"></span>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td><strong>Product</strong></td>
		<td><strong>User</strong></td>
	</tr>
	<tr>
		<td id="celProduct">
			<select id="sltProduct" name="product[]" multiple="multiple" class="wp160">
				<?php foreach($arrMemberProduct as $oProd){ ?>
				<option value="<?php echo $oProd['_id'] ?>" selected="selected"><?php echo $oProd['alias'] ?></option>
				<?php } ?>
			</select>
		</td>
		<td>
			<select id="sltUser" name="user[]" multiple="multiple" class="wp160">
				<?php foreach($arrAllUser as $oUser){ ?>
				<!-- <option value="<?php echo trim($oUser['username']) ?>"<?php if(array_key_exists($oUser['username'], $arrMemberUser)) echo 'selected="selected"' ?>><?php echo htmlentities(trim($oUser['username']), ENT_QUOTES, "UTF-8") ?></option> -->
				<?php } ?>
			</select>
		</td>
	</tr>
</table>
<div class="board-control t-center">
	<input id="btnSave" type="button" class="form-submit" value="Save" onclick="javascript:SaveUserGroupSetting();">
	<input id="btnSave-and-AddMore" type="button" class="form-submit" value="Save & Add More" onclick="javascript:;">
	<input id="btnCancel" type="button" class="form-submit" value="Cancel" onclick="javascript:;">
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#sltDepartment').combobox({placeholder:"Filter Products by Department"}).change(function(){
		ListProduct.call(this);
		ListUser.call(this);
	});
	Init2SideControl();
	$('#sltDepartment').trigger("change");
});

function ListProduct(){
	$('#sltProduct').SDKMultiSelect("Empty");
	$('#sltProduct').SDKMultiSelect("SetCurrentGroup", $(this).val());
	$.ajax({
		url: base_url + 'users/ajax_get_product_by_department/sltProduct/product/product?department=' + $(this).val(),
		data: {"department": $(this).val(), "type": "json"},
		dataType: "json",
		success: function(response){
			$('#sltProduct').SDKMultiSelect("LoadAvailableList", response);
		}
	});
}

function ListUser(){
	$('#sltUser').SDKMultiSelect("Empty");
	$('#sltUser').SDKMultiSelect("SetCurrentGroup", $(this).val());

	$.ajax({
		url: base_url + 'users/ajax_get_user_by_department/sltUser/user/user?department=' + $(this).val(),
		data: {"department": $(this).val(), "type": "json"},
		dataType: "json",
		success: function(response){
			$('#sltUser').SDKMultiSelect("LoadAvailableList", response);
		}
	});
}

function Init2SideControl(){
	$('#sltProduct').SDKMultiSelect();
	$('#sltUser').SDKMultiSelect({multi_group: false});
}

function Init2SideUser(){
	$('#sltUser').attr("multiple", "multiple").multiselect2side({
		search: "<img src='"+ base_url + "asset/js/multiselect2side/img/search.gif' />",
		moveOptions: false,
		autoSort: true,
		autoSortAvailable: true,
		labeldx: 'Users Selected'
	});
}

function SaveUserGroupSetting(){
	<?php if($action=='edit'){ ?>
	SubmitProductList();
	SubmitUserList();
	<?php } else { ?>
	SubmitNewGroup();
	<?php } ?>
}
<?php if($action=='edit'){ ?>
function SubmitProductList(){
	var arrSelectedProduct = $('#sltProduct').SDKMultiSelect("Serialize");
	$.ajax({
		url: base_url + 'user_groups/save_list_product',
		type: 'POST',
		async: false,
		data: {"product_list": arrSelectedProduct, "user_group_id": $("#txtGroupId").val()},
		dataType: "json",
		success: function(response){
			alert("done");
		}
	});
}

function SubmitUserList(){
	var arrSelectedUser = $('#sltUser').SDKMultiSelect("Serialize");

	$.ajax({
		url: base_url + 'user_groups/save_list_user',
		type: 'POST',
		async: false,
		data: {"user_list": arrSelectedUser, "user_group_id": $("#txtGroupId").val()},
		dataType: "json",
		success: function(response){
			alert("done");
		}
	});
}
<?php } else { ?>
function SubmitNewGroup(){
	var isValid = true;
	var arrSelectedProduct = $('#sltProduct').SDKMultiSelect("Serialize");
	var arrSelectedUser    = $('#sltUser').SDKMultiSelect("Serialize");
	var strDepartment      = $('#sltDepartment').val();
	var strGroupName       = $('#txtGroupname').val();
	$("#spDeptMsg,#spGroupNameMsg").attr("class", "msg").css("display", "none").text("");

	if(strDepartment == ""){
		$("#spDeptMsg").attr("class", "msg failed").text("Please input Department").css("display", "inline");
		isValid = false;
	}
	if(strGroupName == ""){
		$("#spGroupNameMsg").attr("class", "msg failed").text("Please input GroupName").css("display", "inline");
		isValid = false;
	}
	if(isValid){
		$.ajax({
			url: base_url + 'user_groups/insert_group',
			type: 'POST',
			async: false,
			data: {
				"product_list": arrSelectedProduct,
				"user_group_id": $("#txtGroupId").val(),
				"group_name":strGroupName,
				"department_key": strDepartment,
				"user_list": arrSelectedUser
			},
			dataType: "json",
			success: function(response){
				alert("done");
			}
		});
	}
}
<?php } ?>
</script>