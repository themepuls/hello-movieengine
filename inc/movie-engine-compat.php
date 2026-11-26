<?php
/**
 * Movie Engine plugin detection and compatibility layer.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if Movie Engine plugin is active.
 */
function hello_movieengine_is_movie_engine_active() {
	return defined( 'MOVIE_ENGINE_VERSION' ) || class_exists( 'MovieEngine\\Plugin' );
}

/**
 * Check if the current page is a Movie Engine archive.
 */
function hello_movieengine_is_movie_engine_archive() {
	if ( ! hello_movieengine_is_movie_engine_active() ) {
		return false;
	}

	$me_types = array(
		'movie_engine_movie',
		'movie_engine_series',
		'movie_engine_person',
	);

	return is_post_type_archive( $me_types );
}

/**
 * Whether the current page is the Movie Engine “Studios” page from Design settings.
 *
 * @return bool
 */
function hello_movieengine_is_studios_page() {
	if ( ! hello_movieengine_is_movie_engine_active() || ! is_page() ) {
		return false;
	}

	$options = get_option( 'movie_engine_design_settings', array() );
	if ( empty( $options['studios_page'] ) ) {
		return false;
	}

	return (int) $options['studios_page'] === (int) get_queried_object_id();
}

/**
 * Whether the request is a single Movie Engine studio taxonomy archive.
 *
 * @return bool
 */
function hello_movieengine_is_studio_taxonomy() {
	return hello_movieengine_is_movie_engine_active() && is_tax( 'movie_engine_studio' );
}

/**
 * Movie Engine post types that this theme supports.
 */
function hello_movieengine_get_movie_engine_post_types() {
	return array(
		'movie_engine_movie',
		'movie_engine_series',
		'movie_engine_episode',
		'movie_engine_person',
		'movie_engine_season',
	);
}

/**
 * Add Movie Engine post type support for thumbnails.
 */
function hello_movieengine_movie_engine_post_type_support() {
	if ( ! hello_movieengine_is_movie_engine_active() ) {
		return;
	}

	foreach ( hello_movieengine_get_movie_engine_post_types() as $pt ) {
		if ( post_type_exists( $pt ) ) {
			add_post_type_support( $pt, 'thumbnail' );
		}
	}
}
add_action( 'init', 'hello_movieengine_movie_engine_post_type_support', 20 );

/**
 * Adjust the document title for Movie Engine archives if needed.
 */
function hello_movieengine_movie_engine_archive_title( $title ) {
	if ( ! hello_movieengine_is_movie_engine_active() ) {
		return $title;
	}

	if ( is_post_type_archive( 'movie_engine_movie' ) ) {
		$title = esc_html__( 'Movies', 'hello-movieengine' );
	} elseif ( is_post_type_archive( 'movie_engine_series' ) ) {
		$title = esc_html__( 'TV Series', 'hello-movieengine' );
	} elseif ( is_post_type_archive( 'movie_engine_person' ) ) {
		$title = esc_html__( 'People', 'hello-movieengine' );
	}

	return $title;
}
add_filter( 'get_the_archive_title', 'hello_movieengine_movie_engine_archive_title' );

/**
 * Output live Movie Engine palette CSS from current design settings.
 *
 * The plugin caches tokens in uploads/custom-style.css; that file can lag behind
 * Customizer preview or unsaved changes. Inline CSS from get_option() (preview-aware)
 * overrides the cached file so preset/tab changes apply immediately.
 */
function hello_movieengine_enqueue_movie_engine_design_palette() {
	if ( ! hello_movieengine_is_movie_engine_active() || ! class_exists( 'MovieEngine\\Core\\Cache' ) ) {
		return;
	}

	$design = get_option( 'movie_engine_design_settings', array() );
	if ( ! is_array( $design ) ) {
		$design = array();
	}

	$css = \MovieEngine\Core\Cache::generate_design_css( $design );
	if ( empty( $css ) ) {
		return;
	}

	$handles = array( 'movie-engine-custom', 'movie-engine', 'hello-movieengine-base' );
	$handle  = '';
	foreach ( $handles as $candidate ) {
		if ( wp_style_is( $candidate, 'enqueued' ) ) {
			$handle = $candidate;
			break;
		}
	}
	if ( '' === $handle ) {
		return;
	}

	wp_add_inline_style( $handle, $css );
}
add_action( 'wp_enqueue_scripts', 'hello_movieengine_enqueue_movie_engine_design_palette', 100 );

/**
 * Sanitize Movie Engine design settings to prevent layout breakdowns.
 *
 * Cine Movie Engine series episodes template only supports 'list' and 'grid'.
 * If 'tabs' or any legacy invalid value is stored, normalize it to 'list'.
 *
 * @param mixed $settings Options array.
 * @return mixed Normalized options array.
 */
function hello_movieengine_sanitize_design_settings( $settings ) {
	if ( is_array( $settings ) ) {
		if ( isset( $settings['series_seasons_layout'] ) && ! in_array( $settings['series_seasons_layout'], array( 'list', 'grid' ), true ) ) {
			$settings['series_seasons_layout'] = 'list';
		}
	}
	return $settings;
}
add_filter( 'option_movie_engine_design_settings', 'hello_movieengine_sanitize_design_settings' );
add_filter( 'default_option_movie_engine_design_settings', 'hello_movieengine_sanitize_design_settings' );

