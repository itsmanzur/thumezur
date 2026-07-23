<?php
/**
 * Themezur blog archive (home, category, tag, date, author).
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$a        = Themezur_Blog::archive_opts();
$layout   = isset( $a['layout'] ) ? $a['layout'] : 'grid';
$columns  = isset( $a['columns'] ) ? (int) $a['columns'] : 3;
$columns  = max( 2, min( 4, $columns ) );
$layout   = in_array( $layout, array( 'grid', 'list' ), true ) ? $layout : 'grid';
$readmore = ! empty( $a['read_more_text'] ) ? $a['read_more_text'] : __( 'Read more', 'themezur' );
$words    = isset( $a['excerpt_length'] ) ? (int) $a['excerpt_length'] : 22;
?>
<main id="content" class="site-main tz-blog tz-blog--archive tz-blog--<?php echo esc_attr( $layout ); ?>">
	<div class="tz-blog__inner">
		<?php Themezur_Breadcrumbs::render(); ?>
		<?php if ( ! empty( $a['show_title'] ) || ( ! empty( $a['show_description'] ) && get_the_archive_description() ) ) : ?>
			<header class="tz-blog-header">
				<?php if ( ! empty( $a['show_title'] ) ) : ?>
					<?php
					if ( is_home() && ! is_front_page() ) {
						echo '<h1 class="tz-blog-header__title">' . esc_html( single_post_title( '', false ) ) . '</h1>';
					} elseif ( is_home() ) {
						echo '<h1 class="tz-blog-header__title">' . esc_html__( 'Blog', 'themezur' ) . '</h1>';
					} else {
						the_archive_title( '<h1 class="tz-blog-header__title">', '</h1>' );
					}
					?>
				<?php endif; ?>
				<?php if ( ! empty( $a['show_description'] ) ) : ?>
					<?php the_archive_description( '<div class="tz-blog-header__desc">', '</div>' ); ?>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div
				class="tz-blog-loop"
				data-tz-blog-layout="<?php echo esc_attr( $layout ); ?>"
				style="--tz-blog-cols: <?php echo esc_attr( (string) $columns ); ?>"
			>
				<?php
				while ( have_posts() ) :
					the_post();
					$pid = get_the_ID();
					?>
					<article <?php post_class( 'tz-blog-card' ); ?>>
						<?php if ( ! empty( $a['show_image'] ) && has_post_thumbnail() ) : ?>
							<a class="tz-blog-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'medium_large', array( 'class' => 'tz-blog-card__img' ) ); ?>
							</a>
						<?php endif; ?>
						<div class="tz-blog-card__body">
							<?php Themezur_Blog::render_card_meta( $a ); ?>
							<h2 class="tz-blog-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<?php if ( ! empty( $a['show_excerpt'] ) ) : ?>
								<p class="tz-blog-card__excerpt"><?php echo esc_html( Themezur_Blog::excerpt( $pid, $words ) ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $a['show_read_more'] ) ) : ?>
								<a class="tz-blog-card__more" href="<?php the_permalink(); ?>"><?php echo esc_html( $readmore ); ?></a>
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
			<p class="tz-blog-empty"><?php esc_html_e( 'No posts found.', 'themezur' ); ?></p>
		<?php endif; ?>
	</div>
</main>
