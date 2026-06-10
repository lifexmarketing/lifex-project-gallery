/**
 * Vanilla JS lightbox and thumbnail switcher for single project pages.
 * No dependencies. Loaded deferred on single project pages only.
 */
( function () {
	'use strict';

	// ── Lightbox ───────────────────────────────────────────────────────────────

	class LXPGLightbox {
		constructor( items ) {
			this.items   = items;
			this.current = 0;
			this.isOpen  = false;
			this.el      = null;
			this._build();
			this._bindKeys();
		}

		_build() {
			const el = document.createElement( 'div' );
			el.className   = 'lxpg-lb';
			el.setAttribute( 'role', 'dialog' );
			el.setAttribute( 'aria-modal', 'true' );
			el.setAttribute( 'aria-label', 'Image lightbox' );
			el.setAttribute( 'aria-hidden', 'true' );

			el.innerHTML = `
				<div class="lxpg-lb-backdrop"></div>
				<div class="lxpg-lb-dialog">
					<button class="lxpg-lb-close" aria-label="Close lightbox" type="button">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
					</button>
					<button class="lxpg-lb-prev" aria-label="Previous image" type="button">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><polyline points="15 18 9 12 15 6"/></svg>
					</button>
					<div class="lxpg-lb-content">
						<img class="lxpg-lb-img" src="" alt="">
						<div class="lxpg-lb-caption"></div>
						<div class="lxpg-lb-counter"></div>
					</div>
					<button class="lxpg-lb-next" aria-label="Next image" type="button">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><polyline points="9 18 15 12 9 6"/></svg>
					</button>
				</div>
			`;

			el.querySelector( '.lxpg-lb-backdrop' ).addEventListener( 'click', () => this.close() );
			el.querySelector( '.lxpg-lb-close'    ).addEventListener( 'click', () => this.close() );
			el.querySelector( '.lxpg-lb-prev'     ).addEventListener( 'click', () => this.prev() );
			el.querySelector( '.lxpg-lb-next'     ).addEventListener( 'click', () => this.next() );

			document.body.appendChild( el );
			this.el = el;
		}

		_bindKeys() {
			document.addEventListener( 'keydown', ( e ) => {
				if ( ! this.isOpen ) return;
				if ( e.key === 'Escape'     ) { e.preventDefault(); this.close(); }
				if ( e.key === 'ArrowLeft'  ) { e.preventDefault(); this.prev(); }
				if ( e.key === 'ArrowRight' ) { e.preventDefault(); this.next(); }
			} );
		}

		open( index ) {
			this.current = index;
			this.isOpen  = true;
			this.el.setAttribute( 'aria-hidden', 'false' );
			this.el.classList.add( 'is-open' );
			document.body.classList.add( 'lxpg-lb-open' );
			this._update();
			this.el.querySelector( '.lxpg-lb-close' ).focus();
		}

		close() {
			this.isOpen = false;
			this.el.setAttribute( 'aria-hidden', 'true' );
			this.el.classList.remove( 'is-open' );
			document.body.classList.remove( 'lxpg-lb-open' );
		}

		prev() {
			this.current = ( this.current - 1 + this.items.length ) % this.items.length;
			this._update();
		}

		next() {
			this.current = ( this.current + 1 ) % this.items.length;
			this._update();
		}

		_update() {
			const item    = this.items[ this.current ];
			const img     = this.el.querySelector( '.lxpg-lb-img' );
			const caption = this.el.querySelector( '.lxpg-lb-caption' );
			const counter = this.el.querySelector( '.lxpg-lb-counter' );
			const prev    = this.el.querySelector( '.lxpg-lb-prev' );
			const next    = this.el.querySelector( '.lxpg-lb-next' );

			img.src = item.full;
			img.alt = item.alt || '';

			caption.textContent = item.alt || '';
			counter.textContent = ( this.current + 1 ) + ' / ' + this.items.length;

			const multi = this.items.length > 1;
			prev.style.display = multi ? '' : 'none';
			next.style.display = multi ? '' : 'none';
		}
	}

	// ── Thumbnail switcher ─────────────────────────────────────────────────────

	function initGallery() {
		const container = document.getElementById( 'lxpg-gallery' );
		if ( ! container ) return;

		const dataEl = document.getElementById( 'lxpg-gallery-data' );
		if ( ! dataEl ) return;

		let items;
		try {
			items = JSON.parse( dataEl.textContent );
		} catch ( e ) {
			return;
		}

		if ( ! Array.isArray( items ) || items.length === 0 ) return;

		const lightbox   = new LXPGLightbox( items );
		const mainLink   = container.querySelector( '.lxpg-gallery-main-link' );
		const mainImg    = container.querySelector( '.lxpg-gallery-main-img' );
		const thumbLinks = container.querySelectorAll( '.lxpg-gallery-thumb' );

		// Thumbnail click: update main image + open lightbox.
		thumbLinks.forEach( function ( link ) {
			link.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				const idx = parseInt( this.dataset.lxpgIndex, 10 );
				if ( isNaN( idx ) || ! items[ idx ] ) return;

				// Update main preview.
				if ( mainImg ) {
					mainImg.src = items[ idx ].thumb;
					mainImg.alt = items[ idx ].alt || '';
				}
				if ( mainLink ) {
					mainLink.dataset.lxpgIndex = idx;
				}

				// Update active state.
				thumbLinks.forEach( ( t ) => t.classList.remove( 'is-active' ) );
				this.classList.add( 'is-active' );
			} );
		} );

		// Main image click: open lightbox at current index.
		if ( mainLink ) {
			mainLink.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				const idx = parseInt( this.dataset.lxpgIndex, 10 ) || 0;
				lightbox.open( idx );
			} );
		}
	}

	document.addEventListener( 'DOMContentLoaded', initGallery );
} )();
