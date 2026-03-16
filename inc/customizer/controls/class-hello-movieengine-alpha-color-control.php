<?php
/**
 * Custom Customizer control: Alpha Color Picker.
 *
 * Extends the WP color picker with an opacity/alpha slider.
 * Supports hex and rgba output.
 *
 * Uses a hidden input for the Customizer API binding so that
 * wpColorPicker's DOM manipulation doesn't break the link.
 *
 * @package Hello_Cine Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

class Hello_Cine_Movie_Engine_Customize_Alpha_Color_Control extends WP_Customize_Control {

	public $type = 'hello-movieengine-alpha-color';

	public $show_opacity = true;

	public $palette = true;

	public function enqueue() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_style(
			'hello-movieengine-customizer-controls',
			HELLO_MOVIEENGINE_URI . '/assets/css/customizer-controls.css',
			array( 'wp-color-picker' ),
			HELLO_MOVIEENGINE_VERSION
		);
		wp_enqueue_script(
			'hello-movieengine-customizer-controls',
			HELLO_MOVIEENGINE_URI . '/assets/js/customizer-controls.js',
			array( 'jquery', 'wp-color-picker', 'customize-controls' ),
			HELLO_MOVIEENGINE_VERSION,
			true
		);
	}

	public function render_content() {
		$value = $this->value();
		?>
		<div class="hello-movieengine-alpha-color-control">
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<div class="hello-movieengine-color-field-wrap">
				<input
					type="text"
					class="hello-movieengine-alpha-color-input"
					value="<?php echo esc_attr( $value ); ?>"
					data-default-color="<?php echo esc_attr( $this->setting->default ); ?>"
				/>
			</div>

			<?php if ( $this->show_opacity ) : ?>
				<div class="hello-movieengine-alpha-slider-wrap">
					<label class="hello-movieengine-alpha-label"><?php esc_html_e( 'Opacity', 'hello-movieengine' ); ?></label>
					<input type="range" class="hello-movieengine-alpha-slider" min="0" max="1" step="0.01" value="1" />
					<span class="hello-movieengine-alpha-value">100%</span>
				</div>
			<?php endif; ?>

			<input
				type="hidden"
				class="hello-movieengine-alpha-color-value"
				value="<?php echo esc_attr( $value ); ?>"
				<?php $this->link(); ?>
			/>
		</div>
		<?php
	}
}
