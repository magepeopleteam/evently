/**
 * "Choose Your Vibe" tab filter (homepage) + generic pill-filter groups
 * reused on the Event Archive. Proper ARIA tabs pattern — every panel
 * already exists in the DOM (server-rendered), this only toggles which one
 * is visible, so the feature degrades gracefully without JS (brief §38).
 *
 * Also handles the Event Archive's standalone "Sort by" <select> (outside
 * the main filter <form>, so plain GET submission doesn't apply to it) —
 * updates the current URL's query string and navigates. A real navigation,
 * not a simulated filter: the server re-runs the real query either way.
 *
 * @package Evently
 */
( function () {
	'use strict';

	document.querySelectorAll( '[data-evently-query-param]' ).forEach( function ( control ) {
		control.addEventListener( 'change', function () {
			var url = new URL( window.location.href );
			url.searchParams.set( control.getAttribute( 'data-evently-query-param' ), control.value );
			url.searchParams.delete( 'paged' );
			window.location.href = url.toString();
		} );
	} );

	// Organizer Dashboard tabs (page-templates/organizer-dashboard.php) — same
	// "everything already in the DOM, just toggle visibility" tabs pattern.
	var orgTabs = document.querySelectorAll( '[data-evently-org-tab]' );
	orgTabs.forEach( function ( tab ) {
		tab.addEventListener( 'click', function () {
			orgTabs.forEach( function ( otherTab ) {
				otherTab.classList.toggle( 'is-active', otherTab === tab );
			} );
			document.querySelectorAll( '[data-evently-org-panel]' ).forEach( function ( panel ) {
				panel.hidden = panel.getAttribute( 'data-evently-org-panel' ) !== tab.getAttribute( 'data-evently-org-tab' );
			} );
		} );
	} );

	var groups = document.querySelectorAll( '[data-evently-vibe-filter]' );

	groups.forEach( function ( group ) {
		group.addEventListener( 'click', function ( event ) {
			var tab = event.target.closest( '[role="tab"]' );
			if ( ! tab || ! group.contains( tab ) ) {
				return;
			}

			var tabs = group.querySelectorAll( '[role="tab"]' );
			tabs.forEach( function ( otherTab ) {
				var isActive = otherTab === tab;
				otherTab.classList.toggle( 'is-active', isActive );
				otherTab.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
			} );

			var panels = group.querySelectorAll( '[role="tabpanel"]' );
			panels.forEach( function ( panel ) {
				var isTarget = panel.id === tab.getAttribute( 'aria-controls' );
				panel.hidden = ! isTarget;
			} );
		} );
	} );
} )();
