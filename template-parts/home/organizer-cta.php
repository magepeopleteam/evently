<?php
/**
 * Organizer CTA — dark section with a dashboard mockup (brief §11/§23).
 * The dashboard numbers are illustrative marketing content, explicitly
 * labeled "Preview" — the theme does not claim this is a live data feed,
 * since no real organizer-analytics API exists in the inspected booking
 * plugin (brief §44). The real, functional Organizer Dashboard UI (built
 * from actual WooCommerce order data where possible) lives at
 * template-parts/organizer/dashboard.php (brief §23 phase).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_eyebrow    = $args['eyebrow'] ?? __( 'For Organizers', 'evently' );
$evently_heading    = $args['heading'] ?? __( 'Turn your event into an experience.', 'evently' );
$evently_subhead     = $args['subhead'] ?? __( 'Create events, sell tickets, manage attendees and track your performance from one powerful dashboard.', 'evently' );
$evently_button_text = $args['button_text'] ?? __( 'Start Selling', 'evently' );
$evently_secondary_text = $args['secondary_button_text'] ?? __( 'Explore Organizer Tools', 'evently' );

// The Evently Organizer CTA Elementor widget's own repeater/settings
// (class-widget-organizer-cta.php) take priority when present; otherwise
// fall back to the bundled demo numbers overlaid with the legacy per-index
// evently_get_setting() overrides, exactly as this file behaved before the
// widget had any controls.
if ( ! empty( $args['dash_stats'] ) && is_array( $args['dash_stats'] ) ) {
	$evently_dash_stats = $args['dash_stats'];
} else {
	$evently_dash_stats = evently_demo_dashboard_stats();
	foreach ( $evently_dash_stats as $evently_index => &$evently_dash_stat ) {
		$evently_n                    = $evently_index + 1;
		$evently_dash_stat['label']  = evently_get_setting( "dash_stat_{$evently_n}_label", $evently_dash_stat['label'] );
		$evently_dash_stat['value']  = evently_get_setting( "dash_stat_{$evently_n}_value", $evently_dash_stat['value'] );
		$evently_dash_stat['change'] = evently_get_setting( "dash_stat_{$evently_n}_change", $evently_dash_stat['change'] );
	}
	unset( $evently_dash_stat );
}

if ( ! empty( $args['button_url']['url'] ) ) {
	$evently_create_url = $args['button_url']['url'];
} else {
	$evently_create_url = evently_get_setting( 'create_event_url', '' );
	if ( empty( $evently_create_url ) ) {
		$evently_create_url = evently_has_booking_plugin() ? admin_url( 'post-new.php?post_type=mep_events' ) : '#organizer';
	}
}
?>
<section class="evently-section evently-section--dark">
	<div class="evently-container org-grid">
		<div>
			<span class="evently-eyebrow"><?php echo esc_html( $evently_eyebrow ); ?></span>
			<h2><?php echo esc_html( $evently_heading ); ?></h2>
			<p class="org-sub">
				<?php echo esc_html( $evently_subhead ); ?>
			</p>
			<div class="org-btns">
				<?php
				evently_button(
					array(
						'text'    => $evently_button_text,
						'url'     => $evently_create_url,
						'variant' => 'white',
					)
				);
				?>
				<a href="<?php echo esc_url( home_url( '/organizer-tools' ) ); ?>" class="btn btn--outline-white">
					<?php echo esc_html( $evently_secondary_text ); ?>
				</a>
			</div>
		</div>

		<div class="dashboard" aria-hidden="true">
			<div class="dashboard-title">
				<?php esc_html_e( 'Dashboard Overview', 'evently' ); ?>
				<span class="dashboard-badge"><?php esc_html_e( 'Preview', 'evently' ); ?></span>
			</div>

			<div class="dash-stats">
				<?php foreach ( $evently_dash_stats as $evently_stat ) : ?>
					<div class="dash-stat">
						<div class="dash-stat-lbl"><?php echo esc_html( $evently_stat['label'] ); ?></div>
						<div class="dash-stat-val"><?php echo esc_html( $evently_stat['value'] ); ?></div>
						<div class="dash-stat-chg"><?php echo esc_html( $evently_stat['change'] ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="dash-chart">
				<div class="dash-chart-lbl"><?php esc_html_e( 'Ticket Sales — Aug 2026', 'evently' ); ?></div>
				<svg viewBox="0 0 280 80" preserveAspectRatio="none" focusable="false">
					<defs>
						<linearGradient id="evently-chart-grad" x1="0" x2="0" y1="0" y2="1">
							<stop offset="0%" stop-color="#6C5CE7" stop-opacity="0.4" />
							<stop offset="100%" stop-color="#6C5CE7" stop-opacity="0" />
						</linearGradient>
					</defs>
					<path d="M0,60 L40,45 L80,52 L120,30 L160,38 L200,15 L240,22 L280,8 L280,80 L0,80 Z" fill="url(#evently-chart-grad)" />
					<path d="M0,60 L40,45 L80,52 L120,30 L160,38 L200,15 L240,22 L280,8" fill="none" stroke="#6C5CE7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</div>
		</div>
	</div>
</section>
