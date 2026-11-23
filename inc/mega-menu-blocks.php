<?php
/**
 * Mega menu rich blocks (banner + movie row) for Style Two side rail.
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HELLO_MOVIEENGINE_MEGA_LAYOUT', '_hello_movieengine_mega_layout' );
define( 'HELLO_MOVIEENGINE_MEGA_BANNER_IMAGE', '_hello_movieengine_mega_banner_image' );
define( 'HELLO_MOVIEENGINE_MEGA_BANNER_TEXT', '_hello_movieengine_mega_banner_text' );
define( 'HELLO_MOVIEENGINE_MEGA_BANNER_EYEBROW', '_hello_movieengine_mega_banner_eyebrow' );
define( 'HELLO_MOVIEENGINE_MEGA_BANNER_WIDTH', '_hello_movieengine_mega_banner_width' );
define( 'HELLO_MOVIEENGINE_MEGA_BANNER_HEIGHT', '_hello_movieengine_mega_banner_height' );
define( 'HELLO_MOVIEENGINE_MEGA_MOVIES_TYPE', '_hello_movieengine_mega_movies_type' );
define( 'HELLO_MOVIEENGINE_MEGA_MOVIES_SOURCE', '_hello_movieengine_mega_movies_source' );
define( 'HELLO_MOVIEENGINE_MEGA_MOVIES_LIMIT', '_hello_movieengine_mega_movies_limit' );
define( 'HELLO_MOVIEENGINE_MEGA_MOVIES_COLS', '_hello_movieengine_mega_movies_cols' );
define( 'HELLO_MOVIEENGINE_MEGA_MOVIES_STYLE', '_hello_movieengine_mega_movies_style' );

/**
 * @return string[]
 */
function hello_movieengine_mega_layout_choices() {
	return array( 'default', 'banner', 'movies' );
}

/**
 * @param int $item_id Menu item ID.
 * @return string default|banner|movies
 */
function hello_movieengine_get_mega_block_layout( $item_id ) {
	$layout = (string) get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_LAYOUT, true );
	if ( ! in_array( $layout, hello_movieengine_mega_layout_choices(), true ) ) {
		return 'default';
	}
	return $layout;
}

/**
 * @return bool
 */
function hello_movieengine_mega_is_rich_layout( $layout ) {
	return in_array( $layout, array( 'banner', 'movies' ), true );
}

/**
 * Query titles for mega movie row.
 *
 * @param int $item_id Menu item ID.
 * @return WP_Post[]
 */
function hello_movieengine_mega_query_movies( $item_id ) {
	$type   = (string) get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_MOVIES_TYPE, true );
	$source = (string) get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_MOVIES_SOURCE, true );
	$limit  = absint( get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_MOVIES_LIMIT, true ) );

	if ( ! in_array( $type, array( 'movie_engine_movie', 'movie_engine_series' ), true ) ) {
		$type = 'movie_engine_movie';
	}

	if ( ! in_array( $source, array( 'latest', 'popular', 'top_rated' ), true ) ) {
		$source = 'latest';
	}

	if ( $limit < 2 ) {
		$limit = 4;
	}
	if ( $limit > 12 ) {
		$limit = 12;
	}

	$args = array(
		'post_type'              => $type,
		'post_status'            => 'publish',
		'posts_per_page'         => $limit,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	);

	if ( 'popular' === $source ) {
		$args['meta_key'] = '_movie_engine_view_count';
		$args['orderby']  = 'meta_value_num';
		$args['order']    = 'DESC';
	} elseif ( 'top_rated' === $source ) {
		$args['meta_key'] = '_movie_engine_rating';
		$args['orderby']  = 'meta_value_num';
		$args['order']    = 'DESC';
	} else {
		$args['orderby'] = 'date';
		$args['order']   = 'DESC';
	}

	$q = new WP_Query( $args );
	return is_array( $q->posts ) ? $q->posts : array();
}

/**
 * @param int $post_id Post ID.
 * @return string
 */
