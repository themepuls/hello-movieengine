<?php
/**
 * Customizer panel: Animation & motion (both site styles).
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Animation_Section {

	/**
	 * @param WP_Customize_Manager $wp_customize Customizer.
	 */
	public static function register( $wp_customize ) {

		$panel_id   = 'hello_movieengine_animation_panel';
		$section_id = 'hello_movieengine_animation_section';

		$wp_customize->add_panel(
			$panel_id,
			array(
				'title'       => esc_html__( 'Animation', 'hello-movieengine' ),
				'description' => esc_html__( 'OTT-style motion for Style One and Style Two. Turn off here to skip motion CSS/JS entirely. Honors the visitor\'s “reduce motion” system setting.', 'hello-movieengine' ),
				'priority'    => 12,
			)
		);

		$wp_customize->add_section(
			$section_id,
			array(
				'title'    => esc_html__( 'Motion & Performance', 'hello-movieengine' ),
				'panel'    => $panel_id,
				'priority' => 10,
			)
		);

		$wp_customize->add_setting(
			'hello_movieengine_animation_enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new Hello_Movie_Engine_Customize_Toggle_Control(
				$wp_customize,
				'hello_movieengine_animation_enable',
				array(
					'label'       => esc_html__( 'Enable site animations', 'hello-movieengine' ),
					'description' => esc_html__( 'Master switch. Off = no motion.css / motion.js loaded (best for speed).', 'hello-movieengine' ),
					'section'     => $section_id,
					'priority'    => 10,
				)
			)
		);

		$wp_customize->add_setting(
			'hello_movieengine_animation_intensity',
			array(
				'default'           => 'balanced',
				'sanitize_callback' => 'hello_movieengine_sanitize_animation_intensity',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new Hello_Movie_Engine_Customize_Button_Group_Control(
				$wp_customize,
				'hello_movieengine_animation_intensity',
				array(
					'label'       => esc_html__( 'Motion intensity', 'hello-movieengine' ),
					'description' => esc_html__( 'Subtle uses shorter, smaller moves. Cinematic is richer but slightly heavier.', 'hello-movieengine' ),
					'section'     => $section_id,
					'priority'    => 20,
					'choices'     => array(
						'subtle'     => esc_html__( 'Subtle', 'hello-movieengine' ),
						'balanced'   => esc_html__( 'Balanced', 'hello-movieengine' ),
						'cinematic'  => esc_html__( 'Cinematic', 'hello-movieengine' ),
					),
				)
			)
		);

		$wp_customize->add_setting(
			'hello_movieengine_heading_animation_effects',
			array(
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			new Hello_Movie_Engine_Customize_Heading_Control(
				$wp_customize,
				'hello_movieengine_heading_animation_effects',
				array(
					'label'    => esc_html__( 'Effect layers', 'hello-movieengine' ),
					'section'  => $section_id,
					'priority' => 30,
				)
			)
		);

		$layers = array(
			'hello_movieengine_animation_scroll_reveal' => array(
				'label'       => esc_html__( 'Scroll reveal (sections)', 'hello-movieengine' ),
				'description' => esc_html__( 'Fade rows in as you scroll. Uses one lightweight IntersectionObserver.', 'hello-movieengine' ),
				'default'     => true,
				'priority'    => 32,
			),
			'hello_movieengine_animation_page_paint'    => array(
				'label'       => esc_html__( 'Page fade-in on load', 'hello-movieengine' ),
				'description' => esc_html__( 'Short opacity transition when the page appears.', 'hello-movieengine' ),
				'default'     => true,
				'priority'    => 34,
			),
			'hello_movieengine_animation_card_hover'    => array(
				'label'       => esc_html__( 'Poster hover lift', 'hello-movieengine' ),
				'description' => esc_html__( 'Scale posters on hover (desktop only).', 'hello-movieengine' ),
				'default'     => true,
				'priority'    => 36,
			),
			'hello_movieengine_animation_card_stagger'  => array(
				'label'       => esc_html__( 'Card entrance stagger', 'hello-movieengine' ),
				'description' => esc_html__( 'Animates each poster when a row appears. Turn off on slow devices.', 'hello-movieengine' ),
				'default'     => true,
				'priority'    => 38,
			),
		);

		foreach ( $layers as $setting_id => $args ) {
			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => $args['default'],
					'sanitize_callback' => 'hello_movieengine_sanitize_toggle',
					'transport'         => 'refresh',
				)
			);
			$wp_customize->add_control(
				new Hello_Movie_Engine_Customize_Toggle_Control(
					$wp_customize,
					$setting_id,
					array(
						'label'       => $args['label'],
						'description' => $args['description'],
						'section'     => $section_id,
						'priority'    => $args['priority'],
					)
				)
			);
		}
	}
}