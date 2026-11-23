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

$hello_me_shortcode = sprintf(
	'[movie_engine_library post_type="%s" show_search="true" show_filters="false" columns="8" limit="20"]',
	$hello_me_browse_post_type
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
