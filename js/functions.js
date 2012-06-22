// remap jQuery to $
(function($){})(window.jQuery);


$(document).ready(function (){


    $('img.bgimage').maxImage({
        isBackground: true,
        slideShow: true,
        position: ('absolute'),
        verticalAlign: 'bottom',
        horizontalAlign:'right',
        slideDelay: 8, 
        slideShowTitle: false,
        maxFollows: 'height'
    });

    // Color Box
    $('.loupe').colorbox({
      width: '700px',
      height: '100%'
    });

    $('#contact-form').colorbox({
    	iframe: true,
    	href: '/contact/pc.html',
    	width: '700px',
    	height: '500px'
    });

    // Contact Form Custum
    $("#m-form").jqTransform();

});


/* optional triggers

$(window).load(function() {
	
});

$(window).resize(function() {
	
});

*/