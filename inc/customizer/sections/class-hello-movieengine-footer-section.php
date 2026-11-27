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
			'title'    => esc_html__( 'Footer', 'hello-movieengine' ),
			'priority' => 6,
		) );

		/* --- Heading: Background --- */
		$wp_customize->add_setting( 'hello_movieengine_heading_footer_background', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_footer_background', array(
			'label'   => esc_html__( 'Background', 'hello-movieengine' ),
			'section' => 'hello_movieengine_footer_section',
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_footer_bg_color', array(
			'default'           => '',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_footer_bg_color', array(
			'label'       => esc_html__( 'Background Color', 'hello-movieengine' ),
			'description' => esc_html__( 'Footer area fill. Supports transparency.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_footer_section',
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_footer_bg_image', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hello_movieengine_footer_bg_image', array(
			'label'       => esc_html__( 'Background Image', 'hello-movieengine' ),
			'description' => esc_html__( 'Optional photo or pattern behind the footer content.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_footer_section',
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_footer_bg_image_opacity', array(
			'default'           => 100,
			'sanitize_callback' => static function ( $value ) {
				return min( 100, max( 0, absint( $value ) ) );
			},
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'hello_movieengine_footer_bg_image_opacity', array(
			'label'       => esc_html__( 'Background Image Opacity (%)', 'hello-movieengine' ),
			'description' => esc_html__( '100 = fully visible image. Lower values fade the image into the background color.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_footer_section',
			'type'        => 'number',
			'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
		) );

		$wp_customize->add_setting( 'hello_movieengine_footer_bg_overlay', array(
			'default'           => '',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_footer_bg_overlay', array(
			'label'       => esc_html__( 'Image Overlay Color', 'hello-movieengine' ),
			'description' => esc_html__( 'Tint over the background image for readability (e.g. dark rgba).', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_footer_section',
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_footer_border_color', array(
			'default'           => '',
			'sanitize_callback' => 'hello_movieengine_sanitize_color_alpha',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Alpha_Color_Control( $wp_customize, 'hello_movieengine_footer_border_color', array(
			'label'       => esc_html__( 'Top Border Color', 'hello-movieengine' ),
			'description' => esc_html__( 'Line above the footer. Leave empty to use the global border color from Colors.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_footer_section',
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_heading_footer_spacing', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Heading_Control( $wp_customize, 'hello_movieengine_heading_footer_spacing', array(
			'label'   => esc_html__( 'Spacing', 'hello-movieengine' ),
			'section' => 'hello_movieengine_footer_section',
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_footer_margin', array(
			'default'           => wp_json_encode( array(
				'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'linked' => true,
			) ),
			'sanitize_callback' => 'hello_movieengine_sanitize_spacing',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Spacing_Control( $wp_customize, 'hello_movieengine_footer_margin', array(
			'label'       => esc_html__( 'Footer Margin', 'hello-movieengine' ),
			'description' => esc_html__( 'Outer space around the footer block.', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_footer_section',
		) ) );

		$wp_customize->add_setting( 'hello_movieengine_footer_padding', array(
			'default'           => wp_json_encode( array(
				'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'linked' => true,
			) ),
			'sanitize_callback' => 'hello_movieengine_sanitize_spacing',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new Hello_Movie_Engine_Customize_Spacing_Control( $wp_customize, 'hello_movieengine_footer_padding', array(
			'label'       => esc_html__( 'Footer Padding', 'hello-movieengine' ),
			'description' => esc_html__( 'Inner space inside the footer wrapper (outside widget columns).', 'hello-movieengine' ),
			'section'     => 'hello_movieengine_footer_section',
		) ) );

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
