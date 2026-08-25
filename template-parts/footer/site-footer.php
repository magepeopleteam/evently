<?php
/**
 * Site footer — brand column + 3 nav columns + legal bar (brief §11 footer,
 * matches evently.html's .site-footer structure 1:1).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$evently_footer_columns = array(
	'footer-explore' => __( 'Explore', 'evently' ),
	'footer-company' => __( 'Company', 'evently' ),
	'footer-support' => __( 'Support', 'evently' ),
);

$evently_fallback_links = array(
	'footer-explore' => array(
		__( 'Events', 'evently' )     => evently_get_events_page_url(),
		__( 'Categories', 'evently' ) => home_url( '/#categories' ),
		__( 'Venues', 'evently' )     => home_url( '/' ),
		__( 'Organizers', 'evently' ) => home_url( '/' ),
	),
	'footer-company' => array(
		__( 'About', 'evently' )    => home_url( '/about' ),
		__( 'Contact', 'evently' )  => home_url( '/contact' ),
		__( 'Blog', 'evently' )     => get_post_type_archive_link( 'post' ),
		__( 'Careers', 'evently' )  => home_url( '/careers' ),
	),
	'footer-support' => array(
		__( 'Help Center', 'evently' ) => home_url( '/help' ),
		__( 'FAQ', 'evently' )         => home_url( '/faq' ),
		__( 'Terms', 'evently' )       => home_url( '/terms' ),
		__( 'Privacy', 'evently' )     => home_url( '/privacy' ),
	),
);

$evently_social_icons = array(
	'instagram' => 'IG',
	'facebook'  => 'FB',
	'x'         => 'X',
	'youtube'   => 'YT',
);
?>
<footer class="site-footer">
	<div class="evently-container">
		<div class="footer-grid">
			<div class="footer-brand">
				<div class="footer-logo"><?php evently_the_logo(); ?></div>
				<p class="footer-tagline">
					<?php echo wp_kses_post( evently_get_setting( 'footer_tagline', __( 'Discover experiences.<br>Create memories.', 'evently' ) ) ); ?>
				</p>
				<?php
				$evently_social_links = evently_get_social_links();
				if ( ! empty( $evently_social_links ) ) :
					?>
					<div class="footer-social">
						<?php foreach ( $evently_social_links as $evently_network => $evently_url ) : ?>
							<a
								href="<?php echo esc_url( $evently_url ); ?>"
								class="social-icon"
								aria-label="<?php echo esc_attr( ucfirst( $evently_network ) ); ?>"
								target="_blank"
								rel="noopener noreferrer"
							><?php echo esc_html( $evently_social_icons[ $evently_network ] ?? strtoupper( substr( $evently_network, 0, 2 ) ) ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php
			foreach ( $evently_footer_columns as $evently_location => $evently_column_title ) :
				$evently_sidebar_id     = 'evently-' . $evently_location;
				$evently_sidebar_active = is_active_sidebar( $evently_sidebar_id );
				$evently_sidebar_html   = '';

				if ( $evently_sidebar_active ) {
					ob_start();
					dynamic_sidebar( $evently_sidebar_id );
					$evently_sidebar_html = ob_get_clean();

					// A widget's own title (e.g. a Navigation Menu widget titled
					// "Company") replaces this column's static heading instead
					// of showing both stacked on top of each other.
					if ( preg_match( '#<p class="footer-widget-title">(.*?)</p>#s', $evently_sidebar_html, $evently_title_match ) ) {
						$evently_column_title = wp_strip_all_tags( $evently_title_match[1] );
						$evently_sidebar_html = preg_replace( '#<p class="footer-widget-title">.*?</p>#s', '', $evently_sidebar_html, 1 );
					}
				}
				?>
				<details class="footer-col" open>
					<summary class="footer-col-title"><?php echo esc_html( $evently_column_title ); ?></summary>
					<?php if ( $evently_sidebar_active ) : ?>
						<?php echo $evently_sidebar_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- widget output, already escaped by each widget itself. ?>
					<?php elseif ( has_nav_menu( $evently_location ) ) : ?>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => $evently_location,
								'container'      => false,
								'menu_class'      => '',
								'items_wrap'      => '<ul>%3$s</ul>',
								'depth'           => 1,
							)
						);
						?>
					<?php else : ?>
						<ul>
							<?php foreach ( $evently_fallback_links[ $evently_location ] as $evently_label => $evently_url ) : ?>
								<li><a href="<?php echo esc_url( $evently_url ); ?>"><?php echo esc_html( $evently_label ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</details>
			<?php endforeach; ?>
		</div>

		<div class="footer-bottom">
			<span class="footer-copy">
				<?php
				echo evently_get_footer_copyright(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already wp_kses_post()'d inside the helper.
				?>
			</span>
			<div class="footer-legal">
				<?php if ( has_nav_menu( 'footer-legal' ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer-legal',
							'container'      => false,
							'menu_class'     => '',
							'items_wrap'     => '%3$s',
							'depth'          => 1,
						)
					);
					?>
				<?php else : ?>
					<?php $evently_privacy_url = get_privacy_policy_url(); ?>
					<a href="<?php echo esc_url( $evently_privacy_url ? $evently_privacy_url : home_url( '/privacy' ) ); ?>"><?php esc_html_e( 'Privacy', 'evently' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/terms' ) ); ?>"><?php esc_html_e( 'Terms', 'evently' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</footer>
