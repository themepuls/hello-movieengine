<?php
/**
 * Customizer section: Page Title.
 *
 * Controls which pages display the styled page title banner.
 *
 * @package Hello_Cine Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Cine Movie Engine_Page_Title_Section {

	public static function register( $wp_customize ) {

		$wp_customize->add_section( 'hello_movieengine_page_title_section', array(
			'title' => esc_html__( 'Page Title', 'hello-movieengine' ),
			'panel' => 'hello_movieengine_panel',
		) );

		/* --- Heading: Visibility --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_page_title_visibility', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_page_title_visibility', array(
			'label'   => esc_html__( 'Visibility', 'hello-movieengine' ),
			'section' => 'hello_movieengine_page_title_section',
		) ) );

		/* Show page title on */
		$default_pages = wp_json_encode( array( 'blog', 'archives', 'search', 'author' ) );
		$wp_customize->add_setting( 'hello_movieengine_page_title_locations', array(
			'default'           => $default_pages,
			'sanitize_callback' => 'hello_movieengine_sanitize_multiselect',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Multiselect_Control( $wp_customize, 'hello_movieengine_page_title_locations', array(
			'label'       => esc_html__( 'Show Page Title On', 'hello-movieengine' ),
			'description' => esc_html__( 'Select where the page title banner appears.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_page_title_section',
			'choices'     => array(
				'blog'        => esc_html__( 'Blog', 'hello-movieengine' ),
				'single_post' => esc_html__( 'Single Post', 'hello-movieengine' ),
				'single_page' => esc_html__( 'Single Page', 'hello-movieengine' ),
				'archives'     => esc_html__( 'Archives', 'hello-movieengine' ),
				'search'       => esc_html__( 'Search', 'hello-movieengine' ),
				'author'       => esc_html__( 'Author', 'hello-movieengine' ),
				'404'          => esc_html__( '404', 'hello-movieengine' ),
			),
		) ) );

		/* Specific pages */
		$wp_customize->add_setting( 'hello_movieengine_page_title_pages', array(
			'default'           => '[]',
			'sanitize_callback' => 'hello_movieengine_sanitize_page_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Page_Select_Control( $wp_customize, 'hello_movieengine_page_title_pages', array(
			'label'       => esc_html__( 'Select Pages', 'hello-movieengine' ),
			'description' => esc_html__( 'Choose specific pages to show the title banner.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_page_title_section',
		) ) );

		/* --- Heading: Style --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_page_title_style', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_page_title_style', array(
			'label'   => esc_html__( 'Style', 'hello-movieengine' ),
			'section' => 'hello_movieengine_page_title_section',
		) ) );

		/* Min height */
		$wp_customize->add_setting( 'hello_movieengine_page_title_min_height', array(
			'default'           => '200',
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_page_title_min_height', array(
			'label'       => esc_html__( 'Min Height (px)', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_page_title_section',
			'type'        => 'number',
			'input_attrs' => array( 'min' => 0, 'max' => 600, 'step' => 10 ),
		) );

		/* Padding */
		$wp_customize->add_setting( 'hello_movieengine_page_title_padding', array(
			'default'           => wp_json_encode( array(
				'top' => '48', 'right' => '0', 'bottom' => '48', 'left' => '0', 'unit' => 'px', 'linked' => true,
			) ),
			'sanitize_callback' => 'hello_movieengine_sanitize_spacing',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Spacing_Control( $wp_customize, 'hello_movieengine_page_title_padding', array(
			'label'   => esc_html__( 'Padding', 'hello-movieengine' ),
			'section' => 'hello_movieengine_page_title_section',
		) ) );

		/* Text alignment */
		$wp_customize->add_setting( 'hello_movieengine_page_title_align', array(
			'default'           => 'center',
			'sanitize_callback' => 'hello_movieengine_sanitize_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_page_title_align', array(
			'label'   => esc_html__( 'Text Alignment', 'hello-movieengine' ),
			'section' => 'hello_movieengine_page_title_section',
			'type'    => 'select',
			'choices' => array(
				'left'   => esc_html__( 'Left', 'hello-movieengine' ),
				'center' => esc_html__( 'Center', 'hello-movieengine' ),
				'right'  => esc_html__( 'Right', 'hello-movieengine' ),
			),
		) );

		/* --- Heading: Background --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_page_title_background', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_page_title_background', array(
			'label'   => esc_html__( 'Background', 'hello-movieengine' ),
			'section' => 'hello_movieengine_page_title_section',
		) ) );

		/* Background color */
		$wp_customize->add_setting( 'hello_movieengine_page_title_bg', array(
			'default'           => '',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_page_title_bg', array(
			'label'   => esc_html__( 'Background Color', 'hello-movieengine' ),
			'section' => 'hello_movieengine_page_title_section',
		) ) );

		/* Default background image */
		$wp_customize->add_setting( 'hello_movieengine_page_title_bg_image', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hello_movieengine_page_title_bg_image', array(
			'label'       => esc_html__( 'Default Background Image', 'hello-movieengine' ),
			'description' => esc_html__( 'Used as fallback. Pages can override this with their own featured image.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_page_title_section',
		) ) );

		/* Overlay color */
		$wp_customize->add_setting( 'hello_movieengine_page_title_overlay', array(
			'default'           => 'rgba(20,20,20,0.7)',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_page_title_overlay', array(
			'label'       => esc_html__( 'Image Overlay Color', 'hello-movieengine' ),
			'description' => esc_html__( 'Dark overlay on top of the background image for readability.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_page_title_section',
		) ) );
	}
}
