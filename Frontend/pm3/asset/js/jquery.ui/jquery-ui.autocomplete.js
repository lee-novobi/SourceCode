(function( $ ) {
	$.widget( "ui.combobox", {
		_create: function() {
			var input,
			that     = this,
			wasOpen  = false,
			select   = this.element.hide(),
			// selected = select.children( ":selected" ),
			selected = select.find( "option:selected" ),
			// value    = selected.val() ? selected.text() : "";
			value    = selected.text();
			var wrapper  = this.wrapper = $("<div>").addClass("autocomplete").css('width',select.css('width')).insertAfter(select);

			function removeIfInvalid( element ) {
				var value = $( element ).val(),
				matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( value ) + "$", "i" ),
				valid = false;

				select.children( "option" ).each(function() {
					if ( $( this ).text().match( matcher ) ) {
						this.selected = valid = true;
						select.trigger("change");
						return false;
					}
				});

				if ( !valid ) {
					// remove invalid value, as it didn't match anything
					$( element ).val( "" )
					select.val( "" );
					select.trigger("change");
					input.data( "ui-autocomplete" ).term = "";
				}
			}

			input = $("<input>")
				.appendTo(wrapper)
				.val( value )
				.attr( "title", "" ).css('width',select.css('width'))
				.attr("placeholder", this.options.placeholder?this.options.placeholder:"Choose ...")
				.autocomplete({
					delay: 0,
					minLength: 0,
					source: function( request, response ) {
						var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
						response( select.children( "option" ).map(function(){
							var text = $( this ).text();
							if ( this.value && ( !request.term || matcher.test(text) ) )
								return {
									label: text.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + $.ui.autocomplete.escapeRegex(request.term) + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>" ),
									value: text,
									option: this
								};
						}));
					},
					select: function( event, ui ) {
						ui.item.option.selected = true;
						that._trigger( "selected", event, {item: ui.item.option});
						select.trigger("change");
					},
					change: function( event, ui ) {
						if ( !ui.item ) {
							removeIfInvalid( this );
						}
					}
				})

			input.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li>" ).append("<a>" + item.label + "</a>").appendTo(ul);
			};
			// Button show all
			ctl = $("<div class=\"ctl-show-all\">")
				.attr( "tabIndex", -1 )
				.attr( "title", "Show All Items" )
				.tooltip()
				.appendTo(wrapper)
				.mousedown(function(){
					wasOpen = input.autocomplete( "widget" ).is( ":visible" );
				})
				.click(function() {
					input.focus();
					// close if already visible
					if ( wasOpen ) {
						return;
					}
					// pass empty string as value to search for, displaying all results
					input.autocomplete( "search", "" );
				});

			wrapper.css("padding-right", (parseInt(ctl.css("width")) + parseInt(input.css("padding-left")) + parseInt(input.css("padding-right")) + parseInt(ctl.css("padding-left")) + parseInt(ctl.css("padding-right")) + 3)+"px");

			input.tooltip({
				tooltipClass: "ui-state-highlight"
			});
		},
		_destroy: function() {
			this.wrapper.remove();
			this.element.show();
		}
	});
})( jQuery );