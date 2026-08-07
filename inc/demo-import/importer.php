<?php
/**
 * Evently_Demo_Importer — creates the "All Events" demo content (brief §28).
 *
 * Every post/term this creates is tagged with a `_evently_demo_content`
 * postmeta/termmeta marker, so re-running the importer can detect what it
 * already created (no duplicates) and so nothing here is ever mistaken for
 * — or silently deletes — real user content (brief §30).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'Evently_Demo_Importer' ) ) {
	return;
}

/**
 * Class Evently_Demo_Importer
 */
class Evently_Demo_Importer {

	const OPTION_FLAG   = 'evently_demo_imported';
	const DEMO_META_KEY = '_evently_demo_content';

	/**
	 * Count of featured images that failed to sideload this run (e.g. the
	 * server has no outbound HTTPS/valid CA bundle) — surfaced in the
	 * import log rather than failing silently (brief §30).
	 *
	 * @var int
	 */
	private static $image_failures = 0;

	/**
	 * @return bool Whether the "All Events" demo has already been imported.
	 */
	public static function is_imported() {
		return (bool) get_option( self::OPTION_FLAG, false );
	}

	/**
	 * Run the full import. Safe to call more than once — every step checks
	 * for its own already-imported marker first.
	 *
	 * @return array{success:bool,log:string[],created:array<string,int>}
	 */
	public static function run() {
		$log     = array();
		$created = array( 'categories' => 0, 'organizers' => 0, 'events' => 0, 'posts' => 0, 'pages' => 0 );

		if ( ! evently_has_booking_plugin() ) {
			return array(
				'success' => false,
				'log'     => array( __( 'The Evently Booking plugin is not active — install/activate it before importing demo events.', 'evently' ) ),
				'created' => $created,
			);
		}

		// media_sideload_image() (used for event/article featured images below)
		// lives in admin-only files not autoloaded on admin-ajax.php requests.
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$categories = self::import_categories();
		$created['categories'] = count( $categories );
		$log[] = sprintf( /* translators: %d: count. */ __( 'Categories ready: %d', 'evently' ), $created['categories'] );

		$organizers = self::import_organizers();
		$created['organizers'] = count( $organizers );
		$log[] = sprintf( __( 'Organizers ready: %d', 'evently' ), $created['organizers'] );

		$events = self::import_events( $categories, $organizers );
		$created['events'] = count( $events );
		$log[] = sprintf( __( 'Events ready: %d', 'evently' ), $created['events'] );

		$posts = self::import_blog_posts();
		$created['posts'] = count( $posts );
		$log[] = sprintf( __( 'Blog posts ready: %d', 'evently' ), $created['posts'] );

		// Gallery attachment is a deliberate second pass over the events,
		// run only now that every event's AND every blog post's own featured
		// image has been attached/tagged above. Several demo events'
		// galleries deliberately reuse another demo item's bundled photo
		// (see sample-data.php's gallery_files lists) — running this before
		// every "owning" post's own featured image exists would let
		// get_or_create_demo_attachment() upload that shared file a second
		// time under the wrong event before the real owner claims it.
		self::apply_event_galleries( $events );

		$pages = self::import_pages();
		$created['pages'] = count( $pages );
		$log[] = sprintf( __( 'Pages ready: %d', 'evently' ), $created['pages'] );

		self::import_nav_menu( ! empty( $pages[0] ) ? $pages[0] : 0 );
		$log[] = __( 'Primary navigation menu created.', 'evently' );

		if ( self::$image_failures > 0 ) {
			$log[] = sprintf(
				/* translators: %d: number of images. */
				__( 'Note: %d demo image(s) could not be downloaded (the server could not reach the image host — check outbound HTTPS/SSL). Everything else imported normally; you can add featured images manually.', 'evently' ),
				self::$image_failures
			);
		}

		update_option( self::OPTION_FLAG, true );
		update_option( 'evently_demo_imported_at', current_time( 'mysql' ) );

		return array( 'success' => true, 'log' => $log, 'created' => $created );
	}

