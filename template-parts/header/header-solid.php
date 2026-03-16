<?php
/**
 * Header Style 2 – Solid / Default.
 *
 * Dark solid background, sticky navigation.
 * Used on normal pages and archives.
 *
 * @package Hello_Cine Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hello_movieengine_has_me       = hello_movieengine_is_movie_engine_active();
$hello_movieengine_container_cl = hello_movieengine_header_container_class();
?>
<header id="masthead" class="hello-movieengine-header hello-movieengine-header--solid" role="banner">
	<div class="hello-movieengine-header__inner <?php echo esc_attr( $hello_movieengine_container_cl ); ?>">
		<div class="hello-movieengine-header__logo">
			<?php
			if ( has_custom_logo() ) :
				the_custom_logo();
			else :
				?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hello-movieengine-header__site-title" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
				<?php
				$hello_movieengine_desc = get_bloginfo( 'description', 'display' );
				if ( $hello_movieengine_desc || is_customize_preview() ) :
					?>
					<p class="hello-movieengine-header__site-desc"><?php echo esc_html( $hello_movieengine_desc ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<nav id="site-navigation" class="hello-movieengine-header__nav" aria-label="<?php esc_attr_e( 'Primary Menu', 'hello-movieengine' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'menu_id'        => 'primary-menu',
				'menu_class'     => 'hello-movieengine-nav-menu',
				'container'      => false,
				'fallback_cb'    => false,
				'depth'          => 3,
			) );
			?>
		</nav>

		<div class="hello-movieengine-header__actions">
			<?php if ( get_theme_mod( 'hello_movieengine_header_search', true ) ) : ?>
			<div class="hello-movieengine-header__search">
				<button class="hello-movieengine-header__search-toggle" aria-label="<?php esc_attr_e( 'Search', 'hello-movieengine' ); ?>" aria-expanded="false">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
				</button>
				<div class="hello-movieengine-header__search-box">
					<?php if ( $hello_movieengine_has_me ) : ?>
						<?php echo do_shortcode( '[movie_engine_live_search]' ); ?>
					<?php else : ?>
						<?php get_search_form(); ?>
					<?php endif; ?>
					<button class="hello-movieengine-header__search-close" aria-label="<?php esc_attr_e( 'Close search', 'hello-movieengine' ); ?>">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
					</button>
				</div>
			</div>
			<?php endif; ?>

			<?php if ( get_theme_mod( 'hello_movieengine_header_user_menu', false ) ) : ?>
				<?php if ( $hello_movieengine_has_me ) : ?>
					<div class="hello-movieengine-header__user-menu">
						<?php echo do_shortcode( '[movie_engine_user_menu]' ); ?>
					</div>
				<?php elseif ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( admin_url() ); ?>" class="hello-movieengine-header__user-btn" aria-label="<?php esc_attr_e( 'My Account', 'hello-movieengine' ); ?>">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="hello-movieengine-header__user-btn" aria-label="<?php esc_attr_e( 'Log In', 'hello-movieengine' ); ?>">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
					</a>
				<?php endif; ?>
			<?php endif; ?>

		<button class="hello-movieengine-header__mobile-toggle" aria-controls="site-navigation" aria-expanded="false" aria-label="<?php esc_attr_e( 'Menu', 'hello-movieengine' ); ?>">
			<span class="hello-movieengine-hamburger" aria-hidden="true">
				<span></span>
				<span></span>
				<span></span>
			</span>
		</button>
		</div>
	</div>
</header>
