<?php
/**
 * Built-in SEO (no plugin needed): titles, meta description, robots, Open Graph / Twitter cards,
 * JSON-LD structured data (LocalBusiness, WebSite, WebPage, Breadcrumbs, ImageGallery),
 * a lean XML sitemap and robots.txt.
 *
 * Edit per page: the "SEO" box under the page editor.
 * Site-wide: Settings → LYFD SEO (business details, verification codes, default share image).
 *
 * Steps aside automatically if Yoast SEO, Rank Math, SEOPress or AIOSEO is activated later.
 *
 * @package lyfd-luxe
 */

defined( 'ABSPATH' ) || exit;

/** True when a dedicated SEO plugin is active — then the theme outputs nothing to avoid duplicates. */
function lyfd_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/* ---------------------------------------------------------------------------
 * Site-wide settings
 * ------------------------------------------------------------------------- */

function lyfd_seo_defaults() {
	return array(
		'business_name' => 'LYFD Stainless Fabrication and Services',
		'business_type' => 'HomeAndConstructionBusiness',
		'phone'         => '+63 933 064 8606',
		'email'         => 'info@lyfdfab.com',
		'street'        => '',
		'city'          => 'Davao City',
		'region'        => 'Davao del Sur',
		'postal'        => '8000',
		'country'       => 'PH',
		'lat'           => '',   // workshop GPS (optional) — set in Settings → LYFD SEO.
		'lng'           => '',
		'facebook'      => 'https://www.facebook.com/profile.php?id=61595745041102',
		'google_verify' => '',
		'bing_verify'   => '',
		'share_image'   => '', // empty → assets/images/og-image.jpg.
		'title_sep'     => '|',
	);
}

function lyfd_seo_settings() {
	$saved = get_option( 'lyfd_seo_settings', array() );
	return wp_parse_args( is_array( $saved ) ? $saved : array(), lyfd_seo_defaults() );
}

/** Built-in defaults for the two pages, used until the page's SEO box is filled in. */
function lyfd_seo_page_defaults() {
	return array(
		'home'     => array(
			'title' => 'Stainless Steel Fabrication in Davao City | LYFD',
			'desc'  => 'Custom stainless steel kitchens, counters, sinks, kitchen hoods, ducting and blowers in Davao City — fabricated and installed by LYFD. Book a visit.',
		),
		'products' => array(
			'title' => 'Stainless Kitchens, Hoods & Blowers | LYFD Davao',
			'desc'  => 'Photos and reels of LYFD’s stainless work in Davao City: commercial kitchens, counters, sinks, cooking stations, kitchen hoods, ducting and blowers.',
		),
	);
}

/* ---------------------------------------------------------------------------
 * Per-page fields (stored as post meta, edited in the "SEO" box)
 * ------------------------------------------------------------------------- */

add_action(
	'init',
	function () {
		foreach ( array( '_lyfd_seo_title', '_lyfd_seo_desc', '_lyfd_seo_image', '_lyfd_seo_noindex' ) as $key ) {
			register_post_meta(
				'page',
				$key,
				array(
					'single'            => true,
					'type'              => 'string',
					'show_in_rest'      => false,
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => function () {
						return current_user_can( 'edit_pages' );
					},
				)
			);
		}
	}
);

function lyfd_seo_page_key( $post ) {
	if ( ! $post ) {
		return '';
	}
	if ( (int) get_option( 'page_on_front' ) === (int) $post->ID ) {
		return 'home';
	}
	return 'products' === $post->post_name ? 'products' : '';
}

