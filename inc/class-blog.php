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
				'show_date'     => ! empty( $opts['show_date'] ),
				'show_author'   => ! empty( $opts['show_author'] ),
				'show_category' => ! empty( $opts['show_category'] ),
			)
		);
	}
}
