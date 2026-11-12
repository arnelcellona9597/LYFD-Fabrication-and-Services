<?php
/**
 * Title: LYFD Contact
 * Slug: lyfd-luxe/home-contact
 * Categories: lyfd-luxe
 * Description: Contact details plus the contact form (shortcode [lyfd_contact_form]).
 */
$fb = esc_url( 'https://www.facebook.com/profile.php?id=61595745041102' );
$m  = esc_url( 'https://m.me/61595745041102' );
?>
<!-- wp:group {"tagName":"section","anchor":"contact","align":"full","className":"lyfd-section lyfd-contact","layout":{"type":"default"}} -->
<section id="contact" class="wp-block-group alignfull lyfd-section lyfd-contact"><!-- wp:group {"className":"lyfd-wrap","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-wrap"><!-- wp:columns {"className":"lyfd-contact__cols"} -->
<div class="wp-block-columns lyfd-contact__cols"><!-- wp:column {"width":"42%"} -->
<div class="wp-block-column" style="flex-basis:42%"><!-- wp:paragraph {"className":"lyfd-eyebrow lyfd-reveal"} -->
<p class="lyfd-eyebrow lyfd-reveal">Contact</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"lyfd-title lyfd-split"} -->
<h2 class="wp-block-heading lyfd-title lyfd-split">Let’s build something exceptional.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lyfd-lead lyfd-reveal"} -->
<p class="lyfd-lead lyfd-reveal">Planning a new kitchen, a hood or blower installation, or a custom metal project? Tell us what you need and we’ll get back to you with a plan and a quotation.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"lyfd-contact__list lyfd-stagger","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-contact__list lyfd-stagger"><!-- wp:group {"className":"lyfd-contact__item lyfd-reveal","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-contact__item lyfd-reveal"><!-- wp:paragraph {"className":"lyfd-contact__label"} -->
<p class="lyfd-contact__label">Email</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"lyfd-contact__value"} -->
<p class="lyfd-contact__value"><a href="mailto:info@lyfdfab.com">info@lyfdfab.com</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"lyfd-contact__item lyfd-reveal","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-contact__item lyfd-reveal"><!-- wp:paragraph {"className":"lyfd-contact__label"} -->
<p class="lyfd-contact__label">Visit</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"lyfd-contact__value"} -->
<p class="lyfd-contact__value">Davao City, Philippines</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"lyfd-contact__item lyfd-reveal","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-contact__item lyfd-reveal"><!-- wp:paragraph {"className":"lyfd-contact__label"} -->
<p class="lyfd-contact__label">Facebook</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"lyfd-contact__value"} -->
<p class="lyfd-contact__value"><a href="<?php echo $fb; ?>" target="_blank" rel="noreferrer noopener">facebook.com/LYFD</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"lyfd-contact__item lyfd-reveal","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-contact__item lyfd-reveal"><!-- wp:paragraph {"className":"lyfd-contact__label"} -->
<p class="lyfd-contact__label">Phone</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"lyfd-contact__value"} -->
<p class="lyfd-contact__value"><a href="tel:+639330648606">0933 064 8606</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:buttons {"className":"lyfd-reveal"} -->
<div class="wp-block-buttons lyfd-reveal"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo $m; ?>" target="_blank" rel="noreferrer noopener">Message on Facebook</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"58%"} -->
<div class="wp-block-column" style="flex-basis:58%"><!-- wp:group {"className":"is-style-lyfd-steel lyfd-form-panel lyfd-reveal lyfd-reveal--right","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-lyfd-steel lyfd-form-panel lyfd-reveal lyfd-reveal--right"><!-- wp:heading {"level":3,"className":"lyfd-form-panel__title"} -->
<h3 class="wp-block-heading lyfd-form-panel__title">Send us a message</h3>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[lyfd_contact_form]
<!-- /wp:shortcode --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
