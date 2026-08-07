/**
 * Two small visual-polish behaviors that don't warrant their own files:
 *  1. Count-up animation for [data-evently-count-up] stat numbers when they
 *     enter the viewport (brief §11 Stats section).
 *  2. Keyboard scrolling (arrow keys) for horizontal-scroll card rows on
 *     mobile (testimonials, categories) so the "carousel" is operable
 *     without a trackpad/touch (brief §34).
 *
 * @package Evently
 */
( function () {
	'use strict';

	/* ---- Count-up ---------------------------------------------------- */

	var countUpRoots = document.querySelectorAll( '[data-evently-count-up]' );

	function parseValue( text ) {
		var match = text.match( /([\d,.]+)/ );
		if ( ! match ) {
			return null;
		}
		return {
			number: parseFloat( match[ 1 ].replace( /,/g, '' ) ),
			prefix: text.slice( 0, match.index ),
			suffix: text.slice( match.index + match[ 1 ].length ),
		};
	}

	function animateValue( el, target, duration ) {
		var start = 0;
		var startTime = null;

		function step( timestamp ) {
			if ( startTime === null ) {
				startTime = timestamp;
			}
			var progress = Math.min( ( timestamp - startTime ) / duration, 1 );
			var current = Math.floor( start + ( target.number - start ) * progress );
			el.textContent = target.prefix + current.toLocaleString() + target.suffix;

			if ( progress < 1 ) {
				window.requestAnimationFrame( step );
			} else {
				el.textContent = target.prefix + target.number.toLocaleString() + target.suffix;
			}
		}

		window.requestAnimationFrame( step );
	}

	if ( countUpRoots.length && 'IntersectionObserver' in window && ! window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		var observer = new IntersectionObserver(
			function ( entries, obs ) {
				entries.forEach( function ( entry ) {
					if ( ! entry.isIntersecting ) {
						return;
					}
					entry.target.querySelectorAll( '.stat-val' ).forEach( function ( el ) {
						var target = parseValue( el.textContent.trim() );
						if ( target ) {
							animateValue( el, target, 900 );
						}
					} );
					obs.unobserve( entry.target );
				} );
			},
			{ threshold: 0.4 }
		);

		countUpRoots.forEach( function ( root ) {
			observer.observe( root );
		} );
	}

	/* ---- Keyboard scroll for horizontal card rows -------------------- */

	document.querySelectorAll( '.evently-scroll-row, .testi-grid' ).forEach( function ( row ) {
		row.setAttribute( 'tabindex', '0' );
		row.addEventListener( 'keydown', function ( event ) {
			var step = 260;
			if ( event.key === 'ArrowRight' ) {
				row.scrollBy( { left: step, behavior: 'smooth' } );
			} else if ( event.key === 'ArrowLeft' ) {
				row.scrollBy( { left: -step, behavior: 'smooth' } );
			}
		} );
	} );
} )();
