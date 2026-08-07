/**
 * Theme Settings screen — live preview panels. Each `.evently-live-preview`
 * with a `data-section` renders the real homepage/footer template-part
 * server-side (evently_ajax_preview_section(), inc/admin/theme-settings.php)
 * with the fields currently on screen overlaid on top of the saved option —
 * nothing is ever saved by this. Debounced on input/change; also renders
 * once on page load so a fresh screen isn't just an empty frame.
 *
 * The Colors section gets a separate, instant, no-round-trip swatch since
 * it isn't a single homepage block — see the bottom of this file.
 *
 * @package Evently
 */
( function () {
	'use strict';

	if ( typeof eventlyAdmin === 'undefined' ) {
		return;
	}

	/**
	 * @param {Function} fn
	 * @param {number} wait
	 * @return {Function}
	 */
	function debounce( fn, wait ) {
		var timer;
		return function () {
			var args = arguments;
			clearTimeout( timer );
			timer = setTimeout( function () {
				fn.apply( null, args );
			}, wait );
		};
	}

	/**
	 * Read every `evently_settings[key]` control inside a fields table into
	 * a plain {key: value} object (checkboxes become '1' / '').
	 *
	 * @param {Element} table
	 * @return {Object}
	 */
	function collectValues( table ) {
		var values = {};
		table.querySelectorAll( '[name^="evently_settings["]' ).forEach( function ( el ) {
			var match = el.name.match( /evently_settings\[(.+?)\]/ );
			if ( ! match ) {
				return;
			}
			values[ match[ 1 ] ] = 'checkbox' === el.type ? ( el.checked ? '1' : '' ) : el.value;
		} );
		return values;
	}

	/**
	 * Grow/shrink the iframe to fit its rendered content instead of
	 * showing a fixed-height frame with dead space or a scrollbar.
	 *
	 * @param {HTMLIFrameElement} frame
	 * @return {void}
	 */
	function autosize( frame ) {
		try {
			var doc = frame.contentWindow && frame.contentWindow.document;
			if ( doc && doc.body ) {
				frame.style.height = Math.max( 120, Math.min( doc.body.scrollHeight + 8, 900 ) ) + 'px';
			}
		} catch ( e ) {
			// Not loaded yet — leave the current height alone.
		}
	}

	/**
	 * @param {Element} panel `.evently-live-preview[data-section]`
	 * @param {Element} table Its section's `table.form-table`.
	 * @return {void}
	 */
	function refresh( panel, table ) {
		var frame = panel.querySelector( '.evently-live-preview__frame' );
		if ( ! frame ) {
			return;
		}

		var body = new URLSearchParams();
		body.set( 'action', 'evently_preview_section' );
		body.set( 'nonce', eventlyAdmin.nonce );
		body.set( 'section', panel.getAttribute( 'data-section' ) );

		var values = collectValues( table );
		Object.keys( values ).forEach( function ( key ) {
			body.append( 'values[' + key + ']', values[ key ] );
		} );

		fetch( eventlyAdmin.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString(),
		} )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( response ) {
				if ( response.success ) {
					frame.srcdoc = response.data.html;
				}
			} )
			.catch( function () {
				// Best-effort preview — a failed refresh just leaves the last good render up.
			} );
	}

	document.querySelectorAll( '.evently-live-preview[data-section]' ).forEach( function ( panel ) {
		var card  = panel.closest( '.evently-setup-card' );
		var table = card ? card.querySelector( 'table.form-table' ) : null;
		var frame = panel.querySelector( '.evently-live-preview__frame' );

		if ( ! table || ! frame ) {
			return;
		}

		frame.addEventListener( 'load', function () {
			autosize( frame );
		} );

		var debouncedRefresh = debounce( function () {
			refresh( panel, table );
		}, 350 );

		table.addEventListener( 'input', debouncedRefresh );
		table.addEventListener( 'change', debouncedRefresh );

		refresh( panel, table );
	} );

	// Colors: a small illustrative swatch, updated instantly (no AJAX —
	// these are plain CSS custom properties, not a template render).
	var colorPreview = document.getElementById( 'evently-color-preview' );
	if ( colorPreview ) {
		var fieldToProperty = {
			color_primary: '--evently-preview-primary',
			color_orange: '--evently-preview-orange',
			color_dark: '--evently-preview-dark',
		};

		Object.keys( fieldToProperty ).forEach( function ( key ) {
			var input = document.querySelector( '[name="evently_settings[' + key + ']"]' );
			if ( ! input ) {
				return;
			}
			var apply = function () {
				colorPreview.style.setProperty( fieldToProperty[ key ], input.value );
			};
			input.addEventListener( 'input', apply );
			apply();
		} );
	}
} )();
