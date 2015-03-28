<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery.ui/no-theme/jquery-ui-1.10.4.min.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/override.jquery-ui.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/edit.user.css" media="screen" />
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery.ui/jquery-ui-1.10.4.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery.ui/jquery-ui.autocomplete.js"></script>
<table class="tbl-user-permission-data" width="100%" celpadding="3" celspacing="3">
	<tr>
		<td><strong>Department</strong></td>
		<td><strong>Username</strong></td>
	</tr>
	<tr>
		<td style="padding-left: 20px">
			<?php if($this->session->userdata('usertype')==USERTYPE_SUPERADMIN){ ?>
			<select id="cboDepartment" class="wp160">
				<option value="_all_">All</option>
				<?php foreach($arrDepartment as $oDept){ ?>
				<?php if(!empty($oDept[DEPARTMENT_KEY])&&!empty($oDept['alias'])){ ?>
				<option value="<?php echo $oDept[DEPARTMENT_KEY] ?>"<?php if(strtolower($oUser['department_key'])==strtolower((string)$oDept[DEPARTMENT_KEY])) echo ' selected="selected"' ?>><?php echo htmlentities(trim($oDept['alias']), ENT_QUOTES, "UTF-8") ?></option>
				<?php } ?>
				<?php } ?>
			</select><input type="hidden" value="<?php echo @$oUser['department_key'] ?>" id="hidOldDepartment">
			<?php } else {?>
			<select disabled="disabled" class="wp160">
				<option value="">Unknown</option>
				<?php foreach($arrDepartment as $oDept){ ?>
				<?php if(!empty($oDept[DEPARTMENT_KEY])&&!empty($oDept['alias'])){ ?>
				<?php if(strtolower($oUser['department_key'])==strtolower((string)$oDept[DEPARTMENT_KEY])){ ?>
				<option value="<?php echo $oDept[DEPARTMENT_KEY] ?>" selected="selected"><?php echo htmlentities(trim($oDept['alias']), ENT_QUOTES, "UTF-8") ?></option>
				<?php } ?>
				<?php } ?>
				<?php } ?>
			</select>
			<?php } ?>
		</td>
		<td style="padding-left: 20px">
			<?php if($action=='edit'){ ?>
			<div class="input-disabled wp160"><?php echo htmlentities(trim($oUser['username']), ENT_QUOTES, "UTF-8") ?></div>
			<input type="hidden" id="txtUsername" value="<?php echo $oUser['username'] ?>">
			<?php } else { ?>
			<input type="text" id="txtUsername" value="">
			<?php } ?>
		</td>
	</tr>
	<tr class="section">
		<td colspan="2"><strong>Member of Product</strong></td>
	</tr>
	<tr>
		<td><strong>Product</strong></td>
		<td><strong>Role</strong></td>
	</tr>
	<tbody id="ProductContainer">
	<?php foreach($arrProductOwn as $strProductKey=>$arrProductRole){ ?>
	<?php foreach($arrProductRole as $oProduct){ ?>
	<tr class="rowProduct" deleted="0" is_new="0" org_value="<?php echo $strProductKey . '|' . $oProduct['role']['user_group_id'] . '|' . (string)$oProduct['role']['_id'] ?>">
		<td>
			<?php if(!$oProduct['role']['is_editable']){ ?>
			<?php /*echo htmlentities(strtoupper($oProduct['alias']), ENT_QUOTES, "UTF-8")*/ ?>
			<?php } else {?>
			<!-- <select class="selproduct wp160">
				<?php foreach($arrAssignableProduct as $strAssignableProduct=>$oAssignableProduct){ ?>
				<option value="<?php echo $strAssignableProduct ?>" <?php if($strAssignableProduct==$strProductKey) echo 'selected="selected"'?>><?php echo htmlentities(strtoupper($oAssignableProduct['name']), ENT_QUOTES, "UTF-8") ?></option>
				<?php } ?>
			</select> -->
			<?php } ?>
			<?php echo htmlentities(strtoupper($oProduct['alias']), ENT_QUOTES, "UTF-8") ?>
			<input type="hidden" value="<?php echo $strProductKey ?>" class="selproduct">
		</td>
		<td>
			<?php if(!$oProduct['role']['is_editable']){ ?>
			<?php echo htmlentities(strtoupper($oUserProfile['arrPermittedUserGroup'][$oProduct['role']['user_group_id']]['group_name'] . ' - ' . $oProduct['role']['role_name']), ENT_QUOTES, "UTF-8") ?>
			<?php } else {?>
			<select product_owner_id="<?php echo $oProduct['product_owner_id'] ?>" class="selproduct_role wp160">
				<?php foreach($oUserProfile['arrPermittedUserGroupDetail'] as $strGroupId=>$oGroupDetail){ ?>
				<?php foreach($oGroupDetail['arrPermittedProductRole'] as $strRoleId=>$oRole){ ?>
				<option value="<?php echo $strGroupId . '|' . $strRoleId ?>" <?php if($strRoleId==(string)$oProduct['role']['_id'] && $strGroupId==$oProduct['role']['user_group_id']) echo 'selected="selected"'?>><?php echo htmlentities(strtoupper($oUserProfile['arrPermittedUserGroup'][$strGroupId]['group_name'] . ' - ' . $oRole['role_name']), ENT_QUOTES, "UTF-8") ?></option>
				<?php } ?>
				<?php } ?>
			</select>
			&nbsp;
			<a href="#" onclick="RemoveProductOwner(this)">Delete</a>
			<span class="msg"></span>
			<?php } ?>
		</td>
	</tr>
	<?php } ?>
	<?php } ?>
	<tr id="productTemplate" style="display:none" deleted="0" is_new="1" org_value="">
		<td>
			<select product_owner_id="" class="selproduct wp160">
				<option value="" selected="selected"></option>
				<?php foreach($arrAssignableProduct as $strAssignableProduct=>$oAssignableProduct){ ?>
				<option value="<?php echo $strAssignableProduct ?>"><?php echo htmlentities(strtoupper($oAssignableProduct['name']), ENT_QUOTES, "UTF-8") ?></option>
				<?php } ?>
			</select>
		</td>
		<td>
			<select class="selproduct_role wp160"></select>&nbsp;
			<input class="btnAddProductRole" type="button" value="Add" onclick="AddProduct(this)">
			<span class="msg"></span>
		</td>
	</tr>
	</tbody>
	<tr class="section">
		<td colspan="2"><strong>Member of Group</strong></td>
	</tr>
	<tr>
		<td><strong>Group Name</strong></td>
		<td><strong>Role</strong></td>
	</tr>
	<tbody id="GroupContainer">
	<?php foreach($arrBelongToUserGroups as $strGroupId=>$oGroup) { ?>
	<tr class="rowGroup" deleted="0" is_new="0">
		<td>
			<!-- <select class="selgroup wp160" <?php if(!$oGroup['deletable_user']) echo 'disabled="disabled"' ?>>
			<?php if(!$oGroup['deletable_user']) { ?>
			<option value=""><?php echo htmlentities($oGroup['group_name'], ENT_QUOTES, "UTF-8") ?></option>
			<?php } else { ?>
			<?php foreach($oUserProfile['arrPermittedUserGroupDetail'] as $strProfileGroupId=>$oProfileGroupDetail){ ?>
			<?php if(in_array(ADD_USER, $oProfileGroupDetail['arrRightOnGroup'])) ?>
			<option value="<?php echo $strProfileGroupId ?>" <?php if($strGroupId==$strProfileGroupId) echo 'selected="selected"' ?>><?php echo htmlentities($oUserProfile['arrPermittedUserGroup'][$strProfileGroupId]['group_name'], ENT_QUOTES, "UTF-8") ?></option>
			<?php } ?>
			<?php } ?>
			</select> -->
			<?php echo htmlentities($oGroup['group_name'], ENT_QUOTES, "UTF-8") ?>
		</td>
		<td>
			<?php if($oGroup['pm_role']['assignable']){ ?>
			<select pm_user_role_id="<?php echo $oGroup['pm_user_role_id'] ?>" org_value="<?php echo $strGroupId . '|' . (string)$oGroup['pm_role']['_id']?>" class="selgroup_role wp160">
				<?php foreach(@$oUserProfile['arrPermittedUserGroupDetail'][$strGroupId]['arrPMRoleAssignable'] as $strId=>$strName){ ?>
				<option value="<?php echo $strGroupId.'|'.$strId ?>" <?php if($strId == (string)$oGroup['pm_role']['_id']){ ?>selected="selected"<?php } ?>><?php echo htmlentities(strtoupper($strName), ENT_QUOTES, "UTF-8") ?></option>
				<?php } ?>
			</select>&nbsp;
			<?php if($oGroup['deletable_user']){ ?><a href="#" onclick="RemoveUserGroup(this)">Delete</a><?php } ?>
			<span class="msg"></span>
			<?php } /*if*/ else { ?>
			<?php echo htmlentities(strtoupper($oGroup['pm_role']['role_name']), ENT_QUOTES, "UTF-8") ?>
			<?php } ?>
		</td>
	</tr>
	<?php } ?>
	<?php $strCanAddGroup = 'false' ?>
	<tr id="groupTemplate" style="display: none" deleted="0" is_new="1">
		<td>
			<select class="selgroup wp160">
				<option selected="selected" value=""></option>
				<?php foreach($oUserProfile['arrPermittedUserGroupDetail'] as $strProfileGroupId=>$oProfileGroupDetail){ ?>
				<?php if(in_array(ADD_USER, $oProfileGroupDetail['arrRightOnGroup'])){ $strCanAddGroup = 'true' ?>
				<option value="<?php echo $strProfileGroupId ?>"><?php echo htmlentities($oUserProfile['arrPermittedUserGroup'][$strProfileGroupId]['group_name'], ENT_QUOTES, "UTF-8") ?></option>
				<?php } ?>
				<?php } ?>
			</select>
		</td>
		<td>
			<select pm_user_role_id="" class="selgroup_role wp160"></select>&nbsp;
			<input class="btnAddGroup" type="button" value="Add" onclick="AddGroup(this)">
			<span class="msg"></span>
		</td>
	</tr>
	</tbody>
