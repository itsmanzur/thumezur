<?php
/**
 * Themezur social network helpers.
 *
 * @package Themezur
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Themezur_Social
 */
class Themezur_Social {

	/**
	 * Supported networks: slug => label.
	 *
	 * @return array<string,string>
	 */
	public static function networks() {
		return array(
			'facebook'  => 'Facebook',
			'x'         => 'X / Twitter',
			'instagram' => 'Instagram',
			'youtube'   => 'YouTube',
			'linkedin'  => 'LinkedIn',
			'whatsapp'  => 'WhatsApp',
			'telegram'  => 'Telegram',
			'tiktok'    => 'TikTok',
			'pinterest' => 'Pinterest',
			'email'     => 'Email',
			'website'   => 'Website',
		);
	}

	/**
	 * Normalize socials list from options (with legacy migration).
	 *
	 * @param array $top Header top options.
	 * @return array<int,array{network:string,url:string}>
	 */
	public static function normalize_list( array $top ) {
		$socials = array();

		if ( ! empty( $top['socials'] ) && is_array( $top['socials'] ) ) {
			foreach ( $top['socials'] as $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}
				$network = isset( $item['network'] ) ? sanitize_key( $item['network'] ) : '';
				$url     = isset( $item['url'] ) ? esc_url_raw( $item['url'] ) : '';
				if ( $network && $url && isset( self::networks()[ $network ] ) ) {
					$socials[] = array(
						'network' => $network,
						'url'     => $url,
					);
				}
			}
		}

		if ( empty( $socials ) ) {
			$legacy = array(
				'x'         => 'social_x',
				'facebook'  => 'social_facebook',
				'instagram' => 'social_instagram',
			);
			foreach ( $legacy as $network => $key ) {
				if ( ! empty( $top[ $key ] ) ) {
					$socials[] = array(
						'network' => $network,
						'url'     => esc_url_raw( $top[ $key ] ),
					);
				}
			}
		}

