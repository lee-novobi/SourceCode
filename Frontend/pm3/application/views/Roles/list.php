<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery.tablednd/tablednd.css" media="screen" />
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery.tablednd/jquery.tablednd.0.7.min.js"></script>
<div class="board-control">
	<?php echo $message;?>
	<input type="button" class="form-submit" value="+ Add Role">
</div>
<table id="table-roles" cellpadding="0" cellspacing="0" width="100%" class="list">
  <thead>
  	<tr>
  		<th class="wp20">&nbsp;</th>
  		<th>Role Name</th>
  		<th>Operations</th>
  	</tr>
  </thead>
  <?php $order = 0 ?>
  <?php foreach($arrRole as $oRole) {?>
  <tr>
  	<td class="t-center"><div class="drag-control">&nbsp;</div></td>
  	<td order="<?php echo $order ?>" role_id="<?php echo htmlentities($oRole['_id'], ENT_QUOTES, "UTF-8") ?>"><?php echo htmlentities($oRole['role_name'], ENT_QUOTES, "UTF-8") ?></td>
  	<td><a class="type-button" href="#">Edit</a></td>
  </tr>
  <?php $order++; ?>
  <?php } ?>
</table><br />
<div class="board-control">
	<input type="button" class="form-submit" value="Save Order" onclick="onRoleListSubmit()">
</div>
<script type="text/javascript">
$(document).ready(function(){
	// $("#table-roles").tableDnD({dragHandle: ".drag-control"});
	$("#table-roles").tableDnD();
});

function onRoleListSubmit(){
	var arrRole = new Array();

	$("#table-roles td[role_id]").each(function(index){
		arrRole.push({order: index, _id: $(this).attr("role_id")});
	});
	$.ajax({
		type: 'POST',
	    url: "<?php echo $base_url ?>roles/save_list",
	    data: {data: JSON.stringify(arrRole)},
	    dataType: "json",
	    success: function(respond){
	    	location.reload();
	    }
	});
}
</script>