/**
 * Site header behavior: sticky/scrolled state + mobile nav drawer toggle.
 * Vanilla JS, event delegation, no dependencies (brief §38).
 *
 * @package Evently
 */
( function () {
	'use strict';

	var header = document.getElementById( 'evently-site-header' );

	if ( header ) {
		var SCROLL_THRESHOLD = 40;
		var onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > SCROLL_THRESHOLD );
		};
		onScroll();
		window.addEventListener( 'scroll', onScroll, { passive: true } );
	}

	var toggle = document.querySelector( '[data-evently-mobile-toggle]' );
	var nav = document.getElementById( 'evently-mobile-nav' );

	if ( toggle && nav ) {
		toggle.addEventListener( 'click', function () {
			var isOpen = toggle.getAttribute( 'aria-expanded' ) === 'true';
			toggle.setAttribute( 'aria-expanded', isOpen ? 'false' : 'true' );
			if ( isOpen ) {
				nav.setAttribute( 'hidden', '' );
			} else {
				nav.removeAttribute( 'hidden' );
			}
		} );

		// Close the mobile drawer on Escape or when a nav link is followed.
		document.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'Escape' && toggle.getAttribute( 'aria-expanded' ) === 'true' ) {
				toggle.setAttribute( 'aria-expanded', 'false' );
				nav.setAttribute( 'hidden', '' );
				toggle.focus();
			}
		} );

		nav.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( 'a' ) ) {
				toggle.setAttribute( 'aria-expanded', 'false' );
				nav.setAttribute( 'hidden', '' );
			}
		} );
	}
} )();
