<?php
/**
 * Mega Menu – Appearance → Menus item fields.
 *
 * Adds Enable Mega Menu + Columns (and Column heading for depth-1 items).
 * Settings sync to CSS classes used by header.css on the front end.
 *
 * @package Hello_Movie Engine
 * @since 1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meta keys.
 */
define( 'HELLO_MOVIEENGINE_MEGA_ENABLE', '_hello_movieengine_mega_enable' );
define( 'HELLO_MOVIEENGINE_MEGA_COLUMNS', '_hello_movieengine_mega_columns' );
define( 'HELLO_MOVIEENGINE_MEGA_HEADING', '_hello_movieengine_mega_heading' );

/**
 * Allowed column counts (2–8).
 *
 * @return int[]
 */
function hello_movieengine_mega_allowed_columns() {
	return range( 2, 8 );
}

/**
 * Render custom fields inside each menu item accordion.
 *
 * @param int      $item_id Menu item ID.
 * @param WP_Post  $item    Menu item object.
 * @param int      $depth   Depth of menu item.
 * @param stdClass $args    Menu item args.
 * @param int      $id      Nav menu ID.
 */
function hello_movieengine_nav_menu_item_fields( $item_id, $item, $depth, $args, $id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	$enabled  = (bool) get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_ENABLE, true );
	$columns  = absint( get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_COLUMNS, true ) );
	$heading  = (bool) get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_HEADING, true );

	/* Fallback: detect from existing CSS classes (manual / older setup). */
	$classes = is_array( $item->classes ) ? $item->classes : array();
	if ( ! $enabled && in_array( 'mega-menu', $classes, true ) ) {
		$enabled = true;
	}
	if ( ! $heading && in_array( 'mega-heading', $classes, true ) ) {
		$heading = true;
	}
	if ( ! in_array( $columns, hello_movieengine_mega_allowed_columns(), true ) ) {
		foreach ( hello_movieengine_mega_allowed_columns() as $n ) {
			if ( in_array( 'mega-columns-' . $n, $classes, true ) ) {
				$columns = $n;
				break;
			}
		}
	}
	if ( ! in_array( $columns, hello_movieengine_mega_allowed_columns(), true ) ) {
		$columns = 3;
	}

	wp_nonce_field( 'hello_movieengine_mega_menu', 'hello_movieengine_mega_menu_nonce_' . $item_id );
	?>
	<p class="field-mega-enable description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--top">
		<label for="edit-menu-item-mega-enable-<?php echo esc_attr( (string) $item_id ); ?>">
			<input
				type="checkbox"
				id="edit-menu-item-mega-enable-<?php echo esc_attr( (string) $item_id ); ?>"
				class="hello-movieengine-mega-enable"
				name="menu-item-mega-enable[<?php echo esc_attr( (string) $item_id ); ?>]"
				value="1"
				<?php checked( $enabled ); ?>
			/>
			<?php esc_html_e( 'Enable Mega Menu', 'hello-movieengine' ); ?>
		</label>
		<span class="description" style="display:block;margin-top:4px;">
			<?php esc_html_e( 'Top-level items only. Turns the submenu into a multi-column panel on desktop.', 'hello-movieengine' ); ?>
		</span>
	</p>

	<p class="field-mega-columns description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--top hello-movieengine-mega-columns-wrap"<?php echo $enabled ? '' : ' style="display:none;"'; ?>>
		<label for="edit-menu-item-mega-columns-<?php echo esc_attr( (string) $item_id ); ?>">
			<?php esc_html_e( 'Mega Menu Columns', 'hello-movieengine' ); ?><br />
			<select
				id="edit-menu-item-mega-columns-<?php echo esc_attr( (string) $item_id ); ?>"
				name="menu-item-mega-columns[<?php echo esc_attr( (string) $item_id ); ?>]"
			>
				<?php foreach ( hello_movieengine_mega_allowed_columns() as $n ) : ?>
					<option value="<?php echo esc_attr( (string) $n ); ?>" <?php selected( $columns, $n ); ?>>
						<?php
						/* translators: %d: number of columns */
						echo esc_html( sprintf( _n( '%d column', '%d columns', $n, 'hello-movieengine' ), $n ) );
						?>
					</option>
				<?php endforeach; ?>
			</select>
		</label>
	</p>

	<p class="field-mega-heading description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--child">
		<label for="edit-menu-item-mega-heading-<?php echo esc_attr( (string) $item_id ); ?>">
			<input
				type="checkbox"
				id="edit-menu-item-mega-heading-<?php echo esc_attr( (string) $item_id ); ?>"
				name="menu-item-mega-heading[<?php echo esc_attr( (string) $item_id ); ?>]"
				value="1"
				<?php checked( $heading ); ?>
			/>
			<?php esc_html_e( 'Mega column heading', 'hello-movieengine' ); ?>
		</label>
		<span class="description" style="display:block;margin-top:4px;">
			<?php esc_html_e( 'For items nested under a mega menu parent. Styles this link as a column title.', 'hello-movieengine' ); ?>
		</span>
	</p>
	<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'hello_movieengine_nav_menu_item_fields', 10, 5 );

