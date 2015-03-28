<?php if($isAjax){ ?>
<?php echo $strHTMLServerList ?>
<?php } else { ?>
<div style="width: px" class="ci-header chkchk" id="ci-header">
<h2>Server Management</h2>
    <ul class="ci-subnav no-buddy-icon" id="explore-subnav">
        <li class="ci-subnav-item"><span>List</span></li>
        <li class="ci-subnav-item"><a href="/commons/">Add new server</a></li>
        <li class="ci-subnav-item"><a href="/gettyimages/">Export info</a></li>
        <li class="ci-subnav-item"><a onclick="PopUpCustomView('<?php echo $base_url?>custom_view/index?cid=<?php echo CI_SERVER ?>')" href="#">Custom View</a></li>
    </ul>
</div>

<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<!-- <div class="max-width">
    <!-- <div class="content"> -->
    	<?php echo $message;?>
    	<div id="pp_tblServerListTop" class="easyui-pagination"></div>
    	<div id="divServerListContainer" style="overflow-x: auto">
    	<?php echo $strHTMLServerList ?>
		</div>
		<div id="pp_tblServerListBottom" class="easyui-pagination"></div>
    <!-- </div> -->
<!-- </div> -->
<input type="hidden" id="hidQueryString" value="k=<?php echo urlencode($strKeyword) ?>">
<div style="display:none"><div id="divServerDetail"></div></div>
<script type="text/javascript">
var iPageSize      = <?php echo $iPageSize ?>;
var iTotalRecords  = <?php echo $iTotalRow ?>;
var iCurrentPage   = <?php echo $iCurrentPage ?>;
</script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/server.server_list.js"></script>
<?php } ?>