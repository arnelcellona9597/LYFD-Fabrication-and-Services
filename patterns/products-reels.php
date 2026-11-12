<?php
/**
 * Title: LYFD Facebook Reels
 * Slug: lyfd-luxe/products-reels
 * Categories: lyfd-luxe
 * Description: Click-to-play Facebook reels. The Facebook player only loads after a click (faster page, no tracking until then). To add a reel: copy a card, then change the reel URL in the HTML block and the poster image.
 */
$reels = array(
	array( '1416817723946368', 'reel-1.webp', 'Reel 01' ),
	array( '1898022618235899', 'reel-2.webp', 'Reel 02' ),
	array( '1431586745805838', 'reel-3.webp', 'Reel 03' ),
	array( '1147742731115859', 'reel-4.webp', 'Reel 04' ),
	array( '4194695164154407', 'reel-4.webp', 'Reel 05' ),
);
?>
<!-- wp:group {"tagName":"section","anchor":"videos","align":"full","className":"lyfd-section lyfd-videos","layout":{"type":"default"}} -->
<section id="videos" class="wp-block-group alignfull lyfd-section lyfd-videos"><!-- wp:group {"className":"lyfd-wrap","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-wrap"><!-- wp:group {"className":"lyfd-head lyfd-head--center","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-head lyfd-head--center"><!-- wp:paragraph {"className":"lyfd-eyebrow lyfd-reveal"} -->
<p class="lyfd-eyebrow lyfd-reveal">In Motion</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"lyfd-title lyfd-split"} -->
<h2 class="wp-block-heading lyfd-title lyfd-split">See the craft up close.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"lyfd-lead lyfd-reveal"} -->
<p class="lyfd-lead lyfd-reveal">Tap a reel to play it right here, straight from our Facebook page.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"lyfd-reel-grid lyfd-stagger","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-reel-grid lyfd-stagger">
<?php foreach ( $reels as $r ) : $url = 'https://www.facebook.com/reel/' . $r[0] . '/'; ?>
<!-- wp:group {"className":"lyfd-reel-card lyfd-tilt lyfd-reveal","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-reel-card lyfd-tilt lyfd-reveal"><!-- wp:html -->
<div class="lyfd-reel" data-src="<?php echo esc_url( $url ); ?>">
	<button type="button" class="lyfd-reel__btn" aria-label="Play <?php echo esc_attr( $r[2] ); ?>"><img src="<?php echo lyfd_img( $r[1] ); ?>" alt="" width="480" height="853" loading="lazy" decoding="async"><span class="lyfd-reel__play" aria-hidden="true"></span></button>
</div>
<!-- /wp:html -->

<!-- wp:group {"className":"lyfd-reel-card__meta","layout":{"type":"default"}} -->
<div class="wp-block-group lyfd-reel-card__meta"><!-- wp:paragraph {"className":"lyfd-product__tag"} -->
<p class="lyfd-product__tag"><?php echo esc_html( $r[2] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"lyfd-reel-card__link"} -->
<p class="lyfd-reel-card__link"><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noreferrer noopener">Watch on Facebook</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<?php endforeach; ?>
</div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
