<?php
/**
 * Customizer section: Site Style (Style One / Style Two).
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Site_Style_Section {

	/**
	 * Register site style controls (first in the panel).
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance.
	 */
	public static function register( $wp_customize ) {

		$wp_customize->add_section( 'hello_movieengine_site_style_section', array(
			'title'       => esc_html__( 'Site Style', 'hello-movieengine' ),
			'description' => esc_html__( 'Choose which design pack loads. Only the selected style\'s CSS, templates, and Movie Engine overrides are used.', 'hello-movieengine' ),
			'panel'       => 'hello_movieengine_panel',
			'priority'    => 1,
		) );

		$choices = array();
		foreach ( Hello_Movie_Engine_Style_Registry::get_styles() as $slug => $meta ) {
			$choices[ $slug ] = $meta['label'];
		}

		$wp_customize->add_setting( Hello_Movie_Engine_Style_Registry::SETTING_KEY, array(
			'default'           => Hello_Movie_Engine_Style_Registry::DEFAULT_STYLE,
			'sanitize_callback' => 'hello_movieengine_sanitize_site_style',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control( Hello_Movie_Engine_Style_Registry::SETTING_KEY, array(
			'label'       => esc_html__( 'Design Pack', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_site_style_section',
			'type'        => 'select',
			'choices'     => $choices,
		) );
	}
}