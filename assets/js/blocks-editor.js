/**
 * Client-side registration for the 14 `evently/{slug}` homepage-section
 * blocks. Server-side registration (inc/blocks/blocks.php) makes render.php
 * work on the front end; the block editor's inserter/canvas only knows about
 * a block once it's also registered here — that's always a JS-side step in
 * WordPress, dynamic block or not.
 *
 * Deliberately hand-written against WordPress's own bundled editor globals
 * (`wp.blocks`, `wp.element`, `wp.serverSideRender`) instead of an
 * npm/webpack build — same "no framework, no build step" rule the rest of
 * the theme's JS follows. `eventlyBlockSections` is localized from the exact
 * same PHP registry (evently_get_block_sections()) used to register these
 * blocks server-side, so titles/icons/descriptions can't drift between the
 * two registrations.
 *
 * Every block is server-rendered (see render.php per block) and takes no
 * attributes, so `edit()` is just a live PHP preview via ServerSideRender
 * and `save()` returns null — WordPress stores an empty block marker and
 * always re-runs render.php on both the front end and the editor canvas.
 *
 * @package Evently
 */
( function ( blocks, element, serverSideRender, i18n ) {
	'use strict';

	if ( typeof eventlyBlockSections === 'undefined' ) {
		return;
	}

	var el = element.createElement;
	var __ = i18n.__;
	var ServerSideRender = serverSideRender.default || serverSideRender;

	Object.keys( eventlyBlockSections ).forEach( function ( slug ) {
		var section = eventlyBlockSections[ slug ];
		var name = 'evently/' + slug;

		blocks.registerBlockType( name, {
			apiVersion: 3,
			title: section.title,
			category: 'evently',
			icon: section.icon || 'layout',
			description: section.description || '',
			supports: { html: false },

			edit: function () {
				return el(
					'div',
					{ className: 'evently-block-preview' },
					el(
						'div',
						{ className: 'evently-block-preview__label' },
						section.title
					),
					el( ServerSideRender, { block: name } )
				);
			},

			save: function () {
				return null;
			},
		} );
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.i18n );
