<?php
/**
 * Themezur blog archive (home, category, tag, date, author).
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$a          = Themezur_Blog::archive_opts();
$layout     = isset( $a['layout'] ) ? $a['layout'] : 'grid';
$columns    = isset( $a['columns'] ) ? (int) $a['columns'] : 3;
$columns    = max( 2, min( 4, $columns ) );
$layout     = in_array( $layout, array( 'grid', 'list' ), true ) ? $layout : 'grid';
$card_style = isset( $a['card_style'] ) ? $a['card_style'] : 'soft';
$readmore   = ! empty( $a['read_more_text'] ) ? $a['read_more_text'] : __( 'Read more', 'themezur' );
$words      = isset( $a['excerpt_length'] ) ? (int) $a['excerpt_length'] : 22;
$hero_on    = ! empty( $a['featured_hero'] ) && ! is_paged();
$post_count = 0;
?>
<main id="content" class="site-main tz-blog tz-blog--archive tz-blog--<?php echo esc_attr( $layout ); ?> tz-blog-cards--<?php echo esc_attr( $card_style ); ?>">
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
			<?php
			if ( $hero_on ) {
				the_post();
				$post_count++;
				$hero_id   = get_the_ID();
				$hero_cats = get_the_category();
				?>
				<!-- Featured Hero Post -->
				<article <?php post_class( 'tz-blog-hero' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a class="tz-blog-hero__media" href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'full', array( 'class' => 'tz-blog-hero__img' ) ); ?>
							<?php if ( ! empty( $a['show_badge'] ) && ! empty( $hero_cats[0] ) ) : ?>
								<span class="tz-blog-badge"><?php echo esc_html( $hero_cats[0]->name ); ?></span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
					<div class="tz-blog-hero__body">
						<?php Themezur_Blog::render_card_meta( $a ); ?>
						<h2 class="tz-blog-hero__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>
						<?php if ( ! empty( $a['show_excerpt'] ) ) : ?>
							<p class="tz-blog-hero__excerpt"><?php echo esc_html( Themezur_Blog::excerpt( $hero_id, 32 ) ); ?></p>
						<?php endif; ?>
						<a class="button tz-blog-hero__btn" href="<?php the_permalink(); ?>"><?php echo esc_html( $readmore ); ?> →</a>
					</div>
				</article>
				<?php
			}
			?>

			<?php if ( have_posts() ) : ?>
				<div
					class="tz-blog-loop"
					data-tz-blog-layout="<?php echo esc_attr( $layout ); ?>"
					style="--tz-blog-cols: <?php echo esc_attr( (string) $columns ); ?>"
				>
					<?php
					while ( have_posts() ) :
						the_post();
						$pid  = get_the_ID();
						$cats = get_the_category();
						?>
						<article <?php post_class( 'tz-blog-card' ); ?>>
							<?php if ( ! empty( $a['show_image'] ) && has_post_thumbnail() ) : ?>
								<a class="tz-blog-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
									<?php the_post_thumbnail( 'medium_large', array( 'class' => 'tz-blog-card__img' ) ); ?>
									<?php if ( ! empty( $a['show_badge'] ) && ! empty( $cats[0] ) ) : ?>
										<span class="tz-blog-badge"><?php echo esc_html( $cats[0]->name ); ?></span>
									<?php endif; ?>
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
			<?php endif; ?>

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
