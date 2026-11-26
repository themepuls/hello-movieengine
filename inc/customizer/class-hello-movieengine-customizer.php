<?php
/**
 * Customizer orchestrator.
 *
 * Loads all control classes, sanitize helpers, and section registrations.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Sanitize callbacks & helpers (no WP_Customize_Control dependency) */
require_once HELLO_MOVIEENGINE_DIR . '/inc/customizer/sanitize.php';

/**
 * Register the main panel and delegate to section classes.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function hello_movieengine_customize_register( $wp_customize ) {

	$dir = HELLO_MOVIEENGINE_DIR . '/inc/customizer';

	/* Controls (WP_Customize_Control available inside customize_register) */
	require_once $dir . '/controls/class-hello-movieengine-divider-section.php';
	require_once $dir . '/controls/class-hello-movieengine-section-tabs-control.php';
	require_once $dir . '/controls/class-hello-movieengine-visual-radio-control.php';
	require_once $dir . '/controls/class-hello-movieengine-spacing-control.php';
	require_once $dir . '/controls/class-hello-movieengine-alpha-color-control.php';
	require_once $dir . '/controls/class-hello-movieengine-toggle-control.php';
	require_once $dir . '/controls/class-hello-movieengine-button-group-control.php';
	require_once $dir . '/controls/class-hello-movieengine-heading-control.php';
	require_once $dir . '/controls/class-hello-movieengine-multiselect-control.php';
	require_once $dir . '/controls/class-hello-movieengine-page-select-control.php';
	require_once $dir . '/controls/class-hello-movieengine-section-builder-control.php';
	require_once $dir . '/controls/class-hello-movieengine-nav-repeater-control.php';

	/* Sections */
	require_once $dir . '/sections/site-style/class-hello-movieengine-site-style-section.php';
	require_once $dir . '/sections/class-hello-movieengine-front-page-section.php';
	require_once $dir . '/sections/ott-feed/class-hello-movieengine-ott-feed-section.php';
	require_once $dir . '/sections/class-hello-movieengine-single-movie-section.php';
	require_once $dir . '/sections/class-hello-movieengine-tv-series-section.php';
	require_once $dir . '/sections/class-hello-movieengine-movie-archive-section.php';
	require_once $dir . '/sections/class-hello-movieengine-player-section.php';
	require_once $dir . '/sections/class-hello-movieengine-header-section.php';
	require_once $dir . '/sections/class-hello-movieengine-colors-section.php';
	require_once $dir . '/sections/class-hello-movieengine-animation-section.php';
	require_once $dir . '/sections/class-hello-movieengine-layout-section.php';
	require_once $dir . '/sections/class-hello-movieengine-blog-section.php';
	require_once $dir . '/sections/class-hello-movieengine-page-title-section.php';
	require_once $dir . '/sections/class-hello-movieengine-bottom-nav-section.php';
	require_once $dir . '/sections/class-hello-movieengine-404-section.php';
	require_once $dir . '/sections/class-hello-movieengine-footer-section.php';

	/* --- Group Heading 1: General Options --- */
	$wp_customize->add_section( new Hello_Movie_Engine_Customize_Divider_Section( $wp_customize, 'hello_movieengine_group_general', array(
		'title'    => esc_html__( 'General Options', 'hello-movieengine' ),
		'priority' => 1,
	) ) );

	/* Register General Sections */
	Hello_Movie_Engine_Site_Style_Section::register( $wp_customize );
	Hello_Movie_Engine_Header_Section::register( $wp_customize );
	Hello_Movie_Engine_Footer_Section::register( $wp_customize );
	Hello_Movie_Engine_Layout_Section::register( $wp_customize );
	Hello_Movie_Engine_Colors_Section::register( $wp_customize );
	Hello_Movie_Engine_Animation_Section::register( $wp_customize );

	/* --- Group Heading 2: Movie & OTT Streaming --- */
	$wp_customize->add_section( new Hello_Movie_Engine_Customize_Divider_Section( $wp_customize, 'hello_movieengine_group_movie', array(
		'title'    => esc_html__( 'Movie & OTT Streaming', 'hello-movieengine' ),
		'priority' => 20,
	) ) );

	/* Register Movie & OTT Sections */
	Hello_Movie_Engine_Front_Page_Section::register( $wp_customize );
	Hello_Movie_Engine_Ott_Feed_Section::register( $wp_customize );
	Hello_Movie_Engine_Single_Movie_Section::register( $wp_customize );
	Hello_Movie_Engine_Tv_Series_Section::register( $wp_customize );
	Hello_Movie_Engine_Movie_Archive_Section::register( $wp_customize );
	Hello_Movie_Engine_Player_Section::register( $wp_customize );
	Hello_Movie_Engine_Bottom_Nav_Section::register( $wp_customize );

	/* --- Group Heading 3: Post Types & Pages --- */
	$wp_customize->add_section( new Hello_Movie_Engine_Customize_Divider_Section( $wp_customize, 'hello_movieengine_group_pages', array(
		'title'    => esc_html__( 'Post Types & Pages', 'hello-movieengine' ),
		'priority' => 40,
	) ) );

	/* Register Post Types & Pages Sections */
	Hello_Movie_Engine_Blog_Section::register( $wp_customize );
	Hello_Movie_Engine_Page_Title_Section::register( $wp_customize );
	Hello_Movie_Engine_404_Section::register( $wp_customize );

	/* Live-preview transports */
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.hello-movieengine-header__site-title a',
			'render_callback' => function () {
				bloginfo( 'name' );
			},
		) );

		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.hello-movieengine-header__site-desc',
			'render_callback' => function () {
				bloginfo( 'description' );
			},
		) );

		$wp_customize->selective_refresh->add_partial( 'hello_movieengine_footer_copyright', array(
			'selector'        => '.hello-movieengine-footer__copyright-text',
			'render_callback' => 'hello_movieengine_get_copyright_text',
		) );
	}
}
add_action( 'customize_register', 'hello_movieengine_customize_register' );

/**
 * Enqueue live-preview JS.
 */
function hello_movieengine_customize_preview_js() {
	wp_enqueue_script(
		'hello-movieengine-customizer',
		HELLO_MOVIEENGINE_URI . '/assets/js/customizer.js',
		array( 'customize-preview' ),
		HELLO_MOVIEENGINE_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'hello_movieengine_customize_preview_js' );

/**
 * Enqueue global Customizer control assets for a modern premium UI.
 */
function hello_movieengine_customize_controls_assets() {
	$css_file = HELLO_MOVIEENGINE_DIR . '/assets/admin/css/customizer-controls.css';
	$js_file  = HELLO_MOVIEENGINE_DIR . '/assets/admin/js/customizer-controls.js';
	$css_ver  = file_exists( $css_file ) ? (string) filemtime( $css_file ) : HELLO_MOVIEENGINE_VERSION;
	$js_ver   = file_exists( $js_file ) ? (string) filemtime( $js_file ) : HELLO_MOVIEENGINE_VERSION;

	wp_enqueue_style(
		'hello-movieengine-customizer-controls',
		HELLO_MOVIEENGINE_URI . '/assets/admin/css/customizer-controls.css',
		array(),
		$css_ver
	);

	wp_enqueue_script(
		'hello-movieengine-customizer-controls',
		HELLO_MOVIEENGINE_URI . '/assets/admin/js/customizer-controls.js',
		array( 'jquery', 'customize-controls' ),
		$js_ver,
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'hello_movieengine_customize_controls_assets' );
