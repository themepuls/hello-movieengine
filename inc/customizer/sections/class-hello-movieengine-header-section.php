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
			'title'    => esc_html__( 'Header', 'hello-movieengine' ),
			'priority' => 4,
		) );

		/* Astra-style Section Tabs (GENERAL vs DESIGN) */
		$wp_customize->add_setting( 'hello_movieengine_header_tabs_nav', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Section_Tabs_Control( $wp_customize, 'hello_movieengine_header_tabs_nav', array(
			'section'  => 'hello_movieengine_header_section',
			'priority' => 1,
		) ) );

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
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Button_Group_Control( $wp_customize, 'hello_movieengine_header_style', array(
			'label'       => esc_html__( 'Header Style', 'hello-movieengine' ),
			'description' => esc_html__( 'Transparent uses a fixed overlay on selected pages. Solid is always a normal header.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_header_section',
			'choices'     => array(
				'solid'       => esc_html__( 'Solid', 'hello-movieengine' ),
				'transparent' => esc_html__( 'Transparent', 'hello-movieengine' ),
			),
		) ) );

		/* Header width */
		$wp_customize->add_setting( 'hello_movieengine_header_width', array(
			'default'           => 'boxed',
			'sanitize_callback' => 'hello_movieengine_sanitize_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Button_Group_Control( $wp_customize, 'hello_movieengine_header_width', array(
			'label'       => esc_html__( 'Header Width', 'hello-movieengine' ),
			'description' => esc_html__( 'Boxed keeps the logo and menu inside a max width. Full Width spans the screen with side padding only.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_header_section',
			'choices'     => array(
				'boxed'     => esc_html__( 'Boxed', 'hello-movieengine' ),
				'fullwidth' => esc_html__( 'Full Width', 'hello-movieengine' ),
			),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_header_container_width', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_header_container_width', array(
			'label'             => esc_html__( 'Header Max Width (px)', 'hello-movieengine' ),
			'description'       => esc_html__( 'Used when Header Width is Boxed. Set 0 to match Layout → Container Width.', 'hello-movieengine' ),
			'section'           => 'hello_movieengine_header_section',
			'type'              => 'number',
			'input_attrs'       => array( 'min' => 0, 'max' => 1920, 'step' => 10 ),
			'active_callback'   => 'hello_movieengine_customize_header_is_boxed',
		) );

		/* Fixed header on selected pages (Transparent overlay + position fixed) */
		$default_fixed = wp_json_encode( array( 'front_page', 'single_movie', 'single_series', 'single_episode' ) );
		$wp_customize->add_setting( 'hello_movieengine_header_fixed_locations', array(
			'default'           => $default_fixed,
			'sanitize_callback' => 'hello_movieengine_sanitize_multiselect',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Multiselect_Control( $wp_customize, 'hello_movieengine_header_fixed_locations', array(
			'label'       => esc_html__( 'Fixed Header On', 'hello-movieengine' ),
			'description' => esc_html__( 'Choose where the header is fixed (overlay). Unselected pages use a normal solid header.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_header_section',
			'choices'     => array(
				'front_page'     => esc_html__( 'Front Page', 'hello-movieengine' ),
				'single_movie'   => esc_html__( 'Single Movie', 'hello-movieengine' ),
				'single_series'  => esc_html__( 'Single Series', 'hello-movieengine' ),
				'single_episode' => esc_html__( 'Single Episode', 'hello-movieengine' ),
				'blog'           => esc_html__( 'Blog', 'hello-movieengine' ),
				'single_post'    => esc_html__( 'Single Post', 'hello-movieengine' ),
				'single_page'    => esc_html__( 'Pages', 'hello-movieengine' ),
				'archives'       => esc_html__( 'Archives', 'hello-movieengine' ),
				'search'         => esc_html__( 'Search', 'hello-movieengine' ),
				'author'         => esc_html__( 'Author', 'hello-movieengine' ),
				'404'            => esc_html__( '404', 'hello-movieengine' ),
			),
		) ) );

		/* Specific pages, same picker as Page Title */
		$wp_customize->add_setting( 'hello_movieengine_header_fixed_pages', array(
			'default'           => '[]',
			'sanitize_callback' => 'hello_movieengine_sanitize_page_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Page_Select_Control( $wp_customize, 'hello_movieengine_header_fixed_pages', array(
			'label'       => esc_html__( 'Select Pages', 'hello-movieengine' ),
			'description' => esc_html__( 'Choose specific pages for the fixed header. These are in addition to the locations above.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_header_section',
		) ) );

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
				'top' => '0', 'right' => '1.25', 'bottom' => '0', 'left' => '1.25', 'unit' => 'rem', 'linked' => true,
			) ),
			'sanitize_callback' => 'hello_movieengine_sanitize_spacing',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Spacing_Control( $wp_customize, 'hello_movieengine_header_padding', array(
			'label'   => esc_html__( 'Header Padding', 'hello-movieengine' ),
			'section' => 'hello_movieengine_header_section',
		) ) );

		/* --- Heading: Background & Colors --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_header_bg', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_header_bg', array(
			'label'   => esc_html__( 'Colors & Background', 'hello-movieengine' ),
			'section' => 'hello_movieengine_header_section',
		) ) );

		/* Header background color (alpha) */
		$wp_customize->add_setting( 'hello_movieengine_header_bg_color', array(
			'default'           => '',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_header_bg_color', array(
			'label'       => esc_html__( 'Header Background (Default)', 'hello-movieengine' ),
			'description' => esc_html__( 'Applies to solid headers and initial transparent header state.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_header_section',
		) ) );

		/* Fixed / Scrolled Header background color (alpha) */
		$wp_customize->add_setting( 'hello_movieengine_header_bg_scroll_color', array(
			'default'           => '',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_header_bg_scroll_color', array(
			'label'       => esc_html__( 'Fixed / Scrolled Header Background', 'hello-movieengine' ),
			'description' => esc_html__( 'Background color and opacity when header is fixed/scrolled down.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_header_section',
		) ) );

		/* Dropdown & Mega Menu background color (alpha) */
		$wp_customize->add_setting( 'hello_movieengine_menu_dropdown_bg', array(
			'default'           => '',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_menu_dropdown_bg', array(
			'label'       => esc_html__( 'Dropdown & Mega Menu Background', 'hello-movieengine' ),
			'description' => esc_html__( 'Background for submenu dropdowns, mega menu panels, and side-rail flyouts.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_header_section',
		) ) );

		/* Menu Link color */
		$wp_customize->add_setting( 'hello_movieengine_menu_link_color', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hello_movieengine_menu_link_color', array(
			'label'       => esc_html__( 'Menu Link Color', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_header_section',
		) ) );

		/* Menu Link Hover color */
		$wp_customize->add_setting( 'hello_movieengine_menu_link_hover_color', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hello_movieengine_menu_link_hover_color', array(
			'label'       => esc_html__( 'Menu Link Hover Color', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_header_section',
		) ) );
	}
}
