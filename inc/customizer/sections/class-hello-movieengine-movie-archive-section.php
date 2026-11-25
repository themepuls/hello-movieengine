<?php
/**
 * Customizer: Movie & Series Archive / Browse Grid.
 *
 * Controls responsive grid columns, poster badges, interactive filter bars,
 * and taxonomy/studio branding.
 * Directly synchronized with plugin's `movie_engine_design_settings`.
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Movie_Archive_Section {

	/**
	 * Active callback: plugin must be active.
	 *
	 * @return bool
	 */
	public static function is_active() {
		return function_exists( 'hello_movieengine_is_movie_engine_active' ) && hello_movieengine_is_movie_engine_active();
	}

	/**
	 * Register controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance.
	 */
	public static function register( $wp_customize ) {

		$section_id = 'hello_movieengine_movie_archive_section';

		$wp_customize->add_section( $section_id, array(
			'title'           => esc_html__( 'Movie & Series Archive', 'hello-movieengine' ),
			'description'     => esc_html__( 'Configure grid columns, poster badges, search/filter bars, and studio branding.', 'hello-movieengine' ),
			'priority'        => 28,
			'active_callback' => array( __CLASS__, 'is_active' ),
		) );

		// Section Tabs: [ GENERAL ] [ DESIGN ]
		$wp_customize->add_setting( 'hello_movieengine_movie_archive_tabs', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Section_Tabs_Control( $wp_customize, 'hello_movieengine_movie_archive_tabs', array(
			'section'  => $section_id,
			'priority' => 1,
		) ) );

		/* --------------------------------------------------------
		   GENERAL: Poster Badges & Filter Bar
		   -------------------------------------------------------- */

		// Poster Card Badges
		$wp_customize->add_setting( 'movie_engine_design_settings[archive_show_quality]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[archive_show_quality]', array(
			'label'       => esc_html__( 'Show Quality Badge (HD / 4K)', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 10,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[archive_show_rating]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[archive_show_rating]', array(
			'label'       => esc_html__( 'Show IMDb Star Rating', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 12,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[archive_show_year]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[archive_show_year]', array(
			'label'       => esc_html__( 'Show Release Year', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 14,
		) ) );

		// Filter Bar Controls
		$wp_customize->add_setting( 'movie_engine_design_settings[archive_show_filter_bar]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[archive_show_filter_bar]', array(
			'label'       => esc_html__( 'Show Archive Filter Bar', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 20,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[archive_filter_show_search]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[archive_filter_show_search]', array(
			'label'       => esc_html__( 'Filter Bar: Search Input', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 22,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[archive_filter_show_genres]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[archive_filter_show_genres]', array(
			'label'       => esc_html__( 'Filter Bar: Genre Dropdown', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 24,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[archive_filter_show_years]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[archive_filter_show_years]', array(
			'label'       => esc_html__( 'Filter Bar: Year Dropdown', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 26,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[archive_filter_show_sort]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[archive_filter_show_sort]', array(
			'label'       => esc_html__( 'Filter Bar: Sorting Dropdown', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 28,
		) ) );

		// Studio / Taxonomy Branding
		$wp_customize->add_setting( 'movie_engine_design_settings[taxonomy_show_hero]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[taxonomy_show_hero]', array(
			'label'       => esc_html__( 'Show Studio/Taxonomy Hero Banner', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 30,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[taxonomy_show_studio_logo]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[taxonomy_show_studio_logo]', array(
			'label'       => esc_html__( 'Show Studio Brand Logo', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 32,
		) ) );

		/* --------------------------------------------------------
		   DESIGN TAB: Responsive Columns
		   -------------------------------------------------------- */

		$wp_customize->add_setting( 'movie_engine_design_settings[archive_columns_desktop]', array(
			'type'              => 'option',
			'default'           => 8,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[archive_columns_desktop]', array(
			'label'       => esc_html__( 'Grid Columns: Desktop (>1200px)', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 2, 'max' => 10, 'step' => 1 ),
			'priority'    => 100,
		) );

		$wp_customize->add_setting( 'movie_engine_design_settings[archive_columns_laptop]', array(
			'type'              => 'option',
			'default'           => 5,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[archive_columns_laptop]', array(
			'label'       => esc_html__( 'Grid Columns: Laptop (992px - 1199px)', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 2, 'max' => 8, 'step' => 1 ),
			'priority'    => 102,
		) );

		$wp_customize->add_setting( 'movie_engine_design_settings[archive_columns_tablet]', array(
			'type'              => 'option',
			'default'           => 3,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[archive_columns_tablet]', array(
			'label'       => esc_html__( 'Grid Columns: Tablet (768px - 991px)', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 1, 'max' => 6, 'step' => 1 ),
			'priority'    => 104,
		) );

		$wp_customize->add_setting( 'movie_engine_design_settings[archive_columns_mobile]', array(
			'type'              => 'option',
			'default'           => 2,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[archive_columns_mobile]', array(
			'label'       => esc_html__( 'Grid Columns: Mobile (<768px)', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 1, 'max' => 4, 'step' => 1 ),
			'priority'    => 106,
		) );
	}
}