	/**
	 * @return int[] term_id => term_id map for the demo categories.
	 */
	private static function import_categories() {
		$labels = array( 'Music', 'Conference', 'Workshop', 'Food & Dining', 'Sports', 'Business' );
		$ids    = array();

		foreach ( $labels as $label ) {
			$existing = term_exists( $label, 'mep_cat' );
			if ( $existing ) {
				$ids[ $label ] = is_array( $existing ) ? (int) $existing['term_id'] : (int) $existing;
				continue;
			}
			$result = wp_insert_term( $label, 'mep_cat' );
			if ( ! is_wp_error( $result ) ) {
				$ids[ $label ] = (int) $result['term_id'];
			}
		}

		return $ids;
	}

	/**
	 * @return int[] organizer name => term_id map.
	 */
	private static function import_organizers() {
		$names = array( 'Evently Live', 'Future Forum', 'Studio Nine Collective', 'Taste of the World', 'Evently Tech', 'City Runners Club', 'Frame & Light', 'Founders Bangladesh' );
		$ids   = array();

		foreach ( $names as $name ) {
			$existing = term_exists( $name, 'mep_org' );
			if ( $existing ) {
				$ids[ $name ] = is_array( $existing ) ? (int) $existing['term_id'] : (int) $existing;
				continue;
			}
			$result = wp_insert_term( $name, 'mep_org' );
			if ( ! is_wp_error( $result ) ) {
				$ids[ $name ] = (int) $result['term_id'];
			}
		}

		return $ids;
	}

