<?php
// ------------------------------------------------------------------------
/**
* Error Logging Interface
*
* We use this as a simple mechanism to access the logging
* class and send messages to be logged.
*
* @access	public
* @return	void
*/
if ( ! function_exists('writelog'))
{
	function writelog($message)
	{
		if(function_exists('log_message')){
			log_message('USERLOG', $message);
		}
	}
}

// ------------------------------------------------------------------------
class AccessLogHook {
    function write_access_log() {
    	$oCI = & get_instance();
    	if($oCI){
	        writelog(sprintf('%s %s Request Method: %s', @str_pad($oCI->session->userdata('username'), 16), $_SERVER['REQUEST_URI'],
						$_SERVER['REQUEST_METHOD']));
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				writelog(sprintf('POST Data: %s', str_replace("\n", '', print_r($_POST, true))));
			}
    	}
    }

}
?>
