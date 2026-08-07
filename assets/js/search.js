/**
 * Smart Event Search behavior: the "Use my location" button on the
 * homepage's "Events Near You" section. This performs a real browser
 * geolocation request and updates the visible label — it does not pretend
 * to re-run a distance-based query server-side, since the booking plugin
 * this theme integrates with has no such API (brief §44). A theme/plugin
 * update that adds real geo-filtering can hook into the
 * `evently:location-resolved` custom event dispatched below.
 *
 * @package Evently
 */
( function () {
	'use strict';

	var button = document.querySelector( '[data-evently-use-location]' );
	var label = document.querySelector( '[data-evently-location-label]' );

	if ( ! button || ! label ) {
		return;
	}

	var labelText = label.querySelector( 'span' ) || label;

	button.addEventListener( 'click', function () {
		if ( ! ( 'geolocation' in navigator ) ) {
			button.disabled = true;
			button.textContent = button.getAttribute( 'data-unsupported-text' ) || 'Location unavailable';
			return;
		}

		var originalText = button.textContent;
		button.disabled = true;
		button.textContent = '…';

		navigator.geolocation.getCurrentPosition(
			function ( position ) {
				button.disabled = false;
				button.hidden = true;
				labelText.textContent = 'Near your current location';

				document.dispatchEvent(
					new CustomEvent( 'evently:location-resolved', {
						detail: {
							latitude: position.coords.latitude,
							longitude: position.coords.longitude,
						},
					} )
				);
			},
			function () {
				button.disabled = false;
				button.textContent = originalText;
			},
			{ timeout: 8000 }
		);
	} );
} )();
