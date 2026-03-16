<?php
/**
 * Custom Customizer control: Spacing (Top / Right / Bottom / Left + unit).
 *
 * Features a link/unlink toggle to sync all 4 sides.
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

class Hello_Cine_Movie_Engine_Customize_Spacing_Control extends WP_Customize_Control {

	public $type = 'hello-movieengine-spacing';

	public $units = array( 'px', 'rem', 'em', '%', 'vw', 'vh' );

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
		$val     = $this->value();
		$decoded = json_decode( $val, true );

		if ( ! is_array( $decoded ) ) {
			$decoded = array(
				'top'    => '0',
				'right'  => '4',
				'bottom' => '0',
				'left'   => '4',
				'unit'   => 'rem',
				'linked' => false,
			);
		}

		$linked = ! empty( $decoded['linked'] );

		$sides = array(
			'top'    => esc_html__( 'Top', 'hello-movieengine' ),
			'right'  => esc_html__( 'Right', 'hello-movieengine' ),
			'bottom' => esc_html__( 'Bottom', 'hello-movieengine' ),
			'left'   => esc_html__( 'Left', 'hello-movieengine' ),
		);
		?>
		<div class="hello-movieengine-spacing-control" data-linked="<?php echo $linked ? '1' : '0'; ?>">
			<?php if ( $this->label ) : ?>
				<div class="hello-movieengine-spacing-header">
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<button type="button" class="hello-movieengine-spacing-link <?php echo $linked ? 'is-linked' : ''; ?>" title="<?php esc_attr_e( 'Link / Unlink values', 'hello-movieengine' ); ?>">
						<svg class="hello-movieengine-icon-linked" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
						<svg class="hello-movieengine-icon-unlinked" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 7h3a5 5 0 0 1 5 5 5 5 0 0 1-4 4.9"/><path d="M9 17H6a5 5 0 0 1-5-5 5 5 0 0 1 4-4.9"/></svg>
					</button>
				</div>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<div class="hello-movieengine-spacing-row">
				<?php foreach ( $sides as $key => $lbl ) : ?>
					<div class="hello-movieengine-spacing-field">
						<input
							type="number"
							class="hello-movieengine-spacing-input"
							data-side="<?php echo esc_attr( $key ); ?>"
							value="<?php echo esc_attr( isset( $decoded[ $key ] ) ? $decoded[ $key ] : '0' ); ?>"
							min="0"
							step="1"
						/>
						<span class="hello-movieengine-spacing-label"><?php echo esc_html( $lbl ); ?></span>
					</div>
				<?php endforeach; ?>
				<div class="hello-movieengine-spacing-field hello-movieengine-spacing-unit">
					<select class="hello-movieengine-spacing-unit-select" data-side="unit">
						<?php foreach ( $this->units as $u ) : ?>
							<option value="<?php echo esc_attr( $u ); ?>" <?php selected( isset( $decoded['unit'] ) ? $decoded['unit'] : 'rem', $u ); ?>><?php echo esc_html( $u ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<input
				type="hidden"
				class="hello-movieengine-spacing-value"
				value="<?php echo esc_attr( $val ); ?>"
				<?php $this->link(); ?>
			/>
		</div>
		<?php
	}
}
