$('#pp_tblDivisionListTop, #pp_tblDivisionListBottom').pagination({
    total:iTotalRecords,
    pageSize:iPageSize,
    pageNumber:iCurrentPage,
    showRefresh: false,
	pageList: [10,20,50,100,200],
    onSelectPage: function(strPage, strPageSize){
    	OnDivisionListPageChange(strPage, strPageSize);
    	$('#pp_tblDivisionListTop, #pp_tblDivisionListBottom').pagination('refresh',{pageNumber: strPage});
    },
    onChangePageSize: function(strPageSize){
		OnDivisionListPageChange(1, strPageSize);
		$('#pp_tblDivisionListTop, #pp_tblDivisionListBottom').pagination('refresh',{pageSize: strPageSize});
    }
});

$(document).ready(function(){

});

function OnDivisionListPageChange(strPage, strPageSize){
	var strQueryString = $("#hidQueryString").val();
	var strURL = base_url + 'division/index/ajax_division_list?' + 'p=' + strPage + '&ps=' + strPageSize;
	if(strQueryString != ""){
		strURL = strURL + '&' + strQueryString;
	}

	$("#divDivisionListContainer").load(strURL);
}

function PopUpDivisionDetail(strURL){
	var nHeight = getHeight()-100;
	CreateFancyBox(strURL, nHeight);
};