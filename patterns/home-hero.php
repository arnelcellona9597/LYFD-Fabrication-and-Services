<?php
/**
 * Title: LYFD Hero (3D)
 * Slug: lyfd-luxe/home-hero
 * Categories: lyfd-luxe
 * Description: Full-screen hero with parallax kitchen photo, split-word headline and a floating 3D logo emblem.
 */
$products = esc_url( home_url( '/products/' ) );
$contact  = esc_url( home_url( '/#contact' ) );
?>
<!-- wp:group {"tagName":"section","align":"full","className":"lyfd-hero","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull lyfd-hero"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"lyfd-hero__bg"} -->
<figure class="wp-block-image size-full lyfd-hero__bg"><img src="<?php echo lyfd_img( 'commercial-kitchen.webp' ); ?>" alt="Stainless steel commercial kitchen fabricated by LYFD"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"lyfd-hero__inner","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-hero__inner"><!-- wp:group {"className":"lyfd-hero__copy","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-hero__copy"><!-- wp:paragraph {"className":"lyfd-eyebrow"} -->
<p class="lyfd-eyebrow">Davao City &middot; Stainless Fabrication &amp; Services</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"lyfd-hero__title lyfd-split"} -->
<h1 class="wp-block-heading lyfd-hero__title lyfd-split">Stainless steel,<br>forged into<br>brilliance.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lyfd-hero__lead"} -->
<p class="lyfd-hero__lead">Custom-fabricated kitchens, hoods, ducting, blowers and metal works &mdash; measured, welded, polished and installed with uncompromising precision.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"lyfd-hero__actions"} -->
<div class="wp-block-buttons lyfd-hero__actions"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo $products; ?>">View Our Work</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo $contact; ?>">Request a Quote</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:html -->
<div class="lyfd-scene" aria-hidden="true">
	<div class="lyfd-scene__stage">
		<span class="lyfd-ring lyfd-ring--a" data-layer="0.35"></span>
		<span class="lyfd-ring lyfd-ring--b" data-layer="0.6"></span>
		<span class="lyfd-scene__glow" data-layer="0.2"></span>
		<img class="lyfd-scene__logo" data-layer="1" src="<?php echo lyfd_img( 'lyfd-logo.webp' ); ?>" alt="" width="720" height="720" decoding="async" fetchpriority="high">
		<span class="lyfd-sparks" data-layer="1.4"></span>
	</div>
</div>
<!-- /wp:html --></div>
<!-- /wp:group -->

<!-- wp:html -->
<a class="lyfd-scroll-hint" href="#about" aria-label="Scroll to About"><span>Scroll</span><i></i></a>
<!-- /wp:html --></section>
<!-- /wp:group -->
