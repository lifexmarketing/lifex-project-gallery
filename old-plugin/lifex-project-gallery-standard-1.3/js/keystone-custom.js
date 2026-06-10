jQuery(document).ready(function( $ ) {

	$('.bsp-gallery').each(function() { 
	    $(this).find('a').magnificPopup({
	        type: 'image',
	        gallery: {
	          enabled:true
	        }
	    });
	});

	$( ".group.project__filters" ).on( "submit", function( event ) {
		var oldAction = window.location.href.replace(window.location.search,'');
		var params = $( this ).serialize();
		$(this).attr('action', oldAction + '?' + params);
	});
});