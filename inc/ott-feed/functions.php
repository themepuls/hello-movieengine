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
/**
 * Front page using the Home Feed builder (Style One or Two).
 *
 * @return bool
 */
function hello_movieengine_is_home_feed_builder() {
	if ( ! is_front_page() ) {
		return false;
	}

	$mode = get_theme_mod( 'hello_movieengine_home_mode', 'editor' );
	return in_array( $mode, array( 'builder', 'hybrid' ), true );
}

function hello_movieengine_is_feed_page() {
	return hello_movieengine_is_home_feed_builder();
}

/**
 * Page (or CPT archive) that outputs [movie_engine_library] with search on.
 *
 * @return bool
 */
function hello_movieengine_is_style_two_browse() {
	if ( 'style-two' !== hello_movieengine_get_active_style() ) {
		return false;
	}

	if ( is_front_page() ) {
		return false;
	}

	if ( is_post_type_archive( array( 'movie_engine_movie', 'movie_engine_series' ) ) ) {
		return true;
	}

	if ( ! is_singular() ) {
		return false;
	}

	$post = get_post();
	if ( ! $post ) {
		return false;
	}

	$content = $post->post_content;

	if ( has_shortcode( $content, 'movie_engine_search' ) ) {
		return true;
	}

	if ( function_exists( 'has_block' ) && has_block( 'cine-movie-engine/search', $post ) ) {
		return true;
	}

	if ( has_shortcode( $content, 'movie_engine_library' )
		&& preg_match( '/\[movie_engine_library[^\]]*show_(?:search|filters)=(["\'])true\1/i', $content ) ) {
		return true;
	}

	if ( function_exists( 'has_block' ) && has_block( 'cine-movie-engine/library', $post ) ) {
		return hello_movieengine_block_has_library_search( parse_blocks( $content ) );
	}

	return false;
}

/**
 * Whether a Gutenberg library block has search enabled.
 *
 * @param array $blocks Parsed blocks.
 * @return bool
 */
function hello_movieengine_block_has_library_search( $blocks ) {
	foreach ( $blocks as $block ) {
		$name = $block['blockName'] ?? '';
		if ( 'cine-movie-engine/library' === $name ) {
			$attrs = $block['attrs'] ?? array();
			if ( ! empty( $attrs['showSearch'] ) || ! empty( $attrs['showFilters'] ) ) {
				return true;
			}
		}
		if ( ! empty( $block['innerBlocks'] ) && hello_movieengine_block_has_library_search( $block['innerBlocks'] ) ) {
			return true;
		}
	}

	return false;
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
	$is_style_two = ( 'style-two' === hello_movieengine_get_active_style() );

	if ( hello_movieengine_is_feed_page() ) {
		$classes[] = 'hello-movieengine-feed-page';
	}

	if ( ! $is_style_two ) {
		return $classes;
	}

	if ( hello_movieengine_is_style_two_browse() ) {
		$classes[] = 'hello-me-browse-page';
	}

	if ( get_theme_mod( 'hello_movieengine_feed_main_padding_zero', true ) ) {
		$classes[] = 'hello-movieengine-feed-main-zero';
	}

	if ( get_theme_mod( 'hello_movieengine_feed_top10_style', false ) ) {
		$classes[] = 'hello-movieengine-top10-rows';
	}

	if ( get_theme_mod( 'hello_movieengine_feed_studio_chips', true ) ) {
		$classes[] = 'hello-movieengine-studio-chips';
	}

	if ( is_singular( 'movie_engine_movie' ) ) {
		$design = get_option( 'movie_engine_design_settings', array() );
		if ( ( $design['movie_single_layout'] ?? 'fullwidth' ) === 'inline' ) {
			$classes[] = 'hello-me-watch-page';
		}
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

/**
 * Studio taxonomy type tabs (Style Two).
 */
function hello_movieengine_enqueue_studio_tax_tabs() {
	if ( 'style-two' !== hello_movieengine_get_active_style() ) {
		return;
	}

	if ( ! function_exists( 'hello_movieengine_is_studio_taxonomy' ) || ! hello_movieengine_is_studio_taxonomy() ) {
		return;
	}

	$path = Hello_Movie_Engine_Style_Registry::style_path( 'js/studio-tax-tabs.js' );
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_script(
		'hello-movieengine-studio-tax-tabs',
		Hello_Movie_Engine_Style_Registry::style_uri( 'js/studio-tax-tabs.js' ),
		array( 'jquery', 'movie-engine' ),
		filemtime( $path ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'hello_movieengine_enqueue_studio_tax_tabs', 25 );

/**
 * Single movie inline watch — reveal full-width player on Watch Now (Style Two).
 */
function hello_movieengine_enqueue_single_watch() {
	if ( 'style-two' !== hello_movieengine_get_active_style() ) {
		return;
	}

	if ( ! is_singular( 'movie_engine_movie' ) ) {
		return;
	}

	$design = get_option( 'movie_engine_design_settings', array() );
	if ( ( $design['movie_single_layout'] ?? 'fullwidth' ) !== 'inline' ) {
		return;
	}

	$path = Hello_Movie_Engine_Style_Registry::style_path( 'js/single-watch.js' );
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_script(
		'hello-movieengine-single-watch',
		Hello_Movie_Engine_Style_Registry::style_uri( 'js/single-watch.js' ),
		array( 'hello-movieengine-watch-hero-back' ),
		filemtime( $path ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'hello_movieengine_enqueue_single_watch' );

/**
 * Style Two only: swap plugin watch heroes for theme wrappers that inject Back (plugin files untouched).
 *
 * @param string $template Resolved template path.
 * @param string $slug     Template slug.
 * @param string $name     Template name.
 * @return string
 */
function hello_movieengine_style_two_watch_hero_template_part( $template, $slug, $name ) {
	if ( 'style-two' !== hello_movieengine_get_active_style() || ! $template ) {
		return $template;
	}

	$base = wp_basename( $template );

	if ( 'hero-fullwidth.php' === $base && false !== strpos( $template, 'single-movie' ) ) {
		$theme_wrapper = HELLO_MOVIEENGINE_DIR . '/inc/templates/movie-engine/style-two/watch-hero-fullwidth-movie.php';
		if ( is_readable( $theme_wrapper ) ) {
			return $theme_wrapper;
		}
	}

	if ( 'hero.php' === $base && false !== strpos( $template, 'single-series' ) ) {
		$theme_wrapper = HELLO_MOVIEENGINE_DIR . '/inc/templates/movie-engine/style-two/watch-hero-fullwidth-series.php';
		if ( is_readable( $theme_wrapper ) ) {
			return $theme_wrapper;
		}
	}

	return $template;
}
add_filter( 'movie_engine_get_template_part', 'hello_movieengine_style_two_watch_hero_template_part', 10, 3 );