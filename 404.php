<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

get_header();

$hello_movieengine_404_title   = get_theme_mod( 'hello_movieengine_404_title', '' );
$hello_movieengine_404_desc     = get_theme_mod( 'hello_movieengine_404_description', '' );
$hello_movieengine_404_search   = get_theme_mod( 'hello_movieengine_404_show_search', true );
$hello_movieengine_404_home     = get_theme_mod( 'hello_movieengine_404_show_home_button', true );
$hello_movieengine_404_btn_text = get_theme_mod( 'hello_movieengine_404_home_button_text', '' );

if ( ! $hello_movieengine_404_title ) {
	$hello_movieengine_404_title = __( 'Page Not Found', 'hello-movieengine' );
}
if ( ! $hello_movieengine_404_desc ) {
	$hello_movieengine_404_desc = __( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'hello-movieengine' );
}
if ( ! $hello_movieengine_404_btn_text ) {
	$hello_movieengine_404_btn_text = __( 'Back to Home', 'hello-movieengine' );
}
?>

	<main id="primary" class="site-main">
		<div class="hello-movieengine-404">
			<div class="hello-movieengine-404__inner">
				<div class="hello-movieengine-404__code" aria-hidden="true">404</div>
				<h1 class="hello-movieengine-404__title"><?php echo esc_html( $hello_movieengine_404_title ); ?></h1>
				<p class="hello-movieengine-404__description"><?php echo esc_html( $hello_movieengine_404_desc ); ?></p>

				<div class="hello-movieengine-404__actions">
					<?php if ( $hello_movieengine_404_search ) : ?>
						<?php get_search_form(); ?>
					<?php endif; ?>

					<?php if ( $hello_movieengine_404_home ) : ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hello-movieengine-btn hello-movieengine-btn--primary hello-movieengine-404__home-btn">
							<?php echo esc_html( $hello_movieengine_404_btn_text ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</main>

<?php
get_footer();
