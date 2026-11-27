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
 * Whether the current view should use a fixed (overlay) header.
 *
 * Controlled by Customizer → Header → Fixed Header On.
 *
 * @return bool
 */
function hello_movieengine_is_header_fixed_here() {
	$default   = wp_json_encode( array( 'front_page', 'single_movie', 'single_series', 'single_episode' ) );
	$locations = json_decode( get_theme_mod( 'hello_movieengine_header_fixed_locations', $default ), true );

	if ( ! is_array( $locations ) ) {
		$locations = array();
	}

	/* Movie Engine endpoints that should never be fixed overlay */
	if ( hello_movieengine_is_movie_engine_active() ) {
		global $wp_query;
		if ( isset( $wp_query->query_vars['reviews'] ) || isset( $wp_query->query_vars['player'] ) ) {
			return false;
		}
	}

	if ( in_array( 'front_page', $locations, true ) && is_front_page() ) {
		return true;
	}

	if ( hello_movieengine_is_movie_engine_active() ) {
		if ( in_array( 'single_movie', $locations, true ) && is_singular( 'movie_engine_movie' ) ) {
			return true;
		}
		if ( in_array( 'single_series', $locations, true ) && is_singular( 'movie_engine_series' ) ) {
			return true;
		}
		if ( in_array( 'single_episode', $locations, true ) && is_singular( 'movie_engine_episode' ) ) {
			return true;
		}
	}

	if ( in_array( 'blog', $locations, true ) && is_home() && ! is_front_page() ) {
		return true;
	}
	if ( in_array( 'single_post', $locations, true ) && is_singular( 'post' ) ) {
		return true;
	}
	if ( in_array( 'single_page', $locations, true ) && is_page() && ! is_front_page() ) {
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

	$page_id = hello_movieengine_current_page_id();
	if ( $page_id ) {
		$selected_pages = json_decode( get_theme_mod( 'hello_movieengine_header_fixed_pages', '[]' ), true );
		if ( is_array( $selected_pages ) && in_array( $page_id, array_map( 'intval', $selected_pages ), true ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Determine which header style to use.
 *
 * Transparent (position fixed) only on pages selected in Fixed Header On,
 * and only when Header Style is Transparent. Otherwise solid (normal).
 *
 * @return string 'transparent' or 'solid'
 */
function hello_movieengine_get_header_style() {
	/* Reviews / player endpoints always use solid */
	if ( hello_movieengine_is_movie_engine_active() ) {
		global $wp_query;
		if ( isset( $wp_query->query_vars['reviews'] ) || isset( $wp_query->query_vars['player'] ) ) {
			return 'solid';
		}
	}

	$style = get_theme_mod( 'hello_movieengine_header_style', 'solid' );

	/* Solid mode: never fixed overlay */
	if ( 'transparent' !== $style ) {
		return 'solid';
	}

	/* Transparent mode: fixed only on selected locations */
	return hello_movieengine_is_header_fixed_here() ? 'transparent' : 'solid';
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
 * Customizer: show boxed-only header controls.
 *
 * @return bool
 */
function hello_movieengine_customize_header_is_boxed() {
	return 'boxed' === hello_movieengine_get_header_width();
}

/**
 * Max width for the boxed header inner container (0 = use site container width).
 *
 * @return int
 */
function hello_movieengine_get_header_container_width() {
	$width = absint( get_theme_mod( 'hello_movieengine_header_container_width', 0 ) );
	if ( $width < 800 ) {
		return 0;
	}
	return min( 1920, $width );
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

	if ( 'transparent' === $header_style ) {
		$classes[] = 'hello-movieengine-header-fixed';
	} else {
		$classes[] = 'hello-movieengine-header-normal';
	}

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
 * Current page ID, including the static front page.
 *
 * @return int
 */
function hello_movieengine_current_page_id() {
	if ( is_singular() ) {
		return (int) get_queried_object_id();
	}

	if ( is_front_page() && 'page' === get_option( 'show_on_front' ) ) {
		return (int) get_option( 'page_on_front' );
	}

	return 0;
}

/**
 * True for the page assigned as the site homepage.
 *
 * @param int $page_id Optional page ID. Uses the current page when empty.
 * @return bool
 */
function hello_movieengine_is_static_front_page( $page_id = 0 ) {
	if ( 'page' !== get_option( 'show_on_front' ) ) {
		return is_front_page();
	}

	$front_id = (int) get_option( 'page_on_front' );
	if ( ! $front_id ) {
		return false;
	}

	if ( is_front_page() ) {
		return true;
	}

	$page_id = $page_id ? (int) $page_id : hello_movieengine_current_page_id();

	return $page_id === $front_id;
}

/**
 * Check if the page title section should display on the current page.
 *
 * @return bool
 */
function hello_movieengine_show_page_title() {
	if ( function_exists( 'hello_movieengine_is_feed_page' ) && hello_movieengine_is_feed_page() ) {
		if ( get_theme_mod( 'hello_movieengine_feed_hide_page_title', true ) ) {
			return false;
		}
	}

	/* Playlist page has its own layout; no theme page title */
	if ( hello_movieengine_is_movie_engine_active() && get_query_var( 'movie_engine_playlist_page' ) ) {
		return false;
	}

	if ( is_page() && function_exists( 'hello_movieengine_get_page_title_mode' ) ) {
		$page_mode = hello_movieengine_get_page_title_mode();
		if ( 'hide' === $page_mode ) {
			return false;
		}
		if ( 'show' === $page_mode ) {
			return true;
		}
	}

	$default   = wp_json_encode( array( 'blog', 'archives', 'search', 'author' ) );
	$locations = json_decode( get_theme_mod( 'hello_movieengine_page_title_locations', $default ), true );

	if ( ! is_array( $locations ) ) {
		$locations = array();
	}

	$page_id = hello_movieengine_current_page_id();

	/*
	 * The static front page is not a normal page. It shows a title only when
	 * Front Page is selected, even if that page is checked under Select Pages.
	 */
	if ( hello_movieengine_is_static_front_page( $page_id ) ) {
		return in_array( 'front_page', $locations, true );
	}

	if ( hello_movieengine_is_movie_engine_active() && hello_movieengine_is_movie_engine_archive() ) {
		return false;
	}

	if (
		function_exists( 'hello_movieengine_is_studio_taxonomy' )
		&& hello_movieengine_is_studio_taxonomy()
		&& function_exists( 'hello_movieengine_get_active_style' )
		&& 'style-two' === hello_movieengine_get_active_style()
	) {
		return false;
	}

	if ( in_array( 'blog', $locations, true ) && ( is_home() && ! is_front_page() ) ) {
		return true;
	}
	if ( in_array( 'single_post', $locations, true ) && is_singular( 'post' ) ) {
		return true;
	}
	if ( in_array( 'single_page', $locations, true ) && is_page() && ! is_front_page() ) {
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

	if (
		is_page()
		&& function_exists( 'hello_movieengine_get_active_style' )
		&& 'style-two' === hello_movieengine_get_active_style()
		&& function_exists( 'hello_movieengine_is_studios_page' )
		&& hello_movieengine_is_studios_page()
	) {
		return true;
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
		if ( function_exists( 'hello_movieengine_is_style_two_browse' ) && hello_movieengine_is_style_two_browse() ) {
			$post = get_post();
			if ( $post && false !== strpos( $post->post_content, 'movie_engine_series' ) ) {
				return esc_html__( 'TV Series Archive', 'hello-movieengine' );
			}
			return esc_html__( 'Movie Archive', 'hello-movieengine' );
		}
		if ( function_exists( 'hello_movieengine_is_studios_page' ) && hello_movieengine_is_studios_page() ) {
			return esc_html__( 'Studios', 'hello-movieengine' );
		}
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
 * Last word accent for Style Two cinema page titles (e.g. Movies <span>Archive</span>).
 *
 * @param string $title Plain or HTML title.
 * @return string
 */
function hello_movieengine_format_cinema_page_title( $title ) {
	$plain = trim( wp_strip_all_tags( $title ) );
	if ( '' === $plain ) {
		return '';
	}

	$parts = preg_split( '/\s+/', $plain );
	if ( ! is_array( $parts ) || count( $parts ) < 2 ) {
		return esc_html( $plain );
	}

	$last = array_pop( $parts );

	return esc_html( implode( ' ', $parts ) ) . ' <span>' . esc_html( $last ) . '</span>';
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
		$title = get_the_title();
		if ( function_exists( 'hello_movieengine_get_active_style' ) && 'style-two' === hello_movieengine_get_active_style() ) {
			return hello_movieengine_format_cinema_page_title( $title );
		}
		return $title;
	}
	if ( is_author() ) {
		return get_the_author();
	}
	if ( is_category() ) {
		$title = single_cat_title( '', false );
		if ( function_exists( 'hello_movieengine_get_active_style' ) && 'style-two' === hello_movieengine_get_active_style() ) {
			return hello_movieengine_format_cinema_page_title( $title );
		}
		return $title;
	}
	if ( is_tag() ) {
		$title = single_tag_title( '', false );
		if ( function_exists( 'hello_movieengine_get_active_style' ) && 'style-two' === hello_movieengine_get_active_style() ) {
			return hello_movieengine_format_cinema_page_title( $title );
		}
		return $title;
	}
	if ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
		if ( function_exists( 'hello_movieengine_get_active_style' ) && 'style-two' === hello_movieengine_get_active_style() ) {
			return hello_movieengine_format_cinema_page_title( $title );
		}
		return $title;
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
 * @return bool
 */
function hello_movieengine_page_title_show_description() {
	return (bool) get_theme_mod( 'hello_movieengine_page_title_show_description', true );
}

/**
 * Plain text suitable for page-title description (no shortcodes or bracket-only tokens).
 *
 * @param string $raw Raw HTML or text.
 * @return string
 */
function hello_movieengine_normalize_banner_description_text( $raw ) {
	$text = wp_strip_all_tags( (string) $raw );
	$text = strip_shortcodes( $text );
	$text = trim( preg_replace( '/\s+/u', ' ', $text ) );
	if ( '' === $text ) {
		return '';
	}
	if ( preg_match( '/^\[[^\]]+\]$/', $text ) ) {
		return '';
	}
	return $text;
}

/**
 * First paragraph text from post content (block or classic).
 *
 * @param int $post_id Post ID.
 * @return string
 */
function hello_movieengine_get_first_content_paragraph( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( $post_id < 1 ) {
		return '';
	}

	$post = get_post( $post_id );
	if ( ! $post || ! $post->post_content ) {
		return '';
	}

	if ( function_exists( 'parse_blocks' ) ) {
		$blocks = parse_blocks( $post->post_content );
		$text   = hello_movieengine_find_first_paragraph_in_blocks( $blocks );
		if ( '' !== $text ) {
			return $text;
		}
	}

	if ( preg_match_all( '/<p[^>]*>(.*?)<\/p>/is', $post->post_content, $matches ) && ! empty( $matches[1] ) ) {
		foreach ( $matches[1] as $chunk ) {
			$text = hello_movieengine_normalize_banner_description_text( $chunk );
			if ( '' !== $text ) {
				return $text;
			}
		}
	}

	return '';
}

/**
 * @param array $blocks Parsed blocks.
 * @return string
 */
function hello_movieengine_find_first_paragraph_in_blocks( $blocks ) {
	foreach ( $blocks as $block ) {
		if ( empty( $block ) || ! is_array( $block ) ) {
			continue;
		}
		$name = isset( $block['blockName'] ) ? (string) $block['blockName'] : '';
		if ( 'core/paragraph' === $name ) {
			$inner = '';
			if ( ! empty( $block['innerHTML'] ) ) {
				$inner = $block['innerHTML'];
			} elseif ( ! empty( $block['innerContent'] ) && is_array( $block['innerContent'] ) ) {
				$inner = implode( '', $block['innerContent'] );
			}
			$text = hello_movieengine_normalize_banner_description_text( $inner );
			if ( '' !== $text ) {
				return $text;
			}
		}
		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			$nested = hello_movieengine_find_first_paragraph_in_blocks( $block['innerBlocks'] );
			if ( '' !== $nested ) {
				return $nested;
			}
		}
	}
	return '';
}

/**
 * Page-level custom banner description (editor sidebar).
 *
 * @param int $post_id Post ID.
 * @return string
 */
function hello_movieengine_get_page_title_custom_description( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( $post_id < 1 ) {
		return '';
	}
	$custom = get_post_meta( $post_id, HELLO_MOVIEENGINE_PAGE_TITLE_DESC_META, true );
	return is_string( $custom ) ? trim( $custom ) : '';
}

/**
 * Get the page title description for the current context.
 *
 * Priority for pages: custom sidebar text → first real paragraph (no shortcodes).
 * Returns empty when none (banner hides the line) or Customizer disables descriptions.
 *
 * @param int $post_id Optional post ID.
 * @return string
 */
function hello_movieengine_get_page_title_description( $post_id = 0 ) {
	if ( ! hello_movieengine_page_title_show_description() ) {
		return '';
	}

	if ( is_page() || $post_id ) {
		$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
		if ( $post_id > 0 ) {
			$custom = hello_movieengine_get_page_title_custom_description( $post_id );
			if ( '' !== $custom ) {
				return $custom;
			}
			$from_content = hello_movieengine_get_first_content_paragraph( $post_id );
			if ( '' !== $from_content ) {
				return $from_content;
			}
		}
		return '';
	}

	if ( is_category() || is_tag() || is_tax() ) {
		$desc = get_the_archive_description();
		return is_string( $desc ) ? wp_strip_all_tags( $desc ) : '';
	}
	if ( is_author() ) {
		$desc = get_the_author_meta( 'description' );
		return is_string( $desc ) ? trim( $desc ) : '';
	}
	return '';
}

/**
 * Attachment ID for per-page page-title banner background (editor sidebar).
 *
 * @param int $post_id Page ID.
 * @return int
 */
function hello_movieengine_get_page_title_custom_bg_id( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( $post_id < 1 || ! defined( 'HELLO_MOVIEENGINE_PAGE_TITLE_BG_META' ) ) {
		return 0;
	}
	$id = (int) get_post_meta( $post_id, HELLO_MOVIEENGINE_PAGE_TITLE_BG_META, true );
	if ( $id < 1 || 'attachment' !== get_post_type( $id ) ) {
		return 0;
	}
	return $id;
}

/**
 * Page title banner background URL.
 *
 * Priority: sidebar image → featured image (pages) → Customizer default.
 *
 * @param int $post_id Optional page ID.
 * @return string
 */
function hello_movieengine_get_page_title_bg_image_url( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : 0;
	if ( $post_id < 1 && is_singular() ) {
		$post_id = (int) get_queried_object_id();
	}

	if ( $post_id > 0 ) {
		$custom_id = hello_movieengine_get_page_title_custom_bg_id( $post_id );
		if ( $custom_id > 0 ) {
			$url = wp_get_attachment_image_url( $custom_id, 'full' );
			if ( is_string( $url ) && '' !== $url ) {
				return $url;
			}
		}

		if ( 'page' === get_post_type( $post_id ) && has_post_thumbnail( $post_id ) ) {
			$url = get_the_post_thumbnail_url( $post_id, 'full' );
			if ( is_string( $url ) && '' !== $url ) {
				return $url;
			}
		}
	}

	$default = get_theme_mod( 'hello_movieengine_page_title_bg_image', '' );
	if ( is_string( $default ) && '' !== trim( $default ) ) {
		return esc_url_raw( $default );
	}

	return '';
}

/**
 * Inline style for page-title banner background.
 *
 * @param string $url           Image URL.
 * @param bool   $is_style_two  Active site style is Style Two.
 * @return string
 */
function hello_movieengine_page_title_bg_inline_style( $url, $is_style_two ) {
	if ( '' === $url ) {
		return '';
	}
	if ( $is_style_two ) {
		return '--hme-page-title-photo:url(' . esc_url( $url ) . ');';
	}
	return 'background-image:url(' . esc_url( $url ) . ');';
}

/**
 * Footer wrapper class list (background image state).
 *
 * @return string Escaped class attribute value.
 */
function hello_movieengine_get_footer_class_attribute() {
	$classes = array( 'hello-movieengine-footer' );
	$image   = get_theme_mod( 'hello_movieengine_footer_bg_image', '' );
	if ( is_string( $image ) && '' !== trim( $image ) ) {
		$classes[] = 'has-bg-image';
	}
	return esc_attr( implode( ' ', $classes ) );
}

/**
 * CSS custom properties for footer background and spacing.
 *
 * @return string Declaration block contents (no braces).
 */
function hello_movieengine_footer_root_vars() {
	$vars = '';

	$bg_color = get_theme_mod( 'hello_movieengine_footer_bg_color', '' );
	if ( is_string( $bg_color ) && '' !== trim( $bg_color ) ) {
		$vars .= '--hello-movieengine-footer-bg-color:' . esc_attr( $bg_color ) . ';';
	}

	$bg_image = get_theme_mod( 'hello_movieengine_footer_bg_image', '' );
	if ( is_string( $bg_image ) && '' !== trim( $bg_image ) ) {
		$vars .= '--hello-movieengine-footer-bg-image:url(' . esc_url( $bg_image ) . ');';
	}

	$img_opacity = (int) get_theme_mod( 'hello_movieengine_footer_bg_image_opacity', 100 );
	if ( $img_opacity < 100 ) {
		$vars .= '--hello-movieengine-footer-bg-image-opacity:' . ( $img_opacity / 100 ) . ';';
	}

	$overlay = get_theme_mod( 'hello_movieengine_footer_bg_overlay', '' );
	if ( is_string( $overlay ) && '' !== trim( $overlay ) ) {
		$vars .= '--hello-movieengine-footer-bg-overlay:' . esc_attr( $overlay ) . ';';
	}

	$border_color = get_theme_mod( 'hello_movieengine_footer_border_color', '' );
	if ( is_string( $border_color ) && '' !== trim( $border_color ) ) {
		$vars .= '--hello-movieengine-footer-border-color:' . esc_attr( $border_color ) . ';';
	}

	$spacing_zero = array(
		'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'linked' => true,
	);
	$margin_raw  = get_theme_mod( 'hello_movieengine_footer_margin', wp_json_encode( $spacing_zero ) );
	$margin_data = json_decode( $margin_raw, true );
	$margin_custom = is_array( $margin_data ) && (
		floatval( $margin_data['top'] ?? 0 ) !== 0
		|| floatval( $margin_data['right'] ?? 0 ) !== 0
		|| floatval( $margin_data['bottom'] ?? 0 ) !== 0
		|| floatval( $margin_data['left'] ?? 0 ) !== 0
		|| ( isset( $margin_data['unit'] ) && 'px' !== $margin_data['unit'] )
	);
	if ( $margin_custom ) {
		$mu = isset( $margin_data['unit'] ) ? $margin_data['unit'] : 'px';
		$mt = isset( $margin_data['top'] ) ? floatval( $margin_data['top'] ) : 0;
		$mr = isset( $margin_data['right'] ) ? floatval( $margin_data['right'] ) : 0;
		$mb = isset( $margin_data['bottom'] ) ? floatval( $margin_data['bottom'] ) : 0;
		$ml = isset( $margin_data['left'] ) ? floatval( $margin_data['left'] ) : 0;
		$vars .= '--hello-movieengine-footer-mt:' . $mt . $mu . ';';
		$vars .= '--hello-movieengine-footer-mr:' . $mr . $mu . ';';
		$vars .= '--hello-movieengine-footer-mb:' . $mb . $mu . ';';
		$vars .= '--hello-movieengine-footer-ml:' . $ml . $mu . ';';
	}

	$pad_raw  = get_theme_mod( 'hello_movieengine_footer_padding', wp_json_encode( $spacing_zero ) );
	$pad_data = json_decode( $pad_raw, true );
	$pad_custom = is_array( $pad_data ) && (
		floatval( $pad_data['top'] ?? 0 ) !== 0
		|| floatval( $pad_data['right'] ?? 0 ) !== 0
		|| floatval( $pad_data['bottom'] ?? 0 ) !== 0
		|| floatval( $pad_data['left'] ?? 0 ) !== 0
		|| ( isset( $pad_data['unit'] ) && 'px' !== $pad_data['unit'] )
	);
	if ( $pad_custom ) {
		$pu = isset( $pad_data['unit'] ) ? $pad_data['unit'] : 'px';
		$pt = isset( $pad_data['top'] ) ? floatval( $pad_data['top'] ) : 0;
		$pr = isset( $pad_data['right'] ) ? floatval( $pad_data['right'] ) : 0;
		$pb = isset( $pad_data['bottom'] ) ? floatval( $pad_data['bottom'] ) : 0;
		$pl = isset( $pad_data['left'] ) ? floatval( $pad_data['left'] ) : 0;
		$vars .= '--hello-movieengine-footer-pt:' . $pt . $pu . ';';
		$vars .= '--hello-movieengine-footer-pr:' . $pr . $pu . ';';
		$vars .= '--hello-movieengine-footer-pb:' . $pb . $pu . ';';
		$vars .= '--hello-movieengine-footer-pl:' . $pl . $pu . ';';
	}

	return $vars;
}
