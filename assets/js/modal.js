/**
 * Generic accessible modal controller — used by the header quick-search,
 * the mobile filter drawer (Event Archive), and ticket-info popovers.
 * Vanilla JS, event delegation, one implementation shared by every modal
 * on the site (brief §38).
 *
 * Markup contract:
 *   <button data-evently-modal-trigger="my-modal-id">Open</button>
 *   <div id="my-modal-id" data-evently-modal role="dialog" aria-modal="true" hidden>
 *     <button data-evently-modal-close>Close</button>
 *     ...
 *   </div>
 *
 * @package Evently
 */
( function () {
	'use strict';

	var openModal = null;
	var lastFocused = null;

	function getFocusable( modal ) {
		return Array.prototype.slice.call(
			modal.querySelectorAll( 'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])' )
		);
	}

	function open( modal, trigger ) {
		if ( ! modal ) {
			return;
		}
		lastFocused = trigger || document.activeElement;
		modal.removeAttribute( 'hidden' );
		document.body.classList.add( 'evently-modal-open' );
		openModal = modal;

		var focusable = getFocusable( modal );
		if ( focusable.length ) {
			focusable[ 0 ].focus();
		}
	}

	function close( modal ) {
		if ( ! modal ) {
			return;
		}
		modal.setAttribute( 'hidden', '' );
		document.body.classList.remove( 'evently-modal-open' );
		openModal = null;
		if ( lastFocused && typeof lastFocused.focus === 'function' ) {
			lastFocused.focus();
		}
	}

	document.addEventListener( 'click', function ( event ) {
		var trigger = event.target.closest( '[data-evently-modal-trigger]' );
		if ( trigger ) {
			var modal = document.getElementById( trigger.getAttribute( 'data-evently-modal-trigger' ) );
			open( modal, trigger );
			return;
		}

		var closer = event.target.closest( '[data-evently-modal-close]' );
		if ( closer ) {
			close( closer.closest( '[data-evently-modal]' ) );
			return;
		}

		// Click on the dimmed backdrop (the modal root itself, not its content) closes it.
		if ( event.target.hasAttribute && event.target.hasAttribute( 'data-evently-modal' ) ) {
			close( event.target );
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( ! openModal ) {
			return;
		}

		if ( event.key === 'Escape' ) {
			close( openModal );
			return;
		}

		if ( event.key === 'Tab' ) {
			var focusable = getFocusable( openModal );
			if ( ! focusable.length ) {
				return;
			}
			var first = focusable[ 0 ];
			var last = focusable[ focusable.length - 1 ];

			if ( event.shiftKey && document.activeElement === first ) {
				event.preventDefault();
				last.focus();
			} else if ( ! event.shiftKey && document.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		}
	} );
} )();
