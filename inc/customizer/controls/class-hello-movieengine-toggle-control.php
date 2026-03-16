<?php
/**
 * Custom Customizer control: Toggle Switch.
 *
 * A modern on/off toggle instead of a standard checkbox.
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

class Hello_Movie_Engine_Customize_Toggle_Control extends WP_Customize_Control {

	public $type = 'hello-movieengine-toggle';

	public function enqueue() {
		wp_enqueue_style(
			'hello-movieengine-customizer-controls',
			HELLO_MOVIEENGINE_URI . '/assets/css/customizer-controls.css',
			array(),
			HELLO_MOVIEENGINE_VERSION
		);
		wp_enqueue_script(
			'hello-movieengine-customizer-controls',
			HELLO_MOVIEENGINE_URI . '/assets/js/customizer-controls.js',
			array( 'jquery', 'customize-controls' ),
			HELLO_MOVIEENGINE_VERSION,
			true
		);
	}

	public function render_content() {
		$checked = (bool) $this->value();
		?>
		<div class="hello-movieengine-toggle-control">
			<div class="hello-movieengine-toggle-row">
				<?php if ( $this->label ) : ?>
					<span class="hello-movieengine-toggle-label"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>
				<label class="hello-movieengine-toggle-switch">
					<input
						type="checkbox"
						class="hello-movieengine-toggle-input"
						value="1"
						<?php checked( $checked ); ?>
						<?php $this->link(); ?>
					/>
					<span class="hello-movieengine-toggle-track">
						<span class="hello-movieengine-toggle-thumb"></span>
					</span>
				</label>
			</div>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	}
}
