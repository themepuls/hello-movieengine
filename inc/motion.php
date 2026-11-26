<?php
/**
 * Site-wide motion / animation (Customizer-controlled, performance-aware).
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return bool
 */
function hello_movieengine_animation_enabled() {
	return (bool) get_theme_mod( 'hello_movieengine_animation_enable', true );
}

/**
 * Motion bundle lives under style-two assets but applies to both site styles.
 *
 * @param string $relative e.g. css/motion.css or js/motion.js.
 * @return string Absolute path.
 */
function hello_movieengine_motion_asset_path( $relative ) {
	return trailingslashit( HELLO_MOVIEENGINE_DIR ) . 'assets/style-two/' . ltrim( $relative, '/' );
}

/**
 * @param string $relative e.g. css/motion.css.
 * @return string URI.
 */
function hello_movieengine_motion_asset_uri( $relative ) {
	return trailingslashit( HELLO_MOVIEENGINE_URI ) . 'assets/style-two/' . ltrim( $relative, '/' );
}

/**
 * Motion config for PHP + JS.
 *
 * @return array<string, mixed>
 */
function hello_movieengine_animation_get_config() {
	$enabled = hello_movieengine_animation_enabled();

	return array(
		'enabled'       => $enabled,
		'scrollReveal'  => $enabled && (bool) get_theme_mod( 'hello_movieengine_animation_scroll_reveal', true ),
		'pagePaint'     => $enabled && (bool) get_theme_mod( 'hello_movieengine_animation_page_paint', true ),
		'cardHover'     => $enabled && (bool) get_theme_mod( 'hello_movieengine_animation_card_hover', true ),
		'cardStagger'   => $enabled && (bool) get_theme_mod( 'hello_movieengine_animation_card_stagger', true ),
		'intensity'     => hello_movieengine_sanitize_animation_intensity(
			get_theme_mod( 'hello_movieengine_animation_intensity', 'balanced' )
		),
	);
}

/**
 * Body classes for motion feature flags (CSS can skip heavy effects).
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function hello_movieengine_animation_body_class( $classes ) {
	$config = hello_movieengine_animation_get_config();

	if ( ! $config['enabled'] ) {
		$classes[] = 'hello-movieengine-motion-off';
		return $classes;
	}

	$classes[] = 'hello-movieengine-motion-on';

	if ( ! $config['cardHover'] ) {
		$classes[] = 'hello-movieengine-motion-no-hover';
	}
	if ( ! $config['cardStagger'] ) {
		$classes[] = 'hello-movieengine-motion-no-card-stagger';
	}

	return $classes;
}
add_filter( 'body_class', 'hello_movieengine_animation_body_class', 20 );

/**
 * Intensity tokens on <html> (works with first-paint CSS).
 *
 * @param string $output language_attributes.
 * @return string
 */
function hello_movieengine_animation_language_attributes( $output ) {
	if ( ! hello_movieengine_animation_enabled() ) {
		return $output;
	}

	$class = 'hello-movieengine-motion-on';

	if ( preg_match( '/\bclass=(["\'])(.*?)\1/', $output, $m ) ) {
		if ( false !== strpos( $m[2], $class ) ) {
			return $output;
		}

		return preg_replace(
			'/\bclass=(["\'])/',
			'class=$1' . esc_attr( $class ) . ' ',
			$output,
			1
		);
	}

	return $output . ' class="' . esc_attr( $class ) . '"';
}
add_filter( 'language_attributes', 'hello_movieengine_animation_language_attributes', 12 );

/**
 * Mark Movie Engine rows for scroll/hover motion (Style One parity with Style Two).
 *
 * @param array<string, string> $out Parsed shortcode attributes.
 * @return array<string, string>
 */
function hello_movieengine_animation_shortcode_custom_class( $out ) {
	if ( ! hello_movieengine_animation_enabled() ) {
		return $out;
	}

	$marker          = 'hme-motion-scope';
	$existing        = isset( $out['custom_class'] ) ? trim( (string) $out['custom_class'] ) : '';
	$existing_tokens = preg_split( '/\s+/', $existing, -1, PREG_SPLIT_NO_EMPTY );
	if ( ! is_array( $existing_tokens ) ) {
		$existing_tokens = array();
	}

	if ( ! in_array( $marker, $existing_tokens, true ) ) {
		$existing_tokens[] = $marker;
	}

	$out['custom_class'] = implode( ' ', $existing_tokens );

	return $out;
}

/**
 * @param array<string, string> $out  Parsed attributes.
 * @param array<string, string> $pairs Pairs.
 * @param array<string, string> $atts  Raw attributes.
 * @return array<string, string>
 */
function hello_movieengine_animation_filter_shortcode_atts( $out, $pairs, $atts ) {
	unset( $pairs, $atts );
	return hello_movieengine_animation_shortcode_custom_class( $out );
}

/**
 * Register motion scope classes on home-feed shortcodes (both site styles).
 */
function hello_movieengine_animation_register_shortcode_filters() {
	$tags = array(
		'movie_engine_library',
		'movie_engine_top10',
		'movie_engine_person_list',
		'movie_engine_studios',
		'movie_engine_taxonomies',
		'movie_engine_genres',
		'movie_engine_years',
		'movie_engine_history',
	);

	foreach ( $tags as $tag ) {
		add_filter( 'shortcode_atts_' . $tag, 'hello_movieengine_animation_filter_shortcode_atts', 10, 3 );
	}
}
add_action( 'init', 'hello_movieengine_animation_register_shortcode_filters', 20 );

