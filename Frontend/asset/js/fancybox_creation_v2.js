function CreateFancyBox(selector, url, width, height, autosize) {
	if(!autosize)
		autosize = false;
	$(selector).fancybox({
		'href': url,
		'scrolling'			: 'no',
		'titleShow'			: false,
		'titlePosition'		: 'none',
		//'openEffect'		: 'elastic',
		'closeEffect'		: 'none',
		'closeClick'		: false,
		'openSpeed'			: 'fast',
		'type'              : 'iframe',
		'padding'     		: 0,
		'preload'     		: true,
		'width'             : width,
		'height'			: height,
		'fitToView'			: false,
		'autoSize'			: autosize,
		'openEffect'		: 'none',
        'beforeShow'        : function() {
          // added 50px to avoid scrollbars inside fancybox
			if(!height) {
				this.height = ($('.fancybox-iframe').contents().find('html').height())+30;
			}
        }, 
		'helpers'			: {	
			overlay : 	{
				'closeClick': false,
			}
		}
	});
}