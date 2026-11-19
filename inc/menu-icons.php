<?php
/**
 * Appearance → Menus: rail icon picker (SVG library + custom class / upload).
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HELLO_MOVIEENGINE_MENU_ICON', '_hello_movieengine_menu_icon' );
define( 'HELLO_MOVIEENGINE_MENU_CUSTOM_ICON', '_hello_movieengine_menu_custom_icon' );

/**
 * @param int $item_id Menu item ID.
 * @return array{icon:string, custom_icon:string}
 */
function hello_movieengine_get_menu_icon_meta( $item_id ) {
	return array(
		'icon'        => (string) get_post_meta( $item_id, HELLO_MOVIEENGINE_MENU_ICON, true ),
		'custom_icon' => (string) get_post_meta( $item_id, HELLO_MOVIEENGINE_MENU_CUSTOM_ICON, true ),
	);
}

/**
 * Render icon for a nav menu item (side rail).
 *
 * @param WP_Post|int $item Menu item.
 */
function hello_movieengine_render_menu_item_icon( $item ) {
	$item_id = $item instanceof WP_Post ? (int) $item->ID : (int) $item;
	$meta    = hello_movieengine_get_menu_icon_meta( $item_id );

	$config = array(
		'icon'        => $meta['icon'],
		'custom_icon' => $meta['custom_icon'],
		'type'        => 'custom',
	);

	if ( empty( $config['icon'] ) && empty( $config['custom_icon'] ) && class_exists( 'Hello_Movie_Engine_Bottom_Nav' ) ) {
		$config['icon'] = 'svg:link';
	}

	if ( class_exists( 'Hello_Movie_Engine_Bottom_Nav' ) ) {
		Hello_Movie_Engine_Bottom_Nav::render_icon( $config );
	}
}

/**
 * Menu item icon fields (top-level only for side rail).
 *
 * @param int      $item_id Item ID.
 * @param WP_Post  $item    Item.
 * @param int      $depth   Depth.
 * @param stdClass $args    Walker args.
 * @param int      $id      Menu ID.
 */
function hello_movieengine_menu_icon_item_fields( $item_id, $item, $depth, $args, $id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	if ( $depth > 0 ) {
		return;
	}

	if ( ! class_exists( 'WP_Customize_Control' ) ) {
		require_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
	}
	$repeater_control = HELLO_MOVIEENGINE_DIR . '/inc/customizer/controls/class-hello-movieengine-nav-repeater-control.php';
	if ( file_exists( $repeater_control ) ) {
		require_once $repeater_control;
	}

	$meta      = hello_movieengine_get_menu_icon_meta( $item_id );
	$icon      = $meta['icon'];
	$custom    = $meta['custom_icon'];
	$icon_name = class_exists( 'Hello_Movie_Engine_Customize_Nav_Repeater_Control' )
		? Hello_Movie_Engine_Customize_Nav_Repeater_Control::get_icon_display_name( $icon, $custom )
		: esc_html__( 'Menu icon', 'hello-movieengine' );
	$preview   = class_exists( 'Hello_Movie_Engine_Customize_Nav_Repeater_Control' )
		? Hello_Movie_Engine_Customize_Nav_Repeater_Control::render_icon_preview_html( $icon, $custom )
		: '';
	?>
	<div class="field-hello-menu-icon description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--top hello-movieengine-menu-icon-field">
		<span class="hello-movieengine-menu-icon-field__label"><?php esc_html_e( 'Side rail icon', 'hello-movieengine' ); ?></span>
		<span class="description" style="display:block;margin:4px 0 8px;">
			<?php esc_html_e( 'Style Two left rail. Pick a thin line SVG, Font Awesome / FA Thin class, or upload custom SVG.', 'hello-movieengine' ); ?>
		</span>
		<div class="hello-movieengine-icon-field-row">
			<div class="hello-movieengine-icon-preview-box" title="<?php esc_attr_e( 'Click to change icon', 'hello-movieengine' ); ?>">
				<span class="hello-movieengine-icon-preview-render">
					<?php echo $preview; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			</div>
			<div class="hello-movieengine-icon-meta">
				<span class="hello-movieengine-icon-display-name"><?php echo esc_html( $icon_name ); ?></span>
				<button type="button" class="button button-secondary hello-movieengine-open-icon-picker">
					<span class="dashicons dashicons-format-image"></span>
					<?php esc_html_e( 'Choose Icon…', 'hello-movieengine' ); ?>
				</button>
			</div>
		</div>
		<input type="hidden" class="hello-movieengine-item-icon" name="menu-item-hm-icon[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $icon ); ?>" />
		<input type="hidden" class="hello-movieengine-item-custom-icon" name="menu-item-hm-custom-icon[<?php echo esc_attr( (string) $item_id ); ?>]" value="<?php echo esc_attr( $custom ); ?>" />
	</div>
	<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'hello_movieengine_menu_icon_item_fields', 12, 5 );

/**
 * Save menu icon meta.
 *
 * @param int $menu_id         Menu ID.
 * @param int $menu_item_db_id Item ID.
 * @param array $args          Item args.
 */
