<?php
/**
 * Customizer section: Layout.
 *
 * Controls for content width, layout mode, sidebar, and spacing.
 *
 * @package Hello_Cine Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Cine Movie Engine_Layout_Section {

	public static function register( $wp_customize ) {

		$wp_customize->add_section( 'hello_movieengine_layout_section', array(
			'title' => esc_html__( 'Layout', 'hello-movieengine' ),
			'panel' => 'hello_movieengine_panel',
		) );

		/* --- Heading: Container --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_container', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_container', array(
			'label'   => esc_html__( 'Container', 'hello-movieengine' ),
			'section' => 'hello_movieengine_layout_section',
		) ) );

		/* Container width */
		$wp_customize->add_setting( 'hello_movieengine_container_width', array(
			'default'           => 1340,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_container_width', array(
			'label'       => esc_html__( 'Container Width (px)', 'hello-movieengine' ),
			'description' => esc_html__( 'Max width of the main content area.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_layout_section',
			'type'        => 'number',
			'input_attrs' => array( 'min' => 800, 'max' => 1920, 'step' => 10 ),
		) );

		/* --- Heading: Content --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_content_layout', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_content_layout', array(
			'label'   => esc_html__( 'Content', 'hello-movieengine' ),
			'section' => 'hello_movieengine_layout_section',
		) ) );

		/* Content layout */
		$wp_customize->add_setting( 'hello_movieengine_content_layout', array(
			'default'           => 'boxed',
			'sanitize_callback' => 'hello_movieengine_sanitize_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_content_layout', array(
			'label'   => esc_html__( 'Content Style', 'hello-movieengine' ),
			'section' => 'hello_movieengine_layout_section',
			'type'    => 'select',
			'choices' => array(
				'boxed'     => esc_html__( 'Boxed', 'hello-movieengine' ),
				'fullwidth' => esc_html__( 'Full Width', 'hello-movieengine' ),
			),
		) );

		/* Content padding */
		$wp_customize->add_setting( 'hello_movieengine_content_padding', array(
			'default'           => wp_json_encode( array(
				'top' => '40', 'right' => '0', 'bottom' => '40', 'left' => '0', 'unit' => 'px', 'linked' => true,
			) ),
			'sanitize_callback' => 'hello_movieengine_sanitize_spacing',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Spacing_Control( $wp_customize, 'hello_movieengine_content_padding', array(
			'label'   => esc_html__( 'Content Padding', 'hello-movieengine' ),
			'section' => 'hello_movieengine_layout_section',
		) ) );

		/* --- Heading: Sidebar --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_sidebar', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_sidebar', array(
			'label'   => esc_html__( 'Sidebar', 'hello-movieengine' ),
			'section' => 'hello_movieengine_layout_section',
		) ) );

		/* Sidebar enable */
		$wp_customize->add_setting( 'hello_movieengine_sidebar_enable', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_sidebar_enable', array(
			'label'       => esc_html__( 'Enable Sidebar', 'hello-movieengine' ),
			'description' => esc_html__( 'Show sidebar on blog and archive pages.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_layout_section',
		) ) );

		/* Sidebar position */
		$wp_customize->add_setting( 'hello_movieengine_sidebar_position', array(
			'default'           => 'right',
			'sanitize_callback' => 'hello_movieengine_sanitize_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_sidebar_position', array(
			'label'   => esc_html__( 'Sidebar Position', 'hello-movieengine' ),
			'section' => 'hello_movieengine_layout_section',
			'type'    => 'select',
			'choices' => array(
				'right' => esc_html__( 'Right', 'hello-movieengine' ),
				'left'  => esc_html__( 'Left', 'hello-movieengine' ),
			),
		) );

		/* Sidebar width */
		$wp_customize->add_setting( 'hello_movieengine_sidebar_width', array(
			'default'           => 300,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_sidebar_width', array(
			'label'       => esc_html__( 'Sidebar Width (px)', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_layout_section',
			'type'        => 'number',
			'input_attrs' => array( 'min' => 200, 'max' => 500, 'step' => 10 ),
		) );

		/* --- Heading: Blog Grid --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_blog_grid', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_blog_grid', array(
			'label'   => esc_html__( 'Blog Grid', 'hello-movieengine' ),
			'section' => 'hello_movieengine_layout_section',
		) ) );

		/* Blog columns */
		$wp_customize->add_setting( 'hello_movieengine_blog_columns', array(
			'default'           => '3',
			'sanitize_callback' => 'hello_movieengine_sanitize_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_blog_columns', array(
			'label'   => esc_html__( 'Blog Columns (Desktop)', 'hello-movieengine' ),
			'section' => 'hello_movieengine_layout_section',
			'type'    => 'select',
			'choices' => array(
				'1' => esc_html__( '1 Column', 'hello-movieengine' ),
				'2' => esc_html__( '2 Columns', 'hello-movieengine' ),
				'3' => esc_html__( '3 Columns', 'hello-movieengine' ),
				'4' => esc_html__( '4 Columns', 'hello-movieengine' ),
			),
		) );
	}
}
