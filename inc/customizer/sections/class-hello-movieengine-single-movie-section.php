<?php
/**
 * Customizer: Single Movie (Watch Experience).
 *
 * Controls layout, hero action buttons, and feed rows on single movie pages.
 * Directly synchronized with plugin's `movie_engine_design_settings`.
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Single_Movie_Section {

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

		$section_id = 'hello_movieengine_single_movie_section';

		$wp_customize->add_section( $section_id, array(
			'title'           => esc_html__( 'Single Movie (Watch Page)', 'hello-movieengine' ),
			'description'     => esc_html__( 'Control watch page layout, action buttons, and recommendation rows for single movies.', 'hello-movieengine' ),
			'priority'        => 24,
			'active_callback' => array( __CLASS__, 'is_active' ),
		) );

		// Section Tabs: [ GENERAL ] [ DESIGN ]
		$wp_customize->add_setting( 'hello_movieengine_single_movie_tabs', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Section_Tabs_Control( $wp_customize, 'hello_movieengine_single_movie_tabs', array(
			'section'  => $section_id,
			'priority' => 1,
		) ) );

		/* --------------------------------------------------------
		   GENERAL: Layout & Hero Buttons
		   -------------------------------------------------------- */

		// Layout Mode
		$wp_customize->add_setting( 'movie_engine_design_settings[movie_single_layout]', array(
			'type'              => 'option',
			'default'           => 'fullwidth',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Button_Group_Control( $wp_customize, 'movie_engine_design_settings[movie_single_layout]', array(
			'label'    => esc_html__( 'Single Movie Layout', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 10,
			'choices'  => array(
				'fullwidth' => esc_html__( 'Fullwidth', 'hello-movieengine' ),
				'inline'    => esc_html__( 'Inline Cinema', 'hello-movieengine' ),
			),
		) ) );

		// Hero Action Buttons
		$wp_customize->add_setting( 'movie_engine_design_settings[show_hero_like]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[show_hero_like]', array(
			'label'    => esc_html__( 'Like Button', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 12,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[show_hero_watchlist]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[show_hero_watchlist]', array(
			'label'    => esc_html__( 'Watchlist Button', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 14,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[show_hero_playlist]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[show_hero_playlist]', array(
			'label'    => esc_html__( 'Playlist Button', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 16,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[show_hero_share]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[show_hero_share]', array(
			'label'    => esc_html__( 'Share Button', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 18,
		) ) );

		// Movie Title & Comments
		$wp_customize->add_setting( 'movie_engine_design_settings[movie_show_title]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[movie_show_title]', array(
			'label'    => esc_html__( 'Show Title in Content', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 20,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[movie_show_comments]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[movie_show_comments]', array(
			'label'    => esc_html__( 'Enable Comments / Reviews', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 22,
		) ) );

		/* --------------------------------------------------------
		   GENERAL: Watch Page Rows (Cast, Trending, New, Upcoming, Related)
		   -------------------------------------------------------- */

		// Cast Row
		$wp_customize->add_setting( 'movie_engine_design_settings[movie_show_cast]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[movie_show_cast]', array(
			'label'    => esc_html__( 'Show Cast Row', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 30,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[movie_cast_title]', array(
			'type'              => 'option',
			'default'           => esc_html__( 'Cast', 'cine-movie-engine' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[movie_cast_title]', array(
			'label'    => esc_html__( 'Cast Row Title', 'hello-movieengine' ),
			'section'  => $section_id,
			'type'     => 'text',
			'priority' => 32,
		) );

		// Trending Row
		$wp_customize->add_setting( 'movie_engine_design_settings[movie_show_trending]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[movie_show_trending]', array(
			'label'    => esc_html__( 'Show Trending Row', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 40,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[movie_trending_title]', array(
			'type'              => 'option',
			'default'           => esc_html__( 'Trending Now', 'cine-movie-engine' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[movie_trending_title]', array(
			'label'    => esc_html__( 'Trending Row Title', 'hello-movieengine' ),
			'section'  => $section_id,
			'type'     => 'text',
			'priority' => 42,
		) );

		// New Releases Row
		$wp_customize->add_setting( 'movie_engine_design_settings[movie_show_new_releases]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[movie_show_new_releases]', array(
			'label'    => esc_html__( 'Show New Releases Row', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 50,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[movie_new_releases_title]', array(
			'type'              => 'option',
			'default'           => esc_html__( 'New Releases', 'cine-movie-engine' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[movie_new_releases_title]', array(
			'label'    => esc_html__( 'New Releases Title', 'hello-movieengine' ),
			'section'  => $section_id,
			'type'     => 'text',
			'priority' => 52,
		) );

		// Upcoming Row
		$wp_customize->add_setting( 'movie_engine_design_settings[movie_show_upcoming]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[movie_show_upcoming]', array(
			'label'    => esc_html__( 'Show Upcoming Movies Row', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 60,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[movie_upcoming_title]', array(
			'type'              => 'option',
			'default'           => esc_html__( 'Upcoming Movies', 'cine-movie-engine' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[movie_upcoming_title]', array(
			'label'    => esc_html__( 'Upcoming Movies Title', 'hello-movieengine' ),
			'section'  => $section_id,
			'type'     => 'text',
			'priority' => 62,
		) );

		// Related Movies Row
		$wp_customize->add_setting( 'movie_engine_design_settings[movie_show_related]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_design_settings[movie_show_related]', array(
			'label'    => esc_html__( 'Show Related Movies Row', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 70,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[movie_related_title]', array(
			'type'              => 'option',
			'default'           => esc_html__( 'Related Movies', 'cine-movie-engine' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[movie_related_title]', array(
			'label'    => esc_html__( 'Related Movies Title', 'hello-movieengine' ),
			'section'  => $section_id,
			'type'     => 'text',
			'priority' => 72,
		) );

		$wp_customize->add_setting( 'movie_engine_design_settings[movie_related_logic]', array(
			'type'              => 'option',
			'default'           => 'genre',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[movie_related_logic]', array(
			'label'    => esc_html__( 'Related Movies Logic', 'hello-movieengine' ),
			'section'  => $section_id,
			'type'     => 'select',
			'choices'  => array(
				'genre'  => esc_html__( 'By Same Genre', 'cine-movie-engine' ),
				'tag'    => esc_html__( 'By Same Tag', 'cine-movie-engine' ),
				'random' => esc_html__( 'Random Movies', 'cine-movie-engine' ),
			),
			'priority' => 74,
		) );

		/* --------------------------------------------------------
		   DESIGN TAB: Dimensions, Counts & Overlay
		   -------------------------------------------------------- */

		$wp_customize->add_setting( 'movie_engine_design_settings[movie_watch_hero_height_vh]', array(
			'type'              => 'option',
			'default'           => 0,
			'sanitize_callback' => 'hello_movieengine_sanitize_watch_hero_height_vh',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[movie_watch_hero_height_vh]', array(
			'label'       => esc_html__( 'Hero section height (vh)', 'hello-movieengine' ),
			'description' => esc_html__( 'Viewport height for the watch page hero (40–100). Leave 0 for defaults: Fullwidth 100vh, Inline Cinema 74vh.', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
			'priority'    => 98,
		) );

		// Hero Overlay Opacity
		$wp_customize->add_setting( 'movie_engine_design_settings[movie_hero_overlay_opacity]', array(
			'type'              => 'option',
			'default'           => 0.6,
			'sanitize_callback' => 'floatval',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[movie_hero_overlay_opacity]', array(
			'label'       => esc_html__( 'Hero Backdrop Overlay Opacity', 'hello-movieengine' ),
			'description' => esc_html__( '0 = Transparent, 1 = Black', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ),
			'priority'    => 100,
		) );

		// Row Counts
		$wp_customize->add_setting( 'movie_engine_design_settings[movie_cast_count]', array(
			'type'              => 'option',
			'default'           => 10,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[movie_cast_count]', array(
			'label'       => esc_html__( 'Cast Items Count', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 4, 'max' => 30, 'step' => 1 ),
			'priority'    => 102,
		) );

		$wp_customize->add_setting( 'movie_engine_design_settings[movie_trending_count]', array(
			'type'              => 'option',
			'default'           => 6,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[movie_trending_count]', array(
			'label'       => esc_html__( 'Trending Items Count', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 4, 'max' => 24, 'step' => 1 ),
			'priority'    => 104,
		) );

		$wp_customize->add_setting( 'movie_engine_design_settings[movie_new_releases_count]', array(
			'type'              => 'option',
			'default'           => 6,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[movie_new_releases_count]', array(
			'label'       => esc_html__( 'New Releases Items Count', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 4, 'max' => 24, 'step' => 1 ),
			'priority'    => 106,
		) );

		$wp_customize->add_setting( 'movie_engine_design_settings[movie_upcoming_count]', array(
			'type'              => 'option',
			'default'           => 6,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[movie_upcoming_count]', array(
			'label'       => esc_html__( 'Upcoming Items Count', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 4, 'max' => 24, 'step' => 1 ),
			'priority'    => 108,
		) );

		$wp_customize->add_setting( 'movie_engine_design_settings[movie_related_count]', array(
			'type'              => 'option',
			'default'           => 6,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[movie_related_count]', array(
			'label'       => esc_html__( 'Related Movies Count', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 4, 'max' => 24, 'step' => 1 ),
			'priority'    => 110,
		) );
	}
}
