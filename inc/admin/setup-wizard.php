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
			<h2><?php esc_html_e( '3. Next steps', 'evently' ); ?></h2>
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