/**
 * Save mega menu fields and sync CSS classes on the menu item.
 *
 * @param int   $menu_id         Nav menu ID.
 * @param int   $menu_item_db_id Menu item post ID.
 * @param array $args            Menu item data.
 */
function hello_movieengine_save_nav_menu_item_fields( $menu_id, $menu_item_db_id, $args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	/* Only run on full menu form save from Appearance → Menus. */
	if ( ! isset( $_POST['menu-item-db-id'] ) || ! is_array( $_POST['menu-item-db-id'] ) ) {
		return;
	}

	$nonce_key = 'hello_movieengine_mega_menu_nonce_' . $menu_item_db_id;
	if ( ! empty( $_POST[ $nonce_key ] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_key ] ) ), 'hello_movieengine_mega_menu' ) ) {
		return;
	}

	$enabled = ! empty( $_POST['menu-item-mega-enable'][ $menu_item_db_id ] );
	$heading = ! empty( $_POST['menu-item-mega-heading'][ $menu_item_db_id ] );
	$columns = isset( $_POST['menu-item-mega-columns'][ $menu_item_db_id ] )
		? absint( wp_unslash( $_POST['menu-item-mega-columns'][ $menu_item_db_id ] ) )
		: 3;

	if ( ! in_array( $columns, hello_movieengine_mega_allowed_columns(), true ) ) {
		$columns = 3;
	}

	if ( $enabled ) {
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_ENABLE, '1' );
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_COLUMNS, (string) $columns );
	} else {
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_ENABLE );
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_COLUMNS );
	}

	if ( $heading ) {
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_HEADING, '1' );
	} else {
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_HEADING );
	}

	hello_movieengine_sync_mega_menu_classes( $menu_item_db_id, $enabled, $columns, $heading );
}
add_action( 'wp_update_nav_menu_item', 'hello_movieengine_save_nav_menu_item_fields', 10, 3 );

/**
 * Keep menu item CSS classes in sync with mega settings.
 *
 * @param int  $menu_item_db_id Menu item ID.
 * @param bool $enabled         Mega enabled.
 * @param int  $columns         Column count.
 * @param bool $heading         Column heading.
 */
function hello_movieengine_sync_mega_menu_classes( $menu_item_db_id, $enabled, $columns, $heading ) {
	$classes = get_post_meta( $menu_item_db_id, '_menu_item_classes', true );
	if ( ! is_array( $classes ) ) {
		$classes = array();
	}

	$strip = array( 'mega-menu', 'mega-heading' );
	foreach ( hello_movieengine_mega_allowed_columns() as $n ) {
		$strip[] = 'mega-columns-' . $n;
	}
	$classes = array_values( array_diff( $classes, $strip ) );

	if ( $enabled ) {
		$classes[] = 'mega-menu';
		$classes[] = 'mega-columns-' . absint( $columns );
	}

	if ( $heading ) {
		$classes[] = 'mega-heading';
	}

	$classes = array_values( array_unique( array_filter( array_map( 'sanitize_html_class', $classes ) ) ) );
	update_post_meta( $menu_item_db_id, '_menu_item_classes', $classes );
}

/**
 * Admin assets for nav-menus.php.
 *
 * @param string $hook_suffix Current admin page.
 */
function hello_movieengine_mega_menu_admin_assets( $hook_suffix ) {
	if ( 'nav-menus.php' !== $hook_suffix ) {
		return;
	}

	$css = '
		.hello-movieengine-mega-field { margin: 8px 0 0; }
		.menu-item:not(.menu-item-depth-0) .hello-movieengine-mega-field--top { display: none !important; }
		.menu-item-depth-0 .hello-movieengine-mega-field--child { display: none !important; }
	';

	wp_register_style( 'hello-movieengine-mega-menu-admin', false, array(), HELLO_MOVIEENGINE_VERSION );
	wp_enqueue_style( 'hello-movieengine-mega-menu-admin' );
	wp_add_inline_style( 'hello-movieengine-mega-menu-admin', $css );

	$js = <<<'JS'
(function ($) {
	function toggleColumns($item) {
		var $enable = $item.find('.hello-movieengine-mega-enable');
		var $wrap = $item.find('.hello-movieengine-mega-columns-wrap');
		if (!$enable.length || !$wrap.length) return;
		$wrap.toggle($enable.is(':checked'));
	}

	$(document).on('change', '.hello-movieengine-mega-enable', function () {
		toggleColumns($(this).closest('.menu-item'));
	});

	$(function () {
		$('#menu-to-edit .menu-item').each(function () {
			toggleColumns($(this));
		});
	});

	$(document).on('menu-item-added', function (e, $menuItem) {
		if ($menuItem && $menuItem.length) {
			toggleColumns($menuItem);
		}
	});
})(jQuery);
JS;

	wp_add_inline_script( 'nav-menu', $js );
}
add_action( 'admin_enqueue_scripts', 'hello_movieengine_mega_menu_admin_assets' );
