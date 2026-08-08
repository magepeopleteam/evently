/**
 * Evently Setup + Theme Settings admin screens.
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

	/* Theme Settings: sync label/panel classes + remember last tab.
	   Primary switching is CSS (:has + radio labels) and works without JS. */
	function initSettingsTabs() {
		var form = document.getElementById( 'evently-settings-form' );
		if ( ! form ) {
			return;
		}

		var radios = form.querySelectorAll( '.evently-tab-radio' );
		var labels = form.querySelectorAll( '.evently-settings-nav__btn' );
		var panels = form.querySelectorAll( '[data-evently-panel]' );

		function syncTab( id ) {
			labels.forEach( function ( label ) {
				label.classList.toggle( 'is-active', label.getAttribute( 'data-evently-tab' ) === id );
			} );
			panels.forEach( function ( panel ) {
				panel.classList.toggle( 'is-active', panel.getAttribute( 'data-evently-panel' ) === id );
			} );
			try {
				window.sessionStorage.setItem( 'eventlySettingsTab', id );
			} catch ( e ) { /* ignore */ }
		}

		radios.forEach( function ( radio ) {
			radio.addEventListener( 'change', function () {
				if ( radio.checked ) {
					syncTab( radio.value );
				}
			} );
		} );

		var saved = null;
		try {
			saved = window.sessionStorage.getItem( 'eventlySettingsTab' );
		} catch ( e ) { /* ignore */ }

		if ( saved ) {
			var match = form.querySelector( '#evently-tab-' + saved );
			if ( match ) {
				match.checked = true;
				syncTab( saved );
			}
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initSettingsTabs );
	} else {
		initSettingsTabs();
	}

	/* Color pickers */
	if ( window.jQuery && jQuery.fn.wpColorPicker ) {
		jQuery( '.evently-color-field' ).wpColorPicker();
	}
} )();
