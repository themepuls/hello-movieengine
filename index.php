<?php
/**
 * The main template file.
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

	<main id="primary" class="site-main">
		<?php if ( ! $hello_movieengine_has_sidebar ) : ?>
		<div class="hello-movieengine-container">
		<?php endif; ?>

		<?php
		if ( have_posts() ) :

			echo '<div class="hello-movieengine-post-grid">';

			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', get_post_type() );
			endwhile;

			echo '</div>';

			the_posts_pagination( array(
				'prev_text' => esc_html__( 'Previous', 'hello-movieengine' ),
				'next_text' => esc_html__( 'Next', 'hello-movieengine' ),
				'mid_size'  => 2,
			) );

		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
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