	/**
	 * @param array $category_ids
	 * @param array $organizer_ids
	 * @return int[] Created/found event post IDs, keyed by the demo dataset's
	 *               own 'id' (e.g. 'demo-1') rather than a plain sequential
	 *               array — apply_event_galleries() looks events up by that
	 *               key, so a single event failing to import (a skipped
	 *               index) can never desync it from a different demo event.
	 */
	private static function import_events( $category_ids, $organizer_ids ) {
		$demo_events = evently_demo_events();
		$event_ids   = array();

		foreach ( $demo_events as $demo_event ) {
			$existing = get_page_by_path( sanitize_title( $demo_event['title'] ), OBJECT, 'mep_events' );
			if ( $existing ) {
				// Self-heal: an earlier run may have created the event but failed to
				// attach its image (e.g. no outbound HTTPS at the time) — attach it
				// now if it's still missing, without touching anything else. The rest
				// of the event's content (description, dates, FAQ, timeline, gallery)
				// is refreshed unconditionally on every run — it's our own generated
				// demo content, never something a site owner hand-edits in place, so
				// "re-import" is expected to bring it up to the theme's current demo
				// dataset rather than freeze it at whichever version first created it.
				self::attach_featured_image( $existing->ID, $demo_event, $demo_event['title'] );
				self::sync_event_content( $existing->ID, $demo_event, $category_ids, $organizer_ids );
				$event_ids[ $demo_event['id'] ] = $existing->ID;
				continue;
			}

			$post_id = wp_insert_post(
				array(
					'post_type'    => 'mep_events',
					'post_title'   => $demo_event['title'],
					'post_name'    => sanitize_title( $demo_event['title'] ),
					'post_excerpt' => $demo_event['excerpt'],
					'post_content' => self::build_content_blocks( ! empty( $demo_event['description'] ) ? $demo_event['description'] : $demo_event['excerpt'] ),
					'post_status'  => 'publish',
				)
			);

			if ( is_wp_error( $post_id ) || ! $post_id ) {
				continue;
			}

			update_post_meta( $post_id, self::DEMO_META_KEY, 1 );

			// Location.
			update_post_meta( $post_id, 'mep_org_address', '0' );
			update_post_meta( $post_id, 'mep_location_venue', $demo_event['venue'] );
			update_post_meta( $post_id, 'mep_city', $demo_event['city'] );
			update_post_meta( $post_id, 'mep_country', $demo_event['country'] );
			update_post_meta( $post_id, 'mep_event_type', 'offline' );
			update_post_meta( $post_id, 'mep_reg_status', 'on' );

			// Dates/times, FAQ, timeline and gallery — see the dedicated apply_*
			// methods below, shared with the "existing event" refresh path above.
			self::apply_date_meta( $post_id, $demo_event );
			self::apply_faq_meta( $post_id, $demo_event );
			self::apply_timeline_meta( $post_id, $demo_event );

			// Ticket types — General + VIP, priced off the demo's base price.
			$base_price = max( 0, (float) $demo_event['price'] );
			$tickets    = array(
				array(
					'option_name_t'         => 'General',
					'option_details_t'      => __( 'Standard admission.', 'evently' ),
					'option_price_t'        => (string) $base_price,
					'option_qty_t'          => '150',
					'option_rsv_t'          => '0',
					'option_default_qty_t'  => '0',
					'option_qty_t_type'     => 'inputbox',
					'option_ticket_enable'  => 'yes',
					'option_ticket_mode_t'  => 'inperson',
					'option_sale_end_date'  => '',
					'option_sale_end_time'  => '',
					'option_sale_end_date_t' => '',
					'option_sale_start_date' => '',
					'option_sale_start_time' => '',
					'option_sale_start_date_t' => '',
					'option_min_qty'        => '',
					'option_max_qty'        => '',
				),
				array(
					'option_name_t'         => 'VIP',
					'option_details_t'      => __( 'Priority entry and a reserved area.', 'evently' ),
					'option_price_t'        => (string) round( $base_price * 2 + 30 ),
					'option_qty_t'          => '40',
					'option_rsv_t'          => '0',
					'option_default_qty_t'  => '0',
					'option_qty_t_type'     => 'inputbox',
					'option_ticket_enable'  => 'yes',
					'option_ticket_mode_t'  => 'inperson',
					'option_sale_end_date'  => '',
					'option_sale_end_time'  => '',
					'option_sale_end_date_t' => '',
					'option_sale_start_date' => '',
					'option_sale_start_time' => '',
					'option_sale_start_date_t' => '',
					'option_min_qty'        => '',
					'option_max_qty'        => '',
				),
			);
			update_post_meta( $post_id, 'mep_event_ticket_type', $tickets );

			// Taxonomy assignment.
			if ( isset( $category_ids[ $demo_event['category'] ] ) ) {
				wp_set_object_terms( $post_id, array( $category_ids[ $demo_event['category'] ] ), 'mep_cat' );
			}
			if ( isset( $organizer_ids[ $demo_event['organizer'] ] ) ) {
				wp_set_object_terms( $post_id, array( $organizer_ids[ $demo_event['organizer'] ] ), 'mep_org' );
			}

			// Featured image — attached once, reused on re-import via the slug check above.
			// Gallery meta is applied in a later, dedicated pass — see run()'s
			// call to apply_event_galleries() for why.
			self::attach_featured_image( $post_id, $demo_event, $demo_event['title'] );

			$event_ids[ $demo_event['id'] ] = $post_id;
		}

		return $event_ids;
	}

	/**
	 * Turns a demo event's long-form description (plain text, blank-line
	 * separated paragraphs) into Gutenberg paragraph blocks — the same
	 * shape the theme already used for the short excerpt, just with real
	 * multi-paragraph content now that descriptions run ~300–400 words.
	 *
	 * @param string $description
	 * @return string
	 */
	private static function build_content_blocks( $description ) {
		$paragraphs = preg_split( '/\r\n\r\n|\n\n/', trim( (string) $description ) );
		$content    = '';

		foreach ( $paragraphs as $paragraph ) {
			$paragraph = trim( $paragraph );
			if ( '' === $paragraph ) {
				continue;
			}
			$content .= '<!-- wp:paragraph --><p>' . esc_html( $paragraph ) . '</p><!-- /wp:paragraph -->';
		}

		return $content;
	}

