<?php
/**
 * Evently's skin for the single `mep_events` page (brief §16).
 *
 * Overrides the plugin's templates/single-events.php — same override
 * mechanism as mage-event/event-archive.php (see that file's header).
 * Presentational sections (hero, meta, description, FAQ, related events,
 * organizer card) are built fresh with Evently's design system, reading
 * data only through Evently_Booking_Adapter. The ticket-selection/
 * add-to-cart form is different: it's real transactional business logic
 * (early-bird windows, member gating, cart state, WooCommerce vs. native
 * checkout), so it is NOT rebuilt — Evently_Booking_Adapter::render_booking_form()
 * outputs the plugin's own real form via its public `mpwem_registration`
 * hook, and assets/css/single-event.css restyles that real markup to match
 * the sticky "BOOK THIS EVENT" card design.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

the_post();
$evently_event_id = get_the_ID();

if ( post_password_required() ) {
	?>
	<div class="evently-container evently-section">
		<?php echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core function, already safe. ?>
	</div>
	<?php
	get_footer();
	return;
}

$evently_meta        = Evently_Booking_Adapter::get_event_meta( $evently_event_id );
$evently_timestamp   = Evently_Booking_Adapter::get_upcoming_timestamp( $evently_event_id, $evently_meta );
$evently_category    = Evently_Booking_Adapter::get_category_label( $evently_event_id );
$evently_location    = Evently_Booking_Adapter::get_location_string( $evently_event_id, $evently_meta );
$evently_address     = Evently_Booking_Adapter::get_address( $evently_event_id );
$evently_organizer   = Evently_Booking_Adapter::get_organizer_term( $evently_event_id );
$evently_faqs        = Evently_Booking_Adapter::get_faqs( $evently_event_id );
$evently_min_price   = Evently_Booking_Adapter::get_min_price( $evently_event_id );
$evently_availability = Evently_Booking_Adapter::get_availability_status( $evently_event_id );
$evently_speaker_ids = Evently_Booking_Adapter::get_speaker_ids( $evently_event_id );

do_action( 'evently_before_event_content' );
?>

<article class="evently-single-event">

	<div class="evently-event-hero">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="evently-event-hero__media">
				<?php the_post_thumbnail( 'evently-hero', array( 'loading' => 'eager' ) ); ?>
			</div>
		<?php endif; ?>

		<div class="evently-container evently-event-hero__content">
			<p class="evently-breadcrumb evently-breadcrumb--on-media">
				<a href="<?php echo esc_url( evently_get_events_page_url() ); ?>"><?php esc_html_e( 'All Events', 'evently' ); ?></a>
				<span aria-hidden="true">/</span>
				<span><?php the_title(); ?></span>
			</p>

			<?php if ( $evently_category ) : ?>
				<span class="evently-badge evently-badge--accent evently-event-hero__cat"><?php echo esc_html( $evently_category ); ?></span>
			<?php endif; ?>

			<h1 class="evently-event-hero__title"><?php the_title(); ?></h1>

			<div class="evently-event-hero__meta">
				<?php if ( $evently_timestamp ) : ?>
					<span class="evently-event-hero__meta-item">
						<?php evently_icon( 'calendar' ); ?>
						<?php echo esc_html( wp_date( 'M j, Y', $evently_timestamp ) ); ?>
					</span>
					<span class="evently-event-hero__meta-item">
						<?php evently_icon( 'ticket' ); ?>
						<?php echo esc_html( wp_date( get_option( 'time_format' ), $evently_timestamp ) ); ?>
					</span>
				<?php endif; ?>
				<?php if ( $evently_location ) : ?>
					<span class="evently-event-hero__meta-item">
						<?php evently_icon( 'pin' ); ?>
						<?php echo esc_html( $evently_location ); ?>
					</span>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="evently-container evently-event-layout">
		<div class="evently-event-main">

			<?php if ( Evently_Booking_Adapter::has_gallery( $evently_event_id ) ) : ?>
				<section class="evently-event-section evently-event-gallery">
					<h2><?php esc_html_e( 'Gallery', 'evently' ); ?></h2>
					<?php
					/*
					 * The `mpwem_style` wrapper here is load-bearing, not decoration:
					 * the plugin's own frontend JS (mpwem_global.js) only resolves a
					 * slide's `data-bg-image` into a visible background when it's
					 * inside a `.mpwem_style` ancestor — the plugin's own base
					 * template wraps its whole page in that class, but this file
					 * replaces that template outright, so nothing here would ever
					 * show an image without it. Deliberately scoped to just this
					 * one widget (not the whole page) — .mpwem_style also carries a
					 * blanket `h1–h6` reset that collides with Evently's own section
					 * headings if it wraps anything wider than the plugin's own markup.
					 */
					?>
					<div class="mpwem_style">
						<?php echo Evently_Booking_Adapter::render_hook_widget( 'mpwem_custom_slider', $evently_event_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted plugin-rendered markup, see render_booking_form() docblock. ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( get_the_content() ) : ?>
				<section class="evently-event-section">
					<h2><?php esc_html_e( 'About this event', 'evently' ); ?></h2>
					<div class="evently-event-description">
						<?php the_content(); ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( Evently_Booking_Adapter::has_timeline( $evently_event_id ) ) : ?>
				<section class="evently-event-section evently-event-timeline">
					<h2><?php esc_html_e( 'Event timeline', 'evently' ); ?></h2>
					<?php
					// mpwem_style wrapper: same reason as the Gallery section above —
					// the plugin's collapse/expand accordion CSS (only the first entry
					// open by default) is scoped to a .mpwem_style ancestor.
					?>
					<div class="mpwem_style">
						<?php echo Evently_Booking_Adapter::render_hook_widget( 'mpwem_timeline', $evently_event_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted plugin-rendered markup, see render_booking_form() docblock. ?>
					</div>
				</section>
			<?php endif; ?>

			<?php
			// Real plugin content: multi-date schedule, via the same public hook
			// templates/themes/default-theme.php itself calls.
			$evently_schedule_html = '';
			if ( ! empty( $evently_meta['all_date'] ) && count( (array) $evently_meta['all_date'] ) > 1 ) {
				ob_start();
				do_action( 'mpwem_date_list', $evently_event_id, $evently_meta );
				$evently_schedule_html = ob_get_clean();
			}
			if ( $evently_schedule_html ) :
				?>
				<section class="evently-event-section evently-event-schedule">
					<h2><?php esc_html_e( 'Schedule', 'evently' ); ?></h2>
					<?php echo $evently_schedule_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted plugin-rendered markup, see render_booking_form() docblock. ?>
				</section>
			<?php endif; ?>

			<?php if ( ! empty( $evently_speaker_ids ) ) : ?>
				<section class="evently-event-section">
					<h2><?php esc_html_e( 'Speakers', 'evently' ); ?></h2>
					<div class="evently-speaker-grid">
						<?php foreach ( $evently_speaker_ids as $evently_speaker_id ) : ?>
							<div class="evently-speaker-card">
								<?php if ( has_post_thumbnail( $evently_speaker_id ) ) : ?>
									<div class="evently-speaker-card__avatar">
										<?php echo get_the_post_thumbnail( $evently_speaker_id, 'evently-square' ); ?>
									</div>
								<?php endif; ?>
								<h3><?php echo esc_html( get_the_title( $evently_speaker_id ) ); ?></h3>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( ! empty( $evently_address ) ) : ?>
				<section class="evently-event-section">
					<h2><?php esc_html_e( 'Venue', 'evently' ); ?></h2>
					<div class="evently-venue-card">
						<div>
							<?php if ( ! empty( $evently_address['location'] ) ) : ?>
								<div class="evently-venue-card__name"><?php echo esc_html( $evently_address['location'] ); ?></div>
							<?php endif; ?>
							<p class="evently-venue-card__address">
								<?php
								echo esc_html(
									implode(
										', ',
										array_filter(
											array(
												isset( $evently_address['street'] ) ? $evently_address['street'] : '',
												isset( $evently_address['city'] ) ? $evently_address['city'] : '',
												isset( $evently_address['state'] ) ? $evently_address['state'] : '',
												isset( $evently_address['country'] ) ? $evently_address['country'] : '',
											)
										)
									)
								);
								?>
							</p>
						</div>
						<a
							class="btn btn--secondary btn--sm"
							href="https://www.google.com/maps/search/?api=1&query=<?php echo rawurlencode( implode( ', ', array_filter( $evently_address ) ) ); ?>"
							target="_blank"
							rel="noopener noreferrer"
						>
							<?php esc_html_e( 'View on map', 'evently' ); ?>
						</a>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( ! empty( $evently_faqs ) ) : ?>
				<section class="evently-event-section">
					<h2><?php esc_html_e( 'Frequently asked questions', 'evently' ); ?></h2>
					<div class="evently-faq-list">
						<?php foreach ( $evently_faqs as $evently_faq ) : ?>
							<details class="evently-faq-item">
								<summary><?php echo esc_html( $evently_faq['question'] ); ?></summary>
								<div class="evently-faq-item__answer"><?php echo wp_kses_post( wpautop( $evently_faq['answer'] ) ); ?></div>
							</details>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( evently_get_setting( 'show_related_events', 1 ) ) : ?>
				<?php
				evently_template_part(
					'template-parts/event/related-events',
					'',
					array( 'event_id' => $evently_event_id )
				);
				?>
			<?php endif; ?>
		</div>

		<aside class="evently-event-sidebar">
			<div class="evently-booking-card" data-evently-sticky-card>
				<h2 class="evently-booking-card__title"><?php esc_html_e( 'Book this event', 'evently' ); ?></h2>

				<?php if ( $evently_availability ) : ?>
					<?php evently_badge( 'sold-out' === $evently_availability ? __( 'Sold out', 'evently' ) : __( 'Almost full', 'evently' ), 'sold-out' === $evently_availability ? 'error' : 'warning' ); ?>
				<?php endif; ?>

				<?php
				/**
				 * Fires immediately before the booking/ticket-selection form.
				 *
				 * @param int $evently_event_id
				 */
				do_action( 'evently_before_booking', $evently_event_id );

				$evently_booking_form = Evently_Booking_Adapter::render_booking_form( $evently_event_id );
				if ( $evently_booking_form ) :
					echo $evently_booking_form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted plugin-rendered markup, see render_booking_form() docblock.
				else :
					?>
					<div class="evently-notice evently-notice--warning">
						<?php esc_html_e( 'Event booking requires the Evently Booking plugin.', 'evently' ); ?>
					</div>
				<?php endif; ?>

				<?php
				/**
				 * Fires immediately after the booking/ticket-selection form.
				 *
				 * @param int $evently_event_id
				 */
				do_action( 'evently_after_booking', $evently_event_id );
				?>

				<div class="evently-booking-card__calendar">
					<?php echo Evently_Booking_Adapter::render_hook_widget( 'mpwem_add_calender', $evently_event_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted plugin-rendered markup. ?>
				</div>
			</div>

			<?php if ( $evently_organizer ) : ?>
				<div class="evently-organizer-card">
					<div class="evently-avatar evently-organizer-card__avatar" style="background-color:var(--evently-primary)">
						<?php echo esc_html( mb_substr( $evently_organizer->name, 0, 1 ) ); ?>
					</div>
					<div>
						<div class="evently-organizer-card__label"><?php esc_html_e( 'Organized by', 'evently' ); ?></div>
						<a class="evently-organizer-card__name" href="<?php echo esc_url( get_term_link( $evently_organizer ) ); ?>">
							<?php echo esc_html( $evently_organizer->name ); ?>
						</a>
					</div>
				</div>
			<?php endif; ?>
		</aside>
	</div>

</article>

<?php
do_action( 'evently_after_event_content' );

get_footer();
