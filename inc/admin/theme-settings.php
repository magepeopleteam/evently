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
			foreach ( evently_get_settings_sections() as $section_id => $section_label ) {
				echo '<div class="evently-setup-card">';
				echo '<h2>' . esc_html( $section_label ) . '</h2>';
				echo '<div class="evently-setup-card__body">';

				echo '<table class="form-table" role="presentation">';
				do_settings_fields( 'evently-settings', 'evently_section_' . $section_id );
				echo '</table>';

				echo '</div>'; // .evently-setup-card__body
				echo '</div>'; // .evently-setup-card
			}
			submit_button( __( 'Save Settings', 'evently' ) );
			?>
		</form>
	</div>
	<?php
}