	/**
	 * Writes the date/recurrence postmeta for one demo event, branching on
	 * its `date_type` (a deliberate fixed/particular/recurring mix — see
	 * docs/demo-content.md) onto the booking plugin's real 3-mode contract:
	 *
	 * - 'fixed'     → mep_enable_recurring = 'no'.  A single date, or a
	 *                 continuous multi-day span via event_end_date_offset.
	 * - 'particular'→ mep_enable_recurring = 'yes' + mep_event_more_date,
	 *                 the plugin's own repeater for extra one-off dates.
	 * - 'recurring' → mep_enable_recurring = 'everyday' (the plugin's literal
	 *                 value for its date-range-plus-interval mode) +
	 *                 mep_repeated_periods (a day-count interval, e.g. 7 for
	 *                 weekly, 30 for monthly) over event_start_date..event_end_date.
	 *
	 * @param int   $post_id
	 * @param array $demo_event
	 * @return void
	 */
	private static function apply_date_meta( $post_id, $demo_event ) {
		// Always prefer the dataset's own ISO 'start_date' — 'date_full' is a
		// display string (en-dash ranges like "Aug 24–26, 2026", or suffixes
		// like "Weekly from ..." / "(+2 more dates)") that strtotime() can
		// silently mis-parse or fail on entirely, which used to fall through
		// to a random "+10 to +90 days" placeholder date.
		if ( ! empty( $demo_event['start_date'] ) ) {
			$start = $demo_event['start_date'];
		} else {
			$timestamp = strtotime( $demo_event['date_full'] . ' ' . $demo_event['time'] );
			$start     = $timestamp ? gmdate( 'Y-m-d', $timestamp ) : gmdate( 'Y-m-d', strtotime( '+' . wp_rand( 10, 90 ) . ' days' ) );
		}
		$date_type = ! empty( $demo_event['date_type'] ) ? $demo_event['date_type'] : 'fixed';

		if ( 'recurring' === $date_type && ! empty( $demo_event['recurrence']['end_date'] ) ) {
			$end = $demo_event['recurrence']['end_date'];
		} elseif ( ! empty( $demo_event['event_end_date_offset'] ) ) {
			$end = gmdate( 'Y-m-d', strtotime( $start . ' +' . (int) $demo_event['event_end_date_offset'] . ' days' ) );
		} else {
			$end = $start;
		}

		update_post_meta( $post_id, 'event_start_date', $start );
		update_post_meta( $post_id, 'event_start_time', $demo_event['time'] );
		update_post_meta( $post_id, 'event_end_date', $end );
		update_post_meta( $post_id, 'event_end_time', $demo_event['time'] );
		update_post_meta( $post_id, 'event_start_datetime', $start . ' ' . $demo_event['time'] . ':00' );
		update_post_meta( $post_id, 'event_upcoming_datetime', $start . ' ' . $demo_event['time'] . ':00' );
		update_post_meta( $post_id, 'event_expire_datetime', $end . ' ' . $demo_event['time'] . ':00' );

		if ( 'particular' === $date_type && ! empty( $demo_event['extra_dates'] ) ) {
			update_post_meta( $post_id, 'mep_enable_recurring', 'yes' );
			$more_dates = array();
			foreach ( $demo_event['extra_dates'] as $extra ) {
				$more_dates[] = array(
					'event_more_start_date' => $extra['date'],
					'event_more_start_time' => $extra['time'],
					'event_more_end_date'   => ! empty( $extra['end_date'] ) ? $extra['end_date'] : $extra['date'],
					'event_more_end_time'   => ! empty( $extra['end_time'] ) ? $extra['end_time'] : $extra['time'],
				);
			}
			update_post_meta( $post_id, 'mep_event_more_date', $more_dates );
		} elseif ( 'recurring' === $date_type ) {
			update_post_meta( $post_id, 'mep_enable_recurring', 'everyday' );
			$period = 'monthly' === ( $demo_event['recurrence']['period'] ?? '' ) ? 30 : 7;
			update_post_meta( $post_id, 'mep_repeated_periods', $period );
			delete_post_meta( $post_id, 'mep_event_more_date' );
		} else {
			update_post_meta( $post_id, 'mep_enable_recurring', 'no' );
			delete_post_meta( $post_id, 'mep_event_more_date' );
		}
	}