function hello_movieengine_mega_poster_url( $post_id ) {
	if ( class_exists( '\MovieEngine\Utils\Helpers' ) ) {
		return (string) \MovieEngine\Utils\Helpers::get_poster_url( $post_id, 'medium' );
	}
	if ( has_post_thumbnail( $post_id ) ) {
		return (string) get_the_post_thumbnail_url( $post_id, 'medium' );
	}
	return '';
}

/**
 * @return string[]
 */
function hello_movieengine_mega_movies_style_choices() {
	return array( 'landscape', 'poster' );
}

/**
 * @param int $post_id Post ID.
 * @return string
 */
function hello_movieengine_mega_movie_quality( $post_id ) {
	$tax  = 'movie_engine_series' === get_post_type( $post_id ) ? 'movie_engine_tv_quality' : 'movie_engine_quality';
	$terms = get_the_terms( $post_id, $tax );
	if ( $terms && ! is_wp_error( $terms ) && ! empty( $terms[0]->name ) ) {
		return $terms[0]->name;
	}
	return __( 'HD', 'hello-movieengine' );
}

/**
 * @param int $post_id Post ID.
 * @return string
 */
function hello_movieengine_mega_movie_year( $post_id ) {
	$date = (string) get_post_meta( $post_id, '_movie_engine_release_date', true );
	if ( $date && preg_match( '/^(\d{4})/', $date, $m ) ) {
		return $m[1];
	}
	return get_the_date( 'Y', $post_id );
}

/**
 * @param int $post_id Post ID.
 * @return string
 */
function hello_movieengine_mega_movie_rating( $post_id ) {
	if ( class_exists( '\MovieEngine\Utils\Helpers' ) ) {
		$rating = \MovieEngine\Utils\Helpers::get_movie_rating( $post_id );
	} else {
		$rating = get_post_meta( $post_id, '_movie_engine_rating', true );
	}
	if ( '' === $rating || false === $rating || null === $rating ) {
		return '';
	}
	return is_numeric( $rating ) ? number_format( (float) $rating, 3 ) : (string) $rating;
}

/**
 * Render promo banner block (depth-1 under mega parent).
 *
 * @param WP_Post $item            Menu item.
 * @param string  $parent_label    Top-level mega parent label (eyebrow fallback).
 */
function hello_movieengine_render_mega_banner_block( $item, $parent_label = '' ) {
	$image_id = absint( get_post_meta( $item->ID, HELLO_MOVIEENGINE_MEGA_BANNER_IMAGE, true ) );
	$text     = (string) get_post_meta( $item->ID, HELLO_MOVIEENGINE_MEGA_BANNER_TEXT, true );
	$eyebrow  = (string) get_post_meta( $item->ID, HELLO_MOVIEENGINE_MEGA_BANNER_EYEBROW, true );
	$width    = absint( get_post_meta( $item->ID, HELLO_MOVIEENGINE_MEGA_BANNER_WIDTH, true ) );
	$height   = absint( get_post_meta( $item->ID, HELLO_MOVIEENGINE_MEGA_BANNER_HEIGHT, true ) );
	$title    = $item->title;
	$url      = $item->url;
	$image    = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '';

	if ( $width && ( $width < 120 || $width > 560 ) ) {
		$width = 220;
	}
	if ( $height && ( $height < 160 || $height > 800 ) ) {
		$height = 0;
	}
	if ( ! $width ) {
		$width = 220;
	}

	$inline = array();
	if ( $image ) {
		$inline[] = 'background-image:url(' . esc_url( $image ) . ')';
	}
	$inline[] = '--hm-mega-banner-w:' . (int) $width . 'px';
	if ( $height ) {
		$inline[] = '--hm-mega-banner-h:' . (int) $height . 'px';
	}
	$bg_style = ' style="' . esc_attr( implode( ';', $inline ) ) . ';"';

	$banner_class = 'hello-movieengine-mega-banner';
	if ( $height ) {
		$banner_class .= ' hello-movieengine-mega-banner--fixed';
	} else {
		$banner_class .= ' hello-movieengine-mega-banner--stretch';
	}

	if ( ! $text && ! empty( $item->description ) ) {
		$text = $item->description;
	}
	if ( '' === $eyebrow && $parent_label ) {
		$eyebrow = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $parent_label ) : strtoupper( $parent_label );
	}
	?>
	<article class="<?php echo esc_attr( $banner_class ); ?>"<?php echo $bg_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<span class="hello-movieengine-mega-banner__shade" aria-hidden="true"></span>
		<div class="hello-movieengine-mega-banner__body">
			<?php if ( $eyebrow ) : ?>
				<p class="hello-movieengine-mega-banner__eyebrow">
					<span class="hello-movieengine-mega-banner__eyebrow-mark" aria-hidden="true"></span>
					<?php echo esc_html( $eyebrow ); ?>
				</p>
			<?php endif; ?>
			<?php if ( $title ) : ?>
				<h3 class="hello-movieengine-mega-banner__title"><?php echo esc_html( $title ); ?></h3>
			<?php endif; ?>
			<?php if ( $text ) : ?>
				<p class="hello-movieengine-mega-banner__text"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
			<?php if ( $url && '#' !== $url ) : ?>
				<a class="hello-movieengine-mega-banner__cta" href="<?php echo esc_url( $url ); ?>">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: section name */
							__( 'Browse All %s', 'hello-movieengine' ),
							$parent_label ? $parent_label : ( $title ? $title : __( 'Titles', 'hello-movieengine' ) )
						)
					);
					?>
					<span class="hello-movieengine-mega-banner__cta-arrow" aria-hidden="true">›</span>
				</a>
			<?php endif; ?>
		</div>
	</article>
	<?php
}

