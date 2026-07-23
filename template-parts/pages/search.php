<?php
/**
 * Themezur search results template part.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$s      = Themezur_Options::get( 'pages.search', array() );
$layout = isset( $s['layout'] ) && 'grid' === $s['layout'] ? 'grid' : 'list';
?>
<main id="content" class="site-main tz-page tz-page--search tz-page--<?php echo esc_attr( $layout ); ?>">
	<div class="tz-page__inner">
		<?php Themezur_Breadcrumbs::render(); ?>
		<header class="tz-page-header">
			<h1 class="tz-page__title">
				<?php
				printf(
					/* translators: %s: search query */
					esc_html__( 'Search results for “%s”', 'themezur' ),
					esc_html( get_search_query() )
				);
				?>
			</h1>
			<form class="tz-page-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="tz-search-again"><?php esc_html_e( 'Search', 'themezur' ); ?></label>
				<input id="tz-search-again" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search again…', 'themezur' ); ?>">
				<button type="submit" class="tz-btn"><?php esc_html_e( 'Search', 'themezur' ); ?></button>
			</form>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="tz-search-loop">
				<?php
				while ( have_posts() ) :
					the_post();
					$type_obj = get_post_type_object( get_post_type() );
					$type_lbl = $type_obj && ! empty( $type_obj->labels->singular_name ) ? $type_obj->labels->singular_name : get_post_type();
					?>
					<article <?php post_class( 'tz-search-card' ); ?>>
						<?php if ( ! empty( $s['show_image'] ) && has_post_thumbnail() ) : ?>
							<a class="tz-search-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'medium', array( 'class' => 'tz-search-card__img' ) ); ?>
							</a>
						<?php endif; ?>
						<div class="tz-search-card__body">
							<?php if ( ! empty( $s['show_type'] ) ) : ?>
								<span class="tz-search-card__type"><?php echo esc_html( $type_lbl ); ?></span>
							<?php endif; ?>
							<h2 class="tz-search-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<?php if ( ! empty( $s['show_excerpt'] ) ) : ?>
								<p class="tz-search-card__excerpt"><?php echo esc_html( Themezur_Blog::excerpt( get_the_ID(), 24 ) ); ?></p>
							<?php endif; ?>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => __( 'Previous', 'themezur' ),
					'next_text' => __( 'Next', 'themezur' ),
				)
			);
			?>
		<?php else : ?>
			<p class="tz-page__text"><?php esc_html_e( 'Nothing matched your search. Try different keywords.', 'themezur' ); ?></p>
		<?php endif; ?>
	</div>
</main>
