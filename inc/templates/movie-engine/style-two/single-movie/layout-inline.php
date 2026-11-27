<?php
/**
 * Style Two inline movie — cinematic hero, full-width player on Play, feed rows below.
 *
 * @package Hello_Movie Engine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$movie_engine_post_id = get_the_ID();
$movie_engine_design  = get_option( 'movie_engine_design_settings', array() );
$movie_engine_show_title = isset( $movie_engine_design['movie_show_title'] ) ? (int) $movie_engine_design['movie_show_title'] : 1;

$movie_engine_year    = get_post_meta( $movie_engine_post_id, '_movie_engine_year', true );
$movie_engine_runtime = get_post_meta( $movie_engine_post_id, '_movie_engine_runtime', true );
$movie_engine_quality = get_post_meta( $movie_engine_post_id, '_movie_engine_quality', true ) ?: 'HD';
$movie_engine_rating  = get_post_meta( $movie_engine_post_id, '_movie_engine_rating', true );

$movie_engine_backdrop = get_post_meta( $movie_engine_post_id, '_movie_engine_backdrop_url', true );
if ( ! $movie_engine_backdrop ) {
	$movie_engine_backdrop = \MovieEngine\Utils\Helpers::get_poster_url( $movie_engine_post_id, 'full' );
}

$movie_engine_genres = get_the_terms( $movie_engine_post_id, 'movie_engine_genre' );

$movie_engine_runtime_label = '';
if ( $movie_engine_runtime !== '' && $movie_engine_runtime !== null ) {
	if ( is_numeric( $movie_engine_runtime ) ) {
		$movie_engine_mins          = (int) $movie_engine_runtime;
		$movie_engine_runtime_label = sprintf(
			/* translators: 1: hours, 2: minutes */
			esc_html__( '%1$dh %2$dm', 'cine-movie-engine' ),
			(int) floor( $movie_engine_mins / 60 ),
			$movie_engine_mins % 60
		);
	} else {
		$movie_engine_runtime_label = (string) $movie_engine_runtime;
	}
}

$movie_engine_synopsis = get_the_excerpt();
if ( ! $movie_engine_synopsis ) {
	$movie_engine_content = get_post_field( 'post_content', $movie_engine_post_id );
	$movie_engine_synopsis = $movie_engine_content ? wp_trim_words( wp_strip_all_tags( $movie_engine_content ), 42, '…' ) : '';
}

$movie_engine_is_logged_in    = is_user_logged_in();
$movie_engine_is_in_watchlist = false;
if ( $movie_engine_is_logged_in ) {
	$movie_engine_is_in_watchlist = \MovieEngine\Frontend\Users\UserLibrary::in_watchlist( get_current_user_id(), $movie_engine_post_id );
}
$movie_engine_watchlist_icon = $movie_engine_is_in_watchlist ? 'fa-solid fa-check' : 'fa-solid fa-plus';

$movie_engine_back_href = function_exists( 'hello_movieengine_get_watch_back_href' )
	? hello_movieengine_get_watch_back_href( 'movie' )
	: home_url( '/' );

$movie_engine_hero_title = get_the_title();
?>

