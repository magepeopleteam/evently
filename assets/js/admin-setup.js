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

	// Elementor + mage-eventpress installers: one handler wired to every
	// button that can trigger them — the Setup screen's Requirements card
	// (#evently-install-elementor / #evently-install-booking) and the
	// persistent admin notice's twins (#evently-notice-install-elementor /
	// #evently-notice-install-booking) shown on every other wp-admin screen
	// (see evently_required_plugins_notice() in inc/admin/setup-wizard.php).
	function wireInstallButton( id, action ) {
		var btn = document.getElementById( id );
		if ( ! btn ) {
			return;
		}
		btn.addEventListener( 'click', function () {
			btn.disabled = true;
			btn.textContent = eventlyAdmin.strings.importing;

			postAction( action )
				.then( function ( response ) {
					if ( response.success ) {
						window.location.reload();
					} else {
						btn.disabled = false;
						btn.textContent = eventlyAdmin.strings.error;
						window.alert( response.data && response.data.message ? response.data.message : eventlyAdmin.strings.error );
					}
				} )
				.catch( function () {
					btn.disabled = false;
					window.alert( eventlyAdmin.strings.error );
				} );
		} );
	}

	wireInstallButton( 'evently-install-elementor', 'evently_install_elementor' );
	wireInstallButton( 'evently-install-booking', 'evently_install_booking_plugin' );
	wireInstallButton( 'evently-notice-install-elementor', 'evently_install_elementor' );
	wireInstallButton( 'evently-notice-install-booking', 'evently_install_booking_plugin' );

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
