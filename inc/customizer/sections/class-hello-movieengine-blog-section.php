<?php
/**
 * Customizer section: Blog.
 *
 * Controls for blog archive: post image, thumbnail visibility, etc.
 *
 * @package Hello_Cine Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Cine Movie Engine_Blog_Section {

	public static function register( $wp_customize ) {

		$wp_customize->add_section( 'hello_movieengine_blog_section', array(
			'title' => esc_html__( 'Blog', 'hello-movieengine' ),
			'panel' => 'hello_movieengine_panel',
		) );

		/* --- Heading: Post Image --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_blog_image', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_blog_image', array(
			'label'   => esc_html__( 'Post Image', 'hello-movieengine' ),
			'section' => 'hello_movieengine_blog_section',
		) ) );

		/* Show post thumbnail */
		$wp_customize->add_setting( 'hello_movieengine_blog_show_thumbnail', array(
			'default'           => true,
			'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Cine Movie Engine_Customize_Toggle_Control( $wp_customize, 'hello_movieengine_blog_show_thumbnail', array(
			'label'   => esc_html__( 'Show Featured Image', 'hello-movieengine' ),
			'section' => 'hello_movieengine_blog_section',
		) ) );

		/* Post image height */
		$wp_customize->add_setting( 'hello_movieengine_blog_image_height', array(
			'default'           => 'auto',
			'sanitize_callback' => 'hello_movieengine_sanitize_select',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_blog_image_height', array(
			'label'       => esc_html__( 'Image Height', 'hello-movieengine' ),
			'description' => esc_html__( 'Use "Auto" to keep natural image proportions.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_blog_section',
			'type'        => 'select',
			'choices'     => array(
				'auto'   => esc_html__( 'Auto (natural height)', 'hello-movieengine' ),
				'180'    => esc_html__( '180px', 'hello-movieengine' ),
				'220'    => esc_html__( '220px', 'hello-movieengine' ),
				'260'    => esc_html__( '260px', 'hello-movieengine' ),
			),
		) );
	}
}