/**
 * Render trending / latest movie cards row.
 *
 * @param WP_Post $item Menu item.
 */
function hello_movieengine_render_mega_movies_block( $item ) {
	$posts   = hello_movieengine_mega_query_movies( $item->ID );
	$title   = $item->title ? $item->title : __( 'Trending', 'hello-movieengine' );
	$view    = ( $item->url && '#' !== $item->url ) ? $item->url : '';
	$is_tv   = 'movie_engine_series' === get_post_meta( $item->ID, HELLO_MOVIEENGINE_MEGA_MOVIES_TYPE, true );
	$style   = (string) get_post_meta( $item->ID, HELLO_MOVIEENGINE_MEGA_MOVIES_STYLE, true );
	$cols    = absint( get_post_meta( $item->ID, HELLO_MOVIEENGINE_MEGA_MOVIES_COLS, true ) );

	if ( ! in_array( $style, hello_movieengine_mega_movies_style_choices(), true ) ) {
		$style = 'landscape';
	}
	if ( $cols < 2 || $cols > 6 ) {
		$cols = 4;
	}

	$wrap_cl = 'hello-movieengine-mega-movies hello-movieengine-mega-movies--' . sanitize_html_class( $style );
	$type_label = $is_tv ? __( 'Series', 'hello-movieengine' ) : __( 'Movie', 'hello-movieengine' );
	?>
	<div class="<?php echo esc_attr( $wrap_cl ); ?>" style="--hm-mega-movies-cols:<?php echo (int) $cols; ?>;">
		<div class="hello-movieengine-mega-movies__hd">
			<h4 class="hello-movieengine-mega-movies__title"><?php echo esc_html( $title ); ?></h4>
			<?php if ( $view ) : ?>
				<a class="hello-movieengine-mega-movies__all" href="<?php echo esc_url( $view ); ?>">
					<?php esc_html_e( 'View All', 'hello-movieengine' ); ?>
					<span aria-hidden="true">→</span>
				</a>
			<?php endif; ?>
		</div>
		<?php if ( $posts ) : ?>
			<div class="hello-movieengine-mega-movies__grid" data-count="<?php echo esc_attr( (string) count( $posts ) ); ?>">
				<?php foreach ( $posts as $post ) : ?>
					<?php
					$poster  = hello_movieengine_mega_poster_url( $post->ID );
					$plink   = get_permalink( $post );
					$quality = hello_movieengine_mega_movie_quality( $post->ID );
					$year    = hello_movieengine_mega_movie_year( $post->ID );
					$rating  = hello_movieengine_mega_movie_rating( $post->ID );
					$name    = get_the_title( $post );
					?>
					<a class="hello-movieengine-mega-movies__card" href="<?php echo esc_url( $plink ); ?>">
						<span class="hello-movieengine-mega-movies__thumb"<?php echo $poster ? ' style="background-image:url(' . esc_url( $poster ) . ');"' : ''; ?>>
							<?php if ( $quality ) : ?>
								<span class="hello-movieengine-mega-movies__badge"><?php echo esc_html( $quality ); ?></span>
							<?php endif; ?>
							<span class="hello-movieengine-mega-movies__play" aria-hidden="true"></span>
							<?php if ( 'poster' === $style && $rating ) : ?>
								<span class="hello-movieengine-mega-movies__rating">★ <?php echo esc_html( $rating ); ?></span>
							<?php endif; ?>
							<?php if ( 'landscape' === $style ) : ?>
								<span class="hello-movieengine-mega-movies__name"><?php echo esc_html( $name ); ?></span>
							<?php endif; ?>
						</span>
						<?php if ( 'poster' === $style ) : ?>
							<span class="hello-movieengine-mega-movies__meta">
								<span class="hello-movieengine-mega-movies__name"><?php echo esc_html( $name ); ?></span>
								<span class="hello-movieengine-mega-movies__sub">
									<span><?php echo esc_html( $year ); ?></span>
									<span><?php echo esc_html( $type_label ); ?></span>
								</span>
							</span>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="hello-movieengine-mega-movies__empty">
				<?php esc_html_e( 'No titles found. Publish movies or series with Cine Movie Engine.', 'hello-movieengine' ); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Extra fields for submenu items (banner / movies).
 *
 * @param int      $item_id Item ID.
 * @param WP_Post  $item    Item.
 * @param int      $depth   Depth.
 * @param stdClass $args    Args.
 * @param int      $id      Menu ID.
 */
function hello_movieengine_mega_block_nav_fields( $item_id, $item, $depth, $args, $id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	if ( (int) $depth < 1 ) {
		return;
	}

	$layout    = hello_movieengine_get_mega_block_layout( $item_id );
	$image_id  = absint( get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_BANNER_IMAGE, true ) );
	$banner_tx = (string) get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_BANNER_TEXT, true );
	$eyebrow   = (string) get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_BANNER_EYEBROW, true );
	$ban_w     = absint( get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_BANNER_WIDTH, true ) );
	$ban_h     = absint( get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_BANNER_HEIGHT, true ) );
	$mov_type  = (string) get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_MOVIES_TYPE, true );
	$mov_src   = (string) get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_MOVIES_SOURCE, true );
	$mov_limit = absint( get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_MOVIES_LIMIT, true ) );
	$mov_cols  = absint( get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_MOVIES_COLS, true ) );
	$mov_style = (string) get_post_meta( $item_id, HELLO_MOVIEENGINE_MEGA_MOVIES_STYLE, true );

	if ( ! in_array( $mov_type, array( 'movie_engine_movie', 'movie_engine_series' ), true ) ) {
		$mov_type = 'movie_engine_movie';
	}
	if ( ! in_array( $mov_src, array( 'latest', 'popular', 'top_rated' ), true ) ) {
		$mov_src = 'latest';
	}
	if ( $mov_limit < 2 || $mov_limit > 12 ) {
		$mov_limit = 4;
	}
	if ( $mov_cols < 2 || $mov_cols > 6 ) {
		$mov_cols = 4;
	}
	if ( ! in_array( $mov_style, hello_movieengine_mega_movies_style_choices(), true ) ) {
		$mov_style = 'landscape';
	}

	$preview = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
	?>
	<p class="field-mega-layout description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--child hello-movieengine-mega-block-field">
		<label for="edit-menu-item-mega-layout-<?php echo esc_attr( (string) $item_id ); ?>">
			<?php esc_html_e( 'Mega panel block', 'hello-movieengine' ); ?><br />
			<select
				id="edit-menu-item-mega-layout-<?php echo esc_attr( (string) $item_id ); ?>"
				class="hello-movieengine-mega-layout"
				name="menu-item-mega-layout[<?php echo esc_attr( (string) $item_id ); ?>]"
			>
				<option value="default" <?php selected( $layout, 'default' ); ?>><?php esc_html_e( 'Link column (default)', 'hello-movieengine' ); ?></option>
				<option value="banner" <?php selected( $layout, 'banner' ); ?>><?php esc_html_e( 'Promo banner', 'hello-movieengine' ); ?></option>
				<option value="movies" <?php selected( $layout, 'movies' ); ?>><?php esc_html_e( 'Movie / series row', 'hello-movieengine' ); ?></option>
			</select>
		</label>
		<span class="description" style="display:block;margin-top:4px;">
			<?php esc_html_e( 'Reference layout: Promo banner (left) + link columns (top), Trending row full width at the bottom. Put Movie row last.', 'hello-movieengine' ); ?>
		</span>
	</p>

	<div class="hello-movieengine-mega-banner-panel hello-movieengine-mega-block-panel" data-layout="banner"<?php echo 'banner' === $layout ? '' : ' style="display:none;"'; ?>>
		<p class="field-mega-banner-eyebrow description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--child">
			<label for="edit-menu-item-mega-banner-eyebrow-<?php echo esc_attr( (string) $item_id ); ?>">
				<?php esc_html_e( 'Banner eyebrow (e.g. TV SERIES)', 'hello-movieengine' ); ?><br />
				<input
					type="text"
					class="widefat"
					id="edit-menu-item-mega-banner-eyebrow-<?php echo esc_attr( (string) $item_id ); ?>"
					name="menu-item-mega-banner-eyebrow[<?php echo esc_attr( (string) $item_id ); ?>]"
					value="<?php echo esc_attr( $eyebrow ); ?>"
					placeholder="<?php esc_attr_e( 'Optional — defaults to parent menu title', 'hello-movieengine' ); ?>"
				/>
			</label>
		</p>
		<p class="field-mega-banner-size description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--child">
			<span class="hello-movieengine-mega-banner-size-fields">
				<label for="edit-menu-item-mega-banner-width-<?php echo esc_attr( (string) $item_id ); ?>">
					<?php esc_html_e( 'Banner width (px)', 'hello-movieengine' ); ?><br />
					<input
						type="number"
						class="small-text"
						min="120"
						max="560"
						step="10"
						id="edit-menu-item-mega-banner-width-<?php echo esc_attr( (string) $item_id ); ?>"
						name="menu-item-mega-banner-width[<?php echo esc_attr( (string) $item_id ); ?>]"
						value="<?php echo $ban_w ? esc_attr( (string) $ban_w ) : ''; ?>"
						placeholder="220"
					/>
				</label>
				<label for="edit-menu-item-mega-banner-height-<?php echo esc_attr( (string) $item_id ); ?>">
					<?php esc_html_e( 'Banner height (px)', 'hello-movieengine' ); ?><br />
					<input
						type="number"
						class="small-text"
						min="160"
						max="800"
						step="10"
						id="edit-menu-item-mega-banner-height-<?php echo esc_attr( (string) $item_id ); ?>"
						name="menu-item-mega-banner-height[<?php echo esc_attr( (string) $item_id ); ?>]"
						value="<?php echo $ban_h ? esc_attr( (string) $ban_h ) : ''; ?>"
						placeholder="<?php esc_attr_e( 'Auto', 'hello-movieengine' ); ?>"
					/>
				</label>
			</span>
			<span class="description" style="display:block;margin-top:4px;">
				<?php esc_html_e( 'Width 120–560. Height 160–800, or leave height empty to stretch with the panel.', 'hello-movieengine' ); ?>
			</span>
		</p>
		<p class="field-mega-banner-text description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--child">
			<label for="edit-menu-item-mega-banner-text-<?php echo esc_attr( (string) $item_id ); ?>">
				<?php esc_html_e( 'Banner description', 'hello-movieengine' ); ?><br />
				<textarea
					id="edit-menu-item-mega-banner-text-<?php echo esc_attr( (string) $item_id ); ?>"
					name="menu-item-mega-banner-text[<?php echo esc_attr( (string) $item_id ); ?>]"
					rows="3"
					class="widefat"
				><?php echo esc_textarea( $banner_tx ); ?></textarea>
			</label>
		</p>
		<p class="field-mega-banner-image description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--child">
			<span class="hello-movieengine-mega-banner-preview" style="display:block;margin:6px 0;min-height:48px;">
				<?php if ( $preview ) : ?>
					<img src="<?php echo esc_url( $preview ); ?>" alt="" style="max-width:120px;height:auto;border-radius:6px;" />
				<?php endif; ?>
			</span>
			<input
				type="hidden"
				class="hello-movieengine-mega-banner-image-id"
				name="menu-item-mega-banner-image[<?php echo esc_attr( (string) $item_id ); ?>]"
				value="<?php echo esc_attr( (string) $image_id ); ?>"
			/>
			<button type="button" class="button hello-movieengine-mega-banner-pick"><?php esc_html_e( 'Banner image', 'hello-movieengine' ); ?></button>
			<button type="button" class="button hello-movieengine-mega-banner-clear"<?php echo $image_id ? '' : ' style="display:none;"'; ?>><?php esc_html_e( 'Remove', 'hello-movieengine' ); ?></button>
		</p>
	</div>

	<div class="hello-movieengine-mega-movies-panel hello-movieengine-mega-block-panel" data-layout="movies"<?php echo 'movies' === $layout ? '' : ' style="display:none;"'; ?>>
		<p class="description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--child">
			<label>
				<?php esc_html_e( 'Content type', 'hello-movieengine' ); ?><br />
				<select name="menu-item-mega-movies-type[<?php echo esc_attr( (string) $item_id ); ?>]">
					<option value="movie_engine_movie" <?php selected( $mov_type, 'movie_engine_movie' ); ?>><?php esc_html_e( 'Movies', 'hello-movieengine' ); ?></option>
					<option value="movie_engine_series" <?php selected( $mov_type, 'movie_engine_series' ); ?>><?php esc_html_e( 'TV Series', 'hello-movieengine' ); ?></option>
				</select>
			</label>
		</p>
		<p class="description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--child">
			<label>
				<?php esc_html_e( 'Source', 'hello-movieengine' ); ?><br />
				<select name="menu-item-mega-movies-source[<?php echo esc_attr( (string) $item_id ); ?>]">
					<option value="latest" <?php selected( $mov_src, 'latest' ); ?>><?php esc_html_e( 'Latest', 'hello-movieengine' ); ?></option>
					<option value="popular" <?php selected( $mov_src, 'popular' ); ?>><?php esc_html_e( 'Most viewed', 'hello-movieengine' ); ?></option>
					<option value="top_rated" <?php selected( $mov_src, 'top_rated' ); ?>><?php esc_html_e( 'Top rated', 'hello-movieengine' ); ?></option>
				</select>
			</label>
		</p>
		<p class="description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--child">
			<label>
				<?php esc_html_e( 'Columns', 'hello-movieengine' ); ?><br />
				<select name="menu-item-mega-movies-cols[<?php echo esc_attr( (string) $item_id ); ?>]">
					<?php foreach ( range( 2, 6 ) as $n ) : ?>
						<option value="<?php echo esc_attr( (string) $n ); ?>" <?php selected( $mov_cols, $n ); ?>>
							<?php
							/* translators: %d: number of columns */
							echo esc_html( sprintf( _n( '%d column', '%d columns', $n, 'hello-movieengine' ), $n ) );
							?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
		</p>
		<p class="description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--child">
			<label>
				<?php esc_html_e( 'Card style', 'hello-movieengine' ); ?><br />
				<select name="menu-item-mega-movies-style[<?php echo esc_attr( (string) $item_id ); ?>]">
					<option value="landscape" <?php selected( $mov_style, 'landscape' ); ?>><?php esc_html_e( 'Landscape (title on poster)', 'hello-movieengine' ); ?></option>
					<option value="poster" <?php selected( $mov_style, 'poster' ); ?>><?php esc_html_e( 'Poster card (quality, play, rating)', 'hello-movieengine' ); ?></option>
				</select>
			</label>
			<span class="description" style="display:block;margin-top:4px;">
				<?php esc_html_e( 'Poster card matches the library movie card: quality badge, play icon, rating, year.', 'hello-movieengine' ); ?>
			</span>
		</p>
		<p class="description description-wide hello-movieengine-mega-field hello-movieengine-mega-field--child">
			<label>
				<?php esc_html_e( 'Number of posters', 'hello-movieengine' ); ?><br />
				<input
					type="number"
					class="small-text"
					min="2"
					max="12"
					name="menu-item-mega-movies-limit[<?php echo esc_attr( (string) $item_id ); ?>]"
					value="<?php echo esc_attr( (string) $mov_limit ); ?>"
				/>
			</label>
		</p>
	</div>
	<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'hello_movieengine_mega_block_nav_fields', 12, 5 );

