<?php
/**
 * Customizer section: Footer.
 *
 * Registers copyright text and social link settings.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Footer_Section {

	/**
	 * Register footer controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer instance.
	 */
	public static function register( $wp_customize ) {

		$wp_customize->add_section( 'hello_movieengine_footer_section', array(
			'title' => esc_html__( 'Footer', 'hello-movieengine' ),
			'panel' => 'hello_movieengine_panel',
		) );

		/* --- Heading: Content --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_footer_content', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_footer_content', array(
			'label'   => esc_html__( 'Content', 'hello-movieengine' ),
			'section' => 'hello_movieengine_footer_section',
		) ) );

		/* Footer copyright text */
		$wp_customize->add_setting( 'hello_movieengine_footer_copyright', array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
			'transport'         => 'postMessage',
		) );
		$wp_customize->add_control( 'hello_movieengine_footer_copyright', array(
			'label'   => esc_html__( 'Copyright Text', 'hello-movieengine' ),
			'section' => 'hello_movieengine_footer_section',
			'type'    => 'textarea',
		) );

		/* --- Heading: Social Links --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_social', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_social', array(
			'label'   => esc_html__( 'Social Links', 'hello-movieengine' ),
			'section' => 'hello_movieengine_footer_section',
		) ) );

		$socials = array(
			'facebook'  => __( 'Facebook URL', 'hello-movieengine' ),
			'twitter'   => __( 'Twitter / X URL', 'hello-movieengine' ),
			'instagram' => __( 'Instagram URL', 'hello-movieengine' ),
			'youtube'   => __( 'YouTube URL', 'hello-movieengine' ),
		);

		foreach ( $socials as $key => $label ) {
			$wp_customize->add_setting( "hello_movieengine_social_{$key}", array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			) );

			$wp_customize->add_control( "hello_movieengine_social_{$key}", array(
				'label'   => $label,
				'section' => 'hello_movieengine_footer_section',
				'type'    => 'url',
			) );
		}
	}
}
