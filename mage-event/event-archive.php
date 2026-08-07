<?php
/**
 * Evently's skin for the `mep_events` post type archive.
 *
 * This file lives at wp-content/themes/evently/mage-event/event-archive.php
 * — the exact relative path mage-eventpress's own
 * `MPWEM_Functions::template_path()` checks in the active theme before
 * falling back to its own bundled `templates/event-archive.php` (see
 * docs/implementation-plan.md §2.1).
 *
 * In practice `mep_events` is registered with `has_archive => false`
 * (admin/MPWEM_CPT.php), so `is_post_type_archive('mep_events')` is never
 * true through normal navigation and this file's `archive_template` hook
 * never fires on a default install — the real "browse all events" page is
 * page-templates/event-archive.php (see evently_get_events_page_url()).
 * This override is kept correct anyway, in case a future plugin/PRO
 * version enables has_archive, rather than silently relying on dead code.
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Compatibility nod to the plugin's own template, which also touches the
// main archive loop once (some SEO/meta plugins expect it) before running
// its own separate query for the actual listing.
if ( have_posts() ) {
	the_post();
	rewind_posts();
}

evently_template_part( 'template-parts/archive/event-archive-content' );

get_footer();