/**
 * Persist block settings.
 *
 * @param int   $menu_id         Menu ID.
 * @param int   $menu_item_db_id Item ID.
 * @param array $args            Args.
 */
function hello_movieengine_save_mega_block_fields( $menu_id, $menu_item_db_id, $args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	if ( ! isset( $_POST['menu-item-db-id'] ) || ! is_array( $_POST['menu-item-db-id'] ) ) {
		return;
	}

	$layout = isset( $_POST['menu-item-mega-layout'][ $menu_item_db_id ] )
		? sanitize_key( wp_unslash( $_POST['menu-item-mega-layout'][ $menu_item_db_id ] ) )
		: 'default';

	if ( ! in_array( $layout, hello_movieengine_mega_layout_choices(), true ) ) {
		$layout = 'default';
	}

	update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_LAYOUT, $layout );

	$image = isset( $_POST['menu-item-mega-banner-image'][ $menu_item_db_id ] )
		? absint( wp_unslash( $_POST['menu-item-mega-banner-image'][ $menu_item_db_id ] ) )
		: 0;
	$text  = isset( $_POST['menu-item-mega-banner-text'][ $menu_item_db_id ] )
		? sanitize_textarea_field( wp_unslash( $_POST['menu-item-mega-banner-text'][ $menu_item_db_id ] ) )
		: '';
	$eyebrow = isset( $_POST['menu-item-mega-banner-eyebrow'][ $menu_item_db_id ] )
		? sanitize_text_field( wp_unslash( $_POST['menu-item-mega-banner-eyebrow'][ $menu_item_db_id ] ) )
		: '';
	$ban_w = isset( $_POST['menu-item-mega-banner-width'][ $menu_item_db_id ] )
		? absint( wp_unslash( $_POST['menu-item-mega-banner-width'][ $menu_item_db_id ] ) )
		: 0;
	$ban_h = isset( $_POST['menu-item-mega-banner-height'][ $menu_item_db_id ] )
		? absint( wp_unslash( $_POST['menu-item-mega-banner-height'][ $menu_item_db_id ] ) )
		: 0;

	if ( $ban_w && ( $ban_w < 120 || $ban_w > 560 ) ) {
		$ban_w = 200;
	}
	if ( $ban_h && ( $ban_h < 160 || $ban_h > 800 ) ) {
		$ban_h = 0;
	}

	if ( 'banner' === $layout ) {
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_BANNER_IMAGE, (string) $image );
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_BANNER_TEXT, $text );
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_BANNER_EYEBROW, $eyebrow );
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_BANNER_WIDTH, (string) $ban_w );
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_BANNER_HEIGHT, (string) $ban_h );
	} else {
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_BANNER_IMAGE );
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_BANNER_TEXT );
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_BANNER_EYEBROW );
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_BANNER_WIDTH );
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_BANNER_HEIGHT );
	}

	$type = isset( $_POST['menu-item-mega-movies-type'][ $menu_item_db_id ] )
		? sanitize_key( wp_unslash( $_POST['menu-item-mega-movies-type'][ $menu_item_db_id ] ) )
		: 'movie_engine_movie';
	$src  = isset( $_POST['menu-item-mega-movies-source'][ $menu_item_db_id ] )
		? sanitize_key( wp_unslash( $_POST['menu-item-mega-movies-source'][ $menu_item_db_id ] ) )
		: 'latest';
	$lim  = isset( $_POST['menu-item-mega-movies-limit'][ $menu_item_db_id ] )
		? absint( wp_unslash( $_POST['menu-item-mega-movies-limit'][ $menu_item_db_id ] ) )
		: 4;
	$cols = isset( $_POST['menu-item-mega-movies-cols'][ $menu_item_db_id ] )
		? absint( wp_unslash( $_POST['menu-item-mega-movies-cols'][ $menu_item_db_id ] ) )
		: 4;
	$style = isset( $_POST['menu-item-mega-movies-style'][ $menu_item_db_id ] )
		? sanitize_key( wp_unslash( $_POST['menu-item-mega-movies-style'][ $menu_item_db_id ] ) )
		: 'landscape';

	if ( ! in_array( $type, array( 'movie_engine_movie', 'movie_engine_series' ), true ) ) {
		$type = 'movie_engine_movie';
	}
	if ( ! in_array( $src, array( 'latest', 'popular', 'top_rated' ), true ) ) {
		$src = 'latest';
	}
	if ( ! in_array( $style, hello_movieengine_mega_movies_style_choices(), true ) ) {
		$style = 'landscape';
	}
	$lim  = max( 2, min( 12, $lim ) );
	$cols = max( 2, min( 6, $cols ) );

	if ( 'movies' === $layout ) {
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_MOVIES_TYPE, $type );
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_MOVIES_SOURCE, $src );
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_MOVIES_LIMIT, (string) $lim );
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_MOVIES_COLS, (string) $cols );
		update_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_MOVIES_STYLE, $style );
	} else {
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_MOVIES_TYPE );
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_MOVIES_SOURCE );
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_MOVIES_LIMIT );
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_MOVIES_COLS );
		delete_post_meta( $menu_item_db_id, HELLO_MOVIEENGINE_MEGA_MOVIES_STYLE );
	}
}
add_action( 'wp_update_nav_menu_item', 'hello_movieengine_save_mega_block_fields', 11, 3 );

