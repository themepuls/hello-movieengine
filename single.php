<?php
/**
 * The template for displaying all single posts.
 *
 * @package Hello_Cine Movie Engine
 * @since 1.0.0
 */

get_header();
?>

<?php $hello_movieengine_has_sidebar = hello_movieengine_show_sidebar(); ?>

<?php if ( $hello_movieengine_has_sidebar ) : ?>
<div class="hello-movieengine-content-area">
<?php endif; ?>

	<main id="primary" class="site-main">
		<?php if ( ! $hello_movieengine_has_sidebar ) : ?>
		<div class="hello-movieengine-container">
		<?php endif; ?>

			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'hello-movieengine-single-content' ); ?>>
					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

						<?php if ( 'post' === get_post_type() ) : ?>
							<div class="entry-meta">
								<?php
								hello_movieengine_posted_on();
								hello_movieengine_posted_by();
								?>
							</div>
						<?php endif; ?>
					</header>

					<?php hello_movieengine_post_thumbnail(); ?>

					<div class="entry-content">
						<?php
						the_content();

						wp_link_pages( array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'hello-movieengine' ),
							'after'  => '</div>',
						) );
						?>
					</div>

					<footer class="entry-footer">
						<?php hello_movieengine_entry_footer(); ?>
					</footer>
				</article>

				<?php
				the_post_navigation( array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'hello-movieengine' ) . '</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'hello-movieengine' ) . '</span> <span class="nav-title">%title</span>',
				) );

				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile;
			?>

		<?php if ( ! $hello_movieengine_has_sidebar ) : ?>
		</div>
		<?php endif; ?>
	</main>

<?php if ( $hello_movieengine_has_sidebar ) : ?>
	<?php get_sidebar(); ?>
</div><!-- .hello-movieengine-content-area -->
<?php endif; ?>

<?php
get_footer();
