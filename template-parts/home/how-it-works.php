<?php
/**
 * How It Works — 3-step process (brief §11).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Base copy lives here; Evently → Theme Settings → Homepage: How It Works
// can override each step's title/description without touching this file.
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
?>
<section class="evently-section">
	<div class="evently-container">
		<div class="evently-section-head evently-section-head--center">
			<h2><?php esc_html_e( 'Book experiences in three simple steps.', 'evently' ); ?></h2>
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
