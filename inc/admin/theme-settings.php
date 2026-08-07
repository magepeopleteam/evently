<?php
/**
 * Evently → Theme Settings (brief §31/§32). One option (`evently_settings`,
 * an associative array) backs every field — read anywhere via
 * evently_get_setting( $key, $default ) (inc/helpers.php). Every setting
 * registered here is either read by a real template already, or is a
 * genuine WordPress Settings API field with proper sanitization — nothing
 * is decorative.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field registry: key => {label, section, type, default, description, placeholder}.
 * `type` drives both the sanitizer and which control renders. `placeholder`
 * is shown greyed-out inside an empty field so an admin can see at a glance
 * what currently displays on the live site (the theme's bundled demo
 * content) without having to open the homepage in another tab first.
 *
 * @return array
 */
function evently_get_settings_fields() {
	// Pulled once so every placeholder below is guaranteed to match what the
	// homepage templates actually fall back to — never a hand-typed copy that
	// can drift out of sync with inc/demo-import/sample-data.php.
	$demo_categories    = evently_demo_categories();
	$demo_stats         = evently_demo_stats();
	$demo_testimonials  = evently_demo_testimonials();
	$demo_dash_stats    = evently_demo_dashboard_stats();
	$demo_ticket_event  = evently_demo_events()[0];

	return array(
		// General.
		'create_event_url'        => array( 'label' => __( 'Create Event URL', 'evently' ), 'section' => 'general', 'type' => 'url', 'default' => '', 'description' => __( 'Where the header/footer "Create Event" buttons link. Leave empty to use the admin new-event screen.', 'evently' ), 'placeholder' => admin_url( 'post-new.php?post_type=mep_events' ) ),
		'events_page_id'          => array( 'label' => __( 'Events Page', 'evently' ), 'section' => 'general', 'type' => 'page', 'default' => 0, 'description' => __( 'The page using the "Evently — Event Archive" template. Auto-detected if left unset.', 'evently' ) ),

		// Branding / Header.
		'hero_image'              => array( 'label' => __( 'Hero Image URL', 'evently' ), 'section' => 'header', 'type' => 'url', 'default' => '', 'description' => __( 'Homepage hero photograph.', 'evently' ), 'placeholder' => evently_demo_image_url( array( 'image_file' => 'hero-concert-crowd.jpg' ) ) ),
		'hero_live_note'          => array( 'label' => __( 'Hero Live Note', 'evently' ), 'section' => 'header', 'type' => 'text', 'default' => '', 'description' => __( 'e.g. "2,840 tickets sold today".', 'evently' ), 'placeholder' => __( '2,840 tickets sold today', 'evently' ) ),

		// Colors.
		'color_primary'           => array( 'label' => __( 'Primary Accent', 'evently' ), 'section' => 'colors', 'type' => 'color', 'default' => '#6C5CE7' ),
		'color_orange'            => array( 'label' => __( 'Secondary Accent', 'evently' ), 'section' => 'colors', 'type' => 'color', 'default' => '#FF7657' ),
		'color_dark'              => array( 'label' => __( 'Dark', 'evently' ), 'section' => 'colors', 'type' => 'color', 'default' => '#0B0B0D' ),

		// Events / Archive.
		'default_city'            => array( 'label' => __( 'Default City', 'evently' ), 'section' => 'events', 'type' => 'text', 'default' => __( 'Dhaka', 'evently' ) ),
		'archive_columns_per_page' => array( 'label' => __( 'Events per page', 'evently' ), 'section' => 'archive', 'type' => 'number', 'default' => 12 ),
		'archive_default_view'    => array( 'label' => __( 'Default archive view', 'evently' ), 'section' => 'archive', 'type' => 'select', 'default' => 'grid', 'options' => array( 'grid' => __( 'Grid', 'evently' ), 'list' => __( 'List', 'evently' ) ) ),
		'show_price'              => array( 'label' => __( 'Show price on cards', 'evently' ), 'section' => 'events', 'type' => 'checkbox', 'default' => 1 ),
		'show_location'           => array( 'label' => __( 'Show location on cards', 'evently' ), 'section' => 'events', 'type' => 'checkbox', 'default' => 1 ),
		'show_favorite'           => array( 'label' => __( 'Show favorite button on cards', 'evently' ), 'section' => 'events', 'type' => 'checkbox', 'default' => 1 ),
		'show_rating'             => array( 'label' => __( 'Show rating on featured cards', 'evently' ), 'section' => 'events', 'type' => 'checkbox', 'default' => 1 ),

		// Single event.
		'single_event_template'   => array(
			'label'       => __( 'Event details page', 'evently' ),
			'section'     => 'single_event',
			'type'        => 'select',
			'default'     => 'theme',
			'options'     => array(
				'theme'  => __( 'Theme (Evently design)', 'evently' ),
				'plugin' => __( 'Plugin (Event Booking Manager)', 'evently' ),
			),
			'description' => __( 'Choose whether single event pages use Evently\'s design or the Event Booking Manager plugin\'s own details templates (including the layout selected in plugin settings).', 'evently' ),
		),
		'show_related_events'     => array( 'label' => __( 'Show related events', 'evently' ), 'section' => 'single_event', 'type' => 'checkbox', 'default' => 1, 'description' => __( 'Only applies when Event details page is set to Theme.', 'evently' ) ),

		// Featured event (homepage).
		'featured_event_title'    => array( 'label' => __( 'Featured Event Title', 'evently' ), 'section' => 'events', 'type' => 'text', 'default' => '', 'placeholder' => __( 'Future Music Festival', 'evently' ) ),
		'featured_event_image'   => array( 'label' => __( 'Featured Event Image URL', 'evently' ), 'section' => 'events', 'type' => 'url', 'default' => '', 'placeholder' => evently_demo_image_url( array( 'image_file' => 'featured-music-festival.jpg' ) ) ),
		'featured_event_date'     => array( 'label' => __( 'Featured Event Date', 'evently' ), 'section' => 'events', 'type' => 'text', 'default' => '', 'placeholder' => __( 'August 24–26, 2026', 'evently' ) ),
		'featured_event_location' => array( 'label' => __( 'Featured Event Location', 'evently' ), 'section' => 'events', 'type' => 'text', 'default' => '', 'placeholder' => __( 'Dhaka, Bangladesh', 'evently' ) ),
		'featured_event_note'     => array( 'label' => __( 'Featured Event Note', 'evently' ), 'section' => 'events', 'type' => 'text', 'default' => '', 'placeholder' => __( '20,000+ attendees expected', 'evently' ) ),

		// Categories bento grid (homepage). Every field defaults to '' — the
		// template falls back to the theme's bundled demo copy/photo per item
		// (see evently_get_setting()'s empty-string-is-unset behavior). The
		// placeholder shown in each empty field is that same bundled photo/label.
		'category_1_label'        => array( 'label' => __( 'Category 1 — Label', 'evently' ), 'section' => 'categories', 'type' => 'text', 'default' => '', 'placeholder' => $demo_categories[0]['label'] ),
		'category_1_image'        => array( 'label' => __( 'Category 1 — Image URL', 'evently' ), 'section' => 'categories', 'type' => 'url', 'default' => '', 'placeholder' => evently_demo_image_url( $demo_categories[0] ) ),
		'category_2_label'        => array( 'label' => __( 'Category 2 — Label', 'evently' ), 'section' => 'categories', 'type' => 'text', 'default' => '', 'placeholder' => $demo_categories[1]['label'] ),
		'category_2_image'        => array( 'label' => __( 'Category 2 — Image URL', 'evently' ), 'section' => 'categories', 'type' => 'url', 'default' => '', 'placeholder' => evently_demo_image_url( $demo_categories[1] ) ),
		'category_3_label'        => array( 'label' => __( 'Category 3 — Label', 'evently' ), 'section' => 'categories', 'type' => 'text', 'default' => '', 'placeholder' => $demo_categories[2]['label'] ),
		'category_3_image'        => array( 'label' => __( 'Category 3 — Image URL', 'evently' ), 'section' => 'categories', 'type' => 'url', 'default' => '', 'placeholder' => evently_demo_image_url( $demo_categories[2] ) ),
		'category_4_label'        => array( 'label' => __( 'Category 4 — Label', 'evently' ), 'section' => 'categories', 'type' => 'text', 'default' => '', 'placeholder' => $demo_categories[3]['label'] ),
		'category_4_image'        => array( 'label' => __( 'Category 4 — Image URL', 'evently' ), 'section' => 'categories', 'type' => 'url', 'default' => '', 'placeholder' => evently_demo_image_url( $demo_categories[3] ) ),
		'category_5_label'        => array( 'label' => __( 'Category 5 — Label', 'evently' ), 'section' => 'categories', 'type' => 'text', 'default' => '', 'placeholder' => $demo_categories[4]['label'] ),
		'category_5_image'        => array( 'label' => __( 'Category 5 — Image URL', 'evently' ), 'section' => 'categories', 'type' => 'url', 'default' => '', 'placeholder' => evently_demo_image_url( $demo_categories[4] ) ),
		'category_6_label'        => array( 'label' => __( 'Category 6 — Label', 'evently' ), 'section' => 'categories', 'type' => 'text', 'default' => '', 'placeholder' => $demo_categories[5]['label'] ),
		'category_6_image'        => array( 'label' => __( 'Category 6 — Image URL', 'evently' ), 'section' => 'categories', 'type' => 'url', 'default' => '', 'placeholder' => evently_demo_image_url( $demo_categories[5] ) ),

		// Event Calendar (homepage). Only the month label is editable — the
		// actual day-by-day event list is curated demo data (see calendar.php's
		// own doc comment); wiring that up needs real per-day event data, not
		// a text field.
		'calendar_month_label'    => array( 'label' => __( 'Calendar — Month Label', 'evently' ), 'section' => 'calendar', 'type' => 'text', 'default' => '', 'placeholder' => __( 'August 2026', 'evently' ) ),

		// How It Works (homepage 3-step process).
		'step_1_label'            => array( 'label' => __( 'Step 1 — Title', 'evently' ), 'section' => 'how_it_works', 'type' => 'text', 'default' => '', 'placeholder' => __( 'Discover', 'evently' ) ),
		'step_1_desc'              => array( 'label' => __( 'Step 1 — Description', 'evently' ), 'section' => 'how_it_works', 'type' => 'textarea', 'default' => '', 'placeholder' => __( 'Find an event you love from thousands of curated experiences worldwide.', 'evently' ) ),
		'step_2_label'            => array( 'label' => __( 'Step 2 — Title', 'evently' ), 'section' => 'how_it_works', 'type' => 'text', 'default' => '', 'placeholder' => __( 'Book', 'evently' ) ),
		'step_2_desc'              => array( 'label' => __( 'Step 2 — Description', 'evently' ), 'section' => 'how_it_works', 'type' => 'textarea', 'default' => '', 'placeholder' => __( 'Choose your ticket and pay securely. Get instant confirmation.', 'evently' ) ),
		'step_3_label'            => array( 'label' => __( 'Step 3 — Title', 'evently' ), 'section' => 'how_it_works', 'type' => 'text', 'default' => '', 'placeholder' => __( 'Enjoy', 'evently' ) ),
		'step_3_desc'              => array( 'label' => __( 'Step 3 — Description', 'evently' ), 'section' => 'how_it_works', 'type' => 'textarea', 'default' => '', 'placeholder' => __( 'Receive your digital ticket and enjoy the event worry-free.', 'evently' ) ),

		// Stats strip (also feeds the hero's 3-up teaser stats).
		'stat_1_value'             => array( 'label' => __( 'Stat 1 — Value', 'evently' ), 'section' => 'stats', 'type' => 'text', 'default' => '', 'placeholder' => $demo_stats[0]['value'] ),
		'stat_1_label'             => array( 'label' => __( 'Stat 1 — Label', 'evently' ), 'section' => 'stats', 'type' => 'text', 'default' => '', 'placeholder' => $demo_stats[0]['label'] ),
		'stat_2_value'             => array( 'label' => __( 'Stat 2 — Value', 'evently' ), 'section' => 'stats', 'type' => 'text', 'default' => '', 'placeholder' => $demo_stats[1]['value'] ),
		'stat_2_label'             => array( 'label' => __( 'Stat 2 — Label', 'evently' ), 'section' => 'stats', 'type' => 'text', 'default' => '', 'placeholder' => $demo_stats[1]['label'] ),
		'stat_3_value'             => array( 'label' => __( 'Stat 3 — Value', 'evently' ), 'section' => 'stats', 'type' => 'text', 'default' => '', 'placeholder' => $demo_stats[2]['value'] ),
		'stat_3_label'             => array( 'label' => __( 'Stat 3 — Label', 'evently' ), 'section' => 'stats', 'type' => 'text', 'default' => '', 'placeholder' => $demo_stats[2]['label'], 'description' => __( 'This 3rd stat is shown only in the Stats section further down the page, not in the hero teaser above the fold.', 'evently' ) ),
		'stat_4_value'             => array( 'label' => __( 'Stat 4 — Value', 'evently' ), 'section' => 'stats', 'type' => 'text', 'default' => '', 'placeholder' => $demo_stats[3]['value'] ),
		'stat_4_label'             => array( 'label' => __( 'Stat 4 — Label', 'evently' ), 'section' => 'stats', 'type' => 'text', 'default' => '', 'placeholder' => $demo_stats[3]['label'] ),

		// Testimonials (3 cards).
		'testimonial_1_name'       => array( 'label' => __( 'Testimonial 1 — Name', 'evently' ), 'section' => 'testimonials', 'type' => 'text', 'default' => '', 'placeholder' => $demo_testimonials[0]['name'] ),
		'testimonial_1_role'       => array( 'label' => __( 'Testimonial 1 — Role', 'evently' ), 'section' => 'testimonials', 'type' => 'text', 'default' => '', 'placeholder' => $demo_testimonials[0]['role'] ),
		'testimonial_1_text'       => array( 'label' => __( 'Testimonial 1 — Quote', 'evently' ), 'section' => 'testimonials', 'type' => 'textarea', 'default' => '', 'placeholder' => $demo_testimonials[0]['text'] ),
		'testimonial_1_stars'      => array( 'label' => __( 'Testimonial 1 — Stars (1–5)', 'evently' ), 'section' => 'testimonials', 'type' => 'text', 'default' => '', 'placeholder' => (string) $demo_testimonials[0]['stars'] ),
		'testimonial_1_initials'   => array( 'label' => __( 'Testimonial 1 — Avatar Initials', 'evently' ), 'section' => 'testimonials', 'type' => 'text', 'default' => '', 'placeholder' => $demo_testimonials[0]['initials'] ),
		'testimonial_1_color'      => array( 'label' => __( 'Testimonial 1 — Avatar Color', 'evently' ), 'section' => 'testimonials', 'type' => 'color', 'default' => '#6C5CE7' ),
		'testimonial_2_name'       => array( 'label' => __( 'Testimonial 2 — Name', 'evently' ), 'section' => 'testimonials', 'type' => 'text', 'default' => '', 'placeholder' => $demo_testimonials[1]['name'] ),
		'testimonial_2_role'       => array( 'label' => __( 'Testimonial 2 — Role', 'evently' ), 'section' => 'testimonials', 'type' => 'text', 'default' => '', 'placeholder' => $demo_testimonials[1]['role'] ),
		'testimonial_2_text'       => array( 'label' => __( 'Testimonial 2 — Quote', 'evently' ), 'section' => 'testimonials', 'type' => 'textarea', 'default' => '', 'placeholder' => $demo_testimonials[1]['text'] ),
		'testimonial_2_stars'      => array( 'label' => __( 'Testimonial 2 — Stars (1–5)', 'evently' ), 'section' => 'testimonials', 'type' => 'text', 'default' => '', 'placeholder' => (string) $demo_testimonials[1]['stars'] ),
		'testimonial_2_initials'   => array( 'label' => __( 'Testimonial 2 — Avatar Initials', 'evently' ), 'section' => 'testimonials', 'type' => 'text', 'default' => '', 'placeholder' => $demo_testimonials[1]['initials'] ),
		'testimonial_2_color'      => array( 'label' => __( 'Testimonial 2 — Avatar Color', 'evently' ), 'section' => 'testimonials', 'type' => 'color', 'default' => '#FF7657' ),
		'testimonial_3_name'       => array( 'label' => __( 'Testimonial 3 — Name', 'evently' ), 'section' => 'testimonials', 'type' => 'text', 'default' => '', 'placeholder' => $demo_testimonials[2]['name'] ),
		'testimonial_3_role'       => array( 'label' => __( 'Testimonial 3 — Role', 'evently' ), 'section' => 'testimonials', 'type' => 'text', 'default' => '', 'placeholder' => $demo_testimonials[2]['role'] ),
		'testimonial_3_text'       => array( 'label' => __( 'Testimonial 3 — Quote', 'evently' ), 'section' => 'testimonials', 'type' => 'textarea', 'default' => '', 'placeholder' => $demo_testimonials[2]['text'] ),
		'testimonial_3_stars'      => array( 'label' => __( 'Testimonial 3 — Stars (1–5)', 'evently' ), 'section' => 'testimonials', 'type' => 'text', 'default' => '', 'placeholder' => (string) $demo_testimonials[2]['stars'] ),
		'testimonial_3_initials'   => array( 'label' => __( 'Testimonial 3 — Avatar Initials', 'evently' ), 'section' => 'testimonials', 'type' => 'text', 'default' => '', 'placeholder' => $demo_testimonials[2]['initials'] ),
		'testimonial_3_color'      => array( 'label' => __( 'Testimonial 3 — Avatar Color', 'evently' ), 'section' => 'testimonials', 'type' => 'color', 'default' => '#16A34A' ),

		// Digital ticket showcase (single illustrative sample ticket).
		'ticket_event_title'       => array( 'label' => __( 'Ticket — Event Title', 'evently' ), 'section' => 'digital_ticket', 'type' => 'text', 'default' => '', 'placeholder' => $demo_ticket_event['title'] ),
		'ticket_event_date'        => array( 'label' => __( 'Ticket — Event Date', 'evently' ), 'section' => 'digital_ticket', 'type' => 'text', 'default' => '', 'placeholder' => $demo_ticket_event['date_label'] ),
		'ticket_event_city'        => array( 'label' => __( 'Ticket — City', 'evently' ), 'section' => 'digital_ticket', 'type' => 'text', 'default' => '', 'placeholder' => $demo_ticket_event['city'] ),
		'ticket_type'              => array( 'label' => __( 'Ticket — Type', 'evently' ), 'section' => 'digital_ticket', 'type' => 'text', 'default' => '', 'placeholder' => __( 'VIP PASS', 'evently' ) ),
		'ticket_entry_time'        => array( 'label' => __( 'Ticket — Entry Time', 'evently' ), 'section' => 'digital_ticket', 'type' => 'text', 'default' => '', 'placeholder' => __( 'ENTRY 06:30 PM', 'evently' ) ),

		// Organizer CTA dashboard mockup (4 illustrative stats, labeled "Preview" on the page).
		'dash_stat_1_label'        => array( 'label' => __( 'Dashboard Stat 1 — Label', 'evently' ), 'section' => 'organizer_dashboard', 'type' => 'text', 'default' => '', 'placeholder' => $demo_dash_stats[0]['label'] ),
		'dash_stat_1_value'        => array( 'label' => __( 'Dashboard Stat 1 — Value', 'evently' ), 'section' => 'organizer_dashboard', 'type' => 'text', 'default' => '', 'placeholder' => $demo_dash_stats[0]['value'] ),
		'dash_stat_1_change'       => array( 'label' => __( 'Dashboard Stat 1 — Change', 'evently' ), 'section' => 'organizer_dashboard', 'type' => 'text', 'default' => '', 'placeholder' => $demo_dash_stats[0]['change'] ),
		'dash_stat_2_label'        => array( 'label' => __( 'Dashboard Stat 2 — Label', 'evently' ), 'section' => 'organizer_dashboard', 'type' => 'text', 'default' => '', 'placeholder' => $demo_dash_stats[1]['label'] ),
		'dash_stat_2_value'        => array( 'label' => __( 'Dashboard Stat 2 — Value', 'evently' ), 'section' => 'organizer_dashboard', 'type' => 'text', 'default' => '', 'placeholder' => $demo_dash_stats[1]['value'] ),
		'dash_stat_2_change'       => array( 'label' => __( 'Dashboard Stat 2 — Change', 'evently' ), 'section' => 'organizer_dashboard', 'type' => 'text', 'default' => '', 'placeholder' => $demo_dash_stats[1]['change'] ),
		'dash_stat_3_label'        => array( 'label' => __( 'Dashboard Stat 3 — Label', 'evently' ), 'section' => 'organizer_dashboard', 'type' => 'text', 'default' => '', 'placeholder' => $demo_dash_stats[2]['label'] ),
		'dash_stat_3_value'        => array( 'label' => __( 'Dashboard Stat 3 — Value', 'evently' ), 'section' => 'organizer_dashboard', 'type' => 'text', 'default' => '', 'placeholder' => $demo_dash_stats[2]['value'] ),
		'dash_stat_3_change'       => array( 'label' => __( 'Dashboard Stat 3 — Change', 'evently' ), 'section' => 'organizer_dashboard', 'type' => 'text', 'default' => '', 'placeholder' => $demo_dash_stats[2]['change'] ),
		'dash_stat_4_label'        => array( 'label' => __( 'Dashboard Stat 4 — Label', 'evently' ), 'section' => 'organizer_dashboard', 'type' => 'text', 'default' => '', 'placeholder' => $demo_dash_stats[3]['label'] ),
		'dash_stat_4_value'        => array( 'label' => __( 'Dashboard Stat 4 — Value', 'evently' ), 'section' => 'organizer_dashboard', 'type' => 'text', 'default' => '', 'placeholder' => $demo_dash_stats[3]['value'] ),
		'dash_stat_4_change'       => array( 'label' => __( 'Dashboard Stat 4 — Change', 'evently' ), 'section' => 'organizer_dashboard', 'type' => 'text', 'default' => '', 'placeholder' => $demo_dash_stats[3]['change'] ),

		// Final CTA (last section on the homepage).
		'final_cta_title'          => array( 'label' => __( 'Final CTA — Title', 'evently' ), 'section' => 'final_cta', 'type' => 'text', 'default' => '', 'placeholder' => __( 'Your next great experience is waiting.', 'evently' ) ),
		'final_cta_subtitle'       => array( 'label' => __( 'Final CTA — Subtitle', 'evently' ), 'section' => 'final_cta', 'type' => 'textarea', 'default' => '', 'placeholder' => __( 'Discover thousands of events, experiences and unforgettable moments.', 'evently' ) ),
		'final_cta_button_text'    => array( 'label' => __( 'Final CTA — Button Text', 'evently' ), 'section' => 'final_cta', 'type' => 'text', 'default' => '', 'placeholder' => __( 'Explore Events', 'evently' ) ),
		'final_cta_button_url'     => array( 'label' => __( 'Final CTA — Button Link', 'evently' ), 'section' => 'final_cta', 'type' => 'url', 'default' => '', 'placeholder' => evently_get_events_page_url() ),

		// Footer.
		'footer_tagline'          => array( 'label' => __( 'Footer Tagline', 'evently' ), 'section' => 'footer', 'type' => 'textarea', 'default' => '', 'placeholder' => __( 'Discover experiences. Create memories.', 'evently' ) ),

		// Social.
		'social_instagram'        => array( 'label' => __( 'Instagram URL', 'evently' ), 'section' => 'social', 'type' => 'url', 'default' => '', 'placeholder' => 'https://instagram.com/yourhandle' ),
		'social_facebook'         => array( 'label' => __( 'Facebook URL', 'evently' ), 'section' => 'social', 'type' => 'url', 'default' => '', 'placeholder' => 'https://facebook.com/yourpage' ),
		'social_x'                => array( 'label' => __( 'X (Twitter) URL', 'evently' ), 'section' => 'social', 'type' => 'url', 'default' => '', 'placeholder' => 'https://x.com/yourhandle' ),
		'social_youtube'          => array( 'label' => __( 'YouTube URL', 'evently' ), 'section' => 'social', 'type' => 'url', 'default' => '', 'placeholder' => 'https://youtube.com/@yourchannel' ),

		// Performance.
		'lazy_load_images'        => array( 'label' => __( 'Lazy-load images', 'evently' ), 'section' => 'performance', 'type' => 'checkbox', 'default' => 1 ),
	);
}

