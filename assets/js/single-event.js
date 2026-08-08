/**
 * Single-event polish: description Read more + Pro review UI enhancement.
 *
 * @package Evently
 */
( function () {
	'use strict';

	var i18n = window.eventlySingleI18n || {};

	function t( key, fallback ) {
		return i18n[ key ] || fallback;
	}

	function initReadMore( root ) {
		var wraps = root.querySelectorAll( '[data-evently-readmore]' );
		if ( ! wraps.length ) {
			return;
		}

		Array.prototype.forEach.call( wraps, function ( wrap ) {
			if ( wrap.getAttribute( 'data-evently-readmore-ready' ) ) {
				return;
			}
			wrap.setAttribute( 'data-evently-readmore-ready', '1' );

			var btn = wrap.querySelector( '[data-evently-readmore-toggle]' );
			if ( ! btn ) {
				return;
			}

			var more = btn.querySelector( '[data-label-more]' );
			var less = btn.querySelector( '[data-label-less]' );

			btn.addEventListener( 'click', function () {
				var expanded = wrap.classList.toggle( 'is-expanded' );
				wrap.classList.toggle( 'is-collapsed', ! expanded );
				btn.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
				if ( more ) {
					more.hidden = expanded;
				}
				if ( less ) {
					less.hidden = ! expanded;
				}
			} );
		} );
	}

	function starLabel( value ) {
		var n = parseInt( value, 10 ) || 0;
		if ( n === 1 ) {
			return t( 'starSingular', '1 star' );
		}
		return t( 'starPlural', '%d stars' ).replace( '%d', String( n ) );
	}

	function enhanceClassicRating( form ) {
		if ( form.querySelector( '.mep-review-stars' ) || form.querySelector( '.evently-review-stars' ) ) {
			return;
		}

		var radios = form.querySelectorAll( 'input[name="mep-event-review-form_rating"]' );
		if ( ! radios.length ) {
			return;
		}

		var first = radios[ 0 ];
		var group = first.closest( '.group' );
		if ( ! group ) {
			return;
		}

		var label = group.querySelector( '.label' );
		var err = group.querySelector( '.rating_err' );
		var stars = document.createElement( 'div' );
		stars.className = 'evently-review-stars';
		stars.setAttribute( 'role', 'radiogroup' );
		stars.setAttribute( 'aria-label', t( 'rateEvent', 'How would you rate this event?' ) );

		var items = Array.prototype.slice.call( radios );
		items.sort( function ( a, b ) {
			return parseInt( b.value, 10 ) - parseInt( a.value, 10 );
		} );

		items.forEach( function ( input ) {
			var oldLabel = input.closest( 'label' );
			var star = document.createElement( 'label' );
			star.className = 'evently-review-star';
			input.classList.add( 'evently-review-star__input' );
			star.appendChild( input );

			var icon = document.createElement( 'span' );
			icon.className = 'evently-review-star__icon';
			icon.setAttribute( 'aria-hidden', 'true' );
			icon.textContent = '★';
			star.appendChild( icon );

			var sr = document.createElement( 'span' );
			sr.className = 'screen-reader-text';
			sr.textContent = starLabel( input.value );
			star.appendChild( sr );

			stars.appendChild( star );
			if ( oldLabel && oldLabel.parentNode ) {
				oldLabel.parentNode.removeChild( oldLabel );
			}
		} );

		if ( label && label.nextSibling ) {
			group.insertBefore( stars, label.nextSibling );
		} else {
			group.appendChild( stars );
		}
		if ( err ) {
			group.appendChild( err );
		}

		group.classList.add( 'evently-review-rating-group' );
		if ( label ) {
			label.textContent = t( 'rateEvent', 'How would you rate this event?' );
		}
	}

	function enhanceReviewModal( modal ) {
		if ( ! modal || modal.getAttribute( 'data-evently-modal-ready' ) ) {
			return;
		}
		modal.setAttribute( 'data-evently-modal-ready', '1' );
		modal.classList.add( 'evently-review-modal' );

		var header = modal.querySelector( '.mage-modal-header' );
		if ( header && ! header.querySelector( '.evently-review-modal__eyebrow' ) ) {
			var eyebrow = document.createElement( 'span' );
			eyebrow.className = 'evently-review-modal__eyebrow';
			eyebrow.textContent = t( 'eventReview', 'Event review' );
			header.insertBefore( eyebrow, header.firstChild );
		}

		var heading = modal.querySelector( '.mage-modal-header h3' );
		if ( heading && /review form/i.test( heading.textContent || '' ) ) {
			heading.textContent = t( 'writeReview', 'Write a review' );
		}

		var formHeading = modal.querySelector( '.mep-event-review-heading' );
		if ( formHeading ) {
			formHeading.setAttribute( 'hidden', 'hidden' );
		}

		var form = modal.querySelector( '#mep_review_form' );
		if ( form ) {
			enhanceClassicRating( form );

			var submit = form.querySelector( 'input[type="submit"]' );
			if ( submit && ! submit.classList.contains( 'evently-review-submit' ) ) {
				submit.classList.add( 'evently-review-submit' );
				if ( /submit/i.test( submit.value || '' ) ) {
					submit.value = t( 'submitReview', 'Submit review' );
				}
			}
		}

		var close = modal.querySelector( '.close' );
		if ( close ) {
			close.setAttribute( 'aria-label', t( 'close', 'Close' ) );
			close.setAttribute( 'role', 'button' );
			close.setAttribute( 'tabindex', '0' );
		}
	}

	function enhanceReviews( section ) {
		if ( ! section || section.getAttribute( 'data-evently-reviews-ready' ) ) {
			return;
		}
		section.setAttribute( 'data-evently-reviews-ready', '1' );

		var actions = section.querySelector( '[data-evently-reviews-actions]' );
		var btn = section.querySelector( '#give-review-btn' );
		if ( actions && btn && btn.parentNode !== actions ) {
			actions.appendChild( btn );
			btn.classList.add( 'evently-review-write' );
		}

		var list = section.querySelector( '.mep-event-review-list' );
		var items = list ? list.querySelectorAll( '.mep-event-review-item' ) : [];
		var avg = list ? list.querySelector( '.mep-event-review-avg' ) : null;

		if ( list && ! items.length && ! section.querySelector( '.evently-reviews-empty' ) ) {
			var empty = document.createElement( 'div' );
			empty.className = 'evently-reviews-empty';
			empty.innerHTML =
				'<p class="evently-reviews-empty__title"></p>' +
				'<p class="evently-reviews-empty__text"></p>';
			empty.querySelector( '.evently-reviews-empty__title' ).textContent = t( 'noReviews', 'No reviews yet' );
			empty.querySelector( '.evently-reviews-empty__text' ).textContent = t( 'beFirst', 'Be the first to share your experience.' );
			if ( avg && avg.nextSibling ) {
				list.insertBefore( empty, avg.nextSibling );
			} else {
				list.appendChild( empty );
			}
		}

		var modal = section.querySelector( '#mageModal' ) || document.getElementById( 'mageModal' );
		enhanceReviewModal( modal );

		if ( btn && modal && ! btn.getAttribute( 'data-evently-review-bound' ) ) {
			btn.setAttribute( 'data-evently-review-bound', '1' );
			btn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				modal.style.display = 'block';
				modal.classList.add( 'is-open' );
				document.body.classList.add( 'evently-review-modal-open' );
			} );

			function closeModal() {
				modal.style.display = 'none';
				modal.classList.remove( 'is-open' );
				document.body.classList.remove( 'evently-review-modal-open' );
			}

			modal.querySelectorAll( '.close, [data-mage-modal-close]' ).forEach( function ( el ) {
				el.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					closeModal();
				} );
				el.addEventListener( 'keydown', function ( e ) {
					if ( e.key === 'Enter' || e.key === ' ' ) {
						e.preventDefault();
						closeModal();
					}
				} );
			} );

			modal.addEventListener( 'click', function ( e ) {
				if ( e.target === modal ) {
					closeModal();
				}
			} );

			document.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'Escape' && modal.classList.contains( 'is-open' ) ) {
					closeModal();
				}
			} );
		}
	}

	var calProviders = {
		google: { cls: 'evently-cal-google', icon: 'fab fa-google' },
		yahoo: { cls: 'evently-cal-yahoo', icon: 'fab fa-yahoo' },
		outlook: { cls: 'evently-cal-outlook', icon: 'fab fa-microsoft' },
		apple: { cls: 'evently-cal-apple', icon: 'fab fa-apple' },
	};

	function setCalendarOpen( area, btn, panel, open ) {
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
		var panel = area.querySelector( '[data-collapse]' );
		if ( ! btn ) {
			return;
		}

		if ( panel ) {
			panel.classList.add( 'evently-cal-panel' );
			Array.prototype.forEach.call( panel.querySelectorAll( 'a' ), function ( link ) {
				if ( link.getAttribute( 'data-evently-cal-link' ) ) {
					return;
				}
				link.setAttribute( 'data-evently-cal-link', '1' );
				var key = ( link.textContent || '' ).replace( /\s+/g, ' ' ).trim().toLowerCase();
				var meta = calProviders[ key ];
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

		if ( btn.getAttribute( 'data-evently-cal-btn' ) ) {
			return;
		}
		btn.setAttribute( 'data-evently-cal-btn', '1' );
		btn.classList.add( 'evently-cal-btn' );
		btn.setAttribute( 'type', 'button' );
		btn.setAttribute( 'aria-expanded', 'false' );

		var closeText = btn.getAttribute( 'data-close-text' ) || 'Add to Calendar';
		var openText = btn.getAttribute( 'data-open-text' ) || 'Hide Calendar';
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

		setCalendarOpen( area, btn, panel, false );

		btn.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			event.stopPropagation();
			var open = panel && panel.classList.contains( 'mActive' );
			setCalendarOpen( area, btn, panel, ! open );
		} );
	}

	function init() {
		initReadMore( document );
		document.querySelectorAll( '[data-evently-reviews]' ).forEach( enhanceReviews );
		document.querySelectorAll( '.default_theme .mpwem_calender_area, .mep-default-sidebar .mpwem_calender_area' ).forEach( enhanceCalendarArea );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