		return $socials;
	}

	/**
	 * SVG icon markup for a network.
	 *
	 * @param string $network Network slug.
	 * @return string
	 */
	public static function icon_svg( $network ) {
		$icons = array(
			'facebook'  => '<path d="M22 12.07C22 6.48 17.52 2 11.93 2S2 6.48 2 12.07c0 5.02 3.66 9.18 8.44 9.93v-7.03H8.08v-2.9h2.36V9.41c0-2.33 1.39-3.62 3.52-3.62 1.02 0 2.09.18 2.09.18v2.3h-1.18c-1.16 0-1.52.72-1.52 1.46v1.75h2.59l-.41 2.9h-2.18V22c4.78-.75 8.44-4.91 8.44-9.93z"/>',
			'x'         => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.727-8.849L1.25 2.25H8.08l4.253 5.622L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/>',
			'instagram' => '<path d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm0 2a3 3 0 00-3 3v10a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H7zm5 3.5A4.5 4.5 0 1112 16.5 4.5 4.5 0 0112 7.5zm0 2A2.5 2.5 0 1014.5 12 2.5 2.5 0 0012 9.5zM17.5 6.75a1.05 1.05 0 11-1.05 1.05A1.05 1.05 0 0117.5 6.75z"/>',
			'youtube'   => '<path d="M23.5 6.2a3 3 0 00-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 00.5 6.2 31.5 31.5 0 000 12a31.5 31.5 0 00.5 5.8 3 3 0 002.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 002.1-2.1A31.5 31.5 0 0024 12a31.5 31.5 0 00-.5-5.8zM9.75 15.5v-7l6.5 3.5-6.5 3.5z"/>',
			'linkedin'  => '<path d="M4.98 3.5C4.98 4.88 3.88 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.5 8.5h4V24h-4V8.5zM8.5 8.5h3.8v2.1h.1c.5-1 1.8-2.1 3.8-2.1 4 0 4.8 2.7 4.8 6.1V24h-4v-7.1c0-1.7 0-3.9-2.4-3.9s-2.7 1.8-2.7 3.8V24h-4V8.5z"/>',
			'whatsapp'  => '<path d="M20.5 3.5A10.5 10.5 0 003.4 18.7L2 22.5l3.9-1.3A10.5 10.5 0 0020.5 3.5zm-8.5 16a8.5 8.5 0 01-4.3-1.2l-.3-.2-2.3.8.8-2.2-.2-.3a8.5 8.5 0 1111.8 3.1A8.4 8.4 0 0112 19.5zm4.9-6.3c-.3-.1-1.6-.8-1.8-.9s-.4-.1-.6.1-.7.9-.8 1-.3.2-.6.1a7 7 0 01-2-1.2 7.5 7.5 0 01-1.4-1.7c-.1-.3 0-.4.1-.6l.4-.5c.1-.1.2-.3.3-.4s0-.3 0-.4-.6-1.4-.8-1.9-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3a2 2 0 00-.6 1.5 3.5 3.5 0 00.7 1.9 8 8 0 003.4 3.1 11 11 0 002 .8 2.4 2.4 0 001.6.1 2.8 2.8 0 001.3-1.1 1.8 1.8 0 00.1-1.1c-.1-.1-.2-.1-.5-.2z"/>',
			'telegram'  => '<path d="M9.04 15.3l-.38 5.3c.54 0 .78-.23 1.06-.5l2.55-2.43 5.29 3.88c.97.53 1.66.25 1.93-.9L23.7 3.84c.31-1.4-.52-1.95-1.45-1.62L1.6 9.35C.24 9.87.26 10.64 1.37 10.97l5.45 1.7L18.9 5.6c.6-.4 1.15-.18.7.22L9.04 15.3z"/>',
			'tiktok'    => '<path d="M19.6 7.2a6.5 6.5 0 01-3.7-1.2v7.1a5.7 5.7 0 11-5.7-5.7c.3 0 .6 0 .9.1v2.8a2.9 2.9 0 100 5.7 2.9 2.9 0 002.9-2.9V2h2.9a6.5 6.5 0 003.7 3.6v1.6z"/>',
			'pinterest' => '<path d="M12 2a10 10 0 00-3.6 19.3c-.1-.8-.2-2.1 0-3l1.3-5.5s-.3-.7-.3-1.6c0-1.5.9-2.6 2-2.6.9 0 1.4.7 1.4 1.5 0 .9-.6 2.3-.9 3.5-.3 1.1.5 1.9 1.6 1.9 1.9 0 3.2-2.4 3.2-5.3 0-2.2-1.5-3.8-4.2-3.8-3.1 0-5 2.3-5 4.8 0 .9.3 1.8.7 2.3.1.1.1.2.1.3l-.3 1c0 .2-.2.2-.4.1-1.4-.6-2.1-2.2-2.1-4 0-3 2.5-6.6 7.5-6.6 4 0 6.7 2.9 6.7 6 0 4.1-2.3 7.1-5.6 7.1-1.1 0-2.2-.6-2.5-1.3l-.7 2.6c-.2.9-.9 2-1.3 2.7A10 10 0 0012 2z"/>',
			'email'     => '<path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>',
			'website'   => '<path d="M12 2a10 10 0 100 20 10 10 0 000-20zm7.9 9h-3.2a15 15 0 00-1.3-5 8.1 8.1 0 014.5 5zM12 4c.9 0 2.3 2.1 3 7H9c.7-4.9 2.1-7 3-7zM4.1 11h3.2a15 15 0 011.3-5 8.1 8.1 0 00-4.5 5zM4.1 13a8.1 8.1 0 004.5 5 15 15 0 01-1.3-5H4.1zm4.9 0h6c-.7 4.9-2.1 7-3 7s-2.3-2.1-3-7zm7.7 0a15 15 0 01-1.3 5 8.1 8.1 0 004.5-5h-3.2z"/>',
		);

		$path = isset( $icons[ $network ] ) ? $icons[ $network ] : $icons['website'];
		return '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">' . $path . '</svg>';
	}
}
