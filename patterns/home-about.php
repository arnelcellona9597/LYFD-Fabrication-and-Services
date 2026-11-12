<?php
/**
 * Title: LYFD About / Company Profile
 * Slug: lyfd-luxe/home-about
 * Categories: lyfd-luxe
 * Description: Company profile with a 3D photo slider and three brand pillars.
 */
// Slider photos: [file, tag, title, alt]. Add/remove slides by duplicating a slide group in the editor.
$slides = array(
	array( 'kitchen-griddle-hood.webp',       'Full Kitchens',    'Kitchen with Griddle & Hood', 'Stainless steel kitchen with griddle station, backsplash and hood fabricated by LYFD' ),
	array( 'double-sink-counter.webp',        'Counters & Sinks', 'Double-Sink Counter',         'Stainless steel double-sink counter with cabinets' ),
	array( 'three-burner-counter.webp',       'Cooking Stations', 'Three-Burner Counter',        'Stainless steel three-burner cooking counter' ),
	array( 'wall-mounted-blower-ducting.webp','Blowers & Ducting','Wall-Mounted Blower',         'Wall-mounted green blower with black ducting' ),
	array( 'blowers-on-delivery-truck.webp',  'Blowers & Ducting','Blowers Ready for Delivery',  'Three green centrifugal blowers loaded on a delivery truck' ),
);
?>
<!-- wp:group {"tagName":"section","anchor":"about","align":"full","className":"lyfd-section lyfd-about","layout":{"type":"default"}} -->
<section id="about" class="wp-block-group alignfull lyfd-section lyfd-about"><!-- wp:group {"className":"lyfd-wrap","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-wrap"><!-- wp:columns {"verticalAlignment":"center","className":"lyfd-about__cols"} -->
<div class="wp-block-columns are-vertically-aligned-center lyfd-about__cols"><!-- wp:column {"verticalAlignment":"center","width":"58%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:58%"><!-- wp:group {"className":"lyfd-slider lyfd-scroll3d","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-slider lyfd-scroll3d"><!-- wp:group {"className":"lyfd-slider__track","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-slider__track">
<?php foreach ( $slides as $sl ) : ?>
<!-- wp:group {"className":"lyfd-slide","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-slide"><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo lyfd_img( $sl[0] ); ?>" alt="<?php echo esc_attr( $sl[3] ); ?>"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"lyfd-slide__meta","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-slide__meta"><!-- wp:paragraph {"className":"lyfd-product__tag"} -->
<p class="lyfd-product__tag"><?php echo esc_html( $sl[1] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"lyfd-product__title"} -->
<h3 class="wp-block-heading lyfd-product__title"><?php echo esc_html( $sl[2] ); ?></h3>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<?php endforeach; ?>
</div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"42%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:42%"><!-- wp:paragraph {"className":"lyfd-eyebrow lyfd-reveal"} -->
<p class="lyfd-eyebrow lyfd-reveal">Company Profile</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"lyfd-title lyfd-split"} -->
<h2 class="wp-block-heading lyfd-title lyfd-split">Craftsmanship you can see in every weld.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lyfd-lead lyfd-reveal"} -->
<p class="lyfd-lead lyfd-reveal">LYFD Stainless Fabrication and Services is a Davao City workshop specializing in custom stainless steel fabrication &mdash; from commercial kitchen hoods, ducting systems and blowers to counters, cooking stations and bespoke metal works.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"lyfd-reveal"} -->
<p class="lyfd-reveal">We work with restaurants, hotels, commissaries and homeowners to turn a floor plan into a working, easy-to-clean, built-to-last space. Every piece is measured to your layout, welded and finished by skilled fabricators, then delivered and installed on site.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"className":"lyfd-pillars lyfd-stagger"} -->
<div class="wp-block-columns lyfd-pillars lyfd-stagger"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"is-style-lyfd-card lyfd-tilt lyfd-reveal","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-lyfd-card lyfd-tilt lyfd-reveal"><!-- wp:paragraph {"className":"lyfd-num"} -->
<p class="lyfd-num">01</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"lyfd-card__title"} -->
<h3 class="wp-block-heading lyfd-card__title">Precision</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lyfd-card__text"} -->
<p class="lyfd-card__text">Made-to-measure fabrication with clean welds, tight seams and a flawless satin finish.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"is-style-lyfd-card lyfd-tilt lyfd-reveal","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-lyfd-card lyfd-tilt lyfd-reveal"><!-- wp:paragraph {"className":"lyfd-num"} -->
<p class="lyfd-num">02</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"lyfd-card__title"} -->
<h3 class="wp-block-heading lyfd-card__title">Durability</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lyfd-card__text"} -->
<p class="lyfd-card__text">Stainless steel built for the heat, moisture and daily demands of professional kitchens.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"is-style-lyfd-card lyfd-tilt lyfd-reveal","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-lyfd-card lyfd-tilt lyfd-reveal"><!-- wp:paragraph {"className":"lyfd-num"} -->
<p class="lyfd-num">03</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"lyfd-card__title"} -->
<h3 class="wp-block-heading lyfd-card__title">Full Service</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lyfd-card__text"} -->
<p class="lyfd-card__text">From design and fabrication to delivery and on-site installation &mdash; one team, start to finish.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
