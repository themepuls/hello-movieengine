<?php
/**
 * Template helper functions hooked into WordPress.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine which header style to use.
 *
 * Priority: page-level meta → customizer default.
 * Movie Engine single movie/series pages auto-use transparent (episodes use solid).
 *
 * @return string 'transparent' or 'solid'
 */
function hello_movieengine_get_header_style() {
	$style = get_theme_mod( 'hello_movieengine_header_style', 'solid' );

	/* Episodes and playlist page always use solid header */
	if ( hello_movieengine_is_movie_engine_active() ) {
		if ( is_singular( 'movie_engine_episode' ) || get_query_var( 'movie_engine_playlist_page' ) ) {
			return 'solid';
		}
	}

	if ( is_singular() ) {
		$per_page = get_post_meta( get_the_ID(), '_hello_movieengine_header_style', true );
		if ( $per_page && in_array( $per_page, array( 'transparent', 'solid' ), true ) ) {
			return $per_page;
		}
	}

	if ( hello_movieengine_is_movie_engine_active() ) {
		$me_types = array( 'movie_engine_movie', 'movie_engine_series' );
		if ( is_singular( $me_types ) ) {
			return 'transparent';
		}
	}

	return $style;
}

/**
 * Get the header width mode.
 *
 * @return string 'boxed' or 'fullwidth'
 */
function hello_movieengine_get_header_width() {
	return get_theme_mod( 'hello_movieengine_header_width', 'boxed' );
}

/**
 * Return the CSS class for the header inner container.
 *
 * @return string
 */
function hello_movieengine_header_container_class() {
	return 'fullwidth' === hello_movieengine_get_header_width()
		? 'hello-movieengine-container-fluid'
		: 'hello-movieengine-container';
}

/**
 * Add custom body classes.
 */
function hello_movieengine_body_classes( $classes ) {
	$classes[] = 'hello-movieengine-theme';

	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	$sidebar_enabled = get_theme_mod( 'hello_movieengine_sidebar_enable', true );
	if ( ! $sidebar_enabled || ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	$header_style = hello_movieengine_get_header_style();
	$classes[]    = 'hello-movieengine-header-' . $header_style;
	$classes[]    = 'hello-movieengine-header-width-' . hello_movieengine_get_header_width();

	$content_layout = get_theme_mod( 'hello_movieengine_content_layout', 'boxed' );
	if ( 'fullwidth' === $content_layout ) {
		$classes[] = 'hello-movieengine-content-fullwidth';
	}

	$sidebar_pos = get_theme_mod( 'hello_movieengine_sidebar_position', 'right' );
	if ( 'left' === $sidebar_pos ) {
		$classes[] = 'hello-movieengine-sidebar-left';
	}

	if ( hello_movieengine_is_movie_engine_active() ) {
		$classes[] = 'hello-movieengine-has-movie-engine';
	}

	return $classes;
}
add_filter( 'body_class', 'hello_movieengine_body_classes' );

/**
 * Pingback auto-discovery for singular content.
 */
function hello_movieengine_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'hello_movieengine_pingback_header' );

/**
 * Check if the current page should hide the sidebar.
 */
function hello_movieengine_show_sidebar() {
	if ( ! get_theme_mod( 'hello_movieengine_sidebar_enable', true ) ) {
		return false;
	}

	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		return false;
	}

	if ( hello_movieengine_is_movie_engine_active() ) {
		$me_types = array(
			'movie_engine_movie',
			'movie_engine_series',
			'movie_engine_episode',
			'movie_engine_person',
		);
		if ( is_singular( $me_types ) || hello_movieengine_is_movie_engine_archive() ) {
			return false;
		}
	}

	return true;
}

/**
 * Check if the page title section should display on the current page.
 *
 * @return bool
 */
