<?php
register_shutdown_function('shutdown_hook', $this);
function shutdown_hook(&$oCI){
	if(!empty($oCI) && !empty($oCI->hooks)){
		chdir(FCPATH);
		$oCI->hooks->_call_hook('post_system');
	}
}
?>
