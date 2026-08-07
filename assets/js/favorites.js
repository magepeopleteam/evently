/**
 * Favorite/heart toggle on event cards. Persists to localStorage so it
 * survives a page reload — genuinely working, not a decorative no-op.
 * Server-side sync for logged-in users (My Account → Favorites, brief §21)
 * is a separate, explicit enhancement layered on top when that account
 * area ships; this file never pretends that sync already happened.
 *
 * @package Evently
 */
( function () {
	'use strict';

	var STORAGE_KEY = 'evently_favorites';

	function readFavorites() {
		try {
			var raw = window.localStorage.getItem( STORAGE_KEY );
			var parsed = raw ? JSON.parse( raw ) : [];
			return Array.isArray( parsed ) ? parsed : [];
		} catch ( error ) {
			return [];
		}
	}

	function writeFavorites( ids ) {
		try {
			window.localStorage.setItem( STORAGE_KEY, JSON.stringify( ids ) );
		} catch ( error ) {
			// Storage unavailable (private browsing, quota) — fail silently,
			// the toggle still works visually for the current page view.
		}
	}

	function applyStoredState() {
		var favorites = readFavorites();
		document.querySelectorAll( '[data-evently-favorite]' ).forEach( function ( button ) {
			var id = button.getAttribute( 'data-event-id' );
			if ( id && favorites.indexOf( id ) !== -1 ) {
				button.setAttribute( 'aria-pressed', 'true' );
			}
		} );
	}

	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '[data-evently-favorite]' );
		if ( ! button ) {
			return;
		}

		event.preventDefault();

		var id = button.getAttribute( 'data-event-id' );
		if ( ! id ) {
			return;
		}

		var favorites = readFavorites();
		var index = favorites.indexOf( id );
		var nowFavorited;

		if ( index === -1 ) {
			favorites.push( id );
			nowFavorited = true;
		} else {
			favorites.splice( index, 1 );
			nowFavorited = false;
		}

		writeFavorites( favorites );
		button.setAttribute( 'aria-pressed', nowFavorited ? 'true' : 'false' );
	} );

	applyStoredState();
} )();
