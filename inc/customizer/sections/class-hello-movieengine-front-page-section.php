<?php
/**
 * Front Page (Homepage Builder) Customizer Section.
 *
 * Provides a dynamic drag-and-drop section builder and content mode
 * selector for the homepage feed, working across both Style One and Style Two.
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Front_Page_Section {

	/**
	 * Register the section, settings, and controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance.
	 */
	public static function register( $wp_customize ) {
		$section_id = 'hello_movieengine_front_page_section';

		// Standalone Section under Movie & OTT Streaming (Works for BOTH Style One & Style Two)
		$wp_customize->add_section( $section_id, array(
			'title'       => esc_html__( 'Front Page (Homepage Builder)', 'hello-movieengine' ),
			'description' => esc_html__( 'Configure your homepage layout, drag-and-drop feed rows, and content source.', 'hello-movieengine' ),
			'priority'    => 21,
		) );

		/* ==========================================================
		   Section Tabs: [ GENERAL ] [ DESIGN ]
		   ========================================================== */
		$wp_customize->add_setting( 'hello_movieengine_front_page_tabs', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Section_Tabs_Control( $wp_customize, 'hello_movieengine_front_page_tabs', array(
			'section'  => $section_id,
			'priority' => 1,
		) ) );

		/* ==========================================================
		   GENERAL TAB CONTROLS
		   ========================================================== */

		// Homepage Content Source Mode
		$wp_customize->add_setting( 'hello_movieengine_home_mode', array(
			'default'           => 'editor',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Button_Group_Control( $wp_customize, 'hello_movieengine_home_mode', array(
			'label'       => esc_html__( 'Homepage Content Source', 'hello-movieengine' ),
			'description' => esc_html__( 'Choose how your homepage feed is built. "Page Content" preserves your Elementor / Block Editor page.', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 5,
			'choices'     => array(
				'editor'  => esc_html__( 'Page Content', 'hello-movieengine' ),
				'builder' => esc_html__( 'OTT Builder', 'hello-movieengine' ),
				'hybrid'  => esc_html__( 'Hybrid', 'hello-movieengine' ),
			),
		) ) );

		// Drag & Drop Section Builder Control
		$wp_customize->add_setting( 'hello_movieengine_home_sections', array(
			'default'           => wp_json_encode( Hello_Movie_Engine_Customize_Section_Builder_Control::get_default_sections() ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Section_Builder_Control( $wp_customize, 'hello_movieengine_home_sections', array(
			'label'       => esc_html__( 'Homepage Section Manager', 'hello-movieengine' ),
			'description' => esc_html__( 'Drag and drop rows to reorder. Toggle visibility (👁️) or expand (▾) to configure parameters.', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 8,
		) ) );

		/* ==========================================================
		   DESIGN TAB CONTROLS
		   ========================================================== */

		$wp_customize->add_setting( 'hello_movieengine_feed_hide_page_title', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_feed_hide_page_title', array(
			'label'       => esc_html__( 'Hide page title on front page', 'hello-movieengine' ),
			'description' => esc_html__( 'Hides the default page heading bar on the homepage.', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 20,
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_feed_hide_footer', array(
			'default'           => false,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_feed_hide_footer', array(
			'label'       => esc_html__( 'Hide footer on front page', 'hello-movieengine' ),
			'description' => esc_html__( 'Clean infinite streaming look without footer widgets.', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 22,
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_feed_main_padding_zero', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_feed_main_padding_zero', array(
			'label'       => esc_html__( 'Remove main content padding', 'hello-movieengine' ),
			'description' => esc_html__( 'Allows hero banners and full-width rows to touch screen edges.', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 24,
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_feed_container_width', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_feed_container_width', array(
			'label'       => esc_html__( 'Content max width (px)', 'hello-movieengine' ),
			'description' => esc_html__( '0 = full width (recommended). Set a value (e.g. 1400) to cap inner feed row width.', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 0, 'max' => 1920, 'step' => 10 ),
			'priority'    => 26,
		) );
	}
}
