$('#pp_tblProductListTop, #pp_tblProductListBottom').pagination({
    total:iTotalRecords,
    pageSize:iPageSize,
    pageNumber:iCurrentPage,
    showRefresh: false,
	pageList: [10,20,50,100,200],
    onSelectPage: function(strPage, strPageSize){
    	OnProductListPageChange(strPage, strPageSize);
    	$('#pp_tblProductListTop, #pp_tblProductListBottom').pagination('refresh',{pageNumber: strPage});
    },
    onChangePageSize: function(strPageSize){
		OnProductListPageChange(1, strPageSize);
		$('#pp_tblProductListTop, #pp_tblProductListBottom').pagination('refresh',{pageSize: strPageSize});
    }
});

$(document).ready(function(){

});

function OnProductListPageChange(strPage, strPageSize){
	var strQueryString = $("#hidQueryString").val();
	var strURL = base_url + 'product/index/ajax_product_list?' + 'p=' + strPage + '&ps=' + strPageSize;
	if(strQueryString != ""){
		strURL = strURL + '&' + strQueryString;
	}

	$("#divProductListContainer").load(strURL);
}

function PopUpProductDetail(strURL){
	var nHeight = getHeight()-100;
	CreateFancyBox(strURL, nHeight);
};