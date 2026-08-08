/**
 * Single-event booking card polish — modern "Add Calendar" control over
 * the plugin's real mpwem_add_calender markup.
 *
 * The plugin's collapse handler only binds `.mpwem_style [data-collapse-target]`,
 * and Evently's calendar lives outside that wrapper — so we own the toggle here.
 *
 * @package Evently
 */
( function () {
	'use strict';

	var providers = {
		google: { cls: 'evently-cal-google', icon: 'fab fa-google' },
		yahoo: { cls: 'evently-cal-yahoo', icon: 'fab fa-yahoo' },
		outlook: { cls: 'evently-cal-outlook', icon: 'fab fa-microsoft' },
		apple: { cls: 'evently-cal-apple', icon: 'fab fa-apple' },
	};

	function getPanel( area ) {
		return area.querySelector( '[data-collapse]' );
	}

	function isOpen( panel ) {
		return !!( panel && panel.classList.contains( 'mActive' ) );
	}

	function setOpen( area, btn, panel, open ) {
		if ( ! panel || ! btn ) {
			return;
		}

		panel.classList.toggle( 'mActive', open );
		btn.classList.toggle( 'is-open', open );
		btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );

		var closeText = btn.getAttribute( 'data-close-text' ) || 'Add to Calendar';
		var openText = btn.getAttribute( 'data-open-text' ) || 'Hide Calendar';
		var textEl = btn.querySelector( '[data-text]' );
		if ( textEl ) {
			textEl.textContent = open ? openText : closeText;
		}
	}

	function enhanceCalendarArea( area ) {
		if ( ! area || area.getAttribute( 'data-evently-cal' ) ) {
			return;
		}
		area.setAttribute( 'data-evently-cal', '1' );

		var btn = area.querySelector( ':scope > button, button[data-collapse-target]' );
		var panel = getPanel( area );

		if ( panel ) {
			panel.classList.add( 'evently-cal-panel' );
			var list = panel.querySelector( ':scope > div' ) || panel.querySelector( 'div' );
			if ( list ) {
				list.classList.add( 'evently-cal-providers' );
			}

			Array.prototype.forEach.call( panel.querySelectorAll( 'a' ), function ( link ) {
				if ( link.getAttribute( 'data-evently-cal-link' ) ) {
					return;
				}
				link.setAttribute( 'data-evently-cal-link', '1' );
				var key = ( link.textContent || '' ).replace( /\s+/g, ' ' ).trim().toLowerCase();
				var meta = providers[ key ];
				if ( ! meta ) {
					return;
				}
				link.classList.add( 'evently-cal-provider', meta.cls );
				if ( ! link.querySelector( '.evently-cal-provider__icon' ) ) {
					var wrap = document.createElement( 'span' );
					wrap.className = 'evently-cal-provider__icon';
					wrap.setAttribute( 'aria-hidden', 'true' );
					wrap.innerHTML = '<i class="' + meta.icon + '"></i>';
					link.insertBefore( wrap, link.firstChild );
				}
			} );
		}

		if ( btn && ! btn.getAttribute( 'data-evently-cal-btn' ) ) {
			btn.setAttribute( 'data-evently-cal-btn', '1' );
			btn.classList.add( 'evently-cal-btn' );
			btn.setAttribute( 'type', 'button' );
			btn.setAttribute( 'aria-expanded', 'false' );

			var closeText = btn.getAttribute( 'data-close-text' ) || 'Add to Calendar';
			var openText = btn.getAttribute( 'data-open-text' ) || 'Hide Calendar';
			// Prefer clearer copy than the plugin's default typo'd labels.
			if ( /add calendar/i.test( closeText ) ) {
				closeText = 'Add to Calendar';
			}
			if ( /hide calender/i.test( openText ) ) {
				openText = 'Hide Calendar';
			}
			btn.setAttribute( 'data-close-text', closeText );
			btn.setAttribute( 'data-open-text', openText );

			if ( ! btn.querySelector( '.evently-cal-btn__icon' ) ) {
				var icon = document.createElement( 'span' );
				icon.className = 'evently-cal-btn__icon';
				icon.setAttribute( 'aria-hidden', 'true' );
				icon.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>';
				btn.insertBefore( icon, btn.firstChild );
			}

			var textEl = btn.querySelector( '[data-text]' );
			if ( ! textEl ) {
				textEl = document.createElement( 'span' );
				textEl.setAttribute( 'data-text', '' );
				btn.appendChild( textEl );
			}
			textEl.textContent = closeText;

			if ( ! btn.querySelector( '.evently-cal-btn__chevron' ) ) {
				var chevron = document.createElement( 'span' );
				chevron.className = 'evently-cal-btn__chevron';
				chevron.setAttribute( 'aria-hidden', 'true' );
				chevron.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>';
				btn.appendChild( chevron );
			}

			// Start closed — plugin hide rules only apply inside .mpwem_style.
			setOpen( area, btn, panel, false );

			btn.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				event.stopPropagation();
				setOpen( area, btn, panel, ! isOpen( panel ) );
			} );
		}
	}

	function init() {
		document.querySelectorAll( '.evently-booking-card__calendar .mpwem_calender_area' ).forEach( enhanceCalendarArea );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
