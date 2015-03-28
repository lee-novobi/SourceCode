$('#pp_tblCIListTop, #pp_tblCIListBottom').pagination({
    total:iTotalRecords,
    pageSize:iPageSize,
    pageNumber:iCurrentPage,
    showRefresh: false,
	pageList: [10,15,20,50,100,200],
    onSelectPage: function(strPage, strPageSize){
    	OnServerListPageChange(strPage, strPageSize);
    	$('#pp_tblCIListTop, #pp_tblCIListBottom').pagination('refresh',{pageNumber: strPage});
    },
    onChangePageSize: function(strPageSize){
		OnServerListPageChange(1, strPageSize);
		$('#pp_tblCIListTop, #pp_tblCIListBottom').pagination('refresh',{pageSize: strPageSize});
    }
});

function FreezHeader(){
	$("#tbCIList").freezeHeader({offset: 49, container: "divCIListContainer"});
}

function ReDrawFreezHeader(){
	$("#tbCIList").freezeHeader({redraw: true, offset: 49, container: "divCIListContainer"});
}

$(document).ready(function(){
	FreezHeader();
	$( "#body #right-content" ).bind('right_content_resize', function() {
		ReDrawFreezHeader();
	});
});

function OnServerListPageChange(strPage, strPageSize){
	var strQueryString = $("#hidQueryString").val();

	var strURL = base_url + strURLData + '?' + 'p=' + strPage + '&ps=' + strPageSize;
	if(strQueryString != ""){
		strURL = strURL + '&' + strQueryString;
	}

	var arrData = {};
	$('.hidFilterOption').each(function(i, obj) {
		//test
		var strKey = $(obj).attr('key');
		var strVal = $(obj).val();
		arrData[strKey + "[]"] = jQuery.parseJSON(strVal);
	});

	$("#divCIListContainer").load(strURL, arrData, function(){
		FreezHeader();
	});
}