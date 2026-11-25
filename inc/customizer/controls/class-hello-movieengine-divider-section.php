<?php
/**
 * Custom Customizer section: Group Heading Divider.
 *
 * Renders an unclickable category group title in the Customizer root (Blocksy style).
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Customize_Section' ) ) {
	return;
}

class Hello_Movie_Engine_Customize_Divider_Section extends WP_Customize_Section {

	public $type = 'hello-movieengine-divider';

	protected function render() {
		?>
		<li id="accordion-section-<?php echo esc_attr( $this->id ); ?>" class="hello-movieengine-customizer-group-heading">
			<span><?php echo esc_html( $this->title ); ?></span>
		</li>
		<?php
	}
}