	/**
	 * Writes an event-specific FAQ (brief §16) — 3 real, event-specific
	 * questions per demo event rather than the same 2 generic ones repeated
	 * across all 8.
	 *
	 * @param int   $post_id
	 * @param array $demo_event
	 * @return void
	 */
	private static function apply_faq_meta( $post_id, $demo_event ) {
		if ( empty( $demo_event['faq'] ) || ! is_array( $demo_event['faq'] ) ) {
			return;
		}

		$faq = array();
		foreach ( $demo_event['faq'] as $item ) {
			$faq[] = array(
				'mep_faq_title'   => $item['q'],
				'mep_faq_content' => $item['a'],
			);
		}
		update_post_meta( $post_id, 'mep_event_faq', $faq );
	}

	/**
	 * Writes an event's Timeline/agenda (brief's new "every event should
	 * have a Timeline" requirement) into the real `mep_event_day` meta the
	 * plugin's `mpwem_timeline` hook reads (templates/layout/timeline.php).
	 *
	 * @param int   $post_id
	 * @param array $demo_event
	 * @return void
	 */
	private static function apply_timeline_meta( $post_id, $demo_event ) {
		if ( empty( $demo_event['timeline'] ) || ! is_array( $demo_event['timeline'] ) ) {
			return;
		}

		$days = array();
		foreach ( $demo_event['timeline'] as $item ) {
			$days[] = array(
				'mep_day_title'   => $item['title'],
				'mep_day_time'    => $item['time'],
				'mep_day_content' => '<p>' . esc_html( $item['desc'] ) . '</p>',
			);
		}
		update_post_meta( $post_id, 'mep_event_day', $days );
		update_post_meta( $post_id, 'mep_timeline_status', 'on' );
	}

	/**
	 * Applies every demo event's Gallery meta in one dedicated pass, run by
	 * run() only after both import_events() and import_blog_posts() have
	 * finished — i.e. only once every demo event's AND every blog post's
	 * own featured image has been attached and tagged with
	 * `_evently_demo_source_file`. Several events' gallery_files lists
	 * deliberately reuse another demo item's bundled photo (e.g. demo-2's
	 * gallery includes demo-5's featured photo), and get_or_create_demo_attachment()
	 * can only find-and-reuse an attachment that's already tagged — running
	 * this any earlier would race demo-2's gallery build against demo-5's
	 * own featured-image attachment and upload that shared file twice.
	 *
	 * @param int[] $event_ids Keyed by demo event 'id', as returned by import_events().
	 * @return void
	 */
	private static function apply_event_galleries( $event_ids ) {
		foreach ( evently_demo_events() as $demo_event ) {
			if ( ! empty( $event_ids[ $demo_event['id'] ] ) ) {
				self::apply_gallery_meta( $event_ids[ $demo_event['id'] ], $demo_event );
			}
		}
	}

	/**
	 * Writes an event's Gallery (brief's new "every event should have a
	 * Gallery" requirement) into the real `mep_gallery_images` meta the
	 * plugin's `mpwem_custom_slider` hook reads (inc/MPWEM_Custom_Slider.php)
	 * — a flat array of attachment ID *strings*, not URLs or filenames.
	 * Reuses the theme's bundled demo photos, uploading each one once and
	 * reusing the same Media Library attachment across every event whose
	 * gallery references it (get_or_create_demo_attachment()).
	 *
	 * @param int   $post_id
	 * @param array $demo_event
	 * @return void
	 */
	private static function apply_gallery_meta( $post_id, $demo_event ) {
		if ( empty( $demo_event['gallery_files'] ) || ! is_array( $demo_event['gallery_files'] ) ) {
			return;
		}

		$ids = array();
		foreach ( $demo_event['gallery_files'] as $file ) {
			$attachment_id = self::get_or_create_demo_attachment( $file, $demo_event['title'] );
			if ( $attachment_id ) {
				$ids[] = (string) $attachment_id;
			}
		}

		if ( empty( $ids ) ) {
			return;
		}

		update_post_meta( $post_id, 'mep_gallery_images', array_values( array_unique( $ids ) ) );
		update_post_meta( $post_id, 'mep_display_slider', 'on' );
	}

