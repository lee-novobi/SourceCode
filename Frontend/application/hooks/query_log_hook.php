<?php
class QueryLogHook {
    function log_queries() {
        $oCI = & get_instance();
		if(isset($oCI->db4log) && !empty($oCI->db4log)){
	        $arrTimes   = $oCI->db4log->query_times;
	        $strOutput  = '';
	        $arrQueries = $oCI->db4log->queries;

	        if (count($arrQueries) > 0)
	        {
	        	$oCI->load->library('my_session','','session');
	        	$strUser = @str_pad($oCI->session->userdata('username'), 16);
	            foreach ($arrQueries as $nKey=>$strQuery)
	            {
	                $strOutput .= $strUser . str_pad($_SERVER['REMOTE_ADDR'], 16) . date('Y-m-d H:i:s') . " -->\t" . str_replace(array("\r\n","\n")," ", $strQuery) . "\n";
	            }
	            $nTook = round(doubleval($arrTimes[$nKey]), 3);
	            $strOutput .= "[took:{$nTook}]======================================================\n";
	        }

	        $oCI->load->helper('file');
	        if ( ! write_file(APPPATH  . "/logs/queries.log." . date('Ymd'), $strOutput, 'a+'))
	        {
	             log_message('debug','Unable to write query the file');
	        }
		}
    }

}
?>
