
$(document).ready(function() {

$("a[href$='.jpg'],a[href$='.png'],a[href$='.gif']").fancybox({
		'padding'		:	'6',
		'margin'		:	'20',
		'opacity'		:	'true',
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'swing',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	true,
		'overlayOpacity':	'0.7',
		'titlePosition' :	'over',
		'overlayColor'	: 	'#555'
	});

$("a.gallery").fancybox({
		'padding'		:	'6',
		'margin'		:	'20',
		'opacity'		:	'true',
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'swing',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	true,
		'overlayOpacity':	'0.7',
		'titlePosition' :	'over',
		'overlayColor'	: 	'#555'
	});

$("#menu-contact a").fancybox({
        'padding'		:	'6',
		'margin'		:	'20',
		'opacity'		:	'true',
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'swing',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	true,
		'overlayOpacity':	'0.7',
		'titlePosition' :	'over',
		'overlayColor'	: 	'#555',
		'autoScale'		: 	true,
        'autoDimensions':	true,
		'height'		:	340,
		'width'			:	522,
        'type'			:	'iframe'
     });
	 


});


