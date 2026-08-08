<?php
/**
 * Evently Setup — the demo-import wizard (brief §28).
 *
 * Implemented as one guided screen (Requirements → Import → Finish)
 * rather than a multi-page SPA: every step is still real and functional
 * (live plugin detection, genuine one-click installs of Elementor and
 * mage-eventpress — both required — plus WooCommerce (optional) via
 * WordPress's own plugin API, and a real AJAX-driven import that now also
 * builds a fully pre-designed, directly editable Elementor homepage rather
 * than a blank canvas — see inc/demo-import/importer.php's import_homepage())
 * — just without inventing extra client-side routing for what is,
 * underneath, a single linear action.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Install + activate a wordpress.org-hosted plugin by slug — the one shared
 * implementation behind every "Install & Activate" button on this screen
 * (Elementor, mage-eventpress; WooCommerce keeps its own pre-existing,
 * untouched copy of this same pattern below). Both `elementor` and
 * `mage-eventpress` are confirmed real wordpress.org slugs.
 *
 * @param string $slug        wordpress.org plugin slug.
 * @param string $plugin_file Plugin file relative to wp-content/plugins/, e.g. 'elementor/elementor.php'.
 * @return true|WP_Error
 */
function evently_install_plugin_from_dot_org( $slug, $plugin_file ) {
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	$api = plugins_api( 'plugin_information', array( 'slug' => $slug ) );
	if ( is_wp_error( $api ) ) {
		return $api;
	}

	$skin     = new Automatic_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader( $skin );
	$result   = $upgrader->install( $api->download_link );

	if ( is_wp_error( $result ) ) {
		return $result;
	}
	if ( ! $result ) {
		return new WP_Error( 'evently_install_failed', __( 'Could not install the plugin. You can install it manually from Plugins → Add New.', 'evently' ) );
	}

	return activate_plugin( $plugin_file );
}

/**
 * AJAX: install + activate Elementor (required, wordpress.org-hosted).
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

	$result = evently_install_plugin_from_dot_org( 'elementor', 'elementor/elementor.php' );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}

	wp_send_json_success( array( 'message' => __( 'Elementor installed and activated.', 'evently' ) ) );
}
add_action( 'wp_ajax_evently_install_elementor', 'evently_ajax_install_elementor' );

/**
 * AJAX: install + activate mage-eventpress (required, wordpress.org-hosted —
 * confirmed live against the .org plugins API at the exact slug/version this
 * theme targets).
 *
 * @return void
 */
function evently_ajax_install_booking_plugin() {
	check_ajax_referer( 'evently_admin_nonce', 'nonce' );

	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to install plugins.', 'evently' ) ), 403 );
	}

	if ( evently_has_booking_plugin() ) {
		wp_send_json_success( array( 'message' => __( 'mage-eventpress is already active.', 'evently' ) ) );
	}

	$result = evently_install_plugin_from_dot_org( 'mage-eventpress', 'mage-eventpress/woocommerce-event-press.php' );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}

	wp_send_json_success( array( 'message' => __( 'mage-eventpress installed and activated.', 'evently' ) ) );
}
add_action( 'wp_ajax_evently_install_booking_plugin', 'evently_ajax_install_booking_plugin' );

/**
 * Redirect the admin to Evently Setup right after activating the theme —
 * standard theme onboarding UX, and the first chance to install the two
 * required plugins.
 *
 * @return void
 */
function evently_redirect_to_setup_on_activation() {
	if ( ! current_user_can( 'manage_options' ) || wp_doing_ajax() || is_network_admin() ) {
		return;
	}
	set_transient( 'evently_activation_redirect', 1, 30 );
}
add_action( 'after_switch_theme', 'evently_redirect_to_setup_on_activation' );

/**
 * Fires the actual redirect on the next admin page load — after_switch_theme
 * runs too early in the request to safely wp_safe_redirect() from directly.
 *
 * @return void
 */
