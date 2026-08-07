<?php
/**
 * Server-side render for the `evently/how-it-works` block — delegates entirely to
 * the same template-part the built-in demo homepage uses
 * (template-parts/home/how-it-works.php), so editing Evently → Theme Settings
 * always affects this block too, with zero duplicated markup.
 *
 * $attributes, $content, $block are provided by WordPress's block.json
 * "render" mechanism; unused here since this block takes no attributes.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

evently_template_part( 'template-parts/home/how-it-works' );
