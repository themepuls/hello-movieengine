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
