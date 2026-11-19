<?php
/**
 * The footer template.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	<?php
	if ( hello_movieengine_show_site_footer() ) {
		hello_movieengine_get_style_template_part( 'footer', 'default' );
	}
	?>

</div><!-- #page -->

<?php hello_movieengine_get_style_template_part( 'navigation', 'bottom-nav' ); ?>

<?php wp_footer(); ?>
</body>
</html>
