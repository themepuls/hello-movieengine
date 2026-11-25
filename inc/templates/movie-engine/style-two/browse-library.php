<?php
/**
 * Style Two browse layout — hero + [movie_engine_library] search bar (no sidebar filters).
 *
 * @package Hello_Movie Engine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hello_me_browse_post_type = isset( $hello_me_browse_post_type ) ? $hello_me_browse_post_type : 'movie_engine_movie';
$hello_me_is_series        = 'movie_engine_series' === $hello_me_browse_post_type;

$hello_me_eyebrow = $hello_me_is_series
	? esc_html__( 'TV Series Archive', 'hello-movieengine' )
	: esc_html__( 'Movie Archive', 'hello-movieengine' );

$hello_me_title = $hello_me_is_series
	? sprintf(
		/* translators: 1: opening accent span, 2: closing span */
		esc_html__( 'TV %1$sSeries%2$s', 'hello-movieengine' ),
		'<span>',
		'</span>'
	)
	: sprintf(
		/* translators: 1: opening accent span, 2: closing span */
		esc_html__( 'Movies %1$sArchive%2$s', 'hello-movieengine' ),
		'<span>',
		'</span>'
	);

$hello_me_desc = $hello_me_is_series
	? esc_html__( 'Discover, search and filter from our complete TV series collection', 'hello-movieengine' )
	: esc_html__( 'Discover, search and filter from our complete movie collection', 'hello-movieengine' );

$movie_engine_design_settings = get_option( 'movie_engine_design_settings', array() );
$hello_me_columns_desktop     = ! empty( $movie_engine_design_settings['archive_columns_desktop'] ) ? absint( $movie_engine_design_settings['archive_columns_desktop'] ) : 6;
$hello_me_columns_laptop      = ! empty( $movie_engine_design_settings['archive_columns_laptop'] ) ? absint( $movie_engine_design_settings['archive_columns_laptop'] ) : 4;
$hello_me_columns_tablet      = ! empty( $movie_engine_design_settings['archive_columns_tablet'] ) ? absint( $movie_engine_design_settings['archive_columns_tablet'] ) : 3;
$hello_me_columns_mobile      = ! empty( $movie_engine_design_settings['archive_columns_mobile'] ) ? absint( $movie_engine_design_settings['archive_columns_mobile'] ) : 2;
$hello_me_limit               = ! empty( $movie_engine_design_settings['archive_items_per_page'] ) ? absint( $movie_engine_design_settings['archive_items_per_page'] ) : 24;
$hello_me_show_rating         = ! isset( $movie_engine_design_settings['archive_show_rating'] ) || ! empty( $movie_engine_design_settings['archive_show_rating'] ) ? 'true' : 'false';
$hello_me_show_year           = ! isset( $movie_engine_design_settings['archive_show_year'] ) || ! empty( $movie_engine_design_settings['archive_show_year'] ) ? 'true' : 'false';
$hello_me_show_quality        = ! isset( $movie_engine_design_settings['archive_show_quality'] ) || ! empty( $movie_engine_design_settings['archive_show_quality'] ) ? 'true' : 'false';

$hello_me_shortcode = sprintf(
	'[movie_engine_library post_type="%s" show_search="true" show_filters="false" columns="%d" columns_laptop="%d" columns_tablet="%d" columns_mobile="%d" limit="%d" show_rating="%s" show_year="%s" show_quality="%s"]',
	esc_attr( $hello_me_browse_post_type ),
	$hello_me_columns_desktop,
	$hello_me_columns_laptop,
	$hello_me_columns_tablet,
	$hello_me_columns_mobile,
	$hello_me_limit,
	esc_attr( $hello_me_show_rating ),
	esc_attr( $hello_me_show_year ),
	esc_attr( $hello_me_show_quality )
);

get_header();
?>

<main id="primary" class="site-main hello-me-browse">
	<div class="hello-me-browse__hero hello-movieengine-page-title-section--cinema">
		<div class="hello-movieengine-page-title-section__overlay" aria-hidden="true"></div>
		<div class="hello-me-browse__hero-inner">
			<p class="hello-me-browse__eyebrow"><?php echo esc_html( $hello_me_eyebrow ); ?></p>
			<h1 class="hello-me-browse__title"><?php echo wp_kses( $hello_me_title, array( 'span' => array() ) ); ?></h1>
			<p class="hello-me-browse__desc"><?php echo esc_html( $hello_me_desc ); ?></p>
		</div>
	</div>
	<div class="hello-me-browse__body">
		<?php echo do_shortcode( $hello_me_shortcode ); ?>
	</div>
</main>

<?php
get_footer();
