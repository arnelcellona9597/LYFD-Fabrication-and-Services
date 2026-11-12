<?php
/**
 * Title: LYFD Product Gallery (filterable)
 * Slug: lyfd-luxe/products-gallery
 * Categories: lyfd-luxe
 * Description: Filterable photo gallery with lightbox. Category = the lyfd-cat-* class (kitchens, counters, cooking, blowers); is-wide / is-tall make a card span 2 columns / 2 rows.
 */
$items = array(
	array( 'commercial-kitchen.webp', 'kitchens', 'Full Kitchens', 'Complete Stainless Kitchen', 'Complete stainless steel commercial kitchen with work tables, cooking stations and exhaust hood', 'is-wide is-tall' ),
	array( 'sink-counter-backsplash.webp', 'counters', 'Counters & Sinks', 'Sink Counter with Backsplash', 'Stainless steel sink counter with backsplash and cabinet', '' ),
	array( 'double-sink-counter.webp', 'counters', 'Counters & Sinks', 'Double-Sink Counter', 'Stainless steel double-sink counter with cabinets', 'is-wide' ),
	array( 'rooftop-ducting-blower.webp', 'blowers', 'Blowers & Ducting', 'Rooftop Ducting & Blower', 'Red ducting and green blower installed on a rooftop', 'is-tall' ),
	array( 'wall-mounted-blower-ducting.webp', 'blowers', 'Blowers & Ducting', 'Wall-Mounted Blower', 'Wall-mounted green blower with black ducting', '' ),
	array( 'rooftop-exhaust-ducting.webp', 'blowers', 'Blowers & Ducting', 'Rooftop Exhaust Ducting', 'Galvanized exhaust ducting and green blower on a rooftop', 'is-tall' ),
	array( 'rooftop-blower-installation.webp', 'blowers', 'Blowers & Ducting', 'Rooftop Blower Installation', 'Green blower with ducting installed on a building roof', 'is-tall' ),
	array( 'balcony-blower-unit.webp', 'blowers', 'Blowers & Ducting', 'Balcony Blower Unit', 'Green belt-driven blower unit mounted on a balcony frame', 'is-tall' ),
	array( 'two-burner-stove.webp', 'cooking', 'Cooking Stations', 'Two-Burner Stove Station', 'Stainless steel two-burner stove station', 'is-tall' ),
	array( 'blowers-on-delivery-truck.webp', 'blowers', 'Blowers & Ducting', 'Blowers Ready for Delivery', 'Three green centrifugal blowers loaded on a delivery truck', 'is-wide' ),
	array( 'corner-cabinets-sink.webp', 'kitchens', 'Full Kitchens', 'Corner Cabinets & Sink', 'Stainless steel corner cabinets with sink and wall cabinets', '' ),
	array( 'kitchen-griddle-hood.webp', 'kitchens', 'Full Kitchens', 'Kitchen with Griddle & Hood', 'Stainless steel kitchen with griddle station, backsplash and hood', '' ),
	array( 'three-burner-counter.webp', 'cooking', 'Cooking Stations', 'Three-Burner Counter', 'Stainless steel three-burner cooking counter', '' ),
	array( 'counter-with-drawers.webp', 'counters', 'Counters & Sinks', 'Counter with Drawers', 'Stainless steel counter with drawers and cabinets', 'is-wide' ),
	array( 'green-centrifugal-blowers.webp', 'blowers', 'Blowers & Ducting', 'Centrifugal Blowers', 'Green centrifugal blowers with blue motors', '' ),
	array( 'kitchen-cabinets-window.webp', 'kitchens', 'Full Kitchens', 'Cabinet & Counter Run', 'Stainless steel cabinets and counter run beside a window', 'is-wide' ),
);
?>
<!-- wp:group {"tagName":"section","anchor":"gallery","align":"full","className":"lyfd-section lyfd-gallery-section","layout":{"type":"default"}} -->
<section id="gallery" class="wp-block-group alignfull lyfd-section lyfd-gallery-section"><!-- wp:group {"className":"lyfd-wrap","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-wrap"><!-- wp:group {"className":"lyfd-head","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-head"><!-- wp:paragraph {"className":"lyfd-eyebrow lyfd-reveal"} -->
<p class="lyfd-eyebrow lyfd-reveal">Photo Gallery</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"lyfd-title lyfd-split"} -->
<h2 class="wp-block-heading lyfd-title lyfd-split">Fabricated, finished, installed.</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:html -->
<div class="lyfd-filters lyfd-reveal" role="group" aria-label="Filter products">
	<button type="button" class="lyfd-filter is-active" data-filter="all">All</button>
	<button type="button" class="lyfd-filter" data-filter="kitchens">Full Kitchens</button>
	<button type="button" class="lyfd-filter" data-filter="counters">Counters &amp; Sinks</button>
	<button type="button" class="lyfd-filter" data-filter="cooking">Cooking Stations</button>
	<button type="button" class="lyfd-filter" data-filter="blowers">Blowers &amp; Ducting</button>
</div>
<!-- /wp:html -->

<!-- wp:group {"className":"lyfd-gallery lyfd-stagger","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-gallery lyfd-stagger">
<?php foreach ( $items as $it ) :
	$cls = 'lyfd-product lyfd-cat-' . $it[1] . ' lyfd-tilt lyfd-reveal' . ( $it[5] ? ' ' . $it[5] : '' ); ?>
<!-- wp:group {"className":"<?php echo esc_attr( $cls ); ?>","layout":{"type":"default"}} -->
<div class="wp-block-group <?php echo esc_attr( $cls ); ?>"><!-- wp:image {"lightbox":{"enabled":true},"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo lyfd_img( $it[0] ); ?>" alt="<?php echo esc_attr( $it[4] ); ?>"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"lyfd-product__meta","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-product__meta"><!-- wp:paragraph {"className":"lyfd-product__tag"} -->
<p class="lyfd-product__tag"><?php echo esc_html( $it[2] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"lyfd-product__title"} -->
<h3 class="wp-block-heading lyfd-product__title"><?php echo esc_html( $it[3] ); ?></h3>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<?php endforeach; ?>
</div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
