<?php
/**
 * Custom Customizer control: Section Tabs (GENERAL vs DESIGN).
 *
 * Astra-style top tab switcher inside Customizer sections.
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

class Hello_Movie_Engine_Customize_Section_Tabs_Control extends WP_Customize_Control {

	public $type = 'hello-movieengine-section-tabs';

	public function render_content() {
		?>
		<div class="hello-movieengine-section-tabs-wrap" data-section="<?php echo esc_attr( $this->section ); ?>">
			<div class="hello-movieengine-section-tabs-nav" role="tablist">
				<button type="button" class="hello-movieengine-section-tab-btn is-active" data-tab="general" role="tab" aria-selected="true">
					<?php esc_html_e( 'GENERAL', 'hello-movieengine' ); ?>
				</button>
				<button type="button" class="hello-movieengine-section-tab-btn" data-tab="design" role="tab" aria-selected="false">
					<?php esc_html_e( 'DESIGN', 'hello-movieengine' ); ?>
				</button>
			</div>
		</div>
		<?php
	}
}
