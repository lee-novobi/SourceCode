<?php if(!empty($arrUsers)) { ?>
<?php foreach($arrUsers as $strUser=>$arrURInfor) { ?>
<tr>
	<!-- <td class="t-center"><input type="checkbox" name="chkUser" value="<?php echo $arrURInfor['_id']; ?>"></td> -->
	<td><?php echo $strUser ?></td>
	<td><?php echo @$arrURInfor['department'] ?></td>
	<td class="t-center">Active</td>
	<td>
		<?php foreach($arrURInfor['user_group_id'] as $strGroupId=>$oValue) { ?>
		<?php echo $arrURInfor['pm_role_name'][$strGroupId] . ' of ' . $arrURInfor['user_group'][$strGroupId] ?><br />
		<?php } ?>
	</td>
	<td>
		<?php foreach($arrURInfor['user_role_product'] as $oUR) { ?>
		<?php echo $oUR['user_group_name'] . ' ' . $oUR['role']['role_name'] . ' of ' . @$oUR['product']['alias'] ?><br />
		<?php } ?>
	</td>
	<td class="t-center">
		<a class="sm hand-pointer" id="lnkEdit" target="_blank" href="<?php echo $base_url ?>users/edit?uid=<?php echo $arrURInfor['username'] ?>" style="font-weight: bold;">Edit</a>
		<!-- <input type="submit" class="sm" id="btnDelete" name="delete" value="Delete" /> -->
		<!-- <a class="sm hand-pointer" id="lnkPermission" target="_blank" href="<?php echo $base_url ?>permission" style="font-weight: bold;">Permission</a> -->
	</td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
	<td colspan="7" class="t-center">No more data in this table.</td>
</tr>
<?php } ?>
