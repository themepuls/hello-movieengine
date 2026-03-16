<?php
/**
 * The header template.
 *
 * Loads the appropriate header style (transparent or solid) based on
 * Customizer setting, page meta, or Movie Engine context.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hello_movieengine_header_style = hello_movieengine_get_header_style();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hello-movieengine-site">
<?php wp_body_open(); ?>
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'hello-movieengine' ); ?></a>

	<?php get_template_part( 'template-parts/header/header', $hello_movieengine_header_style ); ?>

	<?php get_template_part( 'template-parts/page-title' ); ?>
