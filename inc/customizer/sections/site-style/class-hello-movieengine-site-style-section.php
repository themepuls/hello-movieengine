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
			'priority'    => 2,
		) );

		$choices = array(
			'style-one' => array(
				'label' => esc_html__( 'Style One (Classic Cinema)', 'hello-movieengine' ),
				'svg'   => '<svg viewBox="0 0 120 75" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="120" height="75" rx="4" fill="#F8FAFC" stroke="#CBD5E1"/><rect x="6" y="6" width="108" height="12" rx="2" fill="#3B82F6"/><rect x="6" y="24" width="32" height="44" rx="2" fill="#CBD5E1"/><rect x="44" y="24" width="32" height="44" rx="2" fill="#CBD5E1"/><rect x="82" y="24" width="32" height="44" rx="2" fill="#CBD5E1"/></svg>',
			),
			'style-two' => array(
				'label' => esc_html__( 'Style Two (OTT Streaming)', 'hello-movieengine' ),
				'svg'   => '<svg viewBox="0 0 120 75" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="120" height="75" rx="4" fill="#0F172A"/><rect x="4" y="4" width="16" height="67" rx="2" fill="#3B82F6"/><rect x="24" y="4" width="92" height="26" rx="2" fill="#334155"/><rect x="24" y="34" width="20" height="37" rx="2" fill="#475569"/><rect x="48" y="34" width="20" height="37" rx="2" fill="#475569"/><rect x="72" y="34" width="20" height="37" rx="2" fill="#475569"/><rect x="96" y="34" width="20" height="37" rx="2" fill="#475569"/></svg>',
			),
		);

		$wp_customize->add_setting( Hello_Movie_Engine_Style_Registry::SETTING_KEY, array(
			'default'           => Hello_Movie_Engine_Style_Registry::DEFAULT_STYLE,
			'sanitize_callback' => 'hello_movieengine_sanitize_site_style',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Visual_Radio_Control( $wp_customize, Hello_Movie_Engine_Style_Registry::SETTING_KEY, array(
			'label'       => esc_html__( 'Design Pack Selection', 'hello-movieengine' ),
			'description' => esc_html__( 'Select your site layout style. Style Two enables the global OTT left navigation and cinema features.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_site_style_section',
			'choices'     => $choices,
		) ) );
	}
}