function hello_movieengine_show_page_title() {
	if ( is_front_page() ) {
		return false;
	}

	/* Playlist page has its own layout; no theme page title */
	if ( hello_movieengine_is_movie_engine_active() && get_query_var( 'movie_engine_playlist_page' ) ) {
		return false;
	}

	$default   = wp_json_encode( array( 'blog', 'archives', 'search', 'author' ) );
	$locations = json_decode( get_theme_mod( 'hello_movieengine_page_title_locations', $default ), true );

	if ( ! is_array( $locations ) ) {
		$locations = array();
	}

	if ( hello_movieengine_is_movie_engine_active() && hello_movieengine_is_movie_engine_archive() ) {
		return false;
	}

	if ( in_array( 'blog', $locations, true ) && ( is_home() && ! is_front_page() ) ) {
		return true;
	}
	if ( in_array( 'single_post', $locations, true ) && is_singular( 'post' ) ) {
		return true;
	}
	if ( in_array( 'single_page', $locations, true ) && is_page() ) {
		return true;
	}
	if ( in_array( 'archives', $locations, true ) && ( is_category() || is_tag() || is_post_type_archive() || is_date() ) ) {
		return true;
	}
	if ( in_array( 'search', $locations, true ) && is_search() ) {
		return true;
	}
	if ( in_array( 'author', $locations, true ) && is_author() ) {
		return true;
	}
	if ( in_array( '404', $locations, true ) && is_404() ) {
		return true;
	}

	if ( is_page() ) {
		$selected_pages = json_decode( get_theme_mod( 'hello_movieengine_page_title_pages', '[]' ), true );
		if ( is_array( $selected_pages ) && ! empty( $selected_pages ) ) {
			$current_id = (int) get_the_ID();
			if ( in_array( $current_id, array_map( 'intval', $selected_pages ), true ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Get the small context label above the page title.
 *
 * @return string
 */
function hello_movieengine_get_page_title_label() {
	if ( is_home() && ! is_front_page() ) {
		return esc_html__( 'Blog', 'hello-movieengine' );
	}
	if ( is_search() ) {
		return esc_html__( 'Search Results', 'hello-movieengine' );
	}
	if ( is_404() ) {
		return esc_html__( 'Error 404', 'hello-movieengine' );
	}
	if ( is_singular( 'post' ) ) {
		return esc_html__( 'Post', 'hello-movieengine' );
	}
	if ( is_page() ) {
		return esc_html__( 'Page', 'hello-movieengine' );
	}
	if ( is_author() ) {
		return esc_html__( 'Author', 'hello-movieengine' );
	}
	if ( is_category() ) {
		return esc_html__( 'Category', 'hello-movieengine' );
	}
	if ( is_tag() ) {
		return esc_html__( 'Tag', 'hello-movieengine' );
	}
	if ( is_date() ) {
		return esc_html__( 'Archives', 'hello-movieengine' );
	}
	if ( is_post_type_archive() ) {
		return esc_html__( 'Archive', 'hello-movieengine' );
	}
	return esc_html__( 'Archive', 'hello-movieengine' );
}

/**
 * Get the page title text for the current context.
 *
 * @return string
 */
function hello_movieengine_get_page_title_text() {
	if ( is_home() && ! is_front_page() ) {
		return single_post_title( '', false );
	}
	if ( is_search() ) {
		$query = get_search_query();
		return $query ? $query : esc_html__( 'Search', 'hello-movieengine' );
	}
	if ( is_404() ) {
		return esc_html__( 'Page Not Found', 'hello-movieengine' );
	}
	if ( is_singular( 'post' ) ) {
		return get_the_title();
	}
	if ( is_page() ) {
		return get_the_title();
	}
	if ( is_author() ) {
		return get_the_author();
	}
	if ( is_category() ) {
		return single_cat_title( '', false );
	}
	if ( is_tag() ) {
		return single_tag_title( '', false );
	}
	if ( is_post_type_archive() ) {
		return post_type_archive_title( '', false );
	}
	if ( is_date() ) {
		if ( is_year() ) {
			return get_the_date( 'Y' );
		}
		if ( is_month() ) {
			return get_the_date( 'F Y' );
		}
		return get_the_date();
	}
	return get_the_archive_title();
}

/**
 * Get the page title description for the current context.
 *
 * @return string
 */
function hello_movieengine_get_page_title_description() {
	if ( is_category() || is_tag() || is_tax() ) {
		return get_the_archive_description();
	}
	if ( is_author() ) {
		return get_the_author_meta( 'description' );
	}
	return '';
}
