<?php
/**
 * Customizer section: 404 Page.
 *
 * Controls for the error page title, description, search form, and home button.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_404_Section {

	public static function register( $wp_customize ) {

		$wp_customize->add_section( 'hello_movieengine_404_section', array(
			'title'       => esc_html__( '404 Page', 'hello-movieengine' ),
			'panel'       => 'hello_movieengine_panel',
			'description' => esc_html__( 'Customize the "Page Not Found" error page.', 'hello-movieengine' ),
		) );

		/* Title */
		$wp_customize->add_setting( 'hello_movieengine_404_title', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_404_title', array(
			'label'       => esc_html__( 'Title', 'hello-movieengine' ),
			'description' => esc_html__( 'Leave blank for default: "Page Not Found".', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_404_section',
			'type'        => 'text',
		) );

		/* Description */
		$wp_customize->add_setting( 'hello_movieengine_404_description', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_404_description', array(
			'label'       => esc_html__( 'Description', 'hello-movieengine' ),
			'description' => esc_html__( 'Leave blank for the default message.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_404_section',
			'type'        => 'textarea',
		) );

		/* Show search form */
		$wp_customize->add_setting( 'hello_movieengine_404_show_search', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_404_show_search', array(
			'label'   => esc_html__( 'Show Search Form', 'hello-movieengine' ),
			'section' => 'hello_movieengine_404_section',
		) ) );

		/* Show home button */
		$wp_customize->add_setting( 'hello_movieengine_404_show_home_button', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_404_show_home_button', array(
			'label'   => esc_html__( 'Show Back to Home Button', 'hello-movieengine' ),
			'section' => 'hello_movieengine_404_section',
		) ) );

		/* Home button text */
		$wp_customize->add_setting( 'hello_movieengine_404_home_button_text', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_404_home_button_text', array(
			'label'       => esc_html__( 'Home Button Text', 'hello-movieengine' ),
			'description' => esc_html__( 'Leave blank for default: "Back to Home".', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_404_section',
			'type'        => 'text',
		) );
	}
}
