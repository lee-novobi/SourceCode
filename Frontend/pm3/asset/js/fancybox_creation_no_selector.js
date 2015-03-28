function create_fancybox(url, width, height) {
    $.fancybox({
        'href': url,
        'scrolling'         : 'no',
        'titleShow'         : false,
        'titlePosition'     : 'none',
        'openEffect'        : 'elastic',
        'closeEffect'       : 'none',
        'closeClick'        : false,
        'openSpeed'         : 'fast',
        'type'              : 'iframe',
        'padding'           : 0,
        'preload'           : true,
        'width'             : width,
        'height'            : height,
        'fitToView'         : false,
        'autoSize'          : false, 
        'helpers'           : { 
            overlay :   {
                'closeClick': false,
            }
        }
    });    
}
