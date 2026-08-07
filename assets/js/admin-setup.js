/**
 * Evently Setup admin screen — real AJAX calls (import + WooCommerce
 * install), no simulated progress. Vanilla JS.
 *
 * @package Evently
 */
( function () {
	'use strict';

	if ( typeof eventlyAdmin === 'undefined' ) {
		return;
	}

	function postAction( action, extra ) {
		var body = new URLSearchParams( Object.assign( { action: action, nonce: eventlyAdmin.nonce }, extra || {} ) );
		return fetch( eventlyAdmin.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString(),
		} ).then( function ( response ) {
			return response.json();
		} );
	}

	function appendLog( list, text, isError ) {
		var item = document.createElement( 'li' );
		item.textContent = text;
		if ( isError ) {
			item.className = 'is-error';
		}
		list.appendChild( item );
	}

	var installBtn = document.getElementById( 'evently-install-woocommerce' );
	if ( installBtn ) {
		installBtn.addEventListener( 'click', function () {
			installBtn.disabled = true;
			installBtn.textContent = eventlyAdmin.strings.importing;

			postAction( 'evently_install_woocommerce' )
				.then( function ( response ) {
					if ( response.success ) {
						window.location.reload();
					} else {
						installBtn.disabled = false;
						installBtn.textContent = eventlyAdmin.strings.error;
						window.alert( response.data && response.data.message ? response.data.message : eventlyAdmin.strings.error );
					}
				} )
				.catch( function () {
					installBtn.disabled = false;
					window.alert( eventlyAdmin.strings.error );
				} );
		} );
	}

	var installElementorBtn = document.getElementById( 'evently-install-elementor' );
	if ( installElementorBtn ) {
		installElementorBtn.addEventListener( 'click', function () {
			installElementorBtn.disabled = true;
			installElementorBtn.textContent = eventlyAdmin.strings.importing;

			postAction( 'evently_install_elementor' )
				.then( function ( response ) {
					if ( response.success ) {
						window.location.reload();
					} else {
						installElementorBtn.disabled = false;
						installElementorBtn.textContent = eventlyAdmin.strings.error;
						window.alert( response.data && response.data.message ? response.data.message : eventlyAdmin.strings.error );
					}
				} )
				.catch( function () {
					installElementorBtn.disabled = false;
					window.alert( eventlyAdmin.strings.error );
				} );
		} );
	}

	document.querySelectorAll( '[data-evently-homepage-mode]' ).forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			var mode = button.getAttribute( 'data-evently-homepage-mode' );
			button.disabled = true;

			postAction( 'evently_setup_homepage_mode', { mode: mode } )
				.then( function ( response ) {
					button.disabled = false;

					if ( ! response.success ) {
						window.alert( response.data && response.data.message ? response.data.message : eventlyAdmin.strings.error );
						return;
					}

					if ( response.data.editUrl ) {
						window.location.href = response.data.editUrl;
					} else {
						window.location.reload();
					}
				} )
				.catch( function () {
					button.disabled = false;
					window.alert( eventlyAdmin.strings.error );
				} );
		} );
	} );

	var importBtn = document.getElementById( 'evently-run-import' );
	var progress  = document.getElementById( 'evently-import-progress' );
	var fill      = progress ? progress.querySelector( '.evently-setup-progress__fill' ) : null;
	var log       = document.getElementById( 'evently-import-log' );

	if ( importBtn && progress && log ) {
		importBtn.addEventListener( 'click', function () {
			importBtn.disabled = true;
			progress.hidden = false;
			log.innerHTML = '';
			if ( fill ) {
				fill.style.width = '15%';
			}
			appendLog( log, eventlyAdmin.strings.importing );

			postAction( 'evently_run_import' )
				.then( function ( response ) {
					if ( fill ) {
						fill.style.width = '100%';
					}
					if ( response.success ) {
						( response.data.log || [] ).forEach( function ( line ) {
							appendLog( log, line );
						} );
						appendLog( log, eventlyAdmin.strings.done );
					} else {
						appendLog( log, response.data && response.data.message ? response.data.message : eventlyAdmin.strings.error, true );
					}
					importBtn.disabled = false;
				} )
				.catch( function () {
					appendLog( log, eventlyAdmin.strings.error, true );
					importBtn.disabled = false;
				} );
		} );
	}
} )();
