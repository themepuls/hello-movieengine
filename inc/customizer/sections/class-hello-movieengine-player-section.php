<?php
/**
 * Customizer: Video Player Settings.
 *
 * Controls player behavior, ambient glow lighting, autoplay, and server selector.
 * Directly synchronized with plugin's `movie_engine_player_settings`.
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Player_Section {

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

		$section_id = 'hello_movieengine_player_section';

		$wp_customize->add_section( $section_id, array(
			'title'           => esc_html__( 'Video Player Settings', 'hello-movieengine' ),
			'description'     => esc_html__( 'Configure playback behavior, ambient glow lighting, and server switching.', 'hello-movieengine' ),
			'priority'        => 30,
			'active_callback' => array( __CLASS__, 'is_active' ),
		) );

		// Section Tabs: [ GENERAL ] [ DESIGN ]
		$wp_customize->add_setting( 'hello_movieengine_player_tabs', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Section_Tabs_Control( $wp_customize, 'hello_movieengine_player_tabs', array(
			'section'  => $section_id,
			'priority' => 1,
		) ) );

		// Autoplay
		$wp_customize->add_setting( 'movie_engine_player_settings[autoplay]', array(
			'type'              => 'option',
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_player_settings[autoplay]', array(
			'label'       => esc_html__( 'Autoplay Video on Page Load', 'hello-movieengine' ),
			'description' => esc_html__( 'Note: Most modern browsers require muted audio for autoplay.', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 10,
		) ) );

		// Ambient Glow Lighting
		$wp_customize->add_setting( 'movie_engine_player_settings[ambient_lighting]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_player_settings[ambient_lighting]', array(
			'label'       => esc_html__( 'Ambient Glow Lighting', 'hello-movieengine' ),
			'description' => esc_html__( 'Creates an immersive colored glow effect behind the video player matching the video scene.', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 12,
		) ) );

		// Server Switcher
		$wp_customize->add_setting( 'movie_engine_player_settings[server_selector]', array(
			'type'              => 'option',
			'default'           => 1,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'movie_engine_player_settings[server_selector]', array(
			'label'       => esc_html__( 'Show Server Switcher Pills', 'hello-movieengine' ),
			'description' => esc_html__( 'Allows users to switch between multiple streaming sources/mirrors.', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 14,
		) ) );

		// Default Quality
		$wp_customize->add_setting( 'movie_engine_player_settings[default_quality]', array(
			'type'              => 'option',
			'default'           => 'auto',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_player_settings[default_quality]', array(
			'label'    => esc_html__( 'Default Video Quality', 'hello-movieengine' ),
			'section'  => $section_id,
			'type'     => 'select',
			'choices'  => array(
				'auto'  => esc_html__( 'Auto (Recommended)', 'hello-movieengine' ),
				'1080p' => '1080p Full HD',
				'720p'  => '720p HD',
				'480p'  => '480p SD',
			),
			'priority' => 16,
		) );

		// Preload
		$wp_customize->add_setting( 'movie_engine_player_settings[preload]', array(
			'type'              => 'option',
			'default'           => 'metadata',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_player_settings[preload]', array(
			'label'    => esc_html__( 'Video Preload Strategy', 'hello-movieengine' ),
			'section'  => $section_id,
			'type'     => 'select',
			'choices'  => array(
				'metadata' => esc_html__( 'Metadata Only (Fast)', 'hello-movieengine' ),
				'auto'     => esc_html__( 'Auto Preload Video', 'hello-movieengine' ),
				'none'     => esc_html__( 'None (Save Bandwidth)', 'hello-movieengine' ),
			),
			'priority' => 18,
		) );
	}
}
