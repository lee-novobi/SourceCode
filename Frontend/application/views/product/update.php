<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/bootstrap/easyui.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/override.jquery-ui.css" />
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<script type="text/javascript">
$(function(){
	$('#cboDivision').combobox({
		onChange: function(){
			var strDivisionId = $('#cboDivision').combobox('getValue');
            var url = base_url + 'department/index/ajax_get_department_by_division/department_id/cboDepartment/wp200?division_id=' + strDivisionId;
			strHtml = AjaxLoad(url);
            $("#tdDepartment").html(strHtml);
			LoadDepartmentComboBox();
        }
		, 
		filter: function(q,row){
			 var opts = $(this).combobox('options');
			 return row[opts.textField].toUpperCase().indexOf(q.toUpperCase()) >= 0;
		}
	});
	LoadDepartmentComboBox();
});

function LoadDepartmentComboBox() 
{
	$('#cboDepartment').combobox({
		onChange: function(){
			FillDepartmentCode();
		},
		filter: function(q,row){
			 var opts = $(this).combobox('options');
			 return row[opts.textField].toUpperCase().indexOf(q.toUpperCase()) >= 0;
		}
	});
	FillDepartmentCode();
}

function FillDepartmentCode()
{
	var strDepartmentId = $('#cboDepartment').combobox('getValue');
	var url = base_url + 'department/index/ajax_get_department_code_by_department_id?department_id=' + strDepartmentId;
	strHtml = AjaxLoad(url);
	$("#iptDepartmentCode").val(strHtml);
}

function Validate() 
{
	var arrElementId = ["iptAlias", "iptFACode", "iptFAAlias"];
	var bIsValid = IsAllAlphanumeric(arrElementId);
	return bIsValid;
}
</script>
<div style="overflow: visible;position: relative;padding-top:10px">
  <div id="ci-header" class="ci-header chkchk" style="float: right">
    <ul id="explore-subnav" class="ci-subnav no-buddy-icon">
       		<li class="ci-subnav-item"><a class="tlbList" href="<?php echo $base_url?><?php echo $strCIName ?>/index">List</a></li>
		    <li class="ci-subnav-item"><a class="tlbAddNew" href="<?php echo $base_url?><?php echo $strCIName ?>/index/add">Add new <?php echo $strCIName ?></a></li>
    </ul>
  </div>
  <div style="float: left;position:absolute;top:5px;">
    <h2 class="hostinfo-hostname">Product Management</h2>
  </div>
  <div class="clear"></div>
