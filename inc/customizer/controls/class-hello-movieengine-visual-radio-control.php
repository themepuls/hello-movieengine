<?php
/**
 * Custom Customizer control: Visual Radio Cards (SVG Layout Wireframes).
 *
 * Astra-style visual wireframe cards for layouts and styles instead of dropdowns.
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

class Hello_Movie_Engine_Customize_Visual_Radio_Control extends WP_Customize_Control {

	public $type = 'hello-movieengine-visual-radio';

	public function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}

		$current_value = (string) $this->value();
		?>
		<div class="hello-movieengine-visual-radio-control">
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<div class="hello-movieengine-visual-cards-grid">
				<?php foreach ( $this->choices as $val => $data ) : ?>
					<?php
					$is_active = ( (string) $val === $current_value );
					$label     = is_array( $data ) ? ( $data['label'] ?? $val ) : $data;
					$svg       = is_array( $data ) ? ( $data['svg'] ?? '' ) : '';
					?>
					<div
						class="hello-movieengine-visual-card<?php echo $is_active ? ' is-active' : ''; ?>"
						data-value="<?php echo esc_attr( $val ); ?>"
						tabindex="0"
						role="radio"
						aria-checked="<?php echo $is_active ? 'true' : 'false'; ?>"
					>
						<div class="hello-movieengine-visual-card__preview">
							<?php if ( ! empty( $svg ) ) : ?>
								<?php echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php else : ?>
								<div class="hello-movieengine-visual-card__fallback-icon">
									<span><?php echo esc_html( strtoupper( substr( (string) $val, 0, 2 ) ) ); ?></span>
								</div>
							<?php endif; ?>
						</div>
						<span class="hello-movieengine-visual-card__label"><?php echo esc_html( $label ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>

			<input
				type="hidden"
				class="hello-movieengine-visual-radio-value"
				value="<?php echo esc_attr( $current_value ); ?>"
				<?php $this->link(); ?>
			/>
		</div>
		<?php
	}
}
