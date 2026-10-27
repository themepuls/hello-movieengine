<?php
/**
 * Custom Customizer control: Mobile Bottom Navigation Repeater with Icon Picker.
 *
 * Allows adding, removing, reordering, and customizing mobile bottom nav items.
 * Includes a visual icon picker with categories, search, sleek SVG line icons and FontAwesome support.
 *
 * @package Hello_Movie Engine
 * @since 1.0.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return;
}

class Hello_Movie_Engine_Customize_Nav_Repeater_Control extends WP_Customize_Control {

	public $type = 'hello-movieengine-nav-repeater';

	/**
	 * Enqueue control scripts and styles.
	 */
	public function enqueue() {
		wp_enqueue_style(
			'hello-movieengine-customizer-controls',
			HELLO_MOVIEENGINE_URI . '/assets/css/customizer-controls.css',
			array(),
			HELLO_MOVIEENGINE_VERSION
		);

		if ( defined( 'MOVIE_ENGINE_URL' ) ) {
			wp_enqueue_style(
				'hello-movieengine-fa-admin',
				MOVIE_ENGINE_URL . 'assets/fonts/fontawesome.min.css',
				array(),
				HELLO_MOVIEENGINE_VERSION
			);
		}

		wp_enqueue_script(
			'hello-movieengine-customizer-controls',
			HELLO_MOVIEENGINE_URI . '/assets/js/customizer-controls.js',
			array( 'jquery', 'jquery-ui-sortable', 'customize-controls' ),
			HELLO_MOVIEENGINE_VERSION,
			true
		);
	}

	/**
	 * Get list of available item types.
	 *
	 * @return array
	 */
	public static function get_item_types() {
		return array(
			'home'         => esc_html__( 'Home', 'hello-movieengine' ),
			'movies'       => esc_html__( 'Movies', 'hello-movieengine' ),
			'series'       => esc_html__( 'Series', 'hello-movieengine' ),
			'watchlist'    => esc_html__( 'Watchlist', 'hello-movieengine' ),
			'account'      => esc_html__( 'Account', 'hello-movieengine' ),
			'search'       => esc_html__( 'Search', 'hello-movieengine' ),
			'genres'       => esc_html__( 'Genres', 'hello-movieengine' ),
			'subscription' => esc_html__( 'Subscription', 'hello-movieengine' ),
			'custom'       => esc_html__( 'Custom Link', 'hello-movieengine' ),
		);
	}

	/**
	 * Default sleek icons by item type.
	 *
	 * @return array
	 */
	public static function get_default_icons() {
		return array(
			'home'         => 'svg:home',
			'movies'       => 'svg:film',
			'series'       => 'svg:tv',
			'watchlist'    => 'svg:bookmark-plus',
			'account'      => 'svg:user',
			'search'       => 'svg:search',
			'genres'       => 'svg:tags',
			'subscription' => 'svg:crown',
			'custom'       => 'svg:link',
		);
	}

	/**
	 * FontAwesome curated icons for the picker (removed in favor of sleek line SVG icons).
	 *
	 * @return array
	 */
	public static function get_fa_icons() {
		return array();
	}

	/**
	 * Render HTML preview for an icon.
	 *
	 * @param string $icon Icon key or class.
	 * @param string $custom_icon Optional custom icon class.
	 * @return string HTML
	 */
	public static function render_icon_preview_html( $icon, $custom_icon = '' ) {
		$val = ! empty( $custom_icon ) ? trim( $custom_icon ) : trim( $icon );
		if ( empty( $val ) ) {
			return '<span class="dashicons dashicons-format-image"></span>';
		}
		if ( strpos( $val, 'svg:' ) === 0 || Hello_Movie_Engine_Bottom_Nav::get_registered_svg( $val ) ) {
			$svg = Hello_Movie_Engine_Bottom_Nav::get_registered_svg( $val );
			if ( $svg ) {
				return $svg;
			}
		}
		if ( strpos( $val, 'fa-' ) !== false || strpos( $val, 'fa ' ) !== false ) {
			return '<i class="' . esc_attr( $val ) . '" aria-hidden="true"></i>';
		}
		return Hello_Movie_Engine_Bottom_Nav::get_type_svg( $val );
	}

	/**
	 * Get display name for an icon.
	 *
	 * @param string $icon Icon key or class.
	 * @param string $custom_icon Optional custom icon class.
	 * @return string Human name
	 */
	public static function get_icon_display_name( $icon, $custom_icon = '' ) {
		$val = ! empty( $custom_icon ) ? trim( $custom_icon ) : trim( $icon );
		if ( empty( $val ) ) {
			return esc_html__( 'Default Icon', 'hello-movieengine' );
		}
		$clean_key = strtolower( str_replace( 'svg:', '', $val ) );
		$lib = Hello_Movie_Engine_Bottom_Nav::get_icon_library();
		if ( isset( $lib[ $clean_key ]['name'] ) ) {
			return $lib[ $clean_key ]['name'];
		}
		$fa_icons = self::get_fa_icons();
		if ( isset( $fa_icons[ $val ] ) ) {
			return $fa_icons[ $val ];
		}
		return $val;
	}

	/**
	 * Render control content.
	 */
	public function render_content() {
		$value = $this->value();
		$items = json_decode( (string) $value, true );

		if ( ! is_array( $items ) || empty( $items ) ) {
			$items = Hello_Movie_Engine_Bottom_Nav::get_default_items_config();
		}

		$legacy_map = array(
			'fa-solid fa-house'            => 'svg:home',
			'fa-solid fa-film'             => 'svg:film',
			'fa-solid fa-tv'               => 'svg:tv',
			'fa-solid fa-plus'             => 'svg:bookmark-plus',
			'fa-regular fa-user'           => 'svg:user',
			'fa-solid fa-magnifying-glass' => 'svg:search',
			'fa-solid fa-tags'             => 'svg:tags',
			'fa-solid fa-crown'            => 'svg:crown',
			'fa-solid fa-link'             => 'svg:link',
		);

		foreach ( $items as $idx => $it ) {
			if ( is_array( $it ) && isset( $it['icon'] ) && isset( $legacy_map[ $it['icon'] ] ) && empty( $it['custom_icon'] ) ) {
				$items[ $idx ]['icon'] = $legacy_map[ $it['icon'] ];
			}
		}

		$pages        = get_pages( array(
			'post_status' => 'publish',
			'sort_column' => 'post_title',
			'sort_order'  => 'ASC',
		) );
		$types        = self::get_item_types();
		$icon_lib     = Hello_Movie_Engine_Bottom_Nav::get_icon_library();
		$fa_icons     = self::get_fa_icons();
		?>
		<div class="hello-movieengine-nav-repeater-control">
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<div class="hello-movieengine-nav-repeater-items" id="<?php echo esc_attr( $this->id ); ?>-items">
				<?php foreach ( $items as $index => $item ) :
					$id          = isset( $item['id'] ) ? $item['id'] : 'item_' . $index;
					$type        = isset( $item['type'] ) ? $item['type'] : 'custom';
					$label       = isset( $item['label'] ) ? $item['label'] : '';
					$icon        = isset( $item['icon'] ) ? $item['icon'] : ( isset( self::get_default_icons()[ $type ] ) ? self::get_default_icons()[ $type ] : 'svg:link' );
					$custom_icon = isset( $item['custom_icon'] ) ? $item['custom_icon'] : '';
					$url         = isset( $item['url'] ) ? $item['url'] : '';
					$page_id     = isset( $item['page_id'] ) ? absint( $item['page_id'] ) : 0;
					$visibility  = isset( $item['visibility'] ) ? $item['visibility'] : 'all';
					$enabled     = ! isset( $item['enabled'] ) || ! empty( $item['enabled'] );

					$display_title = ! empty( $label ) ? $label : ( isset( $types[ $type ] ) ? $types[ $type ] : ucfirst( $type ) );
					$icon_name     = self::get_icon_display_name( $icon, $custom_icon );
				?>
					<div class="hello-movieengine-repeater-row" data-id="<?php echo esc_attr( $id ); ?>">
						<div class="hello-movieengine-repeater-header">
							<span class="hello-movieengine-repeater-drag" title="<?php esc_attr_e( 'Drag to reorder', 'hello-movieengine' ); ?>">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="5" r="2"/><circle cx="9" cy="12" r="2"/><circle cx="9" cy="19" r="2"/><circle cx="15" cy="5" r="2"/><circle cx="15" cy="12" r="2"/><circle cx="15" cy="19" r="2"/></svg>
							</span>
							<span class="hello-movieengine-repeater-title">
								<span class="hello-movieengine-repeater-type-badge"><?php echo esc_html( isset( $types[ $type ] ) ? $types[ $type ] : $type ); ?></span>
								<strong class="hello-movieengine-repeater-title-text"><?php echo esc_html( $display_title ); ?></strong>
							</span>
							<div class="hello-movieengine-repeater-actions">
								<button type="button" class="hello-movieengine-repeater-toggle-edit" title="<?php esc_attr_e( 'Edit item', 'hello-movieengine' ); ?>">
									<span class="dashicons dashicons-arrow-down-alt2"></span>
								</button>
								<button type="button" class="hello-movieengine-repeater-delete" title="<?php esc_attr_e( 'Remove item', 'hello-movieengine' ); ?>">
									<span class="dashicons dashicons-trash"></span>
								</button>
							</div>
						</div>

						<div class="hello-movieengine-repeater-body" style="display: none;">
							<div class="hello-movieengine-repeater-field">
								<label><?php esc_html_e( 'Item Type', 'hello-movieengine' ); ?></label>
								<select class="hello-movieengine-item-type">
									<?php foreach ( $types as $type_key => $type_label ) : ?>
										<option value="<?php echo esc_attr( $type_key ); ?>" <?php selected( $type, $type_key ); ?>>
											<?php echo esc_html( $type_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="hello-movieengine-repeater-field">
								<label><?php esc_html_e( 'Custom Label', 'hello-movieengine' ); ?></label>
								<input type="text" class="hello-movieengine-item-label" value="<?php echo esc_attr( $label ); ?>" placeholder="<?php esc_attr_e( 'Default is based on type', 'hello-movieengine' ); ?>" />
							</div>

							<!-- Visual Icon Picker Field -->
							<div class="hello-movieengine-repeater-field hello-movieengine-repeater-field--icon">
								<label><?php esc_html_e( 'Item Icon', 'hello-movieengine' ); ?></label>
								<div class="hello-movieengine-icon-field-row">
									<div class="hello-movieengine-icon-preview-box" title="<?php esc_attr_e( 'Click to change icon', 'hello-movieengine' ); ?>">
										<span class="hello-movieengine-icon-preview-render">
											<?php echo self::render_icon_preview_html( $icon, $custom_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
								<input type="hidden" class="hello-movieengine-item-icon" value="<?php echo esc_attr( $icon ); ?>" />
								<input type="hidden" class="hello-movieengine-item-custom-icon" value="<?php echo esc_attr( $custom_icon ); ?>" />
							</div>

							<div class="hello-movieengine-repeater-field">
								<label><?php esc_html_e( 'Page Override (Optional)', 'hello-movieengine' ); ?></label>
								<select class="hello-movieengine-item-page">
									<option value="0"><?php esc_html_e( '— Use Default Resolution —', 'hello-movieengine' ); ?></option>
									<?php foreach ( $pages as $p ) : ?>
										<option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( $page_id, $p->ID ); ?>>
											<?php echo esc_html( $p->post_title ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="hello-movieengine-repeater-field">
								<label><?php esc_html_e( 'Custom URL (Optional)', 'hello-movieengine' ); ?></label>
								<input type="text" class="hello-movieengine-item-url" value="<?php echo esc_attr( $url ); ?>" placeholder="https://... <?php esc_attr_e( 'or /custom-link', 'hello-movieengine' ); ?>" />
							</div>

							<div class="hello-movieengine-repeater-field">
								<label><?php esc_html_e( 'Visibility', 'hello-movieengine' ); ?></label>
								<select class="hello-movieengine-item-visibility">
									<option value="all" <?php selected( $visibility, 'all' ); ?>><?php esc_html_e( 'Everyone', 'hello-movieengine' ); ?></option>
									<option value="logged_in" <?php selected( $visibility, 'logged_in' ); ?>><?php esc_html_e( 'Logged In Only', 'hello-movieengine' ); ?></option>
									<option value="logged_out" <?php selected( $visibility, 'logged_out' ); ?>><?php esc_html_e( 'Logged Out Only', 'hello-movieengine' ); ?></option>
								</select>
							</div>

							<div class="hello-movieengine-repeater-field hello-movieengine-repeater-field--checkbox">
								<label>
									<input type="checkbox" class="hello-movieengine-item-enabled" value="1" <?php checked( $enabled ); ?> />
									<?php esc_html_e( 'Enable this menu item', 'hello-movieengine' ); ?>
								</label>
							</div>

							<div class="hello-movieengine-repeater-item-footer-actions">
								<div class="hello-movieengine-move-btn-group">
									<button type="button" class="button-link hello-movieengine-repeater-move-up">
										<span class="dashicons dashicons-arrow-up-alt2"></span> <?php esc_html_e( 'Move Up', 'hello-movieengine' ); ?>
									</button>
									<span class="hello-movieengine-action-divider">|</span>
									<button type="button" class="button-link hello-movieengine-repeater-move-down">
										<span class="dashicons dashicons-arrow-down-alt2"></span> <?php esc_html_e( 'Move Down', 'hello-movieengine' ); ?>
									</button>
								</div>
								<button type="button" class="button-link button-link-delete hello-movieengine-repeater-delete">
									<?php esc_html_e( 'Remove Item', 'hello-movieengine' ); ?>
								</button>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="hello-movieengine-repeater-footer">
				<button type="button" class="button button-secondary hello-movieengine-repeater-add">
					<span class="dashicons dashicons-plus-alt2"></span>
					<?php esc_html_e( 'Add Menu Item', 'hello-movieengine' ); ?>
				</button>
			</div>

			<input type="hidden" class="hello-movieengine-nav-repeater-value" value="<?php echo esc_attr( wp_json_encode( $items ) ); ?>" <?php $this->link(); ?> />

			<!-- Reusable Visual Icon Picker Modal -->
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
							<input type="text" class="hello-movieengine-icon-search-input" placeholder="<?php esc_attr_e( 'Search modern streaming, media, watchlist & UI icons...', 'hello-movieengine' ); ?>" />
							<button type="button" class="hello-movieengine-icon-search-clear" style="display: none;">&times;</button>
						</div>
						<div class="hello-movieengine-icon-categories">
							<button type="button" class="hello-movieengine-cat-pill is-active" data-cat="all"><?php esc_html_e( 'All', 'hello-movieengine' ); ?></button>
							<button type="button" class="hello-movieengine-cat-pill" data-cat="media"><?php esc_html_e( 'Streaming & Cinema', 'hello-movieengine' ); ?></button>
							<button type="button" class="hello-movieengine-cat-pill" data-cat="nav"><?php esc_html_e( 'Navigation', 'hello-movieengine' ); ?></button>
							<button type="button" class="hello-movieengine-cat-pill" data-cat="action"><?php esc_html_e( 'Watchlist & Actions', 'hello-movieengine' ); ?></button>
							<button type="button" class="hello-movieengine-cat-pill" data-cat="account"><?php esc_html_e( 'User & Account', 'hello-movieengine' ); ?></button>
							<button type="button" class="hello-movieengine-cat-pill" data-cat="genre"><?php esc_html_e( 'Genres & Moods', 'hello-movieengine' ); ?></button>
							<button type="button" class="hello-movieengine-cat-pill" data-cat="custom"><?php esc_html_e( 'Custom Class', 'hello-movieengine' ); ?></button>
						</div>
					</div>

					<div class="hello-movieengine-icon-picker-body">
						<!-- Sleek Open-Source Line Icons (SVG) -->
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

						<!-- Custom Class Panel -->
						<div class="hello-movieengine-icon-custom-panel" style="display: none;">
							<div class="hello-movieengine-custom-panel-inner">
								<h4><?php esc_html_e( 'Use Custom Icon Class', 'hello-movieengine' ); ?></h4>
								<p class="description"><?php esc_html_e( 'Enter any FontAwesome 6 class name (e.g. fa-solid fa-sparkles, fa-regular fa-bell) or custom class:', 'hello-movieengine' ); ?></p>
								<div class="hello-movieengine-custom-input-group">
									<input type="text" class="hello-movieengine-custom-class-field" placeholder="fa-solid fa-bolt" />
									<button type="button" class="button button-primary hello-movieengine-apply-custom-icon"><?php esc_html_e( 'Set Custom Icon', 'hello-movieengine' ); ?></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