</div>
<?php echo $message; ?>
<form action="<?php echo $base_url?>product/index/update_sm" method="POST" id="f2" onsubmit="return Validate()">
<input type="hidden" name="cid" value="<?php echo $oCI['_id'];?>" />
  <table width="100%" cellspacing="10" cellpadding="0">
    <tbody>
      <tr>
        <td valign="top" col="1"><table width="100%" cellspacing="0" cellpadding="0" class="detail-zebra td-bordered" group="Basic Information">
            <tbody>
              <tr class="odd">
                <th class="group-field" colspan="2"> 
                  <label for="basic_information">Basic Information</label>
                </th>
              </tr>
              <tr class="even">
                <td class="wp120" align="right">
                	<label class="bold">Code</label>
                </td>
                <td>
                	<input type="text" value="<?php echo (isset($oCI['code']) ? $oCI['code'] : "") ?>" readonly="readonly" name="code" />
                </td>
              </tr>
              <tr class="odd">
                 <td class="wp120" align="right">
                 	<label class="bold">Alias</label><span class="require_mark">*</span>
                 </td>
                 <td>
                	<input id="iptAlias" field="Alias" type="text" value="<?php echo (isset($oCI['alias']) ? $oCI['alias'] : "") ?>" name="alias" />
                    <?php if (isset($arrError['alias']) && $arrError['alias'] != "") { ?>
                    <label class="label-error"><span class="bullet-icon icon-error"></span><?php echo $arrError['alias'] ?></label>
                    <?php } ?>
                 </td>
              </tr>
              <tr class="even">
                 <td class="wp120" align="right"><label class="bold">FA Code</label></td>
                 <td>
                	<input type="text" id="iptFACode" field="FA Code" value="<?php echo (isset($oCI['fa_code']) ? $oCI['fa_code'] : "") ?>" name="fa_code" />
                 </td>
              </tr>
              <tr class="odd">
               	 <td class="wp120" align="right"><label class="bold">FA Alias</label></td>
                 <td>
                	<input type="text" id="iptFAAlias" field="FA Alias" value="<?php echo (isset($oCI['fa_alias']) ? $oCI['fa_alias'] : "") ?>" name="fa_alias" />
                 </td>
              </tr>
              <tr class="even">
                <td class="wp120" align="right"><label class="bold">Type</label></td>
                <td>
                	<input type="radio" name="type"  value="Internal" <?php if (isset($oCI['type']) && $oCI['type'] == "Internal") {?>checked="checked"<?php } ?> />Internal
                    <input type="radio" name="type"  value="External" <?php if (isset($oCI['type']) && $oCI['type'] == "External") {?>checked="checked"<?php } ?> />External
                </td>
              </tr>
              <tr class="odd">
                <td class="wp120" align="right"><label class="bold">Status</label></td>
                <td>
                	<input type="radio" name="status" value="<?php echo VALUE_PRODUCT_STATUS_NEW ?>" <?php if (isset($oCI['status']) && $oCI['status'] == VALUE_PRODUCT_STATUS_NEW) {?>checked="checked"<?php } ?> /><?php echo STR_PRODUCT_STATUS_NEW ?>
                    <input type="radio" name="status" value="<?php echo VALUE_PRODUCT_STATUS_IN_USED ?>" <?php if (isset($oCI['status']) && $oCI['status'] == VALUE_PRODUCT_STATUS_IN_USED) {?>checked="checked"<?php } ?> /><?php echo STR_PRODUCT_STATUS_IN_USED ?>
                    <input type="radio" name="status" value="<?php echo VALUE_PRODUCT_STATUS_TRANSFERRING ?>" <?php if (isset($oCI['status']) && $oCI['status'] == VALUE_PRODUCT_STATUS_TRANSFERRING) {?>checked="checked"<?php } ?> /><?php echo STR_PRODUCT_STATUS_TRANSFERRING ?>
                    <input type="radio" name="status" value="<?php echo VALUE_PRODUCT_STATUS_REMOVED ?>" <?php if (isset($oCI['status']) && $oCI['status'] == VALUE_PRODUCT_STATUS_REMOVED) {?>checked="checked"<?php } ?> /><?php echo STR_PRODUCT_STATUS_REMOVED ?>
                    <input type="radio" name="status" value="<?php echo VALUE_PRODUCT_STATUS_CLOSED ?>" <?php if (isset($oCI['status']) && $oCI['status'] == VALUE_PRODUCT_STATUS_CLOSED) {?>checked="checked"<?php } ?> /><?php echo STR_PRODUCT_STATUS_CLOSED ?>
                </td>
              </tr>

            </tbody>
          </table>
          <br>
          <br>
          <table width="100%" cellspacing="0" cellpadding="0" class="detail-zebra td-bordered" group="Operation Information">
            <tbody>
              <tr class="odd">
                <th class="group-field" colspan="2"> 
                  <label for="operation_information">Operation Information</label>
                </th>
              </tr>
              <tr class="even">
                <td class="wp120" align="right"><label class="bold">Division</label><span class="require_mark">*</span></td>
                <td>
                	<select name="division_id" id="cboDivision" class="wp200">
                    	<option value="" selected="selected">--Select Division--</option>
                        <?php if (!empty($arrCIInfoOption['division'])) { ?>
                       	 	<?php foreach ($arrCIInfoOption['division'] as $oDivision) { ?>
                    	<option value="<?php echo @$oDivision['_id'] ?>" <?php if(isset($oCI['division_id']) && (@$oCI['division_id'] == strval($oDivision['_id']))) { ?>selected="selected"<?php } ?>><?php echo @$oDivision['alias'] ?></option>
                        	<?php } ?>
                        <?php } ?>
                    </select>
                    <?php if (isset($arrError['division_id']) && $arrError['division_id'] != "") { ?>
                    <label class="label-error"><span class="bullet-icon icon-error"></span><?php echo $arrError['division_id'] ?></label>
                    <?php } ?>
                </td>
              </tr>
              <tr class="odd">
                 <td class="wp120" align="right"><label class="bold">Department Alias</label><span class="require_mark">*</span></td>
                <td id="tdDepartment">
                	<select name="department_id" id="cboDepartment" class="wp200">
                    	
                        <?php if (!empty($arrCIInfoOption['department'])) { ?>
                       	 	<?php foreach ($arrCIInfoOption['department'] as $oDepartment) { ?>
                            <?php if ($oDepartment['alias'] != null && $oDepartment['alias'] != "") { ?>
                    	<option value="<?php echo @$oDepartment['_id'] ?>" <?php if(isset($oCI['department_id']) && (strval($oCI['department_id']) == strval($oDepartment['_id']))) { ?>selected="selected"<?php } ?>><?php echo @$oDepartment['alias'] ?></option>
                       		<?php } ?>
                        	<?php } ?>
                        <?php } else { ?>
                        <option value="" selected="selected">--Select Department--</option>
                        <?php } ?> 
                    </select>
                    <?php if (isset($arrError['department_id']) && $arrError['department_id'] != "") { ?>
                    <label class="label-error"><span class="bullet-icon icon-error"></span><?php echo $arrError['department_id'] ?></label>
                    <?php } ?>
                </td>
              </tr>
              <tr class="even">
                <td class="wp120" align="right"><label class="bold">Department Code</label></td>
                <td>
                	<input name="department_code" readonly="readonly" value="" id="iptDepartmentCode"/>
                </td>
              </tr>
            </tbody>
          </table>
         </td>
      </tr>
    </tbody>
  </table>
  <div class="t-center">
    <button type="submit" name="save_and_continue" value="Update">Update</button>
    <button type="submit" name="save_and_exit" value="Update and Exit">Update and Exit</button>
    <button type="button" onclick="window.location.href='<?php echo $base_url?><?php echo $strCIName ?>/index'">Exit</button>
  </div>
</form>
