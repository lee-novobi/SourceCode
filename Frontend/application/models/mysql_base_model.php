<?php
class Mysql_base_model extends CI_Model {

	var $tblArea;
	var $tblSubarea;
	var $tblAssignee;
	var $tblAssignmentGroup;
	var $tblDeparment;
	var $tblProduct;
	var $tblBugCategory;
	var $tblBugUnit;
	var $tblIncidentFollow;
	var $tblActionHistory;
	var $tblUser;
	var $tblAvaya;
	var $tblIncidentHistory;
	var $tblChangeFollow;
	var $tblChangeHistory;
	var $tblShiftTransferInfo;
	var $tblShiftScheduleAssign;
	var $tblVNGStaffList;
	var $tblCriticalAsset;

	function __construct()
	{
		// Call the Model constructor
		parent :: __construct();
		$this->db_ma = $this->load->database('monitoring_assistant', TRUE);
	}

	// ------------------------------------------------------------------------------------------ //
	public function GetUniqueITSMProduct()
	{
		$this->db_ma->select('*');
		$this->db_ma->from($this->tblProduct);
		$this->db_ma->where(array('is_itsm_product'=> 1));
		$this->db_ma->order_by('name', 'ASC');
		$query = $this->db_ma->get();
		//vd($this->db_ma->last_query());
		if ( $query->num_rows() > 0 )
		{
			$result = $query->result();
			return $result;
		}
		else
			return null;
	}

	// ------------------------------------------------------------------------------------------ //
	public function GetUniqueITSMDepartment()
	{
		$this->db_ma->distinct();
		$this->db_ma->select('*');
		$this->db_ma->from($this->tblDeparment);
		$this->db_ma->where(array('is_itsm_department'=> 1));
		$this->db_ma->order_by('name', 'ASC');
		$query = $this->db_ma->get();

		if ( $query->num_rows() > 0 )
		{
			$result = $query->result();
			return $result;
		}
		else
			return null;
	}

	// ------------------------------------------------------------------------------------------ //
	function GetProductByDepartment($strDepartment){
		$this->db_ma->select('*');
		$this->db_ma->from($this->tblProduct);
		$this->db_ma->where(array(
								'is_itsm_product' => 1
								, 'UPPER(department_name)' => strtoupper($strDepartment)
								, 'deleted' => '0'));
		$query = $this->db_ma->get();

		if ( $query->num_rows() > 0 )
		{
			$result = $query->result();
			return $result;
		}
		else
			return null;
	}

	// ------------------------------------------------------------------------------------------ //
	function GetDepartmentOfProduct($strProduct) {
		$sql = "SELECT department_name FROM " . $this->tblProduct . " WHERE name LIKE '" . $this->db_ma->escape_like_str($strProduct);
		$sql .= "' AND deleted='0' AND is_itsm_product=1 LIMIT 1";

		$query = $this->db_ma->query($sql);

		if ( $query->num_rows() > 0 )
		{
			$result = $query->result();
			return $result[0];
		}
		else
			return null;
	}

	// ------------------------------------------------------------------------------------------ //
	function GetCurrentShift() {
		$sql    = 'SELECT f_get_current_shift() as current_shift';
		$query  = $this->db_ma->query($sql);
		$result = $query->result();
		if (count($result) > 0) {
			return $result[0];
		}
		else {
			return null;
		}
	}

	// ------------------------------------------------------------------------------------------ //
	function GetDepartmentListForContact() {
		$this->db_ma->where(array('is_itsm_department' => IS_NOT_ITSM_DEPARTMENT, 'deleted' => '0'));
		$this->db_ma->order_by('name', "asc");
		$query = $this->db_ma->get($this->tblDeparment);
		$res = $query->result_array();
		if($query->num_rows() > 0) {
			return $res;
		} else {
			return null;
		}

	}

	// ------------------------------------------------------------------------------------------ //
	function GetProductListByDepartmentIdForContact($nDepartmentId) {
		$this->db_ma->where(array('department_id' => $nDepartmentId, 'is_itsm_product' => IS_NOT_ITSM_PRODUCT, 'deleted' => '0'));
		$query = $this->db_ma->get($this->tblProduct);
		$res = $query->result_array();
		if($query->num_rows() > 0) {
			return $res;
		} else {
			return null;
		}

	}
	// ------------------------------------------------------------------------------------------ //
	function GetUsersByDepartmentId($noDepartmentId) {
		$this->db_ma->select('u.*, d.name as sdk_dept ');
		$this->db_ma->from('user AS u ');
		$this->db_ma->join('department AS d', 'u.department_id = d.departmentid', 'left');
		$this->db_ma->where(array('u.department_id' => $noDepartmentId, 'd.deleted' => '0', 'd.is_itsm_department' => IS_NOT_ITSM_DEPARTMENT));
		$query = $this->db_ma->get();
		//$sql = "";
		$res = $query->result_array();
		if($query->num_rows() > 0) {
			return $res;
		} else {
			return null;
		}
	}

	// ------------------------------------------------------------------------------------------ //
	function getUserById($iUserId) {
		$this->db_ma->where('userid', intval($iUserId));
		$query = $this->db_ma->get($this->tblUser);
		$res = $query->row_array();
		return $res;
	}

	// ------------------------------------------------------------------------------------------ //
	function get_avaya_info_by_ip($strIpAddress) {
		$this->db_ma->where('ip_address', $strIpAddress);
		$query = $this->db_ma->get($this->tblAvaya);
		return $query->row();
	}

	// ------------------------------------------------------------------------------------------ //
	function LoadUserByUserName($strUserName) {
		if(!empty($strUserName)){
			$strSQL = "select user.* from user INNER JOIN department d ON(d.departmentid=user.department_id)
					where email like '". $this->db_ma->escape_like_str($strUserName) ."@%' AND d.name LIKE ? AND d.is_itsm_department=0 LIMIT 1";
			$oQuery = $this->db_ma->query($strSQL, SDK_DEPARTMENT_NAME);

			if ($oQuery->num_rows() > 0) {
				return $oQuery->row_array();
			}
		}

		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	function getDepartmentIdByDeptName($strDepartmentName) {
		$this->db_ma->where(array('name' => $strDepartmentName, 'deleted' => '0', 'is_itsm_department' => 0));
		$oQuery = $this->db_ma->get($this->tblDeparment);
		if($oQuery->num_rows() > 0) {
			return $oQuery->row();
		} else {
			return null;
		}
	}
	// ------------------------------------------------------------------------------------------ //
	function checkEmailExisted($strEmail) {
		$strSQL = "SELECT `userid` FROM `user` WHERE SUBSTRING_INDEX(`email`, '@', 1) LIKE '". $this->db_ma->escape_like_str($strEmail) . "'";
		$oQuery = $this->db_ma->query($strSQL);
		//pd($this->db_ma->last_query());
		return $oQuery->num_rows();
	}

	// ------------------------------------------------------------------------------------------ //
	function getStaffById($iUserId) {
		$this->db_ma->where('id', intval($iUserId));
		$query = $this->db_ma->get($this->tblVNGStaffList);
		$res = $query->row_array();
		return $res;
	}

}
?>
