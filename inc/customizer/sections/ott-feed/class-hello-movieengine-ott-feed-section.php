<?php
/**
 * Customizer: OTT Feed (Style Two only).
 *
 * Registered as a Panel with sub-sections for Global Left Navigation,
 * Front Page (Home Feed), Rows & Cards, and Hero Banner.
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Ott_Feed_Section {

	/**
	 * Show this panel and sections only when Style Two is selected.
	 *
	 * @return bool
	 */
	public static function is_style_two() {
		return hello_movieengine_customize_is_style_two();
	}

	/**
	 * Register OTT feed panel and sub-sections.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance.
	 */
	public static function register( $wp_customize ) {

		$panel_id = 'hello_movieengine_ott_panel';

		/* ==========================================================
		   Panel: OTT Feed (Style Two)
		   ========================================================== */
		$wp_customize->add_panel( $panel_id, array(
			'title'           => esc_html__( 'OTT Feed (Style Two)', 'hello-movieengine' ),
			'description'     => esc_html__( 'Style Two is a full-site OTT streaming look (home, Movies/Series pages, singles, account). The left rail is global. Row, card, and hero options apply across those pages.', 'hello-movieengine' ),
			'priority'        => 22,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		/* ==========================================================
		   Sub-Section 1: Global Left Navigation
		   ========================================================== */
		$nav_section_id = 'hello_movieengine_ott_nav_section';

		$wp_customize->add_section( $nav_section_id, array(
			'title'           => esc_html__( 'Global Left Navigation', 'hello-movieengine' ),
			'panel'           => $panel_id,
			'priority'        => 10,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		// Section Tabs: [ GENERAL ] [ DESIGN ]
		$wp_customize->add_setting( 'hello_movieengine_ott_nav_tabs', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Section_Tabs_Control( $wp_customize, 'hello_movieengine_ott_nav_tabs', array(
			'section'  => $nav_section_id,
			'priority' => 1,
		) ) );

		// Enable left navigation
		$wp_customize->add_setting( 'hello_movieengine_side_rail_enable', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_side_rail_enable', array(
			'label'           => esc_html__( 'Fixed left navigation (all pages)', 'hello-movieengine' ),
			'description'     => esc_html__( 'Uses Appearance → Menus (Primary Menu by default). If no menu is assigned, falls back to Mobile Bottom Navigation items.', 'hello-movieengine' ),
			'section'         => $nav_section_id,
			'priority'        => 10,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		// Show logo
		$wp_customize->add_setting( 'hello_movieengine_side_rail_show_logo', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_side_rail_show_logo', array(
			'label'           => esc_html__( 'Show logo in left rail', 'hello-movieengine' ),
			'section'         => $nav_section_id,
			'priority'        => 12,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		// Custom rail logo
		$wp_customize->add_setting( 'hello_movieengine_side_rail_custom_logo', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hello_movieengine_side_rail_custom_logo', array(
			'label'           => esc_html__( 'Custom rail logo / icon mark', 'hello-movieengine' ),
			'description'     => esc_html__( 'Upload a compact square/monogram logo or icon for the left rail. If empty, falls back to the default site logo.', 'hello-movieengine' ),
			'section'         => $nav_section_id,
			'priority'        => 14,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		// Exclude pages
		$wp_customize->add_setting( 'hello_movieengine_side_rail_exclude_pages', array(
			'default'           => '[]',
			'sanitize_callback' => 'hello_movieengine_sanitize_page_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Page_Select_Control( $wp_customize, 'hello_movieengine_side_rail_exclude_pages', array(
			'label'           => esc_html__( 'Show default top header on specific pages', 'hello-movieengine' ),
			'description'     => esc_html__( 'Selected pages will disable the left rail and display the classic top header/menu instead (e.g. Blog, Contact, etc.).', 'hello-movieengine' ),
			'section'         => $nav_section_id,
			'priority'        => 16,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		// Menu location
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
			'section'         => $nav_section_id,
			'type'            => 'select',
			'choices'         => $menu_choices,
			'priority'        => 18,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		// Always show labels
		$wp_customize->add_setting( 'hello_movieengine_side_rail_labels_pinned', array(
			'default'           => false,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_side_rail_labels_pinned', array(
			'label'           => esc_html__( 'Always show menu labels', 'hello-movieengine' ),
			'description'     => esc_html__( 'Off = icons only until you hover the left bar (recommended).', 'hello-movieengine' ),
			'section'         => $nav_section_id,
			'priority'        => 20,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		// Design Tab Controls
		$wp_customize->add_setting( 'hello_movieengine_side_rail_logo_height', array(
			'default'           => 34,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_side_rail_logo_height', array(
			'label'           => esc_html__( 'Rail logo height (px)', 'hello-movieengine' ),
			'section'         => $nav_section_id,
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 20, 'max' => 60, 'step' => 2 ),
			'priority'        => 30,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_side_rail_width', array(
			'default'           => 240,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_side_rail_width', array(
			'label'           => esc_html__( 'Rail width on large screens (px)', 'hello-movieengine' ),
			'section'         => $nav_section_id,
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 160, 'max' => 320, 'step' => 4 ),
			'priority'        => 32,
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
			'section'         => $nav_section_id,
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 56, 'max' => 120, 'step' => 2 ),
			'priority'        => 34,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_side_rail_sh1_width', array(
			'default'           => 200,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_side_rail_sh1_width', array(
			'label'           => esc_html__( 'Left scrim width — layer 1 (px)', 'hello-movieengine' ),
			'section'         => $nav_section_id,
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 120, 'max' => 400, 'step' => 10 ),
			'priority'        => 36,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_side_rail_sh2_width', array(
			'default'           => 450,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_side_rail_sh2_width', array(
			'label'           => esc_html__( 'Left scrim width — layer 2 on hover (px)', 'hello-movieengine' ),
			'section'         => $nav_section_id,
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 200, 'max' => 600, 'step' => 10 ),
			'priority'        => 38,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		/* ==========================================================
		   Sub-Section 2: Rows & Cards (Streaming Layout)
		   ========================================================== */
		$rows_section_id = 'hello_movieengine_ott_rows_section';

		$wp_customize->add_section( $rows_section_id, array(
			'title'           => esc_html__( 'Rows & Cards', 'hello-movieengine' ),
			'panel'           => $panel_id,
			'priority'        => 20,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		// Section Tabs: [ GENERAL ] [ DESIGN ]
		$wp_customize->add_setting( 'hello_movieengine_ott_rows_tabs', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Section_Tabs_Control( $wp_customize, 'hello_movieengine_ott_rows_tabs', array(
			'section'  => $rows_section_id,
			'priority' => 1,
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_feed_top10_style', array(
			'default'           => false,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_feed_top10_style', array(
			'label'           => esc_html__( 'Top 10 row styling', 'hello-movieengine' ),
			'description'     => esc_html__( 'Adds large rank numbers on rows that use the top-10 CSS class (add custom_class on library shortcodes).', 'hello-movieengine' ),
			'section'         => $rows_section_id,
			'priority'        => 10,
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
			'section'         => $rows_section_id,
			'priority'        => 12,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );

		// Design Tab Controls
		$wp_customize->add_setting( 'hello_movieengine_feed_row_gap', array(
			'default'           => 28,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_feed_row_gap', array(
			'label'           => esc_html__( 'Space between rows (px)', 'hello-movieengine' ),
			'section'         => $rows_section_id,
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 0, 'max' => 120, 'step' => 2 ),
			'priority'        => 20,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_feed_row_padding_x', array(
			'default'           => 20,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_feed_row_padding_x', array(
			'label'           => esc_html__( 'Row side padding (px)', 'hello-movieengine' ),
			'section'         => $rows_section_id,
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 0, 'max' => 120, 'step' => 2 ),
			'priority'        => 22,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_feed_section_title_size', array(
			'default'           => 1.125,
			'sanitize_callback' => 'hello_movieengine_sanitize_feed_title_size',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_feed_section_title_size', array(
			'label'           => esc_html__( 'Section title size (rem)', 'hello-movieengine' ),
			'section'         => $rows_section_id,
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 0.75, 'max' => 2.5, 'step' => 0.0625 ),
			'priority'        => 24,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_feed_card_min_width', array(
			'default'           => 140,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_feed_card_min_width', array(
			'label'           => esc_html__( 'Minimum card width in sliders (px)', 'hello-movieengine' ),
			'section'         => $rows_section_id,
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 90, 'max' => 260, 'step' => 5 ),
			'priority'        => 26,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		/* ==========================================================
		   Sub-Section 4: Hero Banner
		   ========================================================== */
		$hero_section_id = 'hello_movieengine_ott_hero_section';

		$wp_customize->add_section( $hero_section_id, array(
			'title'           => esc_html__( 'Hero Banner', 'hello-movieengine' ),
			'panel'           => $panel_id,
			'priority'        => 40,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_feed_hero_min_height', array(
			'default'           => 72,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_feed_hero_min_height', array(
			'label'           => esc_html__( 'Hero minimum height (vh)', 'hello-movieengine' ),
			'section'         => $hero_section_id,
			'type'            => 'number',
			'input_attrs'     => array( 'min' => 40, 'max' => 100, 'step' => 1 ),
			'priority'        => 10,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_feed_hero_overlay', array(
			'default'           => 'rgba(0,0,0,0.45)',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_feed_hero_overlay', array(
			'label'           => esc_html__( 'Hero overlay color', 'hello-movieengine' ),
			'section'         => $hero_section_id,
			'priority'        => 12,
			'active_callback' => array( __CLASS__, 'is_style_two' ),
		) ) );
	}
}
