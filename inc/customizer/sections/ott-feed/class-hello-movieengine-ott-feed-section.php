<?php
/**
 * Customizer: OTT Feed (Style Two only).
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Ott_Feed_Section {

	/**
	 * Show this section only when Style Two is selected.
	 *
	 * @return bool
	 */
	public static function is_style_two() {
		return hello_movieengine_customize_is_style_two();
	}

	/**
	 * Register OTT feed controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance.
	 */
	public static function register( $wp_customize ) {

		$wp_customize->add_section( 'hello_movieengine_ott_feed_section', array(
			'title'           => esc_html__( 'OTT Feed (Style Two)', 'hello-movieengine' ),
			'description'     => esc_html__( 'Style Two is a full-site OTT look (home, Movies/Series pages, singles, account). The left rail is global. Row, card, and hero options apply across those pages, not only the homepage.', 'hello-movieengine' ),
			'panel'           => 'hello_movieengine_panel',
			'priority'        => 2,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_heading_ott_nav', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_ott_nav', array(
			'label'           => esc_html__( 'Global left navigation', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_side_rail_enable', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_side_rail_enable', array(
			'label'           => esc_html__( 'Fixed left navigation (all pages)', 'hello-movieengine' ),
			'description'     => esc_html__( 'Uses Appearance → Menus (Primary Menu by default). If no menu is assigned, falls back to Mobile Bottom Navigation items.', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_side_rail_show_logo', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_side_rail_show_logo', array(
			'label'           => esc_html__( 'Show logo in left rail', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_side_rail_custom_logo', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hello_movieengine_side_rail_custom_logo', array(
			'label'           => esc_html__( 'Custom rail logo / icon mark', 'hello-movieengine' ),
			'description'     => esc_html__( 'Upload a compact square/monogram logo or icon for the left rail. If empty, falls back to the default site logo.', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_side_rail_logo_height', array(
			'default'           => 34,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_side_rail_logo_height', array(
			'label'           => esc_html__( 'Rail logo height (px)', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 20, 'max' => 60, 'step' => 2 ),
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_side_rail_exclude_pages', array(
			'default'           => '[]',
			'sanitize_callback' => 'hello_movieengine_sanitize_page_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Page_Select_Control( $wp_customize, 'hello_movieengine_side_rail_exclude_pages', array(
			'label'           => esc_html__( 'Show default top header on specific pages', 'hello-movieengine' ),
			'description'     => esc_html__( 'Selected pages will disable the left rail and display the classic top header/menu instead (e.g. Blog, Contact, etc.).', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$menu_choices = array();
		foreach ( get_registered_nav_menus() as $slug => $label ) {
			$menu_choices[ $slug ] = $label;
		}

		$wp_customize->add_setting( 'hello_movieengine_side_rail_menu_location', array(
			'default'           => 'primary',
			'sanitize_callback' => function ( $value ) use ( $menu_choices ) {
				return isset( $menu_choices[ $value ] ) ? $value : 'primary';
			},
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_side_rail_menu_location', array(
			'label'           => esc_html__( 'Menu location for left rail', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'type'            => 'select',
			'choices'         => $menu_choices,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_side_rail_width', array(
			'default'           => 240,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_side_rail_width', array(
			'label'           => esc_html__( 'Rail width on large screens (px)', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 160, 'max' => 320, 'step' => 4 ),
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_side_rail_compact_width', array(
			'default'           => 80,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_side_rail_compact_width', array(
			'label'           => esc_html__( 'Icon column width (px)', 'hello-movieengine' ),
			'description'     => esc_html__( 'How far content is inset. Labels expand over the hero on hover.', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 56, 'max' => 120, 'step' => 2 ),
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_side_rail_labels_pinned', array(
			'default'           => false,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_side_rail_labels_pinned', array(
			'label'           => esc_html__( 'Always show menu labels', 'hello-movieengine' ),
			'description'     => esc_html__( 'Off = icons only until you hover the left bar (recommended).', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_side_rail_sh1_width', array(
			'default'           => 200,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_side_rail_sh1_width', array(
			'label'           => esc_html__( 'Left scrim width — layer 1 (px)', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 120, 'max' => 400, 'step' => 10 ),
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_side_rail_sh2_width', array(
			'default'           => 450,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_side_rail_sh2_width', array(
			'label'           => esc_html__( 'Left scrim width — layer 2 on hover (px)', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 200, 'max' => 600, 'step' => 10 ),
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_heading_ott_home', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_ott_home', array(
			'label'           => esc_html__( 'Front page (home feed)', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_feed_hide_page_title', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_feed_hide_page_title', array(
			'label'           => esc_html__( 'Hide page title on front page', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_feed_hide_footer', array(
			'default'           => false,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_feed_hide_footer', array(
			'label'           => esc_html__( 'Hide footer on front page', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_feed_main_padding_zero', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_feed_main_padding_zero', array(
			'label'           => esc_html__( 'Remove main content padding', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_heading_ott_rows', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_ott_rows', array(
			'label'           => esc_html__( 'Rows & cards', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_feed_row_gap', array(
			'default'           => 28,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_feed_row_gap', array(
			'label'           => esc_html__( 'Space between rows (px)', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'type'            => 'number',
			'input_attrs'       => array( 'min' => 0, 'max' => 120, 'step' => 2 ),
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_feed_row_padding_x', array(
			'default'           => 20,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_feed_row_padding_x', array(
			'label'           => esc_html__( 'Row side padding (px)', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'type'            => 'number',
			'input_attrs'       => array( 'min' => 0, 'max' => 120, 'step' => 2 ),
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_feed_section_title_size', array(
			'default'           => 1.125,
			'sanitize_callback' => 'hello_movieengine_sanitize_feed_title_size',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_feed_section_title_size', array(
			'label'           => esc_html__( 'Section title size (rem)', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'type'            => 'number',
			'input_attrs'       => array( 'min' => 0.75, 'max' => 2.5, 'step' => 0.0625 ),
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_feed_card_min_width', array(
			'default'           => 140,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_feed_card_min_width', array(
			'label'           => esc_html__( 'Minimum card width in sliders (px)', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'type'            => 'number',
			'input_attrs'       => array( 'min' => 90, 'max' => 260, 'step' => 5 ),
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_feed_top10_style', array(
			'default'           => false,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_feed_top10_style', array(
			'label'           => esc_html__( 'Top 10 row styling', 'hello-movieengine' ),
			'description'     => esc_html__( 'Adds large rank numbers on rows that use the top-10 CSS class (add custom_class on library shortcodes).', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_feed_studio_chips', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_feed_studio_chips', array(
			'label'           => esc_html__( 'Studio chip bar styling', 'hello-movieengine' ),
			'description'     => esc_html__( 'Styles [movie_engine_studios] / taxonomy chip rows wherever they appear.', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_heading_ott_hero', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_ott_hero', array(
			'label'           => esc_html__( 'Hero', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_feed_hero_min_height', array(
			'default'           => 72,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_feed_hero_min_height', array(
			'label'           => esc_html__( 'Hero minimum height (vh)', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'type'            => 'number',
			'input_attrs'       => array( 'min' => 40, 'max' => 100, 'step' => 1 ),
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_feed_hero_overlay', array(
			'default'           => 'rgba(0,0,0,0.45)',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_feed_hero_overlay', array(
			'label'           => esc_html__( 'Hero overlay color', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_feed_container_width', array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_feed_container_width', array(
			'label'           => esc_html__( 'Content max width (px)', 'hello-movieengine' ),
			'description'     => esc_html__( '0 = full width. Set a value to cap inner content width on Style Two pages.', 'hello-movieengine' ),
			'section'         => 'hello_movieengine_ott_feed_section',
			'type'            => 'number',
			'input_attrs'       => array( 'min' => 0, 'max' => 1920, 'step' => 10 ),
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );
	}
}