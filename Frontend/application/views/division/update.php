<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/bootstrap/easyui.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/css/override.jquery-ui.css" />
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<div style="overflow: visible;position: relative;padding-top:10px">
  <div id="ci-header" class="ci-header chkchk" style="float: right">
    <ul id="explore-subnav" class="ci-subnav no-buddy-icon">
       		<li class="ci-subnav-item"><a class="tlbList" href="<?php echo $base_url?><?php echo $strCIName ?>/index">List</a></li>
    </ul>
  </div>
  <div style="float: left;position:absolute;top:5px;">
    <h2 class="hostinfo-hostname"><?php echo ucfirst($strCIName); ?> Management</h2>
  </div>
  <div class="clear"></div>
</div>
<?php echo $message; ?>
<script type="text/javascript">
function Validate() 
{
	var arrElementId = ["iptCode", "iptAlias"];
	var bIsValid = IsAllAlphanumeric(arrElementId);
	return bIsValid;
}
</script>
<form action="<?php echo $base_url?><?php echo $strCIName ?>/index/update_sm" method="POST" id="f2" onsubmit="return Validate()">
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
               
               <tr class="odd">
                 <td class="wp120" align="right">
                 	<label class="bold">HR ID</label>
                 </td>
                 <td>
                	<input type="text" value="<?php echo (isset($oCI['hr_id']) ? $oCI['hr_id'] : "") ?>" name="hr_id" readonly="readonly" />
                 </td>
              </tr>
               <tr class="even">
                <td class="wp120" align="right">
                	<label class="bold">HR Code</label>
                </td>
                <td>
                	<input type="text" value="<?php echo (isset($oCI['hr_code']) ? $oCI['hr_code'] : "") ?>" name="hr_code" readonly="readonly" />
                </td>
              </tr>
              <tr class="odd">
                <td class="wp120" align="right">
                	<label class="bold">Code</label><span class="require_mark">*</span>
                </td>
                <td>
                	<input id="iptCode" field="Code" type="text" value="<?php echo (isset($oCI['code']) ? $oCI['code'] : "") ?>" name="code" />
                    <?php if (isset($arrError['code']) && $arrError['code'] != "") { ?>
                    <label class="label-error"><span class="bullet-icon icon-error"></span><?php echo @$arrError['code'] ?></label>
                    <?php } ?>
                </td>
              </tr>
              <tr class="even">
                 <td class="wp120" align="right">
                 	<label class="bold">Alias</label><span class="require_mark">*</span>
                 </td>
                 <td>
                	<input id="iptAlias" field="Alias" type="text" value="<?php echo (isset($oCI['alias']) ? $oCI['alias'] : "") ?>" name="alias" />
                    <?php if (isset($arrError['alias']) && $arrError['alias'] != "") { ?>
                    <label class="label-error"><span class="bullet-icon icon-error"></span><?php echo @$arrError['alias'] ?></label>
                    <?php } ?>
                 </td>
              </tr>
              <tr class="odd">
                <td class="wp120" align="right"><label class="bold">Status</label></td>
                <td>
                	<input disabled="disabled" type="radio" name="status" value="<?php echo VALUE_DIVISION_STATUS_ACTIVE ?>" <?php if (isset($oCI['status']) && $oCI['status'] == VALUE_DIVISION_STATUS_ACTIVE) {?>checked="checked"<?php } ?> /><?php echo STR_DIVISION_STATUS_ACTIVE ?>
                    <input disabled="disabled" type="radio" name="status" value="<?php echo VALUE_DIVISION_STATUS_INACTIVE ?>" <?php if (isset($oCI['status']) && $oCI['status'] == VALUE_DIVISION_STATUS_INACTIVE) {?>checked="checked"<?php } ?> /><?php echo STR_DIVISION_STATUS_INACTIVE ?>
                </td>
              </tr>

            </tbody>
          </table>
          <br>
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
