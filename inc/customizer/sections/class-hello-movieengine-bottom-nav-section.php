<?php
/**
 * Customizer section: Mobile Bottom Navigation.
 *
 * Registers controls for enabling/disabling, display breakpoint, label visibility,
 * page exclusions, color scheme, and the sortable menu items repeater.
 *
 * @package Hello_Movie Engine
 * @since 1.0.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Bottom_Nav_Section {

	/**
	 * Register section controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance.
	 */
	public static function register( $wp_customize ) {

		$wp_customize->add_section( 'hello_movieengine_bottom_nav_section', array(
			'title'       => esc_html__( 'Mobile Bottom Navigation', 'hello-movieengine' ),
			'description' => esc_html__( 'Configure an app-like bottom navigation bar for mobile and tablet visitors.', 'hello-movieengine' ),
			'panel'       => 'hello_movieengine_panel',
			'priority'    => 45,
		) );

		/* --------------------------------------------------------
		   Heading: General Settings
		   -------------------------------------------------------- */
		$wp_customize->add_setting( 'hello_movieengine_heading_bottom_nav_general', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_bottom_nav_general', array(
			'label'   => esc_html__( 'General Settings', 'hello-movieengine' ),
			'section' => 'hello_movieengine_bottom_nav_section',
		) ) );

		/* Enable / Disable */
		$wp_customize->add_setting( 'hello_movieengine_bottom_nav_enable', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_bottom_nav_enable', array(
			'label'       => esc_html__( 'Enable Bottom Navigation', 'hello-movieengine' ),
			'description' => esc_html__( 'Display the fixed bottom navigation bar.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_bottom_nav_section',
		) ) );

		/* Breakpoint: Mobile only vs Mobile & Tablet */
		$wp_customize->add_setting( 'hello_movieengine_bottom_nav_breakpoint', array(
			'default'           => 'mobile',
			'sanitize_callback' => 'hello_movieengine_sanitize_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_bottom_nav_breakpoint', array(
			'label'       => esc_html__( 'Display On', 'hello-movieengine' ),
			'description' => esc_html__( 'Choose screen sizes where navigation should be visible.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_bottom_nav_section',
			'type'        => 'select',
			'choices'     => array(
				'mobile' => esc_html__( 'Mobile Only (≤ 767px)', 'hello-movieengine' ),
				'tablet' => esc_html__( 'Mobile & Tablet (≤ 1024px)', 'hello-movieengine' ),
			),
		) );

		/* Show / Hide Text Labels */
		$wp_customize->add_setting( 'hello_movieengine_bottom_nav_show_labels', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_bottom_nav_show_labels', array(
			'label'       => esc_html__( 'Show Text Labels', 'hello-movieengine' ),
			'description' => esc_html__( 'Display text labels underneath item icons.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_bottom_nav_section',
		) ) );

		/* Exclude Pages */
		$wp_customize->add_setting( 'hello_movieengine_bottom_nav_exclude_pages', array(
			'default'           => '[]',
			'sanitize_callback' => 'hello_movieengine_sanitize_page_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Page_Select_Control( $wp_customize, 'hello_movieengine_bottom_nav_exclude_pages', array(
			'label'       => esc_html__( 'Exclude Pages', 'hello-movieengine' ),
			'description' => esc_html__( 'Select pages where bottom navigation should be hidden.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_bottom_nav_section',
		) ) );

		/* --------------------------------------------------------
		   Heading: Menu Items
		   -------------------------------------------------------- */
		$wp_customize->add_setting( 'hello_movieengine_heading_bottom_nav_items', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_bottom_nav_items', array(
			'label'   => esc_html__( 'Navigation Items', 'hello-movieengine' ),
			'section' => 'hello_movieengine_bottom_nav_section',
		) ) );

		/* Sortable Repeater Items */
		$wp_customize->add_setting( 'hello_movieengine_bottom_nav_items', array(
			'default'           => wp_json_encode( Hello_Movie_Engine_Bottom_Nav::get_default_items_config() ),
			'sanitize_callback' => 'hello_movieengine_sanitize_nav_repeater',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Nav_Repeater_Control( $wp_customize, 'hello_movieengine_bottom_nav_items', array(
			'label'       => esc_html__( 'Menu Items (3–5 recommended)', 'hello-movieengine' ),
			'description' => esc_html__( 'Drag items to reorder. Expand an item to configure type, label, icon, and visibility.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_bottom_nav_section',
		) ) );

		/* --------------------------------------------------------
		   Heading: Colors & Styling
		   -------------------------------------------------------- */
		$wp_customize->add_setting( 'hello_movieengine_heading_bottom_nav_colors', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_bottom_nav_colors', array(
			'label'   => esc_html__( 'Colors & Styling', 'hello-movieengine' ),
			'section' => 'hello_movieengine_bottom_nav_section',
		) ) );

		/* Background Color */
		$wp_customize->add_setting( 'hello_movieengine_bottom_nav_bg_color', array(
			'default'           => 'rgba(18, 18, 18, 0.96)',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_bottom_nav_bg_color', array(
			'label'       => esc_html__( 'Bar Background Color', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_bottom_nav_section',
		) ) );

		/* Top Border Color */
		$wp_customize->add_setting( 'hello_movieengine_bottom_nav_border_color', array(
			'default'           => 'rgba(255, 255, 255, 0.08)',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_bottom_nav_border_color', array(
			'label'       => esc_html__( 'Top Border Color', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_bottom_nav_section',
		) ) );

		/* Icon Color */
		$wp_customize->add_setting( 'hello_movieengine_bottom_nav_icon_color', array(
			'default'           => '#a0a0a0',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_bottom_nav_icon_color', array(
			'label'       => esc_html__( 'Icon Color (Default)', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_bottom_nav_section',
		) ) );

		/* Label Color */
		$wp_customize->add_setting( 'hello_movieengine_bottom_nav_label_color', array(
			'default'           => '#8d8d8d',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_bottom_nav_label_color', array(
			'label'       => esc_html__( 'Label Text Color (Default)', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_bottom_nav_section',
		) ) );

		/* Active / Hover Color */
		$wp_customize->add_setting( 'hello_movieengine_bottom_nav_active_color', array(
			'default'           => '#ff0055',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_bottom_nav_active_color', array(
			'label'       => esc_html__( 'Active & Accent Color', 'hello-movieengine' ),
			'description' => esc_html__( 'Color used for active item icon, label, and indicator dot.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_bottom_nav_section',
		) ) );
	}
}
