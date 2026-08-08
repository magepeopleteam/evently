/**
 * Single-event gallery:
 *  1. Owl Carousel for thumbnail strip (4-up, arrow nav when overflow).
 *  2. Click-to-zoom lightbox with prev/next (reuses data-evently-modal).
 *
 * @package Evently
 */
( function () {
	'use strict';

	/* ---- Owl thumbnail carousel -------------------------------------- */

	function initGalleryCarousel() {
		if ( typeof jQuery === 'undefined' || typeof jQuery.fn.owlCarousel !== 'function' ) {
			return;
		}

		jQuery( function ( $ ) {
			var $carousel = $( '[data-evently-gallery-carousel]' );
			if ( ! $carousel.length ) {
				return;
			}

			var count = $carousel.children().length;
			var prevLabel = $carousel.attr( 'data-nav-prev' ) || 'Previous';
			var nextLabel = $carousel.attr( 'data-nav-next' ) || 'Next';
			var $section = $carousel.closest( '[data-evently-gallery]' );
			var navEl = $section.find( '[data-evently-gallery-nav]' ).get( 0 );

			$carousel.owlCarousel( {
				items: 4,
				margin: 16,
				loop: false,
				rewind: false,
				dots: false,
				nav: count > 4,
				navContainer: navEl || false,
				mouseDrag: count > 4,
				touchDrag: count > 1,
				pullDrag: count > 4,
				navText: [
					'<span class="evently-gallery-carousel__arrow" aria-hidden="true"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></span>',
					'<span class="evently-gallery-carousel__arrow" aria-hidden="true"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg></span>',
				],
				responsive: {
					0: {
						items: 2,
						nav: count > 2,
						mouseDrag: count > 2,
					},
					640: {
						items: 3,
						nav: count > 3,
						mouseDrag: count > 3,
					},
					1000: {
						items: 4,
						nav: count > 4,
						mouseDrag: count > 4,
					},
				},
				onInitialized: function () {
					var $nav = navEl ? $( navEl ) : $carousel.find( '.owl-nav' );
					$nav.find( '.owl-prev' ).attr( 'aria-label', prevLabel );
					$nav.find( '.owl-next' ).attr( 'aria-label', nextLabel );
				},
			} );
		} );
	}

	initGalleryCarousel();

	/* ---- Zoom lightbox ----------------------------------------------- */

	var lightbox = document.querySelector( '[data-evently-lightbox]' );
	if ( ! lightbox ) {
		return;
	}

	var img = lightbox.querySelector( '[data-evently-lightbox-img]' );
	var prevBtn = lightbox.querySelector( '[data-evently-lightbox-prev]' );
	var nextBtn = lightbox.querySelector( '[data-evently-lightbox-next]' );
	var triggers = Array.prototype.slice.call(
		document.querySelectorAll( '[data-evently-lightbox-trigger]' )
	);

	if ( ! img || ! triggers.length ) {
		return;
	}

	var items = triggers.map( function ( trigger ) {
		return {
			src: trigger.getAttribute( 'data-evently-lightbox-src' ) || '',
			alt: trigger.getAttribute( 'data-evently-lightbox-alt' ) || '',
		};
	} ).filter( function ( item ) {
		return !! item.src;
	} );

	var current = 0;

	function show( index ) {
		if ( ! items.length ) {
			return;
		}
		current = ( index + items.length ) % items.length;
		img.src = items[ current ].src;
		img.alt = items[ current ].alt;

		var multi = items.length > 1;
		if ( prevBtn ) {
			prevBtn.hidden = ! multi;
		}
		if ( nextBtn ) {
			nextBtn.hidden = ! multi;
		}

		lightbox.removeAttribute( 'hidden' );
		document.body.classList.add( 'evently-modal-open' );
	}

	function close() {
		lightbox.setAttribute( 'hidden', '' );
		document.body.classList.remove( 'evently-modal-open' );
		img.removeAttribute( 'src' );
		img.alt = '';
	}

	document.addEventListener( 'click', function ( event ) {
		var trigger = event.target.closest( '[data-evently-lightbox-trigger]' );
		if ( trigger ) {
			event.preventDefault();
			var index = parseInt( trigger.getAttribute( 'data-evently-lightbox-index' ), 10 );
			show( isNaN( index ) ? 0 : index );
			return;
		}

		if ( event.target.closest( '[data-evently-lightbox-prev]' ) ) {
			event.preventDefault();
			event.stopPropagation();
			show( current - 1 );
			return;
		}

		if ( event.target.closest( '[data-evently-lightbox-next]' ) ) {
			event.preventDefault();
			event.stopPropagation();
			show( current + 1 );
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( lightbox.hasAttribute( 'hidden' ) ) {
			return;
		}
		if ( event.key === 'Escape' ) {
			event.preventDefault();
			close();
			return;
		}
		if ( event.key === 'ArrowLeft' ) {
			event.preventDefault();
			show( current - 1 );
		} else if ( event.key === 'ArrowRight' ) {
			event.preventDefault();
			show( current + 1 );
		}
	} );

	var observer = new MutationObserver( function () {
		if ( lightbox.hasAttribute( 'hidden' ) && img.getAttribute( 'src' ) ) {
			img.removeAttribute( 'src' );
			img.alt = '';
		}
	} );
	observer.observe( lightbox, { attributes: true, attributeFilter: [ 'hidden' ] } );

	lightbox.addEventListener( 'click', function ( event ) {
		if ( event.target === lightbox ) {
			close();
		}
	} );
} )();
