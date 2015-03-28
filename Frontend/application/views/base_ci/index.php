<?php if($isAjax){ ?>
<?php echo $strHTMLCIList ?>
<?php } else { ?>
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquerymultiselect/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquerymultiselect/jquery.multiselect.filter.css" />
<!--<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />-->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquerymultiselect/jquery.multiselect.js"></script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquerymultiselect/jquery.multiselect.filter.js"></script>

<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery.freezeheader.js"></script>
<link href="<?php echo $base_url ?>asset/js/jquery.freezeheader.css" rel="stylesheet" type="text/css">

<div style="overflow: visible;position: relative;padding-top:10px">
	<div style="float: right" class="ci-header chkchk" id="ci-header">
		<ul class="ci-subnav no-buddy-icon" id="explore-subnav">
		    <li class="ci-subnav-item"><span class="tlbList">List</span></li>
		    <li class="ci-subnav-item"><a class="tlbAddNew" href="<?php echo $base_url?><?php echo $strCIName ?>/index/add">Add new <?php echo $strCIName ?></a></li>
		    <li class="ci-subnav-item"><a class="tlbCustomView" onclick="PopUpCustomView('<?php echo $base_url?>custom_view/index?cid=<?php echo $nCIType ?>')" href="#">Custom View</a></li>
		</ul>
	</div>
	<div style="float: left;">
		<h2 class="hostinfo-hostname"><?php echo ucfirst($strCIName) ?> Management</h2>
        <form id="f2" method="post" onsubmit="GetKeyword();">
            <input type="hidden" id="hidQueryString" value="k=<?php echo urlencode($strKeyword) ?>">
            <input type="hidden" id="hidKeyword" name="k" value="<?php echo urldecode($strKeyword) ?>">
                <?php echo $strHTMLFilter; ?>
        </form>
	</div>
	<div class="clear"></div>
</div>

<!-- <div class="max-width">
    <!-- <div class="content"> -->
    	<?php echo $message;?>
    	<div id="pp_tblCIListTop" class="easyui-pagination"></div>
    	<div id="divCIListContainer" style="overflow-x: auto">
    	<?php echo $strHTMLCIList ?>
		</div>
		<div id="pp_tblCIListBottom" class="easyui-pagination"></div>
    <!-- </div> -->
<!-- </div> -->


<div style="display:none"><div id="divCIDetail"></div></div>
<script type="text/javascript">
var iPageSize      = <?php echo $iPageSize ?>;
var iTotalRecords  = <?php echo $iTotalRow ?>;
var iCurrentPage   = <?php echo $iCurrentPage ?>;
var strURLData     = '<?php echo $strURLListData ?>';
function GetKeyword() {
	var strKeyword = $("#iptSearch").val();
	$("#hidKeyword").val(strKeyword);
	$("#hidQueryString").val("k=" + strKeyword);
}
</script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/base_ci.base_ci_list.js"></script>
<?php } ?>