/** Everything the head needs for the current request. */
function lyfd_seo_context() {
	static $ctx = null;
	if ( null !== $ctx ) {
		return $ctx;
	}
	$s    = lyfd_seo_settings();
	$post = is_singular() ? get_queried_object() : null;
	$key  = $post ? lyfd_seo_page_key( $post ) : '';
	$def  = lyfd_seo_page_defaults();

	$title = $post ? trim( (string) get_post_meta( $post->ID, '_lyfd_seo_title', true ) ) : '';
	$desc  = $post ? trim( (string) get_post_meta( $post->ID, '_lyfd_seo_desc', true ) ) : '';
	if ( '' === $title && $key ) {
		$title = $def[ $key ]['title'];
	}
	if ( '' === $desc && $key ) {
		$desc = $def[ $key ]['desc'];
	}
	if ( '' === $desc && $post ) {
		$desc = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 28, '…' );
	}
	if ( '' === $desc ) {
		$desc = get_bloginfo( 'description' );
	}

	$image = $post ? trim( (string) get_post_meta( $post->ID, '_lyfd_seo_image', true ) ) : '';
	if ( '' === $image && $post && has_post_thumbnail( $post ) ) {
		$image = (string) get_the_post_thumbnail_url( $post, 'full' );
	}
	if ( '' === $image ) {
		$image = $s['share_image'] ? $s['share_image'] : get_theme_file_uri( 'assets/images/og-image.jpg' );
	}

	$url = $post ? get_permalink( $post ) : home_url( add_query_arg( array() ) );
	if ( is_front_page() ) {
		$url = home_url( '/' );
	}

	$ctx = array(
		'title'   => $title,
		'desc'    => mb_substr( wp_strip_all_tags( $desc ), 0, 300 ),
		'image'   => $image,
		'url'     => $url,
		'key'     => $key,
		'noindex' => $post && '1' === get_post_meta( $post->ID, '_lyfd_seo_noindex', true ),
		'post'    => $post,
	);
	return $ctx;
}

/* ---------------------------------------------------------------------------
 * <title>
 * ------------------------------------------------------------------------- */

add_filter(
	'pre_get_document_title',
	function ( $title ) {
		if ( lyfd_seo_plugin_active() || ! is_singular() ) {
			return $title;
		}
		$ctx = lyfd_seo_context();
		return $ctx['title'] ? $ctx['title'] : $title;
	},
	20
);

add_filter(
	'document_title_separator',
	function ( $sep ) {
		return lyfd_seo_plugin_active() ? $sep : lyfd_seo_settings()['title_sep'];
	}
);

/* ---------------------------------------------------------------------------
 * robots meta: keep thin/utility pages out of Google
 * ------------------------------------------------------------------------- */

add_filter(
	'wp_robots',
	function ( $robots ) {
		if ( lyfd_seo_plugin_active() || ! get_option( 'blog_public' ) ) {
			return $robots;
		}
		$ctx = lyfd_seo_context();
		if ( $ctx['noindex'] || is_search() || is_404() || is_attachment() || is_author() || is_date() || ( is_archive() && ! is_post_type_archive() ) ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
			unset( $robots['max-image-preview'] );
		} else {
			$robots['index']             = true;
			$robots['follow']            = true;
			$robots['max-image-preview'] = 'large';
			$robots['max-snippet']       = '-1';
		}
		return $robots;
	}
);

/* ---------------------------------------------------------------------------
 * Meta description, Open Graph, Twitter, verification
 * ------------------------------------------------------------------------- */

