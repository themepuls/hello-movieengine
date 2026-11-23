<?php
/**
 * Style Two — trending row on single movie (horizontal cards like home feed).
 *
 * @package Hello_Movie Engine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$movie_engine_design_options = get_option( 'movie_engine_design_settings', array() );
$movie_engine_show_trending  = isset( $movie_engine_design_options['movie_show_trending'] ) ? (int) $movie_engine_design_options['movie_show_trending'] : 0;

if ( ! $movie_engine_show_trending ) {
	return;
}

$movie_engine_trending_title = $movie_engine_design_options['movie_trending_title'] ?? esc_html__( 'Trending Now', 'cine-movie-engine' );
$movie_engine_trending_count = isset( $movie_engine_design_options['movie_trending_count'] ) ? (int) $movie_engine_design_options['movie_trending_count'] : 6;
$movie_engine_trending_query = \MovieEngine\Frontend\Query::get_posts_by_type(
	'trending',
	$movie_engine_trending_count > 0 ? $movie_engine_trending_count : 6,
	get_the_ID()
);

$movie_engine_archive_url = '';
if ( ! empty( $movie_engine_design_options['movie_archive_page'] ) ) {
	$movie_engine_archive_url = get_permalink( $movie_engine_design_options['movie_archive_page'] );
} elseif ( ! empty( $movie_engine_design_options['browse_page'] ) ) {
	$movie_engine_archive_url = get_permalink( $movie_engine_design_options['browse_page'] );
} else {
	$movie_engine_archive_url = get_post_type_archive_link( 'movie_engine_movie' );
}

if ( ! $movie_engine_trending_query || ! $movie_engine_trending_query->have_posts() ) {
	return;
}

$movie_engine_trending_slider_enabled = isset( $movie_engine_design_options['movie_trending_slider'] ) ? (int) $movie_engine_design_options['movie_trending_slider'] : 1;
$movie_engine_trending_columns        = isset( $movie_engine_design_options['movie_trending_columns'] ) ? (int) $movie_engine_design_options['movie_trending_columns'] : 6;
$movie_engine_trending_columns_laptop   = isset( $movie_engine_design_options['movie_trending_columns_laptop'] ) ? (int) $movie_engine_design_options['movie_trending_columns_laptop'] : 5;
$movie_engine_trending_columns_tablet   = isset( $movie_engine_design_options['movie_trending_columns_tablet'] ) ? (int) $movie_engine_design_options['movie_trending_columns_tablet'] : 4;
$movie_engine_trending_columns_mobile   = isset( $movie_engine_design_options['movie_trending_columns_mobile'] ) ? (int) $movie_engine_design_options['movie_trending_columns_mobile'] : 3;

$movie_engine_slider_classes = array( 'movie-engine-trending-section' );
if ( $movie_engine_trending_slider_enabled ) {
	$movie_engine_slider_classes[] = 'movie-engine-slider-enabled';
	$movie_engine_slider_classes[] = 'me-slider-layout';
}
?>
<section class="movie-engine-section <?php echo esc_attr( implode( ' ', $movie_engine_slider_classes ) ); ?>"
	style="--grid-columns: <?php echo esc_attr( $movie_engine_trending_columns ); ?>; --grid-columns-laptop: <?php echo esc_attr( $movie_engine_trending_columns_laptop ); ?>; --grid-columns-tablet: <?php echo esc_attr( $movie_engine_trending_columns_tablet ); ?>; --grid-columns-mobile: <?php echo esc_attr( $movie_engine_trending_columns_mobile ); ?>;">
	<div class="movie-engine-section-title me-section-header">
		<h2><?php echo esc_html( $movie_engine_trending_title ); ?></h2>
		<?php if ( $movie_engine_trending_query->found_posts > $movie_engine_trending_count ) : ?>
			<a href="<?php echo esc_url( add_query_arg( 'orderby', 'views', $movie_engine_archive_url ) ); ?>" class="movie-engine-view-all me-view-all">
				<?php esc_html_e( 'View All', 'cine-movie-engine' ); ?> <span class="fa-solid fa-angle-right"></span>
			</a>
		<?php endif; ?>
	</div>
	<div class="movie-engine-items-grid">
		<?php
		while ( $movie_engine_trending_query->have_posts() ) :
			$movie_engine_trending_query->the_post();
			\MovieEngine\Utils\TemplateLoader::get_template_part( 'loop/content-movie' );
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>