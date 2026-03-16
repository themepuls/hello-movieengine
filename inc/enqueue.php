<?php
/**
 * Enqueue styles and scripts.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hello_movieengine_scripts() {
	$v = HELLO_MOVIEENGINE_VERSION;

	wp_enqueue_style( 'hello-movieengine-base', HELLO_MOVIEENGINE_URI . '/assets/css/base.css', array(), $v );
	wp_enqueue_style( 'hello-movieengine-layout', HELLO_MOVIEENGINE_URI . '/assets/css/layout.css', array( 'hello-movieengine-base' ), $v );
	wp_enqueue_style( 'hello-movieengine-header', HELLO_MOVIEENGINE_URI . '/assets/css/header.css', array( 'hello-movieengine-base' ), $v );
	wp_enqueue_style( 'hello-movieengine-footer', HELLO_MOVIEENGINE_URI . '/assets/css/footer.css', array( 'hello-movieengine-base' ), $v );
	wp_enqueue_style( 'hello-movieengine-components', HELLO_MOVIEENGINE_URI . '/assets/css/components.css', array( 'hello-movieengine-base' ), $v );
	wp_enqueue_style( 'hello-movieengine-responsive', HELLO_MOVIEENGINE_URI . '/assets/css/responsive.css', array( 'hello-movieengine-components' ), $v );

	if ( hello_movieengine_is_movie_engine_active() ) {
		wp_enqueue_style( 'hello-movieengine-me-compat', HELLO_MOVIEENGINE_URI . '/assets/css/movie-engine-compat.css', array( 'hello-movieengine-base' ), $v );
	}

	wp_enqueue_style( 'hello-movieengine-style', get_stylesheet_uri(), array(), $v );

	wp_enqueue_script( 'hello-movieengine-navigation', HELLO_MOVIEENGINE_URI . '/assets/js/navigation.js', array(), $v, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	hello_movieengine_inline_custom_css();
}
add_action( 'wp_enqueue_scripts', 'hello_movieengine_scripts' );

function hello_movieengine_inline_custom_css() {
	$primary_color = get_theme_mod( 'hello_movieengine_primary_color', '#E50914' );
	$header_bg     = get_theme_mod( 'hello_movieengine_header_bg_color', '' );

	$pad_default = wp_json_encode( array(
		'top' => '0', 'right' => '4', 'bottom' => '0', 'left' => '4', 'unit' => 'rem',
	) );
	$pad_raw  = get_theme_mod( 'hello_movieengine_header_padding', $pad_default );
	$pad_data = json_decode( $pad_raw, true );

	if ( ! is_array( $pad_data ) ) {
		$pad_data = array( 'top' => '0', 'right' => '4', 'bottom' => '0', 'left' => '4', 'unit' => 'rem' );
	}

	$pu = isset( $pad_data['unit'] ) ? $pad_data['unit'] : 'rem';

	$css       = '';
	$root_vars = '';

	if ( '#E50914' !== $primary_color ) {
		$hex = sanitize_hex_color( $primary_color );
		if ( ! $hex ) {
			$hex = '#E50914';
		}
		$r = hexdec( substr( $hex, 1, 2 ) );
		$g = hexdec( substr( $hex, 3, 2 ) );
		$b = hexdec( substr( $hex, 5, 2 ) );

		$hover_r = max( 0, $r - 30 );
		$hover_g = max( 0, $g - 30 );
		$hover_b = max( 0, $b - 30 );
		$hover   = sprintf( '#%02x%02x%02x', $hover_r, $hover_g, $hover_b );

		$root_vars .= '--hello-movieengine-primary-color:' . $hex . ';';
		$root_vars .= '--hello-movieengine-primary-hover:' . $hover . ';';
		$root_vars .= '--hello-movieengine-primary-rgb:' . "{$r},{$g},{$b}" . ';';

		$css .= 'body.hello-movieengine-has-movie-engine{'
			. '--hello-movieengine-primary-color:' . $hex . ';'
			. '--hello-movieengine-primary-hover:' . $hover . ';'
			. '--hello-movieengine-primary-rgb:' . "{$r},{$g},{$b}" . ';'
			. '--movie-engine-primary-color:' . $hex . ';'
			. '--movie-engine-primary-hover-color:' . $hover . ';'
			. '--movie-engine-primary-rgb:' . "{$r},{$g},{$b}" . ';'
			. '}';
	}

	$pt = isset( $pad_data['top'] )    ? floatval( $pad_data['top'] )    : 0;
	$pr = isset( $pad_data['right'] )  ? floatval( $pad_data['right'] )  : 4;
	$pb = isset( $pad_data['bottom'] ) ? floatval( $pad_data['bottom'] ) : 0;
	$pl = isset( $pad_data['left'] )   ? floatval( $pad_data['left'] )   : 4;

	$defaults = array( 'top' => 0, 'right' => 4, 'bottom' => 0, 'left' => 4 );
	$is_default = ( $pt === $defaults['top'] && $pr === $defaults['right'] && $pb === $defaults['bottom'] && $pl === $defaults['left'] && 'rem' === $pu );

	if ( ! $is_default ) {
		$root_vars .= '--hello-movieengine-header-pt:' . $pt . $pu . ';';
		$root_vars .= '--hello-movieengine-header-pr:' . $pr . $pu . ';';
		$root_vars .= '--hello-movieengine-header-pb:' . $pb . $pu . ';';
		$root_vars .= '--hello-movieengine-header-pl:' . $pl . $pu . ';';
	}

	if ( '' !== $header_bg ) {
		$root_vars .= '--hello-movieengine-header-bg:' . esc_attr( $header_bg ) . ';';
		$root_vars .= '--hello-movieengine-header-bg-scroll:' . esc_attr( $header_bg ) . ';';
	}

	$pt_bg = get_theme_mod( 'hello_movieengine_page_title_bg', '' );
	if ( '' !== $pt_bg ) {
		$root_vars .= '--hello-movieengine-page-title-bg:' . esc_attr( $pt_bg ) . ';';
	}

	$pt_min_h = absint( get_theme_mod( 'hello_movieengine_page_title_min_height', 200 ) );
	if ( 200 !== $pt_min_h ) {
		$root_vars .= '--hello-movieengine-pt-min-height:' . $pt_min_h . 'px;';
	}

	$pt_overlay = get_theme_mod( 'hello_movieengine_page_title_overlay', 'rgba(20,20,20,0.7)' );
	if ( 'rgba(20,20,20,0.7)' !== $pt_overlay && '' !== $pt_overlay ) {
		$root_vars .= '--hello-movieengine-pt-overlay:' . esc_attr( $pt_overlay ) . ';';
	}

	$pt_pad_default = wp_json_encode( array(
		'top' => '48', 'right' => '0', 'bottom' => '48', 'left' => '0', 'unit' => 'px',
	) );
	$pt_pad_raw  = get_theme_mod( 'hello_movieengine_page_title_padding', $pt_pad_default );
	$pt_pad_data = json_decode( $pt_pad_raw, true );
	if ( is_array( $pt_pad_data ) ) {
		$ptu  = isset( $pt_pad_data['unit'] ) ? $pt_pad_data['unit'] : 'px';
		$ptt  = isset( $pt_pad_data['top'] ) ? floatval( $pt_pad_data['top'] ) : 48;
		$ptb  = isset( $pt_pad_data['bottom'] ) ? floatval( $pt_pad_data['bottom'] ) : 48;
		$pt_is_default = ( 48.0 === $ptt && 48.0 === $ptb && 'px' === $ptu );
		if ( ! $pt_is_default ) {
			$root_vars .= '--hello-movieengine-pt-pt:' . $ptt . $ptu . ';';
			$root_vars .= '--hello-movieengine-pt-pb:' . $ptb . $ptu . ';';
		}
	}

	/* Layout: container width */
	$container_w = absint( get_theme_mod( 'hello_movieengine_container_width', 1340 ) );
	if ( 1340 !== $container_w ) {
		$root_vars .= '--hello-movieengine-container-width:' . $container_w . 'px;';
	}

	/* Layout: content padding */
	$cp_default = wp_json_encode( array(
		'top' => '40', 'right' => '0', 'bottom' => '40', 'left' => '0', 'unit' => 'px', 'linked' => true,
	) );
	$cp_raw  = get_theme_mod( 'hello_movieengine_content_padding', $cp_default );
	$cp_data = json_decode( $cp_raw, true );
	if ( is_array( $cp_data ) ) {
		$cpu = isset( $cp_data['unit'] ) ? $cp_data['unit'] : 'px';
		$cpt = isset( $cp_data['top'] )    ? floatval( $cp_data['top'] )    : 40;
		$cpr = isset( $cp_data['right'] )  ? floatval( $cp_data['right'] )  : 0;
		$cpb = isset( $cp_data['bottom'] ) ? floatval( $cp_data['bottom'] ) : 40;
		$cpl = isset( $cp_data['left'] )   ? floatval( $cp_data['left'] )   : 0;
		$cp_is_default = ( 40.0 === $cpt && 0.0 === $cpr && 40.0 === $cpb && 0.0 === $cpl && 'px' === $cpu );
		if ( ! $cp_is_default ) {
			$root_vars .= '--hello-movieengine-content-pt:' . $cpt . $cpu . ';';
			$root_vars .= '--hello-movieengine-content-pr:' . $cpr . $cpu . ';';
			$root_vars .= '--hello-movieengine-content-pb:' . $cpb . $cpu . ';';
			$root_vars .= '--hello-movieengine-content-pl:' . $cpl . $cpu . ';';
		}
	}

	/* Layout: sidebar width */
	$sidebar_w = absint( get_theme_mod( 'hello_movieengine_sidebar_width', 300 ) );
	if ( 300 !== $sidebar_w ) {
		$root_vars .= '--hello-movieengine-sidebar-width:' . $sidebar_w . 'px;';
	}

	/* Layout: blog columns */
	$blog_cols = get_theme_mod( 'hello_movieengine_blog_columns', '3' );
	if ( '3' !== $blog_cols ) {
		$root_vars .= '--hello-movieengine-blog-columns:' . absint( $blog_cols ) . ';';
	}

	/* Blog: post image height (auto = natural, or 180/220/260px) */
	$blog_img_h = get_theme_mod( 'hello_movieengine_blog_image_height', 'auto' );
	if ( 'auto' !== $blog_img_h && in_array( $blog_img_h, array( '180', '220', '260' ), true ) ) {
		$root_vars .= '--hello-movieengine-post-thumb-height:' . $blog_img_h . 'px;';
	}

	if ( $root_vars ) {
		$css .= ':root{' . $root_vars . '}';
	}

	$header_border = get_theme_mod( 'hello_movieengine_header_border', true );
	if ( ! $header_border ) {
		$css .= '.hello-movieengine-header--solid{border-bottom:none;}';
	}

	if ( $css ) {
		wp_add_inline_style( 'hello-movieengine-base', $css );
	}
}