/**
 * Style One layout helpers when home-feed CSS is not loaded.
 */
function hello_movieengine_animation_style_one_layout_css() {
	if ( ! hello_movieengine_animation_enabled() || 'style-two' === hello_movieengine_get_active_style() ) {
		return '';
	}

	return '
.hello-movieengine-motion-on .movie-engine-library-container.hme-motion-scope,
.hello-movieengine-motion-on .movie-engine-taxonomies-wrapper.hme-motion-scope,
.hello-movieengine-motion-on .movie-engine-section:not(.movie-engine-hero-section) {
	overflow: visible;
}
.hello-movieengine-motion-on .movie-engine-section.movie-engine-slider-enabled,
.hello-movieengine-motion-on .movie-engine-section.me-slider-layout,
.hello-movieengine-motion-on .movie-engine-library-container.movie-engine-slider-enabled,
.hello-movieengine-motion-on .movie-engine-library-container.me-slider-layout {
	overflow: visible !important;
}
.hello-movieengine-motion-on .movie-engine-slider-enabled .movie-engine-items-grid,
.hello-movieengine-motion-on .me-slider-layout .movie-engine-items-grid {
	overflow-x: auto;
	overflow-y: visible !important;
	padding-top: 22px;
	padding-bottom: 14px;
}';
}

/**
 * CSS custom properties for motion intensity.
 */
function hello_movieengine_animation_inline_css_vars() {
	if ( ! hello_movieengine_animation_enabled() ) {
		return;
	}

	$config = hello_movieengine_animation_get_config();
	$intensity = $config['intensity'];

	$duration = 1;
	$distance = 26;
	$hover    = 1.04;

	if ( 'subtle' === $intensity ) {
		$duration = 0.72;
		$distance = 16;
		$hover    = 1.02;
	} elseif ( 'cinematic' === $intensity ) {
		$duration = 1.12;
		$distance = 32;
		$hover    = 1.06;
	}

	$css = ':root{--hme-motion-duration:' . $duration . ';--hme-motion-distance:' . (int) $distance . 'px;--hme-motion-hover:' . $hover . ';}';

	wp_add_inline_style( 'hello-movieengine-base', $css );
}

/**
 * Enqueue motion stylesheet + script when enabled (Style One & Two).
 */
function hello_movieengine_animation_should_load_motion_script() {
	if ( hello_movieengine_animation_enabled() ) {
		return true;
	}

	return 'style-two' === hello_movieengine_get_active_style();
}

function hello_movieengine_animation_enqueue_assets() {
	$css_path = hello_movieengine_motion_asset_path( 'css/motion.css' );
	if ( hello_movieengine_animation_enabled() && file_exists( $css_path ) ) {
		wp_enqueue_style(
			'hello-movieengine-motion',
			hello_movieengine_motion_asset_uri( 'css/motion.css' ),
			array( 'hello-movieengine-base' ),
			(string) filemtime( $css_path )
		);
		hello_movieengine_animation_inline_css_vars();

		$layout_css = hello_movieengine_animation_style_one_layout_css();
		if ( $layout_css ) {
			wp_add_inline_style( 'hello-movieengine-motion', $layout_css );
		}
	}

	if ( ! hello_movieengine_animation_should_load_motion_script() ) {
		return;
	}

	$js_path = hello_movieengine_motion_asset_path( 'js/motion.js' );
	if ( ! file_exists( $js_path ) ) {
		return;
	}

	wp_enqueue_script(
		'hello-movieengine-motion',
		hello_movieengine_motion_asset_uri( 'js/motion.js' ),
		array(),
		(string) filemtime( $js_path ),
		true
	);

	$config = hello_movieengine_animation_get_config();
	wp_add_inline_script(
		'hello-movieengine-motion',
		'window.helloMovieEngineMotionConfig=' . wp_json_encode(
			array(
				'enabled'      => (bool) $config['enabled'],
				'scrollReveal' => (bool) $config['scrollReveal'],
			)
		) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'hello_movieengine_animation_enqueue_assets', 25 );

/**
 * Optional first-paint fade (skip in Customizer preview — avoids blank preview).
 */
function hello_movieengine_animation_paint_head() {
	$config = hello_movieengine_animation_get_config();

	if ( ! $config['pagePaint'] || is_customize_preview() ) {
		return;
	}

	echo '<style id="hello-movieengine-paint">html.hello-movieengine-motion-on:not(.hmestyle-ready) #page{opacity:0}html.hello-movieengine-motion-on.hmestyle-ready #page{opacity:1;transition:opacity calc(.28s * var(--hme-motion-duration,1)) ease}@media (prefers-reduced-motion:reduce){html.hello-movieengine-motion-on #page{opacity:1!important;transition:none!important}}</style>' . "\n";
	echo '<script>!function(){function r(){document.documentElement.classList.add("hmestyle-ready")}if("complete"===document.readyState)r();else{document.addEventListener("DOMContentLoaded",function(){requestAnimationFrame(r)});window.addEventListener("load",r)}setTimeout(r,2500)}();</script>' . "\n";
	echo '<noscript><style>html.hello-movieengine-motion-on #page{opacity:1}</style></noscript>' . "\n";
}
add_action( 'wp_head', 'hello_movieengine_animation_paint_head', 1 );