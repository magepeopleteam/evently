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

	/**
	 * Plugin Default Theme Style Two gallery → full-bleed hero + bottom-right arrows.
	 * Click image / View All opens .sliderPopup (zoom + slide).
	 */
	function clearInlineSliderHeights( stage ) {
		if ( ! stage ) {
			return;
		}
		stage.style.maxHeight = '';
		stage.style.minHeight = '';
		stage.style.height = '';
		Array.prototype.forEach.call( stage.querySelectorAll( '.sliderItem' ), function ( item ) {
			item.style.minHeight = '';
			item.style.maxHeight = '';
			item.style.height = '';
		} );
		Array.prototype.forEach.call( stage.querySelectorAll( '[data-bg-image]' ), function ( bg ) {
			bg.style.minHeight = '';
			bg.style.maxHeight = '';
			bg.style.height = '';
		} );
	}

	function currentSlideIndex( stage ) {
		var active = stage.querySelector( '.sliderItem.activeSlide' );
		var idx = active ? parseInt( active.getAttribute( 'data-slide-index' ), 10 ) : 1;
		return idx > 0 ? idx : 1;
	}

	function getHeroSlides( stage ) {
		var slides = [];
		Array.prototype.forEach.call( stage.children, function ( child ) {
			if ( child.classList && child.classList.contains( 'sliderItem' ) && child.getAttribute( 'data-slide-index' ) ) {
				slides.push( child );
			}
		} );
		return slides;
	}

	function setHeroSlide( stage, targetIndex ) {
		var slides = getHeroSlides( stage );
		var total = slides.length;
		if ( ! total ) {
			return 1;
		}

		var current = targetIndex;
		if ( current < 1 ) {
			current = total;
		}
		if ( current > total ) {
			current = 1;
		}

		Array.prototype.forEach.call( slides, function ( item ) {
			var n = parseInt( item.getAttribute( 'data-slide-index' ), 10 );
			item.classList.remove( 'activeSlide', 'prevSlider', 'nextSlider' );
			if ( n === current ) {
				item.classList.add( 'activeSlide' );
			} else if ( n < current ) {
				item.classList.add( 'prevSlider' );
			} else {
				item.classList.add( 'nextSlider' );
			}
		} );

		// Match plugin wrap behavior for seamless looping.
		if ( total > 1 ) {
			if ( current === 1 ) {
				var last = stage.querySelector( '.sliderItem[data-slide-index="' + total + '"]' );
				if ( last ) {
					last.classList.remove( 'nextSlider' );
					last.classList.add( 'prevSlider' );
				}
			}
			if ( current === total ) {
				var first = stage.querySelector( '.sliderItem[data-slide-index="1"]' );
				if ( first ) {
					first.classList.remove( 'prevSlider' );
					first.classList.add( 'nextSlider' );
				}
			}
		}

		return current;
	}

	function advanceHeroSlide( stage, delta ) {
		return setHeroSlide( stage, currentSlideIndex( stage ) + delta );
	}

	function openGalleryPopup( shell, slideIndex ) {
		var $ = window.jQuery;
		if ( ! $ || ! shell ) {
			return;
		}
		var idx = slideIndex > 0 ? slideIndex : 1;
		var proxy = document.createElement( 'button' );
		proxy.type = 'button';
		proxy.setAttribute( 'data-target-popup', 'superSlider' );
		proxy.setAttribute( 'data-slide-index', String( idx ) );
		proxy.setAttribute( 'aria-hidden', 'true' );
		proxy.tabIndex = -1;
		proxy.style.cssText = 'position:absolute;width:1px;height:1px;opacity:0;pointer-events:none;';
		shell.appendChild( proxy );
		$( proxy ).trigger( 'click' );
		if ( proxy.parentNode ) {
			proxy.parentNode.removeChild( proxy );
		}
	}

	function initPluginGallery( root ) {
		var areas = root.querySelectorAll( '.default_theme .mpwem_slider_area' );
		if ( ! areas.length ) {
			return;
		}

		Array.prototype.forEach.call( areas, function ( area ) {
			if ( area.getAttribute( 'data-evently-slider-ready' ) ) {
				return;
			}

			var shell = null;
			var children = area.children;
			var i;
			for ( i = 0; i < children.length; i++ ) {
				if ( children[ i ].classList && children[ i ].classList.contains( 'superSlider' ) && children[ i ].classList.contains( 'placeholder_area' ) ) {
					shell = children[ i ];
					break;
				}
			}
			if ( ! shell || ! shell.classList.contains( 'mpwem-slider--style-2' ) ) {
				return;
			}

			var stage = shell.querySelector( '.dFlex > .sliderAllItem' );
			if ( ! stage ) {
				return;
			}

			area.setAttribute( 'data-evently-slider-ready', '1' );
			area.classList.add( 'evently-slider-hero' );
			stage.classList.add( 'evently-slider-hero__stage' );
			clearInlineSliderHeights( stage );

			var slides = getHeroSlides( stage );
			var total = slides.length;

			// Click image → open lightbox with zoom/slide.
			Array.prototype.forEach.call( slides, function ( item ) {
				item.removeAttribute( 'data-target-popup' );
				item.style.cursor = 'zoom-in';
				item.setAttribute( 'role', 'button' );
				item.setAttribute( 'tabindex', '0' );
				item.setAttribute( 'aria-label', t( 'zoomGallery', 'View gallery' ) );
				item.addEventListener( 'click', function ( event ) {
					if ( event.target.closest && event.target.closest( '.iconIndicator, .evently-slider-rail, .mpwem-slider-style2__view-all' ) ) {
						return;
					}
					event.preventDefault();
					event.stopPropagation();
					openGalleryPopup( shell, currentSlideIndex( stage ) );
				} );
				item.addEventListener( 'keydown', function ( event ) {
					if ( event.key !== 'Enter' && event.key !== ' ' ) {
						return;
					}
					event.preventDefault();
					openGalleryPopup( shell, currentSlideIndex( stage ) );
				} );
			} );

			ensurePopupClose( shell );

			if ( total < 2 ) {
				window.setTimeout( function () {
					clearInlineSliderHeights( stage );
				}, 50 );
				return;
			}

			var rail = document.createElement( 'div' );
			rail.className = 'evently-slider-rail';
			rail.setAttribute( 'data-evently-slider-rail', '' );

			var prev = null;
			var next = null;
			Array.prototype.forEach.call( stage.children, function ( child ) {
				if ( ! child.classList || ! child.classList.contains( 'iconIndicator' ) ) {
					return;
				}
				if ( child.classList.contains( 'prevItem' ) ) {
					prev = child;
				}
				if ( child.classList.contains( 'nextItem' ) ) {
					next = child;
				}
			} );

			function bindArrow( el, delta ) {
				if ( ! el ) {
					return;
				}
				el.setAttribute( 'role', 'button' );
				el.setAttribute( 'tabindex', '0' );
				el.setAttribute( 'aria-label', delta < 0 ? t( 'prevImage', 'Previous image' ) : t( 'nextImage', 'Next image' ) );
				el.addEventListener( 'click', function ( event ) {
					event.preventDefault();
					event.stopPropagation();
					advanceHeroSlide( stage, delta );
				} );
				el.addEventListener( 'keydown', function ( event ) {
					if ( event.key !== 'Enter' && event.key !== ' ' ) {
						return;
					}
					event.preventDefault();
					event.stopPropagation();
					advanceHeroSlide( stage, delta );
				} );
				rail.appendChild( el );
			}

			bindArrow( prev, -1 );
			bindArrow( next, 1 );
			stage.appendChild( rail );

			// Prefer plugin-rendered count badge; only add one if missing.
			// Smart theme hides this badge (title/meta overlay occupies bottom-left).
			var isSmartTheme = !!( area.closest && area.closest( '.mep_smart_theme' ) );
			if ( ! isSmartTheme && ! shell.querySelector( '.mpwem-slider-style2__count' ) && ! stage.querySelector( '.evently-slider-count' ) ) {
				var count = document.createElement( 'div' );
				count.className = 'evently-slider-count';
				count.setAttribute( 'aria-hidden', 'true' );
				count.textContent = t( 'galleryCount', '%d photos' ).replace( '%d', String( total ) );
				stage.appendChild( count );
			} else if ( isSmartTheme ) {
				Array.prototype.forEach.call( shell.querySelectorAll( '.mpwem-slider-style2__count, .evently-slider-count' ), function ( badge ) {
					if ( badge && badge.parentNode ) {
						badge.parentNode.removeChild( badge );
					}
				} );
			}

			// Plugin resize may re-apply inline heights after images load.
			window.setTimeout( function () {
				clearInlineSliderHeights( stage );
			}, 50 );
			window.setTimeout( function () {
				clearInlineSliderHeights( stage );
			}, 400 );
			window.addEventListener( 'resize', function () {
				clearInlineSliderHeights( stage );
			} );
		} );
	}

	function closeGalleryPopup( popup ) {
		if ( ! popup ) {
			return;
		}
		popup.classList.remove( 'in' );
		document.body.classList.remove( 'noScroll' );
	}

	function ensurePopupClose( shell ) {
		var popup = shell.querySelector( '[data-popup="superSlider"]' );
		if ( ! popup || popup.getAttribute( 'data-evently-popup-close' ) ) {
			return;
		}
		popup.setAttribute( 'data-evently-popup-close', '1' );

		var header = popup.querySelector( '.popupHeader' );
		var closeBtn = popup.querySelector( '.popup_close' );
		if ( ! closeBtn && header ) {
			closeBtn = document.createElement( 'button' );
			closeBtn.type = 'button';
			closeBtn.className = 'popup_close fas fa-times';
			header.appendChild( closeBtn );
		}
		if ( closeBtn ) {
			closeBtn.setAttribute( 'role', 'button' );
			closeBtn.setAttribute( 'tabindex', '0' );
			closeBtn.setAttribute( 'aria-label', t( 'close', 'Close' ) );
			closeBtn.setAttribute( 'title', t( 'close', 'Close' ) );
			closeBtn.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				event.stopPropagation();
				closeGalleryPopup( popup );
			} );
			closeBtn.addEventListener( 'keydown', function ( event ) {
				if ( event.key !== 'Enter' && event.key !== ' ' ) {
					return;
				}
				event.preventDefault();
				closeGalleryPopup( popup );
			} );
		}

		popup.addEventListener( 'click', function ( event ) {
			if ( event.target === popup ) {
				closeGalleryPopup( popup );
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'Escape' && popup.classList.contains( 'in' ) ) {
				closeGalleryPopup( popup );
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

	/**
	 * Schedule "View More Dates" is handled by plugin mpwem_script.js
	 * (AJAX action mpwem_get_schedule_more_dates). Keep this as a no-op
	 * so we do not double-bind a collapse-only handler.
	 */
	function initScheduleMore() {
		// Intentionally empty — plugin AJAX handler owns the button.
	}

	function init() {
		initReadMore( document );
		initPluginGallery( document );
		initScheduleMore( document );
		document.querySelectorAll( '[data-evently-reviews]' ).forEach( enhanceReviews );
		document.querySelectorAll( '.default_theme .mpwem_calender_area, .mep-default-sidebar .mpwem_calender_area' ).forEach( enhanceCalendarArea );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
