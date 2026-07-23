<?php
/**
 * Themezur single blog post.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$s = Themezur_Blog::single_opts();

while ( have_posts() ) :
	the_post();
	$pid = get_the_ID();
	?>
	<main id="content" <?php post_class( 'site-main tz-blog tz-blog--single' ); ?>>
		<article class="tz-blog-single">
			<div class="tz-blog-single__inner">
				<?php Themezur_Breadcrumbs::render(); ?>
				<header class="tz-blog-single__header">
					<?php Themezur_Blog::render_single_meta( $s ); ?>
					<?php the_title( '<h1 class="tz-blog-single__title">', '</h1>' ); ?>
				</header>

				<?php if ( ! empty( $s['show_image'] ) && has_post_thumbnail() ) : ?>
					<figure class="tz-blog-single__media">
						<?php the_post_thumbnail( 'large', array( 'class' => 'tz-blog-single__img' ) ); ?>
					</figure>
				<?php endif; ?>

				<div class="tz-blog-single__content entry-content">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<nav class="tz-blog-pages"><span class="tz-blog-pages__label">' . esc_html__( 'Pages:', 'themezur' ) . '</span>',
							'after'  => '</nav>',
						)
					);
					?>
				</div>

				<?php if ( ! empty( $s['show_tags'] ) && has_tag() ) : ?>
					<div class="tz-blog-single__tags">
						<?php the_tags( '<span class="tz-blog-single__tags-label">' . esc_html__( 'Tags', 'themezur' ) . '</span> ', ' ', '' ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $s['show_author_box'] ) ) : ?>
					<?php
					$bio = get_the_author_meta( 'description' );
					?>
					<aside class="tz-blog-author">
						<div class="tz-blog-author__avatar">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 72 ); ?>
						</div>
						<div class="tz-blog-author__body">
							<p class="tz-blog-author__label"><?php esc_html_e( 'Written by', 'themezur' ); ?></p>
							<p class="tz-blog-author__name"><?php the_author(); ?></p>
							<?php if ( $bio ) : ?>
								<p class="tz-blog-author__bio"><?php echo esc_html( $bio ); ?></p>
							<?php endif; ?>
						</div>
					</aside>
				<?php endif; ?>

				<?php if ( ! empty( $s['show_nav'] ) ) : ?>
					<nav class="tz-blog-post-nav" aria-label="<?php echo esc_attr__( 'Post navigation', 'themezur' ); ?>">
						<div class="tz-blog-post-nav__prev">
							<?php
							previous_post_link(
								'%link',
								'<span class="tz-blog-post-nav__label">' . esc_html__( 'Previous', 'themezur' ) . '</span><span class="tz-blog-post-nav__title">%title</span>'
							);
							?>
						</div>
						<div class="tz-blog-post-nav__next">
							<?php
							next_post_link(
								'%link',
								'<span class="tz-blog-post-nav__label">' . esc_html__( 'Next', 'themezur' ) . '</span><span class="tz-blog-post-nav__title">%title</span>'
							);
							?>
						</div>
					</nav>
				<?php endif; ?>

				<?php if ( ! empty( $s['show_related'] ) ) : ?>
					<?php
					$related = Themezur_Blog::related_posts( $pid, isset( $s['related_count'] ) ? (int) $s['related_count'] : 3 );
					if ( ! empty( $related ) ) :
						?>
						<section class="tz-blog-related">
							<h2 class="tz-blog-related__heading"><?php esc_html_e( 'Related posts', 'themezur' ); ?></h2>
							<div class="tz-blog-related__grid">
								<?php foreach ( $related as $rel ) : ?>
									<article class="tz-blog-related__card">
										<?php if ( has_post_thumbnail( $rel ) ) : ?>
											<a class="tz-blog-related__media" href="<?php echo esc_url( get_permalink( $rel ) ); ?>">
												<?php echo get_the_post_thumbnail( $rel, 'medium', array( 'class' => 'tz-blog-related__img' ) ); ?>
											</a>
										<?php endif; ?>
										<h3 class="tz-blog-related__title">
											<a href="<?php echo esc_url( get_permalink( $rel ) ); ?>"><?php echo esc_html( get_the_title( $rel ) ); ?></a>
										</h3>
									</article>
								<?php endforeach; ?>
							</div>
						</section>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( ! empty( $s['show_comments'] ) ) : ?>
					<div class="tz-blog-comments">
						<?php comments_template(); ?>
					</div>
				<?php endif; ?>
			</div>
		</article>
	</main>
	<?php
endwhile;
