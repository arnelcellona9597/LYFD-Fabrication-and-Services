<?php
/**
 * LYFD Luxe – child theme of Twenty Twenty-Five.
 *
 * @package lyfd-luxe
 */

defined( 'ABSPATH' ) || exit;

define( 'LYFD_LUXE_VERSION', wp_get_theme()->get( 'Version' ) ?: '1.0.0' );

require_once get_theme_file_path( 'inc/mail.php' );
if ( is_admin() ) {
	require_once get_theme_file_path( 'inc/mail-settings.php' );
}
require_once get_theme_file_path( 'inc/forms.php' );
require_once get_theme_file_path( 'inc/booking.php' );
require_once get_theme_file_path( 'inc/floating.php' );
require_once get_theme_file_path( 'inc/seo.php' );
if ( is_admin() ) {
	require_once get_theme_file_path( 'inc/seo-admin.php' );
}
require_once get_theme_file_path( 'inc/setup.php' );

/**
 * Theme supports, editor styles and pattern category.
 */
function lyfd_luxe_setup() {
	add_editor_style( 'assets/css/lyfd.css' );
}
add_action( 'after_setup_theme', 'lyfd_luxe_setup' );

function lyfd_luxe_pattern_category() {
	register_block_pattern_category(
		'lyfd-luxe',
		array( 'label' => __( 'LYFD Luxe', 'lyfd-luxe' ) )
	);
}
add_action( 'init', 'lyfd_luxe_pattern_category' );

/**
 * Block styles for the editor (shows up in the block sidebar "Styles" panel).
 */
function lyfd_luxe_block_styles() {
	register_block_style( 'core/group', array( 'name' => 'lyfd-card', 'label' => __( '3D Card', 'lyfd-luxe' ) ) );
	register_block_style( 'core/group', array( 'name' => 'lyfd-steel', 'label' => __( 'Brushed Steel Panel', 'lyfd-luxe' ) ) );
	register_block_style( 'core/image', array( 'name' => 'lyfd-frame', 'label' => __( 'Steel Frame (3D)', 'lyfd-luxe' ) ) );
	register_block_style( 'core/heading', array( 'name' => 'lyfd-steel-text', 'label' => __( 'Steel Gradient Text', 'lyfd-luxe' ) ) );
}
add_action( 'init', 'lyfd_luxe_block_styles' );

/**
 * Front-end assets.
 */
function lyfd_luxe_assets() {
	$css = get_theme_file_path( 'assets/css/lyfd.css' );
	$js  = get_theme_file_path( 'assets/js/lyfd.js' );

	wp_enqueue_style(
		'lyfd-luxe',
		get_theme_file_uri( 'assets/css/lyfd.css' ),
		array(),
		file_exists( $css ) ? filemtime( $css ) : LYFD_LUXE_VERSION
	);

	wp_enqueue_script(
		'lyfd-luxe',
		get_theme_file_uri( 'assets/js/lyfd.js' ),
		array(),
		file_exists( $js ) ? filemtime( $js ) : LYFD_LUXE_VERSION,
		array( 'strategy' => 'defer', 'in_footer' => true )
	);

	// Contact form + booking calendar live on the homepage only.
	if ( is_front_page() ) {
		$forms = get_theme_file_path( 'assets/js/forms.js' );
		wp_enqueue_script(
			'lyfd-forms',
			get_theme_file_uri( 'assets/js/forms.js' ),
			array(),
			file_exists( $forms ) ? filemtime( $forms ) : LYFD_LUXE_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'lyfd_luxe_assets' );

/**
 * <head> extras: JS flag (so reveal animations never hide content when JS is off),
 * font preload, theme colour and a fallback favicon.
 */
function lyfd_luxe_head() {
	echo "<script>document.documentElement.classList.add('lyfd-js');</script>\n";
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( get_theme_file_uri( 'assets/fonts/saira-italic-600-800.woff2' ) )
	);
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( get_theme_file_uri( 'assets/fonts/barlow-normal-500.woff2' ) )
	);
	echo '<meta name="theme-color" content="#05090A">' . "\n";

	if ( ! has_site_icon() ) {
		printf(
			'<link rel="icon" type="image/png" sizes="192x192" href="%s">' . "\n",
			esc_url( get_theme_file_uri( 'assets/images/lyfd-icon-192.png' ) )
		);
	}
}
add_action( 'wp_head', 'lyfd_luxe_head', 1 );

/**
 * Helper used by patterns: path of a file inside assets/images.
 * Root-relative on purpose: page content stays valid when the domain or http/https changes
 * (local DDEV → staging → live) without a search-replace.
 */
function lyfd_img( $file ) {
	return esc_url( wp_make_link_relative( get_theme_file_uri( 'assets/images/' . ltrim( $file, '/' ) ) ) );
}

