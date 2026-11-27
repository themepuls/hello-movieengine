<?php
/**
 * Customizer section: Colors.
 *
 * Full Movie Engine color palette and layout tokens synced with plugin design settings
 * (or theme_mods if plugin is inactive).
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Colors_Section {

	/**
	 * Presets definition matching plugin's GeneralTab.
	 *
	 * @var array
	 */
	public static $presets = array(
		'netflix' => array(
			'name'    => 'Streaming Red',
			'primary' => '#E50914',
			'hover'   => '#B2070F',
			'text'    => '#FFFFFF',
			'gray'    => '#A3A3A3',
			'border'  => '#23252c',
			'bg'      => '#141414',
			'surface' => '#1a1b1f',
		),
		'hulu'    => array(
			'name'    => 'Stream Green',
			'primary' => '#1CE783',
			'hover'   => '#15AB61',
			'text'    => '#FFFFFF',
			'gray'    => '#A3A3A3',
			'border'  => '#23252c',
			'bg'      => '#0b0c0f',
			'surface' => '#14151a',
		),
		'prime'   => array(
			'name'    => 'Prime Blue',
			'primary' => '#00A8E1',
			'hover'   => '#007FB2',
			'text'    => '#FFFFFF',
			'gray'    => '#A3A3A3',
			'border'  => '#23252c',
			'bg'      => '#0f171e',
			'surface' => '#1b2530',
		),
	);

	/**
	 * @return bool
	 */
	public static function is_plugin_active() {
		return function_exists( 'hello_movieengine_is_movie_engine_active' ) && hello_movieengine_is_movie_engine_active();
	}

	/**
	 * Get resolved colors array.
	 *
	 * @return array
	 */
	public static function get_color_palette() {
		$is_plugin = self::is_plugin_active();

		if ( $is_plugin ) {
			$options = get_option( 'movie_engine_design_settings', array() );
			$preset  = ! empty( $options['color_preset'] ) ? $options['color_preset'] : 'netflix';
		} else {
			$preset = get_theme_mod( 'hello_movieengine_color_preset', 'netflix' );
			$options = array(
				'primary_color'       => get_theme_mod( 'hello_movieengine_primary_color', '#E50914' ),
				'primary_hover_color' => get_theme_mod( 'hello_movieengine_primary_hover_color', '#B2070F' ),
				'text_color'          => get_theme_mod( 'hello_movieengine_text_color', '#FFFFFF' ),
				'gray_color'          => get_theme_mod( 'hello_movieengine_gray_color', '#A3A3A3' ),
				'border_color'        => get_theme_mod( 'hello_movieengine_border_color', '#23252c' ),
				'background_color'    => get_theme_mod( 'hello_movieengine_background_color', '#141414' ),
				'surface_color'       => get_theme_mod( 'hello_movieengine_surface_color', '#1a1b1f' ),
				'container_width'     => get_theme_mod( 'hello_movieengine_container_width', 1170 ),
				'border_radius'       => get_theme_mod( 'hello_movieengine_border_radius', 8 ),
			);
		}

		if ( 'custom' === $preset ) {
			$primary = ! empty( $options['primary_color'] ) ? $options['primary_color'] : '#E50914';
			$hover   = ! empty( $options['primary_hover_color'] ) ? $options['primary_hover_color'] : '#B2070F';
			$text    = ! empty( $options['text_color'] ) ? $options['text_color'] : '#FFFFFF';
			$gray    = ! empty( $options['gray_color'] ) ? $options['gray_color'] : '#A3A3A3';
			$border  = ! empty( $options['border_color'] ) ? $options['border_color'] : '#23252c';
			$bg      = ! empty( $options['background_color'] ) ? $options['background_color'] : '#141414';
			$surface = ! empty( $options['surface_color'] ) ? $options['surface_color'] : '#1a1b1f';
		} else {
			$p = isset( self::$presets[ $preset ] ) ? self::$presets[ $preset ] : self::$presets['netflix'];
			$primary = $p['primary'];
			$hover   = $p['hover'];
			$text    = $p['text'];
			$gray    = $p['gray'];
			$border  = $p['border'];
			$bg      = $p['bg'];
			$surface = $p['surface'];
		}

		$container_width = ! empty( $options['container_width'] ) ? intval( $options['container_width'] ) : 1170;
		$border_radius   = isset( $options['border_radius'] ) ? intval( $options['border_radius'] ) : 8;

		return array(
			'preset'          => $preset,
			'primary'         => $primary,
			'hover'           => $hover,
			'text'            => $text,
			'gray'            => $gray,
			'border'          => $border,
			'bg'              => $bg,
			'surface'         => $surface,
			'container_width' => $container_width,
			'border_radius'   => $border_radius,
		);
	}

	/**
	 * Register color controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance.
	 */
	public static function register( $wp_customize ) {

		$section_id = 'hello_movieengine_colors_section';
		$is_plugin  = self::is_plugin_active();

		$wp_customize->add_section( $section_id, array(
			'title'       => esc_html__( 'Colors & Branding', 'hello-movieengine' ),
			'description' => $is_plugin
				? esc_html__( 'Palette and layout tokens sync with Movie Engine Design settings (same as the plugin admin).', 'hello-movieengine' )
				: esc_html__( 'Global website colors and branding palette for your theme.', 'hello-movieengine' ),
			'priority'    => 10,
		) );

		/* --- Palette Presets --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_palette', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_palette', array(
			'label'    => esc_html__( 'Preset Colors', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 10,
		) ) );

		$preset_setting = $is_plugin ? 'movie_engine_design_settings[color_preset]' : 'hello_movieengine_color_preset';
		$preset_type    = $is_plugin ? 'option' : 'theme_mod';

		$wp_customize->add_setting( $preset_setting, array(
			'type'              => $preset_type,
			'default'           => 'netflix',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_preset',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Button_Group_Control( $wp_customize, $preset_setting, array(
			'label'       => esc_html__( 'Color Preset', 'hello-movieengine' ),
			'description' => esc_html__( 'Streaming presets apply fixed palettes. Choose Custom to use the individual color pickers below.', 'hello-movieengine' ),
			'section'     => $section_id,
			'priority'    => 12,
			'choices'     => array(
				'netflix' => esc_html__( 'Streaming Red', 'hello-movieengine' ),
				'hulu'    => esc_html__( 'Stream Green', 'hello-movieengine' ),
				'prime'   => esc_html__( 'Prime Blue', 'hello-movieengine' ),
				'custom'  => esc_html__( 'Custom', 'hello-movieengine' ),
			),
		) ) );

		/* --- Custom Colors --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_custom_colors', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_custom_colors', array(
			'label'    => esc_html__( 'Custom Palette', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 20,
		) ) );

		$custom_colors = array(
			'primary_color'       => array(
				'label'       => esc_html__( 'Primary Color', 'hello-movieengine' ),
				'description' => esc_html__( 'Choose a primary brand color.', 'hello-movieengine' ),
				'default'     => '#E50914',
			),
			'primary_hover_color' => array(
				'label'       => esc_html__( 'Primary Hover Color', 'hello-movieengine' ),
				'description' => esc_html__( 'Choose a primary hover color.', 'hello-movieengine' ),
				'default'     => '#B2070F',
			),
			'text_color'          => array(
				'label'       => esc_html__( 'Text Color', 'hello-movieengine' ),
				'description' => esc_html__( 'Choose a text color for your website.', 'hello-movieengine' ),
				'default'     => '#FFFFFF',
			),
			'gray_color'          => array(
				'label'       => esc_html__( 'Muted / Gray Color', 'hello-movieengine' ),
				'description' => esc_html__( 'Choose a color for secondary elements, table, cards etc.', 'hello-movieengine' ),
				'default'     => '#A3A3A3',
			),
			'border_color'        => array(
				'label'       => esc_html__( 'Border Color', 'hello-movieengine' ),
				'description' => esc_html__( 'Choose a border color for your website.', 'hello-movieengine' ),
				'default'     => '#23252c',
			),
			'background_color'    => array(
				'label'       => esc_html__( 'Background Color', 'hello-movieengine' ),
				'description' => esc_html__( 'Choose a main background color.', 'hello-movieengine' ),
				'default'     => '#141414',
			),
			'surface_color'       => array(
				'label'       => esc_html__( 'Surface / Card Color', 'hello-movieengine' ),
				'description' => esc_html__( 'Choose a color for boxes, side rail, and cards.', 'hello-movieengine' ),
				'default'     => '#1a1b1f',
			),
		);

		$priority = 22;
		foreach ( $custom_colors as $key => $args ) {
			$setting_id = $is_plugin ? 'movie_engine_design_settings[' . $key . ']' : 'hello_movieengine_' . $key;
			$type       = $is_plugin ? 'option' : 'theme_mod';

			$wp_customize->add_setting( $setting_id, array(
				'type'              => $type,
				'default'           => $args['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			) );
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, array(
				'label'       => $args['label'],
				'description' => $args['description'],
				'section'     => $section_id,
				'priority'    => $priority,
				'classes'     => 'hello-movieengine-me-custom-color',
			) ) );
			$priority += 2;
		}

		/* --- Layout & Style Tokens --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_movie_engine_layout', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_movie_engine_layout', array(
			'label'    => esc_html__( 'Layout & Style Tokens', 'hello-movieengine' ),
			'section'  => $section_id,
			'priority' => 50,
		) ) );

		$width_setting  = $is_plugin ? 'movie_engine_design_settings[container_width]' : 'hello_movieengine_container_width';
		$radius_setting = $is_plugin ? 'movie_engine_design_settings[border_radius]' : 'hello_movieengine_border_radius';
		$layout_type    = $is_plugin ? 'option' : 'theme_mod';

		$wp_customize->add_setting( $width_setting, array(
			'type'              => $layout_type,
			'default'           => 1170,
			'sanitize_callback' => 'hello_movieengine_sanitize_container_width',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $width_setting, array(
			'label'       => esc_html__( 'Container Max Width (px)', 'hello-movieengine' ),
			'description' => esc_html__( 'Maximum width for the main content container.', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'priority'    => 52,
			'input_attrs' => array( 'min' => 800, 'max' => 3840, 'step' => 10 ),
		) );

		$wp_customize->add_setting( $radius_setting, array(
			'type'              => $layout_type,
			'default'           => 8,
			'sanitize_callback' => 'hello_movieengine_sanitize_border_radius',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $radius_setting, array(
			'label'       => esc_html__( 'Border Radius (px)', 'hello-movieengine' ),
			'description' => esc_html__( 'Rounded corners for cards, posters, and buttons.', 'hello-movieengine' ),
			'section'     => $section_id,
			'type'        => 'number',
			'priority'    => 54,
			'input_attrs' => array( 'min' => 0, 'max' => 50, 'step' => 1 ),
		) );
	}
}

// Automatically purge plugin CSS cache when Customizer saves so new colors take effect immediately.
add_action( 'customize_save_after', function() {
	if ( class_exists( '\MovieEngine\Core\Cache' ) && method_exists( '\MovieEngine\Core\Cache', 'purge_all' ) ) {
		\MovieEngine\Core\Cache::purge_all();
	}
} );
