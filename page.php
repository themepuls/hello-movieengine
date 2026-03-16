<?php
/**
 * The template for displaying all pages.
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
				get_template_part( 'template-parts/content', 'page' );

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
