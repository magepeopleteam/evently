/**
 * Single-event booking card polish — calendar toggle + datepicker open.
 *
 * The plugin's collapse handler only binds `.mpwem_style [data-collapse-target]`,
 * and Evently's calendar lives outside that wrapper — so we own the toggle here.
 * Also ensures the readonly ticket date field opens jQuery UI datepicker on
 * label/icon click (focus alone is unreliable inside the sticky card).
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

		var btn = area.querySelector( ':scope > button.mpwem_calender_toggle, :scope > button, button[data-collapse-target]' );
		var panel = getPanel( area );
		var hasModernMarkup = !!area.querySelector( '.mpwem_calender_area__head, .mpwem_calender_toggle, .mpwem_calender_providers' );

		if ( panel ) {
			panel.classList.add( 'evently-cal-panel' );
			var list = panel.querySelector( '.mpwem_calender_providers__list' )
				|| panel.querySelector( ':scope > div' )
				|| panel.querySelector( 'div' );
			if ( list ) {
				list.classList.add( 'evently-cal-providers' );
			}

			Array.prototype.forEach.call( panel.querySelectorAll( 'a' ), function ( link ) {
				if ( link.getAttribute( 'data-evently-cal-link' ) ) {
					return;
				}
				link.setAttribute( 'data-evently-cal-link', '1' );
				link.classList.add( 'evently-cal-provider' );

				var key = '';
				var labelEl = link.querySelector( '.mpwem_calender_provider__label' );
				if ( labelEl ) {
					key = ( labelEl.textContent || '' ).replace( /\s+/g, ' ' ).trim().toLowerCase();
				} else {
					key = ( link.textContent || '' ).replace( /\s+/g, ' ' ).trim().toLowerCase();
				}
				var meta = providers[ key ];
				if ( ! meta ) {
					return;
				}
				link.classList.add( meta.cls );

				// Only inject Evently icon when plugin markup has none.
				if (
					! link.querySelector( '.mpwem_calender_provider__icon' ) &&
					! link.querySelector( '.evently-cal-provider__icon' )
				) {
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

			var closeText = btn.getAttribute( 'data-close-text' ) || 'Choose Calendar';
			var openText = btn.getAttribute( 'data-open-text' ) || 'Hide Calendar';
			if ( /add calendar/i.test( closeText ) ) {
				closeText = 'Choose Calendar';
			}
			if ( /hide calender/i.test( openText ) ) {
				openText = 'Hide Calendar';
			}
			btn.setAttribute( 'data-close-text', closeText );
			btn.setAttribute( 'data-open-text', openText );

			// Legacy markup only — new plugin toggle already has icon + chevron.
			if ( ! hasModernMarkup ) {
				if ( ! btn.querySelector( '.evently-cal-btn__icon' ) ) {
					var icon = document.createElement( 'span' );
					icon.className = 'evently-cal-btn__icon';
					icon.setAttribute( 'aria-hidden', 'true' );
					icon.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>';
					btn.insertBefore( icon, btn.firstChild );
				}

				if ( ! btn.querySelector( '.evently-cal-btn__chevron' ) ) {
					var chevron = document.createElement( 'span' );
					chevron.className = 'evently-cal-btn__chevron';
					chevron.setAttribute( 'aria-hidden', 'true' );
					chevron.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>';
					btn.appendChild( chevron );
				}
			}

			var textEl = btn.querySelector( '[data-text]' );
			if ( ! textEl ) {
				textEl = document.createElement( 'span' );
				textEl.setAttribute( 'data-text', '' );
				btn.appendChild( textEl );
			}
			textEl.textContent = closeText;

			// Start closed — plugin hide rules only apply inside .mpwem_style.
			setOpen( area, btn, panel, false );

			btn.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				event.stopPropagation();
				setOpen( area, btn, panel, ! isOpen( panel ) );
			} );
		}
	}

	/**
	 * Open the plugin's jQuery UI datepicker from the whole date label.
	 */
	function enhanceDatePicker( root ) {
		if ( typeof window.jQuery === 'undefined' ) {
			return;
		}

		var $ = window.jQuery;
		if ( ! $.fn || ! $.fn.datepicker ) {
			return;
		}

		var $inputs = $( root ).find( '.evently-booking-card input#mpwem_date_time[type="text"]' );
		if ( ! $inputs.length ) {
			$inputs = $( root ).find( '.mpwem_registration_area input#mpwem_date_time[type="text"]' );
		}

		$inputs.each( function () {
			var $input = $( this );
			if ( $input.data( 'eventlyDatepickerBound' ) ) {
				return;
			}
			$input.data( 'eventlyDatepickerBound', 1 );

			// Ensure instance exists even if plugin ready handler raced.
			if ( ! $input.hasClass( 'hasDatepicker' ) && typeof window.mpwemDateData !== 'undefined' ) {
				tryInitPluginDatepicker( $input );
			}

			var $label = $input.closest( 'label' );
			var openPicker = function ( event ) {
				if ( event ) {
					event.preventDefault();
					event.stopPropagation();
				}
				if ( ! $input.hasClass( 'hasDatepicker' ) ) {
					tryInitPluginDatepicker( $input );
				}
				if ( ! $input.hasClass( 'hasDatepicker' ) ) {
					return;
				}
				$input.trigger( 'focus' );
				try {
					$input.datepicker( 'show' );
				} catch ( err ) {
					// no-op
				}
			};

			$input.on( 'click.eventlyDatepicker', function ( event ) {
				openPicker( event );
			} );

			if ( $label.length ) {
				$label.on( 'click.eventlyDatepicker', function ( event ) {
					var $t = $( event.target );
					if ( $t.closest( '.mpwem-modern-select, select, button, a' ).length ) {
						return;
					}
					openPicker( event );
				} );
			}
		} );
	}

	function tryInitPluginDatepicker( $input ) {
		var $ = window.jQuery;
		var data = window.mpwemDateData;
		if ( ! data || ! $input || ! $input.length || ! $.fn.datepicker ) {
			return;
		}

		var format = ( typeof window.mpwem_date_format !== 'undefined' ) ? window.mpwem_date_format : 'D d M , yy';
		var availableDates = Array.isArray( data.availableDates ) ? data.availableDates : [];
		var minDateData = data.minDate || {};
		var maxDateData = data.maxDate || {};

		$input.datepicker( {
			dateFormat: format,
			minDate: new window.Date( minDateData.year, minDateData.month, minDateData.day ),
			maxDate: new window.Date( maxDateData.year, maxDateData.month, maxDateData.day ),
			autoSize: true,
			changeMonth: true,
			changeYear: true,
			showButtonPanel: false,
			beforeShow: function ( input, inst ) {
				if ( inst && inst.dpDiv ) {
					inst.dpDiv
						.addClass( 'mpwem-datepicker-modern' )
						.attr( 'data-mpwem-picker', '1' )
						.css( 'z-index', 100100 );
				}
			},
			onClose: function ( dateText, inst ) {
				if ( inst && inst.dpDiv ) {
					inst.dpDiv
						.removeClass( 'mpwem-datepicker-modern' )
						.removeAttr( 'data-mpwem-picker' );
				}
			},
			beforeShowDay: function ( date ) {
				var dmy = date.getDate() + '-' + ( date.getMonth() + 1 ) + '-' + date.getFullYear();
				if ( $.inArray( dmy, availableDates ) !== -1 ) {
					return [ true, 'mpwem-day-available', 'Available' ];
				}
				return [ false, 'mpwem-day-unavailable', 'Unavailable' ];
			},
			onSelect: function ( dateText, ui ) {
				var date = ui.selectedYear + '-' +
					( '0' + ( parseInt( ui.selectedMonth, 10 ) + 1 ) ).slice( -2 ) + '-' +
					( '0' + parseInt( ui.selectedDay, 10 ) ).slice( -2 );
				$( this )
					.closest( 'label' )
					.find( 'input[type="hidden"]' )
					.val( date )
					.trigger( 'change' );
			},
		} );
	}

	function init() {
		document.querySelectorAll( '.evently-booking-card__calendar .mpwem_calender_area' ).forEach( enhanceCalendarArea );
		enhanceDatePicker( document );
	}

	function boot() {
		init();
		// Plugin datepicker binds on document.ready; run once more after it.
		window.setTimeout( init, 0 );
		window.setTimeout( init, 250 );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}

	if ( typeof window.jQuery !== 'undefined' ) {
		window.jQuery( boot );
	}
} )();
