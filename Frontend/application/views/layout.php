<!DOCTYPE html>
<html>
<head>
<meta content="IE=9,chrome=1" http-equiv="X-UA-Compatible">
<meta charset="utf-8">
<title>uCMDB</title>
<link type="image/png" href="<?php echo $base_url?>asset/images/favicon.png" rel="shortcut icon">

<!-- STYLES -->
<link type="text/css" rel="stylesheet" href="<?php echo $base_url?>asset/js/dropdown/jquery.dropdown.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url?>asset/css/common.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<!-- END STYLES -->

<!-- JAVASCRIPTS -->
<script type="text/javascript">	var base_url = '<?php echo $base_url?>'; </script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/common.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/layout.js"></script>
<!-- END JAVASCRIPTS -->

<!-- JQUERY PLUGINS -->
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $base_url ?>asset/js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $base_url?>asset/js/dropdown/jquery.dropdown.js"></script>

<script type="text/javascript" src="<?php echo $base_url?>asset/js/jquery-easyui-1.3.2/jquery.easyui.min.js"></script>
<link rel="StyleSheet" type="text/css" href="<?php echo $base_url ?>asset/js/jquery-easyui-1.3.2/themes/bootstrap/easyui.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $base_url?>asset/js/jquery-easyui-1.3.2/themes/icon.css">
<link rel="stylesheet" type="text/css" href="<?php echo $base_url?>asset/css/override.jquery-ui.css">
<!-- END JQUERY PLUGINS -->
</head>
<body>
<div id="screen">
	<div id="header">
    	<?php echo $_header; ?>
	</div>
	<div id="body">
	    <div id="left-content">
	     	<?php echo $_horizontal_menu; ?>
	    </div>
	    <div id="right-content">
			<?php echo $_content; ?>
	    </div>
    	<div class="clear"></div>
	</div>
</div>
<div id="bttop">BACK TO TOP</div>
<!-- <div id="btmenu"><a href="javascript: SetSideMenuVisibility()">Menu</a></div> -->
<!-- <div style="position: fixed; top:0;width: 30px;height: 20px;color:white;background-color: red;z-index: 13000" id="divDebug"></div> -->
</body>
</html>
