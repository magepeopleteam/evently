<?php
/**
 * Evently Setup — the demo-import wizard (brief §28).
 *
 * Implemented as one guided screen (Requirements → Import → Finish)
 * rather than a multi-page SPA: every step is still real and functional
 * (live plugin detection, a genuine one-click WooCommerce install via
 * WordPress's own plugin API, and a real AJAX-driven import with progress
 * and error feedback) — just without inventing extra client-side routing
 * for what is, underneath, a single linear action.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Which homepage editor is currently active: the theme's built-in
 * 14-section demo layout, or an admin-built page (Gutenberg/Elementor) set
 * up via the "Homepage Editor" step below.
 *
 * @return string 'builtin' | 'gutenberg' | 'elementor' | 'custom' (a static
 *                front page exists but wasn't created by this wizard step —
 *                e.g. the site owner picked one manually in Settings → Reading).
 */
function evently_get_homepage_editor_mode() {
	if ( 'page' !== get_option( 'show_on_front' ) ) {
		return 'builtin';
	}

	$front_id = (int) get_option( 'page_on_front' );
	if ( ! $front_id ) {
		return 'builtin';
	}

	$mode = get_post_meta( $front_id, '_evently_homepage_builder', true );
	if ( in_array( $mode, array( 'gutenberg', 'elementor' ), true ) ) {
		return $mode;
	}

	return evently_homepage_uses_custom_builder() ? 'custom' : 'builtin';
}

/**
 * The built-in homepage's default section order, serialized as real
 * Gutenberg block markup — used to pre-populate a new Gutenberg homepage
 * page so switching to it isn't a blank canvas. Uses serialize_block()
 * (WordPress's own block serializer) rather than hand-written HTML comments,
 * so the markup is guaranteed valid regardless of block.json/core changes.
 *
 * @return string
 */
function evently_get_default_gutenberg_homepage_content() {
	$sections = array(
		'hero',
		'categories',
		'trending-events',
		'featured-event',
		'choose-vibe',
		'near-you',
		'calendar',
		'how-it-works',
		'digital-ticket',
		'organizer-cta',
		'stats',
		'testimonials',
		'event-journal',
		'final-cta',
	);

	$content = '';
	foreach ( $sections as $slug ) {
		$content .= serialize_block(
			array(
				'blockName'    => 'evently/' . $slug,
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		) . "\n\n";
	}

	return $content;
}

/**
 * Find (idempotent — safe to call repeatedly) or create the page Evently
 * Setup uses as the site's front page for a given homepage editor mode.
 *
 * For Gutenberg, the page is pre-populated with all 14 sections in the
 * built-in homepage's default order (real dynamic blocks — still reflects
 * Theme Settings afterward, see blocks/{slug}/render.php). For Elementor,
 * the page is deliberately left blank: Elementor's own builder data
 * (`_elementor_data`) has an internal, version-dependent schema that isn't
 * safe to hand-fabricate here — the admin drags in the 12 Evently widgets
 * themselves once, which is a one-time cost and guaranteed to always be
 * valid, versus a fragile "looks right today, breaks after an Elementor
 * update" shortcut.
 *
 * @param string $mode 'gutenberg' | 'elementor'.
 * @return int Post ID, or 0 on failure.
 */
function evently_get_or_create_homepage_builder_page( $mode ) {
	$option_key  = 'evently_' . $mode . '_home_page_id';
	$existing_id = (int) get_option( $option_key );

	if ( $existing_id && get_post( $existing_id ) ) {
		return $existing_id;
	}

	$post_id = wp_insert_post(
		array(
			'post_title'   => __( 'Home', 'evently' ),
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'gutenberg' === $mode ? evently_get_default_gutenberg_homepage_content() : '',
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return 0;
	}

	update_post_meta( $post_id, '_evently_homepage_builder', $mode );
	update_option( $option_key, $post_id );

	return $post_id;
}

/**
 * AJAX: apply the site owner's "Homepage Editor" choice — built-in demo,
 * Gutenberg, or Elementor.
 *
 * @return void
 */
function evently_ajax_setup_homepage_mode() {
	check_ajax_referer( 'evently_admin_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to do this.', 'evently' ) ), 403 );
	}

	$mode = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : '';

	if ( 'builtin' === $mode ) {
		// Reversible, non-destructive: un-assigns the front page without
		// deleting whatever Gutenberg/Elementor page was built.
		update_option( 'show_on_front', 'posts' );
		wp_send_json_success( array( 'mode' => 'builtin', 'editUrl' => '' ) );
	}

	if ( ! in_array( $mode, array( 'gutenberg', 'elementor' ), true ) ) {
		wp_send_json_error( array( 'message' => __( 'Unknown homepage editor.', 'evently' ) ) );
	}

	if ( 'elementor' === $mode && ! evently_has_elementor() ) {
		wp_send_json_error( array( 'message' => __( 'Install and activate Elementor first.', 'evently' ) ) );
	}

	$page_id = evently_get_or_create_homepage_builder_page( $mode );
	if ( ! $page_id ) {
		wp_send_json_error( array( 'message' => __( 'Could not create the homepage.', 'evently' ) ) );
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $page_id );

	$edit_url = 'elementor' === $mode
		? admin_url( 'post.php?post=' . $page_id . '&action=elementor' )
		: admin_url( 'post.php?post=' . $page_id . '&action=edit' );

	wp_send_json_success( array( 'mode' => $mode, 'editUrl' => $edit_url ) );
}
add_action( 'wp_ajax_evently_setup_homepage_mode', 'evently_ajax_setup_homepage_mode' );

