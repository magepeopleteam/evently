<?php
/**
 * Event Calendar (brief §11) — a minimal, editorial month-grid, not a
 * generic calendar-plugin widget. Client-side day switching is handled by
 * assets/js/calendar.js (progressive enhancement — every day panel exists
 * in the DOM already, so nothing breaks without JS).
 *
 * When the booking plugin is active, brief §17's adapter is the correct
 * place to eventually source real per-day events from
 * MPWEM_Functions::get_all_dates(); until then this renders the curated
 * demo month so the section never looks broken on a fresh install.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_heading       = $args['heading'] ?? __( "What's happening this month", 'evently' );
$evently_month_label   = $args['month_label'] ?? evently_get_setting( 'calendar_month_label', __( 'August 2026', 'evently' ) );
$evently_days_in_month = 31;
$evently_first_weekday = 6; // Saturday — matches the design's Aug 2026 layout.
$evently_calendar_data = evently_demo_calendar_events();
$evently_selected_day  = 24;
$evently_day_names     = array( __( 'S', 'evently' ), __( 'M', 'evently' ), __( 'T', 'evently' ), __( 'W', 'evently' ), __( 'T', 'evently' ), __( 'F', 'evently' ), __( 'S', 'evently' ) );
?>
<section class="evently-section evently-section--soft" id="evently-calendar">
	<div class="evently-container">
		<div class="evently-section-head evently-section-head--center">
			<h2><?php echo esc_html( $evently_heading ); ?></h2>
		</div>

		<div class="cal-grid" data-evently-calendar>
			<div class="cal-widget">
				<div class="cal-header">
					<h3 class="cal-month"><?php echo esc_html( $evently_month_label ); ?></h3>
					<div class="cal-nav">
						<button type="button" aria-label="<?php esc_attr_e( 'Previous month', 'evently' ); ?>" disabled><?php evently_icon( 'chevron-left' ); ?></button>
						<button type="button" aria-label="<?php esc_attr_e( 'Next month', 'evently' ); ?>" disabled><?php evently_icon( 'chevron-right' ); ?></button>
					</div>
				</div>

				<div class="cal-days-head">
					<?php foreach ( $evently_day_names as $evently_day_name ) : ?>
						<div class="cal-day-name"><?php echo esc_html( $evently_day_name ); ?></div>
					<?php endforeach; ?>
				</div>

				<div class="cal-days" role="group" aria-label="<?php echo esc_attr( $evently_month_label ); ?>">
					<?php for ( $evently_blank = 0; $evently_blank < $evently_first_weekday; $evently_blank++ ) : ?>
						<div></div>
					<?php endfor; ?>
					<?php for ( $evently_day = 1; $evently_day <= $evently_days_in_month; $evently_day++ ) : ?>
						<?php $evently_has_event = ! empty( $evently_calendar_data[ $evently_day ] ); ?>
						<button
							type="button"
							class="cal-day<?php echo $evently_day === $evently_selected_day ? ' is-selected' : ''; ?>"
							data-evently-day="<?php echo esc_attr( $evently_day ); ?>"
							aria-pressed="<?php echo $evently_day === $evently_selected_day ? 'true' : 'false'; ?>"
							<?php if ( ! $evently_has_event ) : ?>aria-label="<?php echo esc_attr( sprintf( /* translators: %d: day of month. */ __( '%d, no events', 'evently' ), $evently_day ) ); ?>"<?php endif; ?>
						>
							<?php echo esc_html( $evently_day ); ?>
							<?php if ( $evently_has_event ) : ?><span class="cal-dot" aria-hidden="true"></span><?php endif; ?>
						</button>
					<?php endfor; ?>
				</div>
			</div>

			<div class="cal-events-panel">
				<?php if ( empty( $evently_calendar_data ) ) : ?>
					<div class="cal-empty"><?php esc_html_e( 'Select a date to see events', 'evently' ); ?></div>
				<?php else : ?>
					<?php foreach ( $evently_calendar_data as $evently_day => $evently_day_events ) : ?>
						<div
							class="cal-day-events"
							data-evently-day-panel="<?php echo esc_attr( $evently_day ); ?>"
							<?php echo $evently_day === $evently_selected_day ? '' : 'hidden'; ?>
						>
							<div class="cal-event-date">
								<?php
								printf(
									/* translators: 1: month name, 2: day of month. */
									esc_html__( '%1$s %2$d', 'evently' ),
									esc_html( wp_date( 'F', strtotime( '2026-08-01' ) ) ),
									(int) $evently_day
								);
								?>
							</div>
							<div class="cal-event-list">
								<?php foreach ( $evently_day_events as $evently_cal_event ) : ?>
									<div class="cal-event-row">
										<div>
											<h4><?php echo esc_html( $evently_cal_event['title'] ); ?></h4>
											<p><?php echo esc_html( $evently_cal_event['location'] ); ?> · <?php echo esc_html( $evently_cal_event['price'] ); ?></p>
										</div>
										<?php
										evently_button(
											array(
												'text'    => __( 'Book', 'evently' ),
												'url'     => evently_get_events_page_url(),
												'variant' => 'primary',
												'size'    => 'sm',
											)
										);
										?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
					<div class="cal-empty" data-evently-day-empty <?php echo isset( $evently_calendar_data[ $evently_selected_day ] ) ? 'hidden' : ''; ?>>
						<?php esc_html_e( 'Select a date to see events', 'evently' ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
