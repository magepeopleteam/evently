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
		var wrap = root.querySelector( '[data-evently-readmore]' );
		if ( ! wrap || wrap.getAttribute( 'data-evently-readmore-ready' ) ) {
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

	function init() {
		initReadMore( document );
		document.querySelectorAll( '[data-evently-reviews]' ).forEach( enhanceReviews );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
