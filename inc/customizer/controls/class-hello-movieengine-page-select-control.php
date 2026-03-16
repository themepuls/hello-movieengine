<?php
/**
 * Custom Customizer control: Page multi-select.
 *
 * Renders a searchable, scrollable checkbox list of all published pages.
 * Value is stored as a JSON-encoded array of page IDs.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hello_Movie_Engine_Customize_Page_Select_Control extends WP_Customize_Control {

	public $type = 'hello-movieengine-page-select';

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

		$pages = get_pages( array(
			'post_status' => 'publish',
			'sort_column' => 'post_title',
			'sort_order'  => 'ASC',
		) );
		?>
		<div class="hello-movieengine-page-select-control">
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<input type="text" class="hello-movieengine-page-select-search" placeholder="<?php esc_attr_e( 'Search pages…', 'hello-movieengine' ); ?>" />

			<div class="hello-movieengine-page-select-list">
				<?php if ( ! empty( $pages ) ) : ?>
					<?php foreach ( $pages as $page ) : ?>
						<label class="hello-movieengine-page-select-item" data-title="<?php echo esc_attr( strtolower( $page->post_title ) ); ?>">
							<input type="checkbox" value="<?php echo esc_attr( $page->ID ); ?>"
								<?php checked( in_array( $page->ID, $selected, false ) ); ?> />
							<span class="hello-movieengine-page-select-label"><?php echo esc_html( $page->post_title ); ?></span>
						</label>
					<?php endforeach; ?>
				<?php else : ?>
					<p class="hello-movieengine-page-select-empty"><?php esc_html_e( 'No pages found.', 'hello-movieengine' ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $selected ) ) : ?>
				<span class="hello-movieengine-page-select-count">
					<?php printf( esc_html__( '%d selected', 'hello-movieengine' ), count( $selected ) ); ?>
				</span>
			<?php else : ?>
				<span class="hello-movieengine-page-select-count"></span>
			<?php endif; ?>

			<input type="hidden" class="hello-movieengine-page-select-value" value="<?php echo esc_attr( $value ); ?>" <?php $this->link(); ?> />
		</div>
		<?php
	}
}
