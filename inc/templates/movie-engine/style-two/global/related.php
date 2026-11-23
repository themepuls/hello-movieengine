<?php
/**
 * Style Two — related row (home feed slider cards).
 *
 * @package Hello_Movie Engine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$movie_engine_design = get_option( 'movie_engine_design_settings', array() );
$movie_engine_count  = isset( $movie_engine_design['movie_related_count'] ) ? (int) $movie_engine_design['movie_related_count'] : 6;
$movie_engine_related_title = $movie_engine_design['movie_related_title'] ?? esc_html__( 'More Like This', 'cine-movie-engine' );
$movie_engine_related_columns = isset( $movie_engine_design['movie_related_columns'] ) ? (int) $movie_engine_design['movie_related_columns'] : 6;
$movie_engine_related_columns_laptop = isset( $movie_engine_design['movie_related_columns_laptop'] ) ? (int) $movie_engine_design['movie_related_columns_laptop'] : 5;
$movie_engine_related_columns_tablet = isset( $movie_engine_design['movie_related_columns_tablet'] ) ? (int) $movie_engine_design['movie_related_columns_tablet'] : 4;
$movie_engine_related_columns_mobile = isset( $movie_engine_design['movie_related_columns_mobile'] ) ? (int) $movie_engine_design['movie_related_columns_mobile'] : 3;
$movie_engine_related_slider = isset( $movie_engine_design['movie_related_slider'] ) ? (int) $movie_engine_design['movie_related_slider'] : 1;

$movie_engine_related_query = \MovieEngine\Frontend\Query::get_related_posts( get_the_ID(), $movie_engine_count > 0 ? $movie_engine_count : 6 );

if ( ! $movie_engine_related_query->have_posts() ) {
	return;
}

$movie_engine_slider_classes = array();
if ( $movie_engine_related_slider ) {
	$movie_engine_slider_classes[] = 'movie-engine-slider-enabled';
	$movie_engine_slider_classes[] = 'me-slider-layout';
}
?>
<section class="movie-engine-section movie-engine-related-section <?php echo esc_attr( implode( ' ', $movie_engine_slider_classes ) ); ?>"
	style="--grid-columns: <?php echo esc_attr( $movie_engine_related_columns ); ?>; --grid-columns-laptop: <?php echo esc_attr( $movie_engine_related_columns_laptop ); ?>; --grid-columns-tablet: <?php echo esc_attr( $movie_engine_related_columns_tablet ); ?>; --grid-columns-mobile: <?php echo esc_attr( $movie_engine_related_columns_mobile ); ?>;">
	<div class="movie-engine-section-title me-section-header">
		<h2><?php echo esc_html( $movie_engine_related_title ); ?></h2>
		<?php
		if ( $movie_engine_related_query->found_posts > $movie_engine_count ) {
			$movie_engine_related_url = '';
			if ( ! empty( $movie_engine_design['movie_archive_page'] ) ) {
				$movie_engine_related_url = get_permalink( $movie_engine_design['movie_archive_page'] );
			} elseif ( ! empty( $movie_engine_design['browse_page'] ) ) {
				$movie_engine_related_url = get_permalink( $movie_engine_design['browse_page'] );
			} else {
				$movie_engine_related_url = get_post_type_archive_link( 'movie_engine_movie' );
			}

			$movie_engine_genre_slugs = wp_get_post_terms( get_the_ID(), 'movie_engine_genre', array( 'fields' => 'slugs' ) );
			if ( ! empty( $movie_engine_genre_slugs ) && ! is_wp_error( $movie_engine_genre_slugs ) ) {
				$movie_engine_related_url = add_query_arg( 'movie_engine_genre', $movie_engine_genre_slugs[0], $movie_engine_related_url );
			}
			?>
			<a href="<?php echo esc_url( $movie_engine_related_url ); ?>" class="movie-engine-view-all me-view-all">
				<?php esc_html_e( 'View All', 'cine-movie-engine' ); ?> <span class="fa-solid fa-angle-right"></span>
			</a>
		<?php } ?>
	</div>
	<div class="movie-engine-items-grid">
		<?php
		while ( $movie_engine_related_query->have_posts() ) :
			$movie_engine_related_query->the_post();
			\MovieEngine\Utils\TemplateLoader::get_template_part( 'loop/content-movie' );
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>