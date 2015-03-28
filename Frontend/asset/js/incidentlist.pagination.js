$('#pp_tblIncidentList').pagination({
	total:      iTotal1,
    pageSize:   iPageSize1,
    pageNumber: iPage1,
    showRefresh: false,
    onSelectPage: function(strPage, strPageSize){
    	if(paging_type == 'ajax'){
			var strURL = base_url + strURLIncidentCtrl + 'ajax_list_incident/' + strPage + '/' + strPageSize;
			var strQueryString = $("#hidQueryString").val();
			if(strQueryString != ""){
				strURL = strURL + '?' + strQueryString;
			}
			$("#tblIncidentList").load(strURL);
	    } else {
	    	$("#hidPage1").val(strPage);
	    	$("#hidPageSize1").val(strPageSize);
	    	ReloadPageIncidentList();
	    }
	}
});

$('#pp_tblIncidentWithoutSubarea').pagination({
	total:      iTotal2,
    pageSize:   iPageSize2,
    pageNumber: iPage2,
    showRefresh: false,
    onSelectPage: function(strPage, strPageSize){
    	if(paging_type == 'ajax'){
			var strURL = base_url + strURLIncidentCtrl + 'ajax_list_closed_incident_without_subarea/' + strPage + '/' + strPageSize;
			$("#tblIncidentWithoutSubareaList").load(strURL);
	    } else {
	    	$("#hidPage2").val(strPage);
	    	$("#hidPageSize2").val(strPageSize);
	    	ReloadPageIncidentList();
	    }
	}
});

$('#pp_tblIncidentClosedBySE').pagination({
	total:      iTotal3,
    pageSize:   iPageSize3,
    pageNumber: iPage3,
    showRefresh: false,
    onSelectPage: function(strPage, strPageSize){
    	if(paging_type == 'ajax'){
			var strURL = base_url + strURLIncidentCtrl + 'ajax_list_incident_just_closed_by_se/' + strPage + '/' + strPageSize;
			$("#tblIncidentClosedBySEList").load(strURL);
	    } else {
	    	$("#hidPage3").val(strPage);
	    	$("#hidPageSize3").val(strPageSize);
	    	ReloadPageIncidentList();
	    }
	}
});

function ReloadPageIncidentList(){
	var nPage1 = $("#hidPage1").val();
	var nPage2 = $("#hidPage2").val();
	var nPage3 = $("#hidPage3").val();

	var nPageSize1 = $("#hidPageSize1").val();
	var nPageSize2 = $("#hidPageSize2").val();
	var nPageSize3 = $("#hidPageSize3").val();

	var strURL = base_url + strURLIncidentCtrl + 'inc_list?page1=' + nPage1 + '&limit1=' + nPageSize1;
	if(nPage2>0) strURL = strURL + '&page2=' + nPage2 + '&limit2=' + nPageSize2;
	if(nPage3>0) strURL = strURL + '&page3=' + nPage3 + '&limit3=' + nPageSize3;

	var strQueryString = $("#hidQueryString").val();
	if(strQueryString != ""){
		strURL = strURL + '&' + strQueryString;
	}
	window.location = strURL;
}