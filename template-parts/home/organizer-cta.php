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

$evently_dash_stats  = evently_demo_dashboard_stats();
$evently_create_url  = evently_get_setting( 'create_event_url', '' );
if ( empty( $evently_create_url ) ) {
	$evently_create_url = evently_has_booking_plugin() ? admin_url( 'post-new.php?post_type=mep_events' ) : '#organizer';
}
?>
<section class="evently-section evently-section--dark">
	<div class="evently-container org-grid">
		<div>
			<span class="evently-eyebrow"><?php esc_html_e( 'For Organizers', 'evently' ); ?></span>
			<h2><?php esc_html_e( 'Turn your event into an experience.', 'evently' ); ?></h2>
			<p class="org-sub">
				<?php esc_html_e( 'Create events, sell tickets, manage attendees and track your performance from one powerful dashboard.', 'evently' ); ?>
			</p>
			<div class="org-btns">
				<?php
				evently_button(
					array(
						'text'    => __( 'Start Selling', 'evently' ),
						'url'     => $evently_create_url,
						'variant' => 'white',
					)
				);
				?>
				<a href="<?php echo esc_url( home_url( '/organizer-tools' ) ); ?>" class="btn btn--outline-white">
					<?php esc_html_e( 'Explore Organizer Tools', 'evently' ); ?>
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