<div class="me-inline-watch-page hme-single-cinema">
	<?php if ( $movie_engine_show_title ) : ?>
		<section class="hme-watch-hero"<?php echo $movie_engine_backdrop ? ' style="--hme-watch-hero-bg:url(' . esc_url( $movie_engine_backdrop ) . ')"' : ''; ?> id="hme-watch-hero">
			<a
				class="hme-watch-hero__back"
				href="<?php echo esc_url( $movie_engine_back_href ); ?>"
				data-fallback-href="<?php echo esc_url( $movie_engine_back_href ); ?>"
				data-back-mode="fallback">
				<span class="fa-solid fa-arrow-left" aria-hidden="true"></span>
				<?php esc_html_e( 'Back', 'hello-movieengine' ); ?>
			</a>
			<div class="hme-watch-hero__shade" aria-hidden="true"></div>
			<div class="hme-watch-hero__inner">
				<h1 class="hme-watch-hero__title"><?php echo esc_html( get_the_title() ); ?></h1>

				<div class="hme-watch-hero__meta">
					<?php if ( $movie_engine_rating && (float) $movie_engine_rating > 0 ) : ?>
						<span class="hme-watch-hero__meta-item hme-watch-hero__rating">
							<span class="fa-solid fa-star" aria-hidden="true"></span>
							<?php echo esc_html( number_format( (float) $movie_engine_rating, 1 ) ); ?>
						</span>
					<?php endif; ?>
					<?php if ( $movie_engine_year ) : ?>
						<span class="hme-watch-hero__meta-item"><?php echo esc_html( $movie_engine_year ); ?></span>
					<?php endif; ?>
					<?php if ( $movie_engine_runtime_label ) : ?>
						<span class="hme-watch-hero__meta-item"><?php echo esc_html( $movie_engine_runtime_label ); ?></span>
					<?php endif; ?>
					<?php if ( $movie_engine_quality ) : ?>
						<span class="hme-watch-hero__meta-item hme-watch-hero__quality"><?php echo esc_html( $movie_engine_quality ); ?></span>
					<?php endif; ?>
				</div>

				<?php if ( $movie_engine_synopsis ) : ?>
					<p class="hme-watch-hero__synopsis"><?php echo esc_html( $movie_engine_synopsis ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $movie_engine_genres ) && ! is_wp_error( $movie_engine_genres ) ) : ?>
					<div class="hme-watch-hero__genres">
						<?php foreach ( $movie_engine_genres as $movie_engine_genre_term ) : ?>
							<?php
							$movie_engine_genre_link = get_term_link( $movie_engine_genre_term );
							if ( is_wp_error( $movie_engine_genre_link ) ) {
								continue;
							}
							?>
							<a class="hme-watch-hero__genre" href="<?php echo esc_url( $movie_engine_genre_link ); ?>">
								<?php echo esc_html( $movie_engine_genre_term->name ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="hme-watch-hero__actions">
					<a href="#movie-engine-player"
						class="hme-watch-hero__play movie-engine-btn movie-engine-btn-play me-inline-play-now">
						<span class="fa-solid fa-play" aria-hidden="true"></span>
						<?php esc_html_e( 'Watch Now', 'cine-movie-engine' ); ?>
					</a>
					<button type="button"
						class="hme-watch-hero__icon-btn movie-engine-watchlist-btn movie-engine-action-btn <?php echo esc_attr( $movie_engine_is_in_watchlist ? 'active' : '' ); ?>"
						data-post-id="<?php echo esc_attr( $movie_engine_post_id ); ?>"
						aria-label="<?php esc_attr_e( 'Watchlist', 'cine-movie-engine' ); ?>"
						<?php if ( ! $movie_engine_is_logged_in ) : ?>
							data-login-url="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>"
						<?php endif; ?>>
						<span class="<?php echo esc_attr( $movie_engine_watchlist_icon ); ?>" aria-hidden="true"></span>
					</button>
					<?php
					\MovieEngine\Utils\TemplateLoader::get_template_part(
						'global/actions-social',
						'',
						array(
							'post_id' => $movie_engine_post_id,
						)
					);
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<div class="hme-watch-player-stage" id="hme-watch-player-stage" hidden>
		<a href="#hme-watch-hero" class="hme-watch-player-back" id="hme-watch-player-back">
			<span class="fa-solid fa-arrow-left" aria-hidden="true"></span>
			<span class="hme-watch-player-back__title"><?php echo esc_html( $movie_engine_hero_title ); ?></span>
		</a>
		<?php
		ob_start();
		do_action( 'movie_engine_ad_player_top' );
		$movie_engine_inline_ad_top = trim( ob_get_clean() );
		if ( $movie_engine_inline_ad_top !== '' ) :
			?>
			<div class="me-inline-ad me-inline-ad-top">
				<?php echo $movie_engine_inline_ad_top; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>

		<div id="movie-engine-player" class="hme-watch-player me-inline-player-wrap movie-engine-player-section">
			<?php
			add_filter( 'movie_engine_skip_player_top_ad', '__return_true' );
			add_filter( 'movie_engine_skip_player_bottom_ad', '__return_true' );
			echo \MovieEngine\Frontend\Player::render( $movie_engine_post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			remove_filter( 'movie_engine_skip_player_top_ad', '__return_true' );
			remove_filter( 'movie_engine_skip_player_bottom_ad', '__return_true' );
			?>
		</div>

		<?php do_action( 'movie_engine_ad_player_bottom' ); ?>
	</div>

	<div class="me-inline-below me-main-content">
		<?php
		$movie_engine_sections_order = isset( $movie_engine_design['movie_sections_order'] ) ? $movie_engine_design['movie_sections_order'] : 'related,trending,new_releases,upcoming,cast';
		$movie_engine_sections       = array_map( 'trim', explode( ',', $movie_engine_sections_order ) );

		foreach ( $movie_engine_sections as $movie_engine_section ) {
			switch ( $movie_engine_section ) {
				case 'trending':
					\MovieEngine\Utils\TemplateLoader::get_template_part( 'single-movie/section-trending' );
					break;
				case 'related':
					\MovieEngine\Utils\TemplateLoader::get_template_part( 'single-movie/section-related' );
					break;
				case 'cast':
					\MovieEngine\Utils\TemplateLoader::get_template_part( 'single-movie/section-cast' );
					break;
				case 'new_releases':
					\MovieEngine\Utils\TemplateLoader::get_template_part( 'single-movie/section-new-releases' );
					break;
				case 'upcoming':
					\MovieEngine\Utils\TemplateLoader::get_template_part( 'single-movie/section-upcoming' );
					break;
			}
		}

		do_action( 'movie_engine_ad_after_content' );
		\MovieEngine\Frontend\TemplateHooks::render_reviews_section();
		?>
	</div>
</div>