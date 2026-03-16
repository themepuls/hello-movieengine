<?php
/**
 * Theme setup: supports, menus, image sizes, content width.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hello_movieengine_setup() {
	load_theme_textdomain( 'hello-movieengine', HELLO_MOVIEENGINE_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 200,
		'flex-width'  => true,
		'flex-height' => true,
	) );

	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
		'navigation-widgets',
	) );

	add_theme_support( 'custom-background', array(
		'default-color' => '141414',
	) );

	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );

	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'hello-movieengine' ),
		'footer'  => esc_html__( 'Footer Menu', 'hello-movieengine' ),
	) );

	add_image_size( 'hello-movieengine-card-thumb', 300, 450, true );
	add_image_size( 'hello-movieengine-hero-banner', 1920, 800, true );
}
add_action( 'after_setup_theme', 'hello_movieengine_setup' );

function hello_movieengine_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'hello_movieengine_content_width', 1200 );
}
add_action( 'after_setup_theme', 'hello_movieengine_content_width', 0 );