add_action(
	'wp_head',
	function () {
		if ( lyfd_seo_plugin_active() ) {
			return;
		}
		$s   = lyfd_seo_settings();
		$ctx = lyfd_seo_context();

		$title = $ctx['title'] ? $ctx['title'] : wp_get_document_title();
		$out   = array();

		$out[] = sprintf( '<meta name="description" content="%s">', esc_attr( $ctx['desc'] ) );
		if ( $s['google_verify'] ) {
			$out[] = sprintf( '<meta name="google-site-verification" content="%s">', esc_attr( $s['google_verify'] ) );
		}
		if ( $s['bing_verify'] ) {
			$out[] = sprintf( '<meta name="msvalidate.01" content="%s">', esc_attr( $s['bing_verify'] ) );
		}

		$og = array(
			'og:locale'       => str_replace( '-', '_', get_bloginfo( 'language' ) ),
			'og:type'         => is_front_page() ? 'website' : 'article',
			'og:site_name'    => $s['business_name'],
			'og:title'        => $title,
			'og:description'  => $ctx['desc'],
			'og:url'          => $ctx['url'],
			'og:image'        => $ctx['image'],
			'og:image:alt'    => $s['business_name'],
		);
		if ( false !== strpos( $ctx['image'], 'og-image.jpg' ) ) {
			$og['og:image:width']  = '1200';
			$og['og:image:height'] = '630';
		}
		foreach ( $og as $prop => $val ) {
			$out[] = sprintf( '<meta property="%s" content="%s">', esc_attr( $prop ), esc_attr( $val ) );
		}
		if ( $ctx['post'] ) {
			$out[] = sprintf( '<meta property="article:modified_time" content="%s">', esc_attr( get_post_modified_time( 'c', true, $ctx['post'] ) ) );
		}
		$out[] = '<meta name="twitter:card" content="summary_large_image">';
		$out[] = sprintf( '<meta name="twitter:title" content="%s">', esc_attr( $title ) );
		$out[] = sprintf( '<meta name="twitter:description" content="%s">', esc_attr( $ctx['desc'] ) );
		$out[] = sprintf( '<meta name="twitter:image" content="%s">', esc_url( $ctx['image'] ) );
		$out[] = '<meta name="geo.region" content="PH-DAS"><meta name="geo.placename" content="' . esc_attr( $s['city'] ) . '">';

		echo "\n<!-- LYFD SEO -->\n" . implode( "\n", $out ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above.
	},
	2
);

/* ---------------------------------------------------------------------------
 * JSON-LD structured data
 * ------------------------------------------------------------------------- */

function lyfd_seo_abs( $path ) {
	return 0 === strpos( $path, 'http' ) ? $path : home_url( $path );
}

add_action(
	'wp_head',
	function () {
		if ( lyfd_seo_plugin_active() || ! is_singular() ) {
			return;
		}
		$s     = lyfd_seo_settings();
		$ctx   = lyfd_seo_context();
		$home  = home_url( '/' );
		$biz   = $home . '#business';
		$site  = $home . '#website';
		$logo  = get_theme_file_uri( 'assets/images/lyfd-logo.webp' );

		$address = array_filter(
			array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $s['street'],
				'addressLocality' => $s['city'],
				'addressRegion'   => $s['region'],
				'postalCode'      => $s['postal'],
				'addressCountry'  => $s['country'],
			)
		);

		$business = array_filter(
			array(
				'@type'       => array( 'LocalBusiness', $s['business_type'] ),
				'@id'         => $biz,
				'name'        => $s['business_name'],
				'alternateName' => 'LYFD',
				'description' => lyfd_seo_page_defaults()['home']['desc'],
				'url'         => $home,
				'logo'        => array( '@type' => 'ImageObject', 'url' => $logo ),
				'image'       => get_theme_file_uri( 'assets/images/og-image.jpg' ),
				'telephone'   => $s['phone'],
				'email'       => $s['email'],
				'address'     => $address,
				'geo'         => ( $s['lat'] && $s['lng'] ) ? array( '@type' => 'GeoCoordinates', 'latitude' => (float) $s['lat'], 'longitude' => (float) $s['lng'] ) : null,
				'areaServed'  => array(
					array( '@type' => 'City', 'name' => 'Davao City' ),
					array( '@type' => 'AdministrativeArea', 'name' => 'Davao Region' ),
					array( '@type' => 'Country', 'name' => 'Philippines' ),
				),
				'sameAs'      => array_values( array_filter( array( $s['facebook'] ) ) ),
				'knowsAbout'  => array( 'Stainless steel fabrication', 'Commercial kitchen equipment', 'Kitchen exhaust hoods', 'Ducting systems', 'Centrifugal blowers', 'Welding', 'Metal works' ),
				'hasOfferCatalog' => array(
					'@type'           => 'OfferCatalog',
					'name'            => 'Stainless fabrication services',
					'itemListElement' => array_map(
						function ( $n ) {
							return array( '@type' => 'Offer', 'itemOffered' => array( '@type' => 'Service', 'name' => $n ) );
						},
						array( 'Stainless steel counters and work tables', 'Commercial kitchen hoods', 'Ducting and blowers', 'Cooking and burner stations', 'Sinks, cabinets and storage', 'Welding and custom metal works' )
					),
				),
			)
		);

		$graph = array(
			$business,
			array(
				'@type'     => 'WebSite',
				'@id'       => $site,
				'url'       => $home,
				'name'      => $s['business_name'],
				'publisher' => array( '@id' => $biz ),
				'inLanguage' => get_bloginfo( 'language' ),
			),
		);

		$page = array(
			'@type'       => 'products' === $ctx['key'] ? 'CollectionPage' : 'WebPage',
			'@id'         => $ctx['url'] . '#webpage',
			'url'         => $ctx['url'],
			'name'        => $ctx['title'] ? $ctx['title'] : wp_get_document_title(),
			'description' => $ctx['desc'],
			'isPartOf'    => array( '@id' => $site ),
			'about'       => array( '@id' => $biz ),
			'primaryImageOfPage' => array( '@type' => 'ImageObject', 'url' => $ctx['image'] ),
			'inLanguage'  => get_bloginfo( 'language' ),
			'dateModified' => get_post_modified_time( 'c', true, $ctx['post'] ),
		);

		if ( ! is_front_page() ) {
			$page['breadcrumb'] = array( '@id' => $ctx['url'] . '#breadcrumb' );
			$graph[]            = array(
				'@type'           => 'BreadcrumbList',
				'@id'             => $ctx['url'] . '#breadcrumb',
				'itemListElement' => array(
					array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $home ),
					array( '@type' => 'ListItem', 'position' => 2, 'name' => get_the_title( $ctx['post'] ), 'item' => $ctx['url'] ),
				),
			);
		}

		// Products page: describe the photo gallery so images can surface in Google Images.
		if ( 'products' === $ctx['key'] && preg_match_all( '/<img[^>]+src="([^"]+\.webp)"[^>]*alt="([^"]*)"/i', $ctx['post']->post_content, $m, PREG_SET_ORDER ) ) {
			$images = array();
			foreach ( $m as $img ) {
				if ( false !== strpos( $img[1], 'reel-' ) || false !== strpos( $img[1], 'lyfd-logo' ) || '' === $img[2] ) {
					continue;
				}
				$images[ $img[1] ] = array(
					'@type'         => 'ImageObject',
					'contentUrl'    => lyfd_seo_abs( $img[1] ),
					'name'          => $img[2],
					'creator'       => array( '@id' => $biz ),
					'copyrightNotice' => $s['business_name'],
				);
			}
			if ( $images ) {
				$page['mainEntity'] = array(
					'@type'  => 'ImageGallery',
					'name'   => 'LYFD stainless fabrication projects',
					'image'  => array_values( $images ),
				);
			}
		}
		$graph[] = $page;

		echo "<script type=\"application/ld+json\">\n" . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	},
	3
);

