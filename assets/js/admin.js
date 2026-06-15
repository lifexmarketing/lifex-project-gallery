/**
 * Admin settings page: color pickers + live card/filter previews.
 * Loaded only on the Project Gallery settings page.
 */
( function ( $ ) {
	'use strict';

	$( document ).ready( function () {
		// All preview containers share the same CSS var pool, so updating any
		// setting propagates to every preview on the page at once.
		const previews = document.querySelectorAll( '[data-lxpg-preview]' );

		function setProp( prop, value ) {
			if ( ! prop ) return;
			previews.forEach( function ( preview ) {
				if ( value === '' || value == null ) {
					preview.style.removeProperty( prop );
				} else {
					preview.style.setProperty( prop, value );
				}
			} );
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

		// Collapse inline-CTA fields and preview when the enable checkbox is unchecked.
		// When enabled, also show only the sub-field matching the selected link type.
		const ctaCheckbox = document.getElementById( 'lxpg_content_cta_enabled' );
		if ( ctaCheckbox ) {
			const ctaRows = Array.from(
				ctaCheckbox.closest( 'table' ).querySelectorAll( 'tr' )
			).filter( function ( row ) {
				return ! row.contains( ctaCheckbox );
			} );
			const ctaPreview     = document.getElementById( 'lxpg-content-cta-preview' );
			const linkTypeSelect = document.getElementById( 'lxpg_content_cta_link_type' );
			const rowAcf         = document.getElementById( 'lxpg_content_cta_acf_field' )?.closest( 'tr' );
			const rowUrl         = document.getElementById( 'lxpg_content_cta_static_url' )?.closest( 'tr' );
			const rowPhone       = document.getElementById( 'lxpg_content_cta_phone' )?.closest( 'tr' );

			function applyLinkType() {
				if ( ! linkTypeSelect ) return;
				const type = linkTypeSelect.value;
				if ( rowAcf   ) rowAcf.style.display   = ( type === 'acf'   ) ? '' : 'none';
				if ( rowUrl   ) rowUrl.style.display   = ( type === 'url'   ) ? '' : 'none';
				if ( rowPhone ) rowPhone.style.display = ( type === 'phone' ) ? '' : 'none';
			}

			function toggleCtaFields() {
				const visible = ctaCheckbox.checked;
				ctaRows.forEach( function ( row ) {
					row.style.display = visible ? '' : 'none';
				} );
				if ( ctaPreview ) {
					ctaPreview.style.display = visible ? '' : 'none';
				}
				if ( visible ) applyLinkType();
			}

			toggleCtaFields();
			ctaCheckbox.addEventListener( 'change', toggleCtaFields );
			if ( linkTypeSelect ) {
				linkTypeSelect.addEventListener( 'change', applyLinkType );
			}
		}
	} );
} )( jQuery );
