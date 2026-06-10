jQuery(document).ready(function( $ ) {

	$('.bsp-gallery').each(function() { 
	    $(this).find('a').magnificPopup({
	        type: 'image',
	        gallery: {
	          enabled:true
	        }
	    });
	});
});