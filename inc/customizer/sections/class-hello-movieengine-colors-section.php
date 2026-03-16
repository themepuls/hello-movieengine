<?php
/**
 * Customizer section: Colors.
 *
 * Registers brand / color settings.
 *
 * @package Hello_Cine Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Cine_Movie_Engine_Colors_Section {

	/**
	 * Register color controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance.
	 */
	public static function register( $wp_customize ) {

		$wp_customize->add_section( 'hello_movieengine_colors_section', array(
			'title' => esc_html__( 'Colors', 'hello-movieengine' ),
			'panel' => 'hello_movieengine_panel',
		) );

		/* --- Heading: Brand --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_brand', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Cine_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_brand', array(
			'label'   => esc_html__( 'Brand', 'hello-movieengine' ),
			'section' => 'hello_movieengine_colors_section',
		) ) );

		/* Primary color */
		$wp_customize->add_setting( 'hello_movieengine_primary_color', array(
			'default'           => '#E50914',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hello_movieengine_primary_color', array(
			'label'   => esc_html__( 'Primary Color', 'hello-movieengine' ),
			'section' => 'hello_movieengine_colors_section',
		) ) );
	}
}
