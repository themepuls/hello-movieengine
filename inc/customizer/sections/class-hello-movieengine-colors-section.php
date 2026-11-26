<?php
/**
 * Customizer section: Colors.
 *
 * Theme brand color (without plugin) + Movie Engine palette synced to plugin design settings.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Colors_Section {

	/**
	 * @return bool
	 */
	public static function is_plugin_active() {
		return function_exists( 'hello_movieengine_is_movie_engine_active' ) && hello_movieengine_is_movie_engine_active();
	}

	/**
	 * @return bool
	 */
	public static function is_theme_brand_only() {
		return ! self::is_plugin_active();
	}

	/**
	 * Register color controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance.
	 */
	public static function register( $wp_customize ) {

		$section_id = 'hello_movieengine_colors_section';

		$wp_customize->add_section( $section_id, array(
			'title'       => esc_html__( 'Colors', 'hello-movieengine' ),
			'description' => self::is_plugin_active()
				? esc_html__( 'Palette and layout tokens sync with Movie Engine Design settings (same as the plugin admin).', 'hello-movieengine' )
				: esc_html__( 'Theme accent color for headers and UI when Movie Engine is not active.', 'hello-movieengine' ),
			'priority'    => 10,
		) );

		/* --- Heading: Theme brand (no plugin) --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_brand', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_brand', array(
			'label'           => esc_html__( 'Theme Brand', 'hello-movieengine' ),
			'section'         => $section_id,
			'active_callback' => array( __CLASS__, 'is_theme_brand_only' ),
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_primary_color', array(
			'default'           => '#E50914',
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hello_movieengine_primary_color', array(
			'label'           => esc_html__( 'Primary Color', 'hello-movieengine' ),
			'section'         => $section_id,
			'active_callback' => array( __CLASS__, 'is_theme_brand_only' ),
		) ) );

		if ( ! self::is_plugin_active() ) {
			return;
		}

		/* --- Movie Engine: Preset palette --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_movie_engine_colors', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_movie_engine_colors', array(
			'label'    => esc_html__( 'Movie Engine Palette', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 20,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[color_preset]', array(
			'type'              => 'option',
			'default'           => 'netflix',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_preset',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Button_Group_Control( $wp_customize, 'movie_engine_design_settings[color_preset]', array(
			'label'       => esc_html__( 'Color Preset', 'hello-movieengine' ),
			'description' => esc_html__( 'Streaming presets apply fixed palettes. Choose Custom to use the pickers below.', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 22,
			'choices'     => array(
				'netflix' => esc_html__( 'Streaming Red', 'hello-movieengine' ),
				'hulu'    => esc_html__( 'Stream Green', 'hello-movieengine' ),
				'prime'   => esc_html__( 'Prime Blue', 'hello-movieengine' ),
				'custom'  => esc_html__( 'Custom', 'hello-movieengine' ),
			),
		) ) );

		$custom_colors = array(
			'primary_color'       => array(
				'label' => esc_html__( 'Primary Color', 'hello-movieengine' ),
				'default' => '#E50914',
			),
			'primary_hover_color' => array(
				'label' => esc_html__( 'Primary Hover Color', 'hello-movieengine' ),
				'default' => '#B2070F',
			),
			'text_color'          => array(
				'label' => esc_html__( 'Text Color', 'hello-movieengine' ),
				'default' => '#FFFFFF',
			),
			'gray_color'          => array(
				'label' => esc_html__( 'Muted / Gray', 'hello-movieengine' ),
				'default' => '#A3A3A3',
			),
			'border_color'        => array(
				'label' => esc_html__( 'Border Color', 'hello-movieengine' ),
				'default' => '#23252c',
			),
			'background_color'    => array(
				'label' => esc_html__( 'Background Color', 'hello-movieengine' ),
				'default' => '#141414',
			),
			'surface_color'       => array(
				'label' => esc_html__( 'Surface / Card Color', 'hello-movieengine' ),
				'default' => '#1a1b1f',
			),
		);

		$priority = 30;
		foreach ( $custom_colors as $key => $args ) {
			$setting_id = 'movie_engine_design_settings[' . $key . ']';
			$wp_customize->add_setting( $setting_id, array(
				'type'              => 'option',
				'default'           => $args['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			) );
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, array(
				'label'       => $args['label'],
				'description' => esc_html__( 'Used when Color Preset is Custom.', 'hello-movieengine' ),
				'section'     => $section_id,
				'priority'    => $priority,
				'classes'     => 'hello-movieengine-me-custom-color',
			) ) );
			$priority += 2;
		}

		/* --- Layout tokens (General tab parity) --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_movie_engine_layout', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_movie_engine_layout', array(
			'label'    => esc_html__( 'Movie Engine Layout', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 50,
		) ) );

		$wp_customize->add_setting( 'movie_engine_design_settings[container_width]', array(
			'type'              => 'option',
			'default'           => 1170,
			'sanitize_callback' => 'hello_movieengine_sanitize_container_width',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[container_width]', array(
			'label'       => esc_html__( 'Container Max Width (px)', 'hello-movieengine' ),
			'description' => esc_html__( 'Maximum width for the main Movie Engine content container.', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'priority'    => 52,
			'input_attrs' => array( 'min' => 800, 'max' => 3840, 'step' => 10 ),
		) );

		$wp_customize->add_setting( 'movie_engine_design_settings[border_radius]', array(
			'type'              => 'option',
			'default'           => 8,
			'sanitize_callback' => 'hello_movieengine_sanitize_border_radius',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'movie_engine_design_settings[border_radius]', array(
			'label'       => esc_html__( 'Border Radius (px)', 'hello-movieengine' ),
			'description' => esc_html__( 'Rounded corners for cards, posters, and buttons.', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'priority'    => 54,
			'input_attrs' => array( 'min' => 0, 'max' => 50, 'step' => 1 ),
		) );
	}
}