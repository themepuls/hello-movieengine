<?php
/**
 * Custom Customizer control: Section Heading / Divider.
 *
 * Renders a styled heading to visually group controls within a section.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

class Hello_Movie_Engine_Customize_Heading_Control extends WP_Customize_Control {

	public $type = 'hello-movieengine-heading';

	public function enqueue() {
		wp_enqueue_style(
			'hello-movieengine-customizer-controls',
			HELLO_MOVIEENGINE_URI . '/assets/css/customizer-controls.css',
			array(),
			HELLO_MOVIEENGINE_VERSION
		);
	}

	public function render_content() {
		?>
		<div class="hello-movieengine-heading-control">
			<?php if ( $this->label ) : ?>
				<span class="hello-movieengine-heading-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<span class="hello-movieengine-heading-desc"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	}
}
