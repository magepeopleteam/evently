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
	return array(
		// General.
		'create_event_url'        => array( 'label' => __( 'Create Event URL', 'evently' ), 'section' => 'general', 'type' => 'url', 'default' => '', 'description' => __( 'Where the header/footer "Create Event" buttons link. Leave empty to use the admin new-event screen.', 'evently' ), 'placeholder' => admin_url( 'post-new.php?post_type=mep_events' ) ),
		'events_page_id'          => array( 'label' => __( 'Events Page', 'evently' ), 'section' => 'general', 'type' => 'page', 'default' => 0, 'description' => __( 'The page using the "Evently — Event Archive" template. Auto-detected if left unset.', 'evently' ) ),

		// Colors.
		'color_primary'           => array( 'label' => __( 'Primary Accent', 'evently' ), 'section' => 'colors', 'type' => 'color', 'default' => '#6C5CE7' ),
		'color_orange'            => array( 'label' => __( 'Secondary Accent', 'evently' ), 'section' => 'colors', 'type' => 'color', 'default' => '#FF7657' ),
		'color_dark'              => array( 'label' => __( 'Dark', 'evently' ), 'section' => 'colors', 'type' => 'color', 'default' => '#0B0B0D' ),

		// Events / Archive.
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

		// Hero (image/live-note), Featured Event (title/image/date/location/
		// note), Near You (default city), Categories, Calendar, How It Works,
		// Stats, Testimonials, Digital Ticket, Organizer Dashboard and Final
		// CTA no longer have Theme Settings fields — Elementor is required
		// now, every one of those sections has its own real Elementor widget
		// controls (inc/integrations/elementor/class-widget-*.php), and the
		// demo importer builds a real, pre-designed Elementor homepage (see
		// Evently_Demo_Importer::import_homepage()) — so that content is
		// edited directly in the Elementor editor, not through a separate
		// wp-admin form. `create_event_url` is the one exception that stays
		// above: the site header/footer's own "Create Event" button isn't
		// part of any homepage widget, so it still needs a global setting.

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
		'colors'       => __( 'Colors', 'evently' ),
		'events'       => __( 'Events', 'evently' ),
		'archive'      => __( 'Archive', 'evently' ),
		'single_event' => __( 'Single Event', 'evently' ),
		'footer'       => __( 'Footer', 'evently' ),
		'social'       => __( 'Social', 'evently' ),
		'performance'  => __( 'Performance', 'evently' ),
	);
}

/**
 * Which settings sections get a live preview panel on the Theme Settings
 * screen, and which template-part renders it. Sections not listed here
 * (General, Archive, Single Event, Colors, Performance) either aren't a
 * single visual block or — Colors — get a lightweight client-side swatch
 * instead (see assets/js/admin-live-preview.js), not a rendered-template
 * iframe. Every former homepage-section preview (Header/Hero, Featured
 * Event, Categories, Calendar, How It Works, Stats, Testimonials, Digital
 * Ticket, Organizer Dashboard, Final CTA) is gone along with its settings
 * fields above — that content now lives directly in the real Elementor
 * homepage the demo importer builds, and Elementor's own canvas is the
 * live preview. `events` no longer gets a preview here either: the fields
 * still left in that section (show_price/location/favorite/rating) are
 * global card-display toggles spanning several different sections of the
 * homepage at once, not a single block one template-part could represent.
 *
 * @return array<string,array{template:string,note?:string}>
 */
function evently_get_preview_section_map() {
	return array(
		'footer'              => array( 'template' => 'template-parts/footer/site-footer' ),
		'social'              => array(
			'template' => 'template-parts/footer/site-footer',
			'note'     => __( 'Social icons only appear once at least one URL above is set.', 'evently' ),
		),
	);
}

/**
 * AJAX: render one homepage/footer template-part for the Theme Settings
 * live-preview panel, with the fields the visitor is currently editing
 * overlaid on top of the saved settings — nothing here is ever written to
 * the database (see evently_get_setting()'s `$GLOBALS['evently_preview_overrides']`
 * check, inc/helpers.php). Runs the posted values through the exact same
 * evently_sanitize_settings() used on real save, so preview escaping/typing
 * behavior never drifts from what Save Settings would actually persist.
 *
 * @return void
 */
