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
				'id'      => 'system',
				'label'   => 'System UI',
				'family'  => 'system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Bengali", sans-serif',
				'google'  => '',
				'weights' => array( '300', '400', '500', '600', '700' ),
			),
			array(
				'id'      => 'inter',
				'label'   => 'Inter',
				'family'  => '"Inter", system-ui, sans-serif',
				'google'  => 'Inter',
				'weights' => array( '300', '400', '500', '600', '700' ),
			),
			array(
				'id'      => 'poppins',
				'label'   => 'Poppins',
				'family'  => '"Poppins", system-ui, sans-serif',
				'google'  => 'Poppins',
				'weights' => array( '300', '400', '500', '600', '700', '800' ),
			),
			array(
				'id'      => 'lato',
				'label'   => 'Lato',
				'family'  => '"Lato", system-ui, sans-serif',
				'google'  => 'Lato',
				'weights' => array( '300', '400', '700', '900' ),
			),
			array(
				'id'      => 'roboto',
				'label'   => 'Roboto',
				'family'  => '"Roboto", system-ui, sans-serif',
				'google'  => 'Roboto',
				'weights' => array( '300', '400', '500', '700', '900' ),
			),
			array(
				'id'      => 'dm_sans',
				'label'   => 'DM Sans',
				'family'  => '"DM Sans", system-ui, sans-serif',
				'google'  => 'DM+Sans',
				'weights' => array( '300', '400', '500', '600', '700' ),
			),
			array(
				'id'      => 'manrope',
				'label'   => 'Manrope',
				'family'  => '"Manrope", system-ui, sans-serif',
				'google'  => 'Manrope',
				'weights' => array( '300', '400', '500', '600', '700', '800' ),
			),
			array(
				'id'      => 'plus_jakarta',
				'label'   => 'Plus Jakarta Sans',
				'family'  => '"Plus Jakarta Sans", system-ui, sans-serif',
				'google'  => 'Plus+Jakarta+Sans',
				'weights' => array( '300', '400', '500', '600', '700', '800' ),
			),
			array(
				'id'      => 'outfit',
				'label'   => 'Outfit',
				'family'  => '"Outfit", system-ui, sans-serif',
				'google'  => 'Outfit',
				'weights' => array( '300', '400', '500', '600', '700' ),
			),
			array(
				'id'      => 'source_sans',
				'label'   => 'Source Sans 3',
				'family'  => '"Source Sans 3", system-ui, sans-serif',
				'google'  => 'Source+Sans+3',
				'weights' => array( '300', '400', '500', '600', '700' ),
			),
			array(
				'id'      => 'nunito_sans',
				'label'   => 'Nunito Sans',
				'family'  => '"Nunito Sans", system-ui, sans-serif',
				'google'  => 'Nunito+Sans',
				'weights' => array( '300', '400', '500', '600', '700', '800' ),
			),
			array(
				'id'      => 'rubik',
				'label'   => 'Rubik',
				'family'  => '"Rubik", system-ui, sans-serif',
				'google'  => 'Rubik',
				'weights' => array( '300', '400', '500', '600', '700' ),
			),
			array(
				'id'      => 'noto_sans',
				'label'   => 'Noto Sans',
				'family'  => '"Noto Sans", "Noto Sans Bengali", system-ui, sans-serif',
				'google'  => 'Noto+Sans&family=Noto+Sans+Bengali',
				'weights' => array( '300', '400', '500', '600', '700' ),
			),
			array(
				'id'      => 'hind_siliguri',
				'label'   => 'Hind Siliguri (বাংলা)',
				'family'  => '"Hind Siliguri", "Noto Sans Bengali", system-ui, sans-serif',
				'google'  => 'Hind+Siliguri',
				'weights' => array( '300', '400', '500', '600', '700' ),
			),
			array(
				'id'      => 'noto_bengali',
				'label'   => 'Noto Sans Bengali (বাংলা)',
				'family'  => '"Noto Sans Bengali", system-ui, sans-serif',
				'google'  => 'Noto+Sans+Bengali',
				'weights' => array( '100', '200', '300', '400', '500', '600', '700', '800', '900' ),
			),
			array(
				'id'      => 'literata',
				'label'   => 'Literata (serif)',
				'family'  => '"Literata", Georgia, serif',
				'google'  => 'Literata',
				'weights' => array( '300', '400', '500', '600', '700' ),
			),
			array(
				'id'      => 'fraunces',
				'label'   => 'Fraunces (display)',
				'family'  => '"Fraunces", Georgia, serif',
				'google'  => 'Fraunces',
				'weights' => array( '300', '400', '500', '600', '700', '900' ),
			),
			array(
				'id'      => 'custom',
				'label'   => 'Custom stack…',
				'family'  => '',
				'google'  => '',
				'weights' => array( '300', '400', '500', '600', '700' ),
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
	 * Available weights for a given font id.
	 *
	 * @param string $id Font id.
	 * @return string[]
	 */
	public static function weights( $id ) {
		$font = self::get( $id );
		if ( $font && ! empty( $font['weights'] ) ) {
			return $font['weights'];
		}
		return array( '300', '400', '500', '600', '700' );
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
	 * Build a Google Fonts URL for a set of font+weight combinations.
	 *
	 * @param array[] $requests Array of ['google'=>string, 'weights'=>string[]].
	 * @return string|null  URL or null if nothing to load.
	 */
	private static function build_gfonts_url( array $requests ) {
		$parts = array();
		foreach ( $requests as $req ) {
			if ( empty( $req['google'] ) ) {
				continue;
			}
			$w = ! empty( $req['weights'] ) ? $req['weights'] : array( '400' );
			$w = array_unique( array_filter( array_map( 'absint', $w ) ) );
			sort( $w );
			$parts[] = $req['google'] . ':wght@' . implode( ';', $w );
		}
		if ( empty( $parts ) ) {
			return null;
		}
		return 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', $parts ) . '&display=swap';
	}

	/**
	 * Enqueue Google Fonts for selected body/heading ids + weights.
	 *
	 * @return void
	 */
	public static function enqueue() {
		$body_id      = Themezur_Options::get( 'general.font_body_id', 'system' );
		$head_id      = Themezur_Options::get( 'general.font_heading_id', 'system' );
		$body_weights = Themezur_Options::get( 'general.font_body_weights', array( '400' ) );
		$head_weights = Themezur_Options::get( 'general.font_heading_weights', array( '700' ) );

		if ( ! is_array( $body_weights ) || empty( $body_weights ) ) {
			$body_weights = array( '400' );
		}
		if ( ! is_array( $head_weights ) || empty( $head_weights ) ) {
			$head_weights = array( '700' );
		}

		$seen = array();

		foreach (
			array(
				array( 'id' => $body_id, 'weights' => $body_weights ),
				array( 'id' => $head_id, 'weights' => $head_weights ),
			) as $item
		) {
			$font = self::get( $item['id'] );
			if ( ! $font || empty( $font['google'] ) ) {
				continue;
			}
			$key = $font['google'];
			if ( isset( $seen[ $key ] ) ) {
				// Merge weights for the same font family.
				$seen[ $key ]['weights'] = array_unique( array_merge( $seen[ $key ]['weights'], $item['weights'] ) );
			} else {
				$seen[ $key ] = array( 'google' => $font['google'], 'weights' => $item['weights'] );
			}
		}

		$url = self::build_gfonts_url( array_values( $seen ) );

		if ( ! $url ) {
			return;
		}

		wp_enqueue_style( 'themezur-google-fonts', $url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}

	/**
	 * Return the font catalog as a compact array for JS.
	 *
	 * @return array[]
	 */
	public static function catalog_for_js() {
		$out = array();
		foreach ( self::catalog() as $font ) {
			$out[] = array(
				'id'      => $font['id'],
				'label'   => $font['label'],
				'family'  => $font['family'],
				'google'  => $font['google'],
				'weights' => $font['weights'],
			);
		}
		return $out;
	}
}
