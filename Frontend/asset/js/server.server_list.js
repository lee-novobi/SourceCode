$('#pp_tblServerListTop, #pp_tblServerListBottom').pagination({
    total:iTotalRecords,
    pageSize:iPageSize,
    pageNumber:iCurrentPage,
    showRefresh: false,
	pageList: [10,20,50,100,200],
    onSelectPage: function(strPage, strPageSize){
    	OnServerListPageChange(strPage, strPageSize);
    	$('#pp_tblServerListTop, #pp_tblServerListBottom').pagination('refresh',{pageNumber: strPage});
    },
    onChangePageSize: function(strPageSize){
		OnServerListPageChange(1, strPageSize);
		$('#pp_tblServerListTop, #pp_tblServerListBottom').pagination('refresh',{pageSize: strPageSize});
    }
});

$(document).ready(function(){

});

function OnServerListPageChange(strPage, strPageSize){
	var strQueryString = $("#hidQueryString").val();
	var strURL = base_url + 'server/index/ajax_server_list?' + 'p=' + strPage + '&ps=' + strPageSize;
	if(strQueryString != ""){
		strURL = strURL + '&' + strQueryString;
	}

	$("#divServerListContainer").load(strURL);
}

function PopUpServerDetail(strURL){
	var nHeight = getHeight()-100;
	CreateFancyBox(strURL, nHeight);
};