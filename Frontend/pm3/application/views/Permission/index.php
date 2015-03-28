<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery.ui/no-theme/jquery-ui-1.10.4.min.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/override.jquery-ui.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/permission.css" media="screen" />
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery.ui/jquery-ui-1.10.4.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery.ui/jquery-ui.autocomplete.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/table-collapsible.js"></script>
<div class="board-control">
	<?php echo $message;?>
	<div class="f-left" style="line-height: 30px"><b>User Group&nbsp;&nbsp;</b></div>
	<div class="f-left">
		<select id="cboUserGroups" class="wp200">
			<?php foreach($arrUserGroups as $oUserGroup) { ?>
			<option value="<?php echo $oUserGroup['_id'] ?>" <?php if((string)$oUserGroup['_id']==$strGroupId) echo 'selected="selected"' ?>><?php echo htmlentities($oUserGroup['group_name'], ENT_QUOTES, "UTF-8") ?></option>
			<?php } ?>
		</select>
	</div>
	<div class="f-right">
		<input type="button" class="form-submit" value="Save" onclick="onRolesPermissionSubmit()">
	</div>
</div>
<form id="frmRolesPermission" method="get">
<div id="divRolesPermission">
</div>
</form>
<div class="board-control t-right">
	<input type="button" class="form-submit" value="Save" onclick="onRolesPermissionSubmit()">
</div>
<script type="text/javascript">
$(document).ready(function(){
	$("#cboUserGroups").change(function(){
		LoadRolesPermission();
	})
	$("#cboUserGroups").combobox();
	LoadRolesPermission();
	$( window ).resize(function() {
		setTimeout(function(){ReDrawFreezHeader()}, 100);
	});
});
function LoadRolesPermission(){
	var strUserGroupId = $("#cboUserGroups").val();
	$("#divRolesPermission").load('<?php echo $base_url ?>permission/ajax_load_roles_permission?gid=' + strUserGroupId, function(){
		FreezHeader();
	});
}
function onRolesPermissionSubmit(){
	var strUserGroupId = $("#cboUserGroups").val();
	var arrData = new Array();
	$("#divRolesPermission input[type=checkbox]").each(function(index){
		var strRoleId = $(this).attr("role_id");
		var strPnId = $(this).attr("pn_id");
		var nValue = this.checked?1:0;

		arrData.push({role_id: strRoleId, pn_id:strPnId, value: nValue})
	});
	$.ajax({
		type: 'POST',
	    url: "<?php echo $base_url ?>permission/save_list",
	    data: {user_group_id: strUserGroupId, data: JSON.stringify(arrData)},
	    dataType: "json",
	    success: function(respond){
	    	var url = "<?php echo $base_url ?>permission?gid=" + strUserGroupId;
	    	window.location = url;
	    }
	});
}

function FreezHeader(){
	$("#table-roles-permission").freezeHeader({offset: 89, container: "divRolesPermission"});
}

function ReDrawFreezHeader(){
	$("#hdtable-roles-permission").css("width", $("#divRolesPermission").css("width")).css("left", "auto");
}
</script>
