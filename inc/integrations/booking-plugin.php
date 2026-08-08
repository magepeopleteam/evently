<?php
/**
 * Evently_Booking_Adapter — the ONLY place in the theme that talks to the
 * event-booking plugin's internals (mage-eventpress, CPT `mep_events`).
 *
 * Every template calls these static methods, never `MPWEM_*` classes or
 * `mep_*` functions directly. That keeps the integration swappable and
 * matches brief §17: "First inspect the existing booking plugin... If the
 * plugin exposes APIs/hooks, integrate with those... Use actual APIs only
 * after inspecting them." Every accessor below was verified against the
 * plugin's real source (inc/MPWEM_Functions.php, inc/MPWEM_Query.php) —
 * see docs/implementation-plan.md §2.1 for citations.
 *
 * If the plugin is inactive, every method degrades to null/empty rather
 * than throwing — callers already check evently_has_booking_plugin() via
 * evently_get_home_events() etc., but the adapter is defensive on its own
 * too so it's safe to call directly.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'Evently_Booking_Adapter' ) ) {
	return;
}

/**
 * Class Evently_Booking_Adapter
 */
class Evently_Booking_Adapter {

	/**
	 * @return bool Whether the plugin this adapter wraps is active.
	 */
	public static function is_active() {
		return evently_has_booking_plugin() && class_exists( 'MPWEM_Functions' ) && class_exists( 'MPWEM_Query' );
	}

	/**
	 * Raw plugin data for one event — the full `MPWEM_Functions::get_all_info()`
	 * array (all post meta + computed date/location keys). Single-event
	 * templates (Phase 5) use this directly for anything not covered by the
	 * more specific accessors below.
	 *
	 * @param int $event_id
	 * @return array
	 */
	public static function get_event_meta( $event_id ) {
		if ( ! self::is_active() || ! $event_id ) {
			return array();
		}
		return MPWEM_Functions::get_all_info( $event_id );
	}

	/**
	 * The event's next/primary date as a Unix timestamp, or 0 if unknown.
	 *
	 * @param int   $event_id
	 * @param array $meta Optional — pass an already-fetched get_event_meta() to avoid re-querying.
	 * @return int
	 */
	public static function get_upcoming_timestamp( $event_id, $meta = null ) {
		if ( ! self::is_active() ) {
			return 0;
		}
		$meta = null !== $meta ? $meta : self::get_event_meta( $event_id );
		$raw  = isset( $meta['upcoming_date'] ) ? $meta['upcoming_date'] : '';
		return $raw ? (int) strtotime( $raw ) : 0;
	}

	/**
	 * Human-readable venue/city/country string, e.g. "Dhaka, Bangladesh" or
	 * "Army Stadium, Dhaka". Prefers the venue name when set.
	 *
	 * @param int   $event_id
	 * @param array $meta Optional pre-fetched meta.
	 * @return string
	 */
	public static function get_location_string( $event_id, $meta = null ) {
		if ( ! self::is_active() ) {
			return '';
		}
		$meta  = null !== $meta ? $meta : self::get_event_meta( $event_id );
		$venue = isset( $meta['mep_location_venue'] ) ? $meta['mep_location_venue'] : '';
		$city  = isset( $meta['mep_city'] ) ? $meta['mep_city'] : '';
		$country = isset( $meta['mep_country'] ) ? $meta['mep_country'] : '';

		if ( $venue && $city ) {
			return $venue . ', ' . $city;
		}
		$parts = array_filter( array( $city, $country ) );
		return $venue ? $venue : implode( ', ', $parts );
	}

	/**
	 * Full postal address parts, for the single-event Venue block + map.
	 *
	 * @param int $event_id
	 * @return array{location:string,street:string,city:string,state:string,zip:string,country:string}
	 */
	public static function get_address( $event_id ) {
		if ( ! self::is_active() ) {
			return array();
		}
		return MPWEM_Functions::get_location( $event_id );
	}

