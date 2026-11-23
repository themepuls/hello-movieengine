<?php
/**
 * Style Two — upcoming row (home feed slider).
 *
 * @package Hello_Movie Engine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$movie_engine_design_options = get_option( 'movie_engine_design_settings', array() );
$movie_engine_show_upcoming = isset( $movie_engine_design_options['movie_show_upcoming'] ) ? (int) $movie_engine_design_options['movie_show_upcoming'] : 0;
if ( ! $movie_engine_show_upcoming ) {
	return;
}

$movie_engine_upcoming_title = $movie_engine_design_options['movie_upcoming_title'] ?? esc_html__( 'Coming Soon', 'cine-movie-engine' );
$movie_engine_upcoming_count = isset( $movie_engine_design_options['movie_upcoming_count'] ) ? (int) $movie_engine_design_options['movie_upcoming_count'] : 6;
$movie_engine_upcoming_query = \MovieEngine\Frontend\Query::get_posts_by_type( 'upcoming', $movie_engine_upcoming_count > 0 ? $movie_engine_upcoming_count : 6, get_the_ID() );

$movie_engine_archive_url = '';
if ( ! empty( $movie_engine_design_options['movie_archive_page'] ) ) {
	$movie_engine_archive_url = get_permalink( $movie_engine_design_options['movie_archive_page'] );
} elseif ( ! empty( $movie_engine_design_options['browse_page'] ) ) {
	$movie_engine_archive_url = get_permalink( $movie_engine_design_options['browse_page'] );
} else {
	$movie_engine_archive_url = get_post_type_archive_link( 'movie_engine_movie' );
}

if ( ! $movie_engine_upcoming_query || ! $movie_engine_upcoming_query->have_posts() ) {
	return;
}

$movie_engine_upcoming_slider_enabled = isset( $movie_engine_design_options['movie_upcoming_slider'] ) ? (int) $movie_engine_design_options['movie_upcoming_slider'] : 1;
$movie_engine_upcoming_columns = isset( $movie_engine_design_options['movie_upcoming_columns'] ) ? (int) $movie_engine_design_options['movie_upcoming_columns'] : 6;
$movie_engine_upcoming_columns_laptop = isset( $movie_engine_design_options['movie_upcoming_columns_laptop'] ) ? (int) $movie_engine_design_options['movie_upcoming_columns_laptop'] : 5;
$movie_engine_upcoming_columns_tablet = isset( $movie_engine_design_options['movie_upcoming_columns_tablet'] ) ? (int) $movie_engine_design_options['movie_upcoming_columns_tablet'] : 4;
$movie_engine_upcoming_columns_mobile = isset( $movie_engine_design_options['movie_upcoming_columns_mobile'] ) ? (int) $movie_engine_design_options['movie_upcoming_columns_mobile'] : 3;

$movie_engine_slider_classes = array();
if ( $movie_engine_upcoming_slider_enabled ) {
	$movie_engine_slider_classes[] = 'movie-engine-slider-enabled';
	$movie_engine_slider_classes[] = 'me-slider-layout';
}
?>
<section class="movie-engine-section movie-engine-upcoming-section <?php echo esc_attr( implode( ' ', $movie_engine_slider_classes ) ); ?>"
	style="--grid-columns: <?php echo esc_attr( $movie_engine_upcoming_columns ); ?>; --grid-columns-laptop: <?php echo esc_attr( $movie_engine_upcoming_columns_laptop ); ?>; --grid-columns-tablet: <?php echo esc_attr( $movie_engine_upcoming_columns_tablet ); ?>; --grid-columns-mobile: <?php echo esc_attr( $movie_engine_upcoming_columns_mobile ); ?>;">
	<div class="movie-engine-section-title me-section-header">
		<h2><?php echo esc_html( $movie_engine_upcoming_title ); ?></h2>
		<?php if ( $movie_engine_upcoming_query->found_posts > $movie_engine_upcoming_count ) : ?>
			<a href="<?php echo esc_url( $movie_engine_archive_url ); ?>" class="movie-engine-view-all me-view-all">
				<?php esc_html_e( 'View All', 'cine-movie-engine' ); ?> <span class="fa-solid fa-angle-right"></span>
			</a>
		<?php endif; ?>
	</div>
	<div class="movie-engine-items-grid">
		<?php
		while ( $movie_engine_upcoming_query->have_posts() ) :
			$movie_engine_upcoming_query->the_post();
			\MovieEngine\Utils\TemplateLoader::get_template_part( 'loop/content-movie' );
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>