	/**
	 * Finds (by the `_evently_demo_source_file` marker this method itself
	 * sets) or creates a Media Library attachment for one bundled demo
	 * image file, so 8 events' worth of overlapping gallery lists upload
	 * each unique file at most once rather than duplicating it per event.
	 *
	 * @param string $file  Filename under assets/images/demo/.
	 * @param string $title Attachment title/alt text for a newly created attachment.
	 * @return int Attachment ID, or 0 if the file couldn't be read/uploaded.
	 */
	private static function get_or_create_demo_attachment( $file, $title ) {
		$existing = get_posts(
			array(
				'post_type'      => 'attachment',
				'posts_per_page' => 1,
				'post_status'    => 'inherit',
				'meta_key'       => '_evently_demo_source_file', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- one-time import step.
				'meta_value'     => $file, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'fields'         => 'ids',
			)
		);
		if ( ! empty( $existing[0] ) ) {
			return (int) $existing[0];
		}

		$local_path = EVENTLY_DIR . 'assets/images/demo/' . $file;
		if ( ! is_readable( $local_path ) ) {
			return 0;
		}

		$bits = file_get_contents( $local_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme asset, not a remote/user-supplied path.
		if ( false === $bits ) {
			return 0;
		}

		$upload = wp_upload_bits( $file, null, $bits );
		if ( ! empty( $upload['error'] ) ) {
			return 0;
		}

		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => $upload['type'],
				'post_title'     => $title,
				'post_status'    => 'inherit',
			),
			$upload['file']
		);
		if ( ! $attachment_id || is_wp_error( $attachment_id ) ) {
			return 0;
		}

		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title );
		update_post_meta( $attachment_id, '_evently_demo_source_file', $file );
		$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
		wp_update_attachment_metadata( $attachment_id, $metadata );

		return $attachment_id;
	}

	/**
	 * Refreshes everything about a previously-imported demo event except its
	 * featured image (which stays protected by attach_featured_image()'s
	 * has_post_thumbnail() check) so that re-running the importer after a
	 * theme update brings existing demo events' content, dates, FAQ,
	 * timeline and gallery up to date with the current dataset — this is
	 * our own generated demo content, not something re-import promises to
	 * freeze in place.
	 *
	 * @param int   $post_id
	 * @param array $demo_event
	 * @param array $category_ids
	 * @param array $organizer_ids
	 * @return void
	 */
	private static function sync_event_content( $post_id, $demo_event, $category_ids, $organizer_ids ) {
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_excerpt' => $demo_event['excerpt'],
				'post_content' => self::build_content_blocks( ! empty( $demo_event['description'] ) ? $demo_event['description'] : $demo_event['excerpt'] ),
			)
		);

		update_post_meta( $post_id, 'mep_location_venue', $demo_event['venue'] );
		update_post_meta( $post_id, 'mep_city', $demo_event['city'] );
		update_post_meta( $post_id, 'mep_country', $demo_event['country'] );

		self::apply_date_meta( $post_id, $demo_event );
		self::apply_faq_meta( $post_id, $demo_event );
		self::apply_timeline_meta( $post_id, $demo_event );
		// Gallery meta is applied in a later, dedicated pass — see run()'s
		// call to apply_event_galleries() for why.

		if ( isset( $category_ids[ $demo_event['category'] ] ) ) {
			wp_set_object_terms( $post_id, array( $category_ids[ $demo_event['category'] ] ), 'mep_cat' );
		}
		if ( isset( $organizer_ids[ $demo_event['organizer'] ] ) ) {
			wp_set_object_terms( $post_id, array( $organizer_ids[ $demo_event['organizer'] ] ), 'mep_org' );
		}
	}

	/**
	 * @return int[] Created/found blog post IDs.
	 */
	private static function import_blog_posts() {
		$articles   = evently_demo_journal_articles();
		$post_ids   = array();
		$paragraphs = array(
			__( "Great events don't happen by accident — they're the result of careful planning, a clear sense of who you're creating the experience for, and a healthy amount of flexibility when things don't go exactly to plan.", 'evently' ),
			__( 'Start with the outcome you want attendees to leave with, then work backwards to the logistics: venue, schedule, ticketing and promotion all follow from that single decision.', 'evently' ),
		);

		foreach ( $articles as $article ) {
			$existing = get_posts(
				array(
					'post_type'      => 'post',
					'title'          => $article['title'],
					'posts_per_page' => 1,
					'post_status'    => 'any',
				)
			);
			if ( ! empty( $existing ) ) {
				self::attach_featured_image( $existing[0]->ID, $article, $article['title'] );
				$post_ids[] = $existing[0]->ID;
				continue;
			}

			$content = '';
			foreach ( $paragraphs as $paragraph ) {
				$content .= '<!-- wp:paragraph --><p>' . esc_html( $paragraph ) . '</p><!-- /wp:paragraph -->';
			}

			$post_id = wp_insert_post(
				array(
					'post_type'    => 'post',
					'post_title'   => $article['title'],
					'post_content' => $content,
					'post_status'  => 'publish',
					'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( $article['date'] ) ),
				)
			);

			if ( is_wp_error( $post_id ) || ! $post_id ) {
				continue;
			}

			update_post_meta( $post_id, self::DEMO_META_KEY, 1 );

			self::attach_featured_image( $post_id, $article, $article['title'] );

			$post_ids[] = $post_id;
		}

		return $post_ids;
	}

	/**
	 * Attach a demo item's featured image, preferring the real photo bundled
	 * with the theme (assets/images/demo/ — no network round-trip, so it
	 * works even on servers with a broken/missing outbound-HTTPS CA bundle,
	 * which is what caused these to silently fail on some local dev stacks).
	 * Falls back to sideloading the original hotlinked URL only if the
	 * bundled file is missing for some reason.
	 *
	 * @param int    $post_id
	 * @param array  $demo_item Must contain 'image_file' and/or 'image_url'/'image'.
	 * @param string $title     Used as the attachment title/alt text.
	 * @return void
	 */
	private static function attach_featured_image( $post_id, $demo_item, $title ) {
		if ( has_post_thumbnail( $post_id ) ) {
			// Tag the existing attachment for gallery reuse if an older import
			// created it before get_or_create_demo_attachment() existed — the
			// plugin's own slider always prepends the post's featured-image ID
			// to mep_gallery_images, so without this tag the same photo could
			// get uploaded a second time and appear twice in the gallery.
			$existing_id = get_post_thumbnail_id( $post_id );
			if ( $existing_id && ! empty( $demo_item['image_file'] ) && ! get_post_meta( $existing_id, '_evently_demo_source_file', true ) ) {
				update_post_meta( $existing_id, '_evently_demo_source_file', $demo_item['image_file'] );
			}
			return; // Already has one (e.g. a site owner replaced it) — never overwrite.
		}

		$local_path = ! empty( $demo_item['image_file'] ) ? EVENTLY_DIR . 'assets/images/demo/' . $demo_item['image_file'] : '';

		if ( $local_path && is_readable( $local_path ) ) {
			$bits = file_get_contents( $local_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme asset, not a remote/user-supplied path.
			if ( false !== $bits ) {
				$upload = wp_upload_bits( $demo_item['image_file'], null, $bits );
				if ( empty( $upload['error'] ) ) {
					$attachment_id = wp_insert_attachment(
						array(
							'post_mime_type' => $upload['type'],
							'post_title'     => $title,
							'post_status'    => 'inherit',
						),
						$upload['file'],
						$post_id
					);
					if ( $attachment_id && ! is_wp_error( $attachment_id ) ) {
						update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title );
						update_post_meta( $attachment_id, '_evently_demo_source_file', $demo_item['image_file'] );
						$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
						wp_update_attachment_metadata( $attachment_id, $metadata );
						set_post_thumbnail( $post_id, $attachment_id );
						return;
					}
				}
			}
		}

		// No usable bundled file — fall back to sideloading the original URL
		// (works on hosts with normal outbound HTTPS; reported honestly if it fails).
		$remote_url = ! empty( $demo_item['image_url'] ) ? $demo_item['image_url'] : ( ! empty( $demo_item['image'] ) ? $demo_item['image'] : '' );
		if ( $remote_url && function_exists( 'media_sideload_image' ) ) {
			$attachment_id = media_sideload_image( $remote_url, $post_id, $title, 'id' );
			if ( ! is_wp_error( $attachment_id ) ) {
				set_post_thumbnail( $post_id, $attachment_id );
				return;
			}
		}

		++self::$image_failures;
	}

	/**
	 * Creates (or reuses) the Events archive + Organizer Dashboard pages.
	 *
	 * @return int[] Page IDs.
	 */
	private static function import_pages() {
		$pages = array(
			array(
				'title'    => __( 'Events', 'evently' ),
				'template' => 'page-templates/event-archive.php',
			),
			array(
				'title'    => __( 'Organizer Dashboard', 'evently' ),
				'template' => 'page-templates/organizer-dashboard.php',
			),
		);

		$ids = array();

		foreach ( $pages as $page ) {
			$existing = get_posts(
				array(
					'post_type'      => 'page',
					'posts_per_page' => 1,
					'post_status'    => 'any',
					'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- one-time import step.
					'meta_value'     => $page['template'], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				)
			);

			if ( ! empty( $existing ) ) {
				$ids[] = $existing[0]->ID;
				continue;
			}

			$post_id = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_title'  => $page['title'],
					'post_status' => 'publish',
				)
			);

			if ( is_wp_error( $post_id ) || ! $post_id ) {
				continue;
			}

			update_post_meta( $post_id, '_wp_page_template', $page['template'] );
			update_post_meta( $post_id, self::DEMO_META_KEY, 1 );
			$ids[] = $post_id;
		}

		return $ids;
	}

	/**
	 * Creates a primary navigation menu matching the header's default links,
	 * only if the "primary" location has no menu assigned yet (never
	 * replaces a site owner's existing menu).
	 *
	 * The Events item links to the actual Page object (menu-item-type
	 * `post_type`) rather than a frozen URL string, so it keeps resolving
	 * correctly even if that page's slug changes later — a plain custom
	 * link would go stale the moment someone edits the page's permalink.
	 *
	 * @param int $events_page_id Post ID of the Events page, if one was just created/found.
	 * @return void
	 */
	private static function import_nav_menu( $events_page_id = 0 ) {
		$locations = get_theme_mod( 'nav_menu_locations' );
		if ( ! empty( $locations['primary'] ) ) {
			return; // A menu is already assigned — leave it alone.
		}

		$menu_name = __( 'Evently Primary Menu', 'evently' );
		$menu      = wp_get_nav_menu_object( $menu_name );
		if ( ! $menu ) {
			$menu_id = wp_create_nav_menu( $menu_name );
		} else {
			$menu_id = $menu->term_id;
		}

		if ( is_wp_error( $menu_id ) ) {
			return;
		}

		if ( $events_page_id ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => __( 'Events', 'evently' ),
					'menu-item-object-id' => $events_page_id,
					'menu-item-object'    => 'page',
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		} else {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'  => __( 'Events', 'evently' ),
					'menu-item-url'    => evently_get_events_page_url(),
					'menu-item-status' => 'publish',
				)
			);
		}

		$custom_items = array(
			__( 'Categories', 'evently' ) => home_url( '/#evently-categories' ),
			__( 'Venues', 'evently' )     => home_url( '/' ),
			__( 'Organizers', 'evently' ) => home_url( '/' ),
		);

		foreach ( $custom_items as $label => $url ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'  => $label,
					'menu-item-url'    => $url,
					'menu-item-status' => 'publish',
				)
			);
		}

		$locations            = (array) $locations;
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}