	/**
	 * The event's primary category term name (for the small badge on cards).
	 *
	 * @param int $event_id
	 * @return string
	 */
	public static function get_category_label( $event_id ) {
		if ( ! self::is_active() || ! taxonomy_exists( 'mep_cat' ) ) {
			return '';
		}
		$terms = get_the_terms( $event_id, 'mep_cat' );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return '';
		}
		return mb_strtoupper( $terms[0]->name );
	}

	/**
	 * The event's organizer/venue term (brief §22 Organizer Profile).
	 *
	 * @param int $event_id
	 * @return WP_Term|null
	 */
	public static function get_organizer_term( $event_id ) {
		if ( ! self::is_active() || ! taxonomy_exists( 'mep_org' ) ) {
			return null;
		}
		$terms = get_the_terms( $event_id, 'mep_org' );
		return ( empty( $terms ) || is_wp_error( $terms ) ) ? null : $terms[0];
	}

	/**
	 * The event's ticket types — one entry per row of the `mep_event_ticket_type`
	 * postmeta array, normalized to a stable shape for the ticket-selection UI
	 * (brief §18).
	 *
	 * @param int $event_id
	 * @return array[] {name, description, price, quantity_available, reserved, sale_end}
	 */
	public static function get_ticket_types( $event_id ) {
		if ( ! self::is_active() ) {
			return array();
		}

		$raw = MPWEM_Global_Function::get_post_info( $event_id, 'mep_event_ticket_type', array() );
		if ( ! is_array( $raw ) ) {
			return array();
		}

		$tickets = array();
		foreach ( $raw as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			if ( isset( $row['option_ticket_enable'] ) && 'no' === $row['option_ticket_enable'] ) {
				continue;
			}
			$name       = isset( $row['option_name_t'] ) ? $row['option_name_t'] : '';
			$price      = isset( $row['option_price_t'] ) ? (float) $row['option_price_t'] : 0.0;
			$price      = apply_filters( 'mep_ticket_type_price', $price, $name, $event_id, $row );
			$total      = isset( $row['option_qty_t'] ) ? (int) $row['option_qty_t'] : 0;
			$reserved   = isset( $row['option_rsv_t'] ) ? (int) $row['option_rsv_t'] : 0;
			$sold       = self::get_ticket_sold_count( $event_id, $name );
			$available  = max( 0, $total - $reserved - $sold );

			$tickets[] = array(
				'name'        => $name,
				'description' => isset( $row['option_details_t'] ) ? wp_strip_all_tags( $row['option_details_t'] ) : '',
				'price'       => $price,
				'price_label' => evently_format_price( $price ),
				'total'       => $total,
				'available'   => $available,
				'sold_out'    => $total > 0 && $available <= 0,
				'sale_end'    => isset( $row['option_sale_end_date_t'] ) ? $row['option_sale_end_date_t'] : '',
			);
		}

		return $tickets;
	}

	/**
	 * Tickets already sold for one ticket-type name (used to compute live
	 * availability the same way the plugin's own templates do).
	 *
	 * @param int    $event_id
	 * @param string $ticket_name
	 * @return int
	 */
	public static function get_ticket_sold_count( $event_id, $ticket_name ) {
		if ( ! self::is_active() || function_exists( 'mep_ticket_type_sold' ) === false ) {
			return 0;
		}
		return (int) mep_ticket_type_sold( $event_id, $ticket_name );
	}

	/**
	 * The lowest ticket price for an event (brief §16 "From $49").
	 *
	 * @param int $event_id
	 * @return float
	 */
	public static function get_min_price( $event_id ) {
		if ( ! self::is_active() ) {
			return 0.0;
		}
		return (float) MPWEM_Functions::get_min_price( $event_id );
	}

	/**
	 * Total remaining seats across all ticket types for the event's next date.
	 *
	 * @param int $event_id
	 * @return int
	 */
	public static function get_total_available_seats( $event_id ) {
		$stats = self::get_seat_stats( $event_id );
		return isset( $stats['available'] ) ? (int) $stats['available'] : 0;
	}

	/**
	 * Seat totals matching Horizon's organizer card (Total / Available / Sold).
	 * Uses the same plugin helpers as templates/layout/horizon/organizer.php.
	 *
	 * @param int $event_id
	 * @return array{total:int,available:int,sold:int}
	 */
	public static function get_seat_stats( $event_id ) {
		$empty = array(
			'total'     => 0,
			'available' => 0,
			'sold'      => 0,
		);
		if ( ! self::is_active() || ! $event_id ) {
			return $empty;
		}

		$meta = self::get_event_meta( $event_id );
		$date = '';
		if ( ! empty( $meta['upcoming_date'] ) ) {
			$date = $meta['upcoming_date'];
		} elseif ( ! empty( $meta['event_start_datetime'] ) ) {
			$date = $meta['event_start_datetime'];
		}

		$total_sold  = function_exists( 'mep_ticket_type_sold' ) ? (int) mep_ticket_type_sold( $event_id, '', $date ) : 0;
		$total_seats = method_exists( 'MPWEM_Functions', 'get_total_ticket' ) ? (int) MPWEM_Functions::get_total_ticket( $event_id, $date ) : 0;
		$available   = method_exists( 'MPWEM_Functions', 'get_total_available_seat' )
			? (int) MPWEM_Functions::get_total_available_seat( $event_id, $date )
			: max( $total_seats - $total_sold, 0 );

		return array(
			'total'     => max( $total_seats, 0 ),
			'available' => max( $available, 0 ),
			'sold'      => max( $total_sold, 0 ),
		);
	}

	/**
	 * Horizon-style "venue · city" line for the sticky ticket card head.
	 *
	 * @param int   $event_id
	 * @param array $meta Optional pre-fetched meta.
	 * @return string
	 */
	public static function get_ticket_location_line( $event_id, $meta = null ) {
		if ( ! self::is_active() ) {
			return '';
		}
		$meta  = null !== $meta ? $meta : self::get_event_meta( $event_id );
		$venue = ! empty( $meta['mep_location_venue'] ) ? (string) $meta['mep_location_venue'] : '';
		$city  = ! empty( $meta['mep_city'] ) ? (string) $meta['mep_city'] : '';
		$line  = $venue;
		if ( $city && ( ! $venue || false === stripos( $venue, $city ) ) ) {
			$line = trim( $venue . ( $venue ? ' · ' : '' ) . $city );
		}
		if ( $line ) {
			return $line;
		}
		if ( ! empty( $meta['full_address'] ) && is_array( $meta['full_address'] ) ) {
			return implode( ' · ', array_filter( array_slice( $meta['full_address'], 0, 2 ) ) );
		}
		return self::get_location_string( $event_id, $meta );
	}

	/**
	 * A coarse availability status for badges: '' (plenty), 'low-stock', or 'sold-out'.
	 *
	 * @param int $event_id
	 * @param int $low_stock_threshold Below this remaining-seat count, flag "low-stock".
	 * @return string
	 */
	public static function get_availability_status( $event_id, $low_stock_threshold = 15 ) {
		if ( ! self::is_active() ) {
			return '';
		}
		$remaining = self::get_total_available_seats( $event_id );
		if ( $remaining <= 0 ) {
			return 'sold-out';
		}
		if ( $remaining <= $low_stock_threshold ) {
			return 'low-stock';
		}
		return '';
	}

	/**
	 * FAQ entries for the single-event page (brief §16).
	 *
	 * @param int $event_id
	 * @return array[] {question, answer}
	 */
	public static function get_faqs( $event_id ) {
		if ( ! self::is_active() ) {
			return array();
		}
		$raw = MPWEM_Global_Function::get_post_info( $event_id, 'mep_event_faq', array() );
		if ( ! is_array( $raw ) ) {
			return array();
		}
		$faqs = array();
		foreach ( $raw as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$question = isset( $row['mep_faq_title'] ) ? $row['mep_faq_title'] : '';
			$answer   = isset( $row['mep_faq_content'] ) ? $row['mep_faq_content'] : '';
			if ( $question ) {
				$faqs[] = array( 'question' => $question, 'answer' => $answer );
			}
		}
		return $faqs;
	}

	/**
	 * Normalized agenda/timeline rows from `mep_event_day` (same meta the
	 * plugin's `mpwem_timeline` hook reads). Empty when timeline is off.
	 *
	 * @param int $event_id
	 * @return array[] {title:string,time:string,content:string}
	 */
	public static function get_timeline_items( $event_id ) {
		if ( ! self::is_active() || ! $event_id ) {
			return array();
		}
		$status = MPWEM_Global_Function::get_post_info( $event_id, 'mep_timeline_status', 'on' );
		if ( 'off' === $status ) {
			return array();
		}
		$raw = MPWEM_Global_Function::get_post_info( $event_id, 'mep_event_day', array() );
		if ( ! is_array( $raw ) || empty( $raw ) ) {
			return array();
		}

		$items = array();
		foreach ( $raw as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$title   = isset( $row['mep_day_title'] ) ? (string) $row['mep_day_title'] : '';
			$time    = isset( $row['mep_day_time'] ) ? (string) $row['mep_day_time'] : '';
			$content = isset( $row['mep_day_content'] ) ? (string) $row['mep_day_content'] : '';
			if ( ! $title && ! $time && ! $content ) {
				continue;
			}
			$items[] = array(
				'title'   => $title,
				'time'    => $time,
				'content' => $content,
			);
		}
		return $items;
	}

	/**
	 * Whether the event has real Timeline/agenda entries to show (brief §16).
	 *
	 * @param int $event_id
	 * @return bool
	 */
	public static function has_timeline( $event_id ) {
		return ! empty( self::get_timeline_items( $event_id ) );
	}

	/**
	 * Attachment IDs for the event Gallery (`mep_gallery_images` only —
	 * deliberately not the plugin slider's "fall back to featured image"
	 * behavior, since Evently already shows that photo in the hero).
	 *
	 * @param int $event_id
	 * @return int[]
	 */
	public static function get_gallery_ids( $event_id ) {
		if ( ! self::is_active() || ! $event_id ) {
			return array();
		}
		if ( 'off' === MPWEM_Global_Function::get_post_info( $event_id, 'mep_display_slider', 'on' ) ) {
			return array();
		}
		$images = MPWEM_Global_Function::get_post_info( $event_id, 'mep_gallery_images', array() );
		if ( ! is_array( $images ) ) {
			return array();
		}
		return array_values( array_filter( array_map( 'absint', $images ) ) );
	}

	/**
	 * Whether the event has real Gallery images to show.
	 *
	 * @param int $event_id
	 * @return bool
	 */
	public static function has_gallery( $event_id ) {
		return ! empty( self::get_gallery_ids( $event_id ) );
	}

	/**
	 * Speaker post IDs attached to the event (brief §16 Schedule/Speakers).
	 *
	 * @param int $event_id
	 * @return int[]
	 */
	public static function get_speaker_ids( $event_id ) {
		if ( ! self::is_active() ) {
			return array();
		}
		$enabled = MPWEM_Global_Function::get_post_info( $event_id, 'mep_event_enable_speaker', 'no' );
		if ( 'yes' !== $enabled ) {
			return array();
		}
		$ids = MPWEM_Global_Function::get_post_info( $event_id, 'mep_event_speakers_list', array() );
		return is_array( $ids ) ? array_map( 'absint', $ids ) : array();
	}

	/**
	 * The REAL ticket-selection + add-to-cart form, straight from the
	 * plugin. This is the one place Evently deliberately does NOT rebuild
	 * plugin markup from scratch — early-bird windows, member-only gating,
	 * group-quantity rules, already-in-cart detection, and the
	 * WooCommerce-vs-native-checkout branch all live here, and duplicating
	 * that in the theme would be exactly the "business logic in the theme"
	 * anti-pattern brief §17 warns against. `do_action( 'mpwem_registration' )`
	 * is the plugin's own public hook for this (also what its
	 * `[event-add-cart-section]` shortcode calls) — Evently only wraps and
	 * restyles the real output via assets/css/single-event.css.
	 *
	 * @param int $event_id
	 * @return string HTML. Trusted plugin-rendered markup — echo directly, don't esc_html() it.
	 */
	public static function render_booking_form( $event_id ) {
		if ( ! self::is_active() || ! $event_id ) {
			return '';
		}

		ob_start();
		/**
		 * Fires the plugin's real registration/ticket-selection form.
		 *
		 * @param int   $event_id
		 * @param array $event_infos
		 */
		do_action( 'mpwem_registration', $event_id, self::get_event_meta( $event_id ) );
		return (string) ob_get_clean();
	}

	/**
	 * Same real-hook pattern as render_booking_form(), for the smaller
	 * plugin-rendered widgets Evently reuses instead of re-deriving from
	 * raw meta: seat status, calendar, share, timeline, gallery slider.
	 *
	 * @param string $hook 'mpwem_seat_status'|'mpwem_add_calender'|'mpwem_social'|'mpwem_timeline'|'mpwem_custom_slider'.
	 * @param int    $event_id
	 * @return string HTML.
	 */
	public static function render_hook_widget( $hook, $event_id ) {
		$allowed = array( 'mpwem_seat_status', 'mpwem_add_calender', 'mpwem_social', 'mpwem_timeline', 'mpwem_custom_slider' );
		if ( ! self::is_active() || ! $event_id || ! in_array( $hook, $allowed, true ) ) {
			return '';
		}

		$meta = self::get_event_meta( $event_id );

		ob_start();
		if ( 'mpwem_add_calender' === $hook ) {
			do_action( 'mpwem_add_calender', $event_id, isset( $meta['all_date'] ) ? $meta['all_date'] : array(), isset( $meta['upcoming_date'] ) ? $meta['upcoming_date'] : '' );
		} else {
			do_action( $hook, $event_id, $meta );
		}
		return (string) ob_get_clean();
	}

	/**
	 * Whether the Pro event-review addon is loaded (review list/form callbacks exist).
	 *
	 * Free mage-eventpress has no reviews UI; Pro bundles
	 * woocommerce-event-manager-addon-review-rating, which registers
	 * mep_event_review_list / mep_event_review_form on after-single-events
	 * and mpwem_horizon_reviews.
	 *
	 * @return bool
	 */
	public static function has_event_reviews() {
		return self::is_active() && function_exists( 'mep_event_review_list' );
	}

	/**
	 * Real Pro review list + write/edit modal for the Evently single template.
	 *
	 * Mirrors the plugin's single-events.php / Horizon placement: fire
	 * after-single-events (form, success notice, and list when the event's
	 * details template is not horizon.php) then mpwem_horizon_reviews (list
	 * only when the event template setting is horizon.php). The addon itself
	 * skips the wrong hook so the list is never double-printed.
	 *
	 * @param int $event_id
	 * @return string HTML. Trusted Pro-rendered markup — echo directly.
	 */
	public static function render_event_reviews( $event_id ) {
		if ( ! self::has_event_reviews() || ! $event_id ) {
			return '';
		}

		ob_start();
		do_action( 'after-single-events' );
		do_action( 'mpwem_horizon_reviews', $event_id );
		return (string) ob_get_clean();
	}

	/**
	 * Where "Book this event" should actually happen. This plugin puts the
	 * whole ticket-selection + add-to-cart form on the event's own single
	 * page (there's no separate booking-flow URL), so this is just the
	 * permalink — kept as a named method so templates never hardcode that
	 * assumption directly.
	 *
	 * @param int $event_id
	 * @return string
	 */
	public static function get_booking_url( $event_id ) {
		return get_permalink( $event_id );
	}

	/**
	 * Normalize one event post into the exact shape evently_event_card()
	 * expects (see evently_normalize_event()). This is the single mapping
	 * function every card-producing method below funnels through.
	 *
	 * @param int $event_id
	 * @return array
	 */
	public static function get_event_card_data( $event_id ) {
		if ( ! self::is_active() ) {
			return evently_normalize_event( array() );
		}

		$meta      = self::get_event_meta( $event_id );
		$timestamp = self::get_upcoming_timestamp( $event_id, $meta );
		$min_price = self::get_min_price( $event_id );
		$organizer = self::get_organizer_term( $event_id );

		return evently_normalize_event(
			array(
				'id'           => $event_id,
				'title'        => get_the_title( $event_id ),
				'url'          => get_permalink( $event_id ),
				'image_id'     => get_post_thumbnail_id( $event_id ),
				'image_alt'    => get_the_title( $event_id ),
				'date_label'   => $timestamp ? mb_strtoupper( wp_date( 'M j', $timestamp ) ) : '',
				'date_full'    => $timestamp ? wp_date( 'M j, Y', $timestamp ) : '',
				'time'         => $timestamp ? wp_date( get_option( 'time_format' ), $timestamp ) : '',
				'location'     => self::get_location_string( $event_id, $meta ),
				'category'     => self::get_category_label( $event_id ),
				'price_label'  => $min_price > 0 ? sprintf( /* translators: %s: lowest ticket price. */ __( 'From %s', 'evently' ), wp_strip_all_tags( evently_format_price( $min_price ) ) ) : __( 'Free', 'evently' ),
				'price'        => $min_price,
				'organizer'    => $organizer ? $organizer->name : '',
				'availability' => self::get_availability_status( $event_id ),
				'is_favorite'  => false,
			)
		);
	}

	/**
	 * Run a real, filtered `MPWEM_Query::event_query()` and return the
	 * results already mapped to card-ready arrays. This is what
	 * evently_get_home_events() and the Event Archive call.
	 *
	 * @param int    $count Max events.
	 * @param string $context Hint only (affects sort), one of 'trending'|'near-you'|'tag'|'archive'.
	 * @param array  $args {
	 *     @type string $cat, $org, $tag, $city, $country, $state, $year Passed straight to event_query().
	 *     @type string $status upcoming|expired|today. Default 'upcoming'.
	 *     @type string $sort ASC|DESC. Default 'ASC' (soonest first).
	 *     @type int    $paged
	 * }
	 * @return array[]
	 */
	public static function get_events_for_cards( $count, $context = 'trending', $args = array() ) {
		if ( ! self::is_active() ) {
			return array();
		}

		$defaults = array(
			'cat'     => '',
			'org'     => '',
			'tag'     => '',
			'city'    => '',
			'country' => '',
			'state'   => '',
			'year'    => '',
			'status'  => 'upcoming',
			'sort'    => 'ASC',
			'paged'   => 0,
		);
		$args = wp_parse_args( $args, $defaults );

		$query = MPWEM_Query::event_query(
			$count,
			$args['sort'],
			$args['cat'],
			$args['org'],
			$args['city'],
			$args['country'],
			$args['status'],
			$args['state'],
			$args['year'],
			$args['paged'],
			$args['tag']
		);

		if ( ! $query || ! $query->have_posts() ) {
			return array();
		}

		$cards = array();
		foreach ( $query->posts as $post ) {
			$cards[] = self::get_event_card_data( $post->ID );
		}

		return $cards;
	}

	/**
	 * Same as get_events_for_cards() but returns the raw WP_Query so archive
	 * templates can also read pagination (max_num_pages) and total counts.
	 *
	 * @param array $args See get_events_for_cards().
	 * @return WP_Query|null
	 */
	public static function query_events( $args = array() ) {
		if ( ! self::is_active() ) {
			return null;
		}

		$defaults = array(
			'show'    => 12,
			'cat'     => '',
			'org'     => '',
			'tag'     => '',
			'city'    => '',
			'country' => '',
			'state'   => '',
			'year'    => '',
			'status'  => 'upcoming',
			'sort'    => 'ASC',
			'paged'   => 0,
		);
		$args = wp_parse_args( $args, $defaults );

		return MPWEM_Query::event_query(
			$args['show'],
			$args['sort'],
			$args['cat'],
			$args['org'],
			$args['city'],
			$args['country'],
			$args['status'],
			$args['state'],
			$args['year'],
			$args['paged'],
			$args['tag']
		);
	}

	/**
	 * All category/organizer terms with at least one non-expired event, for
	 * filter dropdowns (brief §15).
	 *
	 * @param string $taxonomy 'mep_cat' or 'mep_org'.
	 * @return WP_Term[]
	 */
	public static function get_filter_terms( $taxonomy ) {
		if ( ! self::is_active() || ! taxonomy_exists( $taxonomy ) ) {
			return array();
		}
		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
		return is_wp_error( $terms ) ? array() : $terms;
	}

	/**
	 * Distinct cities that have at least one event — for the "Where" filter.
	 *
	 * @return string[]
	 */
	public static function get_filter_cities() {
		if ( ! self::is_active() ) {
			return array();
		}
		$cities = MPWEM_Query::get_all_post_meta_value( 'mep_city' );
		return is_array( $cities ) ? array_values( array_unique( array_filter( $cities ) ) ) : array();
	}

	/**
	 * Event IDs authored by a given user — the closest real, honest concept
	 * of "this organizer's events" the plugin's data model supports (there
	 * is no dedicated organizer-user relationship; brief §23's Organizer
	 * Dashboard is scoped to whoever actually created the event posts).
	 *
	 * @param int $user_id
	 * @return int[]
	 */
	public static function get_events_by_author( $user_id ) {
		if ( ! self::is_active() || ! $user_id ) {
			return array();
		}

		return get_posts(
			array(
				'post_type'      => 'mep_events',
				'author'         => $user_id,
				'post_status'    => array( 'publish', 'draft', 'pending', 'future' ),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
	}

	/**
	 * Real registration/revenue totals for one event — thin wrapper around
	 * `MPWEM_Functions::registration_stats()`, which counts uniformly from
	 * the `mep_events_attendees` records both WooCommerce and native
	 * checkout create.
	 *
	 * @param int $event_id
	 * @return array{tickets:int,revenue:float,lines:int}
	 */
	public static function get_event_stats( $event_id ) {
		if ( ! self::is_active() || ! method_exists( 'MPWEM_Functions', 'registration_stats' ) ) {
			return array( 'tickets' => 0, 'revenue' => 0.0, 'lines' => 0 );
		}
		return MPWEM_Functions::registration_stats( '', '', $event_id );
	}

	/**
	 * Aggregate real stats across every event a user authored — the data
	 * behind the Organizer Dashboard's Overview tab (brief §23). Every
	 * number here is a real sum over real attendee/order records, never a
	 * placeholder.
	 *
	 * @param int $user_id
	 * @return array{tickets:int,revenue:float,lines:int,event_count:int,upcoming_count:int}
	 */
	public static function get_organizer_stats( $user_id ) {
		$event_ids = self::get_events_by_author( $user_id );
		$totals    = array( 'tickets' => 0, 'revenue' => 0.0, 'lines' => 0, 'event_count' => count( $event_ids ), 'upcoming_count' => 0 );

		foreach ( $event_ids as $event_id ) {
			$stats               = self::get_event_stats( $event_id );
			$totals['tickets']  += $stats['tickets'];
			$totals['revenue']  += $stats['revenue'];
			$totals['lines']    += $stats['lines'];

			$timestamp = self::get_upcoming_timestamp( $event_id );
			if ( $timestamp && $timestamp > time() ) {
				++$totals['upcoming_count'];
			}
		}

		return $totals;
	}

	/**
	 * Real attendee records across every event a user authored — the data
	 * behind the Organizer Dashboard's Attendees tab (brief §23).
	 *
	 * @param int $user_id
	 * @param int $limit
	 * @return array[] {attendee_id, name, email, ticket_type, qty, event_id, event_title, order_id}
	 */
	public static function get_organizer_attendees( $user_id, $limit = 50 ) {
		$event_ids = self::get_events_by_author( $user_id );
		if ( empty( $event_ids ) ) {
			return array();
		}

		$posts = get_posts(
			array(
				'post_type'      => 'mep_events_attendees',
				'post_status'    => 'any',
				'posts_per_page' => $limit,
				'orderby'        => 'ID',
				'order'          => 'DESC',
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- organizer-facing dashboard, low traffic, not the main query.
					array(
						'key'     => 'ea_event_id',
						'value'   => $event_ids,
						'compare' => 'IN',
					),
				),
			)
		);

		$attendees = array();
		foreach ( $posts as $post ) {
			$attendees[] = array(
				'attendee_id' => $post->ID,
				'name'        => get_post_meta( $post->ID, 'ea_name', true ),
				'email'       => get_post_meta( $post->ID, 'ea_email', true ),
				'ticket_type' => get_post_meta( $post->ID, 'ea_ticket_type', true ),
				'qty'         => (int) get_post_meta( $post->ID, 'ea_ticket_qty', true ),
				'event_id'    => (int) get_post_meta( $post->ID, 'ea_event_id', true ),
				'event_title' => get_the_title( (int) get_post_meta( $post->ID, 'ea_event_id', true ) ),
				'order_id'    => (int) get_post_meta( $post->ID, 'ea_order_id', true ),
			);
		}

		return $attendees;
	}

	/**
	 * The Event Archive's combined search + filter query.
	 *
	 * `MPWEM_Query::event_query()` has no free-text search parameter (it only
	 * filters by taxonomy/city/country/state/year/status), so a keyword
	 * search is layered on top honestly rather than invented: when a search
	 * term is present, this fetches a larger unpaginated batch from the real
	 * filtered query, narrows it by title match in PHP, and paginates the
	 * narrowed result itself — so the displayed count and page links stay
	 * accurate instead of pretending the plugin's query understood `s`.
	 *
	 * @param array $args {
	 *     @type string $search   Free-text term matched against the title. Optional.
	 *     @type string $cat, $org, $tag, $city, $country, $state, $year
	 *     @type string $status   upcoming|expired|today. Default 'upcoming'.
	 *     @type string $sort     ASC|DESC. Default 'ASC'.
	 *     @type int    $per_page Default 12.
	 *     @type int    $paged    Default from the `paged`/`page` query var.
	 * }
	 * @return array{cards:array[],total:int,max_pages:int,paged:int}
	 */
	public static function search_events( $args = array() ) {
		if ( ! self::is_active() ) {
			return array( 'cards' => array(), 'total' => 0, 'max_pages' => 0, 'paged' => 1 );
		}

		$defaults = array(
			'search'   => '',
			'cat'      => '',
			'org'      => '',
			'tag'      => '',
			'city'     => '',
			'country'  => '',
			'state'    => '',
			'year'     => '',
			'status'   => 'upcoming',
			'sort'     => 'ASC',
			'per_page' => 12,
			'paged'    => 0,
		);
		$args   = wp_parse_args( $args, $defaults );
		$paged  = $args['paged'] ? absint( $args['paged'] ) : max( 1, absint( get_query_var( 'paged' ) ), absint( get_query_var( 'page' ) ) );
		$search = trim( (string) $args['search'] );

		if ( '' === $search ) {
			$query = self::query_events(
				array(
					'show'    => $args['per_page'],
					'cat'     => $args['cat'],
					'org'     => $args['org'],
					'tag'     => $args['tag'],
					'city'    => $args['city'],
					'country' => $args['country'],
					'state'   => $args['state'],
					'year'    => $args['year'],
					'status'  => $args['status'],
					'sort'    => $args['sort'],
					'paged'   => $paged,
				)
			);

			if ( ! $query ) {
				return array( 'cards' => array(), 'total' => 0, 'max_pages' => 0, 'paged' => $paged );
			}

			return array(
				'cards'     => array_map( array( __CLASS__, 'get_event_card_data' ), wp_list_pluck( $query->posts, 'ID' ) ),
				'total'     => (int) $query->found_posts,
				'max_pages' => (int) $query->max_num_pages,
				'paged'     => $paged,
			);
		}

		// Search present: fetch a large real-filtered batch, then narrow + paginate ourselves.
		$query = self::query_events(
			array(
				'show'    => 200,
				'cat'     => $args['cat'],
				'org'     => $args['org'],
				'tag'     => $args['tag'],
				'city'    => $args['city'],
				'country' => $args['country'],
				'state'   => $args['state'],
				'year'    => $args['year'],
				'status'  => $args['status'],
				'sort'    => $args['sort'],
				'paged'   => 1,
			)
		);

		if ( ! $query || empty( $query->posts ) ) {
			return array( 'cards' => array(), 'total' => 0, 'max_pages' => 0, 'paged' => $paged );
		}

		$matches = array_values(
			array_filter(
				$query->posts,
				static function ( $post ) use ( $search ) {
					return false !== stripos( $post->post_title, $search );
				}
			)
		);

		$total     = count( $matches );
		$max_pages = (int) max( 1, ceil( $total / max( 1, $args['per_page'] ) ) );
		$slice     = array_slice( $matches, ( $paged - 1 ) * $args['per_page'], $args['per_page'] );

		return array(
			'cards'     => array_map( array( __CLASS__, 'get_event_card_data' ), wp_list_pluck( $slice, 'ID' ) ),
			'total'     => $total,
			'max_pages' => $max_pages,
			'paged'     => $paged,
		);
	}
}
