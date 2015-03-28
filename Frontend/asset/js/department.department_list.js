$('#pp_tblDepartmentListTop, #pp_tblDepartmentListBottom').pagination({
    total:iTotalRecords,
    pageSize:iPageSize,
    pageNumber:iCurrentPage,
    showRefresh: false,
	pageList: [10,20,50,100,200],
    onSelectPage: function(strPage, strPageSize){
    	OnDepartmentListPageChange(strPage, strPageSize);
    	$('#pp_tblDepartmentListTop, #pp_tblDepartmentListBottom').pagination('refresh',{pageNumber: strPage});
    },
    onChangePageSize: function(strPageSize){
		OnDepartmentListPageChange(1, strPageSize);
		$('#pp_tblDepartmentListTop, #pp_tblDepartmentListBottom').pagination('refresh',{pageSize: strPageSize});
    }
});

$(document).ready(function(){

});

function OnDepartmentListPageChange(strPage, strPageSize){
	var strQueryString = $("#hidQueryString").val();
	var strURL = base_url + 'department/index/ajax_department_list?' + 'p=' + strPage + '&ps=' + strPageSize;
	if(strQueryString != ""){
		strURL = strURL + '&' + strQueryString;
	}

	$("#divDepartmentListContainer").load(strURL);
}

function PopUpDepartmentDetail(strURL){
	var nHeight = getHeight()-100;
	CreateFancyBox(strURL, nHeight);
};