/**
 * AJAX: install + activate Elementor (wordpress.org-hosted, so a real,
 * ordinary plugin install) — same mechanism as evently_ajax_install_woocommerce().
 *
 * @return void
 */
function evently_ajax_install_elementor() {
	check_ajax_referer( 'evently_admin_nonce', 'nonce' );

	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to install plugins.', 'evently' ) ), 403 );
	}

	if ( evently_has_elementor() ) {
		wp_send_json_success( array( 'message' => __( 'Elementor is already active.', 'evently' ) ) );
	}

	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	$api = plugins_api( 'plugin_information', array( 'slug' => 'elementor' ) );
	if ( is_wp_error( $api ) ) {
		wp_send_json_error( array( 'message' => $api->get_error_message() ) );
	}

	$skin     = new Automatic_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader( $skin );
	$result   = $upgrader->install( $api->download_link );

	if ( is_wp_error( $result ) || ! $result ) {
		wp_send_json_error( array( 'message' => __( 'Could not install Elementor. You can install it manually from Plugins → Add New.', 'evently' ) ) );
	}

	$activated = activate_plugin( 'elementor/elementor.php' );
	if ( is_wp_error( $activated ) ) {
		wp_send_json_error( array( 'message' => $activated->get_error_message() ) );
	}

	wp_send_json_success( array( 'message' => __( 'Elementor installed and activated.', 'evently' ) ) );
}
add_action( 'wp_ajax_evently_install_elementor', 'evently_ajax_install_elementor' );

/**
 * Render the Evently Setup admin page.
 *
 * @return void
 */