function hello_movieengine_save_menu_icon_fields( $menu_id, $menu_item_db_id, $args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	if ( ! isset( $_POST['menu-item-db-id'] ) || ! is_array( $_POST['menu-item-db-id'] ) ) {
		return;
	}

	$icon   = isset( $_POST['menu-item-hm-icon'][ $menu_item_db_id ] )
		? sanitize_text_field( wp_unslash( $_POST['menu-item-hm-icon'][ $menu_item_db_id ] ) )
		: '';
	$custom = isset( $_POST['menu-item-hm-custom-icon'][ $menu_item_db_id ] )
		? wp_unslash( $_POST['menu-item-hm-custom-icon'][ $menu_item_db_id ] )
		: '';

	$custom = hello_movieengine_sanitize_menu_custom_icon( $custom );

	if ( $icon ) {
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MENU_ICON, $icon );
	} else {
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MENU_ICON );
	}

	if ( $custom ) {
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MENU_CUSTOM_ICON, $custom );
	} else {
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MENU_CUSTOM_ICON );
	}
}
add_action( 'wp_update_nav_menu_item', 'hello_movieengine_save_menu_icon_fields', 12, 3 );

/**
 * Sanitize custom icon: FA class or inline SVG markup.
 *
 * @param string $raw Raw value.
 * @return string
 */
function hello_movieengine_sanitize_menu_custom_icon( $raw ) {
	$raw = trim( (string) $raw );
	if ( '' === $raw ) {
		return '';
	}

	if ( stripos( $raw, '<svg' ) !== false ) {
		$allowed = array(
			'svg'     => array(
				'viewbox'           => true,
				'xmlns'             => true,
				'width'             => true,
				'height'            => true,
				'fill'              => true,
				'stroke'            => true,
				'stroke-width'      => true,
				'stroke-linecap'    => true,
				'stroke-linejoin'   => true,
				'aria-hidden'       => true,
				'focusable'         => true,
				'class'             => true,
				'role'              => true,
			),
			'path'    => array(
				'd'                 => true,
				'fill'              => true,
				'stroke'            => true,
				'stroke-width'      => true,
				'stroke-linecap'    => true,
				'stroke-linejoin'   => true,
			),
			'circle'  => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true ),
			'rect'    => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'fill' => true, 'stroke' => true ),
			'line'    => array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true ),
			'polyline'=> array( 'points' => true, 'fill' => true, 'stroke' => true ),
			'polygon' => array( 'points' => true, 'fill' => true, 'stroke' => true ),
		);

		return wp_kses( $raw, $allowed );
	}

	return sanitize_text_field( $raw );
}

/**
 * Admin assets + shared icon modal on nav-menus.php.
 *
 * @param string $hook_suffix Hook.
 */
function hello_movieengine_menu_icon_admin_assets( $hook_suffix ) {
	if ( 'nav-menus.php' !== $hook_suffix ) {
		return;
	}

	if ( ! class_exists( 'WP_Customize_Control' ) ) {
		require_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
	}
	$repeater_control = HELLO_MOVIEENGINE_DIR . '/inc/customizer/controls/class-hello-movieengine-nav-repeater-control.php';
	if ( file_exists( $repeater_control ) ) {
		require_once $repeater_control;
	}

	$css_path = HELLO_MOVIEENGINE_DIR . '/assets/admin/css/customizer-controls.css';
	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'hello-movieengine-menu-icons',
			HELLO_MOVIEENGINE_URI . '/assets/admin/css/customizer-controls.css',
			array(),
			HELLO_MOVIEENGINE_VERSION
		);
	}

	$js_path = HELLO_MOVIEENGINE_DIR . '/assets/admin/js/nav-menu-icons.js';
	if ( file_exists( $js_path ) ) {
		wp_enqueue_media();
		wp_enqueue_script(
			'hello-movieengine-menu-icons',
			HELLO_MOVIEENGINE_URI . '/assets/admin/js/nav-menu-icons.js',
			array( 'jquery' ),
			HELLO_MOVIEENGINE_VERSION . '.' . (string) filemtime( $js_path ),
			true
		);
	}
}
add_action( 'admin_enqueue_scripts', 'hello_movieengine_menu_icon_admin_assets' );

/**
 * Bootstrap modal + icons on menu screen footer (classic + block-safe).
 */
function hello_movieengine_menu_icon_admin_footer() {
	global $pagenow;
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$on_menus = ( $screen && 'nav-menus' === $screen->id ) || 'nav-menus.php' === $pagenow;
	if ( ! $on_menus ) {
		return;
	}
	hello_movieengine_menu_icon_render_modal();
}
add_action( 'admin_footer', 'hello_movieengine_menu_icon_admin_footer' );

/**
 * Output icon picker modal once below menu editor.
 */
