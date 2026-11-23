<?php
/**
 * Style Two – fixed global left navigation (logo + menu on every page).
 *
 * Overlay rail: transparent chrome, gradient scrims, labels on hover.
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! hello_movieengine_uses_side_rail() ) {
	return;
}

$labels_pinned = Hello_Movie_Engine_Side_Rail::labels_pinned();
$use_wp_menu   = Hello_Movie_Engine_Side_Rail::has_wp_menu();
$nav_items     = $use_wp_menu ? array() : Hello_Movie_Engine_Side_Rail::get_nav_items();

$rail_classes = array(
	'hello-movieengine-side-rail',
);
if ( $labels_pinned ) {
	$rail_classes[] = 'is-expanded';
}

$show_rail_logo  = (bool) get_theme_mod( 'hello_movieengine_side_rail_show_logo', true );
$custom_rail_logo = get_theme_mod( 'hello_movieengine_side_rail_custom_logo', '' );
$rail_logo_height = max( 20, min( 60, absint( get_theme_mod( 'hello_movieengine_side_rail_logo_height', 34 ) ) ) );
?>
<aside id="hello-movieengine-side-rail" class="<?php echo esc_attr( implode( ' ', $rail_classes ) ); ?>" role="navigation" aria-label="<?php esc_attr_e( 'Global navigation', 'hello-movieengine' ); ?>" style="--hm-side-rail-logo-h: <?php echo esc_attr( $rail_logo_height ); ?>px;">
	<div class="hello-movieengine-side-rail__sh1" aria-hidden="true"></div>
	<div class="hello-movieengine-side-rail__sh2" aria-hidden="true"></div>

	<?php if ( $show_rail_logo ) : ?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hello-movieengine-side-rail__logo" rel="home">
			<?php
			if ( ! empty( $custom_rail_logo ) ) :
				?>
				<img
					class="hello-movieengine-side-rail__custom-logo"
					src="<?php echo esc_url( $custom_rail_logo ); ?>"
					alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"
					loading="eager"
					decoding="async"
				/>
			<?php
			elseif ( has_custom_logo() ) :
				the_custom_logo();
			else :
				$site_name = get_bloginfo( 'name', 'display' );
				if ( function_exists( 'mb_substr' ) ) {
					$initial = $site_name ? mb_substr( $site_name, 0, 1 ) : 'M';
				} else {
					$initial = $site_name ? substr( $site_name, 0, 1 ) : 'M';
				}
				?>
				<i class="hello-movieengine-side-rail__logo-mark" aria-hidden="true"><?php echo esc_html( strtoupper( $initial ) ); ?></i>
				<span class="screen-reader-text"><?php echo esc_html( $site_name ); ?></span>
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<nav class="hello-movieengine-side-rail__nav" aria-label="<?php esc_attr_e( 'Primary', 'hello-movieengine' ); ?>">
		<?php
		if ( $use_wp_menu ) {
			wp_nav_menu(
				array(
					'theme_location' => Hello_Movie_Engine_Side_Rail::get_menu_location(),
					'container'      => false,
					'items_wrap'     => '%3$s',
					'depth'          => 3,
					'fallback_cb'    => false,
					'walker'         => new Hello_Movie_Engine_Side_Rail_Walker(),
				)
			);
		} elseif ( ! empty( $nav_items ) ) {
			$index = 0;
			foreach ( $nav_items as $item ) :
				$is_active = ! empty( $item['is_active'] );
				$is_button = ! empty( $item['is_button'] );
				$item_type = isset( $item['type'] ) ? $item['type'] : 'custom';
				$label     = isset( $item['final_label'] ) ? $item['final_label'] : '';
				$url       = isset( $item['resolved_url'] ) ? $item['resolved_url'] : '#';
				$item_cl   = 'hello-movieengine-side-rail__it hello-movieengine-side-rail__it--' . sanitize_html_class( $item_type );
				if ( $is_active ) {
					$item_cl .= ' is-active';
				}
				?>
				<div class="hello-movieengine-side-rail__grp" style="<?php echo esc_attr( '--i:' . (int) $index ); ?>">
					<?php if ( $is_button ) : ?>
						<button type="button" class="<?php echo esc_attr( $item_cl ); ?> hello-movieengine-side-rail__btn hello-movieengine-side-rail__btn--<?php echo sanitize_html_class( $item_type ); ?>" data-type="<?php echo esc_attr( $item_type ); ?>" data-url="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $label ); ?>">
							<span class="hello-movieengine-side-rail__bar" aria-hidden="true"></span>
							<span class="hello-movieengine-side-rail__icon" aria-hidden="true">
								<?php Hello_Movie_Engine_Bottom_Nav::render_icon( $item ); ?>
							</span>
							<b class="hello-movieengine-side-rail__label"><?php echo esc_html( $label ); ?></b>
						</button>
					<?php else : ?>
						<a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( $item_cl ); ?>" <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
							<span class="hello-movieengine-side-rail__bar" aria-hidden="true"></span>
							<span class="hello-movieengine-side-rail__icon" aria-hidden="true">
								<?php Hello_Movie_Engine_Bottom_Nav::render_icon( $item ); ?>
							</span>
							<b class="hello-movieengine-side-rail__label"><?php echo esc_html( $label ); ?></b>
						</a>
					<?php endif; ?>
				</div>
				<?php
				++$index;
			endforeach;
		}
		?>
	</nav>
</aside>