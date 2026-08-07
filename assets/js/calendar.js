/**
 * Homepage Event Calendar — day selection (brief §11). Every day panel is
 * already server-rendered; this only toggles visibility + the selected
 * day's pressed state.
 *
 * @package Evently
 */
( function () {
	'use strict';

	var calendars = document.querySelectorAll( '[data-evently-calendar]' );

	calendars.forEach( function ( calendar ) {
		calendar.addEventListener( 'click', function ( event ) {
			var dayButton = event.target.closest( '[data-evently-day]' );
			if ( ! dayButton || ! calendar.contains( dayButton ) ) {
				return;
			}

			var day = dayButton.getAttribute( 'data-evently-day' );

			calendar.querySelectorAll( '[data-evently-day]' ).forEach( function ( otherDay ) {
				var isSelected = otherDay === dayButton;
				otherDay.classList.toggle( 'is-selected', isSelected );
				otherDay.setAttribute( 'aria-pressed', isSelected ? 'true' : 'false' );
			} );

			var panels = calendar.querySelectorAll( '[data-evently-day-panel]' );
			var matchedAny = false;
			panels.forEach( function ( panel ) {
				var isTarget = panel.getAttribute( 'data-evently-day-panel' ) === day;
				panel.hidden = ! isTarget;
				if ( isTarget ) {
					matchedAny = true;
				}
			} );

			var emptyState = calendar.querySelector( '[data-evently-day-empty]' );
			if ( emptyState ) {
				emptyState.hidden = matchedAny;
			}
		} );
	} );
} )();
