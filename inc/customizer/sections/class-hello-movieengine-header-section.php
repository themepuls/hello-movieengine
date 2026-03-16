<?php
/**
 * Customizer section: Header.
 *
 * Registers all header-related settings, controls, and headings.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Header_Section {

	/**
	 * Register header controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance.
	 */
	public static function register( $wp_customize ) {

		$wp_customize->add_section( 'hello_movieengine_header_section', array(
			'title' => esc_html__( 'Header', 'hello-movieengine' ),
			'panel' => 'hello_movieengine_panel',
		) );

		/* --- Heading: Layout --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_header_layout', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_header_layout', array(
			'label'   => esc_html__( 'Layout', 'hello-movieengine' ),
			'section' => 'hello_movieengine_header_section',
		) ) );

		/* Header style */
		$wp_customize->add_setting( 'hello_movieengine_header_style', array(
			'default'           => 'solid',
			'sanitize_callback' => 'hello_movieengine_sanitize_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_header_style', array(
			'label'   => esc_html__( 'Header Style', 'hello-movieengine' ),
			'section' => 'hello_movieengine_header_section',
			'type'    => 'select',
			'choices' => array(
				'solid'       => esc_html__( 'Solid (Dark)', 'hello-movieengine' ),
				'transparent' => esc_html__( 'Transparent (Overlay)', 'hello-movieengine' ),
			),
		) );

		/* Header width */
		$wp_customize->add_setting( 'hello_movieengine_header_width', array(
			'default'           => 'boxed',
			'sanitize_callback' => 'hello_movieengine_sanitize_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_header_width', array(
			'label'   => esc_html__( 'Header Width', 'hello-movieengine' ),
			'section' => 'hello_movieengine_header_section',
			'type'    => 'select',
			'choices' => array(
				'boxed'     => esc_html__( 'Boxed', 'hello-movieengine' ),
				'fullwidth' => esc_html__( 'Full Width', 'hello-movieengine' ),
			),
		) );

		/* Show search */
		$wp_customize->add_setting( 'hello_movieengine_header_search', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_header_search', array(
			'label'   => esc_html__( 'Show Search', 'hello-movieengine' ),
			'section' => 'hello_movieengine_header_section',
		) ) );

		/* Show user menu */
		$wp_customize->add_setting( 'hello_movieengine_header_user_menu', array(
			'default'           => false,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_header_user_menu', array(
			'label'   => esc_html__( 'Show User Menu', 'hello-movieengine' ),
			'section' => 'hello_movieengine_header_section',
		) ) );

		/* Header border bottom */
		$wp_customize->add_setting( 'hello_movieengine_header_border', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_header_border', array(
			'label'       => esc_html__( 'Bottom Border', 'hello-movieengine' ),
			'description' => esc_html__( 'Show primary-color border at the bottom of the header.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_header_section',
		) ) );

		/* --- Heading: Spacing --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_header_spacing', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_header_spacing', array(
			'label'   => esc_html__( 'Spacing', 'hello-movieengine' ),
			'section' => 'hello_movieengine_header_section',
		) ) );

		/* Header padding */
		$wp_customize->add_setting( 'hello_movieengine_header_padding', array(
			'default'           => wp_json_encode( array(
				'top' => '0', 'right' => '4', 'bottom' => '0', 'left' => '4', 'unit' => 'rem', 'linked' => false,
			) ),
			'sanitize_callback' => 'hello_movieengine_sanitize_spacing',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Spacing_Control( $wp_customize, 'hello_movieengine_header_padding', array(
			'label'   => esc_html__( 'Header Padding', 'hello-movieengine' ),
			'section' => 'hello_movieengine_header_section',
		) ) );

		/* --- Heading: Background --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_header_bg', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_header_bg', array(
			'label'   => esc_html__( 'Background', 'hello-movieengine' ),
			'section' => 'hello_movieengine_header_section',
		) ) );

		/* Header background color (alpha) */
		$wp_customize->add_setting( 'hello_movieengine_header_bg_color', array(
			'default'           => '',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_header_bg_color', array(
			'label'       => esc_html__( 'Header Background', 'hello-movieengine' ),
			'description' => esc_html__( 'Applies to both solid and transparent (scrolled) headers.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_header_section',
		) ) );
	}
}
