<?php
/**
 * Studio taxonomy archive — Style Two uses a centered logo cinema hero.
 * Other styles fall back to the plugin default taxonomy layout.
 *
 * @package Hello_Movie Engine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hello_me_use_cinema_studio = function_exists( 'hello_movieengine_get_active_style' )
	&& 'style-two' === hello_movieengine_get_active_style();

if ( ! $hello_me_use_cinema_studio && defined( 'MOVIE_ENGINE_PATH' ) ) {
	$hello_me_plugin_tax = MOVIE_ENGINE_PATH . 'templates/taxonomy.php';
	if ( is_readable( $hello_me_plugin_tax ) ) {
		include $hello_me_plugin_tax;
		return;
	}
}

get_header();

$movie_engine_term = get_queried_object();
$movie_engine_taxonomy = get_taxonomy( $movie_engine_term->taxonomy );
$movie_engine_taxonomy_label = $movie_engine_taxonomy->labels->singular_name;

do_action( 'movie_engine_before_taxonomy_archive', $movie_engine_term, $movie_engine_taxonomy );

$movie_engine_design_settings = get_option( 'movie_engine_design_settings', array() );
$movie_engine_show_hero       = isset( $movie_engine_design_settings['taxonomy_show_hero'] ) ? (int) $movie_engine_design_settings['taxonomy_show_hero'] : 1;
$movie_engine_show_hero_bg    = isset( $movie_engine_design_settings['taxonomy_show_hero_bg'] ) ? (int) $movie_engine_design_settings['taxonomy_show_hero_bg'] : 1;
$movie_engine_show_label      = isset( $movie_engine_design_settings['taxonomy_show_label'] ) ? (int) $movie_engine_design_settings['taxonomy_show_label'] : 1;
$movie_engine_show_count      = isset( $movie_engine_design_settings['taxonomy_show_count'] ) ? (int) $movie_engine_design_settings['taxonomy_show_count'] : 1;
$movie_engine_show_desc       = isset( $movie_engine_design_settings['taxonomy_show_description'] ) ? (int) $movie_engine_design_settings['taxonomy_show_description'] : 1;
$movie_engine_show_studio_logo = isset( $movie_engine_design_settings['taxonomy_show_studio_logo'] ) ? (int) $movie_engine_design_settings['taxonomy_show_studio_logo'] : 1;

$movie_engine_hero_bg_image = '';
if ( $movie_engine_show_hero && $movie_engine_show_hero_bg ) {
	$movie_engine_latest_posts  = get_posts(
		array(
			'post_type'      => array( 'movie_engine_movie', 'movie_engine_series' ),
			'posts_per_page' => 1,
			'tax_query'      => array(
				array(
					'taxonomy' => $movie_engine_term->taxonomy,
					'field'    => 'term_id',
					'terms'    => $movie_engine_term->term_id,
				),
			),
		)
	);

	if ( ! empty( $movie_engine_latest_posts ) ) {
		$movie_engine_hero_bg_image = \MovieEngine\Utils\Helpers::get_poster_url( $movie_engine_latest_posts[0]->ID, 'full' );
	}
}

$movie_engine_studio_logo = '';
if ( $hello_me_use_cinema_studio && $movie_engine_show_studio_logo && class_exists( '\MovieEngine\Utils\Helpers' ) ) {
	$movie_engine_studio_logo = \MovieEngine\Utils\Helpers::get_studio_image_url( $movie_engine_term->term_id, 'large' );
}

$movie_engine_ajax_data = 'data-me_studio="' . esc_attr( $movie_engine_term->slug ) . '"';
?>

<div class="movie-engine-archive-wrapper movie-engine-plugin movie-engine-ajax-container hello-me-studio-tax-archive" data-action="movie_engine_filter_movies" <?php echo wp_kses( $movie_engine_ajax_data, array() ); ?>>
	<?php if ( $movie_engine_show_hero ) : ?>
		<section class="me-taxonomy-hero me-taxonomy-hero--studio-cinema <?php echo esc_attr( $movie_engine_hero_bg_image ? 'has-bg' : '' ); ?><?php echo $movie_engine_studio_logo ? ' has-studio-logo' : ''; ?>">
			<?php if ( $movie_engine_hero_bg_image ) : ?>
				<div class="me-hero-bg" style="background-image: url('<?php echo esc_url( $movie_engine_hero_bg_image ); ?>');"></div>
			<?php endif; ?>
			<div class="me-hero-overlay" aria-hidden="true"></div>

			<div class="movie-engine-container-fluid me-hero-shell--studio-cinema">
				<div class="me-hero-content me-hero-content--studio-cinema">
					<?php do_action( 'movie_engine_before_archive_header', $movie_engine_term, $movie_engine_taxonomy ); ?>
					<header class="me-hero-header me-hero-header--studio-cinema">
						<?php if ( $movie_engine_studio_logo ) : ?>
							<img
								class="me-studio-hero-logo"
								src="<?php echo esc_url( $movie_engine_studio_logo ); ?>"
								alt="<?php echo esc_attr( $movie_engine_term->name ); ?>"
								loading="eager"
								decoding="async"
								crossorigin="anonymous"
							/>
						<?php else : ?>
							<h1 class="me-hero-title me-studio-hero-fallback-title"><?php echo esc_html( $movie_engine_term->name ); ?></h1>
						<?php endif; ?>

						<?php if ( $movie_engine_show_label || $movie_engine_show_count ) : ?>
							<p class="me-studio-hero-kicker">
								<?php if ( $movie_engine_show_label ) : ?>
									<span class="me-studio-hero-kicker__label"><?php esc_html_e( 'Studio', 'hello-movieengine' ); ?></span>
								<?php endif; ?>
								<?php if ( $movie_engine_show_label && $movie_engine_show_count ) : ?>
									<span class="me-studio-hero-kicker__sep" aria-hidden="true">&middot;</span>
								<?php endif; ?>
								<?php if ( $movie_engine_show_count ) : ?>
									<span class="me-studio-hero-kicker__count">
										<?php
										printf(
											/* translators: %s: title count */
											esc_html( _n( '%s Title', '%s Titles', (int) $movie_engine_term->count, 'hello-movieengine' ) ),
											esc_html( number_format_i18n( (int) $movie_engine_term->count ) )
										);
										?>
									</span>
								<?php endif; ?>
							</p>
						<?php endif; ?>

						<?php if ( $movie_engine_show_desc && ! empty( $movie_engine_term->description ) ) : ?>
							<div class="me-studio-hero-description">
								<?php echo \MovieEngine\Utils\Helpers::kses_post( $movie_engine_term->description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						<?php endif; ?>
					</header>
					<?php do_action( 'movie_engine_after_archive_header', $movie_engine_term, $movie_engine_taxonomy ); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<div class="movie-engine-taxonomy-wrapper">
		<div class="movie-engine-container-fluid hello-me-studio-trending-row">
			<?php
			\MovieEngine\Utils\TemplateLoader::get_template_part(
				'archive/studio-type-tabs',
				'',
				array(
					'studio_term' => $movie_engine_term,
				)
			);
			?>
		</div>

		<div class="me-archive-results-wrapper">
			<div class="movie-engine-container-fluid">
				<?php
				if ( have_posts() ) :
					$movie_engine_design_settings = get_option( 'movie_engine_design_settings', array() );
					$movie_engine_pagination_type = isset( $movie_engine_design_settings['pagination_type'] ) ? $movie_engine_design_settings['pagination_type'] : 'numbers';
					$movie_engine_max_pages       = $wp_query->max_num_pages;
					$movie_engine_paged           = max( 1, get_query_var( 'paged' ) );

					$movie_engine_cols        = $movie_engine_design_settings['archive_columns_desktop'] ?? 8;
					$movie_engine_cols_laptop = $movie_engine_design_settings['archive_columns_laptop'] ?? 5;
					$movie_engine_cols_tablet = $movie_engine_design_settings['archive_columns_tablet'] ?? 3;
					$movie_engine_cols_mobile = $movie_engine_design_settings['archive_columns_mobile'] ?? 2;
					$movie_engine_grid_style  = "style='--grid-columns: " . esc_attr( $movie_engine_cols ) . "; --grid-columns-laptop: " . esc_attr( $movie_engine_cols_laptop ) . "; --grid-columns-tablet: " . esc_attr( $movie_engine_cols_tablet ) . "; --grid-columns-mobile: " . esc_attr( $movie_engine_cols_mobile ) . "; --me-grid-columns-desktop: " . esc_attr( $movie_engine_cols ) . "; --me-grid-columns-laptop: " . esc_attr( $movie_engine_cols_laptop ) . "; --me-grid-columns-tablet: " . esc_attr( $movie_engine_cols_tablet ) . "; --me-grid-columns-mobile: " . esc_attr( $movie_engine_cols_mobile ) . ";'";
					?>
					<section class="movie-engine-section">
						<div class="movie-engine-items-grid movie-engine-movie-grid" <?php echo $movie_engine_grid_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php
						while ( have_posts() ) :
							the_post();
							\MovieEngine\Utils\TemplateLoader::get_template_part( 'loop/content-movie' );
						endwhile;
						?>
						</div>
					</section>

					<nav class="movie-engine-pagination" data-type="<?php echo esc_attr( $movie_engine_pagination_type ); ?>" data-max-pages="<?php echo esc_attr( $movie_engine_max_pages ); ?>" aria-label="<?php esc_attr_e( 'Pagination', 'cine-movie-engine' ); ?>">
						<?php
						if ( 'load_more' === $movie_engine_pagination_type || 'infinite' === $movie_engine_pagination_type ) {
							if ( $movie_engine_paged < $movie_engine_max_pages ) {
								echo '<button class="movie-engine-load-more-btn me-btn me-btn-primary" data-paged="' . esc_attr( $movie_engine_paged + 1 ) . '" aria-label="' . esc_attr__( 'Load more movies', 'cine-movie-engine' ) . '">' . esc_html__( 'Load More', 'cine-movie-engine' ) . '</button>';
								if ( 'infinite' === $movie_engine_pagination_type ) {
									echo '<div class="movie-engine-infinite-trigger" aria-hidden="true"></div>';
								}
							}
						} else {
							$pagination_links = paginate_links(
								array(
									'prev_text' => '<span class="fa-solid fa-angle-left" aria-hidden="true"></span><span class="me-sr-only">' . esc_html__( 'Previous', 'cine-movie-engine' ) . '</span>',
									'next_text' => '<span class="fa-solid fa-angle-right" aria-hidden="true"></span><span class="me-sr-only">' . esc_html__( 'Next', 'cine-movie-engine' ) . '</span>',
								)
							);
							if ( $pagination_links ) {
								$pagination_links = str_replace( 'class="prev page-numbers"', 'class="prev page-numbers" aria-label="' . esc_attr__( 'Previous Page', 'cine-movie-engine' ) . '"', $pagination_links );
								$pagination_links = str_replace( 'class="next page-numbers"', 'class="next page-numbers" aria-label="' . esc_attr__( 'Next Page', 'cine-movie-engine' ) . '"', $pagination_links );
								echo \MovieEngine\Utils\Helpers::kses_post( (string) $pagination_links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
						}
						?>
					</nav>
				<?php else : ?>
					<div class="movie-engine-no-results">
						<p>
							<?php
							echo esc_html(
								sprintf(
									/* translators: %s: taxonomy label */
									__( 'No content found for this %s.', 'cine-movie-engine' ),
									strtolower( $movie_engine_taxonomy_label )
								)
							);
							?>
						</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?php
do_action( 'movie_engine_after_taxonomy_archive', $movie_engine_term, $movie_engine_taxonomy );

get_footer();