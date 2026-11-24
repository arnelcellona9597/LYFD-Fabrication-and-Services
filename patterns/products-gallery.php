<?php
/**
 * Title: LYFD Product Gallery (filterable)
 * Slug: lyfd-luxe/products-gallery
 * Categories: lyfd-luxe
 * Description: Filterable photo gallery with lightbox. Category = the lyfd-cat-* class (kitchens, counters, cooking, blowers = Hoods & Exhaust); is-wide / is-tall make a card span 2 columns / 2 rows.
 */
$items = array(
	array( 'kitchen-island-hood-line.webp', 'kitchens', 'Full Kitchens', 'Kitchen Island with Hood Line', 'Stainless steel kitchen island with shelf and exhaust hood line', 'is-wide is-tall' ),
	array( 'hood-grill-station.webp', 'cooking', 'Cooking Stations', 'Grill Station with Hood', 'Stainless steel grill station under an exhaust hood', '' ),
	array( 'steel-shelving-racks.webp', 'kitchens', 'Full Kitchens', 'Stainless Shelving Racks', 'Stainless steel wall shelving racks in a new kitchen', '' ),
	array( 'triple-sink-table.webp', 'counters', 'Counters & Sinks', 'Triple-Sink Work Table', 'Stainless steel triple-sink work table with shelving', 'is-wide' ),
	array( 'full-kitchen-hall.webp', 'kitchens', 'Full Kitchens', 'Complete Kitchen Fit-Out', 'Complete stainless steel commercial kitchen fit-out', 'is-wide' ),
	array( 'counter-heat-lamps.webp', 'counters', 'Counters & Sinks', 'Counter with Heat Lamps', 'Stainless steel food-service counter with heat lamps', '' ),
	array( 'three-burner-wok-range.webp', 'cooking', 'Cooking Stations', 'Three-Burner Wok Range', 'Heavy-duty stainless steel three-burner wok range', '' ),
	array( 'hood-wok-line.webp', 'blowers', 'Hoods & Exhaust', 'Hood & Wok Cooking Line', 'Stainless steel exhaust hood above a wok cooking line', 'is-wide' ),
	array( 'shelving-racks-wide.webp', 'kitchens', 'Full Kitchens', 'Stainless Storage Racks', 'Stainless steel storage racks', '' ),
	array( 'long-work-tables-sinks.webp', 'counters', 'Counters & Sinks', 'Long Work Tables & Sinks', 'Long stainless steel work tables with sinks', '' ),
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
	<button type="button" class="lyfd-filter" data-filter="blowers">Hoods &amp; Exhaust</button>
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