function evently_maybe_redirect_to_setup() {
	if ( ! get_transient( 'evently_activation_redirect' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	delete_transient( 'evently_activation_redirect' );

	wp_safe_redirect( admin_url( 'admin.php?page=evently' ) );
	exit;
}
add_action( 'admin_init', 'evently_maybe_redirect_to_setup' );

/**
 * Persistent admin notice — shown on every wp-admin screen while Evently is
 * active and either required plugin (Elementor, mage-eventpress) is missing.
 *
 * @return void
 */
function evently_required_plugins_notice() {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	$missing = array();
	if ( ! evently_has_elementor() ) {
		$missing['elementor'] = __( 'Elementor', 'evently' );
	}
	if ( ! evently_has_booking_plugin() ) {
		$missing['mage-eventpress'] = __( 'mage-eventpress', 'evently' );
	}

	if ( empty( $missing ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( $screen && 'toplevel_page_evently' === $screen->id ) {
		return; // The Setup screen's own Requirements card already covers this.
	}

	?>
	<div class="notice notice-error evently-required-plugins-notice">
		<p>
			<strong><?php esc_html_e( 'Evently requires the following plugin(s):', 'evently' ); ?></strong>
			<?php echo esc_html( implode( ', ', $missing ) ); ?>
		</p>
		<p>
			<?php if ( isset( $missing['elementor'] ) ) : ?>
				<button type="button" class="button button-primary" id="evently-notice-install-elementor"><?php esc_html_e( 'Install & Activate Elementor', 'evently' ); ?></button>
			<?php endif; ?>
			<?php if ( isset( $missing['mage-eventpress'] ) ) : ?>
				<button type="button" class="button button-primary" id="evently-notice-install-booking"><?php esc_html_e( 'Install & Activate mage-eventpress', 'evently' ); ?></button>
			<?php endif; ?>
			<a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=evently' ) ); ?>"><?php esc_html_e( 'Go to Evently Setup', 'evently' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'evently_required_plugins_notice' );

/**
 * Shared admin page header used by Setup and Theme Settings.
 *
 * @param string $title   Page title.
 * @param string $intro   Supporting sentence.
 * @param string $active  Active nav slug: setup|settings.
 * @return void
 */
function evently_admin_page_header( $title, $intro, $active = 'setup' ) {
	?>
	<header class="evently-admin-hero">
		<div class="evently-admin-hero__inner">
			<div class="evently-admin-hero__top">
				<div class="evently-admin-hero__brand">
					<span class="evently-admin-logo" aria-hidden="true">
						<svg width="22" height="22" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect width="28" height="28" rx="8" fill="currentColor"/>
							<path d="M8 10.5h12M8 14h8M8 17.5h10" stroke="#fff" stroke-width="1.75" stroke-linecap="round"/>
							<circle cx="19.5" cy="9.5" r="2.25" fill="#BFDBFE"/>
						</svg>
					</span>
					<span class="evently-admin-logo-text"><?php esc_html_e( 'Evently', 'evently' ); ?></span>
				</div>
				<nav class="evently-admin-tabs" aria-label="<?php esc_attr_e( 'Evently admin', 'evently' ); ?>">
					<a class="evently-admin-tabs__link<?php echo 'setup' === $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=evently' ) ); ?>">
						<?php esc_html_e( 'Setup', 'evently' ); ?>
					</a>
					<a class="evently-admin-tabs__link<?php echo 'settings' === $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=evently-settings' ) ); ?>">
						<?php esc_html_e( 'Theme Settings', 'evently' ); ?>
					</a>
				</nav>
			</div>
			<div class="evently-admin-hero__copy">
				<h1><?php echo esc_html( $title ); ?></h1>
				<p><?php echo esc_html( $intro ); ?></p>
			</div>
		</div>
	</header>
	<?php
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

	$has_elementor   = evently_has_elementor();
	$has_booking     = evently_has_booking_plugin();
	$has_woocommerce = evently_has_woocommerce();
	$is_imported     = class_exists( 'Evently_Demo_Importer' ) && Evently_Demo_Importer::is_imported();
	$can_import      = $has_elementor && $has_booking;

	$step1_done = $has_elementor && $has_booking;
	$step2_done = $is_imported;
	$active_step = ! $step1_done ? 1 : ( ! $step2_done ? 2 : 3 );
	?>
	<div class="wrap evently-admin evently-setup-wrap">
		<?php
		evently_admin_page_header(
			__( 'Get Evently ready', 'evently' ),
			__( 'Install the required plugins, import the All Events demo, then jump into your live site — homepage, events, and booking wired up.', 'evently' ),
			'setup'
		);
		?>

		<ol class="evently-setup-steps" aria-label="<?php esc_attr_e( 'Setup progress', 'evently' ); ?>">
			<li class="evently-setup-steps__item<?php echo $step1_done ? ' is-done' : ''; ?><?php echo 1 === $active_step ? ' is-current' : ''; ?>">
				<span class="evently-setup-steps__num"><?php echo $step1_done ? '✓' : '1'; ?></span>
				<span class="evently-setup-steps__label"><?php esc_html_e( 'Plugins', 'evently' ); ?></span>
			</li>
			<li class="evently-setup-steps__item<?php echo $step2_done ? ' is-done' : ''; ?><?php echo 2 === $active_step ? ' is-current' : ''; ?>">
				<span class="evently-setup-steps__num"><?php echo $step2_done ? '✓' : '2'; ?></span>
				<span class="evently-setup-steps__label"><?php esc_html_e( 'Import', 'evently' ); ?></span>
			</li>
			<li class="evently-setup-steps__item<?php echo 3 === $active_step ? ' is-current is-done' : ''; ?>">
				<span class="evently-setup-steps__num"><?php echo 3 === $active_step ? '✓' : '3'; ?></span>
				<span class="evently-setup-steps__label"><?php esc_html_e( 'Explore', 'evently' ); ?></span>
			</li>
		</ol>

		<div class="evently-admin-stack">
			<section class="evently-admin-card<?php echo 1 === $active_step ? ' is-spotlight' : ''; ?>" id="evently-step-plugins">
				<div class="evently-admin-card__head">
					<div>
						<span class="evently-admin-card__eyebrow"><?php esc_html_e( 'Step 1', 'evently' ); ?></span>
						<h2><?php esc_html_e( 'Required plugins', 'evently' ); ?></h2>
						<p><?php esc_html_e( 'Elementor and Event Booking Manager power the homepage builder and ticket sales. WooCommerce is optional for checkout.', 'evently' ); ?></p>
					</div>
					<?php if ( $step1_done ) : ?>
						<span class="evently-admin-badge is-success"><?php esc_html_e( 'Ready', 'evently' ); ?></span>
					<?php else : ?>
						<span class="evently-admin-badge is-warn"><?php esc_html_e( 'Action needed', 'evently' ); ?></span>
					<?php endif; ?>
				</div>

				<ul class="evently-plugin-grid">
					<li class="evently-plugin-card <?php echo $has_elementor ? 'is-ok' : 'is-missing'; ?>">
						<div class="evently-plugin-card__icon" aria-hidden="true">
							<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="4" stroke="currentColor" stroke-width="1.75"/><path d="M8 8h8v3H8V8zm0 5h5v3H8v-3z" fill="currentColor"/></svg>
						</div>
						<div class="evently-plugin-card__body">
							<strong><?php esc_html_e( 'Elementor', 'evently' ); ?></strong>
							<span><?php esc_html_e( 'Required — homepage & page builder', 'evently' ); ?></span>
						</div>
						<?php if ( ! $has_elementor ) : ?>
							<button type="button" class="evently-btn evently-btn--primary" id="evently-install-elementor">
								<?php esc_html_e( 'Install & Activate', 'evently' ); ?>
							</button>
						<?php else : ?>
							<span class="evently-plugin-card__status"><?php esc_html_e( 'Active', 'evently' ); ?></span>
						<?php endif; ?>
					</li>

					<li class="evently-plugin-card <?php echo $has_booking ? 'is-ok' : 'is-missing'; ?>">
						<div class="evently-plugin-card__icon" aria-hidden="true">
							<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 7h16v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7z" stroke="currentColor" stroke-width="1.75"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M8 12h8" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>
						</div>
						<div class="evently-plugin-card__body">
							<strong><?php esc_html_e( 'Event Booking Manager', 'evently' ); ?></strong>
							<span><?php esc_html_e( 'Required — events, tickets & booking', 'evently' ); ?></span>
						</div>
						<?php if ( ! $has_booking ) : ?>
							<button type="button" class="evently-btn evently-btn--primary" id="evently-install-booking">
								<?php esc_html_e( 'Install & Activate', 'evently' ); ?>
							</button>
						<?php else : ?>
							<span class="evently-plugin-card__status"><?php esc_html_e( 'Active', 'evently' ); ?></span>
						<?php endif; ?>
					</li>

					<li class="evently-plugin-card <?php echo $has_woocommerce ? 'is-ok' : 'is-missing'; ?>">
						<div class="evently-plugin-card__icon" aria-hidden="true">
							<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M6 7h15l-1.5 9H8L6 7zm0 0L5 4H2" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="20" r="1.5" fill="currentColor"/><circle cx="18" cy="20" r="1.5" fill="currentColor"/></svg>
						</div>
						<div class="evently-plugin-card__body">
							<strong><?php esc_html_e( 'WooCommerce', 'evently' ); ?></strong>
							<span><?php esc_html_e( 'Optional — ticket checkout', 'evently' ); ?></span>
						</div>
						<?php if ( ! $has_woocommerce ) : ?>
							<button type="button" class="evently-btn evently-btn--ghost" id="evently-install-woocommerce">
								<?php esc_html_e( 'Install & Activate', 'evently' ); ?>
							</button>
						<?php else : ?>
							<span class="evently-plugin-card__status"><?php esc_html_e( 'Active', 'evently' ); ?></span>
						<?php endif; ?>
					</li>
				</ul>
			</section>

			<section class="evently-admin-card<?php echo 2 === $active_step ? ' is-spotlight' : ''; ?>" id="evently-step-import">
				<div class="evently-admin-card__head">
					<div>
						<span class="evently-admin-card__eyebrow"><?php esc_html_e( 'Step 2', 'evently' ); ?></span>
						<h2><?php esc_html_e( 'Import demo content', 'evently' ); ?></h2>
						<p><?php esc_html_e( 'Categories, organizers, ticketed events, blog posts, dashboard pages, and a fully built Elementor homepage.', 'evently' ); ?></p>
					</div>
					<?php if ( $is_imported ) : ?>
						<span class="evently-admin-badge is-success"><?php esc_html_e( 'Imported', 'evently' ); ?></span>
					<?php endif; ?>
				</div>

				<?php if ( $is_imported ) : ?>
					<div class="evently-admin-callout is-success">
						<strong><?php esc_html_e( 'Demo already imported', 'evently' ); ?></strong>
						<p>
							<?php
							printf(
								/* translators: %s: date/time of import. */
								esc_html__( 'Imported on %s. Running again will not duplicate existing demo events or pages.', 'evently' ),
								esc_html( get_option( 'evently_demo_imported_at', '' ) )
							);
							?>
						</p>
					</div>
				<?php endif; ?>

				<div class="evently-demo-picker">
					<label class="evently-demo-picker__label" for="evently-demo-select"><?php esc_html_e( 'Choose a demo', 'evently' ); ?></label>
					<div class="evently-demo-picker__row">
						<select id="evently-demo-select" class="evently-select" disabled>
							<option value="all-events"><?php esc_html_e( 'Evently — All Events (available now)', 'evently' ); ?></option>
							<option value="concert" disabled><?php esc_html_e( 'Evently — Concert (coming soon)', 'evently' ); ?></option>
							<option value="conference" disabled><?php esc_html_e( 'Evently — Conference (coming soon)', 'evently' ); ?></option>
							<option value="wedding" disabled><?php esc_html_e( 'Evently — Wedding (coming soon)', 'evently' ); ?></option>
							<option value="sports" disabled><?php esc_html_e( 'Evently — Sports (coming soon)', 'evently' ); ?></option>
							<option value="workshop" disabled><?php esc_html_e( 'Evently — Workshop (coming soon)', 'evently' ); ?></option>
						</select>
						<button type="button" class="evently-btn evently-btn--primary evently-btn--lg" id="evently-run-import" <?php disabled( ! $can_import ); ?>>
							<?php esc_html_e( 'Import Demo Content', 'evently' ); ?>
						</button>
					</div>
					<?php if ( ! $can_import ) : ?>
						<p class="evently-admin-hint"><?php esc_html_e( 'Install and activate Elementor and Event Booking Manager before importing.', 'evently' ); ?></p>
					<?php endif; ?>
				</div>

				<div id="evently-import-progress" class="evently-setup-progress" hidden>
					<div class="evently-setup-progress__bar"><div class="evently-setup-progress__fill"></div></div>
					<ul id="evently-import-log" class="evently-setup-log"></ul>
				</div>
			</section>

			<section class="evently-admin-card<?php echo 3 === $active_step ? ' is-spotlight' : ''; ?>" id="evently-step-next">
				<div class="evently-admin-card__head">
					<div>
						<span class="evently-admin-card__eyebrow"><?php esc_html_e( 'Step 3', 'evently' ); ?></span>
						<h2><?php esc_html_e( 'Next steps', 'evently' ); ?></h2>
						<p><?php esc_html_e( 'Polish your brand, edit the homepage, and open the site your visitors will see.', 'evently' ); ?></p>
					</div>
				</div>

				<div class="evently-next-grid">
					<?php if ( $is_imported ) : ?>
						<a class="evently-next-card" href="<?php echo esc_url( admin_url( 'post.php?post=' . (int) get_option( 'page_on_front' ) . '&action=elementor' ) ); ?>">
							<span class="evently-next-card__icon" aria-hidden="true">✎</span>
							<strong><?php esc_html_e( 'Edit Homepage', 'evently' ); ?></strong>
							<span><?php esc_html_e( 'Open the Elementor editor on your front page.', 'evently' ); ?></span>
						</a>
					<?php endif; ?>
					<a class="evently-next-card" href="<?php echo esc_url( admin_url( 'admin.php?page=evently-settings' ) ); ?>">
						<span class="evently-next-card__icon" aria-hidden="true">◆</span>
						<strong><?php esc_html_e( 'Theme Settings', 'evently' ); ?></strong>
						<span><?php esc_html_e( 'Colors, archive layout, footer, and social links.', 'evently' ); ?></span>
					</a>
					<a class="evently-next-card" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="evently-next-card__icon" aria-hidden="true">↗</span>
						<strong><?php esc_html_e( 'View Homepage', 'evently' ); ?></strong>
						<span><?php esc_html_e( 'Preview the live front-end experience.', 'evently' ); ?></span>
					</a>
					<a class="evently-next-card" href="<?php echo esc_url( evently_get_events_page_url() ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="evently-next-card__icon" aria-hidden="true">▣</span>
						<strong><?php esc_html_e( 'View Events', 'evently' ); ?></strong>
						<span><?php esc_html_e( 'Browse the events archive page.', 'evently' ); ?></span>
					</a>
				</div>
			</section>
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
 * AJAX: install + activate WooCommerce via WordPress's own plugin API.
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
