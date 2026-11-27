<?php
/**
 * Customizer: TV Series & Episodes.
 *
 * Controls layout, seasons/episodes architecture, and feed rows for TV Series and Episodes.
 * Directly synchronized with plugin's `movie_engine_design_settings`.
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Tv_Series_Section {

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

		$section_id = 'hello_movieengine_tv_series_section';

		$wp_customize->add_section( $section_id, array(
			'title'           => esc_html__( 'TV Series & Episodes', 'hello-movieengine' ),
			'description'     => esc_html__( 'Manage season tabs, episode grids, and recommendation rows for web series and TV shows.', 'hello-movieengine' ),
			'priority'        => 26,
			'active_callback' => array( __CLASS__, 'is_active' ),
		) );

		// Section Tabs: [ GENERAL ] [ DESIGN ]
		$wp_customize->add_setting( 'hello_movieengine_tv_series_tabs', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Section_Tabs_Control( $wp_customize, 'hello_movieengine_tv_series_tabs', array(
			'section'  => $section_id,
			'priority' => 1,
		) ) );

		/* --------------------------------------------------------
		   GENERAL: Seasons & Episodes Architecture
		   -------------------------------------------------------- */

		// Show Seasons Section
		$wp_customize->add_setting( 'movie_engine_design_settings[series_show_seasons]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[series_show_seasons]', array(
			'label'    => esc_html__( 'Show Seasons & Episodes', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 10,
		) ) );

		// Seasons Layout (List vs Grid)
		$wp_customize->add_setting( 'movie_engine_design_settings[series_seasons_layout]', array(
			'type'              => 'option',
			'default'           => 'list',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Button_Group_Control( $wp_customize, 'movie_engine_design_settings[series_seasons_layout]', array(
			'label'    => esc_html__( 'Seasons Layout', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 12,
			'choices'  => array(
				'list' => esc_html__( 'List View', 'hello-movieengine' ),
				'grid' => esc_html__( 'Grid View', 'hello-movieengine' ),
			),
		) ) );

		// Episodes Order
		$wp_customize->add_setting( 'movie_engine_design_settings[series_episodes_order]', array(
			'type'              => 'option',
			'default'           => 'desc',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Button_Group_Control( $wp_customize, 'movie_engine_design_settings[series_episodes_order]', array(
			'label'    => esc_html__( 'Episodes Sorting Order', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 14,
			'choices'  => array(
				'desc' => esc_html__( 'Latest First (DESC)', 'hello-movieengine' ),
				'asc'  => esc_html__( 'Episode 1 First (ASC)', 'hello-movieengine' ),
			),
		) ) );

		// Hero Action Buttons
		$wp_customize->add_setting( 'movie_engine_design_settings[series_show_hero_like]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[series_show_hero_like]', array(
			'label'    => esc_html__( 'Series Like Button', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 20,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[series_show_hero_watchlist]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[series_show_hero_watchlist]', array(
			'label'    => esc_html__( 'Series Watchlist Button', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 22,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[series_show_hero_share]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[series_show_hero_share]', array(
			'label'    => esc_html__( 'Series Share Button', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 24,
		) ) );

		// Single Episode Layout
		$wp_customize->add_setting( 'movie_engine_design_settings[episode_single_layout]', array(
			'type'              => 'option',
			'default'           => 'sidebar',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Button_Group_Control( $wp_customize, 'movie_engine_design_settings[episode_single_layout]', array(
			'label'    => esc_html__( 'Single Episode Page Layout', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 30,
			'choices'  => array(
				'sidebar'   => esc_html__( 'Player with Sidebar', 'hello-movieengine' ),
				'fullwidth' => esc_html__( 'Fullwidth Cinema', 'hello-movieengine' ),
			),
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[episode_show_views]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[episode_show_views]', array(
			'label'    => esc_html__( 'Show Episode Views Count', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 32,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[episode_show_comments]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[episode_show_comments]', array(
			'label'    => esc_html__( 'Show Episode Comments', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 34,
		) ) );

		// Series Rows
		$wp_customize->add_setting( 'movie_engine_design_settings[series_show_cast]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[series_show_cast]', array(
			'label'    => esc_html__( 'Show Series Cast Row', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 40,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[series_show_trending]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[series_show_trending]', array(
			'label'    => esc_html__( 'Show Trending Series Row', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 42,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[series_show_new_releases]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[series_show_new_releases]', array(
			'label'    => esc_html__( 'Show New Series Row', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 44,
		) ) );

		/* --------------------------------------------------------
		   DESIGN TAB: Dimensions, Columns & Opacity
		   -------------------------------------------------------- */

		// Series Hero Overlay
		$wp_customize->add_setting( 'movie_engine_design_settings[series_watch_hero_height_vh]', array(
			'type'              => 'option',
			'default'           => 0,
			'sanitize_callback' => 'hello_movieengine_sanitize_watch_hero_height_vh',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[series_watch_hero_height_vh]', array(
			'label'       => esc_html__( 'Series hero section height (vh)', 'hello-movieengine' ),
			'description' => esc_html__( 'Viewport height for the TV series watch page hero (40–100). Leave 0 to use the theme default (100vh).', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
			'priority'    => 98,
		) );

		$wp_customize->add_setting( 'movie_engine_design_settings[series_hero_overlay_opacity]', array(
			'type'              => 'option',
			'default'           => 70,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[series_hero_overlay_opacity]', array(
			'label'       => esc_html__( 'Series Hero Backdrop Darkness (%)', 'hello-movieengine' ),
			'description' => esc_html__( '0% = Transparent, 100% = Pure Black', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
			'priority'    => 100,
		) );

		// Episode Columns
		$wp_customize->add_setting( 'movie_engine_design_settings[series_episodes_columns]', array(
			'type'              => 'option',
			'default'           => 4,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[series_episodes_columns]', array(
			'label'       => esc_html__( 'Episodes Grid Columns (Desktop)', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 2, 'max' => 6, 'step' => 1 ),
			'priority'    => 102,
		) );

		$wp_customize->add_setting( 'movie_engine_design_settings[series_episodes_count]', array(
			'type'              => 'option',
			'default'           => 20,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[series_episodes_count]', array(
			'label'       => esc_html__( 'Episodes Count Per Season', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 5, 'max' => 100, 'step' => 5 ),
			'priority'    => 104,
		) );
	}
}
