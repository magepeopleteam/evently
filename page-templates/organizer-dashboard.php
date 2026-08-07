<?php
/**
 * Template Name: Evently — Organizer Dashboard
 * Template Post Type: page
 *
 * A REAL organizer dashboard (brief §23), not a mockup: every number comes
 * from Evently_Booking_Adapter's aggregation of actual mep_events_attendees/
 * WooCommerce order records for events the logged-in user authored. There
 * is no frontend event-creation/editing in the free booking plugin, so
 * "Edit" honestly links to wp-admin rather than pretending a frontend
 * editor exists — see brief §44.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$evently_container_class = 'evently-container evently-section--tight evently-organizer-dashboard-page';
?>
<div class="<?php echo esc_attr( $evently_container_class ); ?>">

	<?php if ( ! is_user_logged_in() ) : ?>

		<?php
		evently_template_part(
			'template-parts/cards/empty-state',
			'',
			array(
				'title'   => __( 'Log in to access your Organizer Dashboard', 'evently' ),
				'message' => __( "You'll need an account to view your events, tickets and attendees.", 'evently' ),
				'action'  => array(
					'text' => __( 'Log in', 'evently' ),
					'url'  => evently_has_woocommerce() && function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url( get_permalink() ),
				),
			)
		);
		?>

	<?php elseif ( ! current_user_can( 'edit_posts' ) ) : ?>

		<?php
		evently_template_part(
			'template-parts/cards/empty-state',
			'',
			array(
				'title'   => __( 'Organizer access required', 'evently' ),
				'message' => __( "Your account doesn't currently have permission to create or manage events. Contact the site administrator if you believe this is a mistake.", 'evently' ),
			)
		);
		?>

	<?php else : ?>

		<?php
		$evently_user_id    = get_current_user_id();
		$evently_event_ids  = Evently_Booking_Adapter::get_events_by_author( $evently_user_id );
		$evently_stats      = Evently_Booking_Adapter::get_organizer_stats( $evently_user_id );
		$evently_create_url = evently_has_booking_plugin() ? admin_url( 'post-new.php?post_type=mep_events' ) : '#';
		?>

		<div class="evently-org-dash-header">
			<div>
				<h1><?php esc_html_e( 'Organizer Dashboard', 'evently' ); ?></h1>
				<p><?php echo esc_html( sprintf( /* translators: %s: display name. */ __( 'Welcome back, %s.', 'evently' ), wp_get_current_user()->display_name ) ); ?></p>
			</div>
			<?php
			evently_button(
				array(
					'text'    => __( 'Create Event', 'evently' ),
					'url'     => $evently_create_url,
					'variant' => 'primary',
					'arrow'   => false,
				)
			);
			?>
		</div>

		<?php if ( ! evently_has_booking_plugin() ) : ?>

			<div class="evently-notice evently-notice--warning">
				<?php esc_html_e( 'The Organizer Dashboard requires the Evently Booking plugin to be active.', 'evently' ); ?>
			</div>

		<?php elseif ( empty( $evently_event_ids ) ) : ?>

			<?php
			evently_template_part(
				'template-parts/cards/empty-state',
				'',
				array(
					'title'   => __( "You haven't created any events yet", 'evently' ),
					'message' => __( 'Once you publish an event, your revenue, ticket sales and attendees will show up here automatically.', 'evently' ),
					'action'  => array(
						'text' => __( 'Create your first event', 'evently' ),
						'url'  => $evently_create_url,
					),
				)
			);
			?>

		<?php else : ?>

			<nav class="evently-org-dash-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Organizer dashboard sections', 'evently' ); ?>">
				<button type="button" class="evently-org-dash-tab is-active" data-evently-org-tab="overview"><?php esc_html_e( 'Overview', 'evently' ); ?></button>
				<button type="button" class="evently-org-dash-tab" data-evently-org-tab="events"><?php esc_html_e( 'My Events', 'evently' ); ?></button>
				<button type="button" class="evently-org-dash-tab" data-evently-org-tab="attendees"><?php esc_html_e( 'Attendees', 'evently' ); ?></button>
				<button type="button" class="evently-org-dash-tab" data-evently-org-tab="settings"><?php esc_html_e( 'Settings', 'evently' ); ?></button>
			</nav>

			<div class="evently-org-dash-panel" data-evently-org-panel="overview">
				<div class="evently-org-dash-stats">
					<div class="evently-org-dash-stat">
						<div class="evently-org-dash-stat__label"><?php esc_html_e( 'Revenue', 'evently' ); ?></div>
						<div class="evently-org-dash-stat__value"><?php echo wp_kses_post( evently_format_price( $evently_stats['revenue'] ) ); ?></div>
					</div>
					<div class="evently-org-dash-stat">
						<div class="evently-org-dash-stat__label"><?php esc_html_e( 'Tickets Sold', 'evently' ); ?></div>
						<div class="evently-org-dash-stat__value"><?php echo esc_html( number_format_i18n( $evently_stats['tickets'] ) ); ?></div>
					</div>
					<div class="evently-org-dash-stat">
						<div class="evently-org-dash-stat__label"><?php esc_html_e( 'Upcoming Events', 'evently' ); ?></div>
						<div class="evently-org-dash-stat__value"><?php echo esc_html( number_format_i18n( $evently_stats['upcoming_count'] ) ); ?></div>
					</div>
					<div class="evently-org-dash-stat">
						<div class="evently-org-dash-stat__label"><?php esc_html_e( 'Total Events', 'evently' ); ?></div>
						<div class="evently-org-dash-stat__value"><?php echo esc_html( number_format_i18n( $evently_stats['event_count'] ) ); ?></div>
					</div>
				</div>
				<p class="evently-org-dash-note">
					<?php esc_html_e( 'These figures are calculated from your real ticket orders — there is no simulated or placeholder data on this page.', 'evently' ); ?>
				</p>
			</div>

			<div class="evently-org-dash-panel" data-evently-org-panel="events" hidden>
				<div class="evently-org-dash-table-wrap">
					<table class="evently-org-dash-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Event', 'evently' ); ?></th>
								<th><?php esc_html_e( 'Date', 'evently' ); ?></th>
								<th><?php esc_html_e( 'Status', 'evently' ); ?></th>
								<th><?php esc_html_e( 'Tickets Sold', 'evently' ); ?></th>
								<th><?php esc_html_e( 'Revenue', 'evently' ); ?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $evently_event_ids as $evently_event_id ) : ?>
								<?php
								$evently_event_stats = Evently_Booking_Adapter::get_event_stats( $evently_event_id );
								$evently_timestamp    = Evently_Booking_Adapter::get_upcoming_timestamp( $evently_event_id );
								?>
								<tr>
									<td>
										<a href="<?php echo esc_url( get_permalink( $evently_event_id ) ); ?>" class="evently-org-dash-table__event-link">
											<?php echo esc_html( get_the_title( $evently_event_id ) ); ?>
										</a>
									</td>
									<td><?php echo $evently_timestamp ? esc_html( wp_date( 'M j, Y', $evently_timestamp ) ) : esc_html__( '—', 'evently' ); ?></td>
									<td><span class="evently-badge evently-badge--soft"><?php echo esc_html( ucfirst( get_post_status( $evently_event_id ) ) ); ?></span></td>
									<td><?php echo esc_html( number_format_i18n( $evently_event_stats['tickets'] ) ); ?></td>
									<td><?php echo wp_kses_post( evently_format_price( $evently_event_stats['revenue'] ) ); ?></td>
									<td>
										<a href="<?php echo esc_url( get_edit_post_link( $evently_event_id ) ); ?>" class="btn btn--secondary btn--sm"><?php esc_html_e( 'Edit', 'evently' ); ?></a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>

			<div class="evently-org-dash-panel" data-evently-org-panel="attendees" hidden>
				<?php $evently_attendees = Evently_Booking_Adapter::get_organizer_attendees( $evently_user_id ); ?>
				<?php if ( empty( $evently_attendees ) ) : ?>
					<p class="evently-org-dash-note"><?php esc_html_e( 'No attendees yet.', 'evently' ); ?></p>
				<?php else : ?>
					<div class="evently-org-dash-table-wrap">
						<table class="evently-org-dash-table">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Attendee', 'evently' ); ?></th>
									<th><?php esc_html_e( 'Event', 'evently' ); ?></th>
									<th><?php esc_html_e( 'Ticket', 'evently' ); ?></th>
									<th><?php esc_html_e( 'Qty', 'evently' ); ?></th>
									<th><?php esc_html_e( 'Order', 'evently' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $evently_attendees as $evently_attendee ) : ?>
									<tr>
										<td>
											<div class="evently-org-dash-table__attendee"><?php echo esc_html( $evently_attendee['name'] ); ?></div>
											<div class="evently-org-dash-table__attendee-email"><?php echo esc_html( $evently_attendee['email'] ); ?></div>
										</td>
										<td><?php echo esc_html( $evently_attendee['event_title'] ); ?></td>
										<td><?php echo esc_html( $evently_attendee['ticket_type'] ); ?></td>
										<td><?php echo esc_html( $evently_attendee['qty'] ); ?></td>
										<td>#<?php echo esc_html( $evently_attendee['order_id'] ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>

			<div class="evently-org-dash-panel" data-evently-org-panel="settings" hidden>
				<p><?php esc_html_e( 'Manage your name, email and password from your WordPress profile.', 'evently' ); ?></p>
				<?php evently_button( array( 'text' => __( 'Edit profile', 'evently' ), 'url' => admin_url( 'profile.php' ), 'variant' => 'secondary', 'arrow' => false ) ); ?>
			</div>

		<?php endif; ?>

	<?php endif; ?>

</div>
<?php
get_footer();
