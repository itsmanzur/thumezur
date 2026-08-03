<?php
/**
 * Blog archive / single helpers.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Blog
 */
class Themezur_Blog {

	/**
	 * Whether Themezur blog templates should run.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return 'theme' === Themezur_Options::get( 'blog.mode', 'theme' );
	}

	/**
	 * Blog listing contexts (home + post tax/date/author archives).
	 *
	 * @return bool
	 */
	public static function is_archive_context() {
		if ( is_home() ) {
			return true;
		}
		if ( is_category() || is_tag() || is_author() || is_date() ) {
			return true;
		}
		return is_post_type_archive( 'post' );
	}

	/**
	 * @return array
	 */
	public static function archive_opts() {
		$a = Themezur_Options::get( 'blog.archive', array() );
		return is_array( $a ) ? $a : array();
	}

	/**
	 * @return array
	 */
	public static function single_opts() {
		$s = Themezur_Options::get( 'blog.single', array() );
		return is_array( $s ) ? $s : array();
	}

	/**
	 * Trimmed excerpt for cards.
	 *
	 * @param int $post_id Post ID.
	 * @param int $words   Word count.
	 * @return string
	 */
	public static function excerpt( $post_id, $words = 22 ) {
		$post_id = (int) $post_id;
		$words   = max( 5, min( 80, (int) $words ) );
		$text    = get_post_field( 'post_excerpt', $post_id );
		if ( '' === trim( (string) $text ) ) {
			$text = get_post_field( 'post_content', $post_id );
		}
		$text = wp_strip_all_tags( strip_shortcodes( (string) $text ) );
		return wp_trim_words( $text, $words, '…' );
	}

	/**
	 * Calculate estimated reading time in minutes.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public static function reading_time( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : get_the_ID();
		$content = get_post_field( 'post_content', $post_id );
		$words   = str_word_count( wp_strip_all_tags( strip_shortcodes( (string) $content ) ) );
		$minutes = max( 1, (int) ceil( $words / 200 ) );

		/* translators: %d: reading time in minutes */
		return sprintf( _n( '%d min read', '%d min read', $minutes, 'themezur' ), $minutes );
	}

	/**
	 * Related posts in same category.
	 *
	 * @param int $post_id Post ID.
	 * @param int $count   Count.
	 * @return WP_Post[]
	 */
	public static function related_posts( $post_id, $count = 3 ) {
		$post_id = (int) $post_id;
		$count   = max( 1, min( 6, (int) $count ) );
		$cats    = wp_get_post_categories( $post_id );
		$args    = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $count,
			'post__not_in'        => array( $post_id ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);
		if ( ! empty( $cats ) ) {
			$args['category__in'] = $cats;
		}
		$query = new WP_Query( $args );
		return $query->posts;
	}

	/**
	 * Archive card meta line.
	 *
	 * @param array $opts Archive options.
	 * @return void
	 */
	public static function render_card_meta( array $opts ) {
		$bits = array();
		if ( ! empty( $opts['show_date'] ) ) {
			$bits[] = '<time datetime="' . esc_attr( get_the_date( DATE_W3C ) ) . '">' . esc_html( get_the_date() ) . '</time>';
		}
		if ( ! empty( $opts['show_author'] ) ) {
			$bits[] = '<span class="tz-blog-meta__author">' . esc_html( get_the_author() ) . '</span>';
		}
		if ( ! empty( $opts['show_category'] ) ) {
			$cats = get_the_category();
			if ( ! empty( $cats[0] ) ) {
				$bits[] = '<a class="tz-blog-meta__cat" href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
			}
		}
		if ( ! empty( $opts['show_reading_time'] ) ) {
			$bits[] = '<span class="tz-blog-meta__time"><svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-right:2px;vertical-align:-1px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' . esc_html( self::reading_time() ) . '</span>';
		}
		if ( empty( $bits ) ) {
			return;
		}
		echo '<p class="tz-blog-meta">' . implode( '<span class="tz-blog-meta__sep" aria-hidden="true">·</span>', $bits ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Single post meta under title.
	 *
	 * @param array $opts Single options.
	 * @return void
	 */
	public static function render_single_meta( array $opts ) {
		self::render_card_meta(
			array(
				'show_date'         => ! empty( $opts['show_date'] ),
				'show_author'       => ! empty( $opts['show_author'] ),
				'show_category'     => ! empty( $opts['show_category'] ),
				'show_reading_time' => ! empty( $opts['show_reading_time'] ),
			)
		);
	}

	/**
	 * Render social share buttons for single posts.
	 *
	 * @return void
	 */
	public static function render_social_share() {
		$url   = rawurlencode( get_permalink() );
		$title = rawurlencode( get_the_title() );

		$fb_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
		$tw_url = 'https://twitter.com/intent/tweet?text=' . $title . '&url=' . $url;
		$li_url = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title;
		$wa_url = 'https://api.whatsapp.com/send?text=' . $title . '%20' . $url;
		?>
		<div class="tz-blog-share" aria-label="<?php esc_attr_e( 'Share this post', 'themezur' ); ?>">
			<span class="tz-blog-share__title"><?php esc_html_e( 'Share:', 'themezur' ); ?></span>
			<div class="tz-blog-share__buttons">
				<!-- Facebook -->
				<a class="tz-blog-share__btn tz-blog-share__btn--fb" href="<?php echo esc_url( $fb_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
					<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
				</a>
				<!-- X / Twitter -->
				<a class="tz-blog-share__btn tz-blog-share__btn--tw" href="<?php echo esc_url( $tw_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter)">
					<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
				</a>
				<!-- LinkedIn -->
				<a class="tz-blog-share__btn tz-blog-share__btn--li" href="<?php echo esc_url( $li_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
					<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.25V10.9H6.46M7.86 6.74a1.62 1.62 0 1 0 0 3.24 1.62 1.62 0 0 0 0-3.24z"/></svg>
				</a>
				<!-- WhatsApp -->
				<a class="tz-blog-share__btn tz-blog-share__btn--wa" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
					<svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
				</a>
				<!-- Copy Link -->
				<button class="tz-blog-share__btn tz-blog-share__btn--copy" data-url="<?php echo esc_url( get_permalink() ); ?>" type="button" aria-label="<?php esc_attr_e( 'Copy link', 'themezur' ); ?>">
					<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
				</button>
			</div>
		</div>
		<?php
	}
}
