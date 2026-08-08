<?php
/**
 * Evently override of mage-eventpress templates/layout/date_list.php.
 *
 * Same date/URL logic as the plugin; markup is a modern calendar-row list
 * (month badge + day + label + time) for Default Theme sidebar and the
 * Evently Theme schedule section.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$event_id                  = $event_id ?? 0;
$event_infos               = $event_infos ?? array();
$event_infos               = ( is_array( $event_infos ) && ! empty( $event_infos ) ) ? $event_infos : MPWEM_Functions::get_all_info( $event_id );
$all_dates                 = is_array( $event_infos ) && array_key_exists( 'all_date', $event_infos ) ? $event_infos['all_date'] : array();
$all_dates                 = ( is_array( $all_dates ) && ! empty( $all_dates ) ) ? $all_dates : MPWEM_Functions::get_dates( $event_id );
$upcoming_date             = is_array( $event_infos ) && array_key_exists( 'upcoming_date', $event_infos ) ? $event_infos['upcoming_date'] : '';
$mep_show_end_datetime     = is_array( $event_infos ) && array_key_exists( 'mep_show_end_datetime', $event_infos ) ? $event_infos['mep_show_end_datetime'] : 'yes';
$_single_event_setting_sec = is_array( $event_infos ) && array_key_exists( 'single_event_setting_sec', $event_infos ) ? $event_infos['single_event_setting_sec'] : array();
$single_event_setting_sec  = is_array( $_single_event_setting_sec ) && ! empty( $_single_event_setting_sec ) ? $_single_event_setting_sec : array();
$hide_date_list            = is_array( $single_event_setting_sec ) && array_key_exists( 'mep_event_hide_event_schedule_details', $single_event_setting_sec ) ? $single_event_setting_sec['mep_event_hide_event_schedule_details'] : 'no';
$date_count                = 0;

if ( ! ( is_array( $all_dates ) && ! empty( $all_dates ) && 'no' === $hide_date_list ) ) {
	return;
}

$evently_selected = isset( $_GET['date'] ) ? absint( wp_unslash( $_GET['date'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only highlight of current selection.

/**
 * Render one modern schedule row.
 *
 * @param string $event_url   Permalink with date query args.
 * @param int    $start_ts    Start timestamp.
 * @param int    $end_ts      End timestamp (0 if none).
 * @param bool   $show_end    Whether to show end time.
 * @param bool   $is_active   Currently selected date.
 * @param bool   $collapsed   Hidden behind "view more".
 * @return void
 */
