<?php
/**
 * Customizer orchestrator.
 *
 * Loads all control classes, sanitize helpers, and section registrations.
 *
 * @package Hello_Cine Movie Engine
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
	require_once $dir . '/controls/class-hello-movieengine-spacing-control.php';
	require_once $dir . '/controls/class-hello-movieengine-alpha-color-control.php';
	require_once $dir . '/controls/class-hello-movieengine-toggle-control.php';
	require_once $dir . '/controls/class-hello-movieengine-heading-control.php';
	require_once $dir . '/controls/class-hello-movieengine-multiselect-control.php';
	require_once $dir . '/controls/class-hello-movieengine-page-select-control.php';

	/* Sections */
	require_once $dir . '/sections/class-hello-movieengine-header-section.php';
	require_once $dir . '/sections/class-hello-movieengine-colors-section.php';
	require_once $dir . '/sections/class-hello-movieengine-layout-section.php';
	require_once $dir . '/sections/class-hello-movieengine-blog-section.php';
	require_once $dir . '/sections/class-hello-movieengine-page-title-section.php';
	require_once $dir . '/sections/class-hello-movieengine-404-section.php';
	require_once $dir . '/sections/class-hello-movieengine-footer-section.php';

	/* Main panel */
	$wp_customize->add_panel( 'hello_movieengine_panel', array(
		'title'    => esc_html__( 'Hello Cine Movie Engine', 'hello-movieengine' ),
		'priority' => 30,
	) );

	/* Register each section */
	Hello_Cine_Movie_Engine_Header_Section::register( $wp_customize );
	Hello_Cine_Movie_Engine_Colors_Section::register( $wp_customize );
	Hello_Cine_Movie_Engine_Layout_Section::register( $wp_customize );
	Hello_Cine_Movie_Engine_Blog_Section::register( $wp_customize );
	Hello_Cine_Movie_Engine_Page_Title_Section::register( $wp_customize );
	Hello_Cine_Movie_Engine_404_Section::register( $wp_customize );
	Hello_Cine_Movie_Engine_Footer_Section::register( $wp_customize );

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
