/**
 * Smart Event Search behavior:
 * 1. "Use my location" on Events Near You (geolocation + custom event).
 * 2. Modern custom select for the Category field (and any
 *    `.search-field select[data-evently-modern-select]`).
 *
 * @package Evently
 */
( function () {
	'use strict';

	/* ── Location button ─────────────────────────────────────────── */

	var button = document.querySelector( '[data-evently-use-location]' );
	var label = document.querySelector( '[data-evently-location-label]' );

	if ( button && label ) {
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
	}

	/* ── Modern select ───────────────────────────────────────────── */

	/**
	 * Enhance a native <select> into an accessible custom dropdown.
	 * Keeps the native select in the DOM (synced, form-submittable).
	 *
	 * @param {HTMLSelectElement} select
	 */
	function enhanceSelect( select ) {
		if ( ! select || select.dataset.eventlyEnhanced === '1' ) {
			return;
		}
		select.dataset.eventlyEnhanced = '1';

		var field = select.closest( '.search-field' );
		var wrap = document.createElement( 'div' );
		wrap.className = 'evently-modern-select';

		select.classList.add( 'evently-modern-select__native' );
		select.setAttribute( 'tabindex', '-1' );
		select.setAttribute( 'aria-hidden', 'true' );

		var trigger = document.createElement( 'button' );
		trigger.type = 'button';
		trigger.className = 'evently-modern-select__trigger';
		trigger.setAttribute( 'aria-haspopup', 'listbox' );
		trigger.setAttribute( 'aria-expanded', 'false' );
		if ( select.id ) {
			trigger.id = select.id + '-trigger';
			var linkedLabel = document.querySelector( 'label[for="' + select.id + '"]' );
			if ( linkedLabel ) {
				trigger.setAttribute( 'aria-labelledby', linkedLabel.id || '' );
				if ( ! linkedLabel.id ) {
					linkedLabel.id = select.id + '-label';
					trigger.setAttribute( 'aria-labelledby', linkedLabel.id );
				}
			}
		}

		var valueEl = document.createElement( 'span' );
		valueEl.className = 'evently-modern-select__value';

		var chevron = document.createElement( 'span' );
		chevron.className = 'evently-modern-select__chevron';
		chevron.setAttribute( 'aria-hidden', 'true' );

		trigger.appendChild( valueEl );
		trigger.appendChild( chevron );

		var menu = document.createElement( 'ul' );
		menu.className = 'evently-modern-select__menu';
		menu.setAttribute( 'role', 'listbox' );
		menu.hidden = true;
		if ( select.id ) {
			menu.id = select.id + '-menu';
			trigger.setAttribute( 'aria-controls', menu.id );
		}

		select.parentNode.insertBefore( wrap, select );
		wrap.appendChild( select );
		wrap.appendChild( trigger );
		wrap.appendChild( menu );

		function selectedOption() {
			return select.options[ select.selectedIndex ] || select.options[ 0 ];
		}

		function syncFromNative() {
			var opt = selectedOption();
			valueEl.textContent = opt ? opt.text : '';
			Array.prototype.forEach.call( menu.children, function ( li ) {
				var isSelected = li.getAttribute( 'data-value' ) === select.value;
				li.classList.toggle( 'is-selected', isSelected );
				li.setAttribute( 'aria-selected', isSelected ? 'true' : 'false' );
			} );
		}

		function buildOptions() {
			menu.innerHTML = '';
			Array.prototype.forEach.call( select.options, function ( opt, index ) {
				var li = document.createElement( 'li' );
				li.className = 'evently-modern-select__option';
				li.setAttribute( 'role', 'option' );
				li.setAttribute( 'data-value', opt.value );
				li.setAttribute( 'tabindex', '-1' );
				li.textContent = opt.text;
				if ( opt.disabled ) {
					li.setAttribute( 'aria-disabled', 'true' );
					li.classList.add( 'is-disabled' );
				}
				li.addEventListener( 'click', function ( event ) {
					event.preventDefault();
					if ( opt.disabled ) {
						return;
					}
					select.selectedIndex = index;
					select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
					syncFromNative();
					closeMenu();
					trigger.focus();
				} );
				menu.appendChild( li );
			} );
			syncFromNative();
		}

		function openMenu() {
			buildOptions();
			wrap.classList.add( 'is-open' );
			if ( field ) {
				field.classList.add( 'is-select-open' );
			}
			var bar = wrap.closest( '.search-bar' );
			if ( bar ) {
				bar.classList.add( 'has-select-open' );
			}
			trigger.setAttribute( 'aria-expanded', 'true' );
			menu.hidden = false;

			var selected = menu.querySelector( '.is-selected' );
			if ( selected ) {
				selected.focus();
			}
		}

		function closeMenu() {
			wrap.classList.remove( 'is-open' );
			if ( field ) {
				field.classList.remove( 'is-select-open' );
			}
			var bar = wrap.closest( '.search-bar' );
			if ( bar ) {
				bar.classList.remove( 'has-select-open' );
			}
			trigger.setAttribute( 'aria-expanded', 'false' );
			menu.hidden = true;
		}

		function toggleMenu() {
			if ( menu.hidden ) {
				openMenu();
			} else {
				closeMenu();
			}
		}

		trigger.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			toggleMenu();
		} );

		trigger.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'ArrowDown' || event.key === 'Enter' || event.key === ' ' ) {
				event.preventDefault();
				if ( menu.hidden ) {
					openMenu();
				}
			} else if ( event.key === 'Escape' ) {
				closeMenu();
			}
		} );

		menu.addEventListener( 'keydown', function ( event ) {
			var options = Array.prototype.slice.call(
				menu.querySelectorAll( '.evently-modern-select__option:not(.is-disabled)' )
			);
			var current = document.activeElement;
			var index = options.indexOf( current );

			if ( event.key === 'Escape' ) {
				event.preventDefault();
				closeMenu();
				trigger.focus();
				return;
			}

			if ( event.key === 'ArrowDown' ) {
				event.preventDefault();
				var next = options[ Math.min( index + 1, options.length - 1 ) ] || options[ 0 ];
				if ( next ) {
					next.focus();
				}
			} else if ( event.key === 'ArrowUp' ) {
				event.preventDefault();
				var prev = options[ Math.max( index - 1, 0 ) ] || options[ 0 ];
				if ( prev ) {
					prev.focus();
				}
			} else if ( event.key === 'Enter' || event.key === ' ' ) {
				event.preventDefault();
				if ( current && current.classList.contains( 'evently-modern-select__option' ) ) {
					current.click();
				}
			} else if ( event.key === 'Tab' ) {
				closeMenu();
			}
		} );

		document.addEventListener( 'click', function ( event ) {
			if ( ! wrap.contains( event.target ) ) {
				closeMenu();
			}
		} );

		select.addEventListener( 'change', syncFromNative );

		buildOptions();
	}

	document.querySelectorAll( '.search-field select, select[data-evently-modern-select]' ).forEach( enhanceSelect );

	/* ── Event autocomplete ──────────────────────────────────────── */

	var searchConfig = window.eventlySearch || {};
	var ajaxUrl = searchConfig.ajaxUrl || '';
	var nonce = searchConfig.nonce || '';
	var minChars = searchConfig.minChars || 2;
	var i18n = searchConfig.i18n || {};

	/**
	 * @param {HTMLInputElement} input
	 */
	function enhanceAutocomplete( input ) {
		if ( ! input || input.dataset.eventlyAutocomplete === '1' || ! ajaxUrl ) {
			return;
		}
		input.dataset.eventlyAutocomplete = '1';
		input.setAttribute( 'autocomplete', 'off' );
		input.setAttribute( 'role', 'combobox' );
		input.setAttribute( 'aria-autocomplete', 'list' );
		input.setAttribute( 'aria-expanded', 'false' );
		input.setAttribute( 'aria-haspopup', 'listbox' );

		var field = input.closest( '.search-field, .evently-modal__search-form' ) || input.parentNode;
		field.classList.add( 'evently-autocomplete' );

		var list = document.createElement( 'div' );
		list.className = 'evently-autocomplete__panel';
		list.setAttribute( 'role', 'listbox' );
		list.setAttribute( 'aria-label', i18n.suggestions || 'Event suggestions' );
		list.id = ( input.id || 'evently-search' ) + '-suggestions';
		list.hidden = true;
		input.setAttribute( 'aria-controls', list.id );

		field.appendChild( list );

		var debounceTimer = null;
		var abortController = null;
		var activeIndex = -1;
		var items = [];

		function closePanel() {
			list.hidden = true;
			list.innerHTML = '';
			items = [];
			activeIndex = -1;
			input.setAttribute( 'aria-expanded', 'false' );
			field.classList.remove( 'is-autocomplete-open' );
			var bar = field.closest( '.search-bar' );
			if ( bar ) {
				bar.classList.remove( 'has-select-open' );
			}
		}

		function openPanel() {
			list.hidden = false;
			input.setAttribute( 'aria-expanded', 'true' );
			field.classList.add( 'is-autocomplete-open' );
			var bar = field.closest( '.search-bar' );
			if ( bar ) {
				bar.classList.add( 'has-select-open' );
			}
		}

		function setActive( index ) {
			activeIndex = index;
			Array.prototype.forEach.call( list.querySelectorAll( '[data-evently-suggest-index]' ), function ( el ) {
				var isActive = Number( el.getAttribute( 'data-evently-suggest-index' ) ) === activeIndex;
				el.classList.toggle( 'is-active', isActive );
				if ( isActive ) {
					el.setAttribute( 'aria-selected', 'true' );
					input.setAttribute( 'aria-activedescendant', el.id );
				} else {
					el.setAttribute( 'aria-selected', 'false' );
				}
			} );
		}

		function goToSuggestion( suggestion ) {
			if ( suggestion && suggestion.url ) {
				window.location.href = suggestion.url;
			}
		}

		function renderSuggestions( suggestions, term ) {
			list.innerHTML = '';
			items = suggestions || [];
			activeIndex = -1;
			input.removeAttribute( 'aria-activedescendant' );

			if ( ! items.length ) {
				var empty = document.createElement( 'div' );
				empty.className = 'evently-autocomplete__empty';
				empty.textContent = i18n.noResults || 'No events found';
				list.appendChild( empty );
				openPanel();
				return;
			}

			items.forEach( function ( suggestion, index ) {
				var btn = document.createElement( 'button' );
				btn.type = 'button';
				btn.className = 'evently-autocomplete__item';
				btn.id = list.id + '-opt-' + index;
				btn.setAttribute( 'role', 'option' );
				btn.setAttribute( 'aria-selected', 'false' );
				btn.setAttribute( 'data-evently-suggest-index', String( index ) );

				var media = document.createElement( 'span' );
				media.className = 'evently-autocomplete__thumb';
				if ( suggestion.image ) {
					var img = document.createElement( 'img' );
					img.src = suggestion.image;
					img.alt = '';
					img.loading = 'lazy';
					media.appendChild( img );
				} else {
					media.classList.add( 'is-placeholder' );
				}

				var body = document.createElement( 'span' );
				body.className = 'evently-autocomplete__body';

				var title = document.createElement( 'span' );
				title.className = 'evently-autocomplete__title';
				title.textContent = suggestion.title || '';

				var meta = document.createElement( 'span' );
				meta.className = 'evently-autocomplete__meta';
				meta.textContent = [ suggestion.date, suggestion.location || suggestion.category ]
					.filter( Boolean )
					.join( ' · ' );

				body.appendChild( title );
				if ( meta.textContent ) {
					body.appendChild( meta );
				}

				var price = document.createElement( 'span' );
				price.className = 'evently-autocomplete__price';
				price.textContent = suggestion.price || '';

				btn.appendChild( media );
				btn.appendChild( body );
				if ( suggestion.price ) {
					btn.appendChild( price );
				}

				btn.addEventListener( 'mousedown', function ( event ) {
					// mousedown fires before blur; prevents the panel from closing first.
					event.preventDefault();
					goToSuggestion( suggestion );
				} );

				list.appendChild( btn );
			} );

			var footer = document.createElement( 'button' );
			footer.type = 'button';
			footer.className = 'evently-autocomplete__view-all';
			footer.textContent = ( i18n.viewAll || 'View all results' ) + ( term ? ' “' + term + '”' : '' );
			footer.addEventListener( 'mousedown', function ( event ) {
				event.preventDefault();
				var form = input.closest( 'form' );
				if ( form ) {
					form.requestSubmit ? form.requestSubmit() : form.submit();
				}
			} );
			list.appendChild( footer );

			openPanel();
		}

		function showLoading() {
			list.innerHTML = '';
			var loading = document.createElement( 'div' );
			loading.className = 'evently-autocomplete__loading';
			loading.textContent = i18n.loading || 'Searching…';
			list.appendChild( loading );
			openPanel();
		}

		function fetchSuggestions( term ) {
			if ( abortController && abortController.abort ) {
				abortController.abort();
			}
			abortController = typeof AbortController !== 'undefined' ? new AbortController() : null;

			var params = new URLSearchParams();
			params.set( 'action', 'evently_search_suggest' );
			params.set( 'nonce', nonce );
			params.set( 'term', term );
			params.set( 'limit', '8' );

			showLoading();

			fetch( ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
				},
				body: params.toString(),
				signal: abortController ? abortController.signal : undefined,
			} )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( payload ) {
					if ( ! payload || ! payload.success ) {
						renderSuggestions( [], term );
						return;
					}
					renderSuggestions( ( payload.data && payload.data.suggestions ) || [], term );
				} )
				.catch( function ( error ) {
					if ( error && error.name === 'AbortError' ) {
						return;
					}
					closePanel();
				} );
		}

		input.addEventListener( 'input', function () {
			var term = input.value.trim();
			window.clearTimeout( debounceTimer );

			if ( term.length < minChars ) {
				closePanel();
				return;
			}

			debounceTimer = window.setTimeout( function () {
				fetchSuggestions( term );
			}, 220 );
		} );

		input.addEventListener( 'keydown', function ( event ) {
			if ( list.hidden ) {
				return;
			}

			if ( event.key === 'ArrowDown' ) {
				event.preventDefault();
				setActive( Math.min( activeIndex + 1, items.length - 1 ) );
			} else if ( event.key === 'ArrowUp' ) {
				event.preventDefault();
				setActive( Math.max( activeIndex - 1, 0 ) );
			} else if ( event.key === 'Enter' && activeIndex >= 0 && items[ activeIndex ] ) {
				event.preventDefault();
				goToSuggestion( items[ activeIndex ] );
			} else if ( event.key === 'Escape' ) {
				event.preventDefault();
				closePanel();
			}
		} );

		input.addEventListener( 'blur', function () {
			window.setTimeout( closePanel, 150 );
		} );

		document.addEventListener( 'click', function ( event ) {
			if ( ! field.contains( event.target ) ) {
				closePanel();
			}
		} );
	}

	document
		.querySelectorAll( '#evently-search-what, #evently-quick-search, [data-evently-autocomplete]' )
		.forEach( enhanceAutocomplete );
} )();
