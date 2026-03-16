<?php
/**
 * Custom Customizer control: Multi-select (pill toggles).
 *
 * Renders a set of clickable pill buttons. Each pill represents
 * a choice that can be toggled on/off independently.
 * Value is stored as a JSON-encoded array of selected keys.
 *
 * @package Hello_Cine Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Cine Movie Engine_Customize_Multiselect_Control extends WP_Customize_Control {

	public $type = 'hello-movieengine-multiselect';

	public function enqueue() {
		wp_enqueue_style( 'hello-movieengine-customizer-controls' );
		wp_enqueue_script( 'hello-movieengine-customizer-controls' );
	}

	public function render_content() {
		$value    = $this->value();
		$selected = json_decode( $value, true );
		if ( ! is_array( $selected ) ) {
			$selected = array();
		}
		?>
		<div class="hello-movieengine-multiselect-control">
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<div class="hello-movieengine-multiselect-pills">
				<?php foreach ( $this->choices as $key => $label ) : ?>
					<button type="button"
						class="hello-movieengine-pill <?php echo in_array( $key, $selected, true ) ? 'is-active' : ''; ?>"
						data-value="<?php echo esc_attr( $key ); ?>">
						<?php echo esc_html( $label ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<input type="hidden" class="hello-movieengine-multiselect-value" value="<?php echo esc_attr( $value ); ?>" <?php $this->link(); ?> />
		</div>
		<?php
	}
}
