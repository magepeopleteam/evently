<?php
/**
 * Evently's skin for the single `mep_events` page (brief §16).
 *
 * Overrides the plugin's templates/single-events.php — same override
 * mechanism as mage-event/event-archive.php (see that file's header).
 * Presentational sections (hero, meta, description, FAQ, reviews when Pro
 * is active, related events, organizer card) are built fresh with Evently's
 * design system, reading data only through Evently_Booking_Adapter. The
 * ticket-selection/add-to-cart form is different: it's real transactional
 * business logic (early-bird windows, member gating, cart state, WooCommerce
 * vs. native checkout), so it is NOT rebuilt —
 * Evently_Booking_Adapter::render_booking_form() outputs the plugin's own
 * real form via its public `mpwem_registration` hook, and
 * assets/css/single-event.css restyles that real markup to match the sticky
 * "BOOK THIS EVENT" card design. Reviews likewise reuse the Pro addon's
 * public hooks via Evently_Booking_Adapter::render_event_reviews().
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

$evently_meta           = Evently_Booking_Adapter::get_event_meta( $evently_event_id );
$evently_timestamp      = Evently_Booking_Adapter::get_upcoming_timestamp( $evently_event_id, $evently_meta );
$evently_category       = Evently_Booking_Adapter::get_category_label( $evently_event_id );
$evently_location       = Evently_Booking_Adapter::get_location_string( $evently_event_id, $evently_meta );
$evently_ticket_loc     = Evently_Booking_Adapter::get_ticket_location_line( $evently_event_id, $evently_meta );
$evently_address        = Evently_Booking_Adapter::get_address( $evently_event_id );
$evently_organizer      = Evently_Booking_Adapter::get_organizer_term( $evently_event_id );
$evently_faqs           = Evently_Booking_Adapter::get_faqs( $evently_event_id );
$evently_min_price      = Evently_Booking_Adapter::get_min_price( $evently_event_id );
$evently_availability   = Evently_Booking_Adapter::get_availability_status( $evently_event_id );
$evently_speaker_ids    = Evently_Booking_Adapter::get_speaker_ids( $evently_event_id );
$evently_gallery_ids    = Evently_Booking_Adapter::get_gallery_ids( $evently_event_id );
$evently_timeline_items = Evently_Booking_Adapter::get_timeline_items( $evently_event_id );
$evently_seat_stats     = Evently_Booking_Adapter::get_seat_stats( $evently_event_id );
$evently_share_html     = Evently_Booking_Adapter::render_hook_widget( 'mpwem_social', $evently_event_id );
$evently_calendar_html  = Evently_Booking_Adapter::render_hook_widget( 'mpwem_add_calender', $evently_event_id );
$evently_organizer_link = ( $evently_organizer ) ? get_term_link( $evently_organizer ) : '';
if ( is_wp_error( $evently_organizer_link ) ) {
	$evently_organizer_link = '';
}

do_action( 'evently_before_event_content' );
?>

<article class="evently-single-event">

	<div class="evently-event-hero<?php echo has_post_thumbnail() ? ' evently-event-hero--has-media' : ''; ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="evently-event-hero__media" aria-hidden="true">
				<?php the_post_thumbnail( 'evently-hero', array( 'loading' => 'eager', 'alt' => '' ) ); ?>
			</div>
		<?php endif; ?>

		<div class="evently-container evently-event-hero__content">
			<p class="evently-breadcrumb evently-breadcrumb--on-media">
				<a href="<?php echo esc_url( evently_get_events_page_url() ); ?>"><?php esc_html_e( 'All Events', 'evently' ); ?></a>
				<span class="evently-breadcrumb__sep" aria-hidden="true">/</span>
				<span><?php the_title(); ?></span>
			</p>

			<div class="evently-event-hero__intro">
				<?php if ( $evently_category ) : ?>
					<span class="evently-badge evently-event-hero__cat"><?php echo esc_html( $evently_category ); ?></span>
				<?php endif; ?>

				<h1 class="evently-event-hero__title"><?php the_title(); ?></h1>

				<div class="evently-event-hero__meta">
					<?php if ( $evently_timestamp ) : ?>
						<span class="evently-event-hero__meta-item">
							<?php evently_icon( 'calendar' ); ?>
							<?php echo esc_html( wp_date( 'M j, Y', $evently_timestamp ) ); ?>
						</span>
						<span class="evently-event-hero__meta-item">
							<?php evently_icon( 'clock' ); ?>
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
	</div>

	<div class="evently-container evently-event-layout">
		<div class="evently-event-main">

			<?php if ( get_the_content() ) : ?>
				<?php
				$evently_about_html  = apply_filters( 'the_content', get_the_content() );
				$evently_about_words = str_word_count( wp_strip_all_tags( $evently_about_html ) );
				$evently_about_more  = $evently_about_words > 200;
				?>
				<section class="evently-event-section">
					<h2><?php esc_html_e( 'About this event', 'evently' ); ?></h2>
					<div
						class="evently-event-description<?php echo $evently_about_more ? ' evently-event-description--collapsible is-collapsed' : ''; ?>"
						<?php if ( $evently_about_more ) : ?>
							data-evently-readmore
						<?php endif; ?>
					>
						<div class="evently-event-description__body">
							<?php echo $evently_about_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core the_content filters. ?>
						</div>
						<?php if ( $evently_about_more ) : ?>
							<button
								type="button"
								class="evently-read-more"
								data-evently-readmore-toggle
								aria-expanded="false"
							>
								<span data-label-more><?php esc_html_e( 'Read more', 'evently' ); ?></span>
								<span data-label-less hidden><?php esc_html_e( 'Read less', 'evently' ); ?></span>
							</button>
						<?php endif; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( ! empty( $evently_timeline_items ) ) : ?>
				<section class="evently-event-section evently-event-timeline">
					<h2><?php esc_html_e( 'Event timeline', 'evently' ); ?></h2>
					<ol class="evently-timeline">
						<?php foreach ( $evently_timeline_items as $evently_tl_index => $evently_tl_item ) : ?>
							<li class="evently-timeline__item<?php echo 0 === $evently_tl_index ? ' is-active' : ''; ?>">
								<span class="evently-timeline__dot" aria-hidden="true"></span>
								<div class="evently-timeline__card">
									<div class="evently-timeline__meta">
										<span class="evently-timeline__step"><?php echo esc_html( (string) ( $evently_tl_index + 1 ) ); ?></span>
										<?php if ( ! empty( $evently_tl_item['time'] ) ) : ?>
											<span class="evently-timeline__time"><?php echo esc_html( $evently_tl_item['time'] ); ?></span>
										<?php endif; ?>
									</div>
									<?php if ( ! empty( $evently_tl_item['title'] ) ) : ?>
										<h3 class="evently-timeline__title"><?php echo esc_html( $evently_tl_item['title'] ); ?></h3>
									<?php endif; ?>
									<?php if ( ! empty( $evently_tl_item['content'] ) ) : ?>
										<div class="evently-timeline__desc">
											<?php
											$evently_tl_content = $evently_tl_item['content'];
											// Demo/plugin rows often already wrap in <p>; only autop plain text.
											if ( false === strpos( $evently_tl_content, '<' ) ) {
												$evently_tl_content = wpautop( $evently_tl_content );
											}
											echo wp_kses_post( $evently_tl_content );
											?>
										</div>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
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

			<?php if ( ! empty( $evently_gallery_ids ) ) : ?>
				<section class="evently-event-section evently-event-gallery" data-evently-gallery>
					<div class="evently-event-gallery__head">
						<h2><?php esc_html_e( 'Gallery', 'evently' ); ?></h2>
						<div class="evently-gallery-carousel__nav" data-evently-gallery-nav></div>
					</div>
					<div
						class="evently-gallery-carousel owl-carousel owl-theme"
						data-evently-gallery-carousel
						data-nav-prev="<?php esc_attr_e( 'Previous images', 'evently' ); ?>"
						data-nav-next="<?php esc_attr_e( 'Next images', 'evently' ); ?>"
					>
						<?php
						$evently_gallery_index = 0;
						foreach ( $evently_gallery_ids as $evently_gallery_id ) :
							$evently_gallery_full = wp_get_attachment_image_url( $evently_gallery_id, 'full' );
							if ( ! $evently_gallery_full ) {
								continue;
							}
							$evently_gallery_alt = get_post_meta( $evently_gallery_id, '_wp_attachment_image_alt', true );
							if ( ! $evently_gallery_alt ) {
								$evently_gallery_alt = get_the_title( $evently_gallery_id );
							}
							?>
							<div class="evently-gallery-carousel__item">
								<button
									type="button"
									class="evently-gallery-thumb"
									data-evently-lightbox-trigger
									data-evently-lightbox-src="<?php echo esc_url( $evently_gallery_full ); ?>"
									data-evently-lightbox-alt="<?php echo esc_attr( $evently_gallery_alt ); ?>"
									data-evently-lightbox-index="<?php echo esc_attr( (string) $evently_gallery_index ); ?>"
									aria-label="<?php echo esc_attr( sprintf( /* translators: %s: image alt or title */ __( 'View larger: %s', 'evently' ), $evently_gallery_alt ) ); ?>"
								>
									<?php
									echo wp_get_attachment_image(
										$evently_gallery_id,
										'evently-square',
										false,
										array(
											'loading' => 'lazy',
											'alt'     => $evently_gallery_alt,
										)
									);
									?>
								</button>
							</div>
							<?php
							++$evently_gallery_index;
						endforeach;
						?>
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

			<?php
			/*
			 * Pro review addon (list + write/edit modal). Same public hooks the
			 * plugin's Horizon/default templates use — only rendered when Pro
			 * has loaded mep_event_review_list. Placement matches Horizon:
			 * after the main content blocks, before related events.
			 */
			$evently_reviews_html = Evently_Booking_Adapter::render_event_reviews( $evently_event_id );
			if ( $evently_reviews_html ) :
				?>
				<section class="evently-event-section evently-event-reviews" aria-label="<?php esc_attr_e( 'Event reviews', 'evently' ); ?>" data-evently-reviews>
					<div class="evently-event-reviews__head">
						<div class="evently-event-reviews__titles">
							<span class="evently-event-reviews__eyebrow"><?php esc_html_e( 'Attendee feedback', 'evently' ); ?></span>
							<h2><?php esc_html_e( 'Reviews', 'evently' ); ?></h2>
						</div>
						<div class="evently-event-reviews__actions" data-evently-reviews-actions></div>
					</div>
					<?php echo $evently_reviews_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted Pro-rendered markup, see render_event_reviews() docblock. ?>
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
			<?php
			/*
			 * Sticky ticket card mirrors Horizon's aside (horizon.php):
			 * head (eyebrow / title / location) → mpwem_registration →
			 * foot (mpwem_social + mpwem_add_calender). Same plugin hooks,
			 * Evently presentation shell.
			 */
			?>
			<div class="evently-booking-card" data-evently-sticky-card>
				<div class="evently-booking-card__head">
					<span class="evently-booking-card__eyebrow"><?php esc_html_e( 'Reserve your spot', 'evently' ); ?></span>
					<h2 class="evently-booking-card__title"><?php the_title(); ?></h2>
					<?php if ( $evently_ticket_loc ) : ?>
						<p class="evently-booking-card__location">
							<?php evently_icon( 'pin' ); ?>
							<span><?php echo esc_html( $evently_ticket_loc ); ?></span>
						</p>
					<?php endif; ?>
					<?php if ( $evently_availability ) : ?>
						<div class="evently-booking-card__badge">
							<?php evently_badge( 'sold-out' === $evently_availability ? __( 'Sold out', 'evently' ) : __( 'Almost full', 'evently' ), 'sold-out' === $evently_availability ? 'error' : 'warning' ); ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="evently-booking-card__body">
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
				</div>

				<?php if ( $evently_share_html || $evently_calendar_html ) : ?>
					<div class="evently-booking-card__foot">
						<?php if ( $evently_share_html ) : ?>
							<div class="evently-booking-card__share">
								<span class="evently-booking-card__share-label"><?php esc_html_e( 'Share', 'evently' ); ?></span>
								<?php echo $evently_share_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted plugin-rendered markup. ?>
							</div>
						<?php endif; ?>
						<?php if ( $evently_calendar_html ) : ?>
							<div class="evently-booking-card__calendar">
								<?php echo $evently_calendar_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted plugin-rendered markup. ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $evently_organizer ) : ?>
				<div class="evently-organizer-card">
					<div class="evently-organizer-card__info">
						<div class="evently-organizer-card__identity">
							<div class="evently-organizer-card__avatar" aria-hidden="true">
								<?php echo esc_html( mb_strtoupper( mb_substr( $evently_organizer->name, 0, 1 ) ) ); ?>
							</div>
							<div class="evently-organizer-card__meta">
								<small class="evently-organizer-card__label"><?php esc_html_e( 'Organized by', 'evently' ); ?></small>
								<?php if ( $evently_organizer_link ) : ?>
									<a class="evently-organizer-card__name" href="<?php echo esc_url( $evently_organizer_link ); ?>">
										<?php echo esc_html( $evently_organizer->name ); ?>
									</a>
								<?php else : ?>
									<span class="evently-organizer-card__name"><?php echo esc_html( $evently_organizer->name ); ?></span>
								<?php endif; ?>
							</div>
						</div>
						<?php if ( $evently_organizer_link ) : ?>
							<a class="evently-organizer-card__follow" href="<?php echo esc_url( $evently_organizer_link ); ?>">
								<?php esc_html_e( 'Follow', 'evently' ); ?>
							</a>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $evently_seat_stats['total'] ) || ! empty( $evently_seat_stats['available'] ) || ! empty( $evently_seat_stats['sold'] ) ) : ?>
						<div class="evently-organizer-card__stats">
							<div class="evently-seat-stat evently-seat-stat--total">
								<strong><?php echo esc_html( number_format_i18n( (int) $evently_seat_stats['total'] ) ); ?></strong>
								<span><?php esc_html_e( 'Total Seats', 'evently' ); ?></span>
							</div>
							<div class="evently-seat-stat evently-seat-stat--available">
								<strong><?php echo esc_html( number_format_i18n( (int) $evently_seat_stats['available'] ) ); ?></strong>
								<span><?php esc_html_e( 'Available', 'evently' ); ?></span>
							</div>
							<div class="evently-seat-stat evently-seat-stat--sold">
								<strong><?php echo esc_html( number_format_i18n( (int) $evently_seat_stats['sold'] ) ); ?></strong>
								<span><?php esc_html_e( 'Sold', 'evently' ); ?></span>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</aside>
	</div>

