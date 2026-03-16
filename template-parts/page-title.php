<?php
/**
 * Page Title Section.
 *
 * Styled banner matching the Movie Engine taxonomy header design:
 * small uppercase context label, large bold title, optional description.
 *
 * @package Hello_Movie Engine
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! hello_movieengine_show_page_title() ) {
	return;
}

$hello_movieengine_pt_label = hello_movieengine_get_page_title_label();
$hello_movieengine_pt_title = hello_movieengine_get_page_title_text();
$hello_movieengine_pt_desc  = hello_movieengine_get_page_title_description();
$hello_movieengine_pt_align = get_theme_mod( 'hello_movieengine_page_title_align', 'center' );

$hello_movieengine_pt_bg_image = '';
if ( ( is_page() || is_singular() ) && has_post_thumbnail() ) {
	$hello_movieengine_pt_bg_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
}
if ( ! $hello_movieengine_pt_bg_image ) {
	$hello_movieengine_pt_bg_image = get_theme_mod( 'hello_movieengine_page_title_bg_image', '' );
}

$hello_movieengine_pt_has_image = ! empty( $hello_movieengine_pt_bg_image );
$hello_movieengine_pt_classes   = 'hello-movieengine-page-title-section';
if ( $hello_movieengine_pt_has_image ) {
	$hello_movieengine_pt_classes .= ' has-bg-image';
}

$hello_movieengine_pt_style = 'text-align:' . esc_attr( $hello_movieengine_pt_align ) . ';';
if ( $hello_movieengine_pt_has_image ) {
	$hello_movieengine_pt_style .= 'background-image:url(' . esc_url( $hello_movieengine_pt_bg_image ) . ');';
}
?>
<div class="<?php echo esc_attr( $hello_movieengine_pt_classes ); ?>" style="<?php echo $hello_movieengine_pt_style; ?>">
	<?php if ( $hello_movieengine_pt_has_image ) : ?>
		<div class="hello-movieengine-page-title-section__overlay"></div>
	<?php endif; ?>
	<div class="hello-movieengine-container hello-movieengine-page-title-section__content">
		<?php if ( $hello_movieengine_pt_label ) : ?>
			<span class="hello-movieengine-page-title-section__label"><?php echo esc_html( $hello_movieengine_pt_label ); ?></span>
		<?php endif; ?>
		<?php if ( $hello_movieengine_pt_title ) : ?>
			<h1 class="hello-movieengine-page-title-section__title"><?php echo wp_kses_post( $hello_movieengine_pt_title ); ?></h1>
		<?php endif; ?>
		<?php if ( $hello_movieengine_pt_desc ) : ?>
			<div class="hello-movieengine-page-title-section__desc"><?php echo wp_kses_post( $hello_movieengine_pt_desc ); ?></div>
		<?php endif; ?>
	</div>
</div>