$evently_render_date_row = static function ( $event_url, $start_ts, $end_ts, $show_end, $is_active, $collapsed ) {
	$month = wp_date( 'M', $start_ts );
	$day   = wp_date( 'j', $start_ts );
	$dow   = wp_date( 'l', $start_ts );
	$full  = wp_date( 'F j, Y', $start_ts );
	$start = wp_date( get_option( 'time_format' ), $start_ts );
	$end   = ( $end_ts && $show_end ) ? wp_date( get_option( 'time_format' ), $end_ts ) : '';

	$time_label = $end ? sprintf( '%s – %s', $start, $end ) : $start;
	?>
	<div
		class="date-list-item evently-date-row<?php echo $is_active ? ' is-active' : ''; ?>"
		<?php if ( $collapsed ) : ?>
			data-collapse="#mpwem_more_date"
		<?php endif; ?>
	>
		<a class="evently-date-row__link" href="<?php echo esc_url( $event_url ); ?>">
			<span class="evently-date-row__badge" aria-hidden="true">
				<span class="evently-date-row__month"><?php echo esc_html( $month ); ?></span>
				<span class="evently-date-row__day"><?php echo esc_html( $day ); ?></span>
			</span>
			<span class="evently-date-row__body">
				<span class="evently-date-row__dow"><?php echo esc_html( $dow ); ?></span>
				<span class="evently-date-row__full"><?php echo esc_html( $full ); ?></span>
				<span class="evently-date-row__time">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
					<?php echo esc_html( $time_label ); ?>
				</span>
			</span>
			<span class="evently-date-row__chevron" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
			</span>
		</a>
	</div>
	<?php
};
?>
<div class="date_list_area evently-date-list">
	<?php
	$date_type = is_array( $event_infos ) && array_key_exists( 'mep_enable_recurring', $event_infos ) ? $event_infos['mep_enable_recurring'] : 'no';

	if ( 'no' === $date_type || 'yes' === $date_type ) {
		foreach ( $all_dates as $dates ) {
			$start_time = is_array( $dates ) && array_key_exists( 'time', $dates ) ? $dates['time'] : '';
			$end_time   = is_array( $dates ) && array_key_exists( 'end', $dates ) ? $dates['end'] : '';
			if ( ! $start_time ) {
				continue;
			}

			$start_ts  = strtotime( $start_time );
			$end_ts    = $end_time ? strtotime( $end_time ) : 0;
			$event_url = add_query_arg(
				array(
					'action'   => 'mpwem_date_' . $event_id,
					'date'     => $start_ts,
					'_wpnonce' => wp_create_nonce( 'mpwem_date_' . $event_id ),
				),
				get_the_permalink( $event_id )
			);
			$is_active = $evently_selected && (int) $evently_selected === (int) $start_ts;
			if ( ! $evently_selected && 0 === $date_count ) {
				$is_active = true;
			}

			$evently_render_date_row(
				$event_url,
				$start_ts,
				$end_ts,
				( 'yes' === $mep_show_end_datetime ),
				$is_active,
				$date_count > 4
			);
			++$date_count;
		}
	} else {
		foreach ( $all_dates as $date ) {
			$all_times = MPWEM_Functions::get_times( $event_id, $all_dates, $date );
			$base_url  = add_query_arg(
				array(
					'action'   => 'mpwem_date_' . $event_id,
					'date'     => strtotime( $date ),
					'_wpnonce' => wp_create_nonce( 'mpwem_date_' . $event_id ),
				),
				get_the_permalink( $event_id )
			);

			if ( is_array( $all_times ) && ! empty( $all_times ) ) {
				foreach ( $all_times as $times ) {
					$time_info = is_array( $times ) && array_key_exists( 'start', $times ) ? $times['start'] : array();
					if ( ! is_array( $time_info ) || empty( $time_info ) ) {
						continue;
					}
					$time = isset( $time_info['time'] ) ? $time_info['time'] : '';
					if ( ! $time ) {
						continue;
					}
					$full_date = $date . ' ' . $time;
					$start_ts  = strtotime( $full_date );
					$event_url = add_query_arg(
						array(
							'action'   => 'mpwem_date_' . $event_id,
							'date'     => $start_ts,
							'_wpnonce' => wp_create_nonce( 'mpwem_date_' . $event_id ),
						),
						get_the_permalink( $event_id )
					);
					$is_active = $evently_selected && (int) $evently_selected === (int) $start_ts;
					if ( ! $evently_selected && 0 === $date_count ) {
						$is_active = true;
					}

					$evently_render_date_row(
						$event_url,
						$start_ts,
						0,
						false,
						$is_active,
						$date_count > 4
					);
					++$date_count;
				}
			} else {
				$start_ts  = strtotime( $date );
				$is_active = $evently_selected && (int) $evently_selected === (int) $start_ts;
				if ( ! $evently_selected && 0 === $date_count ) {
					$is_active = true;
				}
				$evently_render_date_row(
					$base_url,
					$start_ts,
					0,
					false,
					$is_active,
					$date_count > 4
				);
				++$date_count;
			}
		}
	}
	?>
</div>
<?php if ( $date_count > 4 ) : ?>
	<button
		type="button"
		class="evently-date-list__more _button_theme_margin_auto"
		data-collapse-target="#mpwem_more_date"
		data-open-text="<?php esc_attr_e( 'Hide Date Lists', 'mage-eventpress' ); ?>"
		data-close-text="<?php esc_attr_e( 'View More Dates', 'mage-eventpress' ); ?>"
	>
		<span data-text><?php esc_html_e( 'View More Dates', 'mage-eventpress' ); ?></span>
	</button>
<?php endif; ?>
