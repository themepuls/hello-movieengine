<?php
/**
 * Template part for displaying the mobile bottom navigation bar.
 *
 * @package Hello_Movie Engine
 * @since 1.0.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Hello_Movie_Engine_Bottom_Nav' ) || ! Hello_Movie_Engine_Bottom_Nav::is_enabled() ) {
	return;
}

$nav_items = Hello_Movie_Engine_Bottom_Nav::get_nav_items();
if ( empty( $nav_items ) ) {
	return;
}

$breakpoint  = Hello_Movie_Engine_Bottom_Nav::get_breakpoint();
$show_labels = Hello_Movie_Engine_Bottom_Nav::show_labels();
$classes     = array(
	'hello-movieengine-bottom-nav',
	'hello-movieengine-bottom-nav--' . $breakpoint,
	$show_labels ? 'has-labels' : 'no-labels',
);
?>

<nav id="hello-movieengine-bottom-nav" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'hello-movieengine' ); ?>">
	<div class="hello-movieengine-bottom-nav__container">
		<ul class="hello-movieengine-bottom-nav__items" role="menubar">
			<?php foreach ( $nav_items as $item ) :
				$is_active   = ! empty( $item['is_active'] );
				$is_button   = ! empty( $item['is_button'] );
				$item_type   = isset( $item['type'] ) ? $item['type'] : 'custom';
				$label       = isset( $item['final_label'] ) ? $item['final_label'] : '';
				$url         = isset( $item['resolved_url'] ) ? $item['resolved_url'] : '#';
				$item_class  = 'hello-movieengine-bottom-nav__item hello-movieengine-bottom-nav__item--' . sanitize_html_class( $item_type );
				if ( $is_active ) {
					$item_class .= ' is-active';
				}
			?>
				<li class="<?php echo esc_attr( $item_class ); ?>" role="none">
					<?php if ( $is_button ) : ?>
						<button type="button" class="hello-movieengine-bottom-nav__btn hello-movieengine-bottom-nav__btn--<?php echo sanitize_html_class( $item_type ); ?>" data-type="<?php echo esc_attr( $item_type ); ?>" aria-label="<?php echo esc_attr( $label ); ?>" role="menuitem">
							<span class="hello-movieengine-bottom-nav__icon-wrap" aria-hidden="true">
								<?php Hello_Movie_Engine_Bottom_Nav::render_icon( $item ); ?>
							</span>
							<?php if ( $show_labels ) : ?>
								<span class="hello-movieengine-bottom-nav__label"><?php echo esc_html( $label ); ?></span>
							<?php else : ?>
								<span class="screen-reader-text"><?php echo esc_html( $label ); ?></span>
							<?php endif; ?>
						</button>
					<?php else : ?>
						<a href="<?php echo esc_url( $url ); ?>" class="hello-movieengine-bottom-nav__link" <?php if ( $is_active ) echo 'aria-current="page"'; ?> role="menuitem">
							<span class="hello-movieengine-bottom-nav__icon-wrap" aria-hidden="true">
								<?php Hello_Movie_Engine_Bottom_Nav::render_icon( $item ); ?>
							</span>
							<?php if ( $show_labels ) : ?>
								<span class="hello-movieengine-bottom-nav__label"><?php echo esc_html( $label ); ?></span>
							<?php else : ?>
								<span class="screen-reader-text"><?php echo esc_html( $label ); ?></span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</nav>
