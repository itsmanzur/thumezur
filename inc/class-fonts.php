<?php
/**
 * Curated font catalog (system + Google Fonts).
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Fonts
 */
class Themezur_Fonts {

	/**
	 * Font catalog for the admin picker.
	 *
	 * @return array[]
	 */
	public static function catalog() {
		$fonts = array(
			array(
				'id'     => 'system',
				'label'  => 'System UI',
				'family' => 'system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Bengali", sans-serif',
				'google' => '',
			),
			array(
				'id'     => 'inter',
				'label'  => 'Inter',
				'family' => '"Inter", system-ui, sans-serif',
				'google' => 'Inter:wght@400;500;600;700',
			),
			array(
				'id'     => 'dm_sans',
				'label'  => 'DM Sans',
				'family' => '"DM Sans", system-ui, sans-serif',
				'google' => 'DM+Sans:wght@400;500;600;700',
			),
			array(
				'id'     => 'manrope',
				'label'  => 'Manrope',
				'family' => '"Manrope", system-ui, sans-serif',
				'google' => 'Manrope:wght@400;500;600;700',
			),
			array(
				'id'     => 'plus_jakarta',
				'label'  => 'Plus Jakarta Sans',
				'family' => '"Plus Jakarta Sans", system-ui, sans-serif',
				'google' => 'Plus+Jakarta+Sans:wght@400;500;600;700',
			),
			array(
				'id'     => 'outfit',
				'label'  => 'Outfit',
				'family' => '"Outfit", system-ui, sans-serif',
				'google' => 'Outfit:wght@400;500;600;700',
			),
			array(
				'id'     => 'source_sans',
				'label'  => 'Source Sans 3',
				'family' => '"Source Sans 3", system-ui, sans-serif',
				'google' => 'Source+Sans+3:wght@400;500;600;700',
			),
			array(
				'id'     => 'nunito_sans',
				'label'  => 'Nunito Sans',
				'family' => '"Nunito Sans", system-ui, sans-serif',
				'google' => 'Nunito+Sans:wght@400;500;600;700',
			),
			array(
				'id'     => 'rubik',
				'label'  => 'Rubik',
				'family' => '"Rubik", system-ui, sans-serif',
				'google' => 'Rubik:wght@400;500;600;700',
			),
			array(
				'id'     => 'noto_sans',
				'label'  => 'Noto Sans',
				'family' => '"Noto Sans", "Noto Sans Bengali", system-ui, sans-serif',
				'google' => 'Noto+Sans:wght@400;500;600;700&family=Noto+Sans+Bengali:wght@400;500;600;700',
			),
			array(
				'id'     => 'literata',
				'label'  => 'Literata (serif)',
				'family' => '"Literata", Georgia, serif',
				'google' => 'Literata:wght@400;500;600;700',
			),
			array(
				'id'     => 'fraunces',
				'label'  => 'Fraunces (display)',
				'family' => '"Fraunces", Georgia, serif',
				'google' => 'Fraunces:wght@400;500;600;700',
			),
			array(
				'id'     => 'custom',
				'label'  => 'Custom stack…',
				'family' => '',
				'google' => '',
			),
		);

		/**
		 * Filter Themezur font catalog.
		 *
		 * @param array[] $fonts Fonts.
		 */
		return apply_filters( 'themezur_font_catalog', $fonts );
	}

	/**
	 * @param string $id Font id.
	 * @return array|null
	 */
	public static function get( $id ) {
		$id = sanitize_key( $id );
		foreach ( self::catalog() as $font ) {
			if ( $font['id'] === $id ) {
				return $font;
			}
		}
		return null;
	}

	/**
	 * Resolve CSS font-family from id + optional custom stack.
	 *
	 * @param string $id     Font id.
	 * @param string $custom Custom stack when id=custom.
	 * @return string
	 */
	public static function family( $id, $custom = '' ) {
		$font = self::get( $id );
		if ( ! $font ) {
			$font = self::get( 'system' );
		}
		if ( 'custom' === $font['id'] ) {
			$custom = is_string( $custom ) ? trim( $custom ) : '';
			return $custom ? $custom : self::get( 'system' )['family'];
		}
		return $font['family'];
	}

	/**
	 * Enqueue Google Fonts for selected body/heading ids.
	 *
	 * @return void
	 */
	public static function enqueue() {
		$body_id = Themezur_Options::get( 'general.font_body_id', 'system' );
		$head_id = Themezur_Options::get( 'general.font_heading_id', 'system' );
		$families = array();

		foreach ( array( $body_id, $head_id ) as $id ) {
			$font = self::get( $id );
			if ( $font && ! empty( $font['google'] ) ) {
				$families[] = $font['google'];
			}
		}

		$families = array_values( array_unique( $families ) );
		if ( empty( $families ) ) {
			return;
		}

		$url = 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', $families ) . '&display=swap';
		wp_enqueue_style( 'themezur-google-fonts', $url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}
}
