<?php
/**
 * Title: LYFD Products CTA
 * Slug: lyfd-luxe/products-cta
 * Categories: lyfd-luxe
 * Description: Closing call-to-action band.
 */
$contact = esc_url( home_url( '/#contact' ) );
$m       = esc_url( 'https://m.me/61595745041102' );
?>
<!-- wp:group {"tagName":"section","align":"full","className":"lyfd-section lyfd-cta-band","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull lyfd-section lyfd-cta-band"><!-- wp:group {"className":"lyfd-wrap lyfd-cta-band__inner lyfd-reveal","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-wrap lyfd-cta-band__inner lyfd-reveal"><!-- wp:paragraph {"className":"lyfd-eyebrow"} -->
<p class="lyfd-eyebrow">Your Project</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"lyfd-title"} -->
<h2 class="wp-block-heading lyfd-title">Have a layout in mind? Let’s fabricate it.</h2>
<!-- /wp:heading -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo $m; ?>" target="_blank" rel="noreferrer noopener">Message on Facebook</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo $contact; ?>">Contact Details</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
