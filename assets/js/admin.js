/**
 * Admin settings page: color pickers + live card preview.
 * Loaded only on the Project Gallery settings page.
 */
( function ( $ ) {
	'use strict';

	$( document ).ready( function () {
		const preview = document.getElementById( 'lxpg-card-preview' );

		function setProp( prop, value ) {
			if ( ! preview || ! prop ) return;
			if ( value === '' || value == null ) {
				preview.style.removeProperty( prop );
			} else {
				preview.style.setProperty( prop, value );
			}
		}

		// Color pickers — pass a change callback so the preview updates as the
		// user drags the color selector, not just on close.
		$( '.lxpg-color-field' ).each( function () {
			const $input = $( this );
			const prop   = $input.attr( 'data-lxpg-prop' );

			$input.wpColorPicker( {
				change: function ( event, ui ) {
					setProp( prop, ui.color.toString() );
				}
			} );

			// "Clear" button removes the saved override; fall back to CSS default.
			$input.closest( '.wp-picker-container' ).on( 'click', '.wp-picker-clear', function () {
				setProp( prop, '' );
			} );
		} );

		// Select and plain text fields (e.g. aspect ratio, underline color).
		$( '[data-lxpg-prop]' ).not( '.lxpg-color-field' ).on( 'change input', function () {
			setProp( $( this ).attr( 'data-lxpg-prop' ), $( this ).val() );
		} );
	} );
} )( jQuery );
