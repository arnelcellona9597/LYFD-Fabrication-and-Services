<?php
/**
 * Title: LYFD Products Hero
 * Slug: lyfd-luxe/products-hero
 * Categories: lyfd-luxe
 * Description: Page header for the Products page.
 */
$gallery = esc_url( '#gallery' );
?>
<!-- wp:group {"tagName":"section","align":"full","className":"lyfd-page-hero","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull lyfd-page-hero"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"lyfd-page-hero__bg"} -->
<figure class="wp-block-image size-full lyfd-page-hero__bg"><img src="<?php echo lyfd_img( 'blowers-on-delivery-truck.webp' ); ?>" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"lyfd-wrap lyfd-page-hero__inner","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-wrap lyfd-page-hero__inner"><!-- wp:paragraph {"className":"lyfd-eyebrow"} -->
<p class="lyfd-eyebrow">Products &amp; Projects</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"lyfd-page-hero__title lyfd-split"} -->
<h1 class="wp-block-heading lyfd-page-hero__title lyfd-split">The work speaks in steel.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lyfd-hero__lead"} -->
<p class="lyfd-hero__lead">A look at the kitchens, counters, cooking stations, ducting and blowers we fabricate and install. Every project is made to measure.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo $gallery; ?>">Browse the Gallery</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
