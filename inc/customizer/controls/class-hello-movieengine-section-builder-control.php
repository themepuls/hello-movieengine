<?php
/**
 * Customizer Control: Homepage Drag & Drop Section Builder.
 *
 * Allows users to visually add, remove, reorder, toggle visibility,
 * and configure sections with comprehensive parameter controls for
 * all MovieEngine shortcodes.
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

class Hello_Movie_Engine_Customize_Section_Builder_Control extends WP_Customize_Control {

	public $type = 'hello-movieengine-section-builder';

	/**
	 * Available section types.
	 *
	 * @return array
	 */
	public static function get_section_types() {
		return array(
			'hero'         => esc_html__( 'Hero Billboard / Banner', 'hello-movieengine' ),
			'library'      => esc_html__( 'Movie / Series Carousel (Slider)', 'hello-movieengine' ),
			'top10'        => esc_html__( 'Top 10 (Rank Numbers)', 'hello-movieengine' ),
			'person_list'  => esc_html__( 'Cast & Stars (Celebrities)', 'hello-movieengine' ),
			'studios'      => esc_html__( 'Studio / Brand Chips', 'hello-movieengine' ),
			'genres'       => esc_html__( 'Genre Category Bar', 'hello-movieengine' ),
			'history'      => esc_html__( 'Continue Watching (User History)', 'hello-movieengine' ),
			'years'        => esc_html__( 'Release Years Filter', 'hello-movieengine' ),
			'page_content' => esc_html__( 'Page Content (From Editor)', 'hello-movieengine' ),
			'shortcode'    => esc_html__( 'Custom Shortcode', 'hello-movieengine' ),
		);
	}

	/**
	 * Allowed TMDb list keys for library / hero builder rows.
	 *
	 * @return string[]
	 */
	public static function get_tmdb_type_options() {
		return array( 'day', 'week', 'now_playing', 'popular', 'top_rated', 'upcoming' );
	}

	/**
	 * Map saved section row to movie_engine_library type + tmdb_type shortcode fragment.
	 *
	 * @param array $section Saved JSON section.
	 * @return array{type: string, tmdb_attr: string}
	 */
	public static function library_query_shortcode_parts( $section ) {
		$section   = is_array( $section ) ? $section : array();
		$ids       = trim( $section['ids'] ?? '' );
		$query_type = $section['query_type'] ?? 'trending';

		if ( $ids !== '' ) {
			return array(
				'type'      => $query_type,
				'tmdb_attr' => '',
			);
		}

		$source = isset( $section['query_source'] ) ? (string) $section['query_source'] : 'site';
		if ( 'tmdb' !== $source ) {
			return array(
				'type'      => $query_type,
				'tmdb_attr' => '',
			);
		}

		$tmdb_type = isset( $section['tmdb_type'] ) ? sanitize_key( $section['tmdb_type'] ) : '';
		if ( ! in_array( $tmdb_type, self::get_tmdb_type_options(), true ) ) {
			$tmdb_type = 'day';
		}

		return array(
			'type'      => '',
			'tmdb_attr' => sprintf( ' tmdb_type="%s"', esc_attr( $tmdb_type ) ),
		);
	}

	/**
	 * Hero row query: site preset type or TMDb list.
	 *
	 * @param array $section Saved JSON section.
	 * @return array{type: string, extra: string}
	 */
	public static function hero_query_shortcode_parts( $section ) {
		$section = is_array( $section ) ? $section : array();
		$ids     = trim( $section['hero_ids'] ?? '' );

		if ( $ids !== '' ) {
			return array(
				'type'  => $section['hero_type'] ?? 'trending',
				'extra' => '',
			);
		}

		$source = isset( $section['hero_query_source'] ) ? (string) $section['hero_query_source'] : 'site';
		if ( 'tmdb' !== $source ) {
			return array(
				'type'  => $section['hero_type'] ?? 'trending',
				'extra' => '',
			);
		}

		$tmdb_type = isset( $section['hero_tmdb_type'] ) ? sanitize_key( $section['hero_tmdb_type'] ) : '';
		if ( ! in_array( $tmdb_type, self::get_tmdb_type_options(), true ) ) {
			$tmdb_type = 'day';
		}

		return array(
			'type'  => 'trending',
			'extra' => sprintf( ' tmdb_type="%s"', esc_attr( $tmdb_type ) ),
		);
	}

	/**
	 * Default OTT Homepage Sections.
	 *
	 * @return array
	 */
	public static function get_default_sections() {
		return array(
			array(
				'id'          => 'hero_billboard',
				'type'        => 'hero',
				'enabled'     => 1,
				'title'       => esc_html__( 'Featured Spotlight', 'hello-movieengine' ),
				'hero_style'  => 'style1',
				'hero_type'   => 'trending',
				'hero_height' => '70vh',
				'hero_limit'  => 5,
				'autoplay'    => 1,
				'hero_speed'  => 5000,
				'hero_loop'   => 1,
				'hero_nav'    => 1,
				'hero_dots'   => 1,
				'show_imdb'   => 1,
				'show_desc'   => 1,
				'show_trailer'=> 1,
				'show_watchlist' => 1,
			),
			array(
				'id'          => 'studio_chips',
				'type'        => 'studios',
				'enabled'     => 1,
				'title'       => esc_html__( 'Popular Studios', 'hello-movieengine' ),
				'studio_layout'  => 'grid',
				'studio_columns' => 6,
				'studio_limit'   => 12,
			),
			array(
				'id'          => 'trending_movies',
				'type'        => 'library',
				'enabled'     => 1,
				'title'       => esc_html__( 'Trending Movies', 'hello-movieengine' ),
				'query_type'  => 'trending',
				'post_type'   => 'movie_engine_movie',
				'count'       => 12,
				'columns'     => 6,
				'slider'      => 1,
				'show_view_all' => 1,
			),
			array(
				'id'           => 'top10_movies',
				'type'         => 'top10',
				'enabled'      => 1,
				'title'        => esc_html__( 'Top 10 in Movies Today', 'hello-movieengine' ),
				'post_type'    => 'movie_engine_movie',
				'query_source' => 'tmdb',
				'tmdb_type'    => 'day',
				'count'        => 10,
				'slider'       => 1,
			),
			array(
				'id'          => 'popular_series',
				'type'        => 'library',
				'enabled'     => 1,
				'title'       => esc_html__( 'Trending TV Shows', 'hello-movieengine' ),
				'query_type'  => 'trending',
				'post_type'   => 'movie_engine_series',
				'count'       => 12,
				'columns'     => 6,
				'slider'      => 1,
				'show_view_all' => 1,
			),
			array(
				'id'           => 'top10_series',
				'type'         => 'top10',
				'enabled'      => 1,
				'title'        => esc_html__( 'Top 10 in TV Today', 'hello-movieengine' ),
				'post_type'    => 'movie_engine_series',
				'query_source' => 'tmdb',
				'tmdb_type'    => 'day',
				'count'        => 10,
				'slider'       => 1,
			),
			array(
				'id'          => 'new_releases',
				'type'        => 'library',
				'enabled'     => 1,
				'title'       => esc_html__( 'New Releases', 'hello-movieengine' ),
				'query_type'  => 'new_releases',
				'post_type'   => 'movie_engine_movie',
				'count'       => 12,
				'columns'     => 6,
				'slider'      => 1,
				'show_view_all' => 1,
			),
			array(
				'id'          => 'upcoming_movies',
				'type'        => 'library',
				'enabled'     => 1,
				'title'       => esc_html__( 'Coming Soon to Theaters', 'hello-movieengine' ),
				'query_type'  => 'upcoming',
				'post_type'   => 'movie_engine_movie',
				'count'       => 12,
				'columns'     => 6,
				'slider'      => 1,
				'show_view_all' => 1,
			),
		);
	}

	/**
	 * Optional responsive column attrs for Movie Engine shortcodes.
	 *
	 * @param array $sec Section row JSON.
	 * @return string
	 */
	public static function builder_responsive_columns_shortcode_attrs( array $sec ) {
		$out = '';
		foreach ( array( 'columns_laptop', 'columns_tablet', 'columns_mobile' ) as $key ) {
			if ( ! isset( $sec[ $key ] ) || ! is_numeric( $sec[ $key ] ) ) {
				continue;
			}
			$val = absint( $sec[ $key ] );
			if ( $val < 1 ) {
				continue;
			}
			$out .= sprintf( ' %s="%d"', esc_attr( $key ), $val );
		}
		return $out;
	}

	/**
	 * @param array $sec Section row JSON.
	 * @return string
	 */
	public static function builder_gap_shortcode_attr( array $sec ) {
		if ( empty( $sec['gap'] ) ) {
			return '';
		}
		return sprintf( ' gap="%d"', absint( $sec['gap'] ) );
	}

	/**
	 * @param array $sec Section row JSON.
	 * @param bool  $default_show Default when show_view_all key is absent.
	 * @return string
	 */
	public static function builder_view_all_shortcode_attrs( array $sec, $default_show = true ) {
		$show = ! isset( $sec['show_view_all'] ) ? $default_show : ! empty( $sec['show_view_all'] );
		$out  = sprintf( ' show_view_all="%s"', $show ? 'true' : 'false' );
		if ( ! empty( $sec['view_all_url'] ) ) {
			$out .= sprintf( ' view_all_url="%s"', esc_url( $sec['view_all_url'] ) );
		}
		if ( ! empty( $sec['view_all_text'] ) ) {
			$out .= sprintf( ' view_all_text="%s"', esc_attr( $sec['view_all_text'] ) );
		}
		return $out;
	}

	/**
	 * @param array $sec Section row JSON.
	 * @return string
	 */
	public static function builder_slider_shortcode_attrs( array $sec ) {
		$autoplay = ! empty( $sec['slider_autoplay'] ) ? 'true' : 'false';
		$loop     = ! empty( $sec['slider_loop'] ) ? 'true' : 'false';
		$pause    = ! isset( $sec['slider_pause'] ) || ! empty( $sec['slider_pause'] ) ? 'true' : 'false';
		$dots     = ! empty( $sec['slider_dots'] ) ? 'true' : 'false';
		$speed    = absint( $sec['slider_speed'] ?? 3000 );
		return sprintf(
			' autoplay="%s" autoplay_speed="%d" loop="%s" pause_on_hover="%s" show_dots="%s"',
			esc_attr( $autoplay ),
			$speed,
			esc_attr( $loop ),
			esc_attr( $pause ),
			esc_attr( $dots )
		);
	}

	/**
	 * Taxonomy include/exclude and order.
	 *
	 * @param array $sec Section row JSON.
	 * @return string
	 */
	public static function builder_taxonomy_pick_shortcode_attrs( array $sec ) {
		$out = '';
		if ( ! empty( $sec['term_include'] ) ) {
			$out .= sprintf( ' include="%s"', esc_attr( $sec['term_include'] ) );
		}
		if ( ! empty( $sec['term_exclude'] ) ) {
			$out .= sprintf( ' exclude="%s"', esc_attr( $sec['term_exclude'] ) );
		}
		if ( ! empty( $sec['tax_orderby'] ) ) {
			$out .= sprintf( ' orderby="%s"', esc_attr( sanitize_key( $sec['tax_orderby'] ) ) );
		}
		if ( ! empty( $sec['tax_order'] ) ) {
			$out .= sprintf( ' order="%s"', esc_attr( strtoupper( $sec['tax_order'] ) === 'ASC' ? 'ASC' : 'DESC' ) );
		}
		return $out;
	}

	/**
	 * Studio / taxonomy card logo ratio.
	 *
	 * @param array $sec Section row JSON.
	 * @return string
	 */
	public static function builder_logo_ratio_shortcode_attrs( array $sec ) {
		$ratio = ! empty( $sec['logo_ratio'] ) ? sanitize_key( $sec['logo_ratio'] ) : 'custom';
		$out   = sprintf( ' ratio="%s"', esc_attr( $ratio ) );
		if ( 'custom' === $ratio && ! empty( $sec['logo_custom_ratio'] ) ) {
			$out .= sprintf( ' custom_ratio="%s"', esc_attr( $sec['logo_custom_ratio'] ) );
		}
		return $out;
	}

	/**
	 * Shared responsive columns + gap + view-all URL markup.
	 *
	 * @param array $section Saved row.
	 */
	private function render_shared_grid_header_controls( $section ) {
		$columns_laptop = isset( $section['columns_laptop'] ) ? absint( $section['columns_laptop'] ) : 0;
		$columns_tablet = isset( $section['columns_tablet'] ) ? absint( $section['columns_tablet'] ) : 0;
		$columns_mobile = isset( $section['columns_mobile'] ) ? absint( $section['columns_mobile'] ) : 0;
		$gap            = isset( $section['gap'] ) ? absint( $section['gap'] ) : 0;
		$show_view_all  = ! isset( $section['show_view_all'] ) || ! empty( $section['show_view_all'] );
		$view_all_url   = isset( $section['view_all_url'] ) ? (string) $section['view_all_url'] : '';
		$view_all_text  = isset( $section['view_all_text'] ) ? (string) $section['view_all_text'] : '';
		?>
		<p class="description" style="margin: 0 0 8px;">
			<?php esc_html_e( 'Use the preview devices: Laptop (1280px), Tablet (720px), Phone (320px). Desktop is the full preview. 0 = automatic.', 'hello-movieengine' ); ?>
		</p>
		<div class="hello-movieengine-builder-row-grid hello-movieengine-builder-row-grid-3">
			<div class="hello-movieengine-builder-field">
				<label><?php esc_html_e( 'Columns (Laptop)', 'hello-movieengine' ); ?></label>
				<input type="number" class="hello-movieengine-section-columns-laptop" value="<?php echo esc_attr( $columns_laptop ); ?>" min="0" max="8" step="1" placeholder="<?php esc_attr_e( '0 = auto', 'hello-movieengine' ); ?>" />
			</div>
			<div class="hello-movieengine-builder-field">
				<label><?php esc_html_e( 'Columns (Tablet)', 'hello-movieengine' ); ?></label>
				<input type="number" class="hello-movieengine-section-columns-tablet" value="<?php echo esc_attr( $columns_tablet ); ?>" min="0" max="8" step="1" placeholder="<?php esc_attr_e( '0 = auto', 'hello-movieengine' ); ?>" />
			</div>
			<div class="hello-movieengine-builder-field">
				<label><?php esc_html_e( 'Columns (Mobile)', 'hello-movieengine' ); ?></label>
				<input type="number" class="hello-movieengine-section-columns-mobile" value="<?php echo esc_attr( $columns_mobile ); ?>" min="0" max="6" step="1" placeholder="<?php esc_attr_e( '0 = auto', 'hello-movieengine' ); ?>" />
			</div>
		</div>
		<div class="hello-movieengine-builder-row-grid">
			<div class="hello-movieengine-builder-field">
				<label><?php esc_html_e( 'Gap between items (px)', 'hello-movieengine' ); ?></label>
				<input type="number" class="hello-movieengine-section-gap" value="<?php echo esc_attr( $gap ); ?>" min="0" max="60" step="1" placeholder="<?php esc_attr_e( '0 = default', 'hello-movieengine' ); ?>" />
			</div>
			<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
				<label>
					<input type="checkbox" class="hello-movieengine-section-view-all" value="1" <?php checked( $show_view_all, true ); ?> />
					<?php esc_html_e( 'Show "View All" link', 'hello-movieengine' ); ?>
				</label>
			</div>
		</div>
		<div class="hello-movieengine-builder-row-grid">
			<div class="hello-movieengine-builder-field">
				<label><?php esc_html_e( 'View All URL (optional)', 'hello-movieengine' ); ?></label>
				<input type="url" class="hello-movieengine-section-view-all-url" value="<?php echo esc_attr( $view_all_url ); ?>" placeholder="<?php esc_attr_e( 'Leave blank for default archive', 'hello-movieengine' ); ?>" />
			</div>
			<div class="hello-movieengine-builder-field">
				<label><?php esc_html_e( 'View All label', 'hello-movieengine' ); ?></label>
				<input type="text" class="hello-movieengine-section-view-all-text" value="<?php echo esc_attr( $view_all_text ); ?>" placeholder="<?php esc_attr_e( 'Default: View All', 'hello-movieengine' ); ?>" />
			</div>
		</div>
		<?php
	}

	/**
	 * Slider carousel options (shared CSS classes for JS sync).
	 *
	 * @param array $section Saved row.
	 */
	private function render_shared_slider_controls( $section ) {
		$slider_autoplay = ! empty( $section['slider_autoplay'] );
		$slider_speed    = isset( $section['slider_speed'] ) ? absint( $section['slider_speed'] ) : 3000;
		$slider_loop     = ! empty( $section['slider_loop'] );
		$slider_pause    = ! isset( $section['slider_pause'] ) || ! empty( $section['slider_pause'] );
		$slider_dots     = ! empty( $section['slider_dots'] );
		?>
		<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-controls-play"></span><?php esc_html_e( 'Slider & Carousel Options', 'hello-movieengine' ); ?></div>
		<div class="hello-movieengine-builder-row-grid">
			<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
				<label>
					<input type="checkbox" class="hello-movieengine-section-slider-autoplay" value="1" <?php checked( $slider_autoplay, true ); ?> />
					<?php esc_html_e( 'Auto-slide carousel', 'hello-movieengine' ); ?>
				</label>
			</div>
			<div class="hello-movieengine-builder-field">
				<label><?php esc_html_e( 'Speed (ms)', 'hello-movieengine' ); ?></label>
				<input type="number" class="hello-movieengine-section-slider-speed" value="<?php echo esc_attr( $slider_speed ); ?>" min="1500" max="10000" step="500" />
			</div>
		</div>
		<div class="hello-movieengine-builder-row-grid">
			<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
				<label>
					<input type="checkbox" class="hello-movieengine-section-slider-loop" value="1" <?php checked( $slider_loop, true ); ?> />
					<?php esc_html_e( 'Infinite Loop', 'hello-movieengine' ); ?>
				</label>
			</div>
			<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
				<label>
					<input type="checkbox" class="hello-movieengine-section-slider-pause" value="1" <?php checked( $slider_pause, true ); ?> />
					<?php esc_html_e( 'Pause on Hover', 'hello-movieengine' ); ?>
				</label>
			</div>
		</div>
		<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
			<label>
				<input type="checkbox" class="hello-movieengine-section-slider-dots" value="1" <?php checked( $slider_dots, true ); ?> />
				<?php esc_html_e( 'Show Dot Indicators', 'hello-movieengine' ); ?>
			</label>
		</div>
		<?php
	}

	/**
	 * Taxonomy-specific: order, include/exclude, logo ratio.
	 *
	 * @param array  $section Saved row.
	 * @param string $order_default ASC or DESC.
	 */
	private function render_shared_taxonomy_controls( $section, $order_default = 'ASC' ) {
		$tax_orderby       = isset( $section['tax_orderby'] ) ? sanitize_key( $section['tax_orderby'] ) : 'name';
		$tax_order         = isset( $section['tax_order'] ) ? strtoupper( (string) $section['tax_order'] ) : $order_default;
		$term_include      = isset( $section['term_include'] ) ? (string) $section['term_include'] : '';
		$term_exclude      = isset( $section['term_exclude'] ) ? (string) $section['term_exclude'] : '';
		$logo_ratio        = isset( $section['logo_ratio'] ) ? sanitize_key( $section['logo_ratio'] ) : 'custom';
		$logo_custom_ratio = isset( $section['logo_custom_ratio'] ) ? (string) $section['logo_custom_ratio'] : '16/9';
		?>
		<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-sort"></span><?php esc_html_e( 'Query & Specific Items', 'hello-movieengine' ); ?></div>
		<div class="hello-movieengine-builder-row-grid">
			<div class="hello-movieengine-builder-field">
				<label><?php esc_html_e( 'Order by', 'hello-movieengine' ); ?></label>
				<select class="hello-movieengine-section-tax-orderby">
					<option value="name" <?php selected( $tax_orderby, 'name' ); ?>><?php esc_html_e( 'Name', 'hello-movieengine' ); ?></option>
					<option value="count" <?php selected( $tax_orderby, 'count' ); ?>><?php esc_html_e( 'Item count', 'hello-movieengine' ); ?></option>
					<option value="slug" <?php selected( $tax_orderby, 'slug' ); ?>><?php esc_html_e( 'Slug', 'hello-movieengine' ); ?></option>
					<option value="rand" <?php selected( $tax_orderby, 'rand' ); ?>><?php esc_html_e( 'Random', 'hello-movieengine' ); ?></option>
				</select>
			</div>
			<div class="hello-movieengine-builder-field">
				<label><?php esc_html_e( 'Sort direction', 'hello-movieengine' ); ?></label>
				<select class="hello-movieengine-section-tax-order">
					<option value="ASC" <?php selected( $tax_order, 'ASC' ); ?>><?php esc_html_e( 'Ascending', 'hello-movieengine' ); ?></option>
					<option value="DESC" <?php selected( $tax_order, 'DESC' ); ?>><?php esc_html_e( 'Descending', 'hello-movieengine' ); ?></option>
				</select>
			</div>
		</div>
		<div class="hello-movieengine-builder-field">
			<label><?php esc_html_e( 'Include only (term IDs or slugs)', 'hello-movieengine' ); ?></label>
			<input type="text" class="hello-movieengine-section-term-include" value="<?php echo esc_attr( $term_include ); ?>" placeholder="<?php esc_attr_e( 'e.g. warner-bros, 42', 'hello-movieengine' ); ?>" />
		</div>
		<div class="hello-movieengine-builder-field">
			<label><?php esc_html_e( 'Exclude (term IDs or slugs)', 'hello-movieengine' ); ?></label>
			<input type="text" class="hello-movieengine-section-term-exclude" value="<?php echo esc_attr( $term_exclude ); ?>" placeholder="<?php esc_attr_e( 'Optional', 'hello-movieengine' ); ?>" />
		</div>
		<div class="hello-movieengine-builder-row-grid">
			<div class="hello-movieengine-builder-field">
				<label><?php esc_html_e( 'Logo / card aspect ratio', 'hello-movieengine' ); ?></label>
				<select class="hello-movieengine-section-logo-ratio">
					<option value="custom" <?php selected( $logo_ratio, 'custom' ); ?>><?php esc_html_e( 'Custom', 'hello-movieengine' ); ?></option>
					<option value="landscape" <?php selected( $logo_ratio, 'landscape' ); ?>><?php esc_html_e( 'Landscape', 'hello-movieengine' ); ?></option>
					<option value="square" <?php selected( $logo_ratio, 'square' ); ?>><?php esc_html_e( 'Square', 'hello-movieengine' ); ?></option>
					<option value="portrait" <?php selected( $logo_ratio, 'portrait' ); ?>><?php esc_html_e( 'Portrait', 'hello-movieengine' ); ?></option>
				</select>
			</div>
			<div class="hello-movieengine-builder-field hello-movieengine-logo-custom-ratio" style="<?php echo ( 'custom' === $logo_ratio ) ? '' : 'display:none;'; ?>">
				<label><?php esc_html_e( 'Custom ratio (W/H)', 'hello-movieengine' ); ?></label>
				<input type="text" class="hello-movieengine-section-logo-custom-ratio" value="<?php echo esc_attr( $logo_custom_ratio ); ?>" placeholder="16/9" />
			</div>
		</div>
		<?php
	}

	/**
	 * Render control content.
	 */
	public function render_content() {
		$value    = $this->value();
		$sections = json_decode( (string) $value, true );

		if ( ! is_array( $sections ) || empty( $sections ) ) {
			$sections = self::get_default_sections();
		}

		$sections = array_values(
			array_filter(
				$sections,
				static function ( $sec ) {
					return ( $sec['type'] ?? '' ) !== 'live_search';
				}
			)
		);

		$types = self::get_section_types();
		?>
		<div class="hello-movieengine-section-builder-control">
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<div class="hello-movieengine-section-builder-items" id="<?php echo esc_attr( $this->id ); ?>-items">
				<?php foreach ( $sections as $index => $section ) :
					$id          = isset( $section['id'] ) ? $section['id'] : 'sec_' . $index;
					$type        = isset( $section['type'] ) ? $section['type'] : 'library';
					$title       = isset( $section['title'] ) ? $section['title'] : '';
					$enabled     = ! isset( $section['enabled'] ) || ! empty( $section['enabled'] );

					// Hero parameters
					$hero_style     = isset( $section['hero_style'] ) ? $section['hero_style'] : 'style1';
					$hero_type      = isset( $section['hero_type'] ) ? $section['hero_type'] : 'trending';
					$hero_query_source = isset( $section['hero_query_source'] ) ? (string) $section['hero_query_source'] : 'site';
					if ( ! in_array( $hero_query_source, array( 'site', 'tmdb' ), true ) ) {
						$hero_query_source = 'site';
					}
					$hero_tmdb_type = isset( $section['hero_tmdb_type'] ) ? sanitize_key( $section['hero_tmdb_type'] ) : 'day';
					if ( ! in_array( $hero_tmdb_type, self::get_tmdb_type_options(), true ) ) {
						$hero_tmdb_type = 'day';
					}
					$hero_ids       = isset( $section['hero_ids'] ) ? $section['hero_ids'] : '';
					$hero_genre     = isset( $section['hero_genre'] ) ? $section['hero_genre'] : '';
					$hero_year      = isset( $section['hero_year'] ) ? $section['hero_year'] : '';
					$hero_height    = isset( $section['hero_height'] ) ? $section['hero_height'] : '70vh';
					$hero_content_width = 'box';
					if ( ! empty( $section['hero_content_width'] ) && 'full' === $section['hero_content_width'] ) {
						$hero_content_width = 'full';
					} elseif ( ! empty( $section['hero_width'] ) && 'full' === $section['hero_width'] ) {
						$hero_content_width = 'full';
					} elseif ( ! empty( $section['hero_width'] ) && 'box' === $section['hero_width'] ) {
						$hero_content_width = 'box';
					}
					$hero_content_max = isset( $section['hero_content_max_width'] ) ? absint( $section['hero_content_max_width'] ) : 0;
					if ( ! $hero_content_max && ! empty( $section['hero_box_width'] ) ) {
						$hero_content_max = absint( $section['hero_box_width'] );
					}
					$hero_limit     = isset( $section['hero_limit'] ) ? absint( $section['hero_limit'] ) : 5;
					$hero_post_type = isset( $section['hero_post_type'] ) ? $section['hero_post_type'] : 'movie_engine_movie,movie_engine_series';
					$hero_speed     = isset( $section['hero_speed'] ) ? absint( $section['hero_speed'] ) : 5000;
					$hero_loop      = ! isset( $section['hero_loop'] ) || ! empty( $section['hero_loop'] );
					$hero_pause     = ! isset( $section['hero_pause'] ) || ! empty( $section['hero_pause'] );
					$hero_nav       = ! isset( $section['hero_nav'] ) || ! empty( $section['hero_nav'] );
					$hero_dots      = ! isset( $section['hero_dots'] ) || ! empty( $section['hero_dots'] );
					$autoplay       = ! isset( $section['autoplay'] ) || ! empty( $section['autoplay'] );
					$show_trailer   = ! isset( $section['show_trailer'] ) || ! empty( $section['show_trailer'] );
					$show_watchlist = ! isset( $section['show_watchlist'] ) || ! empty( $section['show_watchlist'] );
					$show_imdb      = ! isset( $section['show_imdb'] ) || ! empty( $section['show_imdb'] );
					$show_desc      = ! isset( $section['show_desc'] ) || ! empty( $section['show_desc'] );

					// Library parameters
					$query_type     = isset( $section['query_type'] ) ? $section['query_type'] : 'trending';
					$query_source   = isset( $section['query_source'] ) ? (string) $section['query_source'] : 'site';
					if ( ! in_array( $query_source, array( 'site', 'tmdb' ), true ) ) {
						$query_source = 'site';
					}
					$tmdb_type      = isset( $section['tmdb_type'] ) ? sanitize_key( $section['tmdb_type'] ) : 'day';
					if ( ! in_array( $tmdb_type, self::get_tmdb_type_options(), true ) ) {
						$tmdb_type = 'day';
					}
					$post_type      = isset( $section['post_type'] ) ? $section['post_type'] : 'movie_engine_movie';
					$count          = isset( $section['count'] ) ? absint( $section['count'] ) : 12;
					$columns        = isset( $section['columns'] ) ? absint( $section['columns'] ) : 6;
					$slider         = ! isset( $section['slider'] ) || ! empty( $section['slider'] );
					$ids            = isset( $section['ids'] ) ? $section['ids'] : '';
					$genre          = isset( $section['genre'] ) ? $section['genre'] : '';
					$year           = isset( $section['year'] ) ? $section['year'] : '';
					$language       = isset( $section['language'] ) ? $section['language'] : '';
					$orderby        = isset( $section['orderby'] ) ? $section['orderby'] : 'date';
					$order          = isset( $section['order'] ) ? $section['order'] : 'DESC';
					$show_view_all  = ! isset( $section['show_view_all'] ) || ! empty( $section['show_view_all'] );
					$slider_autoplay= ! empty( $section['slider_autoplay'] );
					$slider_speed   = isset( $section['slider_speed'] ) ? absint( $section['slider_speed'] ) : 3000;
					$slider_loop    = ! empty( $section['slider_loop'] );
					$slider_pause   = ! isset( $section['slider_pause'] ) || ! empty( $section['slider_pause'] );
					$slider_dots    = ! empty( $section['slider_dots'] );
					$show_rating    = ! isset( $section['show_rating'] ) || ! empty( $section['show_rating'] );
					$show_year      = ! isset( $section['show_year'] ) || ! empty( $section['show_year'] );
					$show_quality   = ! isset( $section['show_quality'] ) || ! empty( $section['show_quality'] );

					// Person parameters
					$department      = isset( $section['department'] ) ? $section['department'] : '';
					$person_orderby  = isset( $section['person_orderby'] ) ? $section['person_orderby'] : 'title';
					$person_style    = isset( $section['person_style'] ) ? $section['person_style'] : 'overlay';
					if ( ! in_array( $person_style, array( 'overlay', 'stacked' ), true ) ) {
						$person_style = 'overlay';
					}
					$person_autoplay = ! empty( $section['person_autoplay'] );
					$person_speed    = isset( $section['person_speed'] ) ? absint( $section['person_speed'] ) : 3000;
					$person_loop     = ! empty( $section['person_loop'] );

					// Studios parameters
					$studio_layout  = isset( $section['studio_layout'] ) ? $section['studio_layout'] : 'grid';
					$studio_columns = isset( $section['studio_columns'] ) ? absint( $section['studio_columns'] ) : 6;
					$studio_limit   = isset( $section['studio_limit'] ) ? absint( $section['studio_limit'] ) : 12;
					$studio_title   = ! empty( $section['studio_title'] );

					// Genres parameters
					$genre_layout   = isset( $section['genre_layout'] ) ? $section['genre_layout'] : 'pills';
					$genre_limit    = isset( $section['genre_limit'] ) ? absint( $section['genre_limit'] ) : 12;
					$genre_count    = ! isset( $section['genre_count'] ) || ! empty( $section['genre_count'] );

					// History parameters
					$history_count   = isset( $section['history_count'] ) ? absint( $section['history_count'] ) : 6;
					$history_columns = isset( $section['history_columns'] ) ? absint( $section['history_columns'] ) : 4;

					// Years parameters
					$year_layout     = isset( $section['year_layout'] ) ? $section['year_layout'] : 'pills';
					$year_limit      = isset( $section['year_limit'] ) ? absint( $section['year_limit'] ) : 10;
					$year_order      = isset( $section['year_order'] ) ? $section['year_order'] : 'DESC';

					// Shortcode
					$shortcode      = isset( $section['shortcode'] ) ? $section['shortcode'] : '';

					$type_name = isset( $types[ $type ] ) ? $types[ $type ] : ucfirst( $type );
				?>
					<div class="hello-movieengine-builder-row <?php echo $enabled ? 'is-enabled' : 'is-disabled'; ?>" data-id="<?php echo esc_attr( $id ); ?>">
						<div class="hello-movieengine-builder-header">
							<span class="hello-movieengine-builder-drag" title="<?php esc_attr_e( 'Drag to reorder', 'hello-movieengine' ); ?>">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="5" r="2"/><circle cx="9" cy="12" r="2"/><circle cx="9" cy="19" r="2"/><circle cx="15" cy="5" r="2"/><circle cx="15" cy="12" r="2"/><circle cx="15" cy="19" r="2"/></svg>
							</span>
							<button type="button" class="hello-movieengine-builder-eye" title="<?php esc_attr_e( 'Toggle Visibility', 'hello-movieengine' ); ?>" data-enabled="<?php echo $enabled ? '1' : '0'; ?>">
								<span class="dashicons <?php echo $enabled ? 'dashicons-visibility' : 'dashicons-hidden'; ?>"></span>
							</button>
							<span class="hello-movieengine-builder-title">
								<strong class="hello-movieengine-builder-title-text"><?php echo esc_html( ! empty( $title ) ? $title : $type_name ); ?></strong>
								<span class="hello-movieengine-builder-type-badge"><?php echo esc_html( $type_name ); ?></span>
							</span>
							<div class="hello-movieengine-builder-actions">
								<button type="button" class="hello-movieengine-builder-toggle-edit" title="<?php esc_attr_e( 'Configure section', 'hello-movieengine' ); ?>">
									<span class="dashicons dashicons-arrow-down-alt2"></span>
								</button>
								<button type="button" class="hello-movieengine-builder-delete" title="<?php esc_attr_e( 'Remove section', 'hello-movieengine' ); ?>">
									<span class="dashicons dashicons-trash"></span>
								</button>
							</div>
						</div>

						<div class="hello-movieengine-builder-body" style="display: none;">
							<div class="hello-movieengine-builder-field">
								<label><?php esc_html_e( 'Section Type', 'hello-movieengine' ); ?></label>
								<select class="hello-movieengine-section-type">
									<?php foreach ( $types as $t_key => $t_label ) : ?>
										<option value="<?php echo esc_attr( $t_key ); ?>" <?php selected( $type, $t_key ); ?>>
											<?php echo esc_html( $t_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="hello-movieengine-builder-field">
								<label><?php esc_html_e( 'Section Title / Heading', 'hello-movieengine' ); ?></label>
								<input type="text" class="hello-movieengine-section-title" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php esc_attr_e( 'e.g. Trending Now', 'hello-movieengine' ); ?>" />
							</div>

							<!-- ==============================
							     1. Library & Top 10 Fields
							     ============================== -->
							<div class="hello-movieengine-fields-library" style="<?php echo in_array( $type, array( 'library', 'top10' ), true ) ? '' : 'display: none;'; ?>">
								<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-filter"></span><?php esc_html_e( 'Content Query & Filters', 'hello-movieengine' ); ?></div>
								<?php if ( 'top10' === $type ) : ?>
									<p class="description" style="font-size: 11px; color: #64748b; margin: 0 0 10px;">
										<?php esc_html_e( 'Top 10 supports Website presets (type) or TMDb lists (tmdb_type). Use Movies or TV Series post type below.', 'hello-movieengine' ); ?>
									</p>
								<?php endif; ?>

								<div class="hello-movieengine-builder-field">
									<label><?php esc_html_e( 'List data source', 'hello-movieengine' ); ?></label>
									<select class="hello-movieengine-section-query-source">
										<option value="site" <?php selected( $query_source, 'site' ); ?>><?php esc_html_e( 'Website — views, dates & ratings on your site', 'hello-movieengine' ); ?></option>
										<option value="tmdb" <?php selected( $query_source, 'tmdb' ); ?>><?php esc_html_e( 'TMDb — live list (imported titles only)', 'hello-movieengine' ); ?></option>
									</select>
								</div>

								<div class="hello-movieengine-query-site" style="<?php echo ( 'site' === $query_source ) ? '' : 'display: none;'; ?>">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Website preset (type)', 'hello-movieengine' ); ?></label>
										<select class="hello-movieengine-section-query">
											<option value="trending" <?php selected( $query_type, 'trending' ); ?>><?php esc_html_e( 'Trending Now', 'hello-movieengine' ); ?></option>
											<option value="new_releases" <?php selected( $query_type, 'new_releases' ); ?>><?php esc_html_e( 'New Releases', 'hello-movieengine' ); ?></option>
											<option value="upcoming" <?php selected( $query_type, 'upcoming' ); ?>><?php esc_html_e( 'Upcoming Releases', 'hello-movieengine' ); ?></option>
											<option value="popular" <?php selected( $query_type, 'popular' ); ?>><?php esc_html_e( 'Most Popular', 'hello-movieengine' ); ?></option>
											<option value="top_rated" <?php selected( $query_type, 'top_rated' ); ?>><?php esc_html_e( 'Top Rated', 'hello-movieengine' ); ?></option>
										</select>
									</div>
								</div>

								<div class="hello-movieengine-query-tmdb" style="<?php echo ( 'tmdb' === $query_source ) ? '' : 'display: none;'; ?>">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'TMDb list (tmdb_type)', 'hello-movieengine' ); ?></label>
										<select class="hello-movieengine-section-tmdb-type">
											<option value="day" <?php selected( $tmdb_type, 'day' ); ?>><?php esc_html_e( 'Trending today', 'hello-movieengine' ); ?></option>
											<option value="week" <?php selected( $tmdb_type, 'week' ); ?>><?php esc_html_e( 'Trending this week', 'hello-movieengine' ); ?></option>
											<option value="now_playing" <?php selected( $tmdb_type, 'now_playing' ); ?>><?php esc_html_e( 'Now playing / On the air', 'hello-movieengine' ); ?></option>
											<option value="popular" <?php selected( $tmdb_type, 'popular' ); ?>><?php esc_html_e( 'Popular', 'hello-movieengine' ); ?></option>
											<option value="top_rated" <?php selected( $tmdb_type, 'top_rated' ); ?>><?php esc_html_e( 'Top rated', 'hello-movieengine' ); ?></option>
											<option value="upcoming" <?php selected( $tmdb_type, 'upcoming' ); ?>><?php esc_html_e( 'Upcoming', 'hello-movieengine' ); ?></option>
										</select>
										<span class="description" style="display:block;font-size:11px;color:#64748b;margin-top:4px;"><?php esc_html_e( 'Order matches TMDb. Only titles already imported appear.', 'hello-movieengine' ); ?></span>
									</div>
								</div>

								<div class="hello-movieengine-builder-field">
									<label><?php esc_html_e( 'Post Type', 'hello-movieengine' ); ?></label>
									<select class="hello-movieengine-section-post-type">
										<option value="movie_engine_movie" <?php selected( $post_type, 'movie_engine_movie' ); ?>><?php esc_html_e( 'Movies Only', 'hello-movieengine' ); ?></option>
										<option value="movie_engine_series" <?php selected( $post_type, 'movie_engine_series' ); ?>><?php esc_html_e( 'TV Series Only', 'hello-movieengine' ); ?></option>
										<option value="movie_engine_movie,movie_engine_series" <?php selected( $post_type, 'movie_engine_movie,movie_engine_series' ); ?>><?php esc_html_e( 'Both Movies & TV Series', 'hello-movieengine' ); ?></option>
									</select>
								</div>

								<div class="hello-movieengine-builder-field">
									<label><?php esc_html_e( 'Specific Post IDs (optional)', 'hello-movieengine' ); ?></label>
									<input type="text" class="hello-movieengine-section-ids" value="<?php echo esc_attr( $ids ); ?>" placeholder="<?php esc_attr_e( 'e.g. 101, 204, 315 (leave blank for dynamic query)', 'hello-movieengine' ); ?>" />
								</div>

								<?php if ( 'top10' !== $type ) : ?>
									<div class="hello-movieengine-builder-row-grid-3 hello-movieengine-query-site-only" style="<?php echo ( 'site' === $query_source ) ? '' : 'display: none;'; ?>">
										<div class="hello-movieengine-builder-field">
											<label><?php esc_html_e( 'Genre Slug', 'hello-movieengine' ); ?></label>
											<input type="text" class="hello-movieengine-section-genre" value="<?php echo esc_attr( $genre ); ?>" placeholder="e.g. action" />
										</div>
										<div class="hello-movieengine-builder-field">
											<label><?php esc_html_e( 'Year', 'hello-movieengine' ); ?></label>
											<input type="text" class="hello-movieengine-section-year" value="<?php echo esc_attr( $year ); ?>" placeholder="e.g. 2025" />
										</div>
										<div class="hello-movieengine-builder-field">
											<label><?php esc_html_e( 'Language', 'hello-movieengine' ); ?></label>
											<input type="text" class="hello-movieengine-section-language" value="<?php echo esc_attr( $language ); ?>" placeholder="e.g. en, ko, hi" />
										</div>
									</div>

									<div class="hello-movieengine-builder-row-grid hello-movieengine-query-site-only" style="<?php echo ( 'site' === $query_source ) ? '' : 'display: none;'; ?>">
										<div class="hello-movieengine-builder-field">
											<label><?php esc_html_e( 'Order By', 'hello-movieengine' ); ?></label>
											<select class="hello-movieengine-section-orderby">
												<option value="date" <?php selected( $orderby, 'date' ); ?>><?php esc_html_e( 'Release Date', 'hello-movieengine' ); ?></option>
												<option value="views" <?php selected( $orderby, 'views' ); ?>><?php esc_html_e( 'Views Count', 'hello-movieengine' ); ?></option>
												<option value="title" <?php selected( $orderby, 'title' ); ?>><?php esc_html_e( 'Title (Alphabetical)', 'hello-movieengine' ); ?></option>
												<option value="rand" <?php selected( $orderby, 'rand' ); ?>><?php esc_html_e( 'Random', 'hello-movieengine' ); ?></option>
											</select>
										</div>
										<div class="hello-movieengine-builder-field">
											<label><?php esc_html_e( 'Sort Direction', 'hello-movieengine' ); ?></label>
											<select class="hello-movieengine-section-order">
												<option value="DESC" <?php selected( $order, 'DESC' ); ?>><?php esc_html_e( 'Descending (High to Low)', 'hello-movieengine' ); ?></option>
												<option value="ASC" <?php selected( $order, 'ASC' ); ?>><?php esc_html_e( 'Ascending (Low to High)', 'hello-movieengine' ); ?></option>
											</select>
										</div>
									</div>
								<?php endif; ?>

								<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-layout"></span><?php esc_html_e( 'Layout & Display', 'hello-movieengine' ); ?></div>

								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Items Count', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-count" value="<?php echo esc_attr( $count ); ?>" min="3" max="40" step="1" />
									</div>
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Columns (Desktop)', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-columns" value="<?php echo esc_attr( $columns ); ?>" min="3" max="8" step="1" />
									</div>
								</div>

								<?php $this->render_shared_grid_header_controls( $section ); ?>

								<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
									<label>
										<input type="checkbox" class="hello-movieengine-section-slider" value="1" <?php checked( $slider, 1 ); ?> />
										<?php esc_html_e( 'Slider Carousel', 'hello-movieengine' ); ?>
									</label>
								</div>

								<?php $this->render_shared_slider_controls( $section ); ?>

								<?php if ( 'top10' !== $type ) : ?>
									<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-visibility"></span><?php esc_html_e( 'Card Badges & Meta', 'hello-movieengine' ); ?></div>

									<div class="hello-movieengine-builder-row-grid-3">
										<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
											<label>
												<input type="checkbox" class="hello-movieengine-section-show-rating" value="1" <?php checked( $show_rating, 1 ); ?> />
												<?php esc_html_e( 'Rating Badge', 'hello-movieengine' ); ?>
											</label>
										</div>
										<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
											<label>
												<input type="checkbox" class="hello-movieengine-section-show-year" value="1" <?php checked( $show_year, 1 ); ?> />
												<?php esc_html_e( 'Release Year', 'hello-movieengine' ); ?>
											</label>
										</div>
										<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
											<label>
												<input type="checkbox" class="hello-movieengine-section-show-quality" value="1" <?php checked( $show_quality, 1 ); ?> />
												<?php esc_html_e( 'Quality (4K/HD)', 'hello-movieengine' ); ?>
											</label>
										</div>
									</div>
								<?php endif; ?>
							</div>

							<!-- ==============================
							     2. Hero Billboard Fields
							     ============================== -->
							<div class="hello-movieengine-fields-hero" style="<?php echo ( 'hero' === $type ) ? '' : 'display: none;'; ?>">
								<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-layout"></span><?php esc_html_e( 'Hero Style & Layout', 'hello-movieengine' ); ?></div>

								<div class="hello-movieengine-builder-field">
									<label><?php esc_html_e( 'Hero Banner Style', 'hello-movieengine' ); ?></label>
									<select class="hello-movieengine-section-hero-style">
										<option value="style1" <?php selected( $hero_style, 'style1' ); ?>><?php esc_html_e( 'Style 1 — Classic Fullwidth Billboard', 'hello-movieengine' ); ?></option>
										<option value="style2" <?php selected( $hero_style, 'style2' ); ?>><?php esc_html_e( 'Style 2 — Bottom Thumbnail Slider', 'hello-movieengine' ); ?></option>
										<option value="style3" <?php selected( $hero_style, 'style3' ); ?>><?php esc_html_e( 'Style 3 — Compact Thumbnail Strip', 'hello-movieengine' ); ?></option>
										<option value="style4" <?php selected( $hero_style, 'style4' ); ?>><?php esc_html_e( 'Style 4 — Cinema Poster Cards', 'hello-movieengine' ); ?></option>
										<option value="style5" <?php selected( $hero_style, 'style5' ); ?>><?php esc_html_e( 'Style 5 — Arc / Curved Thumbnails Rail', 'hello-movieengine' ); ?></option>
									</select>
								</div>

								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Content width', 'hello-movieengine' ); ?></label>
										<select class="hello-movieengine-section-hero-content-width">
											<option value="full" <?php selected( $hero_content_width, 'full' ); ?>><?php esc_html_e( 'Full width', 'hello-movieengine' ); ?></option>
											<option value="box" <?php selected( $hero_content_width, 'box' ); ?>><?php esc_html_e( 'Boxed', 'hello-movieengine' ); ?></option>
										</select>
										<span class="description" style="font-size: 11px; color: #64748b;"><?php esc_html_e( 'Backdrop stays edge-to-edge; this controls title, text, and buttons only.', 'hello-movieengine' ); ?></span>
									</div>
									<div class="hello-movieengine-builder-field hello-movieengine-hero-content-max" style="<?php echo ( 'box' === $hero_content_width ) ? '' : 'display: none;'; ?>">
										<label><?php esc_html_e( 'Content box width (px)', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-hero-content-max-width" value="<?php echo esc_attr( $hero_content_max ); ?>" min="0" max="2400" step="10" placeholder="<?php esc_attr_e( '0 = theme default', 'hello-movieengine' ); ?>" />
									</div>
								</div>

								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Slides Count', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-hero-limit" value="<?php echo esc_attr( $hero_limit ); ?>" min="1" max="15" />
									</div>
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Hero Height (vh or px)', 'hello-movieengine' ); ?></label>
										<input type="text" class="hello-movieengine-section-hero-height" value="<?php echo esc_attr( $hero_height ); ?>" placeholder="70vh" />
									</div>
								</div>

								<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-filter"></span><?php esc_html_e( 'Content Source & Query', 'hello-movieengine' ); ?></div>

								<div class="hello-movieengine-builder-field">
									<label><?php esc_html_e( 'Specific Post IDs (optional)', 'hello-movieengine' ); ?></label>
									<input type="text" class="hello-movieengine-section-hero-ids" value="<?php echo esc_attr( $hero_ids ); ?>" placeholder="<?php esc_attr_e( 'e.g. 101, 204, 315 (leave blank for preset)', 'hello-movieengine' ); ?>" />
									<span class="description" style="font-size: 11px; color: #64748b;"><?php esc_html_e( 'Overrides preset below when entered.', 'hello-movieengine' ); ?></span>
								</div>

								<div class="hello-movieengine-builder-field">
									<label><?php esc_html_e( 'List data source', 'hello-movieengine' ); ?></label>
									<select class="hello-movieengine-section-hero-query-source">
										<option value="site" <?php selected( $hero_query_source, 'site' ); ?>><?php esc_html_e( 'Website — views, dates & ratings on your site', 'hello-movieengine' ); ?></option>
										<option value="tmdb" <?php selected( $hero_query_source, 'tmdb' ); ?>><?php esc_html_e( 'TMDb — live list (imported titles only)', 'hello-movieengine' ); ?></option>
									</select>
								</div>

								<div class="hello-movieengine-hero-query-site" style="<?php echo ( 'site' === $hero_query_source ) ? '' : 'display: none;'; ?>">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Website preset (type)', 'hello-movieengine' ); ?></label>
										<select class="hello-movieengine-section-hero-type">
											<option value="trending" <?php selected( $hero_type, 'trending' ); ?>><?php esc_html_e( 'Trending Spotlight', 'hello-movieengine' ); ?></option>
											<option value="new_releases" <?php selected( $hero_type, 'new_releases' ); ?>><?php esc_html_e( 'Latest Releases', 'hello-movieengine' ); ?></option>
											<option value="popular" <?php selected( $hero_type, 'popular' ); ?>><?php esc_html_e( 'Most Popular', 'hello-movieengine' ); ?></option>
											<option value="top_rated" <?php selected( $hero_type, 'top_rated' ); ?>><?php esc_html_e( 'Top Rated', 'hello-movieengine' ); ?></option>
											<option value="upcoming" <?php selected( $hero_type, 'upcoming' ); ?>><?php esc_html_e( 'Upcoming', 'hello-movieengine' ); ?></option>
										</select>
									</div>
								</div>

								<div class="hello-movieengine-hero-query-tmdb" style="<?php echo ( 'tmdb' === $hero_query_source ) ? '' : 'display: none;'; ?>">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'TMDb list (tmdb_type)', 'hello-movieengine' ); ?></label>
										<select class="hello-movieengine-section-hero-tmdb-type">
											<option value="day" <?php selected( $hero_tmdb_type, 'day' ); ?>><?php esc_html_e( 'Trending today', 'hello-movieengine' ); ?></option>
											<option value="week" <?php selected( $hero_tmdb_type, 'week' ); ?>><?php esc_html_e( 'Trending this week', 'hello-movieengine' ); ?></option>
											<option value="now_playing" <?php selected( $hero_tmdb_type, 'now_playing' ); ?>><?php esc_html_e( 'Now playing / On the air', 'hello-movieengine' ); ?></option>
											<option value="popular" <?php selected( $hero_tmdb_type, 'popular' ); ?>><?php esc_html_e( 'Popular', 'hello-movieengine' ); ?></option>
											<option value="top_rated" <?php selected( $hero_tmdb_type, 'top_rated' ); ?>><?php esc_html_e( 'Top rated', 'hello-movieengine' ); ?></option>
											<option value="upcoming" <?php selected( $hero_tmdb_type, 'upcoming' ); ?>><?php esc_html_e( 'Upcoming', 'hello-movieengine' ); ?></option>
										</select>
									</div>
								</div>

								<div class="hello-movieengine-builder-field">
									<label><?php esc_html_e( 'Post Type', 'hello-movieengine' ); ?></label>
									<select class="hello-movieengine-section-hero-post-type">
										<option value="movie_engine_movie,movie_engine_series" <?php selected( $hero_post_type, 'movie_engine_movie,movie_engine_series' ); ?>><?php esc_html_e( 'Both', 'hello-movieengine' ); ?></option>
										<option value="movie_engine_movie" <?php selected( $hero_post_type, 'movie_engine_movie' ); ?>><?php esc_html_e( 'Movies Only', 'hello-movieengine' ); ?></option>
										<option value="movie_engine_series" <?php selected( $hero_post_type, 'movie_engine_series' ); ?>><?php esc_html_e( 'TV Series Only', 'hello-movieengine' ); ?></option>
									</select>
								</div>

								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Genre Slug (optional)', 'hello-movieengine' ); ?></label>
										<input type="text" class="hello-movieengine-section-hero-genre" value="<?php echo esc_attr( $hero_genre ); ?>" placeholder="e.g. action" />
									</div>
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Year (optional)', 'hello-movieengine' ); ?></label>
										<input type="text" class="hello-movieengine-section-hero-year" value="<?php echo esc_attr( $hero_year ); ?>" placeholder="e.g. 2025" />
									</div>
								</div>

								<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-controls-play"></span><?php esc_html_e( 'Slider & Carousel Options', 'hello-movieengine' ); ?></div>

								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
										<label>
											<input type="checkbox" class="hello-movieengine-section-autoplay" value="1" <?php checked( $autoplay, 1 ); ?> />
											<?php esc_html_e( 'Enable Autoplay', 'hello-movieengine' ); ?>
										</label>
									</div>
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Speed (ms)', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-hero-speed" value="<?php echo esc_attr( $hero_speed ); ?>" min="2000" max="15000" step="500" />
									</div>
								</div>

								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
										<label>
											<input type="checkbox" class="hello-movieengine-section-hero-loop" value="1" <?php checked( $hero_loop, 1 ); ?> />
											<?php esc_html_e( 'Infinite Loop', 'hello-movieengine' ); ?>
										</label>
									</div>
									<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
										<label>
											<input type="checkbox" class="hello-movieengine-section-hero-pause" value="1" <?php checked( $hero_pause, 1 ); ?> />
											<?php esc_html_e( 'Pause on Hover', 'hello-movieengine' ); ?>
										</label>
									</div>
								</div>

								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
										<label>
											<input type="checkbox" class="hello-movieengine-section-hero-nav" value="1" <?php checked( $hero_nav, 1 ); ?> />
											<?php esc_html_e( 'Prev/Next Arrows', 'hello-movieengine' ); ?>
										</label>
									</div>
									<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
										<label>
											<input type="checkbox" class="hello-movieengine-section-hero-dots" value="1" <?php checked( $hero_dots, 1 ); ?> />
											<?php esc_html_e( 'Dot Indicators', 'hello-movieengine' ); ?>
										</label>
									</div>
								</div>

								<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-visibility"></span><?php esc_html_e( 'Action Buttons & Badges', 'hello-movieengine' ); ?></div>

								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
										<label>
											<input type="checkbox" class="hello-movieengine-section-hero-trailer" value="1" <?php checked( $show_trailer, 1 ); ?> />
											<?php esc_html_e( 'Show Trailer Button', 'hello-movieengine' ); ?>
										</label>
									</div>
									<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
										<label>
											<input type="checkbox" class="hello-movieengine-section-hero-watchlist" value="1" <?php checked( $show_watchlist, 1 ); ?> />
											<?php esc_html_e( 'Show Watchlist Button', 'hello-movieengine' ); ?>
										</label>
									</div>
								</div>

								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
										<label>
											<input type="checkbox" class="hello-movieengine-section-hero-imdb" value="1" <?php checked( $show_imdb, 1 ); ?> />
											<?php esc_html_e( 'Show IMDb Rating', 'hello-movieengine' ); ?>
										</label>
									</div>
									<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
										<label>
											<input type="checkbox" class="hello-movieengine-section-hero-desc" value="1" <?php checked( $show_desc, 1 ); ?> />
											<?php esc_html_e( 'Show Synopsis Text', 'hello-movieengine' ); ?>
										</label>
									</div>
								</div>
							</div>

							<!-- ==============================
							     3. Person List Fields
							     ============================== -->
							<div class="hello-movieengine-fields-person" style="<?php echo ( 'person_list' === $type ) ? '' : 'display: none;'; ?>">
								<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-admin-users"></span><?php esc_html_e( 'Cast & Stars Query', 'hello-movieengine' ); ?></div>

								<div class="hello-movieengine-builder-field">
									<label><?php esc_html_e( 'Department / Role', 'hello-movieengine' ); ?></label>
									<select class="hello-movieengine-section-department">
										<option value="" <?php selected( $department, '' ); ?>><?php esc_html_e( 'All Roles (Actors, Directors, etc.)', 'hello-movieengine' ); ?></option>
										<option value="Acting" <?php selected( $department, 'Acting' ); ?>><?php esc_html_e( 'Acting (Actors)', 'hello-movieengine' ); ?></option>
										<option value="Directing" <?php selected( $department, 'Directing' ); ?>><?php esc_html_e( 'Directing (Directors)', 'hello-movieengine' ); ?></option>
										<option value="Writing" <?php selected( $department, 'Writing' ); ?>><?php esc_html_e( 'Writing (Writers)', 'hello-movieengine' ); ?></option>
									</select>
								</div>
								<div class="hello-movieengine-builder-field">
									<label><?php esc_html_e( 'Order By', 'hello-movieengine' ); ?></label>
									<select class="hello-movieengine-section-person-orderby">
										<option value="title" <?php selected( $person_orderby, 'title' ); ?>><?php esc_html_e( 'Name (A to Z)', 'hello-movieengine' ); ?></option>
										<option value="date" <?php selected( $person_orderby, 'date' ); ?>><?php esc_html_e( 'Recently Added', 'hello-movieengine' ); ?></option>
										<option value="rand" <?php selected( $person_orderby, 'rand' ); ?>><?php esc_html_e( 'Random', 'hello-movieengine' ); ?></option>
									</select>
								</div>
								<div class="hello-movieengine-builder-field">
									<label><?php esc_html_e( 'Card Style', 'hello-movieengine' ); ?></label>
									<select class="hello-movieengine-section-person-style">
										<option value="overlay" <?php selected( $person_style, 'overlay' ); ?>><?php esc_html_e( 'Style 1 — Gradient overlay on photo', 'hello-movieengine' ); ?></option>
										<option value="stacked" <?php selected( $person_style, 'stacked' ); ?>><?php esc_html_e( 'Style 2 — Name & role below photo', 'hello-movieengine' ); ?></option>
									</select>
								</div>
								<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-layout"></span><?php esc_html_e( 'Layout & Display', 'hello-movieengine' ); ?></div>
								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Stars Count', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-person-count" value="<?php echo esc_attr( $count ); ?>" min="3" max="30" step="1" />
									</div>
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Columns (Desktop)', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-person-columns" value="<?php echo esc_attr( $columns ); ?>" min="3" max="8" step="1" />
									</div>
								</div>
								<?php $this->render_shared_grid_header_controls( $section ); ?>
								<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
									<label>
										<input type="checkbox" class="hello-movieengine-section-person-slider" value="1" <?php checked( $slider, 1 ); ?> />
										<?php esc_html_e( 'Enable Horizontal Slider Carousel', 'hello-movieengine' ); ?>
									</label>
								</div>
								<?php
								$person_pause = ! isset( $section['person_pause'] ) || ! empty( $section['person_pause'] );
								$person_dots  = ! empty( $section['person_dots'] );
								?>
								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
										<label>
											<input type="checkbox" class="hello-movieengine-section-person-autoplay" value="1" <?php checked( $person_autoplay, 1 ); ?> />
											<?php esc_html_e( 'Auto-slide', 'hello-movieengine' ); ?>
										</label>
									</div>
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Speed (ms)', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-person-speed" value="<?php echo esc_attr( $person_speed ); ?>" min="1500" max="10000" step="500" />
									</div>
								</div>
								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
										<label>
											<input type="checkbox" class="hello-movieengine-section-person-loop" value="1" <?php checked( $person_loop, true ); ?> />
											<?php esc_html_e( 'Infinite Loop', 'hello-movieengine' ); ?>
										</label>
									</div>
									<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
										<label>
											<input type="checkbox" class="hello-movieengine-section-person-pause" value="1" <?php checked( $person_pause, true ); ?> />
											<?php esc_html_e( 'Pause on Hover', 'hello-movieengine' ); ?>
										</label>
									</div>
								</div>
								<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
									<label>
										<input type="checkbox" class="hello-movieengine-section-person-dots" value="1" <?php checked( $person_dots, true ); ?> />
										<?php esc_html_e( 'Show Dot Indicators', 'hello-movieengine' ); ?>
									</label>
								</div>
							</div>

							<!-- ==============================
							     4. Studios Fields
							     ============================== -->
							<div class="hello-movieengine-fields-studios" style="<?php echo ( 'studios' === $type ) ? '' : 'display: none;'; ?>">
								<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-tag"></span><?php esc_html_e( 'Studio Display & Layout', 'hello-movieengine' ); ?></div>

								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Layout', 'hello-movieengine' ); ?></label>
										<select class="hello-movieengine-section-studio-layout">
											<option value="grid" <?php selected( $studio_layout, 'grid' ); ?>><?php esc_html_e( 'Grid', 'hello-movieengine' ); ?></option>
											<option value="slider" <?php selected( $studio_layout, 'slider' ); ?>><?php esc_html_e( 'Slider Carousel', 'hello-movieengine' ); ?></option>
										</select>
									</div>
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Columns (Desktop)', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-studio-columns" value="<?php echo esc_attr( $studio_columns ); ?>" min="3" max="8" />
									</div>
								</div>
								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Max Studios Limit', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-studio-limit" value="<?php echo esc_attr( $studio_limit ); ?>" min="4" max="30" />
									</div>
									<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field" style="margin-top: 20px;">
										<label>
											<input type="checkbox" class="hello-movieengine-section-studio-title" value="1" <?php checked( $studio_title, 1 ); ?> />
											<?php esc_html_e( 'Show Name Text', 'hello-movieengine' ); ?>
										</label>
									</div>
								</div>
								<?php $this->render_shared_grid_header_controls( $section ); ?>
								<?php $this->render_shared_taxonomy_controls( $section, 'ASC' ); ?>
								<?php $this->render_shared_slider_controls( $section ); ?>
							</div>

							<!-- ==============================
							     5. Genres Fields
							     ============================== -->
							<div class="hello-movieengine-fields-genres" style="<?php echo ( 'genres' === $type ) ? '' : 'display: none;'; ?>">
								<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-category"></span><?php esc_html_e( 'Genre Bar Settings', 'hello-movieengine' ); ?></div>

								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Layout', 'hello-movieengine' ); ?></label>
										<select class="hello-movieengine-section-genre-layout">
											<option value="pills" <?php selected( $genre_layout, 'pills' ); ?>><?php esc_html_e( 'Pill Buttons', 'hello-movieengine' ); ?></option>
											<option value="slider" <?php selected( $genre_layout, 'slider' ); ?>><?php esc_html_e( 'Slider Carousel', 'hello-movieengine' ); ?></option>
											<option value="grid" <?php selected( $genre_layout, 'grid' ); ?>><?php esc_html_e( 'Grid Cards', 'hello-movieengine' ); ?></option>
										</select>
									</div>
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Max Genres Count', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-genre-limit" value="<?php echo esc_attr( $genre_limit ); ?>" min="4" max="30" />
									</div>
								</div>
								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Columns (Desktop)', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-genre-columns" value="<?php echo esc_attr( isset( $section['genre_columns'] ) ? absint( $section['genre_columns'] ) : 6 ); ?>" min="3" max="8" step="1" />
									</div>
								</div>
								<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
									<label>
										<input type="checkbox" class="hello-movieengine-section-genre-count" value="1" <?php checked( $genre_count, 1 ); ?> />
										<?php esc_html_e( 'Show Title Counts (e.g. Action (42))', 'hello-movieengine' ); ?>
									</label>
								</div>
								<?php $this->render_shared_grid_header_controls( $section ); ?>
								<?php $this->render_shared_taxonomy_controls( $section, 'ASC' ); ?>
								<?php $this->render_shared_slider_controls( $section ); ?>
							</div>

							<!-- ==============================
							     6. Continue Watching (History)
							     ============================== -->
							<div class="hello-movieengine-fields-history" style="<?php echo ( 'history' === $type ) ? '' : 'display: none;'; ?>">
								<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-clock"></span><?php esc_html_e( 'Continue Watching Settings', 'hello-movieengine' ); ?></div>

								<p class="description" style="font-size: 11px; color: #64748b; margin-bottom: 8px;">
									<?php esc_html_e( 'Shows unfinished movies/episodes with progress bar for logged-in users. Automatically hidden if user is a guest or history is empty.', 'hello-movieengine' ); ?>
								</p>
								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Max History Items', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-history-count" value="<?php echo esc_attr( $history_count ); ?>" min="2" max="20" />
									</div>
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Columns (Desktop)', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-history-columns" value="<?php echo esc_attr( $history_columns ); ?>" min="2" max="6" />
									</div>
								</div>
								<?php $this->render_shared_grid_header_controls( $section ); ?>
								<div class="hello-movieengine-builder-field hello-movieengine-builder-toggle-field">
									<label>
										<input type="checkbox" class="hello-movieengine-section-slider" value="1" <?php checked( $slider, 1 ); ?> />
										<?php esc_html_e( 'Slider Carousel', 'hello-movieengine' ); ?>
									</label>
								</div>
								<?php $this->render_shared_slider_controls( $section ); ?>
								<div class="hello-movieengine-builder-field">
									<label><?php esc_html_e( 'View All URL (dashboard history)', 'hello-movieengine' ); ?></label>
									<input type="url" class="hello-movieengine-section-history-view-all" value="<?php echo esc_attr( $section['history_view_all'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Optional — defaults to user dashboard history', 'hello-movieengine' ); ?>" />
								</div>
							</div>

							<!-- ==============================
							     7. Release Years Fields
							     ============================== -->
							<div class="hello-movieengine-fields-years" style="<?php echo ( 'years' === $type ) ? '' : 'display: none;'; ?>">
								<div class="hello-movieengine-builder-group-title"><span class="dashicons dashicons-calendar-alt"></span><?php esc_html_e( 'Release Years Settings', 'hello-movieengine' ); ?></div>

								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Layout', 'hello-movieengine' ); ?></label>
										<select class="hello-movieengine-section-year-layout">
											<option value="pills" <?php selected( $year_layout, 'pills' ); ?>><?php esc_html_e( 'Pills (Buttons)', 'hello-movieengine' ); ?></option>
											<option value="slider" <?php selected( $year_layout, 'slider' ); ?>><?php esc_html_e( 'Slider Carousel', 'hello-movieengine' ); ?></option>
										</select>
									</div>
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Years Count', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-year-limit" value="<?php echo esc_attr( $year_limit ); ?>" min="4" max="30" />
									</div>
								</div>
								<div class="hello-movieengine-builder-row-grid">
									<div class="hello-movieengine-builder-field">
										<label><?php esc_html_e( 'Columns (Desktop)', 'hello-movieengine' ); ?></label>
										<input type="number" class="hello-movieengine-section-year-columns" value="<?php echo esc_attr( isset( $section['year_columns'] ) ? absint( $section['year_columns'] ) : 6 ); ?>" min="3" max="8" step="1" />
									</div>
								</div>
								<?php $this->render_shared_grid_header_controls( $section ); ?>
								<?php $this->render_shared_taxonomy_controls( $section, 'DESC' ); ?>
								<?php $this->render_shared_slider_controls( $section ); ?>
							</div>

							<!-- ==============================
							     8. Custom Shortcode Field
							     ============================== -->
							<div class="hello-movieengine-fields-shortcode" style="<?php echo ( 'shortcode' === $type ) ? '' : 'display: none;'; ?>">
								<div class="hello-movieengine-builder-field">
									<label><?php esc_html_e( 'Custom Shortcode', 'hello-movieengine' ); ?></label>
									<textarea class="hello-movieengine-section-shortcode" rows="2" placeholder="[my_shortcode parameter='1']"><?php echo esc_textarea( $shortcode ); ?></textarea>
								</div>
							</div>

							<!-- ==============================
							     10. Page Content info
							     ============================== -->
							<div class="hello-movieengine-fields-page-content" style="<?php echo ( 'page_content' === $type ) ? '' : 'display: none;'; ?>">
								<p class="description" style="margin: 4px 0 8px; font-size: 11.5px; color: #64748b;">
									<?php esc_html_e( 'This block will render any text, Gutenberg blocks, or Elementor content written inside your homepage editor.', 'hello-movieengine' ); ?>
								</p>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="hello-movieengine-builder-footer">
				<button type="button" class="button hello-movieengine-builder-add-btn">
					<span class="dashicons dashicons-plus-alt2"></span> <?php esc_html_e( 'Add Section Row', 'hello-movieengine' ); ?>
				</button>
				<button type="button" class="button hello-movieengine-builder-reset-btn" title="<?php esc_attr_e( 'Reset to standard OTT feed layout', 'hello-movieengine' ); ?>">
					<?php esc_html_e( 'Reset Default', 'hello-movieengine' ); ?>
				</button>
			</div>

			<!-- Hidden input storing complete JSON configuration -->
			<input type="hidden" class="hello-movieengine-section-builder-value" <?php $this->link(); ?> value="<?php echo esc_attr( wp_json_encode( $sections ) ); ?>" />
		</div>
		<?php
	}
}