</table>
<div class="board-control t-center">
	<input type="button" class="form-submit" value="Save" onclick="onEditUserSubmit()">
</div>
<script type="text/javascript">
var RetryTimes = 2;
var CanAddGroup = <?php echo $strCanAddGroup ?>;
var CanAddProduct = <?php echo count($arrAssignableProduct)>0?'true':'false' ?>;

function AddGroup(oButton){
	if(CanAddGroup > 0){
		var oNewRow = $("#groupTemplate").clone().removeAttr("style").removeAttr("id").addClass("rowGroup");
		oNewRow.find("select.selgroup").change(function(){
			var strGroupId = $(this).val();
			oNewRow.find("select.selgroup_role").empty();
			if(strGroupId != ""){
				$.ajax({
					url: '<?php echo $base_url ?>users/ajax_list_pm_role_of_usergroup?gid=' + strGroupId,
					dataType: 'json',
					success: function(response){
						$.each(response, function(key, value){
							oNewRow.find("select.selgroup_role").append($("<option></option>").attr("value",value.id).text(value.name)); ;
						});
					}
				});
			}
		});
		$(oButton).after('<a href="#" onclick="RemoveUserGroup(this)">Delete</a>').remove();
		$("#GroupContainer").append(oNewRow);
		oNewRow.find("select.selgroup, select.selgroup_role").combobox();
	}
}
function AddProduct(oButton){
	if(CanAddProduct > 0){
		var oNewRow = $("#productTemplate").clone().removeAttr("style").removeAttr("id").addClass("rowProduct");
		oNewRow.find("select.selproduct").change(function(){
			var strProductKey = $(this).val();
			var strOptions = '<?php foreach($this->oUserProfile['arrPermittedUserGroupDetail'] as $strUserGroupId => $oGroupDetail){foreach($oGroupDetail['arrPermittedProductRole'] as $strId=>$oRole){ ?><option value="<?php echo $strUserGroupId . '|' . $strId ?>"><?php echo htmlentities(strtoupper($this->oUserProfile['arrPermittedUserGroup'][$strUserGroupId]['group_name'] . ' - ' . $oRole['role_name']), ENT_QUOTES, "UTF-8")?></option><?php }} ?>';
			oNewRow.find("select.selproduct_role").empty();
			if(strProductKey != ""){
				oNewRow.find("select.selproduct_role").append(strOptions);
			}
		});
		// oNewRow.find("select.selproduct").combobox();
		$(oButton).after('<a href="#" onclick="RemoveProductOwner(this)">Delete</a>').remove();
		$("#ProductContainer").append(oNewRow);
		oNewRow.find("select.selproduct, select.selproduct_role").combobox();
	}
}

