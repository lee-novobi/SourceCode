$('#pp_tblIncidentList').pagination({
	total:iTotal1,
    pageSize:iPageSize1,
    pageNumber:iPage1,
    showRefresh: false,
	onSelectPage: function(strPage, strPageSize){
    	if(paging_type == 'ajax'){
			var strURL = base_url + strURLIncidentCtrl + 'ajax_list_incident_review/' + strPage + '/' + strPageSize;
			var strQueryString = $("#hidQueryString").val();
			if(strQueryString != ""){
				strURL = strURL + '?' + strQueryString;
			}
			$("#tblIncidentReviewList").load(strURL);
	    } else {
	    	$("#hidPage1").val(strPage);
	    	$("#hidPageSize1").val(strPageSize);
	    	ReloadPageReviewList();
	    }
	}
});

function ReloadPageReviewList(){
	var nPage1 = $("#hidPage1").val();
	// var nPage2 = $("#hidPage2").val();
	// var nPage3 = $("#hidPage3").val();

	var nPageSize1 = $("#hidPageSize1").val();
	// var nPageSize2 = $("#hidPageSize2").val();
	// var nPageSize3 = $("#hidPageSize3").val();

	var strURL = base_url + strURLIncidentCtrl + 'review?page1=' + nPage1 + '&limit1=' + nPageSize1;
	// if(nPage2>0) strURL = strURL + '&page2=' + nPage2 + '&limit2=' + nPageSize2;
	// if(nPage3>0) strURL = strURL + '&page3=' + nPage3 + '&limit3=' + nPageSize3;

	var strQueryString = $("#hidQueryString").val();
	if(strQueryString != ""){
		strURL = strURL + '&' + strQueryString;
	}
	window.location = strURL;
}