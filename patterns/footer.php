<?php
/**
 * Title: LYFD Footer
 * Slug: lyfd-luxe/footer
 * Categories: lyfd-luxe
 * Description: Premium footer: brand + social, explore, our-work shortcuts, contact details, giant wordmark, back-to-top.
 * Inserter: no
 */
$home     = esc_url( home_url( '/' ) );
$about    = esc_url( home_url( '/#about' ) );
$products = home_url( '/products/' );
$book     = esc_url( home_url( '/#book' ) );
$contact  = esc_url( home_url( '/#contact' ) );
$fb       = esc_url( LYFD_FB_PAGE_URL );
$msg      = esc_url( lyfd_messenger_url() );
?>
<!-- wp:group {"tagName":"footer","align":"full","className":"lyfd-footer","layout":{"type":"default"}} -->
<footer class="wp-block-group alignfull lyfd-footer"><!-- wp:html -->
<div class="lyfd-footer__aurora" aria-hidden="true"></div>
<!-- /wp:html -->

<!-- wp:group {"className":"lyfd-footer__inner","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-footer__inner"><!-- wp:columns {"className":"lyfd-footer__cols lyfd-stagger"} -->
<div class="wp-block-columns lyfd-footer__cols lyfd-stagger"><!-- wp:column {"className":"lyfd-footer__brand lyfd-reveal"} -->
<div class="wp-block-column lyfd-footer__brand lyfd-reveal"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"lyfd-footer__logo"} -->
<figure class="wp-block-image size-full lyfd-footer__logo"><img src="<?php echo lyfd_img( 'lyfd-logo.webp' ); ?>" alt="LYFD Stainless Fabrication and Services"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"lyfd-footer__tagline"} -->
<p class="lyfd-footer__tagline">Stainless steel, forged into brilliance.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"lyfd-footer__about"} -->
<p class="lyfd-footer__about">Custom stainless steel fabrication, kitchen hoods, ducting systems, blowers and precision metal works &mdash; crafted and installed in Davao City.</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<div class="lyfd-social">
	<a class="lyfd-social__btn" href="<?php echo $fb; ?>" target="_blank" rel="noopener noreferrer" aria-label="LYFD on Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"/></svg></a>
	<a class="lyfd-social__btn" href="<?php echo $msg; ?>" target="_blank" rel="noopener noreferrer" aria-label="Message LYFD on Messenger"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0C5.373 0 0 4.974 0 11.111c0 3.498 1.744 6.614 4.469 8.652V24l4.088-2.242c1.092.3 2.246.464 3.443.464 6.627 0 12-4.974 12-11.111C24 4.974 18.627 0 12 0zm1.191 14.963l-3.055-3.26-5.963 3.26L10.732 8l3.131 3.259L19.752 8l-6.561 6.963z"/></svg></a>
</div>
<!-- /wp:html --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"lyfd-reveal"} -->
<div class="wp-block-column lyfd-reveal"><!-- wp:paragraph {"className":"lyfd-footer__title"} -->
<p class="lyfd-footer__title">Explore</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"lyfd-footer__list"} -->
<ul class="wp-block-list lyfd-footer__list"><!-- wp:list-item -->
<li><a href="<?php echo $home; ?>">Home</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $about; ?>">About</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo esc_url( $products ); ?>">Products</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $book; ?>">Book a Visit</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $contact; ?>">Contact</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"lyfd-reveal"} -->
<div class="wp-block-column lyfd-reveal"><!-- wp:paragraph {"className":"lyfd-footer__title"} -->
<p class="lyfd-footer__title">Our Work</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"lyfd-footer__list"} -->
<ul class="wp-block-list lyfd-footer__list"><!-- wp:list-item -->
<li><a href="<?php echo esc_url( $products . '#kitchens' ); ?>">Full Kitchens</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo esc_url( $products . '#counters' ); ?>">Counters &amp; Sinks</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo esc_url( $products . '#cooking' ); ?>">Cooking Stations</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo esc_url( $products . '#blowers' ); ?>">Blowers &amp; Ducting</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo esc_url( $products . '#videos' ); ?>">Project Reels</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"lyfd-footer__reach lyfd-reveal"} -->
<div class="wp-block-column lyfd-footer__reach lyfd-reveal"><!-- wp:paragraph {"className":"lyfd-footer__title"} -->
<p class="lyfd-footer__title">Visit &amp; Contact</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<ul class="lyfd-reach">
	<li><div class="lyfd-reach__row"><span class="lyfd-ico"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg></span><span><small>Workshop</small>Davao City, Philippines</span></div></li>
	<li><a href="tel:+639330648606"><span class="lyfd-ico"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg></span><span><small>Call / text</small>0933 064 8606</span></a></li>
	<li><a href="mailto:info@lyfdfab.com"><span class="lyfd-ico"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span><span><small>Email</small>info@lyfdfab.com</span></a></li>
	<li><a href="<?php echo $book; ?>"><span class="lyfd-ico"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span><span><small>Visits</small>By appointment &mdash; book online</span></a></li>
</ul>
<!-- /wp:html --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:html -->
<div class="lyfd-footer__mark" aria-hidden="true"><span>LYFD</span></div>
<p class="lyfd-footer__markline" aria-hidden="true">Stainless Fabrication and Services</p>
<!-- /wp:html -->

<!-- wp:group {"className":"lyfd-footer__bar","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-footer__bar"><!-- wp:paragraph {"className":"lyfd-footer__legal"} -->
<p class="lyfd-footer__legal">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> LYFD Stainless Fabrication and Services. All rights reserved.</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<button type="button" class="lyfd-totop" aria-label="Back to top"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg><span>Top</span></button>
<!-- /wp:html --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></footer>
<!-- /wp:group -->
