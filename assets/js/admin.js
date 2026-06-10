/**
 * Admin settings page: initialize WordPress color pickers.
 * Loaded only on the Project Gallery settings page.
 */
( function ( $ ) {
	'use strict';

	$( document ).ready( function () {
		$( '.lxpg-color-field' ).wpColorPicker();
	} );
} )( jQuery );
