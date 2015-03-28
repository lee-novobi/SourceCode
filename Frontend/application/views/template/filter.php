<?php if (!empty($arrFilter)) { ?>
<table id="tblFilter" style="display: none">
    <tbody>
    <tr>
    <?php foreach ($arrFilter as $oFilter) { ?>
    <td align="right"><?php echo $oFilter['display_name']; ?></td> 
    <td align="left"><select id="cbo_<?php echo $oFilter['name'] ?>" multiple="multiple" name="<?php echo $oFilter['db_field'] ?>[]" class="wp120" >
    	<?php foreach ($oFilter['options'] as $oOption) { ?>
        <?php if ($oOption[$oFilter['value_field']] != "" && $oOption[$oFilter['name_field']] != "" ) { ?>
		<option value="<?php echo $oOption[$oFilter['value_field']]; ?>" <?php if (@in_array($oOption[$oFilter['value_field']], $oFilter['selected'])) { ?>selected="selected" <?php }?>><?php echo $oOption[$oFilter['name_field']]; ?></option>
    	<?php } ?>
        <?php } ?>
		</select> &nbsp;&nbsp;&nbsp;&nbsp;
        <script type="text/javascript">
			$(function(){
				$("#cbo_<?php echo $oFilter['name'] ?>").multiselect().multiselectfilter();
			});
		</script>
        <input type="hidden" class="hidFilterOption" key="<?php echo $oFilter['name'] ?>" id="hid_<?php echo $oFilter['name'] ?>" value='<?php echo @json_encode($oFilter['selected']); ?>' />
        </td>
     <?php } ?>
        <td><button type="submit">Apply</button></td>
    </tr>
    </tbody>
</table>
<?php } ?>
<script type="text/javascript">
	$(function(){
		$("#tblFilter").css("display", "inline");
	});
</script>