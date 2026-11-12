<?php
/**
 * Title: LYFD Header
 * Slug: lyfd-luxe/header
 * Categories: lyfd-luxe
 * Description: Fixed, glass-effect site header with logo, navigation and quote button.
 * Inserter: no
 */
$home     = esc_url( home_url( '/' ) );
$about    = esc_url( home_url( '/#about' ) );
$products = esc_url( home_url( '/products/' ) );
$book     = esc_url( home_url( '/#book' ) );
$contact  = esc_url( home_url( '/#contact' ) );
?>
<!-- wp:group {"tagName":"header","align":"full","className":"lyfd-header","layout":{"type":"default"}} -->
<header class="wp-block-group alignfull lyfd-header"><!-- wp:group {"className":"lyfd-header__bar","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group lyfd-header__bar"><!-- wp:group {"className":"lyfd-brand","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group lyfd-brand"><!-- wp:image {"sizeSlug":"full","linkDestination":"custom","className":"lyfd-brand__logo"} -->
<figure class="wp-block-image size-full lyfd-brand__logo"><a href="<?php echo $home; ?>"><img src="<?php echo lyfd_img( 'lyfd-logo.webp' ); ?>" alt="LYFD Stainless Fabrication and Services"/></a></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"lyfd-brand__name"} -->
<p class="lyfd-brand__name"><strong>LYFD</strong><span>Stainless Fabrication</span></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"lyfd-header__right","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group lyfd-header__right"><!-- wp:navigation {"overlayMenu":"mobile","icon":"menu","className":"lyfd-nav","layout":{"type":"flex","justifyContent":"right"}} -->
<!-- wp:navigation-link {"label":"Home","type":"custom","url":"<?php echo $home; ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"About","type":"custom","url":"<?php echo $about; ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Products","type":"custom","url":"<?php echo $products; ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Book a Visit","type":"custom","url":"<?php echo $book; ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Contact","type":"custom","url":"<?php echo $contact; ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- /wp:navigation -->

<!-- wp:buttons {"className":"lyfd-header__cta"} -->
<div class="wp-block-buttons lyfd-header__cta"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo $book; ?>">Book a Visit</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></header>
<!-- /wp:group -->