function InitAutoloadForGroup(){
	$("#GroupContainer tr.rowGroup").each(function(index){
		var that = $(this);
		that.find("select.selgroup_role").combobox();
		that.find("select.selgroup").change(function(){
			var strGroupId = $(this).val();
			that.find("select.selgroup_role").empty();
			if(strGroupId != ""){
				$.ajax({
					url: '<?php echo $base_url ?>users/ajax_list_pm_role_of_usergroup?gid=' + strGroupId,
					dataType: 'json',
					success: function(response){
						$.each(response, function(key, value){
							that.find("select.selgroup_role").append($("<option></option>").attr("value",value.id).text(value.name)); ;
						});
					}
				});
			}
		});
	})
}

function InitAutoloadForProduct(){
	$("#ProductContainer tr.rowProduct").each(function(index){
		var that = $(this);
		that.find("select.selproduct_role").combobox();
		that.find("select.selproduct").each(function(index){
			if($(this).attr("disabled") != "disabled"){
				$(this).change(function(){
					var strProductKey = $(this).val();
					that.find("select.selproduct_role").empty();
					if(strProductKey != ""){
						$.ajax({
							url: '<?php echo $base_url ?>users/ajax_list_role_of_product?pid=' + strProductKey,
							dataType: 'json',
							success: function(response){
								$.each(response, function(key, value){
									that.find("select.selproduct_role").append($("<option></option>").attr("value",value.id).text(value.name)); ;
								});
							}
						});
					}
				}).combobox();
			}
		});
	});
}

