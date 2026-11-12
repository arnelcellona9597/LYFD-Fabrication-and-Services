<?php
/**
 * One-time site bootstrap, run when the theme is activated.
 *
 * Creates the two pages (Home, Products) from the theme's patterns, makes Home
 * the static front page and sets nice permalinks. Idempotent: existing pages
 * are never overwritten, so client edits are safe.
 *
 * @package lyfd-luxe
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render a pattern file from /patterns to a string of block markup.
 */
function lyfd_luxe_pattern_markup( $name ) {
	$file = get_theme_file_path( 'patterns/' . $name . '.php' );
	if ( ! file_exists( $file ) ) {
		return '';
	}
	ob_start();
	include $file;
	return trim( ob_get_clean() );
}

function lyfd_luxe_page_content( $slugs ) {
	$out = array();
	foreach ( $slugs as $slug ) {
		$out[] = lyfd_luxe_pattern_markup( $slug );
	}
	return implode( "\n\n", array_filter( $out ) );
}

function lyfd_luxe_find_page( $slug ) {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	return $page ? (int) $page->ID : 0;
}

function lyfd_luxe_setup_site() {
	$pages = array(
		'home'     => array(
			'title'    => 'Home',
			'patterns' => array( 'home-hero', 'home-ticker', 'home-about', 'home-capabilities', 'home-approach', 'home-featured', 'home-booking', 'home-contact' ),
		),
		'products' => array(
			'title'    => 'Products',
			'patterns' => array( 'products-hero', 'products-gallery', 'products-reels', 'products-cta' ),
		),
	);

	$ids = array();
	foreach ( $pages as $slug => $def ) {
		$id = lyfd_luxe_find_page( $slug );
		if ( ! $id ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $def['title'],
					'post_name'    => $slug,
					'post_content' => lyfd_luxe_page_content( $def['patterns'] ),
				)
			);
		}
		if ( $id && ! is_wp_error( $id ) ) {
			$ids[ $slug ] = (int) $id;
		}
	}

	if ( ! empty( $ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
	}

	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules( false );
	}

	// Only replace WordPress' placeholder identity, never a client-set one.
	if ( in_array( get_option( 'blogname' ), array( '', 'kaylyfdfab', 'My WordPress Site', 'WordPress' ), true ) ) {
		update_option( 'blogname', 'LYFD Stainless Fabrication and Services' );
	}
	if ( in_array( get_option( 'blogdescription' ), array( '', 'Just another WordPress site' ), true ) ) {
		update_option( 'blogdescription', 'Custom stainless steel fabrication, kitchen hoods, ducting and metal works in Davao City' );
	}

	// Business is in Davao City: bookings and email timestamps use Philippine time.
	if ( ! get_option( 'timezone_string' ) || 'UTC' === get_option( 'timezone_string' ) ) {
		update_option( 'timezone_string', 'Asia/Manila' );
	}

	// The site has exactly two pages. Remove WordPress' starter content (untouched stubs only).
	$stubs = array(
		'sample-page'    => 'page',
		'privacy-policy' => 'page',
		'hello-world'    => 'post',
	);
	foreach ( $stubs as $slug => $type ) {
		$stub = get_page_by_path( $slug, OBJECT, $type );
		if ( $stub && ( 'sample-page' === $slug || 'hello-world' === $slug || 'draft' === $stub->post_status ) ) {
			wp_delete_post( $stub->ID, true );
		}
	}

	update_option( 'lyfd_luxe_setup_done', LYFD_LUXE_VERSION );
}
add_action( 'after_switch_theme', 'lyfd_luxe_setup_site' );

/**
 * Safety net: if the theme was dropped in without a "switch" event
 * (e.g. deployed via git on a site where it is already active), run setup once.
 */
function lyfd_luxe_maybe_setup() {
	if ( ! get_option( 'lyfd_luxe_setup_done' ) && 'lyfd-luxe' === get_stylesheet() ) {
		lyfd_luxe_setup_site();
	}
}
add_action( 'init', 'lyfd_luxe_maybe_setup', 20 );
