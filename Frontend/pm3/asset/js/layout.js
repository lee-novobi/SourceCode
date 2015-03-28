var isMenuShow = true;
var menuMinWidth = 30;
/* -------------------------------------------------------------------------
 * ACCORDIAN MENU
 * -------------------------------------------------------------------------
 * Author: DuyLH
 */
$(document).ready(function($){
	var isMenuShowCookie = $.cookie("isMenuShow");
	if(isMenuShowCookie != null){
		isMenuShow = (isMenuShowCookie == 'true');
	}
	$(function(){
        // $('#body').layout();
        //setHeight();
        //alert($('#body').width());
        //var nRighPanelWidth = $('#body').width() - $('#body #left-content').width() - 5;
        //$('#body #right-content').width(nRighPanelWidth);
    });
	InitSideMenu();
	$( window ).resize(function() {
		InitSideMenu();
		SetSideMenuVisibility();
	});
});

function InitSideMenu(){
	SetSideMenuVisibility();
    $('#body #left-content').css('top', $('#header').height());
    $('#body #left-content').css('min-height', getHeight()-55);
    $('#menu-tree').tree();
    $('#menu-tree').css('display', 'block');
}

function SetRightPanelWidth(isShowMenu){
	var nScrollWidth = 10;
	if(isShowMenu){
		var nRighPanelWidth = $('#body').width() - $('#body #left-content').width();
    	$('#body #right-content').width(nRighPanelWidth);
    } else {
    	var nRighPanelWidth = $('#body').width()-menuMinWidth-nScrollWidth;
    	$('#body #right-content').width(nRighPanelWidth);
    }
}

function ShowSideMenu(){
	// $('#body #left-content').css('display', 'block');
	$('#body #left-content').css('left', 0);
	SetRightPanelWidth(true);
    var nLeftPanelWidth = $('#body #left-content').width();
    $('#body #right-content').css("margin-left", nLeftPanelWidth);
    $('#btnShowHideMenu').css("background-image", "url('" + base_url + "asset/images/layout/arrow-31-512_close.png')");
}

function HideSideMenu(){
	// $('#body #left-content').css('display', 'none');
	$('#body #left-content').css('left', -$('#body #left-content').width()+menuMinWidth);
	SetRightPanelWidth(false);
    $('#body #right-content').css("margin-left", menuMinWidth);
    $('#btnShowHideMenu').css("background-image", "url('" + base_url + "asset/images/layout/arrow-31-512.png')");
}

function SetSideMenuVisibility(){
	if(isMenuShow){
		ShowSideMenu();
	} else {
		HideSideMenu();
	}
	$.cookie("isMenuShow", isMenuShow, {path: "/"});
	isMenuShow = !isMenuShow;
	$('#body #right-content').trigger("right_content_resize");
}