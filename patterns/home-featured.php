<?php
/**
 * Title: LYFD Featured Work Teaser
 * Slug: lyfd-luxe/home-featured
 * Categories: lyfd-luxe
 * Description: Three photo cards that lead visitors to the Products page.
 */
$products = esc_url( home_url( '/products/' ) );
?>
<!-- wp:group {"tagName":"section","align":"full","className":"lyfd-section lyfd-featured","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull lyfd-section lyfd-featured"><!-- wp:group {"className":"lyfd-wrap","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-wrap"><!-- wp:group {"className":"lyfd-head lyfd-head--split","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-head lyfd-head--split"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"lyfd-eyebrow lyfd-reveal"} -->
<p class="lyfd-eyebrow lyfd-reveal">Selected Work</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"lyfd-title lyfd-split"} -->
<h2 class="wp-block-heading lyfd-title lyfd-split">Built to be seen. Built to last.</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:buttons {"className":"lyfd-reveal"} -->
<div class="wp-block-buttons lyfd-reveal"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo $products; ?>">View All Products</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"lyfd-feature-row lyfd-stagger","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-feature-row lyfd-stagger"><!-- wp:group {"className":"lyfd-product lyfd-tilt lyfd-reveal","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-product lyfd-tilt lyfd-reveal"><!-- wp:image {"sizeSlug":"large","linkDestination":"custom"} -->
<figure class="wp-block-image size-large"><a href="<?php echo $products; ?>"><img src="<?php echo lyfd_img( 'triple-sink-table.webp' ); ?>" alt="Stainless steel triple-sink work table"/></a></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"lyfd-product__meta","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-product__meta"><!-- wp:paragraph {"className":"lyfd-product__tag"} -->
<p class="lyfd-product__tag">Counters &amp; Sinks</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"lyfd-product__title"} -->
<h3 class="wp-block-heading lyfd-product__title">Triple-Sink Work Table</h3>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"lyfd-product lyfd-tilt lyfd-reveal","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-product lyfd-tilt lyfd-reveal"><!-- wp:image {"sizeSlug":"large","linkDestination":"custom"} -->
<figure class="wp-block-image size-large"><a href="<?php echo $products; ?>"><img src="<?php echo lyfd_img( 'three-burner-wok-range.webp' ); ?>" alt="Heavy-duty stainless steel three-burner wok range"/></a></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"lyfd-product__meta","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-product__meta"><!-- wp:paragraph {"className":"lyfd-product__tag"} -->
<p class="lyfd-product__tag">Cooking Stations</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"lyfd-product__title"} -->
<h3 class="wp-block-heading lyfd-product__title">Three-Burner Wok Range</h3>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"lyfd-product lyfd-tilt lyfd-reveal","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-product lyfd-tilt lyfd-reveal"><!-- wp:image {"sizeSlug":"large","linkDestination":"custom"} -->
<figure class="wp-block-image size-large"><a href="<?php echo $products; ?>"><img src="<?php echo lyfd_img( 'hood-wok-line.webp' ); ?>" alt="Stainless steel exhaust hood above a wok cooking line"/></a></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"lyfd-product__meta","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-product__meta"><!-- wp:paragraph {"className":"lyfd-product__tag"} -->
<p class="lyfd-product__tag">Hoods &amp; Exhaust</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"lyfd-product__title"} -->
<h3 class="wp-block-heading lyfd-product__title">Hood &amp; Wok Cooking Line</h3>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
