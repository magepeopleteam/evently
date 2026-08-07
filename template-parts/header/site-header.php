<?php
/**
 * Site header — logo, primary nav, search trigger, log in, Create Event CTA,
 * and the mobile menu drawer. Matches the Figma source's sticky/blend-then-
 * solidify header behavior (assets/js/navigation.js adds `.is-scrolled`).
 *
 * @package Evently
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// WooCommerce's My Account page already branches between its own login form
// and the account dashboard for us, so a single URL covers both states.
if ( evently_has_woocommerce() && function_exists( 'wc_get_page_permalink' ) ) {
	$evently_account_url = wc_get_page_permalink( 'myaccount' );
} else {
	$evently_account_url = is_user_logged_in() ? admin_url( 'profile.php' ) : wp_login_url();
}

$evently_create_event_url = evently_get_setting( 'create_event_url', '' );
if ( empty( $evently_create_event_url ) ) {
	if ( evently_has_booking_plugin() && current_user_can( 'edit_posts' ) ) {
		$evently_create_event_url = admin_url( 'post-new.php?post_type=mep_events' );
	} else {
		$evently_create_event_url = evently_has_booking_plugin() ? wp_login_url( admin_url( 'post-new.php?post_type=mep_events' ) ) : '#organizer';
	}
}
?>
<header class="site-header" id="evently-site-header" data-evently-header>
	<div class="evently-container site-header__inner">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<?php bloginfo( 'name' ); ?>
			<?php endif; ?>
		</a>

		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'evently' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'site-nav__list',
						'items_wrap'     => '%3$s',
						'depth'          => 1,
					)
				);
				?>
			</nav>
		<?php else : ?>
			<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'evently' ); ?>">
				<?php
				$evently_default_nav = array(
					__( 'Events', 'evently' )      => evently_get_events_page_url(),
					__( 'Categories', 'evently' )  => home_url( '/' ) . '#categories',
					__( 'Venues', 'evently' )      => home_url( '/' ),
					__( 'Organizers', 'evently' )  => home_url( '/' ),
				);
				foreach ( $evently_default_nav as $evently_label => $evently_url ) :
					?>
					<a href="<?php echo esc_url( $evently_url ); ?>"><?php echo esc_html( $evently_label ); ?></a>
					<?php
				endforeach;
				?>
			</nav>
		<?php endif; ?>

		<div class="header-spacer"></div>

		<div class="header-actions">
			<button type="button" class="header-search-btn" aria-label="<?php esc_attr_e( 'Search events', 'evently' ); ?>" data-evently-modal-trigger="evently-search-modal">
				<?php evently_icon( 'search' ); ?>
			</button>
			<a href="<?php echo esc_url( $evently_account_url ); ?>" class="header-login">
				<?php is_user_logged_in() ? esc_html_e( 'My Account', 'evently' ) : esc_html_e( 'Log in', 'evently' ); ?>
			</a>
			<?php
			evently_button(
				array(
					'text'    => __( 'Create Event', 'evently' ),
					'url'     => $evently_create_event_url,
					'variant' => 'primary',
					'arrow'   => false,
					'size'    => 'sm',
				)
			);
			?>
		</div>

		<button
			type="button"
			class="mobile-menu-btn"
			aria-label="<?php esc_attr_e( 'Menu', 'evently' ); ?>"
			aria-expanded="false"
			aria-controls="evently-mobile-nav"
			data-evently-mobile-toggle
		>
			<span class="mobile-menu-btn__icon-open"><?php evently_icon( 'menu' ); ?></span>
			<span class="mobile-menu-btn__icon-close"><?php evently_icon( 'close' ); ?></span>
		</button>
	</div>

	<div class="mobile-nav" id="evently-mobile-nav" data-evently-mobile-nav hidden>
		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => '',
					'items_wrap'     => '%3$s',
					'depth'          => 1,
				)
			);
			?>
		<?php else : ?>
			<a href="<?php echo esc_url( evently_get_events_page_url() ); ?>"><?php esc_html_e( 'Events', 'evently' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/#categories' ) ); ?>"><?php esc_html_e( 'Categories', 'evently' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Venues', 'evently' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Organizers', 'evently' ); ?></a>
		<?php endif; ?>
		<div class="mobile-nav-actions">
			<a href="<?php echo esc_url( $evently_account_url ); ?>"><?php is_user_logged_in() ? esc_html_e( 'My Account', 'evently' ) : esc_html_e( 'Log in', 'evently' ); ?></a>
			<a href="<?php echo esc_url( $evently_create_event_url ); ?>" class="mobile-nav-actions__cta"><?php esc_html_e( 'Create Event', 'evently' ); ?></a>
		</div>
	</div>
</header>
