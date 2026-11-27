<?php
/**
 * The template for displaying the front page / home feed.
 *
 * Supports both Dynamic OTT Feed Builder (Drag & Drop) and
 * Classic Page Content (from WordPress Editor / Elementor),
 * working seamlessly across both Style One and Style Two.
 *
 * @package Hello_Movie Engine
 * @since 1.2.0
 */

get_header();

$hello_me_home_mode    = get_theme_mod( 'hello_movieengine_home_mode', 'editor' );
$hello_me_is_style2    = 'style-two' === hello_movieengine_get_active_style();
$hello_me_padding_zero = get_theme_mod( 'hello_movieengine_feed_main_padding_zero', true );
$hello_me_max_width    = absint( get_theme_mod( 'hello_movieengine_feed_container_width', 0 ) );

$main_classes = array( 'site-main', 'hello-me-home-feed' );
if ( $hello_me_padding_zero ) {
	$main_classes[] = 'hello-me-padding-zero';
}

$main_styles = array();
if ( $hello_me_padding_zero ) {
	$main_styles[] = 'padding-top: 0; padding-bottom: 0;';
}
if ( $hello_me_max_width > 0 ) {
	$main_styles[] = 'max-width: ' . $hello_me_max_width . 'px; margin-left: auto; margin-right: auto;';
}
$style_attr = ! empty( $main_styles ) ? ' style="' . esc_attr( implode( ' ', $main_styles ) ) . '"' : '';
?>