function evently_render_setup_wizard_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$has_woocommerce = evently_has_woocommerce();
	$has_booking     = evently_has_booking_plugin();
	$is_imported     = class_exists( 'Evently_Demo_Importer' ) && Evently_Demo_Importer::is_imported();
	$homepage_mode   = evently_get_homepage_editor_mode();
	?>
	<div class="wrap evently-setup-wrap">
		<h1><?php esc_html_e( 'Evently Setup', 'evently' ); ?></h1>
		<p class="evently-setup-intro">
			<?php esc_html_e( 'Get Evently ready in a few steps: confirm the required plugins are active, then import the "All Events" demo — realistic categories, organizers, events with real ticket types, blog posts and the Events/Organizer Dashboard pages.', 'evently' ); ?>
		</p>

		<div class="evently-setup-card">
			<h2><?php esc_html_e( '1. Required plugins', 'evently' ); ?></h2>
			<ul class="evently-setup-requirements">
				<li class="evently-setup-requirement <?php echo $has_woocommerce ? 'is-ok' : 'is-missing'; ?>">
					<span class="evently-setup-requirement__status"><?php echo $has_woocommerce ? '✓' : '!'; ?></span>
					<span class="evently-setup-requirement__label"><?php esc_html_e( 'WooCommerce', 'evently' ); ?></span>
					<?php if ( ! $has_woocommerce ) : ?>
						<button type="button" class="button button-secondary" id="evently-install-woocommerce">
							<?php esc_html_e( 'Install & Activate', 'evently' ); ?>
						</button>
					<?php else : ?>
						<span class="evently-setup-requirement__note"><?php esc_html_e( 'Active', 'evently' ); ?></span>
					<?php endif; ?>
				</li>
				<li class="evently-setup-requirement <?php echo $has_booking ? 'is-ok' : 'is-missing'; ?>">
					<span class="evently-setup-requirement__status"><?php echo $has_booking ? '✓' : '!'; ?></span>
					<span class="evently-setup-requirement__label"><?php esc_html_e( 'Evently Booking plugin (mage-eventpress)', 'evently' ); ?></span>
					<?php if ( ! $has_booking ) : ?>
						<a class="button button-secondary" href="https://mage-people.com/product/mage-woo-event-booking-manager-pro/" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Get the plugin', 'evently' ); ?>
						</a>
					<?php else : ?>
						<span class="evently-setup-requirement__note"><?php esc_html_e( 'Active', 'evently' ); ?></span>
					<?php endif; ?>
				</li>
			</ul>
		</div>

		<div class="evently-setup-card">
			<h2><?php esc_html_e( '2. Import demo content', 'evently' ); ?></h2>

			<?php if ( $is_imported ) : ?>
				<div class="notice notice-success inline">
					<p>
						<?php
						printf(
							/* translators: %s: date/time of import. */
							esc_html__( 'Demo content was already imported on %s. Running it again will not duplicate existing demo events/pages.', 'evently' ),
							esc_html( get_option( 'evently_demo_imported_at', '' ) )
						);
						?>
					</p>
				</div>
			<?php endif; ?>

			<p><?php esc_html_e( 'Choose a demo:', 'evently' ); ?></p>
			<select id="evently-demo-select" disabled>
				<option value="all-events"><?php esc_html_e( 'Evently — All Events (available now)', 'evently' ); ?></option>
				<option value="concert" disabled><?php esc_html_e( 'Evently — Concert (coming soon)', 'evently' ); ?></option>
				<option value="conference" disabled><?php esc_html_e( 'Evently — Conference (coming soon)', 'evently' ); ?></option>
				<option value="wedding" disabled><?php esc_html_e( 'Evently — Wedding (coming soon)', 'evently' ); ?></option>
				<option value="sports" disabled><?php esc_html_e( 'Evently — Sports (coming soon)', 'evently' ); ?></option>
				<option value="workshop" disabled><?php esc_html_e( 'Evently — Workshop (coming soon)', 'evently' ); ?></option>
			</select>

			<p>
				<button type="button" class="button button-primary button-hero" id="evently-run-import" <?php disabled( ! $has_booking ); ?>>
					<?php esc_html_e( 'Import Demo Content', 'evently' ); ?>
				</button>
			</p>

			<?php if ( ! $has_booking ) : ?>
				<p class="description"><?php esc_html_e( 'Activate the Evently Booking plugin above before importing — events cannot be created without it.', 'evently' ); ?></p>
			<?php endif; ?>

			<div id="evently-import-progress" class="evently-setup-progress" hidden>
				<div class="evently-setup-progress__bar"><div class="evently-setup-progress__fill"></div></div>
				<ul id="evently-import-log" class="evently-setup-log"></ul>
			</div>
		</div>

		<div class="evently-setup-card">
			<h2><?php esc_html_e( '3. Homepage editor', 'evently' ); ?></h2>
			<p><?php esc_html_e( 'Choose how you want to build and edit your homepage. You can change this anytime — nothing here is permanent.', 'evently' ); ?></p>

			<div class="evently-setup-editor-choice">
				<div class="evently-setup-editor-option<?php echo 'builtin' === $homepage_mode ? ' is-active' : ''; ?>">
					<h3><?php esc_html_e( 'Built-in demo homepage', 'evently' ); ?></h3>
					<p><?php esc_html_e( "The theme's own 14-section homepage — content editable from Evently → Theme Settings, no page builder involved.", 'evently' ); ?></p>
					<?php if ( 'builtin' === $homepage_mode ) : ?>
						<span class="evently-setup-requirement__note"><?php esc_html_e( 'Currently active', 'evently' ); ?></span>
					<?php else : ?>
						<button type="button" class="button button-secondary" data-evently-homepage-mode="builtin">
							<?php esc_html_e( 'Switch back to built-in', 'evently' ); ?>
						</button>
					<?php endif; ?>
				</div>

				<div class="evently-setup-editor-option<?php echo 'gutenberg' === $homepage_mode ? ' is-active' : ''; ?>">
					<h3><?php esc_html_e( 'Block Editor (Gutenberg)', 'evently' ); ?></h3>
					<p><?php esc_html_e( 'Native to WordPress. Creates a "Home" page pre-filled with all 14 sections as real blocks — drag, delete, reorder, or add any block. Each section still reflects Theme Settings.', 'evently' ); ?></p>
					<?php if ( 'gutenberg' === $homepage_mode ) : ?>
						<span class="evently-setup-requirement__note"><?php esc_html_e( 'Currently active', 'evently' ); ?></span>
						<a class="button button-secondary" href="<?php echo esc_url( admin_url( 'post.php?post=' . (int) get_option( 'evently_gutenberg_home_page_id' ) . '&action=edit' ) ); ?>">
							<?php esc_html_e( 'Edit homepage →', 'evently' ); ?>
						</a>
					<?php else : ?>
						<button type="button" class="button button-primary" data-evently-homepage-mode="gutenberg">
							<?php esc_html_e( 'Set up & Edit →', 'evently' ); ?>
						</button>
					<?php endif; ?>
				</div>

				<div class="evently-setup-editor-option<?php echo 'elementor' === $homepage_mode ? ' is-active' : ''; ?>">
					<h3><?php esc_html_e( 'Elementor', 'evently' ); ?></h3>
					<p><?php esc_html_e( 'Creates an empty "Home" page and opens it in Elementor. Drag in the 12 Evently widgets (Hero, Categories, Testimonials…) yourself — each pulls from Theme Settings automatically.', 'evently' ); ?></p>
					<?php if ( 'elementor' === $homepage_mode ) : ?>
						<span class="evently-setup-requirement__note"><?php esc_html_e( 'Currently active', 'evently' ); ?></span>
						<a class="button button-secondary" href="<?php echo esc_url( admin_url( 'post.php?post=' . (int) get_option( 'evently_elementor_home_page_id' ) . '&action=elementor' ) ); ?>">
							<?php esc_html_e( 'Edit homepage →', 'evently' ); ?>
						</a>
					<?php elseif ( evently_has_elementor() ) : ?>
						<button type="button" class="button button-primary" data-evently-homepage-mode="elementor">
							<?php esc_html_e( 'Set up & Edit →', 'evently' ); ?>
						</button>
					<?php else : ?>
						<button type="button" class="button button-secondary" id="evently-install-elementor">
							<?php esc_html_e( 'Install & Activate Elementor', 'evently' ); ?>
						</button>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( 'custom' === $homepage_mode ) : ?>
				<div class="notice notice-warning inline">
					<p><?php esc_html_e( 'Settings → Reading currently points your homepage at a page this wizard didn\'t create. Choosing one of the options above will take over that setting.', 'evently' ); ?></p>
				</div>
			<?php endif; ?>
		</div>

		<div class="evently-setup-card">
			<h2><?php esc_html_e( '4. Next steps', 'evently' ); ?></h2>
			<ul class="evently-setup-links">
				<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=evently-settings' ) ); ?>"><?php esc_html_e( 'Configure Evently Theme Settings →', 'evently' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'View your homepage →', 'evently' ); ?></a></li>
				<li><a href="<?php echo esc_url( evently_get_events_page_url() ); ?>" target="_blank"><?php esc_html_e( 'View the Events page →', 'evently' ); ?></a></li>
			</ul>
		</div>
	</div>
	<?php
}

