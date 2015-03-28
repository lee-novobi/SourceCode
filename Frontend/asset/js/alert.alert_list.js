function PopUpCreateIncident(alertid) {
	var nHeight = 700;
	var strURL = base_url + strDirIncident + "incident/create_incident_from_alert?alertid=" + alertid;
	nShowingPopup++;
	CreateFancyBoxModal(strURL, nHeight, function(){if(nShowingPopup>0) nShowingPopup--;});
}

function AjaxQuickACK(strType, strAlertId){
	if(strType == strACK_NO_INC){
		$.post(
			base_url + strDirAlert + 'alert/ajax_ack_no_inc',
			{'aid': strAlertId},
			function(response){
				alert('ACK NO-INC OK');
				location.reload();
			}
		);
	}
}

function PopUpACK(strAlertId){
	var nHeight = 700;
	var strURL = base_url + strDirAlert + "alert/ack_alert?alertid=" + strAlertId + "&page=1&limit=10";
	nShowingPopup++;
	CreateFancyBoxModal(strURL, nHeight, function(){if(nShowingPopup>0) nShowingPopup--;ReloadAlertListAfterACK()});
}

function PopContactPoint(zabbixServerID, zabbixHostID){
	var nHeight = 700;
	var strURL = base_url + strDirAlert + "alert/contact_point_alert?zbxServerID=" + zabbixServerID + "&zbxHostID=" + zabbixHostID;
	nShowingPopup++;
	CreateFancyBoxModal(strURL, nHeight, function(){if(nShowingPopup>0) nShowingPopup--;});
}

function ReloadAlertListAfterACK(){
	var bNeedReloadAfterACK = $("#hidNeedReloadAfterACK").val();
	if (bNeedReloadAfterACK==1){
		ReloadAlertList();
	}
}

function SetReloadAlertListAfterACK(){
	$("#hidNeedReloadAfterACK").val(1);
}

function ReloadAlertList(){
	location.reload();
}

function AutoRefresh(){
	setTimeout(function(){AutoRefresh()}, nInterval*1000);
	if(nShowingPopup == 0){
		ReloadAlertList();
	}
}

function OnNoACKPageChange(strPageNoACK, strPageSizeNoACK){
	var strPageACK     = $("#hidPageACK").val();
	var strPageSizeACK = $("#hidPageSizeACK").val();
	var strQueryString = $("#hidQueryString").val();

	var strURL = base_url + strDirAlert + 'alert/alert_list?' + 'page_no_acked=' + strPageNoACK + '&limit_no_acked=' + strPageSizeNoACK + '&page_acked=' + strPageACK + '&limit_acked=' + strPageSizeACK;
	if(strQueryString != ""){
		strURL = strURL + '&' + strQueryString;
	}

	window.location = strURL;
}

function OnACKPageChange(strPageACK, strPageSizeACK){
	var strPageNoACK     = $("#hidPageNoACK").val();
	var strPageSizeNoACK = $("#hidPageSizeNoACK").val();
	var strQueryString   = $("#hidQueryString").val();

	var strURL = base_url + strDirAlert + 'alert/alert_list?' + 'page_no_acked=' + strPageNoACK + '&limit_no_acked=' + strPageSizeNoACK + '&page_acked=' + strPageACK + '&limit_acked=' + strPageSizeACK;
	if(strQueryString != ""){
		strURL = strURL + '&' + strQueryString;
	}

	window.location = strURL;
}

$(document).ready(function(){
	$('#div-pagination-no-acked').pagination({
	    total: iTotalRecordsNoACK,
	    pageNumber: iCurrentPageNoACK,
	    pageSize: iPageSizeNoACK,
	    showPageList: true,
	    showRefresh: false,
		pageList: [10,20,50,100,200],
	    onSelectPage: function(strPageNoACK, strPageSizeNoACK){
	    	OnNoACKPageChange(strPageNoACK, strPageSizeNoACK);
	    },
	    onChangePageSize: function(strPageSizeNoACK){
			OnNoACKPageChange(1, strPageSizeNoACK);
	    }
	});

	$('#div-pagination-acked').pagination({
	    total: iTotalRecordsACK,
	    pageNumber: iCurrentPageACK,
	    pageSize: iPageSizeACK,
	    showPageList: true,
	    showRefresh: false,
		pageList: [10,20,50,100,200],
	    onSelectPage: function(strPageACK, strPageSizeACK){
	    	OnACKPageChange(strPageACK, strPageSizeACK);
	    },
	    onChangePageSize: function(strPageSizeACK){
			OnACKPageChange(1, strPageSizeACK);
	    }
	});
	setTimeout(function(){AutoRefresh()}, nInterval*1000);
})