<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery.ui/no-theme/jquery-ui-1.10.4.min.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-ui-multiselect/jquery.multiselect.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/override.jquery-ui.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/user.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/default/easyui.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/icon.css" />

<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery.ui/jquery-ui-1.10.4.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery.ui/jquery-ui.autocomplete.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery-ui-multiselect/jquery.multiselect.js"></script>

<div class="board-control">
	<?php echo $message;?>

		<!-- <input type="button" class="form-submit" value="+ Add User"> -->
		<div style="margin: 5px"><a class="styled-button" style="color: #fff !important;" target="_blank" href="<?php echo $base_url ?>users/add">+ Add User</a></div>
		<form method="get" id="frmFilter">
		<table id="tblFilter" cellpadding="0" cellspacing="0" width="100%" class="list">
			<tbody>
				<tr>
					<th class="t-right">Name or Email contains</th>
					<td colspan="5">
						<input id="txtSearchByNameOrEmail" name="search_by_name" class="input-medium wp200" value="<?php if(isset($_GET['search_by_name']) && $_GET['search_by_name']) echo trim(urldecode($_GET['search_by_name'])) ?>" />
					</td>
				</tr>
				<tr>
					<th class="t-right">Department</th>
					<td><select id="cboDepartment" name="department" class="wp200">
							<option value=""></option>
							<?php foreach($arrDepartment as $oDepartment) { ?>
							<option value="<?php echo (string)$oDepartment['_id'] ?>" <?php if(isset($_GET['department']) && $_GET['department']!='' && $_GET['department'] === (string)$oDepartment['_id']) {?>selected="selected"<?php } ?>><?php echo $oDepartment['alias'] ?></option>
							<?php } ?>
						</select>
					</td>
					<th class="wp110 t-right">Product</th>
					<td id="celProduct"><select id="cboProduct" name="product" class="wp200">
							<option value=""></option>
							<?php foreach($arrProduct as $oProduct) { ?>
							<option value="<?php echo (string)$oProduct['_id'] ?>" <?php if(isset($_GET['product']) && $_GET['product']!='' && $_GET['product'] === (string)$oProduct['_id']) {?>selected="selected"<?php } ?>><?php echo $oProduct['alias'] ?></option>
							<?php } ?>
						</select>
					</td>
					<th class="wp110 t-right">User Group</th>
					<td><select id="cboUserGroups" name="user_group" class="wp200">
							<option value=""></option>
							<?php foreach($arrUserGroups as $oUG) { ?>
								<option value="<?php echo (string)$oUG['_id'] ?>" <?php if(isset($_GET['user_group']) && $_GET['user_group']!='' && $_GET['user_group'] === (string)$oUG['_id']) {?>selected="selected"<?php } ?>><?php echo $oUG['group_name'] ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<th class="t-right">Status</th>
					<td><select id="cboStatus" name="status" class="wp200">
							<option value="0"></option>
							<option value="1">Active</option>
						</select>
					</td>
					<th class="wp110 t-right">Role</th>
					<td><select id="cboRole" name="role" class="wp200">
							<option value=""></option>
							<?php foreach($arrRoles as $oRole) { ?>
								<option value="<?php echo (string)$oRole['_id'] ?>" <?php if(isset($_GET['role']) && $_GET['role']!='' && $_GET['role'] === (string)$oRole['_id']) {?>selected="selected"<?php } ?>><?php echo $oRole['role_name'] ?></option>
							<?php } ?>
						</select>
					</td>
					<th class="wp110 t-right">Permission</th>
					<td><select id="cboPermission" name="permission" class="wp200">
							<option value="">All Permission</option>
							<?php foreach($arrPermission as $strGroup=>$arrPermission) { ?>
								<optgroup label="<?php echo $strGroup ?>">
									<?php foreach($arrPermission as $idx=>$oPermission) { ?>
										<option group="<?php echo $strGroup ?>" value="<?php echo (string)$oPermission['_id'] ?>" <?php if(isset($_GET['permission']) && $_GET['permission']!='' && $_GET['permission'] === (string)$oPermission['_id']) {?>selected="selected"<?php } ?>><?php echo $oPermission['name'] ?></option>
									<?php } ?>
								</optgroup>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="6" class="t-center"><input type="submit" class="form-submit" value="Filter" /></td>
				</tr>
			</tbody>
		</table> <!-- End #tblFilter -->
		<input type="hidden" id="hidPageUserRoleInfo" value="<?php echo $iPageUR ?>" name="page_user_role" />
		<input type="hidden" id="hidPageSizeUserRoleInfo" value="<?php echo $iPageSizeUR ?>" name="limit_user_role" />
		</form>

</div> <!-- End .board-control --><br />
<div id="divUserRoleInfo">
	<table width="100%" cellspacing="0" cellpadding="0" class="list" id="tblUserRoleInfo" border="0">
        <thead>
          <tr>
          	<!-- <th class="t-center wp50"><input type="checkbox" id="check_all"></th> -->
            <th class="t-left wp100">Username</th>
            <th class="t-left wp50">Department</th>
            <th class="t-center wp30">Status</th>
            <th class="t-center wp100">Member of Group</th>
            <th class="t-center wp150">Member of Product</th>
            <th class="t-center wp160">Operation</th>
          </tr>
        </thead>
        <tbody>
        	<?php echo $strUserRoleView ?>
        </tbody>
      </table>
</div>
<div id="div-pagination-user-role" style="background:#efefef; border: 1px solid #CCCCCC;"></div>
<input type="hidden" id="hidQueryString" value="<?php echo $strQueryString ?>">
<script type="text/javascript">
	var iPageURInfo = <?php echo $iPageUR ?>;
	var iPageSizeURInfo = <?php echo $iPageSizeUR ?>;
	var iTotalRecords = <?php echo $iTotal ?>;

	function OnURInfoPageChange(strPageUR, strPageSizeUR) {
		var strQueryString = $("#hidQueryString").val();

		var strURL = base_url + 'users?page_user_role=' + strPageUR + '&limit_user_role=' + strPageSizeUR;
		if(strQueryString != "") {
			strURL = strURL + '&' + strQueryString;
		}

		window.location = strURL;
	}

	$(document).ready(function() {
		DepartmentBindChange();
		ProductBindChange();
		$("#cboUserGroups").combobox();
		$("#cboStatus").combobox();
		$("#cboRole").combobox();

		$('#cboPermission').multiselect({
			header: false,
			noneSelectedText: "Select an Option",
			multiple: false,
			selectedList: 1,
			height: 400
		});

		// update checkbox check all event
		$('#check_all').click(function() {
			var checked_status = this.checked;
			$("input[name=chkUser]").each(function()
			{
				this.checked = checked_status;
			});
		});

		$('#div-pagination-user-role').pagination({
		    total: iTotalRecords,
		    pageNumber: iPageURInfo,
		    pageSize: iPageSizeURInfo,
		    showPageList: true,
		    showRefresh: false,
			pageList: [10,20,50,100,200],

			onSelectPage: function(){
				OnURInfoPageChange(strPageUR, strPageSizeUR);
			},
			onChangePageSize: function(strPageSizeUR){
				OnURInfoPageChange(1, strPageSizeUR);
			}
		});
		FreezHeader();
		$( window ).resize(function() {
			setTimeout(function(){ReDrawFreezHeader()}, 100);
		});
	});

	function DepartmentBindChange() {
		$("#cboDepartment").combobox().bind('change', function() {
			var department_key = this.options[this.selectedIndex].value;
			var url = base_url + 'users/ajax_get_product_by_department/cboProduct/product/wp200?department=' + department_key;
			var strProductHtml = AjaxLoad(url);
			$("#celProduct").html(strProductHtml);
			ProductBindChange();
		});
	}
	function ProductBindChange(){
		$("#cboProduct").combobox();
	}

	function FreezHeader(){
		$("#tblUserRoleInfo").freezeHeader({offset: 89, container: "divUserRoleInfo"});
	}

	function ReDrawFreezHeader(){
		$("#hdtblUserRoleInfo").css("width", $("#divUserRoleInfo").css("width")).css("left", "auto");
	}
</script>