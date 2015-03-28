<!DOCTYPE html>
<html>
<head>
<meta content="IE=9,chrome=1" http-equiv="X-UA-Compatible">
<meta charset="utf-8">
<title>uCMDB</title>
<link type="image/png" href="<?php echo $base_url?>asset/images/favicon.png" rel="shortcut icon">

<!-- STYLES -->
<link type="text/css" rel="stylesheet" href="<?php echo $base_url?>asset/css/yui/cssreset-min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url?>asset/css/common.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery.freezeheader/jquery.freezeheader.css" media="screen" />
<!-- END STYLES -->

<!-- JAVASCRIPTS -->
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/common.js"></script>
<!-- END JAVASCRIPTS -->

<!-- JQUERY PLUGINS -->
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/jquery.freezeheader/jquery.freezeheader.js"></script>
<!-- END JQUERY PLUGINS -->
</head>
<body>
<div id="screen">
	<div id="header">
    	<?php echo $_header; ?>
	</div>
	<div class="header-tab">
    	<ul>
    		<li id="menu-users"><a href="<?php echo $base_url ?>users">LIST</a></li>
    		<li id="menu-user_groups"><a href="<?php echo $base_url ?>user_groups">USER GROUP</a></li>
    		<li id="menu-permission"><a href="<?php echo $base_url ?>permission">PERMISSION</a></li>
    		<li id="menu-roles"><a href="<?php echo $base_url ?>roles">ROLES</a></li>
    		<?php if($this->session->userdata('usertype')==USERTYPE_SUPERADMIN){ ?><li id="menu-pm_permission"><a href="<?php echo $base_url ?>pm_permission">ADMIN</a></li><?php } ?>
    	</ul>
	</div>
	<div id="body">
	    <div>
			<?php echo $_content; ?>
	    </div>
    	<div class="clear"></div>
	</div>
</div>
<div id="bttop">BACK TO TOP</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#menu-<?php echo strtolower($moduleName)?>').addClass('top-menu-active');
});
var base_url = '<?php echo $base_url?>';
</script>
</body>
</html>
