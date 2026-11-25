<?php
/**
 * Custom Customizer control: Button Group (Segmented Pill Switcher).
 *
 * Modern clickable button group for choices instead of standard select dropdowns.
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

class Hello_Movie_Engine_Customize_Button_Group_Control extends WP_Customize_Control {

	public $type = 'hello-movieengine-button-group';

	public function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}

		$current_value = (string) $this->value();
		?>
		<div class="hello-movieengine-button-group-control">
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<div class="hello-movieengine-btn-group" role="group">
				<?php foreach ( $this->choices as $val => $label ) : ?>
					<?php $is_active = ( (string) $val === $current_value ); ?>
					<button
						type="button"
						class="hello-movieengine-btn-group-item<?php echo $is_active ? ' is-active' : ''; ?>"
						data-value="<?php echo esc_attr( $val ); ?>"
					>
						<?php echo esc_html( $label ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<input
				type="hidden"
				class="hello-movieengine-btn-group-value"
				value="<?php echo esc_attr( $current_value ); ?>"
				<?php $this->link(); ?>
			/>
		</div>
		<?php
	}
}