/**
 * @return array<string,string> section slug => label.
 */
function evently_get_settings_sections() {
	return array(
		'general'      => __( 'General', 'evently' ),
		'header'       => __( 'Header', 'evently' ),
		'colors'       => __( 'Colors', 'evently' ),
		'events'       => __( 'Events', 'evently' ),
		'archive'      => __( 'Archive', 'evently' ),
		'single_event' => __( 'Single Event', 'evently' ),
		'categories'   => __( 'Homepage: Categories', 'evently' ),
		'calendar'     => __( 'Homepage: Calendar', 'evently' ),
		'how_it_works' => __( 'Homepage: How It Works', 'evently' ),
		'stats'        => __( 'Homepage: Stats', 'evently' ),
		'testimonials' => __( 'Homepage: Testimonials', 'evently' ),
		'digital_ticket' => __( 'Homepage: Digital Ticket', 'evently' ),
		'organizer_dashboard' => __( 'Homepage: Organizer Dashboard', 'evently' ),
		'final_cta'    => __( 'Homepage: Final CTA', 'evently' ),
		'footer'       => __( 'Footer', 'evently' ),
		'social'       => __( 'Social', 'evently' ),
		'performance'  => __( 'Performance', 'evently' ),
	);
}

/**
 * Sanitize the whole `evently_settings` array on save.
 *
 * @param array $input Raw POSTed values.
 * @return array
 */