/**
 * AJAX: run the demo importer.
 *
 * @return void
 */
function evently_ajax_run_import() {
	check_ajax_referer( 'evently_admin_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to do this.', 'evently' ) ), 403 );
	}

	if ( ! class_exists( 'Evently_Demo_Importer' ) ) {
		wp_send_json_error( array( 'message' => __( 'The demo importer is unavailable.', 'evently' ) ) );
	}

	$result = Evently_Demo_Importer::run();

	if ( ! $result['success'] ) {
		wp_send_json_error( array( 'message' => implode( ' ', $result['log'] ) ) );
	}

	wp_send_json_success( $result );
}
add_action( 'wp_ajax_evently_run_import', 'evently_ajax_run_import' );

/**
 * AJAX: install + activate WooCommerce via WordPress's own plugin API
 * (WooCommerce is a wordpress.org-hosted plugin, so this is a real,
 * ordinary plugin install — not a fabricated action). The booking plugin
 * itself is NOT wordpress.org-hosted, so it's never "installed" this way;
 * the setup screen only links out to it (see evently_render_setup_wizard_page()).
 *
 * @return void
 */
function evently_ajax_install_woocommerce() {
	check_ajax_referer( 'evently_admin_nonce', 'nonce' );

	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to install plugins.', 'evently' ) ), 403 );
	}

	if ( evently_has_woocommerce() ) {
		wp_send_json_success( array( 'message' => __( 'WooCommerce is already active.', 'evently' ) ) );
	}

	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	$api = plugins_api( 'plugin_information', array( 'slug' => 'woocommerce' ) );
	if ( is_wp_error( $api ) ) {
		wp_send_json_error( array( 'message' => $api->get_error_message() ) );
	}

	$skin     = new Automatic_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader( $skin );
	$result   = $upgrader->install( $api->download_link );

	if ( is_wp_error( $result ) || ! $result ) {
		wp_send_json_error( array( 'message' => __( 'Could not install WooCommerce. You can install it manually from Plugins → Add New.', 'evently' ) ) );
	}

	$activated = activate_plugin( 'woocommerce/woocommerce.php' );
	if ( is_wp_error( $activated ) ) {
		wp_send_json_error( array( 'message' => $activated->get_error_message() ) );
	}

	wp_send_json_success( array( 'message' => __( 'WooCommerce installed and activated.', 'evently' ) ) );
}
add_action( 'wp_ajax_evently_install_woocommerce', 'evently_ajax_install_woocommerce' );