</article>

<?php if ( ! empty( $evently_gallery_ids ) ) : ?>
	<div
		id="evently-gallery-lightbox"
		class="evently-modal evently-modal--lightbox"
		data-evently-modal
		data-evently-lightbox
		role="dialog"
		aria-modal="true"
		aria-label="<?php esc_attr_e( 'Image gallery', 'evently' ); ?>"
		hidden
	>
		<button type="button" class="evently-lightbox__close" data-evently-modal-close aria-label="<?php esc_attr_e( 'Close gallery', 'evently' ); ?>">
			<?php evently_icon( 'close' ); ?>
		</button>
		<button type="button" class="evently-lightbox__nav evently-lightbox__nav--prev" data-evently-lightbox-prev aria-label="<?php esc_attr_e( 'Previous image', 'evently' ); ?>">
			<?php evently_icon( 'chevron-left' ); ?>
		</button>
		<figure class="evently-lightbox__figure">
			<img class="evently-lightbox__img" data-evently-lightbox-img src="" alt="" />
		</figure>
		<button type="button" class="evently-lightbox__nav evently-lightbox__nav--next" data-evently-lightbox-next aria-label="<?php esc_attr_e( 'Next image', 'evently' ); ?>">
			<?php evently_icon( 'chevron-right' ); ?>
		</button>
	</div>
<?php endif; ?>

<?php
do_action( 'evently_after_event_content' );

get_footer();