function evently_sanitize_settings( $input ) {
	$clean = array();
	$input = is_array( $input ) ? $input : array();

	foreach ( evently_get_settings_fields() as $key => $field ) {
		$raw = isset( $input[ $key ] ) ? $input[ $key ] : null;

		switch ( $field['type'] ) {
			case 'checkbox':
				$clean[ $key ] = ! empty( $raw ) ? 1 : 0;
				break;
			case 'number':
				$clean[ $key ] = null !== $raw ? absint( $raw ) : $field['default'];
				break;
			case 'url':
				$clean[ $key ] = null !== $raw ? sanitize_url( wp_unslash( $raw ) ) : '';
				break;
			case 'color':
				$sanitized     = null !== $raw ? sanitize_hex_color( wp_unslash( $raw ) ) : '';
				$clean[ $key ] = $sanitized ? $sanitized : $field['default'];
				break;
			case 'page':
				$clean[ $key ] = null !== $raw ? absint( $raw ) : 0;
				break;
			case 'select':
				$options       = isset( $field['options'] ) ? array_keys( $field['options'] ) : array();
				$clean[ $key ] = in_array( $raw, $options, true ) ? $raw : $field['default'];
				break;
			case 'textarea':
				$clean[ $key ] = null !== $raw ? wp_kses_post( wp_unslash( $raw ) ) : '';
				break;
			default:
				$clean[ $key ] = null !== $raw ? sanitize_text_field( wp_unslash( $raw ) ) : '';
				break;
		}
	}

	return $clean;
}

