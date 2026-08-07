<?php
/**
 * Digital Ticket showcase (brief §11/§20). This is a marketing mockup of
 * what a ticket looks like — the QR pattern below is decorative (not a
 * scannable code tied to a real booking); the theme's actual My-Tickets
 * screen (brief §21) only ever renders a real QR code supplied by a
 * booking-plugin add-on, and shows an honest notice when one isn't
 * available (brief §44) — see template-parts/booking/digital-ticket.php.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_ticket_event = evently_demo_events()[0];

// A fixed decorative cell pattern — not a real QR payload.
$evently_qr_on_cells = array( 0, 1, 2, 7, 8, 9, 14, 6, 13, 20, 21, 28, 35, 42, 43, 44, 48, 47, 46, 41, 34, 27, 24, 25, 26, 17, 10, 11, 16, 23, 30, 37, 38, 31, 32, 33 );
?>
<section class="evently-section evently-section--soft">
	<div class="evently-container ticket-grid">
		<div>
			<h2><?php esc_html_e( 'Your ticket.', 'evently' ); ?><br /><?php esc_html_e( 'Your experience.', 'evently' ); ?></h2>
			<p><?php esc_html_e( 'Everything you need for your next event, right in one beautiful digital ticket.', 'evently' ); ?></p>
			<?php
			evently_button(
				array(
					'text'    => __( 'View My Tickets', 'evently' ),
					'url'     => evently_has_woocommerce() && function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'event-bookings' ) : home_url( '/' ),
					'variant' => 'primary',
				)
			);
			?>
		</div>

		<div class="ticket-ui-wrap">
			<div class="ticket-ui" aria-hidden="true">
				<div class="ticket-deco-1"></div>
				<div class="ticket-deco-2"></div>
				<div class="ticket-body">
					<div class="ticket-brand"><?php bloginfo( 'name' ); ?></div>
					<div class="ticket-event"><?php echo esc_html( mb_strtoupper( $evently_ticket_event['title'] ) ); ?></div>
					<div class="ticket-date-title"><?php echo esc_html( mb_strtoupper( $evently_ticket_event['date_label'] ) ); ?> · 2026</div>
					<div class="ticket-location"><?php echo esc_html( $evently_ticket_event['city'] ); ?></div>

					<div class="ticket-divider"></div>

					<div class="ticket-foot">
						<div>
							<div class="ticket-type-lbl"><?php esc_html_e( 'TICKET TYPE', 'evently' ); ?></div>
							<div class="ticket-type"><?php esc_html_e( 'VIP PASS', 'evently' ); ?></div>
							<div class="ticket-entry"><?php esc_html_e( 'ENTRY 06:30 PM', 'evently' ); ?></div>
						</div>
						<div class="ticket-qr">
							<?php for ( $evently_cell = 0; $evently_cell < 49; $evently_cell++ ) : ?>
								<div class="ticket-qr-cell<?php echo in_array( $evently_cell, $evently_qr_on_cells, true ) ? ' is-on' : ''; ?>"></div>
							<?php endfor; ?>
						</div>
					</div>

					<div class="ticket-num">#EVT-82931</div>
				</div>
			</div>
			<div class="ticket-shadow-card" aria-hidden="true"></div>
		</div>
	</div>
</section>
