<table id="table-roles-permission" cellpadding="0" cellspacing="0" width="100%" class="list">
	<thead>
	  	<tr>
	  		<th nowrap>Permission</th>
	  		<?php foreach($arrRole as $oRole){ ?>
	  		<th class="t-center"><?php echo htmlentities($oRole['role_name'], ENT_QUOTES, "UTF-8") ?></th>
	  		<?php } ?>
	  	</tr>
	</thead>
	<?php foreach($arrPermission as $strGroupName=>$arrPn) { ?>
	<tbody group="<?php echo $strGroupName ?>">
	<tr row-type="title" class="ci-title <?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>"><th class="expanded" colspan="<?php echo count($arrRole)+1 ?>"><?php echo htmlentities($strGroupName, ENT_QUOTES, "UTF-8") ?></th></tr>
	<?php foreach($arrPn as $oPn){ ?>
	<tr row-type="item" class="<?php echo (($row_alternate=!$row_alternate)? 'odd' : 'even') ?>">
		<th><?php echo htmlentities($oPn['name'], ENT_QUOTES, "UTF-8") ?></th>
		<?php foreach($arrRole as $oRole){ ?>
  		<td class="t-center"><input value="1" type="checkbox" role_id="<?php echo $oRole['_id'] ?>" pn_type="<?php echo $oPn['type'] ?>" pn_id="<?php echo $oPn['_id'] ?>" <?php echo (@empty($arrSelectedGroupPermission[$oPn['type'] . '_' . $oRole['_id'].$oPn['_id']])?'':'checked="checked"') ?>></td>
  		<?php } ?>
	</tr>
	<?php } ?>
	</tbody>
	<?php } ?>
</table>
<script type="text/javascript">
$(function(){
	$("#table-roles-permission").tableCollapsible({groupByAttr: "group"});
})
</script>