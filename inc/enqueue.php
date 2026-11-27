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

	wp_enqueue_style(
		'hello-movieengine-base',
		Hello_Movie_Engine_Style_Registry::core_uri( 'css/base.css' ),
		array(),
		$v
	);

	Hello_Movie_Engine_Style_Registry::enqueue_styles( $v );

	$mega_rich_css = HELLO_MOVIEENGINE_DIR . '/assets/core/css/mega-menu-rich.css';
	if ( file_exists( $mega_rich_css ) ) {
		wp_enqueue_style(
			'hello-movieengine-mega-rich',
			Hello_Movie_Engine_Style_Registry::core_uri( 'css/mega-menu-rich.css' ),
			array( 'hello-movieengine-header' ),
			filemtime( $mega_rich_css )
		);
	}

	wp_enqueue_style( 'hello-movieengine-style', get_stylesheet_uri(), array(), $v );

	wp_enqueue_script(
		'hello-movieengine-navigation',
		Hello_Movie_Engine_Style_Registry::core_uri( 'js/navigation.js' ),
		array(),
		$v,
		true
	);

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
		'top' => '0', 'right' => '1.25', 'bottom' => '0', 'left' => '1.25', 'unit' => 'rem',
	) );
	$pad_raw  = get_theme_mod( 'hello_movieengine_header_padding', $pad_default );
	$pad_data = json_decode( $pad_raw, true );

	if ( ! is_array( $pad_data ) ) {
		$pad_data = array( 'top' => '0', 'right' => '1.25', 'bottom' => '0', 'left' => '1.25', 'unit' => 'rem' );
	}

	$pu = isset( $pad_data['unit'] ) ? $pad_data['unit'] : 'rem';

	$css       = '';
	$root_vars = '';

	if ( class_exists( 'Hello_Movie_Engine_Colors_Section' ) ) {
		$palette = Hello_Movie_Engine_Colors_Section::get_color_palette();

		$hex2rgb = function( $hex ) {
			$h = ltrim( (string) $hex, '#' );
			if ( 3 === strlen( $h ) ) {
				$r = hexdec( substr( $h, 0, 1 ) . substr( $h, 0, 1 ) );
				$g = hexdec( substr( $h, 1, 1 ) . substr( $h, 1, 1 ) );
				$b = hexdec( substr( $h, 2, 1 ) . substr( $h, 2, 1 ) );
			} elseif ( 6 === strlen( $h ) ) {
				$r = hexdec( substr( $h, 0, 2 ) );
				$g = hexdec( substr( $h, 2, 2 ) );
				$b = hexdec( substr( $h, 4, 2 ) );
			} else {
				return '229, 9, 20';
			}
			return "{$r}, {$g}, {$b}";
		};

		$primary_rgb = $hex2rgb( $palette['primary'] );
		$bg_rgb      = $hex2rgb( $palette['bg'] );
		$surface_rgb = $hex2rgb( $palette['surface'] );

		$root_vars .= '--movie-engine-primary-color:' . $palette['primary'] . ';';
		$root_vars .= '--movie-engine-primary-hover-color:' . $palette['hover'] . ';';
		$root_vars .= '--movie-engine-primary-rgb:' . $primary_rgb . ';';
		$root_vars .= '--movie-engine-body-color:' . $palette['text'] . ';';
		$root_vars .= '--movie-engine-muted-color:' . $palette['gray'] . ';';
		$root_vars .= '--movie-engine-border-color:' . $palette['border'] . ';';
		$root_vars .= '--movie-engine-bg-color:' . $palette['bg'] . ';';
		$root_vars .= '--movie-engine-bg-rgb:' . $bg_rgb . ';';
		$root_vars .= '--movie-engine-surface-color:' . $palette['surface'] . ';';
		$root_vars .= '--movie-engine-black-color:' . $palette['surface'] . ';';
		$root_vars .= '--movie-engine-black-rgb:' . $surface_rgb . ';';
		$root_vars .= '--movie-engine-container-width:' . $palette['container_width'] . 'px;';
		$root_vars .= '--movie-engine-border-radius:' . $palette['border_radius'] . 'px;';

		$root_vars .= '--hello-movieengine-primary-color:' . $palette['primary'] . ';';
		$root_vars .= '--hello-movieengine-primary-hover:' . $palette['hover'] . ';';
		$root_vars .= '--hello-movieengine-primary-rgb:' . $primary_rgb . ';';
		$root_vars .= '--hello-movieengine-bg-color:' . $palette['bg'] . ';';
		$root_vars .= '--hello-movieengine-surface-color:' . $palette['surface'] . ';';
		$root_vars .= '--hello-movieengine-border-color:' . $palette['border'] . ';';
		$root_vars .= '--hello-movieengine-text-color:' . $palette['text'] . ';';
		$root_vars .= '--hello-movieengine-muted-color:' . $palette['gray'] . ';';
		$root_vars .= '--hello-movieengine-radius:' . $palette['border_radius'] . 'px;';
		$root_vars .= '--hello-movieengine-container-width:' . $palette['container_width'] . 'px;';
	}

	$pt = isset( $pad_data['top'] )    ? floatval( $pad_data['top'] )    : 0;
	$pr = isset( $pad_data['right'] )  ? floatval( $pad_data['right'] )  : 1.25;
	$pb = isset( $pad_data['bottom'] ) ? floatval( $pad_data['bottom'] ) : 0;
	$pl = isset( $pad_data['left'] )   ? floatval( $pad_data['left'] )   : 1.25;

	$root_vars .= '--hello-movieengine-header-pt:' . $pt . $pu . ';';
	$root_vars .= '--hello-movieengine-header-pr:' . $pr . $pu . ';';
	$root_vars .= '--hello-movieengine-header-pb:' . $pb . $pu . ';';
	$root_vars .= '--hello-movieengine-header-pl:' . $pl . $pu . ';';

	if ( '' !== $header_bg ) {
		$root_vars .= '--hello-movieengine-header-bg:' . esc_attr( $header_bg ) . ';';
	}

	$header_bg_scroll = get_theme_mod( 'hello_movieengine_header_bg_scroll_color', '' );
	if ( '' !== $header_bg_scroll ) {
		$root_vars .= '--hello-movieengine-header-bg-scroll:' . esc_attr( $header_bg_scroll ) . ';';
	} elseif ( '' !== $header_bg ) {
		$root_vars .= '--hello-movieengine-header-bg-scroll:' . esc_attr( $header_bg ) . ';';
	}

	$dropdown_bg = get_theme_mod( 'hello_movieengine_menu_dropdown_bg', '' );
	if ( '' !== $dropdown_bg ) {
		$root_vars .= '--hello-movieengine-dropdown-bg:' . esc_attr( $dropdown_bg ) . ';';
	}

	$nav_color = get_theme_mod( 'hello_movieengine_menu_link_color', '' );
	if ( '' !== $nav_color ) {
		$root_vars .= '--hello-movieengine-nav-link-color:' . esc_attr( $nav_color ) . ';';
	}

	$nav_hover = get_theme_mod( 'hello_movieengine_menu_link_hover_color', '' );
	if ( '' !== $nav_hover ) {
		$root_vars .= '--hello-movieengine-nav-link-hover:' . esc_attr( $nav_hover ) . ';';
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

	$container_w = absint( get_theme_mod( 'hello_movieengine_container_width', 1340 ) );
	if ( 1340 !== $container_w ) {
		$root_vars .= '--hello-movieengine-container-width:' . $container_w . 'px;';
	}

	$header_container_w = hello_movieengine_get_header_container_width();
	if ( $header_container_w > 0 && 'boxed' === hello_movieengine_get_header_width() ) {
		$root_vars .= '--hello-movieengine-header-container-width:' . $header_container_w . 'px;';
	}

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

	$sidebar_w = absint( get_theme_mod( 'hello_movieengine_sidebar_width', 300 ) );
	if ( 300 !== $sidebar_w ) {
		$root_vars .= '--hello-movieengine-sidebar-width:' . $sidebar_w . 'px;';
	}

	$blog_cols = get_theme_mod( 'hello_movieengine_blog_columns', '3' );
	if ( '3' !== $blog_cols ) {
		$root_vars .= '--hello-movieengine-blog-columns:' . absint( $blog_cols ) . ';';
	}

	$blog_img_h = get_theme_mod( 'hello_movieengine_blog_image_height', 'auto' );
	if ( 'auto' !== $blog_img_h && in_array( $blog_img_h, array( '180', '220', '260' ), true ) ) {
		$root_vars .= '--hello-movieengine-post-thumb-height:' . $blog_img_h . 'px;';
	}

	if ( function_exists( 'hello_movieengine_ott_feed_root_vars' ) ) {
		$ott_vars = hello_movieengine_ott_feed_root_vars();
		if ( $ott_vars ) {
			$root_vars .= $ott_vars;
		}
	}

	if ( function_exists( 'hello_movieengine_watch_hero_root_vars' ) ) {
		$watch_hero_vars = hello_movieengine_watch_hero_root_vars();
		if ( $watch_hero_vars ) {
			$root_vars .= $watch_hero_vars;
		}
	}

	if ( function_exists( 'hello_movieengine_footer_root_vars' ) ) {
		$footer_vars = hello_movieengine_footer_root_vars();
		if ( $footer_vars ) {
			$root_vars .= $footer_vars;
		}
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