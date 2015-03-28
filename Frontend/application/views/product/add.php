<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/bootstrap/easyui.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/override.jquery-ui.css" />
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<script type="text/javascript">
$(function(){
	$('#cboDivision').combobox({
		onSelect: function(){
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
		onSelect: function(){
			FillDepartmentCode();
		}
		, 
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
	var arrElementId = ["iptCode", "iptAlias", "iptFACode", "iptFAAlias"];
	var bIsValid = IsAllAlphanumeric(arrElementId);
	return bIsValid;
}
</script>
<div style="overflow: visible;position: relative;padding-top:10px">
  <div id="ci-header" class="ci-header chkchk" style="float: right">
    <ul id="explore-subnav" class="ci-subnav no-buddy-icon">
      		<li class="ci-subnav-item"><a class="tlbList" href="<?php echo $base_url?><?php echo $strCIName ?>/index">List</a></li>
		    <li class="ci-subnav-item"><span class="tlbAddNew">Add new <?php echo $strCIName ?></span></li>
    </ul>
  </div>
  <div style="float: left;position:absolute;top:5px;">
    <h2 class="hostinfo-hostname">Product Management</h2>
  </div>
  <div class="clear"></div>
</div>
<?php echo $message; ?>
<form action="<?php echo $base_url?>product/index/add_sm" method="POST" id="frmCustomViewServer" onsubmit="return Validate()">
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
                	<label class="bold">Code</label><span class="require_mark">*</span>
                </td>
                <td>
                	<input id="iptCode" field="Code" type="text" value="<?php echo (isset($arrPreviousInputData['code']) ? $arrPreviousInputData['code'] : "") ?>" name="code" />
                    <?php if (isset($arrError['code']) && $arrError['code'] != "") { ?>
                    <label class="label-error"><span class="bullet-icon icon-error"></span><?php echo $arrError['code'] ?></label>
                    <?php } ?>
                </td>
              </tr>
              <tr class="odd">
                 <td class="wp120" align="right">
                 	<label class="bold">Alias</label><span class="require_mark">*</span>
                 </td>
                 <td>
                	<input id="iptAlias" field="Alias" type="text" value="<?php echo (isset($arrPreviousInputData['alias']) ? $arrPreviousInputData['alias'] : "") ?>" name="alias" />
                    <?php if (isset($arrError['alias']) && $arrError['alias'] != "") { ?>
                    <label class="label-error"><span class="bullet-icon icon-error"></span><?php echo $arrError['alias'] ?></label>
                    <?php } ?>
                 </td>
              </tr>
              <tr class="even">
                 <td class="wp120" align="right"><label class="bold">FA Code</label></td>
                 <td>
                	<input id="iptFACode" field="FA Code" type="text" value="<?php echo (isset($arrPreviousInputData['fa_code']) ? $arrPreviousInputData['fa_code'] : "") ?>" name="fa_code" />
                 </td>
              </tr>
              <tr class="odd">
               	 <td class="wp120" align="right"><label class="bold">FA Alias</label></td>
                 <td>
                	<input id="iptFAAlias" field="FA Alias" type="text" value="<?php echo (isset($arrPreviousInputData['fa_alias']) ? $arrPreviousInputData['fa_alias'] : "") ?>" name="fa_alias" />
                 </td>
              </tr>
              <tr class="even">
                <td class="wp120" align="right"><label class="bold">Type</label></td>
                <td>
                	<input type="radio" name="type"  value="Internal" <?php if ((isset($arrPreviousInputData['type']) && $arrPreviousInputData['type'] == "Internal") || (!isset($arrPreviousInputData['type']))) {?>checked="checked"<?php } ?> />Internal
                    <input type="radio" name="type"  value="External" <?php if ((isset($arrPreviousInputData['type']) && $arrPreviousInputData['type'] == "External")) {?>checked="checked"<?php } ?> />External
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
                    	<option value="<?php echo @$oDivision['_id'] ?>" <?php if(isset($arrPreviousInputData['division_id']) && (@$arrPreviousInputData['division_id'] == strval($oDivision['_id']))) { ?>selected="selected"<?php } ?>><?php echo @$oDivision['alias'] ?></option>
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
                    	<option value="" selected="selected">--Select Department--</option>
                        <?php if (!empty($arrCIInfoOption['department'])) { ?>
                       	 	<?php foreach ($arrCIInfoOption['department'] as $oDepartment) { ?>
                    	<option value="<?php echo @$oDepartment['_id'] ?>" <?php if(isset($arrPreviousInputData['department_id']) && (@$arrPreviousInputData['department_id'] == strval($oDepartment['_id']))) { ?>selected="selected"<?php } ?>><?php echo @$oDepartment['alias'] ?></option>
                        	<?php } ?>
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
    <button type="submit" name="save_and_continue" value="Add">Add</button>
    <button type="submit" name="save_and_exit" value="And and Exit">Add and Exit</button>
    <button type="button" onclick="window.location.href='<?php echo $base_url?>product/index'">Exit</button>
  </div>
</form>
