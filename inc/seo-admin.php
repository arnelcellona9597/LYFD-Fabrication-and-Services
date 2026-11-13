<?php
/**
 * SEO admin UI: per-page "SEO" box (with Google preview) and Settings → LYFD SEO.
 *
 * @package lyfd-luxe
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 * Per-page SEO box
 * ------------------------------------------------------------------------- */

add_action(
	'add_meta_boxes_page',
	function () {
		if ( lyfd_seo_plugin_active() ) {
			return;
		}
		add_meta_box( 'lyfd_seo', 'SEO — Google & social sharing', 'lyfd_seo_metabox', 'page', 'normal', 'high' );
	}
);

function lyfd_seo_metabox( $post ) {
	wp_nonce_field( 'lyfd_seo_save', 'lyfd_seo_nonce' );
	$key   = lyfd_seo_page_key( $post );
	$def   = $key ? lyfd_seo_page_defaults()[ $key ] : array( 'title' => '', 'desc' => '' );
	$title = get_post_meta( $post->ID, '_lyfd_seo_title', true );
	$desc  = get_post_meta( $post->ID, '_lyfd_seo_desc', true );
	$image = get_post_meta( $post->ID, '_lyfd_seo_image', true );
	$noidx = get_post_meta( $post->ID, '_lyfd_seo_noindex', true );
	$url   = (int) get_option( 'page_on_front' ) === $post->ID ? home_url( '/' ) : get_permalink( $post );
	?>
	<style>
		.lyfd-seo p{margin:0 0 14px}.lyfd-seo label{display:block;font-weight:600;margin-bottom:4px}
		.lyfd-seo input[type=text],.lyfd-seo input[type=url],.lyfd-seo textarea{width:100%}
		.lyfd-seo .count{float:right;font-weight:400;color:#666}.lyfd-seo .count.bad{color:#b32d2e}
		.lyfd-serp{max-width:600px;padding:14px 16px;border:1px solid #dcdcde;border-radius:8px;background:#fff;font-family:arial,sans-serif;margin-bottom:16px}
		.lyfd-serp .u{font-size:12px;color:#202124}.lyfd-serp .t{font-size:20px;line-height:1.3;color:#1a0dab;margin:4px 0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
		.lyfd-serp .d{font-size:14px;line-height:1.58;color:#4d5156}
	</style>
	<div class="lyfd-seo">
		<p class="description">How this page appears in Google and when shared on Facebook/Messenger. Leave a field empty to use the built-in default shown as the placeholder.</p>
		<div class="lyfd-serp" aria-hidden="true">
			<div class="u"><?php echo esc_html( preg_replace( '#^https?://#', '', untrailingslashit( $url ) ) ); ?></div>
			<div class="t" id="lyfd-serp-t"></div>
			<div class="d" id="lyfd-serp-d"></div>
		</div>
		<p><label for="lyfd-seo-title">SEO title <span class="count" id="lyfd-seo-title-c"></span></label>
			<input type="text" id="lyfd-seo-title" name="lyfd_seo_title" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php echo esc_attr( $def['title'] ? $def['title'] : get_the_title( $post ) ); ?>" maxlength="120"></p>
		<p><label for="lyfd-seo-desc">Meta description <span class="count" id="lyfd-seo-desc-c"></span></label>
			<textarea id="lyfd-seo-desc" name="lyfd_seo_desc" rows="3" maxlength="300" placeholder="<?php echo esc_attr( $def['desc'] ); ?>"><?php echo esc_textarea( $desc ); ?></textarea></p>
		<p><label for="lyfd-seo-image">Share image URL (optional, 1200×630)</label>
			<input type="url" id="lyfd-seo-image" name="lyfd_seo_image" value="<?php echo esc_attr( $image ); ?>" placeholder="<?php echo esc_attr( get_theme_file_uri( 'assets/images/og-image.jpg' ) ); ?>"></p>
		<p><label><input type="checkbox" name="lyfd_seo_noindex" value="1" <?php checked( '1', $noidx ); ?>> Hide this page from Google (noindex) and from the sitemap</label></p>
	</div>
	<script>
	( function () {
		function bind( id, ideal, max, out ) {
			var f = document.getElementById( id ), c = document.getElementById( id + '-c' ), o = document.getElementById( out );
			function upd() {
				var v = f.value || f.placeholder, n = v.length;
				c.textContent = n + ' / ' + ideal + ' characters';
				c.className = 'count' + ( n > max ? ' bad' : '' );
				o.textContent = n > max ? v.slice( 0, max - 1 ) + '…' : v;
			}
			f.addEventListener( 'input', upd ); upd();
		}
		bind( 'lyfd-seo-title', '50–60', 65, 'lyfd-serp-t' );
		bind( 'lyfd-seo-desc', '120–160', 160, 'lyfd-serp-d' );
	} )();
	</script>
	<?php
}

add_action(
	'save_post_page',
	function ( $post_id ) {
		if ( ! isset( $_POST['lyfd_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lyfd_seo_nonce'] ) ), 'lyfd_seo_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
		$fields = array(
			'_lyfd_seo_title' => sanitize_text_field( wp_unslash( isset( $_POST['lyfd_seo_title'] ) ? $_POST['lyfd_seo_title'] : '' ) ),
			'_lyfd_seo_desc'  => sanitize_textarea_field( wp_unslash( isset( $_POST['lyfd_seo_desc'] ) ? $_POST['lyfd_seo_desc'] : '' ) ),
			'_lyfd_seo_image' => esc_url_raw( wp_unslash( isset( $_POST['lyfd_seo_image'] ) ? $_POST['lyfd_seo_image'] : '' ) ),
			'_lyfd_seo_noindex' => empty( $_POST['lyfd_seo_noindex'] ) ? '' : '1',
		);
		foreach ( $fields as $k => $v ) {
			if ( '' === $v ) {
				delete_post_meta( $post_id, $k );
			} else {
				update_post_meta( $post_id, $k, $v );
			}
		}
	}
);

/* ---------------------------------------------------------------------------
 * Settings → LYFD SEO
 * ------------------------------------------------------------------------- */

add_action(
	'admin_menu',
	function () {
		add_options_page( 'LYFD SEO', 'LYFD SEO', 'manage_options', 'lyfd-seo', 'lyfd_seo_settings_page' );
	}
);

add_action(
	'admin_init',
	function () {
		register_setting(
			'lyfd_seo',
			'lyfd_seo_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => function ( $in ) {
					$d   = lyfd_seo_defaults();
					$in  = is_array( $in ) ? $in : array();
					$out = array();
					foreach ( $d as $k => $v ) {
						$val = isset( $in[ $k ] ) ? trim( (string) $in[ $k ] ) : $v;
						switch ( $k ) {
							case 'email':
								$val = is_email( $val ) ? sanitize_email( $val ) : $d['email'];
								break;
							case 'facebook':
							case 'share_image':
								$val = esc_url_raw( $val );
								break;
							case 'lat':
							case 'lng':
								$val = is_numeric( $val ) ? (string) (float) $val : '';
								break;
							case 'google_verify':
							case 'bing_verify':
								// accept either the bare code or the whole <meta ...> tag pasted from Google/Bing.
								if ( preg_match( '/content=["\']([^"\']+)["\']/', $val, $m ) ) {
									$val = $m[1];
								}
								$val = preg_replace( '/[^A-Za-z0-9_\-]/', '', $val );
								break;
							case 'business_type':
								$val = preg_replace( '/[^A-Za-z]/', '', $val );
								$val = $val ? $val : $d['business_type'];
								break;
							default:
								$val = sanitize_text_field( $val );
						}
						$out[ $k ] = $val;
					}
					return $out;
				},
			)
		);
	}
);

function lyfd_seo_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$s   = lyfd_seo_settings();
	$opt = 'lyfd_seo_settings';
	$row = function ( $key, $label, $help = '', $type = 'text' ) use ( $s, $opt ) {
		printf(
			'<tr><th><label for="lseo-%1$s">%2$s</label></th><td><input type="%5$s" class="regular-text" id="lseo-%1$s" name="%3$s[%1$s]" value="%4$s">%6$s</td></tr>',
			esc_attr( $key ),
			esc_html( $label ),
			esc_attr( $opt ),
			esc_attr( $s[ $key ] ),
			esc_attr( $type ),
			$help ? '<p class="description">' . wp_kses_post( $help ) . '</p>' : ''
		);
	};
	$public = (bool) get_option( 'blog_public' );
	?>
	<div class="wrap">
		<h1>LYFD SEO</h1>
		<?php if ( isset( $_GET['settings-updated'] ) ) : // phpcs:ignore ?>
			<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>
		<?php endif; ?>
		<?php if ( ! $public ) : ?>
			<div class="notice notice-warning"><p><strong>Search engines are blocked.</strong> Untick “Discourage search engines” in Settings → Reading when the site goes live.</p></div>
		<?php endif; ?>
		<p>
			XML sitemap: <a href="<?php echo esc_url( home_url( '/wp-sitemap.xml' ) ); ?>" target="_blank"><?php echo esc_html( home_url( '/wp-sitemap.xml' ) ); ?></a>
			&nbsp;·&nbsp; robots.txt: <a href="<?php echo esc_url( home_url( '/robots.txt' ) ); ?>" target="_blank"><?php echo esc_html( home_url( '/robots.txt' ) ); ?></a>
			&nbsp;·&nbsp; Test structured data: <a href="https://search.google.com/test/rich-results?url=<?php echo rawurlencode( home_url( '/' ) ); ?>" target="_blank" rel="noopener">Google Rich Results Test</a>
		</p>
		<p class="description">Page titles and descriptions are edited in the <strong>SEO</strong> box under each page (Pages → Home / Products).</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'lyfd_seo' ); ?>
			<h2>Business details (Google “local business” information)</h2>
			<table class="form-table" role="presentation">
				<?php
				$row( 'business_name', 'Business name' );
				$row( 'phone', 'Phone', 'International format, e.g. +63 933 064 8606.' );
				$row( 'email', 'Email', '', 'email' );
				$row( 'street', 'Street address', 'Optional. Add it if the workshop accepts walk-in visits.' );
				$row( 'city', 'City' );
				$row( 'region', 'Province / region' );
				$row( 'postal', 'Postal code' );
				$row( 'country', 'Country code', '2 letters, e.g. PH.' );
				$row( 'lat', 'Latitude', 'Optional workshop GPS — right-click the spot in Google Maps to copy it.' );
				$row( 'lng', 'Longitude' );
				$row( 'business_type', 'Schema business type', 'e.g. HomeAndConstructionBusiness, GeneralContractor (schema.org type, no spaces).' );
				$row( 'facebook', 'Facebook page URL', '', 'url' );
				?>
			</table>
			<h2>Search Console &amp; sharing</h2>
			<table class="form-table" role="presentation">
				<?php
				$row( 'google_verify', 'Google Search Console code', 'From Search Console → Add property → URL prefix → HTML tag. Paste the code or the whole meta tag.' );
				$row( 'bing_verify', 'Bing Webmaster code', 'Optional.' );
				$row( 'share_image', 'Default share image URL', 'Shown when a page is shared on Facebook/Messenger. Leave empty to use the built-in 1200×630 image.', 'url' );
				$row( 'title_sep', 'Title separator', 'Character between page name and site name, e.g. | or –' );
				?>
			</table>
			<?php submit_button(); ?>
		</form>
		<h2>After going live</h2>
		<ol>
			<li>Settings → Reading: make sure “Discourage search engines” is <strong>unticked</strong>.</li>
			<li>Add the site to <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Google Search Console</a>, paste the verification code above, then submit <code>wp-sitemap.xml</code>.</li>
			<li>Create / claim a <a href="https://www.google.com/business/" target="_blank" rel="noopener">Google Business Profile</a> for LYFD with the same name, phone and address as above.</li>
		</ol>
	</div>
	<?php
}
