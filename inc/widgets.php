<?php
/**
 * Register widget areas.
 *
 * @package Hello_Cine Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function hello_movieengine_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'hello-movieengine' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Main sidebar widget area.', 'hello-movieengine' ),
		'before_widget' => '<section id="%1$s" class="widget hello-movieengine-widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar( array(
			/* translators: %d: footer column number */
			'name'          => sprintf( esc_html__( 'Footer Column %d', 'hello-movieengine' ), $i ),
			'id'            => 'footer-' . $i,
			'description'   => sprintf(
				/* translators: %d: footer column number */
				esc_html__( 'Footer widget area column %d.', 'hello-movieengine' ),
				$i
			),
			'before_widget' => '<div id="%1$s" class="widget hello-movieengine-footer-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}
}
add_action( 'widgets_init', 'hello_movieengine_widgets_init' );
