<img id="imgLogo" width="28px" src="<?php echo $base_url?>asset/images/icons/vng_logo.png" />
<form method="GET" id="f2" action="<?php echo $base_url?><?php echo (!empty($active_CI)) ? $active_CI['ci_name'] : DEFAULT_CI_KEY_SEARCH; ?>/index">
  <div id="search-wrapper">
    <input type="text" value="<?php echo @$_GET['k'] ?>" name="k" id="iptSearch">
    <input type="button" class="" value="<?php echo (!empty($active_CI)) ? $active_CI['display_name'] : DEFAULT_CI_NAME_SEARCH; ?>" data-dropdown="#dropdown-1" id="iptCIs">
    <input type="hidden" value="<?php echo (!empty($active_CI)) ? $active_CI['ci_name'] : DEFAULT_CI_KEY_SEARCH; ?>" id="hidSelectedCI">
    <div class="dropdown dropdown-tip" id="dropdown-1">
      <ul style="display: block;" class="dropdown-menu">
        <li><a onclick="SelectCI('division', 'Division')"><span class="topmenu-icon icon-division"></span> Division</a></li>
        <li><a onclick="SelectCI('department', 'Department')"><span class="topmenu-icon icon-department"></span> Department</a></li>
        <li><a onclick="SelectCI('product', 'Product')"><span class="topmenu-icon icon-product"></span> Product</a></li>
        <li class="dropdown-divider"></li>
        <li><a onclick="SelectCI('server', 'Server')"><span class="topmenu-icon icon-server"></span> Server</a> </li>
      </ul>
    </div>
  </div>
  <div id="go-btn-wrapper">
    <button type="submit" id="iptGo" value="GO!"><img width="22px" src="<?php echo $base_url ?>asset/images/icons/metro/dark/appbar.magnify.png"></button>
  </div>
</form>
<div class="nav">
  <ul>
    <li class="top-menu-active"><a href=""><img src="<?php echo $base_url?>asset/images/icons/metro/dark/appbar.home.png" width="25px" />Home</a></li>
    <li><a href=""><img src="<?php echo $base_url?>asset/images/icons/metro/dark/appbar.cabinet.files.png" width="25px" />Report</a></li>
    <li><a href=""><img src="<?php echo $base_url?>asset/images/icons/metro/dark/appbar.clipboard.variant.edit.png" width="25px" />Admin</a></li>
    <li><a href=""><img src="<?php echo $base_url?>asset/images/icons/metro/dark/appbar.chat.png" width="25px" />Contact</a></li>
  </ul>
  <div class="clear"></div>
</div>
<!-- <div id="user-wrapper">
            	<img src="<?php echo $base_url?>asset/images/uploads/users/dungdv2.jpg" width="30px" />&nbsp;
				<span id="welcome-text">Welcome, <?php $strTmpUName = $this->session->userdata('username'); echo empty($strTmpUName)?'Guest':$this->session->userdata('userfullname'); ?></span>&nbsp &nbsp
                <a href="<?php echo $base_url?>logout">Logout</a>

                <br/>

  	</div> -->
<script type="text/javascript">
   function SelectCI(strModule, strCI) {
   		var strCI = $("#iptCIs").val(strCI);
        $("#f2").attr("action", base_url + strModule + "/index");
   }
</script>