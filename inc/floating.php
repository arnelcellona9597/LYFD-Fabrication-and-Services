<?php
/**
 * Floating Messenger button (every page) and the 24-hour welcome popup (homepage).
 *
 * @package lyfd-luxe
 */

defined( 'ABSPATH' ) || exit;

define( 'LYFD_FB_PAGE_URL', 'https://www.facebook.com/profile.php?id=61595745041102' );
define( 'LYFD_FB_PAGE_ID', '61595745041102' );

/** Messenger chat link; falls back to the Facebook page when no page ID is known. */
function lyfd_messenger_url() {
	if ( defined( 'LYFD_MESSENGER_URL' ) && LYFD_MESSENGER_URL ) {
		return LYFD_MESSENGER_URL;
	}
	return LYFD_FB_PAGE_ID ? 'https://m.me/' . LYFD_FB_PAGE_ID : LYFD_FB_PAGE_URL;
}

add_action(
	'wp_footer',
	function () {
		?>
		<a class="lyfd-fab" href="<?php echo esc_url( lyfd_messenger_url() ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Chat with LYFD on Facebook Messenger (opens in a new tab)">
			<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 0C5.373 0 0 4.974 0 11.111c0 3.498 1.744 6.614 4.469 8.652V24l4.088-2.242c1.092.3 2.246.464 3.443.464 6.627 0 12-4.974 12-11.111C24 4.974 18.627 0 12 0zm1.191 14.963l-3.055-3.26-5.963 3.26L10.732 8l3.131 3.259L19.752 8l-6.561 6.963z"/></svg>
			<span class="lyfd-fab__label">Chat with us</span>
		</a>
		<?php

		?>
		<template id="lyfd-menu-extra">
			<div class="lyfd-menu-brand">
				<img src="<?php echo lyfd_img( 'lyfd-logo.webp' ); ?>" alt="" width="52" height="52">
				<span><strong>LYFD</strong><small>Stainless Fabrication</small></span>
			</div>
			<div class="lyfd-menu-extra">
				<a class="lyfd-menu-extra__book" href="<?php echo esc_url( home_url( '/#book' ) ); ?>"><span>Book a Visit</span></a>
				<p class="lyfd-menu-extra__label">Get in touch</p>
				<a class="lyfd-menu-extra__line" href="tel:+639330648606">0933 064 8606</a>
				<a class="lyfd-menu-extra__line" href="mailto:info@lyfdfab.com">info@lyfdfab.com</a>
				<div class="lyfd-menu-extra__row">
					<a href="<?php echo esc_url( lyfd_messenger_url() ); ?>" target="_blank" rel="noopener noreferrer">Messenger</a>
					<a href="<?php echo esc_url( LYFD_FB_PAGE_URL ); ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
				</div>
			</div>
		</template>
		<?php

		if ( ! is_front_page() ) {
			return;
		}
		$products = home_url( '/products/' );
		?>
		<div class="lyfd-promo" id="lyfd-promo" role="dialog" aria-modal="true" aria-labelledby="lyfd-promo-title" aria-hidden="true">
			<div class="lyfd-promo__scrim" data-promo-close></div>
			<div class="lyfd-promo__card">
				<button type="button" class="lyfd-promo__close" data-promo-close aria-label="Close">&times;</button>
				<div class="lyfd-promo__media"><img src="<?php echo lyfd_img( 'green-centrifugal-blowers.webp' ); ?>" alt="" width="1282" height="920" loading="lazy" decoding="async"></div>
				<div class="lyfd-promo__body">
					<p class="lyfd-eyebrow">Our Products</p>
					<h2 class="lyfd-promo__title" id="lyfd-promo-title">See what we fabricate</h2>
					<p class="lyfd-promo__text">Stainless kitchens, counters, cooking stations, ducting and custom blowers &mdash; browse our photo gallery and watch our project reels.</p>
					<div class="lyfd-promo__actions">
						<a class="lyfd-btn" href="<?php echo esc_url( $products ); ?>"><span>View Products</span></a>
						<button type="button" class="lyfd-link" data-promo-close>Maybe later</button>
					</div>
				</div>
			</div>
		</div>
		<?php
	},
	20
);
