<?php
/**
 * Style Two archive filter bar (library show_search, show_filters off).
 *
 * @package Hello_Movie Engine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use MovieEngine\Utils\Helpers;

$movie_engine_locked_post_type = $args['locked_post_type'] ?? '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$movie_engine_current_post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : $movie_engine_locked_post_type;

$movie_engine_genres = Helpers::get_terms_by_post_type( 'movie_engine_genre', $movie_engine_current_post_type );

$movie_engine_years = Helpers::get_terms_by_post_type( 'movie_engine_year', $movie_engine_current_post_type );
usort(
	$movie_engine_years,
	function ( $a, $b ) {
		return strcmp( $b->name, $a->name );
	}
);

$movie_engine_preselected_genre   = $args['preselected_genre'] ?? '';
$movie_engine_preselected_year    = $args['preselected_year'] ?? '';
$movie_engine_preselected_orderby = $args['preselected_orderby'] ?? 'date';

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$movie_engine_current_genre = get_query_var( 'movie_engine_genre' ) ?: ( isset( $_GET['movie_engine_genre'] ) ? sanitize_text_field( wp_unslash( $_GET['movie_engine_genre'] ) ) : $movie_engine_preselected_genre );
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$movie_engine_current_year = get_query_var( 'movie_engine_year' ) ?: ( isset( $_GET['movie_engine_year'] ) ? sanitize_text_field( wp_unslash( $_GET['movie_engine_year'] ) ) : $movie_engine_preselected_year );
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$movie_engine_current_sort = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : $movie_engine_preselected_orderby;

$movie_engine_search_placeholder = ( 'movie_engine_series' === $movie_engine_current_post_type )
	? __( 'Search TV series...', 'hello-movieengine' )
	: __( 'Search movies...', 'hello-movieengine' );

$movie_engine_form_action = '';
if ( $movie_engine_locked_post_type && function_exists( 'get_post_type_archive_link' ) ) {
	$movie_engine_form_action = get_post_type_archive_link( $movie_engine_locked_post_type );
}
if ( ! $movie_engine_form_action && is_singular() ) {
	$movie_engine_form_action = get_permalink();
}
if ( ! $movie_engine_form_action ) {
	$movie_engine_form_action = home_url( '/' );
}
?>

<div class="movie-engine-filter-bar">
	<form role="search" method="get" class="movie-engine-filter-form" action="<?php echo esc_url( $movie_engine_form_action ); ?>">
		<input type="hidden" name="movie_engine_search" value="1">

		<div class="movie-engine-filter-group me-search-group">
			<button type="submit" class="movie-engine-search-submit" aria-label="<?php esc_attr_e( 'Submit Search', 'cine-movie-engine' ); ?>">
				<span class="fa-solid fa-magnifying-glass" aria-hidden="true"></span>
			</button>
			<label for="movie-engine-search-input" class="me-sr-only"><?php esc_html_e( 'Search Movies', 'cine-movie-engine' ); ?></label>
			<input type="search" id="movie-engine-search-input" class="movie-engine-search-field" placeholder="<?php echo esc_attr( $movie_engine_search_placeholder ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
		</div>
		<?php if ( empty( $movie_engine_locked_post_type ) ) : ?>
			<div class="movie-engine-filter-group">
				<label for="movie-engine-filter-type" class="me-sr-only"><?php esc_html_e( 'Filter by Type', 'cine-movie-engine' ); ?></label>
				<select name="me_post_type" id="movie-engine-filter-type" class="movie-engine-filter-type movie-engine-filter-select">
					<option value=""><?php esc_html_e( 'All Types', 'cine-movie-engine' ); ?></option>
					<option value="movie_engine_movie" <?php selected( $movie_engine_current_post_type, 'movie_engine_movie' ); ?>><?php esc_html_e( 'Movies', 'cine-movie-engine' ); ?></option>
					<option value="movie_engine_series" <?php selected( $movie_engine_current_post_type, 'movie_engine_series' ); ?>><?php esc_html_e( 'TV Series', 'cine-movie-engine' ); ?></option>
				</select>
			</div>
		<?php else : ?>
			<input type="hidden" name="me_post_type" class="movie-engine-filter-type" value="<?php echo esc_attr( $movie_engine_locked_post_type ); ?>">
		<?php endif; ?>

		<?php if ( ! is_tax( 'movie_engine_genre' ) ) : ?>
			<div class="movie-engine-filter-group">
				<label for="movie-engine-filter-genre" class="me-sr-only"><?php esc_html_e( 'Filter by Genre', 'cine-movie-engine' ); ?></label>
				<select name="movie_engine_genre" id="movie-engine-filter-genre" class="movie-engine-filter-select">
					<option value=""><?php esc_html_e( 'All Genres', 'cine-movie-engine' ); ?></option>
					<?php foreach ( $movie_engine_genres as $movie_engine_genre ) : ?>
						<option value="<?php echo esc_attr( $movie_engine_genre->slug ); ?>" <?php selected( $movie_engine_current_genre, $movie_engine_genre->slug ); ?>>
							<?php echo esc_html( $movie_engine_genre->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php else : ?>
			<input type="hidden" name="movie_engine_genre" value="<?php echo esc_attr( $movie_engine_current_genre ); ?>">
		<?php endif; ?>

		<?php if ( ! is_tax( 'movie_engine_year' ) ) : ?>
			<div class="movie-engine-filter-group">
				<label for="movie-engine-filter-year" class="me-sr-only"><?php esc_html_e( 'Filter by Year', 'cine-movie-engine' ); ?></label>
				<select name="movie_engine_year" id="movie-engine-filter-year" class="movie-engine-filter-select">
					<option value=""><?php esc_html_e( 'All Years', 'cine-movie-engine' ); ?></option>
					<?php foreach ( $movie_engine_years as $movie_engine_year ) : ?>
						<option value="<?php echo esc_attr( $movie_engine_year->slug ); ?>" <?php selected( $movie_engine_current_year, $movie_engine_year->slug ); ?>>
							<?php echo esc_html( $movie_engine_year->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php else : ?>
			<input type="hidden" name="movie_engine_year" value="<?php echo esc_attr( $movie_engine_current_year ); ?>">
		<?php endif; ?>

		<div class="movie-engine-filter-group">
			<label for="movie-engine-filter-sort" class="me-sr-only"><?php esc_html_e( 'Sort by', 'cine-movie-engine' ); ?></label>
			<select name="orderby" id="movie-engine-filter-sort" class="movie-engine-filter-select">
				<option value="date" <?php selected( $movie_engine_current_sort, 'date' ); ?>><?php esc_html_e( 'Newest First', 'cine-movie-engine' ); ?></option>
				<option value="title" <?php selected( $movie_engine_current_sort, 'title' ); ?>><?php esc_html_e( 'A-Z', 'cine-movie-engine' ); ?></option>
				<option value="rating" <?php selected( $movie_engine_current_sort, 'rating' ); ?>><?php esc_html_e( 'Top Rated', 'cine-movie-engine' ); ?></option>
				<option value="views" <?php selected( $movie_engine_current_sort, 'views' ); ?>><?php esc_html_e( 'Most Viewed', 'cine-movie-engine' ); ?></option>
			</select>
		</div>

		<div class="movie-engine-filter-actions">
			<button type="button" class="movie-engine-btn me-btn-reset" aria-label="<?php esc_attr_e( 'Reset Filters', 'cine-movie-engine' ); ?>">
				<span class="fa-solid fa-rotate-left" aria-hidden="true"></span>
				<?php esc_html_e( 'Reset', 'cine-movie-engine' ); ?>
			</button>
			<button type="submit" class="movie-engine-btn me-btn-filter">
				<span class="fa-solid fa-filter" aria-hidden="true"></span>
				<?php esc_html_e( 'Filter', 'cine-movie-engine' ); ?>
			</button>
		</div>
	</form>
</div>
