<?php
/**
 * How It Works — 3-step process (brief §11).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_heading = $args['heading'] ?? __( 'Book experiences in three simple steps.', 'evently' );

// The Evently How It Works Elementor widget's own repeater
// (class-widget-how-it-works.php) takes priority when present; otherwise
// fall back to the legacy per-index evently_get_setting() overrides, exactly
// as this file behaved before the widget had any controls.
if ( ! empty( $args['steps'] ) && is_array( $args['steps'] ) ) {
	$evently_steps = array();
	foreach ( $args['steps'] as $evently_index => $evently_step_item ) {
		$evently_steps[] = array(
			'num'   => sprintf( '%02d', $evently_index + 1 ),
			'label' => $evently_step_item['label'],
			'desc'  => $evently_step_item['desc'],
		);
	}
} else {
	$evently_steps = array(
		array(
			'num'   => '01',
			'label' => evently_get_setting( 'step_1_label', __( 'Discover', 'evently' ) ),
			'desc'  => evently_get_setting( 'step_1_desc', __( 'Find an event you love from thousands of curated experiences worldwide.', 'evently' ) ),
		),
		array(
			'num'   => '02',
			'label' => evently_get_setting( 'step_2_label', __( 'Book', 'evently' ) ),
			'desc'  => evently_get_setting( 'step_2_desc', __( 'Choose your ticket and pay securely. Get instant confirmation.', 'evently' ) ),
		),
		array(
			'num'   => '03',
			'label' => evently_get_setting( 'step_3_label', __( 'Enjoy', 'evently' ) ),
			'desc'  => evently_get_setting( 'step_3_desc', __( 'Receive your digital ticket and enjoy the event worry-free.', 'evently' ) ),
		),
	);
}
?>
<section class="evently-section">
	<div class="evently-container">
		<div class="evently-section-head evently-section-head--center">
			<h2><?php echo esc_html( $evently_heading ); ?></h2>
		</div>

		<div class="steps-grid">
			<div class="steps-line" aria-hidden="true"></div>
			<?php foreach ( $evently_steps as $evently_step ) : ?>
				<div class="step">
					<div class="step-num"><span><?php echo esc_html( $evently_step['num'] ); ?></span></div>
					<div class="step-label"><?php echo esc_html( $evently_step['label'] ); ?></div>
					<p><?php echo esc_html( $evently_step['desc'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
