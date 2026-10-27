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
	<?php get_template_part( 'template-parts/footer/footer', 'default' ); ?>

</div><!-- #page -->

<?php get_template_part( 'template-parts/navigation/bottom-nav' ); ?>

<?php wp_footer(); ?>
</body>
</html>