function evently_ajax_preview_section() {
	check_ajax_referer( 'evently_admin_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to do this.', 'evently' ) ), 403 );
	}

	$section = isset( $_POST['section'] ) ? sanitize_key( wp_unslash( $_POST['section'] ) ) : '';
	$map     = evently_get_preview_section_map();

	if ( ! isset( $map[ $section ] ) ) {
		wp_send_json_error( array( 'message' => __( 'Unknown preview section.', 'evently' ) ) );
	}

	// Only fields actually registered under this settings section may be
	// overridden — a posted key outside that set is silently ignored.
	$section_fields = array_filter(
		evently_get_settings_fields(),
		static function ( $field ) use ( $section ) {
			return $field['section'] === $section;
		}
	);

	$posted = isset( $_POST['values'] ) && is_array( $_POST['values'] ) ? wp_unslash( $_POST['values'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- every value is run through evently_sanitize_settings() below, the same sanitizer real Save Settings uses.
	$scoped = array_intersect_key( $posted, $section_fields );
	$clean  = evently_sanitize_settings( $scoped );

	$GLOBALS['evently_preview_overrides'] = array_intersect_key( $clean, $section_fields );

	$section_html = evently_capture_template_part( $map[ $section ]['template'] );

	unset( $GLOBALS['evently_preview_overrides'] );

	if ( function_exists( 'evently_enqueue_fonts' ) ) {
		evently_enqueue_fonts();
	}
	// The same handle bundle evently_enqueue_assets() loads for is_front_page() —
	// simplest way to guarantee the preview never drifts from real homepage CSS.
	ob_start();
	wp_print_styles( array( 'evently-fonts', 'evently-header', 'evently-hero', 'evently-events', 'evently-home', 'evently-responsive' ) );
	$styles_html = ob_get_clean();

	$html = '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
		. $styles_html
		. '<style>body{margin:0;background:#fff;}</style></head><body>'
		. $section_html
		. '</body></html>';

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_evently_preview_section', 'evently_ajax_preview_section' );

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
	<div class="wrap evently-setup-wrap evently-theme-settings-wrap">
		<h1><?php esc_html_e( 'Evently Theme Settings', 'evently' ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'evently_settings_group' );
			$evently_preview_map = evently_get_preview_section_map();
			foreach ( evently_get_settings_sections() as $section_id => $section_label ) {
				$evently_has_template_preview = isset( $evently_preview_map[ $section_id ] );
				$evently_has_color_preview     = 'colors' === $section_id;
				$evently_has_preview           = $evently_has_template_preview || $evently_has_color_preview;

				echo '<div class="evently-setup-card' . ( $evently_has_preview ? ' evently-setup-card--with-preview' : '' ) . '">';
				echo '<h2>' . esc_html( $section_label ) . '</h2>';
				echo '<div class="evently-setup-card__body">';

				echo '<table class="form-table" role="presentation">';
				do_settings_fields( 'evently-settings', 'evently_section_' . $section_id );
				echo '</table>';

				if ( $evently_has_template_preview ) {
					$evently_note = $evently_preview_map[ $section_id ]['note'] ?? '';
					echo '<div class="evently-live-preview" data-section="' . esc_attr( $section_id ) . '">';
					echo '<div class="evently-live-preview__label">' . esc_html__( 'Live preview', 'evently' ) . '</div>';
					echo '<div class="evently-live-preview__frame-wrap"><iframe class="evently-live-preview__frame" title="' . esc_attr(
						/* translators: %s: settings section name, e.g. "Homepage: Categories". */
						sprintf( __( '%s live preview', 'evently' ), $section_label )
					) . '"></iframe></div>';
					if ( $evently_note ) {
						echo '<p class="evently-live-preview__note">' . esc_html( $evently_note ) . '</p>';
					}
					echo '</div>';
				} elseif ( $evently_has_color_preview ) {
					echo '<div class="evently-live-preview">';
					echo '<div class="evently-live-preview__label">' . esc_html__( 'Live preview', 'evently' ) . '</div>';
					echo '<div class="evently-color-preview" id="evently-color-preview">';
					echo '<span class="evently-color-preview__badge">' . esc_html__( 'Featured Experience', 'evently' ) . '</span>';
					echo '<button type="button" class="evently-color-preview__btn">' . esc_html__( 'Explore Events', 'evently' ) . '</button>';
					echo '</div>';
					echo '<p class="evently-live-preview__note">' . esc_html__( 'A quick swatch, not a full page render — see the homepage itself for the true effect.', 'evently' ) . '</p>';
					echo '</div>';
				}

				echo '</div>'; // .evently-setup-card__body
				echo '</div>'; // .evently-setup-card
			}
			submit_button( __( 'Save Settings', 'evently' ) );
			?>
		</form>
	</div>
	<?php
}
