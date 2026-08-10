/**
 * Attendee form drawer for Evently single-event booking card.
 *
 * Ports Horizon's per-ticket modal drawer behavior onto `.evently-booking-card`.
 * Form fields / qty cloning stay in mage-eventpress (+ Pro form builder);
 * this script only relocates `.mep_attendee_info` into a right-side drawer,
 * auto-opens on qty increase, and gates Book Now until each ticket is saved.
 *
 * @package Evently
 */
( function ( $ ) {
	'use strict';

	var ROOT = '.evently-booking-card';
	var I18N = typeof eventlyAttendeeI18n !== 'undefined' ? eventlyAttendeeI18n : {};

	function i18n( key, fallback ) {
		return I18N[ key ] ? I18N[ key ] : fallback;
	}

	function getRoots() {
		return $( ROOT );
	}

	function cssEscape( value ) {
		var str = String( value || '' );
		if ( window.CSS && typeof window.CSS.escape === 'function' ) {
			return window.CSS.escape( str );
		}
		return str.replace( /["\\]/g, '\\$&' );
	}

	function ensureRootId( $root ) {
		var id = $root.attr( 'data-evently-attendee-root' );
		if ( ! id ) {
			id = 'evently-attendee-' + Math.random().toString( 36 ).slice( 2, 10 );
			$root.attr( 'data-evently-attendee-root', id );
		}
		return id;
	}

	function hasAttendeeSupport( $area ) {
		if ( ! $area || ! $area.length ) {
			return false;
		}
		var $root = $area.closest( ROOT );
		var rootId = $root.length ? ensureRootId( $root ) : '';
		return $area.find( '.mep_attendee_info_hidden .mep_form_item' ).length > 0 ||
			$area.find( '.mep_attendee_info .mep_form_item' ).length > 0 ||
			$area.find( '.mpwem_booking_panel > .mep_attendee_info' ).length > 0 ||
			( rootId && $( '.evently-attendee-drawer[data-evently-root="' + cssEscape( rootId ) + '"] .mep_form_item' ).length > 0 );
	}

	function getSameAttendeeSetting( $area ) {
		var val = '';
		var $field = $();
		if ( $area && $area.length ) {
			$field = $area.find( '[name="mep_same_attendee"]' ).first();
		}
		if ( ! $field.length ) {
			$field = $( ROOT + ' [name="mep_same_attendee"]' ).first();
		}
		if ( $field.length ) {
			val = String( $field.val() || '' ).toLowerCase().trim();
		}
		if ( ! val && I18N.sameAttendee ) {
			val = String( I18N.sameAttendee ).toLowerCase().trim();
		}
		return val;
	}

	function isSameAttendeeMode( $area ) {
		var val = getSameAttendeeSetting( $area );
		return val === 'yes' || val === 'must';
	}

	function getTicketKeyFromItem( $item ) {
		if ( ! $item || ! $item.length ) {
			return '';
		}
		var key = $.trim( $item.find( '[name="option_name[]"]' ).first().val() || '' );
		if ( ! key ) {
			key = $.trim( $item.find( '[name="ticket_type[]"]' ).first().val() || '' );
		}
		if ( ! key ) {
			key = 'ticket-' + ( $item.index() + 1 );
		}
		return key;
	}

	function getTicketLabelFromItem( $item ) {
		if ( ! $item || ! $item.length ) {
			return i18n( 'attendeeDrawerTitle', 'Attendee details' );
		}
		var label = $.trim(
			$item.find( '.ticket-name' ).clone().children().remove().end().text()
		).replace( /\s+/g, ' ' );
		if ( ! label ) {
			label = getTicketKeyFromItem( $item );
		}
		return label;
	}

	function getItemQty( $item ) {
		if ( ! $item || ! $item.length ) {
			return 0;
		}
		return parseInt( $item.find( '[name="option_qty[]"]' ).first().val(), 10 ) || 0;
	}

	function findTicketItemByKey( $root, ticketKey ) {
		var $found = $();
		$root.find( '.mpwem_ticket_type .mep_ticket_item' ).each( function () {
			if ( getTicketKeyFromItem( $( this ) ) === ticketKey ) {
				$found = $( this );
				return false;
			}
		} );
		return $found;
	}

	function getSavedMap( $root ) {
		return $root.data( 'ev-attendee-saved' ) || {};
	}

	function setTicketSaved( $root, ticketKey, saved ) {
		var map = getSavedMap( $root );
		map[ ticketKey ] = !! saved;
		$root.data( 'ev-attendee-saved', map );
	}

	function getDrawerForTicket( $root, ticketKey ) {
		var rootId = ensureRootId( $root );
		var base = '.evently-attendee-drawer[data-evently-root="' + cssEscape( rootId ) + '"]';
		if ( ! ticketKey ) {
			return $( base ).first();
		}
		return $( base + '[data-ev-drawer-ticket="' + cssEscape( ticketKey ) + '"]' );
	}

	function getAttendeeScope( $root, ticketKey ) {
		if ( ! ticketKey ) {
			return getDrawerForTicket( $root, '' ).find( '.evently-attendee-drawer__body' );
		}
		var $drawer = getDrawerForTicket( $root, ticketKey );
		if ( $drawer.length ) {
			return $drawer.find( '.evently-attendee-drawer__body' ).children( '.mep_attendee_info' );
		}
		return $root.find( '.mep_attendee_info[data-ev-ticket-key="' + cssEscape( ticketKey ) + '"]' );
	}

	function getAttendeeFields( $root, ticketKey ) {
		return getAttendeeScope( $root, ticketKey ).find( 'input, select, textarea' ).filter( function () {
			var $el = $( this );
			if ( $el.prop( 'disabled' ) || $el.attr( 'type' ) === 'hidden' ) {
				return false;
			}
			if ( $el.closest( '.dNone, .mep_attendee_info_hidden' ).length ) {
				return false;
			}
			return true;
		} );
	}

	function getRequiredAttendeeFields( $root, ticketKey ) {
		return getAttendeeFields( $root, ticketKey ).filter( function () {
			var $el = $( this );
			return !! $el.prop( 'required' ) || $el.hasClass( 'mep-originally-required' );
		} );
	}

	function areAttendeeFieldsComplete( $root, ticketKey ) {
		var $fields = getRequiredAttendeeFields( $root, ticketKey );
		if ( ! $fields.length ) {
			return true;
		}
		var complete = true;
		$fields.each( function () {
			var $el = $( this );
			var type = ( $el.attr( 'type' ) || '' ).toLowerCase();
			if ( type === 'checkbox' || type === 'radio' ) {
				var name = $el.attr( 'name' );
				var $group = $el.closest( '.mp_form_item, .mep_checkbox_item, .groupCheckBox' );
				if ( $group.length ) {
					if ( ! $group.find( 'input[name="' + name + '"]:checked' ).length && ! $el.is( ':checked' ) ) {
						complete = false;
						return false;
					}
				} else if ( ! $el.is( ':checked' ) ) {
					complete = false;
					return false;
				}
				return;
			}
			if ( $.trim( String( $el.val() || '' ) ) === '' ) {
				complete = false;
				return false;
			}
		} );
		return complete;
	}

	function markInvalidAttendeeFields( $root, ticketKey ) {
		var $first = null;
		getAttendeeFields( $root, ticketKey ).removeClass( 'is-ev-invalid' );
		getRequiredAttendeeFields( $root, ticketKey ).each( function () {
			var $el = $( this );
			var type = ( $el.attr( 'type' ) || '' ).toLowerCase();
			var invalid = false;
			if ( type === 'checkbox' || type === 'radio' ) {
				var name = $el.attr( 'name' );
				var $group = $el.closest( '.mp_form_item, .mep_checkbox_item, .groupCheckBox' );
				invalid = $group.length
					? ! $group.find( 'input[name="' + name + '"]:checked' ).length && ! $el.is( ':checked' )
					: ! $el.is( ':checked' );
			} else {
				invalid = $.trim( String( $el.val() || '' ) ) === '';
			}
			if ( invalid ) {
				$el.addClass( 'is-ev-invalid' );
				if ( ! $first ) {
					$first = $el;
				}
			}
		} );
		return $first;
	}

	function getRegistrationFormId( $area ) {
		var $form = $area.closest( 'form' );
		var id = $form.attr( 'id' );
		if ( ! id ) {
			id = 'mpwem_registration';
			$form.attr( 'id', id );
		}
		return id;
	}

	function bindDrawerFieldsToForm( $drawerBody, formId ) {
		if ( ! $drawerBody.length || ! formId ) {
			return;
		}
		$drawerBody.find( 'input, select, textarea' ).each( function () {
			$( this ).attr( 'form', formId );
		} );
	}

	function unbindDrawerFieldsFromForm( $scope ) {
		$scope.find( 'input[form], select[form], textarea[form]' ).removeAttr( 'form' );
	}

	function drawerTitleId( ticketKey ) {
		var safe = String( ticketKey || 'same' ).replace( /[^a-zA-Z0-9_-]+/g, '-' ).replace( /^-+|-+$/g, '' ) || 'ticket';
		return 'evently_attendee_drawer_title_' + safe;
	}

	function ensureAttendeeDrawer( $root, $area, ticketKey ) {
		ticketKey = ticketKey || '__same__';
		var rootId = ensureRootId( $root );
		var $drawer = getDrawerForTicket( $root, ticketKey );
		if ( $drawer.length ) {
			return $drawer;
		}

		var titleId = drawerTitleId( ticketKey );
		$drawer = $(
			'<div class="evently-attendee-drawer" hidden>' +
				'<button type="button" class="evently-attendee-drawer__backdrop" data-ev-attendee-close aria-label="' + i18n( 'close', 'Close' ) + '"></button>' +
				'<div class="evently-attendee-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="' + titleId + '">' +
					'<div class="evently-attendee-drawer__head">' +
						'<div>' +
							'<span class="evently-attendee-drawer__eyebrow">' + i18n( 'attendeeDetails', 'Enter attendee details' ) + '</span>' +
							'<h3 class="evently-attendee-drawer__title" id="' + titleId + '">' + i18n( 'attendeeDrawerTitle', 'Attendee details' ) + '</h3>' +
							'<p class="evently-attendee-drawer__help">' + i18n( 'attendeeDrawerHelp', 'Complete the required fields for this ticket, then save.' ) + '</p>' +
						'</div>' +
						'<button type="button" class="evently-attendee-drawer__close" data-ev-attendee-close aria-label="' + i18n( 'close', 'Close' ) + '">' +
							'<i class="fas fa-times" aria-hidden="true"></i>' +
						'</button>' +
					'</div>' +
					'<div class="evently-attendee-drawer__body"></div>' +
					'<div class="evently-attendee-drawer__foot">' +
						'<button type="button" class="evently-attendee-drawer__continue">' + i18n( 'attendeeContinue', 'Save attendee details' ) + '</button>' +
					'</div>' +
				'</div>' +
			'</div>'
		);
		$drawer.attr( 'data-ev-drawer-ticket', ticketKey );
		$drawer.attr( 'data-evently-root', rootId );
		$( 'body' ).append( $drawer );
		return $drawer;
	}

	function collectAttendeeHomes( $area ) {
		var homes = [];
		$area.find( '.mep_ticket_item > .mep_attendee_info' ).each( function () {
			homes.push( $( this ) );
		} );
		$area.find( '.mpwem_booking_panel > .mep_attendee_info' ).each( function () {
			homes.push( $( this ) );
		} );
		$area.find( '.mpwem_seat_plan_area .mep_attendee_info' ).each( function () {
			homes.push( $( this ) );
		} );
		return homes;
	}

	function restoreAttendeesFromDrawer( $root ) {
		var $area = $root.find( '.mpwem_registration_area' ).first();
		if ( ! $area.length ) {
			return;
		}
		var rootId = ensureRootId( $root );

		$( '.evently-attendee-drawer[data-evently-root="' + cssEscape( rootId ) + '"] .evently-attendee-drawer__body' )
			.children( '.mep_attendee_info' )
			.each( function () {
				var $info = $( this );
				var id = $info.attr( 'data-ev-home-id' );
				if ( ! id ) {
					return;
				}
				var $home = $area.find( '.ev-attendee-home[data-ev-placeholder="' + id + '"]' ).first();
				if ( $home.length ) {
					unbindDrawerFieldsFromForm( $info );
					$home.before( $info );
				}
			} );
	}

	function relocateAttendeesToDrawer( $root ) {
		var $area = $root.find( '.mpwem_registration_area' ).first();
		if ( ! $area.length || ! hasAttendeeSupport( $area ) ) {
			return;
		}

		var formId = getRegistrationFormId( $area );
		var sameMode = isSameAttendeeMode( $area );
		var rootId = ensureRootId( $root );

		$.each( collectAttendeeHomes( $area ), function ( _, $info ) {
			if ( ! $info || ! $info.length || $info.closest( '.evently-attendee-drawer' ).length ) {
				return;
			}
			var id = $info.attr( 'data-ev-home-id' );
			if ( ! id ) {
				id = 'ev-home-' + Math.random().toString( 36 ).slice( 2, 10 );
				$info.attr( 'data-ev-home-id', id );
			}
			var $item = $info.closest( '.mep_ticket_item' );
			var ticketKey = sameMode || ! $item.length ? '__same__' : getTicketKeyFromItem( $item );
			$info.attr( 'data-ev-ticket-key', ticketKey );
			if ( ticketKey === '__same__' ) {
				$info.attr( 'data-ev-same', '1' );
			}
			var $home = $area.find( '.ev-attendee-home[data-ev-placeholder="' + id + '"]' ).first();
			if ( ! $home.length ) {
				$home = $( '<div class="ev-attendee-home" data-ev-placeholder="' + id + '" hidden></div>' );
				$info.after( $home );
			}
			$home.attr( 'data-ev-ticket-key', ticketKey );

			var $drawer = ensureAttendeeDrawer( $root, $area, ticketKey );
			$drawer.find( '.evently-attendee-drawer__body' ).append( $info );
		} );

		$( '.evently-attendee-drawer[data-evently-root="' + cssEscape( rootId ) + '"] .evently-attendee-drawer__body > .mep_attendee_info' ).each( function () {
			var $info = $( this );
			var ticketKey = $info.attr( 'data-ev-ticket-key' ) || ( sameMode ? '__same__' : '' );
			if ( ! ticketKey ) {
				var homeId = $info.attr( 'data-ev-home-id' );
				var $home = homeId ? $area.find( '.ev-attendee-home[data-ev-placeholder="' + homeId + '"]' ).first() : $();
				ticketKey = ( $home.attr( 'data-ev-ticket-key' ) || '' ) || ( sameMode ? '__same__' : '' );
			}
			if ( ! ticketKey ) {
				return;
			}
			$info.attr( 'data-ev-ticket-key', ticketKey );
			var $targetBody = ensureAttendeeDrawer( $root, $area, ticketKey ).find( '.evently-attendee-drawer__body' );
			if ( ! $info.parent().is( $targetBody ) ) {
				$targetBody.append( $info );
			}
		} );

		$area.find( '.ev-attendee-home' ).each( function () {
			var $home = $( this );
			var id = $home.attr( 'data-ev-placeholder' );
			var ticketKey = $home.attr( 'data-ev-ticket-key' ) || '__same__';
			var $info = getDrawerForTicket( $root, ticketKey )
				.find( '.evently-attendee-drawer__body' )
				.children( '.mep_attendee_info[data-ev-home-id="' + id + '"]' );
			if ( $info.length && $home.children().length ) {
				$info.append( $home.children() );
			}
		} );

		$( '.evently-attendee-drawer[data-evently-root="' + cssEscape( rootId ) + '"] .evently-attendee-drawer__body .mep_attendee_info, .evently-attendee-drawer[data-evently-root="' + cssEscape( rootId ) + '"] .evently-attendee-drawer__body .mep_form_item' )
			.addClass( 'evently-attendee-block is-ev-active-ticket' );

		$( '.evently-attendee-drawer[data-evently-root="' + cssEscape( rootId ) + '"] .evently-attendee-drawer__body' ).each( function () {
			bindDrawerFieldsToForm( $( this ), formId );
			enhanceAttendeeFormCards( $( this ) );
		} );
	}

	function ticketHasAttendeeForms( $root, ticketKey ) {
		return getAttendeeScope( $root, ticketKey ).find( '.mep_form_item' ).length > 0;
	}

	function setActiveTicketInDrawer( $root, ticketKey ) {
		var $drawer = getDrawerForTicket( $root, ticketKey );
		if ( ! $drawer.length ) {
			return;
		}
		var title = i18n( 'attendeeDrawerTitle', 'Attendee details' );
		var label = '';
		if ( ticketKey === '__same__' ) {
			label = title;
		} else {
			var $item = findTicketItemByKey( $root, ticketKey );
			label = getTicketLabelFromItem( $item );
			title = i18n( 'attendeeForTicket', 'Attendees for %s' ).replace( '%s', label );
		}
		$drawer.find( '.evently-attendee-drawer__title' ).text( title );
		$drawer.attr( 'data-ev-active-ticket', ticketKey || '' );
		$root.data( 'ev-active-ticket-key', ticketKey || '' );
	}

	function openAttendeeDrawer( $root, ticketKey ) {
		var $area = $root.find( '.mpwem_registration_area' ).first();
		if ( ! $area.length ) {
			return;
		}
		relocateAttendeesToDrawer( $root );
		if ( ! ticketKey ) {
			ticketKey = isSameAttendeeMode( $area ) ? '__same__' : ( $root.data( 'ev-active-ticket-key' ) || '' );
		}
		if ( ! ticketKey || ! ticketHasAttendeeForms( $root, ticketKey ) ) {
			return;
		}

		var rootId = ensureRootId( $root );
		$( '.evently-attendee-drawer[data-evently-root="' + cssEscape( rootId ) + '"]' ).each( function () {
			var $other = $( this );
			if ( ( $other.attr( 'data-ev-drawer-ticket' ) || '' ) !== ticketKey ) {
				$other.attr( 'hidden', true );
			}
		} );

		var $drawer = ensureAttendeeDrawer( $root, $area, ticketKey );
		setActiveTicketInDrawer( $root, ticketKey );
		$drawer.find( '.evently-attendee-drawer__body .mep_attendee_info' ).addClass( 'is-ev-active-ticket' );
		enhanceAttendeeFormCards( $drawer.find( '.evently-attendee-drawer__body' ) );
		$drawer.removeAttr( 'hidden' );
		$( 'body' ).addClass( 'evently-attendee-drawer-open' );
		setTimeout( function () {
			var $focus = getAttendeeScope( $root, ticketKey ).find( 'input, select, textarea' ).filter( ':visible' ).first();
			if ( $focus.length ) {
				$focus.trigger( 'focus' );
			}
		}, 40 );
	}

	function closeAttendeeDrawer( $root, ticketKey ) {
		var rootId = ensureRootId( $root );
		var $drawers = ticketKey
			? getDrawerForTicket( $root, ticketKey )
			: $( '.evently-attendee-drawer[data-evently-root="' + cssEscape( rootId ) + '"]' );
		if ( ! $drawers.length ) {
			return;
		}
		$drawers.attr( 'hidden', true );
		if ( ! $( '.evently-attendee-drawer:not([hidden])' ).length ) {
			$( 'body' ).removeClass( 'evently-attendee-drawer-open' );
		}
		updateAttendeeStatusCards( $root );
	}

	function ensureStatusCard( $host, ticketKey ) {
		var $card = $();
		$host.children( '.evently-attendee-status' ).each( function () {
			if ( ( $( this ).attr( 'data-ev-ticket-key' ) || '' ) === ticketKey ) {
				$card = $( this );
				return false;
			}
		} );
		if ( $card.length ) {
			return $card;
		}
		$card = $(
			'<div class="evently-attendee-status">' +
				'<span class="evently-attendee-status__icon" aria-hidden="true"><i class="fas fa-user-check"></i></span>' +
				'<span class="evently-attendee-status__text"></span>' +
				'<button type="button" class="evently-attendee-status__edit"></button>' +
			'</div>'
		);
		$card.attr( 'data-ev-ticket-key', ticketKey );
		$host.append( $card );
		return $card;
	}

	function updateAttendeeStatusCards( $root ) {
		var $area = $root.find( '.mpwem_registration_area' ).first();
		if ( ! $area.length || ! hasAttendeeSupport( $area ) ) {
			$root.find( '.evently-attendee-status' ).remove();
			return;
		}

		relocateAttendeesToDrawer( $root );
		var sameMode = isSameAttendeeMode( $area );
		var savedMap = getSavedMap( $root );

		if ( sameMode ) {
			$root.find( '.mpwem_ticket_type .evently-attendee-status' ).remove();
			var $submit = $area.find( '.mpwem_form_submit_area' ).first();
			var $host = $submit.length ? $submit.parent() : $area.find( '.mpwem_booking_panel' ).first();
			var formCount = getAttendeeScope( $root, '__same__' ).find( '.mep_form_item' ).length;
			var totalQty = getTicketQty( $root );
			var $card = ensureStatusCard( $host, '__same__' );
			if ( $submit.length && ! $card.next().is( $submit ) && ! $submit.find( $card ).length ) {
				$card.insertBefore( $submit );
			}
			if ( totalQty < 1 || formCount < 1 ) {
				$card.removeClass( 'is-visible is-complete is-incomplete' ).attr( 'hidden', true );
				return;
			}
			var complete = areAttendeeFieldsComplete( $root, '__same__' ) && !! savedMap.__same__;
			$card.removeAttr( 'hidden' ).addClass( 'is-visible' )
				.toggleClass( 'is-complete', complete )
				.toggleClass( 'is-incomplete', ! complete );
			$card.find( '.evently-attendee-status__text' ).text(
				complete
					? i18n( 'attendeeAdded', 'Attendee details added' )
					: i18n( 'attendeeDetails', 'Enter attendee details' )
			);
			$card.find( '.evently-attendee-status__edit' ).text(
				complete ? i18n( 'attendeeEdit', 'Edit' ) : i18n( 'attendeeDetails', 'Enter attendee details' )
			);
			return;
		}

		$area.find( '.evently-attendee-status[data-ev-ticket-key="__same__"]' ).remove();

		$root.find( '.mpwem_ticket_type .mep_ticket_item' ).each( function () {
			var $item = $( this );
			if ( $item.closest( '.mpwem_ex_service' ).length ) {
				return;
			}
			var key = getTicketKeyFromItem( $item );
			var qty = getItemQty( $item );
			var formCount = getAttendeeScope( $root, key ).find( '.mep_form_item' ).length;
			var $status = ensureStatusCard( $item, key );

			if ( qty < 1 || formCount < 1 ) {
				$status.removeClass( 'is-visible is-complete is-incomplete' ).attr( 'hidden', true );
				return;
			}

			var done = areAttendeeFieldsComplete( $root, key ) && !! savedMap[ key ];
			$status.removeAttr( 'hidden' ).addClass( 'is-visible' )
				.toggleClass( 'is-complete', done )
				.toggleClass( 'is-incomplete', ! done );
			$status.find( '.evently-attendee-status__text' ).text(
				done
					? i18n( 'attendeeAdded', 'Attendee details added' )
					: i18n( 'attendeeDetails', 'Enter attendee details' )
			);
			$status.find( '.evently-attendee-status__edit' ).text(
				done ? i18n( 'attendeeEdit', 'Edit' ) : i18n( 'attendeeIncomplete', 'Required' )
			);
		} );
	}

	function getFirstIncompleteTicketKey( $root ) {
		var $area = $root.find( '.mpwem_registration_area' ).first();
		if ( ! $area.length ) {
			return '';
		}
		if ( isSameAttendeeMode( $area ) ) {
			if ( getTicketQty( $root ) > 0 && ticketHasAttendeeForms( $root, '__same__' ) && ! areAttendeeFieldsComplete( $root, '__same__' ) ) {
				return '__same__';
			}
			if ( getTicketQty( $root ) > 0 && ticketHasAttendeeForms( $root, '__same__' ) && ! getSavedMap( $root ).__same__ ) {
				return '__same__';
			}
			return '';
		}
		var incomplete = '';
		$root.find( '.mpwem_ticket_type .mep_ticket_item' ).each( function () {
			var $item = $( this );
			if ( $item.closest( '.mpwem_ex_service' ).length ) {
				return;
			}
			var key = getTicketKeyFromItem( $item );
			var qty = getItemQty( $item );
			if ( qty < 1 || ! ticketHasAttendeeForms( $root, key ) ) {
				return;
			}
			if ( ! areAttendeeFieldsComplete( $root, key ) || ! getSavedMap( $root )[ key ] ) {
				incomplete = key;
				return false;
			}
		} );
		return incomplete;
	}

	function enhanceAttendeeFormCards( $scope ) {
		if ( ! $scope || ! $scope.length ) {
			return;
		}
		$scope.find( '.mep_attendee_info > .mep_form_item' ).each( function () {
			var $card = $( this );
			var fieldCount = $card.children( '.mp_form_item' ).length;
			$card.toggleClass( 'mep-form--cols-2', fieldCount > 4 );

			if ( $card.data( 'evCardEnhanced' ) ) {
				var $count = $card.find( '.mpwem_ticket_count' ).first();
				var $name = $card.find( '.mpwem_ticket_name' ).first();
				var countText = $.trim( $count.text() ) || '1';
				var nameText = $.trim( $name.text() ) || '';
				$card.find( '.evently-attendee-card__index' ).text( countText );
				$card.find( '.evently-attendee-card__pill' ).text( '#' + countText );
				if ( nameText ) {
					$card.find( '.evently-attendee-card__ticket-name' ).text( nameText );
				}
				return;
			}
			var $h6 = $card.children( 'h6' ).first();
			if ( ! $h6.length ) {
				return;
			}
			var ticketName = $.trim( $h6.find( '.mpwem_ticket_name' ).first().text() ) || '';
			var ticketCount = $.trim( $h6.find( '.mpwem_ticket_count' ).first().text() ) || '1';
			var $head = $(
				'<div class="evently-attendee-card__head">' +
					'<span class="evently-attendee-card__index" aria-hidden="true"></span>' +
					'<div class="evently-attendee-card__meta">' +
						'<span class="evently-attendee-card__label"></span>' +
						'<span class="evently-attendee-card__ticket">' +
							'<span class="evently-attendee-card__ticket-name"></span>' +
							'<span class="evently-attendee-card__pill"></span>' +
						'</span>' +
					'</div>' +
				'</div>'
			);
			$head.find( '.evently-attendee-card__index' ).text( ticketCount );
			$head.find( '.evently-attendee-card__label' ).text( i18n( 'attendeeCardLabel', 'Attendee' ) );
			$head.find( '.evently-attendee-card__ticket-name' ).text( ticketName || i18n( 'attendeeDrawerTitle', 'Attendee details' ) );
			$head.find( '.evently-attendee-card__pill' ).text( '#' + ticketCount );
			var $legacySpans = $h6.find( '.mpwem_ticket_name, .mpwem_ticket_count' ).detach();
			$legacySpans.css( {
				position: 'absolute',
				width: '1px',
				height: '1px',
				padding: 0,
				margin: '-1px',
				overflow: 'hidden',
				clip: 'rect(0, 0, 0, 0)',
				border: 0,
			} );
			$h6.replaceWith( $head );
			$head.append( $legacySpans );
			$card.data( 'evCardEnhanced', 1 );
		} );
	}

	function enhanceAttendee( $root ) {
		if ( ! $root || ! $root.length ) {
			return;
		}
		$root.addClass( 'evently-booking-card--attendee-drawer' );
		updateAttendeeStatusCards( $root );
		var rootId = ensureRootId( $root );
		enhanceAttendeeFormCards(
			$( '.evently-attendee-drawer[data-evently-root="' + cssEscape( rootId ) + '"] .evently-attendee-drawer__body' )
		);
	}

	function bindAttendeeDrawer( $root ) {
		$root.off( 'click.evAttendeeEdit' ).on( 'click.evAttendeeEdit', '.evently-attendee-status__edit, .evently-attendee-status.is-incomplete', function ( e ) {
			e.preventDefault();
			var $card = $( this ).closest( '.evently-attendee-status' );
			var key = $card.attr( 'data-ev-ticket-key' ) || '';
			if ( ! key ) {
				return;
			}
			openAttendeeDrawer( $root, key );
		} );

		$( document )
			.off( 'click.evAttendeeClose.' + ensureRootId( $root ) )
			.on( 'click.evAttendeeClose.' + ensureRootId( $root ), '.evently-attendee-drawer[data-evently-root="' + cssEscape( ensureRootId( $root ) ) + '"] [data-ev-attendee-close]', function ( e ) {
				e.preventDefault();
				var ticketKey = $( this ).closest( '.evently-attendee-drawer' ).attr( 'data-ev-drawer-ticket' ) || '';
				closeAttendeeDrawer( $root, ticketKey );
			} );

		$( document )
			.off( 'click.evAttendeeContinue.' + ensureRootId( $root ) )
			.on( 'click.evAttendeeContinue.' + ensureRootId( $root ), '.evently-attendee-drawer[data-evently-root="' + cssEscape( ensureRootId( $root ) ) + '"] .evently-attendee-drawer__continue', function ( e ) {
				e.preventDefault();
				var $drawer = $( this ).closest( '.evently-attendee-drawer' );
				var ticketKey = $drawer.attr( 'data-ev-drawer-ticket' ) ||
					$drawer.attr( 'data-ev-active-ticket' ) ||
					$root.data( 'ev-active-ticket-key' ) || '';
				var $invalid = markInvalidAttendeeFields( $root, ticketKey );
				if ( $invalid && $invalid.length ) {
					openAttendeeDrawer( $root, ticketKey );
					$invalid.trigger( 'focus' );
					return;
				}
				setTicketSaved( $root, ticketKey, true );
				closeAttendeeDrawer( $root, ticketKey );
			} );

		$( document )
			.off( 'input.evAttendeeChange.' + ensureRootId( $root ) + ' change.evAttendeeChange.' + ensureRootId( $root ) )
			.on(
				'input.evAttendeeChange.' + ensureRootId( $root ) + ' change.evAttendeeChange.' + ensureRootId( $root ),
				'.evently-attendee-drawer[data-evently-root="' + cssEscape( ensureRootId( $root ) ) + '"] .evently-attendee-drawer__body input, .evently-attendee-drawer[data-evently-root="' + cssEscape( ensureRootId( $root ) ) + '"] .evently-attendee-drawer__body select, .evently-attendee-drawer[data-evently-root="' + cssEscape( ensureRootId( $root ) ) + '"] .evently-attendee-drawer__body textarea',
				function () {
					$( this ).removeClass( 'is-ev-invalid' );
					var ticketKey = $( this ).closest( '.evently-attendee-drawer' ).attr( 'data-ev-drawer-ticket' ) ||
						$root.data( 'ev-active-ticket-key' ) || '';
					if ( ticketKey ) {
						setTicketSaved( $root, ticketKey, false );
					}
				}
			);
	}

	function getTicketQty( $root ) {
		var totalQty = 0;
		$root.find( '.mpwem_ticket_type [name="option_qty[]"]' ).each( function () {
			totalQty += parseInt( $( this ).val(), 10 ) || 0;
		} );
		return totalQty;
	}

	function maybeAutoOpenAttendeeDrawer( $root, ticketKey, prevItemQty, nextItemQty ) {
		var $area = $root.find( '.mpwem_registration_area' ).first();
		if ( ! $area.length ) {
			return;
		}
		relocateAttendeesToDrawer( $root );

		if ( isSameAttendeeMode( $area ) ) {
			ticketKey = '__same__';
			if ( ! ticketHasAttendeeForms( $root, ticketKey ) ) {
				return;
			}
			var prevTotal = parseInt( $root.data( 'ev-prev-total-qty' ), 10 );
			if ( isNaN( prevTotal ) ) {
				prevTotal = 0;
			}
			var nextTotal = getTicketQty( $root );
			if ( nextTotal < 1 ) {
				setTicketSaved( $root, ticketKey, false );
				$root.removeData( 'ev-same-attendee-opened' );
				updateAttendeeStatusCards( $root );
				return;
			}
			if ( prevTotal === 0 && nextTotal > 0 && ! $root.data( 'ev-same-attendee-opened' ) ) {
				$root.data( 'ev-same-attendee-opened', 1 );
				openAttendeeDrawer( $root, ticketKey );
			}
			return;
		}

		if ( ! ticketKey ) {
			return;
		}
		if ( ! ticketHasAttendeeForms( $root, ticketKey ) ) {
			return;
		}
		if ( ( nextItemQty || 0 ) > ( prevItemQty || 0 ) && nextItemQty > 0 ) {
			setTicketSaved( $root, ticketKey, false );
			openAttendeeDrawer( $root, ticketKey );
		}
	}

	function initAttendeeForRoot( $root ) {
		if ( ! $root.length ) {
			return;
		}
		var $area = $root.find( '.mpwem_registration_area' ).first();
		if ( ! $area.length || ! hasAttendeeSupport( $area ) ) {
			return;
		}
		ensureRootId( $root );
		bindAttendeeDrawer( $root );
		enhanceAttendee( $root );
	}

	function initAll() {
		getRoots().each( function () {
			initAttendeeForRoot( $( this ) );
		} );
	}

	$( document ).ready( initAll );
	$( document ).on( 'mpwem_ticket_reload mpwem_registration_reload', function () {
		setTimeout( initAll, 80 );
	} );

	$( document ).ajaxComplete( function ( event, xhr, settings ) {
		if ( ! getRoots().length || ! settings ) {
			return;
		}
		var payload = settings.data;
		var query = '';
		if ( typeof payload === 'string' ) {
			query = payload;
		} else if ( payload && typeof payload === 'object' && typeof $.param === 'function' && ! ( payload instanceof FormData ) ) {
			try {
				query = $.param( payload );
			} catch ( err ) {
				query = '';
			}
		}
		if ( ! query || query.indexOf( 'get_mpwem_ticket' ) === -1 ) {
			return;
		}
		setTimeout( initAll, 60 );
	} );

	document.addEventListener( 'click', function ( e ) {
		var t = e.target;
		if ( ! t || ! t.closest ) {
			return;
		}
		if ( ! t.closest( ROOT ) ) {
			return;
		}
		if ( ! t.closest( '.incQty, .decQty, .qtyIncDec' ) ) {
			return;
		}
		if ( t.closest( '.mpwem_ex_service' ) ) {
			return;
		}
		var $root = $( t ).closest( ROOT );
		$root.data( 'ev-prev-total-qty', getTicketQty( $root ) );
		var $item = $( t ).closest( '.mep_ticket_item' );
		if ( $item.length ) {
			$root.data( 'ev-focus-ticket-key', getTicketKeyFromItem( $item ) );
			$root.data( 'ev-prev-item-qty', getItemQty( $item ) );
		}
		restoreAttendeesFromDrawer( $root );
	}, true );

	document.addEventListener( 'focusin', function ( e ) {
		var t = e.target;
		if ( ! t || ! t.closest || ! t.closest( ROOT ) ) {
			return;
		}
		if ( t.closest( '.mpwem_ex_service' ) ) {
			return;
		}
		if ( ! t.matches || ! t.matches( '.inputIncDec, select[name="option_qty[]"], [name="option_qty[]"]' ) ) {
			return;
		}
		var $root = $( t ).closest( ROOT );
		$root.data( 'ev-prev-total-qty', getTicketQty( $root ) );
		var $item = $( t ).closest( '.mep_ticket_item' );
		if ( $item.length ) {
			$root.data( 'ev-focus-ticket-key', getTicketKeyFromItem( $item ) );
			$root.data( 'ev-prev-item-qty', getItemQty( $item ) );
		}
	}, true );

	document.addEventListener( 'change', function ( e ) {
		var t = e.target;
		if ( ! t || ! t.closest ) {
			return;
		}
		if ( ! t.closest( ROOT ) ) {
			return;
		}
		if ( t.closest( '.mpwem_ex_service' ) ) {
			return;
		}
		if ( ! t.matches || ! t.matches( '.inputIncDec, select[name="option_qty[]"], [name="option_qty[]"]' ) ) {
			return;
		}
		restoreAttendeesFromDrawer( $( t ).closest( ROOT ) );
	}, true );

	$( document ).on( 'click', ROOT + ' .qtyIncDec .incQty, ' + ROOT + ' .qtyIncDec .decQty', function () {
		var $btn = $( this );
		var $root = $btn.closest( ROOT );
		if ( $btn.closest( '.mpwem_ex_service' ).length ) {
			setTimeout( function () {
				enhanceAttendee( $root );
			}, 80 );
			return;
		}
		var $item = $btn.closest( '.mep_ticket_item' );
		var ticketKey = $root.data( 'ev-focus-ticket-key' ) || getTicketKeyFromItem( $item );
		var prevItemQty = parseInt( $root.data( 'ev-prev-item-qty' ), 10 );
		if ( isNaN( prevItemQty ) ) {
			prevItemQty = 0;
		}
		setTimeout( function () {
			var $freshItem = findTicketItemByKey( $root, ticketKey );
			var nextItemQty = getItemQty( $freshItem.length ? $freshItem : $item );
			enhanceAttendee( $root );
			maybeAutoOpenAttendeeDrawer( $root, ticketKey, prevItemQty, nextItemQty );
		}, 80 );
	} );

	$( document ).on(
		'change input',
		ROOT + ' .inputIncDec, ' + ROOT + ' select[name="option_qty[]"], ' + ROOT + ' select[name="event_extra_service_qty[]"]',
		function () {
			var $el = $( this );
			var $root = $el.closest( ROOT );
			var isExtraService = $el.closest( '.mpwem_ex_service' ).length > 0 ||
				$el.is( '[name="event_extra_service_qty[]"]' );
			if ( isExtraService || ! $el.is( '[name="option_qty[]"], .inputIncDec' ) ) {
				setTimeout( function () {
					enhanceAttendee( $root );
				}, 80 );
				return;
			}
			var $item = $el.closest( '.mep_ticket_item' );
			var ticketKey = $root.data( 'ev-focus-ticket-key' ) || getTicketKeyFromItem( $item );
			var prevItemQty = parseInt( $root.data( 'ev-prev-item-qty' ), 10 );
			if ( isNaN( prevItemQty ) ) {
				prevItemQty = 0;
			}
			setTimeout( function () {
				var $freshItem = findTicketItemByKey( $root, ticketKey );
				var nextItemQty = getItemQty( $freshItem.length ? $freshItem : $item );
				enhanceAttendee( $root );
				maybeAutoOpenAttendeeDrawer( $root, ticketKey, prevItemQty, nextItemQty );
			}, 80 );
		}
	);

	document.addEventListener( 'click', function ( e ) {
		var t = e.target;
		if ( ! t || ! t.closest ) {
			return;
		}
		var bookBtn = t.closest( ROOT + ' .mpwem_book_now' );
		if ( ! bookBtn ) {
			return;
		}
		var $root = $( bookBtn ).closest( ROOT );
		var $area = $root.find( '.mpwem_registration_area' ).first();
		if ( ! $area.length || ! hasAttendeeSupport( $area ) ) {
			return;
		}
		relocateAttendeesToDrawer( $root );
		var incompleteKey = getFirstIncompleteTicketKey( $root );
		if ( ! incompleteKey ) {
			return;
		}
		e.preventDefault();
		e.stopImmediatePropagation();
		openAttendeeDrawer( $root, incompleteKey );
		var $invalid = markInvalidAttendeeFields( $root, incompleteKey );
		if ( $invalid && $invalid.length ) {
			$invalid.trigger( 'focus' );
		}
		updateAttendeeStatusCards( $root );
	}, true );

	$( document ).on( 'keydown.evAttendeeDrawer', function ( e ) {
		if ( e.key !== 'Escape' ) {
			return;
		}
		var $open = $( '.evently-attendee-drawer:not([hidden])' ).first();
		if ( ! $open.length ) {
			return;
		}
		var rootId = $open.attr( 'data-evently-root' ) || '';
		var $root = rootId ? $( ROOT + '[data-evently-attendee-root="' + cssEscape( rootId ) + '"]' ) : getRoots().first();
		closeAttendeeDrawer( $root );
	} );
}( jQuery ) );
