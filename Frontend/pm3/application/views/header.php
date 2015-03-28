<img id="imgLogo" width="28px" src="<?php echo $base_url?>asset/images/icons/vng_logo.png" />
<div class="nav">
</div>
<div id="divUserLogin">
	Hi&nbsp;<a href="<?php echo $base_url?>logout"><?php echo htmlentities($this->session->userdata('username'), ENT_QUOTES, "UTF-8") ?></a>
</div>