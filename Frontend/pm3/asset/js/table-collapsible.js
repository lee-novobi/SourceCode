jQuery.tableCollapsible = {
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