<main id="primary" class="<?php echo esc_attr( implode( ' ', $main_classes ) ); ?>"<?php echo $style_attr; ?>>
	<?php if ( 'builder' === $hello_me_home_mode || 'hybrid' === $hello_me_home_mode ) : ?>

		<?php
		$raw_sections = get_theme_mod( 'hello_movieengine_home_sections', '' );
		$sections     = json_decode( (string) $raw_sections, true );

		if ( ! is_array( $sections ) || empty( $sections ) ) {
			if ( class_exists( 'Hello_Movie_Engine_Customize_Section_Builder_Control' ) ) {
				$sections = Hello_Movie_Engine_Customize_Section_Builder_Control::get_default_sections();
			} else {
				$sections = array();
			}
		}

		$page_rendered = false;

		foreach ( $sections as $sec ) {
			if ( isset( $sec['enabled'] ) && ! $sec['enabled'] ) {
				continue;
			}

			$sec_type  = $sec['type'] ?? 'library';
			if ( 'live_search' === $sec_type ) {
				continue;
			}
			$sec_title = trim( $sec['title'] ?? '' );
			$title_attr = ! empty( $sec_title ) ? sprintf( ' title="%s"', esc_attr( $sec_title ) ) : '';

			switch ( $sec_type ) {
				case 'hero':
					$hero_style     = $sec['hero_style'] ?? 'style1';
					$hero_query     = class_exists( 'Hello_Movie_Engine_Customize_Section_Builder_Control' )
						? Hello_Movie_Engine_Customize_Section_Builder_Control::hero_query_shortcode_parts( $sec )
						: array(
							'type'  => $sec['hero_type'] ?? 'trending',
							'extra' => '',
						);
					$hero_type      = $hero_query['type'];
					$hero_height    = $sec['hero_height'] ?? '70vh';
					$hero_content_width = 'box';
					if ( ! empty( $sec['hero_content_width'] ) && 'full' === $sec['hero_content_width'] ) {
						$hero_content_width = 'full';
					} elseif ( ! empty( $sec['hero_width'] ) && 'full' === $sec['hero_width'] ) {
						$hero_content_width = 'full';
					}
					$hero_content_max = absint( $sec['hero_content_max_width'] ?? 0 );
					if ( ! $hero_content_max && ! empty( $sec['hero_box_width'] ) ) {
						$hero_content_max = absint( $sec['hero_box_width'] );
					}
					$hero_fullwidth = ( 'full' === $hero_content_width ) ? 'true' : 'false';
					$hero_limit     = absint( $sec['hero_limit'] ?? 5 );
					$hero_post_type = $sec['hero_post_type'] ?? 'movie_engine_movie,movie_engine_series';
					$hero_ids       = trim( $sec['hero_ids'] ?? '' );
					$hero_genre     = trim( $sec['hero_genre'] ?? '' );
					$hero_year      = trim( $sec['hero_year'] ?? '' );
					$hero_speed     = absint( $sec['hero_speed'] ?? 5000 );
					$autoplay       = ! empty( $sec['autoplay'] ) ? 'true' : 'false';
					$hero_loop      = ! isset( $sec['hero_loop'] ) || ! empty( $sec['hero_loop'] ) ? 'true' : 'false';
					$hero_pause     = ! isset( $sec['hero_pause'] ) || ! empty( $sec['hero_pause'] ) ? 'true' : 'false';
					$hero_nav       = ! isset( $sec['hero_nav'] ) || ! empty( $sec['hero_nav'] ) ? 'true' : 'false';
					$hero_dots      = ! isset( $sec['hero_dots'] ) || ! empty( $sec['hero_dots'] ) ? 'true' : 'false';
					$show_trailer   = ! isset( $sec['show_trailer'] ) || ! empty( $sec['show_trailer'] ) ? 'true' : 'false';
					$show_watchlist = ! isset( $sec['show_watchlist'] ) || ! empty( $sec['show_watchlist'] ) ? 'true' : 'false';
					$show_imdb      = ! isset( $sec['show_imdb'] ) || ! empty( $sec['show_imdb'] ) ? 'true' : 'false';
					$show_desc      = ! isset( $sec['show_desc'] ) || ! empty( $sec['show_desc'] ) ? 'true' : 'false';

					$hero_extra = '';
					if ( ! empty( $hero_ids ) ) {
						$hero_extra .= sprintf( ' ids="%s"', esc_attr( $hero_ids ) );
					}
					if ( ! empty( $hero_genre ) ) {
						$hero_extra .= sprintf( ' genre="%s"', esc_attr( $hero_genre ) );
					}
					if ( ! empty( $hero_year ) ) {
						$hero_extra .= sprintf( ' year="%s"', esc_attr( $hero_year ) );
					}
					$hero_extra .= $hero_query['extra'];

					$scope_style = '';
					if ( 'box' === $hero_content_width && $hero_content_max > 0 ) {
						$scope_style = ' style="' . esc_attr( '--hello-me-hero-content-max:' . $hero_content_max . 'px' ) . '"';
					}
					echo '<div class="hello-me-hero-content-scope hello-me-hero-content-scope--' . esc_attr( $hero_content_width ) . '"' . $scope_style . '>';
					echo do_shortcode( sprintf(
						'[movie_engine_hero type="%s" style="%s" height="%s" limit="%d" post_type="%s" fullwidth="%s" autoplay="%s" autoplay_speed="%d" loop="%s" pause_on_hover="%s" show_nav="%s" show_dots="%s" show_imdb_badge="%s" show_description="%s" show_trailer="%s" show_watchlist="%s"%s]',
						esc_attr( $hero_type ),
						esc_attr( $hero_style ),
						esc_attr( $hero_height ),
						$hero_limit,
						esc_attr( $hero_post_type ),
						esc_attr( $hero_fullwidth ),
						esc_attr( $autoplay ),
						$hero_speed,
						esc_attr( $hero_loop ),
						esc_attr( $hero_pause ),
						esc_attr( $hero_nav ),
						esc_attr( $hero_dots ),
						esc_attr( $show_imdb ),
						esc_attr( $show_desc ),
						esc_attr( $show_trailer ),
						esc_attr( $show_watchlist ),
						$hero_extra
					) );
					echo '</div>';
					break;

				case 'studios':
					$studio_layout  = $sec['studio_layout'] ?? 'grid';
					$studio_columns = absint( $sec['studio_columns'] ?? 6 );
					$studio_limit   = absint( $sec['studio_limit'] ?? 12 );
					$studio_title   = ! empty( $sec['studio_title'] ) ? 'true' : 'false';
					$studio_extra   = '';
					if ( class_exists( 'Hello_Movie_Engine_Customize_Section_Builder_Control' ) ) {
						$studio_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_responsive_columns_shortcode_attrs( $sec );
						$studio_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_gap_shortcode_attr( $sec );
						$studio_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_view_all_shortcode_attrs( $sec, false );
						$studio_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_taxonomy_pick_shortcode_attrs( $sec );
						$studio_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_logo_ratio_shortcode_attrs( $sec );
						if ( 'slider' === $studio_layout ) {
							$studio_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_slider_shortcode_attrs( $sec );
						}
					}
					echo do_shortcode( sprintf(
						'[movie_engine_studios layout="%s" columns="%d" limit="%d" show_title="%s"%s%s]',
						esc_attr( $studio_layout ),
						$studio_columns,
						$studio_limit,
						esc_attr( $studio_title ),
						$studio_extra,
						$title_attr
					) );
					break;

				case 'top10':
					$count        = absint( $sec['count'] ?? 10 );
					$columns      = absint( $sec['columns'] ?? 6 );
					$slider       = ! empty( $sec['slider'] ) ? 'slider' : 'grid';
					$post_type    = $sec['post_type'] ?? 'movie_engine_movie';
					$autoplay     = ! empty( $sec['slider_autoplay'] ) ? 'true' : 'false';
					$slider_speed = absint( $sec['slider_speed'] ?? 3000 );
					$slider_loop  = ! empty( $sec['slider_loop'] ) ? 'true' : 'false';
					$slider_pause = ! isset( $sec['slider_pause'] ) || ! empty( $sec['slider_pause'] ) ? 'true' : 'false';
					$slider_dots  = ! empty( $sec['slider_dots'] ) ? 'true' : 'false';
					$top10_query  = class_exists( 'Hello_Movie_Engine_Customize_Section_Builder_Control' )
						? Hello_Movie_Engine_Customize_Section_Builder_Control::library_query_shortcode_parts( $sec )
						: array(
							'type'      => $sec['query_type'] ?? 'trending',
							'tmdb_attr' => '',
						);
					$top10_extra = '';
					if ( ! empty( $sec['ids'] ) ) {
						$top10_extra .= sprintf( ' ids="%s"', esc_attr( trim( $sec['ids'] ) ) );
					}
					if ( class_exists( 'Hello_Movie_Engine_Customize_Section_Builder_Control' ) ) {
						$top10_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_responsive_columns_shortcode_attrs( $sec );
						$top10_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_gap_shortcode_attr( $sec );
						$top10_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_view_all_shortcode_attrs( $sec );
					}
					echo do_shortcode( sprintf(
						'[movie_engine_top10 type="%s" limit="%d" columns="%d" layout="%s" post_type="%s" autoplay="%s" autoplay_speed="%d" loop="%s" pause_on_hover="%s" show_dots="%s"%s%s%s]',
						esc_attr( $top10_query['type'] ),
						$count,
						$columns,
						esc_attr( $slider ),
						esc_attr( $post_type ),
						esc_attr( $autoplay ),
						$slider_speed,
						esc_attr( $slider_loop ),
						esc_attr( $slider_pause ),
						esc_attr( $slider_dots ),
						$top10_extra,
						$top10_query['tmdb_attr'],
						$title_attr
					) );
					break;

				case 'library':
					$lib_query    = class_exists( 'Hello_Movie_Engine_Customize_Section_Builder_Control' )
						? Hello_Movie_Engine_Customize_Section_Builder_Control::library_query_shortcode_parts( $sec )
						: array(
							'type'      => $sec['query_type'] ?? 'trending',
							'tmdb_attr' => '',
						);
					$query_type   = $lib_query['type'];
					$post_type    = $sec['post_type'] ?? 'movie_engine_movie';
					$count        = absint( $sec['count'] ?? 12 );
					$columns      = absint( $sec['columns'] ?? 6 );
					$slider       = ! empty( $sec['slider'] ) ? 'slider' : 'grid';
					$orderby      = $sec['orderby'] ?? 'date';
					$order        = $sec['order'] ?? 'DESC';
					$ids          = trim( $sec['ids'] ?? '' );
					$genre        = trim( $sec['genre'] ?? '' );
					$year         = trim( $sec['year'] ?? '' );
					$language     = trim( $sec['language'] ?? '' );
					$lib_shared   = '';
					if ( class_exists( 'Hello_Movie_Engine_Customize_Section_Builder_Control' ) ) {
						$lib_shared .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_responsive_columns_shortcode_attrs( $sec );
						$lib_shared .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_gap_shortcode_attr( $sec );
						$lib_shared .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_view_all_shortcode_attrs( $sec );
					}
					$autoplay     = ! empty( $sec['slider_autoplay'] ) ? 'true' : 'false';
					$slider_speed = absint( $sec['slider_speed'] ?? 3000 );
					$slider_loop  = ! empty( $sec['slider_loop'] ) ? 'true' : 'false';
					$slider_pause = ! isset( $sec['slider_pause'] ) || ! empty( $sec['slider_pause'] ) ? 'true' : 'false';
					$slider_dots  = ! empty( $sec['slider_dots'] ) ? 'true' : 'false';
					$show_rating  = ! isset( $sec['show_rating'] ) || ! empty( $sec['show_rating'] ) ? 'true' : 'false';
					$show_year    = ! isset( $sec['show_year'] ) || ! empty( $sec['show_year'] ) ? 'true' : 'false';
					$show_quality = ! isset( $sec['show_quality'] ) || ! empty( $sec['show_quality'] ) ? 'true' : 'false';

					$lib_extra = '';
					if ( ! empty( $ids ) ) {
						$lib_extra .= sprintf( ' ids="%s"', esc_attr( $ids ) );
					}
					if ( ! empty( $genre ) ) {
						$lib_extra .= sprintf( ' genre="%s"', esc_attr( $genre ) );
					}
					if ( ! empty( $year ) ) {
						$lib_extra .= sprintf( ' year="%s"', esc_attr( $year ) );
					}
					if ( ! empty( $language ) ) {
						$lib_extra .= sprintf( ' language="%s"', esc_attr( $language ) );
					}

					echo do_shortcode( sprintf(
						'[movie_engine_library type="%s" post_type="%s" limit="%d" columns="%d" layout="%s" orderby="%s" order="%s" autoplay="%s" autoplay_speed="%d" loop="%s" pause_on_hover="%s" show_dots="%s" show_rating="%s" show_year="%s" show_quality="%s" pagination="false"%s%s%s%s]',
						esc_attr( $query_type ),
						esc_attr( $post_type ),
						$count,
						$columns,
						esc_attr( $slider ),
						esc_attr( $orderby ),
						esc_attr( $order ),
						esc_attr( $autoplay ),
						$slider_speed,
						esc_attr( $slider_loop ),
						esc_attr( $slider_pause ),
						esc_attr( $slider_dots ),
						esc_attr( $show_rating ),
						esc_attr( $show_year ),
						esc_attr( $show_quality ),
						$lib_shared,
						$lib_extra,
						$lib_query['tmdb_attr'],
						$title_attr
					) );
					break;

				case 'person_list':
					$count           = absint( $sec['count'] ?? 12 );
					$columns         = absint( $sec['columns'] ?? 6 );
					$slider          = ! empty( $sec['slider'] ) ? 'slider' : 'grid';
					$orderby         = $sec['person_orderby'] ?? ( $sec['orderby'] ?? 'title' );
					$department      = trim( $sec['department'] ?? '' );
					$person_autoplay = ! empty( $sec['person_autoplay'] ) ? 'true' : 'false';
					$person_speed    = absint( $sec['person_speed'] ?? 3000 );
					$person_loop     = ! empty( $sec['person_loop'] ) ? 'true' : 'false';
					$person_pause    = ! isset( $sec['person_pause'] ) || ! empty( $sec['person_pause'] ) ? 'true' : 'false';
					$person_dots     = ! empty( $sec['person_dots'] ) ? 'true' : 'false';
					$person_extra    = '';
					if ( class_exists( 'Hello_Movie_Engine_Customize_Section_Builder_Control' ) ) {
						$person_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_responsive_columns_shortcode_attrs( $sec );
						$person_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_gap_shortcode_attr( $sec );
					}
					$person_style    = isset( $sec['person_style'] ) ? $sec['person_style'] : 'overlay';
					if ( ! in_array( $person_style, array( 'overlay', 'stacked' ), true ) ) {
						$person_style = 'overlay';
					}
					$person_card_style = ( 'stacked' === $person_style ) ? 'classic' : 'overlay';
					$person_wrap_class = sprintf( 'hello-me-home-person-wrap hello-me-person-style-%s', $person_style );

					$dept_attr  = ! empty( $department ) ? sprintf( ' department="%s"', esc_attr( $department ) ) : '';
					echo do_shortcode( sprintf(
						'[movie_engine_person_list limit="%d" columns="%d" layout="%s" orderby="%s" style="%s" ratio="portrait" autoplay="%s" autoplay_speed="%d" loop="%s" pause_on_hover="%s" show_dots="%s" custom_class="%s"%s%s%s]',
						$count,
						$columns,
						esc_attr( $slider ),
						esc_attr( $orderby ),
						esc_attr( $person_card_style ),
						esc_attr( $person_autoplay ),
						$person_speed,
						esc_attr( $person_loop ),
						esc_attr( $person_pause ),
						esc_attr( $person_dots ),
						esc_attr( $person_wrap_class ),
						$dept_attr,
						$person_extra,
						$title_attr
					) );
					break;

				case 'genres':
					$genre_layout  = $sec['genre_layout'] ?? 'pills';
					$genre_limit   = absint( $sec['genre_limit'] ?? 12 );
					$genre_columns = absint( $sec['genre_columns'] ?? 6 );
					$genre_count   = ! isset( $sec['genre_count'] ) || ! empty( $sec['genre_count'] ) ? 'true' : 'false';
					$genre_extra   = '';
					if ( class_exists( 'Hello_Movie_Engine_Customize_Section_Builder_Control' ) ) {
						$genre_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_responsive_columns_shortcode_attrs( $sec );
						$genre_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_gap_shortcode_attr( $sec );
						$genre_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_view_all_shortcode_attrs( $sec, false );
						$genre_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_taxonomy_pick_shortcode_attrs( $sec );
						if ( 'slider' === $genre_layout ) {
							$genre_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_slider_shortcode_attrs( $sec );
						}
					}
					echo do_shortcode( sprintf(
						'[movie_engine_genres layout="%s" limit="%d" columns="%d" show_count="%s"%s%s]',
						esc_attr( $genre_layout ),
						$genre_limit,
						$genre_columns,
						esc_attr( $genre_count ),
						$genre_extra,
						$title_attr
					) );
					break;

				case 'history':
					$hist_count   = absint( $sec['history_count'] ?? 6 );
					$hist_columns = absint( $sec['history_columns'] ?? 4 );
					$hist_layout  = ! empty( $sec['slider'] ) ? 'slider' : 'grid';
					$hist_extra   = '';
					if ( class_exists( 'Hello_Movie_Engine_Customize_Section_Builder_Control' ) ) {
						$hist_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_responsive_columns_shortcode_attrs( $sec );
						$hist_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_gap_shortcode_attr( $sec );
						$hist_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_view_all_shortcode_attrs( $sec, true );
						if ( 'slider' === $hist_layout ) {
							$hist_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_slider_shortcode_attrs( $sec );
						}
					}
					if ( ! empty( $sec['history_view_all'] ) ) {
						$hist_extra .= sprintf( ' view_all_link="%s"', esc_url( $sec['history_view_all'] ) );
					}
					echo '<div class="movie-engine-library-container hello-me-home-history-feed-row">';
					echo '<div class="movie-engine-library-content">';
					echo do_shortcode( sprintf(
						'[movie_engine_history limit="%d" columns="%d" layout="%s"%s%s]',
						$hist_count,
						$hist_columns,
						esc_attr( $hist_layout ),
						$hist_extra,
						$title_attr
					) );
					echo '</div></div>';
					break;

				case 'years':
					$year_layout  = $sec['year_layout'] ?? 'pills';
					$year_limit   = absint( $sec['year_limit'] ?? 10 );
					$year_columns = absint( $sec['year_columns'] ?? 6 );
					$year_extra   = '';
					if ( class_exists( 'Hello_Movie_Engine_Customize_Section_Builder_Control' ) ) {
						$year_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_responsive_columns_shortcode_attrs( $sec );
						$year_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_gap_shortcode_attr( $sec );
						$year_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_view_all_shortcode_attrs( $sec, false );
						$year_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_taxonomy_pick_shortcode_attrs( $sec );
						if ( 'slider' === $year_layout ) {
							$year_extra .= Hello_Movie_Engine_Customize_Section_Builder_Control::builder_slider_shortcode_attrs( $sec );
						}
					}
					echo do_shortcode( sprintf(
						'[movie_engine_years layout="%s" limit="%d" columns="%d"%s%s]',
						esc_attr( $year_layout ),
						$year_limit,
						$year_columns,
						$year_extra,
						$title_attr
					) );
					break;

				case 'page_content':
					$page_rendered = true;
					echo '<div class="hello-me-home-editor-content">';
					while ( have_posts() ) {
						the_post();
						the_content();
					}
					echo '</div>';
					break;

				case 'shortcode':
					$shortcode = $sec['shortcode'] ?? '';
					if ( ! empty( $shortcode ) ) {
						// If the builder title is set, inject it into known Movie Engine shortcodes
						// so the heading stays inside the same section wrapper as the grid (home-feed alignment).
						if ( ! empty( $sec_title ) && ! preg_match( '/\btitle\s*=/i', $shortcode ) ) {
							$shortcode = preg_replace( '/^\[(\s*movie_engine_[\w]+)/', '[${1} title="' . esc_attr( $sec_title ) . '"', $shortcode, 1 );
						}
						echo do_shortcode( $shortcode );
					}
					break;
			}
		}

		// In hybrid mode, if page_content wasn't explicitly placed in sections, append it below
		if ( 'hybrid' === $hello_me_home_mode && ! $page_rendered ) {
			echo '<div class="hello-me-home-editor-content">';
			while ( have_posts() ) {
				the_post();
				the_content();
			}
			echo '</div>';
		}
		?>

	<?php else : ?>

		<?php
		// Classic Page Content (100% preserves existing users' pages)
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				if ( is_singular() || ! is_home() ) {
					get_template_part( 'template-parts/content', 'page' );
				} else {
					get_template_part( 'template-parts/content', get_post_type() );
				}
			endwhile;
		endif;
		?>

	<?php endif; ?>
</main>

<?php
if ( ! get_theme_mod( 'hello_movieengine_feed_hide_footer', false ) ) {
	get_footer();
} else {
	wp_footer();
	echo '</body></html>';
}
