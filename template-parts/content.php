<?php
/**
 * Template part for displaying posts in archive/index views.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'hello-movieengine-article' ); ?>>
	<?php if ( get_theme_mod( 'hello_movieengine_blog_show_thumbnail', true ) ) : ?>
		<?php hello_movieengine_post_thumbnail(); ?>
	<?php endif; ?>

	<div class="hello-movieengine-article__content">
		<header class="entry-header">
			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

			<?php if ( 'post' === get_post_type() ) : ?>
				<div class="entry-meta">
					<?php
					hello_movieengine_posted_on();
					hello_movieengine_posted_by();
					?>
				</div>
			<?php endif; ?>
		</header>

		<div class="entry-content">
			<?php the_excerpt(); ?>
		</div>
	</div>

	<footer class="entry-footer">
		<?php hello_movieengine_entry_footer(); ?>
	</footer>
</article>
