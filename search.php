<?php
/**
 * The template for displaying search results pages.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

get_header();
?>

<?php $hello_movieengine_has_sidebar = hello_movieengine_show_sidebar(); ?>

<?php if ( $hello_movieengine_has_sidebar ) : ?>
<div class="hello-movieengine-content-area">
<?php endif; ?>

	<main id="primary" class="site-main hello-movieengine-search-results">
		<?php if ( have_posts() ) : ?>

			<?php if ( ! $hello_movieengine_has_sidebar ) : ?>
			<div class="hello-movieengine-container">
			<?php endif; ?>

				<div class="hello-movieengine-post-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', 'search' );
					endwhile;
					?>
				</div>

				<?php
				the_posts_pagination( array(
					'prev_text' => esc_html__( 'Previous', 'hello-movieengine' ),
					'next_text' => esc_html__( 'Next', 'hello-movieengine' ),
					'mid_size'  => 2,
				) );
				?>

			<?php if ( ! $hello_movieengine_has_sidebar ) : ?>
			</div>
			<?php endif; ?>

		<?php else : ?>
			<?php if ( ! $hello_movieengine_has_sidebar ) : ?>
			<div class="hello-movieengine-container">
			<?php endif; ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php if ( ! $hello_movieengine_has_sidebar ) : ?>
			</div>
			<?php endif; ?>
		<?php endif; ?>
	</main>

<?php if ( $hello_movieengine_has_sidebar ) : ?>
	<?php get_sidebar(); ?>
</div><!-- .hello-movieengine-content-area -->
<?php endif; ?>

<?php
get_footer();
