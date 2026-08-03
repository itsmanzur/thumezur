<?php
/**
 * Themezur single blog post.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$s          = Themezur_Blog::single_opts();
$layout     = isset( $s['layout'] ) ? $s['layout'] : 'standard';
$has_sidebar = 'sidebar' === $layout;
?>

<?php if ( ! empty( $s['show_progress_bar'] ) ) : ?>
	<div id="tz-blog-progress" class="tz-blog-progress" aria-hidden="true">
		<div id="tz-blog-progress-bar" class="tz-blog-progress__bar" style="width: 0%;"></div>
	</div>
<?php endif; ?>

<?php
while ( have_posts() ) :
	the_post();
	$pid = get_the_ID();
	?>
	<main id="content" <?php post_class( 'site-main tz-blog tz-blog--single tz-blog-single--' . sanitize_html_class( $layout ) ); ?>>
		<article class="tz-blog-single">
			<div class="tz-blog-single__inner">
				<?php Themezur_Breadcrumbs::render(); ?>

				<div class="<?php echo $has_sidebar ? 'tz-blog-single__grid' : 'tz-blog-single__wrap'; ?>">
					<div class="tz-blog-single__main">
						<header class="tz-blog-single__header">
							<?php Themezur_Blog::render_single_meta( $s ); ?>
							<?php the_title( '<h1 class="tz-blog-single__title">', '</h1>' ); ?>
						</header>

						<?php if ( ! empty( $s['show_image'] ) && has_post_thumbnail() ) : ?>
							<figure class="tz-blog-single__media">
								<?php the_post_thumbnail( 'large', array( 'class' => 'tz-blog-single__img' ) ); ?>
							</figure>
						<?php endif; ?>

						<?php if ( ! empty( $s['show_social_share'] ) ) : ?>
							<?php Themezur_Blog::render_social_share(); ?>
						<?php endif; ?>

						<?php if ( ! empty( $s['show_toc'] ) ) : ?>
							<div id="tz-blog-toc" class="tz-blog-toc" hidden>
								<button class="tz-blog-toc__toggle" type="button" aria-expanded="true">
									<span><?php esc_html_e( 'Table of Contents', 'themezur' ); ?></span>
									<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
								</button>
								<div id="tz-blog-toc-list" class="tz-blog-toc__list"></div>
							</div>
						<?php endif; ?>

						<div class="tz-blog-single__content entry-content" id="tz-blog-content">
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

						<?php if ( ! empty( $s['show_social_share'] ) ) : ?>
							<div class="tz-blog-single__share-bottom">
								<?php Themezur_Blog::render_social_share(); ?>
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

					<?php if ( $has_sidebar ) : ?>
						<aside class="tz-blog-sidebar" role="complementary">
							<!-- Recent posts widget -->
							<div class="tz-widget tz-widget--recent">
								<h3 class="tz-widget__title"><?php esc_html_e( 'Recent Posts', 'themezur' ); ?></h3>
								<?php
								$recents = new WP_Query(
									array(
										'post_type'           => 'post',
										'post_status'         => 'publish',
										'posts_per_page'      => 5,
										'post__not_in'        => array( $pid ),
										'ignore_sticky_posts' => true,
										'no_found_rows'       => true,
									)
								);
								if ( $recents->have_posts() ) :
									?>
									<ul class="tz-widget-posts">
										<?php
										while ( $recents->have_posts() ) :
											$recents->the_post();
											?>
											<li class="tz-widget-posts__item">
												<?php if ( has_post_thumbnail() ) : ?>
													<a class="tz-widget-posts__thumb" href="<?php the_permalink(); ?>">
														<?php the_post_thumbnail( 'thumbnail' ); ?>
													</a>
												<?php endif; ?>
												<div>
													<a class="tz-widget-posts__title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
													<time class="tz-widget-posts__date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
												</div>
											</li>
										<?php endwhile; ?>
									</ul>
									<?php
									wp_reset_postdata();
								endif;
								?>
							</div>

							<!-- Categories widget -->
							<div class="tz-widget tz-widget--cats">
								<h3 class="tz-widget__title"><?php esc_html_e( 'Categories', 'themezur' ); ?></h3>
								<ul class="tz-widget-cats">
									<?php
									wp_list_categories(
										array(
											'title_li'   => '',
											'show_count' => true,
										)
									);
									?>
								</ul>
							</div>
						</aside>
					<?php endif; ?>
				</div>
			</div>
		</article>
	</main>
	<?php
endwhile;