function SubmitGroup(){
	var arrGroup = new Array();
	var arrGroupDelete = new Array();
	$("#GroupContainer .rowGroup .selgroup_role").each(function(){
		var oTD = $(this).parent();
		var oTR = $(this).parent().parent();
		var strNewValue = $(this).val();
		var isDisabled = ($(this).attr("disabled")=="disabled");
		var isDeleted = (oTR.attr("deleted")==="1");

		if(strNewValue && strNewValue != "" && !isDisabled){
			if(!isDeleted)
			{
				arrGroup.push({'ele': $(this), 'obj': $(this).val()});
				$(this).parent().find("span.msg").attr("class", "msg waiting").text("Saving ...");
			} else {
				var strOldValue = $(this).val();
				arrGroupDelete.push({'ele': $(this), 'obj': $(this).val()});
			}
		}
	});

	var arrUniqueGroupObj = new Array();
	var arrUniqueGroupId  = new Array();
	var arrDuplicate = new Array();
	$.each(arrGroup, function(index, obj){
		var arrIds = obj.obj.split("|");
		if($.inArray(arrIds[0], arrUniqueGroupId) === -1){
			arrUniqueGroupId.push(arrIds[0]);
			arrUniqueGroupObj.push({'ele': obj.ele, 'obj': obj.obj});
		} else {
			arrDuplicate.push({'ele': obj.ele, 'obj': obj.obj});
		}
	});

	$.each(arrGroupDelete, function(index, obj){
		$.ajax({
			type: 'POST',
			async: false,
			url: base_url + 'users/remove_from_group',
			dataType: 'json',
			data:{key: obj.obj, pm_user_role_id: obj.ele.attr("pm_user_role_id"), username: $("#txtUsername").val()},
			success: function(response){
				var oTD = obj.ele.parent();
				var oTR = obj.ele.parent().parent();

				if(response && response.code != undefined){
					if(response.code===1){
						oTR.remove();
					} else {
						oTD.find("span.msg").attr("class", "msg failed").text("Can't remove this group. " + response.msg);
						oTR.attr("deleted", "0");
						oTR.removeAttr("style");
					}
				} else {
					oTD.find("span.msg").attr("class", "msg failed").text("Can't remove this group. Bad Request/Response");
					oTR.attr("deleted", "0");
					oTR.removeAttr("style");
				}
			}
		});
	});

	$.each(arrUniqueGroupObj, function(index, obj){
		var oTD = obj.ele.parent();
		var oTR = obj.ele.parent().parent();
		var strNewValue = obj.ele.val();
		var isNewRow = (oTR.attr("is_new")==="1");
		var strOldValue = obj.ele.attr("org_value");

		if(isNewRow || (!isNewRow && (strNewValue != strOldValue))){
			$.ajax({
				type: 'POST',
				url: base_url + 'users/save_to_group',
				dataType: 'json',
				data:{key: obj.obj, pm_user_role_id: obj.ele.attr("pm_user_role_id"), username: $("#txtUsername").val()},
				success: function(response){
					if(response && response.code != undefined){
						if(response.code===1){
							oTD.find("span.msg").attr("class", "msg success").text("Saved");
							oTR.attr("is_new", "0");
							obj.ele.attr("org_value", obj.obj);
							if(response.new_id != undefined){
								obj.ele.attr("pm_user_role_id", response.new_id);
							}
						} else {
							oTD.find("span.msg").attr("class", "msg failed").text(response.msg);
						}
					} else {
						oTD.find("span.msg").attr("class", "msg failed").text("Bad Request/Response");
					}
				}
			});
		} else {
			if(!isNewRow){
				oTD.find("span.msg").attr("class", "msg success").text("No change");
			}
		}
	});

	$.each(arrDuplicate, function(index, obj){
		obj.ele.parent().find("span.msg").attr("class", "msg failed").text("Duplicate User Group !!!");
	});
}

