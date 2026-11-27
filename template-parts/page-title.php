<?php
/**
 * Page Title Section.
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
if ( ! in_array( $hello_movieengine_pt_align, array( 'left', 'center', 'right' ), true ) ) {
	$hello_movieengine_pt_align = 'center';
}

$hello_movieengine_is_style_two = function_exists( 'hello_movieengine_get_active_style' )
	&& 'style-two' === hello_movieengine_get_active_style();

$hello_movieengine_pt_post_id   = is_singular() ? (int) get_the_ID() : 0;
$hello_movieengine_pt_bg_image  = hello_movieengine_get_page_title_bg_image_url( $hello_movieengine_pt_post_id );
$hello_movieengine_pt_has_image   = '' !== $hello_movieengine_pt_bg_image;
$hello_movieengine_pt_classes   = 'hello-movieengine-page-title-section';
if ( $hello_movieengine_is_style_two ) {
	$hello_movieengine_pt_classes .= ' hello-movieengine-page-title-section--cinema';
	if ( $hello_movieengine_pt_has_image ) {
		$hello_movieengine_pt_classes .= ' hello-movieengine-page-title-section--has-photo';
	}
} elseif ( $hello_movieengine_pt_has_image ) {
	$hello_movieengine_pt_classes .= ' has-bg-image';
}

$hello_movieengine_pt_style = hello_movieengine_page_title_bg_inline_style( $hello_movieengine_pt_bg_image, $hello_movieengine_is_style_two );

$hello_movieengine_title_content_cl = 'hello-movieengine-title-content hello-movieengine-container hello-movieengine-page-title-section__content';
$hello_movieengine_title_content_cl .= ' hello-movieengine-title-content--align-' . sanitize_html_class( $hello_movieengine_pt_align );
?>
<div class="<?php echo esc_attr( $hello_movieengine_pt_classes ); ?>"<?php echo $hello_movieengine_pt_style ? ' style="' . esc_attr( $hello_movieengine_pt_style ) . '"' : ''; ?>>
	<?php if ( $hello_movieengine_pt_has_image || $hello_movieengine_is_style_two ) : ?>
		<div class="hello-movieengine-page-title-section__overlay" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="hello-movieengine-page-title-section__wrap">
		<div class="<?php echo esc_attr( $hello_movieengine_title_content_cl ); ?>">
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
</div>