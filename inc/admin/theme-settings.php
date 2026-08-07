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
 * Field registry: key => {label, section, type, default, description}.
 * `type` drives both the sanitizer and which control renders.
 *
 * @return array
 */
function evently_get_settings_fields() {
	return array(
		// General.
		'create_event_url'        => array( 'label' => __( 'Create Event URL', 'evently' ), 'section' => 'general', 'type' => 'url', 'default' => '', 'description' => __( 'Where the header/footer "Create Event" buttons link. Leave empty to use the admin new-event screen.', 'evently' ) ),
		'events_page_id'          => array( 'label' => __( 'Events Page', 'evently' ), 'section' => 'general', 'type' => 'page', 'default' => 0, 'description' => __( 'The page using the "Evently — Event Archive" template. Auto-detected if left unset.', 'evently' ) ),

		// Branding / Header.
		'hero_image'              => array( 'label' => __( 'Hero Image URL', 'evently' ), 'section' => 'header', 'type' => 'url', 'default' => '', 'description' => __( 'Homepage hero photograph.', 'evently' ) ),
		'hero_live_note'          => array( 'label' => __( 'Hero Live Note', 'evently' ), 'section' => 'header', 'type' => 'text', 'default' => '', 'description' => __( 'e.g. "2,840 tickets sold today".', 'evently' ) ),

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
		'show_related_events'     => array( 'label' => __( 'Show related events', 'evently' ), 'section' => 'single_event', 'type' => 'checkbox', 'default' => 1 ),

		// Featured event (homepage).
		'featured_event_title'    => array( 'label' => __( 'Featured Event Title', 'evently' ), 'section' => 'events', 'type' => 'text', 'default' => '' ),
		'featured_event_image'   => array( 'label' => __( 'Featured Event Image URL', 'evently' ), 'section' => 'events', 'type' => 'url', 'default' => '' ),
		'featured_event_date'     => array( 'label' => __( 'Featured Event Date', 'evently' ), 'section' => 'events', 'type' => 'text', 'default' => '' ),
		'featured_event_location' => array( 'label' => __( 'Featured Event Location', 'evently' ), 'section' => 'events', 'type' => 'text', 'default' => '' ),
		'featured_event_note'     => array( 'label' => __( 'Featured Event Note', 'evently' ), 'section' => 'events', 'type' => 'text', 'default' => '' ),

		// Footer.
		'footer_tagline'          => array( 'label' => __( 'Footer Tagline', 'evently' ), 'section' => 'footer', 'type' => 'textarea', 'default' => '' ),

		// Social.
		'social_instagram'        => array( 'label' => __( 'Instagram URL', 'evently' ), 'section' => 'social', 'type' => 'url', 'default' => '' ),
		'social_facebook'         => array( 'label' => __( 'Facebook URL', 'evently' ), 'section' => 'social', 'type' => 'url', 'default' => '' ),
		'social_x'                => array( 'label' => __( 'X (Twitter) URL', 'evently' ), 'section' => 'social', 'type' => 'url', 'default' => '' ),
		'social_youtube'          => array( 'label' => __( 'YouTube URL', 'evently' ), 'section' => 'social', 'type' => 'url', 'default' => '' ),

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
	$key    = $args['key'];
	$field  = $args['field'];
	$value  = evently_get_setting( $key, $field['default'] );
	$name   = "evently_settings[{$key}]";

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
			printf( '<textarea name="%1$s" rows="3" class="large-text">%2$s</textarea>', esc_attr( $name ), esc_textarea( $value ) );
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
			printf( '<input type="url" name="%1$s" value="%2$s" class="regular-text" placeholder="https://" />', esc_attr( $name ), esc_attr( $value ) );
			break;
		default:
			printf( '<input type="text" name="%1$s" value="%2$s" class="regular-text" />', esc_attr( $name ), esc_attr( $value ) );
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