/**
 * Extra admin JS for block fields (layout toggles + banner media).
 *
 * @param string $hook_suffix Hook.
 */
function hello_movieengine_mega_blocks_admin_assets( $hook_suffix ) {
	if ( 'nav-menus.php' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_media();

	$js = <<<'JS'
(function ($) {
	function toggleBlockPanels($item) {
		var layout = $item.find('.hello-movieengine-mega-layout').val() || 'default';
		$item.find('.hello-movieengine-mega-block-panel').hide();
		$item.find('.hello-movieengine-mega-block-panel[data-layout="' + layout + '"]').show();
		if (layout === 'banner' || layout === 'movies') {
			$item.find('.field-mega-heading').hide();
		} else {
			$item.find('.field-mega-heading').show();
		}
	}

	$(document).on('change', '.hello-movieengine-mega-layout', function () {
		toggleBlockPanels($(this).closest('.menu-item'));
	});

	$(function () {
		$('#menu-to-edit .menu-item').each(function () {
			toggleBlockPanels($(this));
		});
	});

	$(document).on('menu-item-added', function (e, $menuItem) {
		if ($menuItem && $menuItem.length) {
			toggleBlockPanels($menuItem);
		}
	});

	$(document).on('click', '.hello-movieengine-mega-banner-pick', function (e) {
		e.preventDefault();
		var $btn = $(this);
		var $wrap = $btn.closest('.field-mega-banner-image');
		var $input = $wrap.find('.hello-movieengine-mega-banner-image-id');
		var $preview = $wrap.find('.hello-movieengine-mega-banner-preview');
		var $clear = $wrap.find('.hello-movieengine-mega-banner-clear');

		var frame = wp.media({
			title: 'Banner image',
			button: { text: 'Use image' },
			library: { type: 'image' },
			multiple: false
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			$input.val(attachment.id);
			$preview.html('<img src="' + (attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url) + '" alt="" style="max-width:120px;height:auto;border-radius:6px;" />');
			$clear.show();
		});

		frame.open();
	});

	$(document).on('click', '.hello-movieengine-mega-banner-clear', function (e) {
		e.preventDefault();
		var $wrap = $(this).closest('.field-mega-banner-image');
		$wrap.find('.hello-movieengine-mega-banner-image-id').val('');
		$wrap.find('.hello-movieengine-mega-banner-preview').empty();
		$(this).hide();
	});
})(jQuery);
JS;

	wp_add_inline_script( 'nav-menu', $js );
}
add_action( 'admin_enqueue_scripts', 'hello_movieengine_mega_blocks_admin_assets' );