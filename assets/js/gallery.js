/**
 * Gallery page: auto-submit filter form on select change.
 * No dependencies. Loaded deferred on shortcode pages only.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		const form = document.querySelector( '.lxpg-filters' );
		if ( ! form ) return;

		form.querySelectorAll( '.lxpg-filter-select' ).forEach( function ( select ) {
			select.addEventListener( 'change', function () {
				form.submit();
			} );
		} );
	} );
} )();
