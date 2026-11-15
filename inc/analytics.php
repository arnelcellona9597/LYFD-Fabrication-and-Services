<?php
/**
 * Analytics: Google Tag Manager (GTM-XXXX) or Google Analytics 4 (G-XXXX).
 *
 * Set the ID in wp-admin → Settings → LYFD SEO → "Analytics & tracking".
 * The field accepts the bare ID or the whole snippet pasted from Google.
 *
 * Safeguards: nothing loads in wp-admin, for logged-in editors (unless enabled), on local /
 * staging hosts, or while "Discourage search engines" is on — so test traffic never pollutes
 * the real property. The ID shipped as the default is the client's GTM container.
 *
 * @package lyfd-luxe
 */

defined( 'ABSPATH' ) || exit;

/** Pull a tracking ID out of whatever was pasted (bare ID, gtm.js URL, full snippet…). */
function lyfd_extract_tracking_id( $text ) {
	if ( preg_match( '/\b(GTM-[A-Z0-9]{4,10}|G-[A-Z0-9]{6,12}|AW-[0-9]{6,12})\b/i', (string) $text, $m ) ) {
		return strtoupper( $m[1] );
	}
	return '';
}

function lyfd_is_local_host() {
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	return ( function_exists( 'wp_get_environment_type' ) && in_array( wp_get_environment_type(), array( 'local', 'development' ), true ) )
		|| ! $host
		|| 'localhost' === $host
		|| preg_match( '/(\.ddev\.site|\.local|\.test|\.localhost|\.invalid)$/i', $host ) // phpcs:ignore
		|| preg_match( '/^\d{1,3}(\.\d{1,3}){3}$/', $host );
}

/** Is tracking switched on right now, and if not, why? Returns [bool, message]. */
function lyfd_tracking_status() {
	$s  = lyfd_seo_settings();
	$id = lyfd_extract_tracking_id( $s['tracking_id'] );
	if ( '' === $id ) {
		return array( false, 'No tracking ID set.' );
	}
	if ( lyfd_is_local_host() ) {
		return array( false, 'Paused on this local/test address — it switches on automatically on the live domain.' );
	}
	if ( ! get_option( 'blog_public' ) ) {
		return array( false, 'Paused because “Discourage search engines” is ticked in Settings → Reading.' );
	}
	return array( true, 'Active on the live site (' . $id . ').' );
}

function lyfd_tracking_should_load() {
	if ( is_admin() || is_feed() || is_robots() || is_customize_preview() || wp_doing_ajax() ) {
		return false;
	}
	if ( ! lyfd_tracking_status()[0] ) {
		return false;
	}
	$s = lyfd_seo_settings();
	if ( empty( $s['track_admins'] ) && is_user_logged_in() && current_user_can( 'edit_pages' ) ) {
		return false; // don't count the team's own visits.
	}
	return true;
}

/* <head> snippet ------------------------------------------------------------ */

add_action(
	'wp_head',
	function () {
		if ( ! lyfd_tracking_should_load() ) {
			return;
		}
		$id = lyfd_extract_tracking_id( lyfd_seo_settings()['tracking_id'] );

		if ( 0 === strpos( $id, 'GTM-' ) ) {
			// Standard Google Tag Manager snippet.
			echo "<!-- Google Tag Manager -->\n<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . esc_js( $id ) . "');</script>\n<!-- End Google Tag Manager -->\n"; // phpcs:ignore WordPress.Security.EscapeOutput
			return;
		}

		// GA4 (G-…) / Google Ads (AW-…) via gtag.js.
		printf(
			"<!-- Google tag (gtag.js) -->\n<script async src=\"https://www.googletagmanager.com/gtag/js?id=%1\$s\"></script>\n<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','%2\$s');</script>\n",
			esc_attr( rawurlencode( $id ) ),
			esc_js( $id )
		);
	},
	1
);

/* <body> noscript fallback (GTM only) --------------------------------------- */

add_action(
	'wp_body_open',
	function () {
		if ( ! lyfd_tracking_should_load() ) {
			return;
		}
		$id = lyfd_extract_tracking_id( lyfd_seo_settings()['tracking_id'] );
		if ( 0 !== strpos( $id, 'GTM-' ) ) {
			return;
		}
		printf(
			'<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n",
			esc_attr( rawurlencode( $id ) )
		);
	}
);