/**
 * Register the setting + sections/fields with the Settings API.
 *
 * @return void
 */
function evently_register_settings() {
	register_setting(
		'evently_settings_group',
		'evently_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'evently_sanitize_settings',
			'default'           => array(),
		)
	);

	foreach ( evently_get_settings_sections() as $section_id => $section_label ) {
		add_settings_section( 'evently_section_' . $section_id, $section_label, '__return_false', 'evently-settings' );
	}

	foreach ( evently_get_settings_fields() as $key => $field ) {
		add_settings_field(
			'evently_field_' . $key,
			$field['label'],
			'evently_render_settings_field',
			'evently-settings',
			'evently_section_' . $field['section'],
			array( 'key' => $key, 'field' => $field )
		);
	}
}
add_action( 'admin_init', 'evently_register_settings' );

/**
 * Render one settings field.
 *
 * @param array $args {key, field}
 * @return void
 */
function evently_render_settings_field( $args ) {
	$key         = $args['key'];
	$field       = $args['field'];
	$value       = evently_get_setting( $key, $field['default'] );
	$name        = "evently_settings[{$key}]";
	$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';

	switch ( $field['type'] ) {
		case 'checkbox':
			printf(
				'<label><input type="checkbox" name="%1$s" value="1" %2$s /> %3$s</label>',
				esc_attr( $name ),
				checked( $value, 1, false ),
				esc_html__( 'Enabled', 'evently' )
			);
			break;
		case 'color':
			printf( '<input type="text" class="evently-color-field" name="%1$s" value="%2$s" />', esc_attr( $name ), esc_attr( $value ) );
			break;
		case 'number':
			printf( '<input type="number" min="1" max="48" name="%1$s" value="%2$s" class="small-text" />', esc_attr( $name ), esc_attr( $value ) );
			break;
		case 'select':
			echo '<select name="' . esc_attr( $name ) . '">';
			foreach ( $field['options'] as $option_value => $option_label ) {
				printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $option_value ), selected( $value, $option_value, false ), esc_html( $option_label ) );
			}
			echo '</select>';
			break;
		case 'textarea':
			printf(
				'<textarea name="%1$s" rows="3" class="large-text" placeholder="%2$s">%3$s</textarea>',
				esc_attr( $name ),
				esc_attr( $placeholder ),
				esc_textarea( $value )
			);
			break;
		case 'page':
			wp_dropdown_pages(
				array(
					'name'              => $name,
					'selected'          => $value,
					'show_option_none'  => __( '— Auto-detect —', 'evently' ),
					'option_none_value' => 0,
				)
			);
			break;
		case 'url':
			printf(
				'<input type="url" name="%1$s" value="%2$s" class="regular-text" placeholder="%3$s" />',
				esc_attr( $name ),
				esc_attr( $value ),
				esc_attr( $placeholder ? $placeholder : 'https://' )
			);
			break;
		default:
			printf(
				'<input type="text" name="%1$s" value="%2$s" class="regular-text" placeholder="%3$s" />',
				esc_attr( $name ),
				esc_attr( $value ),
				esc_attr( $placeholder )
			);
			break;
	}

	if ( ! empty( $field['description'] ) ) {
		printf( '<p class="description">%s</p>', esc_html( $field['description'] ) );
	}
}

/**
 * Render the Theme Settings admin page.
 *
 * @return void
 */
function evently_render_theme_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap evently-setup-wrap">
		<h1><?php esc_html_e( 'Evently Theme Settings', 'evently' ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'evently_settings_group' );
			foreach ( evently_get_settings_sections() as $section_id => $section_label ) {
				echo '<div class="evently-setup-card">';
				echo '<h2>' . esc_html( $section_label ) . '</h2>';
				echo '<table class="form-table" role="presentation">';
				do_settings_fields( 'evently-settings', 'evently_section_' . $section_id );
				echo '</table>';
				echo '</div>';
			}
			submit_button( __( 'Save Settings', 'evently' ) );
			?>
		</form>
	</div>
	<?php
}
