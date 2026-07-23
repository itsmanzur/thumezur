<?php
/**
 * Elementor template helpers for Themezur.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Elementor
 */
class Themezur_Elementor {

	const CACHE_KEY = 'themezur_elementor_templates';
	const CACHE_TTL = 10 * MINUTE_IN_SECONDS;

	/**
	 * Whether Elementor is available.
	 *
	 * @return bool
	 */
	public static function is_active() {
		return did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' );
	}

	/**
	 * Boot cache invalidation hooks.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'save_post_elementor_library', array( __CLASS__, 'bust_cache' ) );
		add_action( 'trashed_post', array( __CLASS__, 'maybe_bust_on_post' ) );
		add_action( 'deleted_post', array( __CLASS__, 'maybe_bust_on_post' ) );
		add_action( 'untrashed_post', array( __CLASS__, 'maybe_bust_on_post' ) );
	}

	/**
	 * Bust template list cache.
	 *
	 * @return void
	 */
	public static function bust_cache() {
		delete_transient( self::CACHE_KEY );
	}

	/**
	 * Bust cache when an elementor_library post changes.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public static function maybe_bust_on_post( $post_id ) {
		if ( 'elementor_library' === get_post_type( $post_id ) ) {
			self::bust_cache();
		}
	}

	/**
	 * List published Elementor library templates (cached).
	 *
	 * @return array[] List of { id, title, type }.
	 */
	public static function get_templates() {
		$cached = get_transient( self::CACHE_KEY );
		if ( false !== $cached && is_array( $cached ) ) {
			return $cached;
		}

		if ( ! post_type_exists( 'elementor_library' ) ) {
			return array();
		}

		$query = new WP_Query(
			array(
				'post_type'              => 'elementor_library',
				'post_status'            => 'publish',
				'posts_per_page'         => 200,
				'orderby'                => 'title',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => false,
			)
		);

		$templates = array();
		foreach ( $query->posts as $post ) {
			$type = get_post_meta( $post->ID, '_elementor_template_type', true );
			$templates[] = array(
				'id'    => (int) $post->ID,
				'title' => $post->post_title ? $post->post_title : sprintf( __( 'Template #%d', 'themezur' ), $post->ID ),
				'type'  => $type ? (string) $type : 'page',
			);
		}

		set_transient( self::CACHE_KEY, $templates, self::CACHE_TTL );
		return $templates;
	}

	/**
	 * Templates filtered by preferred types (header/footer still includes section).
	 *
	 * @param string $context header|footer.
	 * @return array[]
	 */
	public static function get_templates_for( $context ) {
		$all = self::get_templates();
		$preferred = ( 'footer' === $context )
			? array( 'footer', 'section', 'container', 'page' )
			: array( 'header', 'section', 'container', 'page' );

		$filtered = array_values(
			array_filter(
				$all,
				static function ( $tpl ) use ( $preferred ) {
					return in_array( $tpl['type'], $preferred, true );
				}
			)
		);

		return ! empty( $filtered ) ? $filtered : $all;
	}

	/**
	 * Validate a published Elementor library template ID.
	 *
	 * @param int $template_id Template post ID.
	 * @return bool
	 */
	public static function is_valid_template( $template_id ) {
		$template_id = absint( $template_id );
		if ( $template_id < 1 ) {
			return false;
		}

		$post = get_post( $template_id );
		if ( ! $post || 'elementor_library' !== $post->post_type || 'publish' !== $post->post_status ) {
			return false;
		}

		return true;
	}

	/**
	 * Render an Elementor template by ID.
	 *
	 * @param int $template_id Template ID.
	 * @return string HTML or empty.
	 */
	public static function render_template( $template_id ) {
		$template_id = absint( $template_id );
		if ( ! self::is_active() || ! self::is_valid_template( $template_id ) ) {
			return '';
		}

		return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id );
	}
}
