<?php
/**
 * Template part for displaying results in search pages.
 *
 * @package Hello_Cine Movie Engine
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'hello-movieengine-article' ); ?>>
	<?php hello_movieengine_post_thumbnail(); ?>

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
</article>
