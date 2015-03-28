/*jQuery.tableCollapsible = {
	init: function(options){
		var oTable = this;
		$(this).find("tr[row-type='title']").attr("is-expanded", "1").click(function(){
			var isExpand = $(this).attr("is-expanded");
			if(isExpand === "1"){
				jQuery.tableCollapsible.collapse(oTable, this, options);
			} else {
				jQuery.tableCollapsible.expand(oTable, this, options);
			}
		})
	},
	expand: function(table, row, options)
	{
		var parent = $(row).parent();
		var groupByValue = parent.attr(options.groupByAttr);
		$(row).attr("is-expanded", "1");
		$(row).find("th").removeClass("collapsed").addClass("expanded");
		parent.find("tr[row-type='item']").show();
	},
	collapse: function(table, row, options){
		var parent = $(row).parent();
		var groupByValue = parent.attr(options.groupByAttr);
		$(row).attr("is-expanded", "0");
		$(row).find("th").removeClass("expanded").addClass("collapsed");
		parent.find("tr[row-type='item']").hide();
	}
};
jQuery.fn.extend({
    tableCollapsible: jQuery.tableCollapsible.init
});
*/
(function($)
{
	// SORT INTERNAL
	function internalSort(a, b) {
		var compA = $(a).text().toUpperCase();
		var compB = $(b).text().toUpperCase();
		return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
	};

	var SDKMultiSelect = {
		oSelect       : null,
		oAvailable    : null,
		oSelected     : null,
		oDivContainer : null,
		strCurrentGroup : "",
		options : {multi_group: true},

		init: function(options){
			$.extend(SDKMultiSelect.options, options);
			var oSelect       = SDKMultiSelect.oSelect    = this;
			var oSelAvailable = SDKMultiSelect.oAvailable = $("<select class=\"sdk-select-2side-sel-available\" multiple=\"multiple\">").attr("id", $(oSelect).attr("id")+"__available").css("width", $(oSelect).css("width"));
			var oSelSelected  = SDKMultiSelect.oSelected  = $("<select class=\"sdk-select-2side-sel-selected\" multiple=\"multiple\">").attr("id", $(oSelect).attr("id")+"__selected").css("width", $(oSelect).css("width"));
			var oDivContainer = SDKMultiSelect.oDivContainer = $("<div class=\"sdk-select-2side-container\">").attr("id", $(oSelect).attr("id")+"__container");
			var oDivAvail = $("<div class=\"sdk-select-2side-avail\">").append(oSelAvailable);
			var oDivSelected = $("<div class=\"sdk-select-2side-selected\">").append(oSelSelected);
			var oDivControl = $("<div class=\"sdk-select-2side-control\">");

			var oBtnAddOne = $("<p class=\"addOne\" title=\"Add Selected\">›</p>");
			var oBtnAddAll = $("<p class=\"addAll\" title=\"Add All\">»</p>");
			var oBtnRemoveOne = $("<p class=\"RemoveOne\" title=\"Remove Selected\">‹</p>");
			var oBtnRemoveAll = $("<p class=\"RemoveAll\" title=\"Remove All\">«</p>");
			oDivControl.append(oBtnAddOne).append(oBtnAddAll).append(oBtnRemoveOne).append(oBtnRemoveAll);

			// var	top = ((heightDiv/2) - ($(this).height()/2));

			oDivContainer.append(oDivAvail).append(oDivControl).append(oDivSelected);
			oSelect.after(oDivContainer).css("display", "none");
			oDivContainer.append(oSelect);

			var nSelHeight = oDivContainer.css("height");
			oSelAvailable.css("height", nSelHeight);
			oSelSelected.css("height", nSelHeight);

			oBtnAddOne.click(function(){
				var idMain = "#" + $(this).parent().parent().attr("id").replace("__container", "");
				$(idMain).SDKMultiSelect("AddSelect");
			});
			oBtnAddAll.click(function(){
				var idMain = "#" + $(this).parent().parent().attr("id").replace("__container", "");
				$(idMain).SDKMultiSelect("AddAll");
			});
			oBtnRemoveOne.click(function(){
				var idMain = "#" + $(this).parent().parent().attr("id").replace("__container", "");
				$(idMain).SDKMultiSelect("RemoveSelect");
			});
			oBtnRemoveAll.click(function(){
				var idMain = "#" + $(this).parent().parent().attr("id").replace("__container", "");
				$(idMain).SDKMultiSelect("RemoveAll");
			});
			oSelAvailable.dblclick(function(){
				oBtnAddOne.trigger("click");
			});
			oSelSelected.dblclick(function(){
				oBtnRemoveOne.trigger("click");
			});

			oSelSelected.append(oSelect.find("option[selected=selected]"));
			oSelAvailable.append(oSelect.find("option"));
			SDKMultiSelect.CloneSelectedToRealControl();

			this.data($(oSelect).attr("id")+".options", $.extend({}, SDKMultiSelect.options));
		},
		ReloadWrapControl: function(){
			var idMain   = this.attr("id");
			SDKMultiSelect.oSelect    = this;
			SDKMultiSelect.oAvailable = $("#" + idMain + "__available");
			SDKMultiSelect.oSelected  = $("#" + idMain + "__selected");
			$.extend(SDKMultiSelect.options, this.data(SDKMultiSelect.oSelect.attr("id")+".options"));
		},
		LoadAvailableList: function(arrOption){
			var oAvl = SDKMultiSelect.oAvailable;
			oAvl.empty();
			$.each(arrOption, function(index, obj){
				if(!SDKMultiSelect.isSelected(obj)){
					oAvl.append($("<option>").val(obj.value).text(obj.text).attr("group", obj.group));
				}
			});
			SDKMultiSelect.SortAvailable();
		},
		LoadSelectedList: function(arrOption){
			var oSelected = SDKMultiSelect.oSelected;
			oSelected.empty();
			$.each(arrOption, function(index, obj){
				if(!SDKMultiSelect.isSelected(obj)){
					oSelected.append($("<option>").val(obj.value).text(obj.text).attr("group", obj.group));
				}
			});
			SDKMultiSelect.SortAvailable();
			SDKMultiSelect.CloneSelectedToRealControl();
		},
		EmptySelected: function(arrOption){
			SDKMultiSelect.oSelected.empty();
		},
		isSelected: function(obj){
			var oSelected = SDKMultiSelect.oSelected;
			var bolResult = false;
			oSelected.children( "option" ).each(function(){
				if($(this).val()==obj.value){
					bolResult = true;
					return;
				}
			});
			return bolResult;
		},
		isAvailabled: function(obj){
			var oAvailable = SDKMultiSelect.oAvailable;
			var bolResult = false;
			oAvailable.children( "option" ).each(function(){
				if($(this).val()==obj.value){
					bolResult = true;
					return;
				}
			});
			return bolResult;
		},
		AddSelect: function(){
			SDKMultiSelect.oAvailable.find("option:selected").each(function(i, selected){
				// alert($(selected).text());
				if(!SDKMultiSelect.isSelected({value: $(selected).val()})){
					SDKMultiSelect.oSelected.append($(selected));
				}
			});
			SDKMultiSelect.SortSelected();
			SDKMultiSelect.CloneSelectedToRealControl();
		},
		AddAll: function(){
			SDKMultiSelect.oAvailable.find("option").each(function(i, selected){
				if(!SDKMultiSelect.isSelected({value: $(selected).val()})){
					SDKMultiSelect.oSelected.append($(selected));
				}
			});
			SDKMultiSelect.SortSelected();
			SDKMultiSelect.CloneSelectedToRealControl();
		},
		RemoveSelect: function(){
			var options = SDKMultiSelect.options;
			SDKMultiSelect.oSelected.find("option:selected").each(function(i, selected){
				var strGN = $(selected).attr("group");
				if(!options.multi_group || (options.multi_group &&strGN == SDKMultiSelect.strCurrentGroup)){
					if(!SDKMultiSelect.isAvailabled({value: $(selected).val()})){
						SDKMultiSelect.oAvailable.append($(selected));
						return;
					}
				}
				$(selected).remove();
			});
			SDKMultiSelect.SortAvailable();
			SDKMultiSelect.CloneSelectedToRealControl();
		},
		RemoveAll: function(){
			var options = SDKMultiSelect.options;
			SDKMultiSelect.oSelected.find("option").each(function(i, selected){
				var strGN = $(selected).attr("group");
				if(!options.multi_group || (options.multi_group &&strGN == SDKMultiSelect.strCurrentGroup)){
					if(!SDKMultiSelect.isAvailabled({value: $(selected).val()})){
						SDKMultiSelect.oAvailable.append($(selected));
						return;
					}
				}
				$(selected).remove();
			});
			SDKMultiSelect.SortAvailable();
			SDKMultiSelect.CloneSelectedToRealControl();
		},
		SortSelected: function(){
			var	arrOptions = SDKMultiSelect.oSelected.find("option");
			arrOptions.sort(internalSort);
			SDKMultiSelect.oSelected.empty().append(arrOptions);
		},
		SortAvailable: function(){
			var	arrOptions = SDKMultiSelect.oAvailable.find("option");
			arrOptions.sort(internalSort);
			SDKMultiSelect.oAvailable.empty().append(arrOptions);
		},
		SetCurrentGroup: function(strGN){
			SDKMultiSelect.strCurrentGroup = strGN;
		},
		CloneSelectedToRealControl: function(){
			SDKMultiSelect.oSelect.empty();
			var	arrOptions = SDKMultiSelect.oSelected.find("option").clone();
			SDKMultiSelect.oSelect.append(arrOptions.attr("selected", "selected"));
		},
		Serialize: function(){
			var arrResult = new Array();
			var	arrOptions = SDKMultiSelect.oSelect.find("option");
			$.each(arrOptions, function(idx, opt){
				arrResult.push({"id": $(opt).val(), "text": $(opt).val(), "group": $(opt).attr("group")});
			})

			return arrResult;
		},
		Empty: function(){
			SDKMultiSelect.oSelect.empty();
			SDKMultiSelect.oAvailable.empty();
			SDKMultiSelect.oSelected.empty();
		}
	}

	$.fn.SDKMultiSelect = function(method){
    	if(SDKMultiSelect[method]){
    		SDKMultiSelect["ReloadWrapControl"].apply(this);
    		return SDKMultiSelect[method].apply(this, Array.prototype.slice.call( arguments, 1 ))
    	} else if ( typeof method === 'object' || ! method ) {
			return SDKMultiSelect.init.apply( this, arguments );
		}
    }
})(jQuery);