function hello_movieengine_menu_icon_render_modal() {
	if ( ! class_exists( 'Hello_Movie_Engine_Bottom_Nav' ) ) {
		return;
	}

	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;

	$icon_lib = Hello_Movie_Engine_Bottom_Nav::get_icon_library();
	?>
	<div class="hello-movieengine-icon-picker-modal" id="hello-movieengine-icon-picker-modal" style="display: none;">
		<div class="hello-movieengine-icon-picker-backdrop"></div>
		<div class="hello-movieengine-icon-picker-dialog">
			<div class="hello-movieengine-icon-picker-header">
				<h3>
					<span class="dashicons dashicons-grid-view"></span>
					<?php esc_html_e( 'Select Icon', 'hello-movieengine' ); ?>
					<span class="hello-movieengine-icon-total-badge"><?php echo esc_html( sprintf( __( '%d icons', 'hello-movieengine' ), count( $icon_lib ) ) ); ?></span>
				</h3>
				<button type="button" class="hello-movieengine-icon-picker-close" aria-label="<?php esc_attr_e( 'Close', 'hello-movieengine' ); ?>">&times;</button>
			</div>

			<div class="hello-movieengine-icon-picker-toolbar">
				<div class="hello-movieengine-icon-search-wrap">
					<span class="dashicons dashicons-search"></span>
					<input type="text" class="hello-movieengine-icon-search-input" placeholder="<?php esc_attr_e( 'Search icons…', 'hello-movieengine' ); ?>" />
					<button type="button" class="hello-movieengine-icon-search-clear" style="display: none;">&times;</button>
				</div>
				<div class="hello-movieengine-icon-categories">
					<button type="button" class="hello-movieengine-cat-pill is-active" data-cat="all"><?php esc_html_e( 'All', 'hello-movieengine' ); ?></button>
					<button type="button" class="hello-movieengine-cat-pill" data-cat="media"><?php esc_html_e( 'Streaming & Cinema', 'hello-movieengine' ); ?></button>
					<button type="button" class="hello-movieengine-cat-pill" data-cat="nav"><?php esc_html_e( 'Navigation', 'hello-movieengine' ); ?></button>
					<button type="button" class="hello-movieengine-cat-pill" data-cat="action"><?php esc_html_e( 'Watchlist & Actions', 'hello-movieengine' ); ?></button>
					<button type="button" class="hello-movieengine-cat-pill" data-cat="account"><?php esc_html_e( 'User & Account', 'hello-movieengine' ); ?></button>
					<button type="button" class="hello-movieengine-cat-pill" data-cat="genre"><?php esc_html_e( 'Genres & Moods', 'hello-movieengine' ); ?></button>
					<button type="button" class="hello-movieengine-cat-pill" data-cat="custom"><?php esc_html_e( 'Custom / Upload', 'hello-movieengine' ); ?></button>
				</div>
			</div>

			<div class="hello-movieengine-icon-picker-body">
				<div class="hello-movieengine-icon-grid" id="hello-movieengine-icon-grid">
					<?php foreach ( $icon_lib as $icon_key => $icon_data ) : ?>
						<button type="button" class="hello-movieengine-icon-card" data-icon="svg:<?php echo esc_attr( $icon_key ); ?>" data-name="<?php echo esc_attr( strtolower( $icon_data['name'] ) . ' ' . $icon_key ); ?>" data-cat="<?php echo esc_attr( $icon_data['category'] ); ?>" title="<?php echo esc_attr( $icon_data['name'] ); ?>">
							<span class="hello-movieengine-icon-card-preview">
								<?php echo $icon_data['svg']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
							<span class="hello-movieengine-icon-card-label"><?php echo esc_html( $icon_data['name'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>

				<div class="hello-movieengine-icon-custom-panel" style="display: none;">
					<div class="hello-movieengine-custom-panel-inner">
						<h4><?php esc_html_e( 'Font Awesome / thin icon class', 'hello-movieengine' ); ?></h4>
						<p class="description"><?php esc_html_e( 'Examples: fa-solid fa-film, fa-regular fa-user, fa-thin fa-house', 'hello-movieengine' ); ?></p>
						<div class="hello-movieengine-custom-input-group">
							<input type="text" class="hello-movieengine-custom-class-field" placeholder="fa-thin fa-clapperboard" />
							<button type="button" class="button button-primary hello-movieengine-apply-custom-icon"><?php esc_html_e( 'Set Icon Class', 'hello-movieengine' ); ?></button>
						</div>

						<h4 style="margin-top:1.25em;"><?php esc_html_e( 'Upload custom SVG', 'hello-movieengine' ); ?></h4>
						<p class="description"><?php esc_html_e( 'Select an SVG from the Media Library or paste SVG markup below.', 'hello-movieengine' ); ?></p>
						<p>
							<button type="button" class="button hello-movieengine-upload-svg"><?php esc_html_e( 'Upload SVG…', 'hello-movieengine' ); ?></button>
						</p>
						<textarea class="hello-movieengine-custom-svg-field widefat" rows="4" placeholder="&lt;svg viewBox=&quot;0 0 24 24&quot; …&gt;"></textarea>
						<p>
							<button type="button" class="button button-primary hello-movieengine-apply-custom-svg"><?php esc_html_e( 'Set Custom SVG', 'hello-movieengine' ); ?></button>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}