/* ---------------------------------------------------------------------------
 * XML sitemap (WordPress core /wp-sitemap.xml): only the real pages
 * ------------------------------------------------------------------------- */

add_filter(
	'wp_sitemaps_add_provider',
	function ( $provider, $name ) {
		return ( 'users' === $name || 'taxonomies' === $name ) ? false : $provider;
	},
	10,
	2
);

add_filter(
	'wp_sitemaps_post_types',
	function ( $types ) {
		return array_intersect_key( $types, array( 'page' => true ) ); // blog posts are not used on this site.
	}
);

add_filter(
	'wp_sitemaps_posts_query_args',
	function ( $args, $post_type ) {
		if ( 'page' === $post_type ) {
			$args['meta_query'] = array(
				'relation' => 'OR',
				array( 'key' => '_lyfd_seo_noindex', 'compare' => 'NOT EXISTS' ),
				array( 'key' => '_lyfd_seo_noindex', 'value' => '1', 'compare' => '!=' ),
			);
		}
		return $args;
	},
	10,
	2
);

add_filter(
	'wp_sitemaps_posts_entry',
	function ( $entry, $post ) {
		$entry['lastmod'] = get_post_modified_time( 'c', true, $post );
		return $entry;
	},
	10,
	2
);

/* /sitemap.xml and /sitemap_index.xml (what people and tools usually try) → core sitemap. */
add_action(
	'template_redirect',
	function () {
		$path = isset( $_SERVER['REQUEST_URI'] ) ? strtok( wp_unslash( $_SERVER['REQUEST_URI'] ), '?' ) : ''; // phpcs:ignore
		if ( in_array( $path, array( '/sitemap.xml', '/sitemap_index.xml' ), true ) ) {
			wp_safe_redirect( home_url( '/wp-sitemap.xml' ), 301 );
			exit;
		}
	},
	0
);

/* ---------------------------------------------------------------------------
 * robots.txt (virtual — served by WordPress)
 * ------------------------------------------------------------------------- */

add_filter(
	'robots_txt',
	function ( $output, $public ) {
		if ( ! $public ) {
			return $output;
		}
		$lines = array(
			'User-agent: *',
			'Disallow: /wp-admin/',
			'Allow: /wp-admin/admin-ajax.php',
			'Disallow: /wp-admin/admin-post.php',
			'Disallow: /?s=',
			'Disallow: /search/',
			'',
			'Sitemap: ' . home_url( '/wp-sitemap.xml' ),
		);
		return implode( "\n", $lines ) . "\n";
	},
	20,
	2
);
