<?php
/**
 * Studio taxonomy — type tabs (All / Movies / TV Series), no search bar.
 *
 * @package Hello_Movie Engine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hello_me_studio_term = isset( $args['studio_term'] ) ? $args['studio_term'] : get_queried_object();
if ( ! $hello_me_studio_term || empty( $hello_me_studio_term->slug ) ) {
	return;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$hello_me_current_type = isset( $_GET['me_post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['me_post_type'] ) ) : '';

$hello_me_tabs = array(
	array(
		'id'    => 'all',
		'label' => __( 'All', 'hello-movieengine' ),
		'value' => '',
	),
	array(
		'id'    => 'movies',
		'label' => __( 'Movies', 'hello-movieengine' ),
		'value' => 'movie_engine_movie',
	),
	array(
		'id'    => 'series',
		'label' => __( 'Series', 'hello-movieengine' ),
		'value' => 'movie_engine_series',
	),
);

$hello_me_section_title = sprintf(
	/* translators: %s: studio name */
	esc_html__( 'Trending on %s', 'hello-movieengine' ),
	$hello_me_studio_term->name
);
?>

<div class="hello-me-studio-type-bar hello-me-studio-trending-header">
	<div class="hello-me-studio-type-bar__head">
		<h2 class="hello-me-studio-type-bar__title"><?php echo esc_html( $hello_me_section_title ); ?></h2>

		<form class="movie-engine-filter-form hello-me-studio-type-tabs" method="get" action="">
			<input type="hidden" name="me_studio" value="<?php echo esc_attr( $hello_me_studio_term->slug ); ?>">
			<input type="hidden" name="me_post_type" class="movie-engine-filter-type movie-engine-filter-select" value="<?php echo esc_attr( $hello_me_current_type ); ?>">

			<nav class="hello-me-studio-type-tabs__nav" aria-label="<?php esc_attr_e( 'Content type', 'hello-movieengine' ); ?>">
				<?php foreach ( $hello_me_tabs as $hello_me_tab ) : ?>
					<button
						type="button"
						class="hello-me-studio-type-tab<?php echo $hello_me_current_type === $hello_me_tab['value'] ? ' is-active' : ''; ?>"
						data-post-type="<?php echo esc_attr( $hello_me_tab['value'] ); ?>"
					>
						<?php echo esc_html( $hello_me_tab['value'] ? $hello_me_tab['label'] : $hello_me_tab['label'] ); ?>
					</button>
				<?php endforeach; ?>
			</nav>
		</form>
	</div>
</div>