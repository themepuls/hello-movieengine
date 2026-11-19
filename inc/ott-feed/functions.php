<?php
/**
 * OTT Feed (Style Two) helpers and front-end behavior.
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customizer: Style Two is the active design pack.
 *
 * @return bool
 */
function hello_movieengine_customize_is_style_two() {
	return 'style-two' === get_theme_mod(
		Hello_Movie_Engine_Style_Registry::SETTING_KEY,
		Hello_Movie_Engine_Style_Registry::DEFAULT_STYLE
	);
}

/**
 * Whether the current request is the static front page (OTT home feed layout).
 *
 * @return bool
 */
function hello_movieengine_is_feed_page() {
	if ( 'style-two' !== hello_movieengine_get_active_style() ) {
		return false;
	}

	return is_front_page();
}

/**
 * Whether the site footer should render.
 *
 * @return bool
 */
function hello_movieengine_show_site_footer() {
	if ( hello_movieengine_is_feed_page() && get_theme_mod( 'hello_movieengine_feed_hide_footer', false ) ) {
		return false;
	}

	return true;
}

/**
 * Append feed-related body classes (Style Two only).
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function hello_movieengine_ott_feed_body_class( $classes ) {
	if ( 'style-two' !== hello_movieengine_get_active_style() ) {
		return $classes;
	}

	if ( hello_movieengine_is_feed_page() ) {
		$classes[] = 'hello-movieengine-feed-page';
		if ( get_theme_mod( 'hello_movieengine_feed_main_padding_zero', true ) ) {
			$classes[] = 'hello-movieengine-feed-main-zero';
		}
	}

	if ( get_theme_mod( 'hello_movieengine_feed_top10_style', false ) ) {
		$classes[] = 'hello-movieengine-top10-rows';
	}

	if ( get_theme_mod( 'hello_movieengine_feed_studio_chips', true ) ) {
		$classes[] = 'hello-movieengine-studio-chips';
	}

	return $classes;
}
add_filter( 'body_class', 'hello_movieengine_ott_feed_body_class' );

/**
 * CSS custom properties for Style Two feed (inlined with theme vars).
 *
 * @return string Declarations without selector wrapper.
 */
function hello_movieengine_ott_feed_root_vars() {
	if ( 'style-two' !== hello_movieengine_get_active_style() ) {
		return '';
	}

	$row_gap    = max( 0, min( 120, absint( get_theme_mod( 'hello_movieengine_feed_row_gap', 28 ) ) ) );
	$row_pad    = max( 0, min( 120, absint( get_theme_mod( 'hello_movieengine_feed_row_padding_x', 20 ) ) ) );
	$title_size = (float) get_theme_mod( 'hello_movieengine_feed_section_title_size', 1.125 );
	$title_size = max( 0.75, min( 2.5, $title_size ) );
	$card_min   = max( 90, min( 260, absint( get_theme_mod( 'hello_movieengine_feed_card_min_width', 140 ) ) ) );
	$hero_min   = max( 40, min( 100, absint( get_theme_mod( 'hello_movieengine_feed_hero_min_height', 72 ) ) ) );
	$overlay    = get_theme_mod( 'hello_movieengine_feed_hero_overlay', 'rgba(0,0,0,0.45)' );
	$overlay    = hello_movieengine_sanitize_color_alpha( $overlay );
	$container  = absint( get_theme_mod( 'hello_movieengine_feed_container_width', 0 ) );
	$container  = min( 1920, $container );

	$vars  = '--hm-feed-row-gap:' . $row_gap . 'px;';
	$vars .= '--hm-feed-row-padding-x:' . $row_pad . 'px;';
	$vars .= '--hm-feed-section-title-size:' . $title_size . 'rem;';
	$vars .= '--hm-feed-card-min-width:' . $card_min . 'px;';
	$vars .= '--hm-feed-hero-min-height:' . $hero_min . 'vh;';
	$vars .= '--hm-feed-hero-overlay:' . $overlay . ';';

	if ( $container > 0 ) {
		$vars .= '--hm-feed-container-width:' . $container . 'px;';
	}

	return $vars;
}