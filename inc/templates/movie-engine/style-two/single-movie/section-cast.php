<?php
/**
 * Style Two — cast row (compact cards, horizontal scroll).
 *
 * @package Hello_Movie Engine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$movie_engine_design_options = get_option( 'movie_engine_design_settings', array() );
$movie_engine_show_cast      = isset( $movie_engine_design_options['movie_show_cast'] ) ? (int) $movie_engine_design_options['movie_show_cast'] : 1;
if ( ! $movie_engine_show_cast ) {
	return;
}

$movie_engine_cast_data_list = get_post_meta( get_the_ID(), '_movie_engine_cast', true );

if ( empty( $movie_engine_cast_data_list ) || ! is_array( $movie_engine_cast_data_list ) ) {
	return;
}

$movie_engine_cast_title            = $movie_engine_design_options['movie_cast_title'] ?? esc_html__( 'Top Cast', 'cine-movie-engine' );
$movie_engine_cast_count            = isset( $movie_engine_design_options['movie_cast_count'] ) ? (int) $movie_engine_design_options['movie_cast_count'] : 16;
$movie_engine_cast_columns          = isset( $movie_engine_design_options['movie_cast_columns'] ) ? (int) $movie_engine_design_options['movie_cast_columns'] : 8;
$movie_engine_cast_columns_laptop   = isset( $movie_engine_design_options['movie_cast_columns_laptop'] ) ? (int) $movie_engine_design_options['movie_cast_columns_laptop'] : 6;
$movie_engine_cast_columns_tablet   = isset( $movie_engine_design_options['movie_cast_columns_tablet'] ) ? (int) $movie_engine_design_options['movie_cast_columns_tablet'] : 6;
$movie_engine_cast_columns_mobile   = isset( $movie_engine_design_options['movie_cast_columns_mobile'] ) ? (int) $movie_engine_design_options['movie_cast_columns_mobile'] : 4;
$movie_engine_cast_limited_list     = array_slice( $movie_engine_cast_data_list, 0, $movie_engine_cast_count );
$movie_engine_cast_slider_enabled   = isset( $movie_engine_design_options['movie_cast_slider'] ) ? (int) $movie_engine_design_options['movie_cast_slider'] : 1;

$movie_engine_slider_classes = array( 'hme-cast-row' );
if ( $movie_engine_cast_slider_enabled ) {
	$movie_engine_slider_classes[] = 'movie-engine-slider-enabled';
	$movie_engine_slider_classes[] = 'me-slider-layout';
}
?>
<section class="movie-engine-section movie-engine-cast-section <?php echo esc_attr( implode( ' ', $movie_engine_slider_classes ) ); ?>"
	style="--grid-columns: <?php echo esc_attr( $movie_engine_cast_columns ); ?>; --grid-columns-laptop: <?php echo esc_attr( $movie_engine_cast_columns_laptop ); ?>; --grid-columns-tablet: <?php echo esc_attr( $movie_engine_cast_columns_tablet ); ?>; --grid-columns-mobile: <?php echo esc_attr( $movie_engine_cast_columns_mobile ); ?>;">
	<div class="movie-engine-section-title me-section-header">
		<h2>
			<?php
			printf(
				/* translators: 1: cast section title, 2: number of cast members */
				esc_html__( '%1$s (%2$d)', 'cine-movie-engine' ),
				esc_html( $movie_engine_cast_title ),
				absint( count( $movie_engine_cast_limited_list ) )
			);
			?>
		</h2>
	</div>
	<div class="movie-engine-items-grid movie-engine-cast-grid">
		<?php foreach ( $movie_engine_cast_limited_list as $movie_engine_actor_item ) : ?>
			<?php
			$movie_engine_person_id   = $movie_engine_actor_item['id'] ?? 0;
			$movie_engine_name        = $movie_engine_actor_item['name'];
			$movie_engine_photo       = $movie_engine_actor_item['photo'];
			$movie_engine_character   = $movie_engine_actor_item['character'];
			$movie_engine_person_url  = '';

			if ( $movie_engine_person_id && get_post_status( $movie_engine_person_id ) ) {
				$movie_engine_person_url   = get_permalink( $movie_engine_person_id );
				$movie_engine_name         = get_the_title( $movie_engine_person_id );
				$movie_engine_person_photo = \MovieEngine\Utils\Helpers::get_person_image_url( $movie_engine_person_id, 'medium' );
				if ( ! empty( $movie_engine_person_photo ) ) {
					$movie_engine_photo = $movie_engine_person_photo;
				}
			}
			?>
			<div class="movie-engine-item-card movie-engine-cast-item">
				<?php if ( $movie_engine_person_url ) : ?>
					<a href="<?php echo esc_url( $movie_engine_person_url ); ?>" class="me-cast-link-wrap">
				<?php endif; ?>
				<div class="movie-engine-item-thumb movie-engine-cast-thumb">
					<?php if ( ! empty( $movie_engine_photo ) ) : ?>
						<img src="<?php echo esc_url( $movie_engine_photo ); ?>" alt="<?php echo esc_attr( $movie_engine_name ); ?>" loading="lazy">
					<?php else : ?>
						<div class="movie-engine-item-placeholder">
							<span class="fa-solid fa-user" aria-hidden="true"></span>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( $movie_engine_person_url ) : ?>
					</a>
				<?php endif; ?>

				<div class="movie-engine-item-info movie-engine-cast-info">
					<h2 class="movie-engine-item-title movie-engine-actor-name">
						<?php if ( $movie_engine_person_url ) : ?>
							<a href="<?php echo esc_url( $movie_engine_person_url ); ?>">
						<?php endif; ?>
						<?php echo esc_html( $movie_engine_name ); ?>
						<?php if ( $movie_engine_person_url ) : ?>
							</a>
						<?php endif; ?>
					</h2>
					<?php if ( $movie_engine_character ) : ?>
						<div class="movie-engine-item-meta movie-engine-actor-role">
							<span><?php echo esc_html( $movie_engine_character ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>