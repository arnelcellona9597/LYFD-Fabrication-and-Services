<?php
/**
 * Title: LYFD Footer
 * Slug: lyfd-luxe/footer
 * Categories: lyfd-luxe
 * Description: Site footer with brand statement, quick links and contact.
 * Inserter: no
 */
$home     = esc_url( home_url( '/' ) );
$about    = esc_url( home_url( '/#about' ) );
$products = esc_url( home_url( '/products/' ) );
$contact  = esc_url( home_url( '/#contact' ) );
$fb       = 'https://www.facebook.com/profile.php?id=61595745041102';
?>
<!-- wp:group {"tagName":"footer","align":"full","className":"lyfd-footer","layout":{"type":"default"}} -->
<footer class="wp-block-group alignfull lyfd-footer"><!-- wp:group {"className":"lyfd-footer__inner","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-footer__inner"><!-- wp:columns {"className":"lyfd-footer__cols"} -->
<div class="wp-block-columns lyfd-footer__cols"><!-- wp:column {"width":"44%"} -->
<div class="wp-block-column" style="flex-basis:44%"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"lyfd-footer__logo"} -->
<figure class="wp-block-image size-full lyfd-footer__logo"><img src="<?php echo lyfd_img( 'lyfd-logo.webp' ); ?>" alt="LYFD Stainless Fabrication and Services"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"lyfd-footer__about"} -->
<p class="lyfd-footer__about">Custom stainless steel fabrication, kitchen hoods, ducting systems and precision metal works &mdash; crafted and installed in Davao City.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"20%"} -->
<div class="wp-block-column" style="flex-basis:20%"><!-- wp:paragraph {"className":"lyfd-footer__title"} -->
<p class="lyfd-footer__title">Explore</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"lyfd-footer__links"} -->
<p class="lyfd-footer__links"><a href="<?php echo $home; ?>">Home</a><br><a href="<?php echo $about; ?>">About</a><br><a href="<?php echo $products; ?>">Products</a><br><a href="<?php echo $contact; ?>">Contact</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"36%"} -->
<div class="wp-block-column" style="flex-basis:36%"><!-- wp:paragraph {"className":"lyfd-footer__title"} -->
<p class="lyfd-footer__title">Visit &amp; Connect</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"lyfd-footer__links"} -->
<p class="lyfd-footer__links">Davao City, Philippines<br><a href="tel:+639330648606">0933 064 8606</a><br><a href="mailto:info@lyfdfab.com">info@lyfdfab.com</a><br><a href="<?php echo esc_url( $fb ); ?>" target="_blank" rel="noreferrer noopener">Facebook &mdash; LYFD</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:separator {"className":"lyfd-footer__rule"} -->
<hr class="wp-block-separator has-alpha-channel-opacity lyfd-footer__rule"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"className":"lyfd-footer__legal"} -->
<p class="lyfd-footer__legal">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> LYFD Stainless Fabrication and Services. All rights reserved.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></footer>
<!-- /wp:group -->