function SubmitProduct(){
	var arrProduct = new Array();
	var arrProductDelete = new Array();

	$("#ProductContainer .rowProduct").each(function(){
		var cboProduct = $(this).find(".selproduct");
		var cboProductRole = $(this).find("select.selproduct_role");
		var oTD = cboProductRole.parent();
		var oTR = cboProduct.parent().parent();

		var strNewProduct = cboProduct.val();
		var isDisabled = (cboProduct.attr("disabled")=="disabled");
		var isDeleted = (oTR.attr("deleted")==="1");
		var strOldKey = oTR.attr("org_value");
		var strNewKey = cboProduct.val() + '|' + cboProductRole.val();
		var strProductOwnerId = cboProductRole.attr("product_owner_id");
		var strUsername = $("#txtUsername").val();
		var isNewProduct = oTR.attr("is_new");

		if(strNewProduct && strNewProduct != "" && !isDisabled){
			var obj = {
				'product_key':    cboProduct.val(),
				'role_key':       cboProductRole.val(),
				'cboProduct':     cboProduct,
				'cboProductRole': cboProductRole,
				'TD':             oTD,
				'TR':             oTR,
				'old_key':        strOldKey,
				'new_key':        strNewKey,
				'product_owner_id': strProductOwnerId,
				'username':       strUsername,
				'is_new_product'  : isNewProduct
			};
			if(!isDeleted)
			{
				arrProduct.push(obj);
				oTD.find("span.msg").attr("class", "msg waiting").text("Saving ...");
			} else {
				arrProductDelete.push(obj);
			}
		}
	});

	var arrUniqueProductObj = new Array();
	var arrUniqueProductKey  = new Array();
	var arrDuplicate = new Array();
	$.each(arrProduct, function(index, obj){
		if($.inArray(obj.new_key, arrUniqueProductKey) === -1){
			arrUniqueProductKey.push(obj.new_key);
			arrUniqueProductObj.push(obj);
		} else {
			arrDuplicate.push(obj);
		}
	});

	$.each(arrProductDelete, function(index, obj){
		$.ajax({
			type: 'POST',
			async: false,
			url: base_url + 'users/remove_from_product_role',
			dataType: 'json',
			data:{product_owner_id: obj.product_owner_id, username: obj.username, role_key: obj.role_key},
			success: function(response){
				if(response && response.code != undefined){
					if(response.code===1){
						obj.TR.remove();
					} else {
						obj.TD.find("span.msg").attr("class", "msg failed").text("Can't remove this product. " + response.msg);
						obj.TR.attr("deleted", "0");
						obj.TR.removeAttr("style");
					}
				} else {
					obj.TD.find("span.msg").attr("class", "msg failed").text("Can't remove this product. Bad Request/Response");
					obj.TR.attr("deleted", "0");
					obj.TR.removeAttr("style");
				}
			}
		});
	});

	var nRetryCount = 0;
	do{
		var arrRetry = new Array();
		$.each(arrUniqueProductObj, function(index, obj){
			var isNewRow = (obj.is_new_product === "1");
			if(isNewRow || (!isNewRow && (obj.old_key != obj.new_key))){
				$.ajax({
					type: 'POST',
					async: false,
					url: base_url + 'users/save_to_product',
					dataType: 'json',
					data:{product_owner_id: obj.product_owner_id, username: obj.username, role_key: obj.role_key, product_key: obj.product_key},
					success: function(response){
						if(response && response.code != undefined){
							if(response.code===1){
								obj.TD.find("span.msg").attr("class", "msg success").text("Saved");
								obj.TR.attr("is_new", "0");
								obj.TR.attr("org_value", obj.new_key);
								if(response.new_id != undefined){
									obj.cboProductRole.attr("product_owner_id", response.new_id);
								}
							} else {
								if(nRetryCount == RetryTimes){
									obj.TD.find("span.msg").attr("class", "msg failed").text(response.msg);
								} else {
									arrRetry.push(obj);
								}
							}
						} else {
							if(nRetryCount == RetryTimes){
								obj.TD.find("span.msg").attr("class", "msg failed").text("Bad Request/Response");
							} else {
								arrRetry.push(obj);
							}
						}
					}
				});
			} else {
				if(!isNewRow){
					obj.TD.find("span.msg").attr("class", "msg success").text("No change");
				}
			}
		});
		arrUniqueProductObj = arrRetry;
		nRetryCount++;
		//alert(nRetryCount);
	} while(nRetryCount <= RetryTimes && arrUniqueProductObj.length > 0)

	$.each(arrDuplicate, function(index, obj){
		var isNewRow = (obj.is_new_product === "1");
		if(!isNewRow && (obj.old_key == obj.new_key)){
			obj.TD.find("span.msg").attr("class", "msg success").text("No change");
		} else {
			obj.TD.find("span.msg").attr("class", "msg failed").text("Duplicated !!!");
		}
	});
}

function onEditUserSubmit(){
	$("#txtUsername").nextAll("span.msg").remove();
	if($("#txtUsername").val()==""){
		$("#txtUsername").after('<span class="msg failed">Please input Username</span>');
	} else {
		<?php if($action == 'add'){ ?>
		if(SubmitNewUser()){
			SubmitGroup();
			SubmitProduct();
		}
		<?php } else {?>
		<?php if($this->session->userdata('usertype')==USERTYPE_SUPERADMIN){ ?>
		SubmitDepartment();
		<?php } ?>
		SubmitGroup();
		SubmitProduct();
		<?php } ?>
	}
}
<?php if($action == 'add'){ ?>
function SubmitNewUser(){
	var oResult = true;

	$.ajax({
		type: 'POST',
		async: false,
		url: base_url + 'users/save_new_user',
		dataType: 'json',
		data:{username: $("#txtUsername").val(), department_key: $("#cboDepartment").val()},
		success: function(response){
			if(response && response.code != undefined){
				if(response.code!==1){
					oResult = false;
					$("#txtUsername").after('<span class="msg failed">' + response.msg + '</span>');
				}
			}
		}
	});
	return oResult;
}
<?php } ?>
<?php if($this->session->userdata('usertype')==USERTYPE_SUPERADMIN || $action == 'add'){ ?>
function SubmitDepartment(){
	var oResult = true;
	var strOldDepartment = $("#hidOldDepartment").val();
	var strNewDepartment = $("#cboDepartment").val();
	if(strOldDepartment != strNewDepartment){
		$.ajax({
			type: 'POST',
			async: false,
			url: base_url + 'users/save_user_department',
			dataType: 'json',
			data:{username: $("#txtUsername").val(), department_key: strNewDepartment},
			success: function(response){
				if(response.code==1){
					$("#hidOldDepartment").val(strNewDepartment);
				}
			}
		});
	}
}
<?php } ?>
function RemoveUserGroup(o){
	if($(o).parent()/*td*/.parent()/*tr*/.attr("is_new") === "0"){
		$(o).parent()/*td*/.parent()/*tr*/.css("display","none").attr("deleted", "1");
	} else {
		$(o).parent()/*td*/.parent()/*tr*/.remove();
	}
	return false;
}

function RemoveProductOwner(o){
	if($(o).parent()/*td*/.parent()/*tr*/.attr("is_new") === "0"){
		$(o).parent()/*td*/.parent()/*tr*/.css("display","none").attr("deleted", "1");
	} else {
		$(o).parent()/*td*/.parent()/*tr*/.remove();
	}
	return false;
}

$(function(){
	InitAutoloadForGroup();
	InitAutoloadForProduct();
	AddGroup();
	AddProduct();
	$("#cboDepartment").combobox();
})
</script>