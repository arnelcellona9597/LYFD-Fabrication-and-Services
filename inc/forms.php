<?php
/**
 * Contact form: shortcode, spam protection, handler, "Inquiries" inbox in wp-admin.
 *
 * No plugin needed. Anti-spam = hidden honeypot + signed timestamp + per-IP rate limit.
 * (A signed timestamp is used instead of a nonce so full-page caching never breaks the form.)
 *
 * @package lyfd-luxe
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 * Shared helpers (also used by booking.php)
 * ------------------------------------------------------------------------- */

function lyfd_form_token() {
	$ts = (string) time();
	return $ts . '.' . hash_hmac( 'sha256', $ts, wp_salt( 'nonce' ) );
}

function lyfd_form_token_ok( $token ) {
	$parts = explode( '.', (string) $token );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
		return false;
	}
	if ( ! hash_equals( hash_hmac( 'sha256', $parts[0], wp_salt( 'nonce' ) ), (string) $parts[1] ) ) {
		return false;
	}
	$age = time() - (int) $parts[0];
	return $age >= 3 && $age <= 7 * DAY_IN_SECONDS; // humans need >3s; page may be cached for days.
}

function lyfd_rate_limited( $bucket, $max = 5, $window = 600 ) {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0';
	$key = 'lyfd_rl_' . md5( $bucket . '|' . $ip );
	$n   = (int) get_transient( $key );
	if ( $n >= $max ) {
		return true;
	}
	set_transient( $key, $n + 1, $window );
	return false;
}

function lyfd_post( $key ) {
	return isset( $_POST[ $key ] ) && is_scalar( $_POST[ $key ] ) ? wp_unslash( (string) $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
}

function lyfd_wants_json() {
	$accept = isset( $_SERVER['HTTP_ACCEPT'] ) ? (string) $_SERVER['HTTP_ACCEPT'] : '';
	return false !== strpos( $accept, 'application/json' );
}

/**
 * Answer an admin-post request: JSON for the JS-enhanced form, redirect otherwise.
 */
function lyfd_form_respond( $ok, $message, $flag, $anchor, $extra = array() ) {
	if ( lyfd_wants_json() ) {
		wp_send_json( array_merge( array( 'ok' => (bool) $ok, 'message' => $message ), $extra ), $ok ? 200 : 422 );
	}
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back = remove_query_arg( array( 'lyfd', 'lyfd_msg' ), $back );
	$back = add_query_arg( array( 'lyfd' => $ok ? $flag . '-ok' : $flag . '-err', 'lyfd_msg' => rawurlencode( $message ) ), $back );
	wp_safe_redirect( $back . '#' . $anchor );
	exit;
}

function lyfd_count_links( $text ) {
	return preg_match_all( '#https?://|www\.#i', $text );
}

/* ---------------------------------------------------------------------------
 * Inquiries inbox (custom post type)
 * ------------------------------------------------------------------------- */

add_action(
	'init',
	function () {
		register_post_type(
			'lyfd_inquiry',
			array(
				'labels'              => array(
					'name'          => 'Inquiries',
					'singular_name' => 'Inquiry',
					'menu_name'     => 'Inquiries',
					'all_items'     => 'All Inquiries',
					'search_items'  => 'Search Inquiries',
					'not_found'     => 'No inquiries yet.',
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_icon'           => 'dashicons-email-alt',
				'menu_position'       => 26,
				'supports'            => array( 'title' ),
				'capability_type'     => 'post',
				'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'        => true,
				'exclude_from_search' => true,
			)
		);
	}
);

add_filter(
	'manage_lyfd_inquiry_posts_columns',
	function () {
		return array(
			'cb'      => '<input type="checkbox" />',
			'title'   => 'From',
			'email'   => 'Email',
			'phone'   => 'Phone',
			'topic'   => 'Interested in',
			'date'    => 'Received',
		);
	}
);

add_action(
	'manage_lyfd_inquiry_posts_custom_column',
	function ( $col, $id ) {
		$map = array( 'email' => '_lyfd_email', 'phone' => '_lyfd_phone', 'topic' => '_lyfd_topic' );
		if ( isset( $map[ $col ] ) ) {
			echo esc_html( get_post_meta( $id, $map[ $col ], true ) );
		}
	},
	10,
	2
);

add_action(
	'add_meta_boxes_lyfd_inquiry',
	function () {
		add_meta_box(
			'lyfd_inquiry_details',
			'Message',
			function ( $post ) {
				$rows = array(
					'Name'          => $post->post_title,
					'Email'         => get_post_meta( $post->ID, '_lyfd_email', true ),
					'Phone'         => get_post_meta( $post->ID, '_lyfd_phone', true ),
					'Interested in' => get_post_meta( $post->ID, '_lyfd_topic', true ),
				);
				echo '<table class="form-table"><tbody>';
				foreach ( $rows as $label => $val ) {
					printf( '<tr><th>%s</th><td>%s</td></tr>', esc_html( $label ), esc_html( $val ) );
				}
				printf( '<tr><th>Message</th><td>%s</td></tr>', nl2br( esc_html( $post->post_content ) ) );
				echo '</tbody></table>';
				$email = get_post_meta( $post->ID, '_lyfd_email', true );
				if ( $email ) {
					printf( '<p><a class="button button-primary" href="mailto:%s">Reply by email</a></p>', esc_attr( $email ) );
				}
			},
			null,
			'normal',
			'high'
		);
	}
);

/* ---------------------------------------------------------------------------
 * Shortcode
 * ------------------------------------------------------------------------- */

function lyfd_contact_topics() {
	return array(
		'Kitchen & counters',
		'Kitchen hood',
		'Ducting & blowers',
		'Cooking stations',
		'Custom metal works',
		'Something else',
	);
}

function lyfd_flash_notice( $prefix ) {
	$flag = isset( $_GET['lyfd'] ) ? sanitize_key( wp_unslash( $_GET['lyfd'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	if ( 0 !== strpos( $flag, $prefix . '-' ) ) {
		return '';
	}
	$ok  = '-ok' === substr( $flag, -3 );
	$msg = isset( $_GET['lyfd_msg'] ) ? sanitize_text_field( rawurldecode( wp_unslash( $_GET['lyfd_msg'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	return sprintf( '<p class="lyfd-form__status %s" role="status">%s</p>', $ok ? 'is-ok' : 'is-error', esc_html( $msg ) );
}

add_shortcode(
	'lyfd_contact_form',
	function () {
		ob_start();
		?>
		<form class="lyfd-form lyfd-form--contact" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
			<input type="hidden" name="action" value="lyfd_contact">
			<input type="hidden" name="lyfd_t" value="<?php echo esc_attr( lyfd_form_token() ); ?>">
			<p class="lyfd-hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>

			<div class="lyfd-form__row">
				<p class="lyfd-field"><label for="lyfd-c-name">Your name <span aria-hidden="true">*</span></label>
					<input id="lyfd-c-name" name="name" type="text" autocomplete="name" required maxlength="120"></p>
				<p class="lyfd-field"><label for="lyfd-c-email">Email <span aria-hidden="true">*</span></label>
					<input id="lyfd-c-email" name="email" type="email" autocomplete="email" required maxlength="160"></p>
			</div>
			<div class="lyfd-form__row">
				<p class="lyfd-field"><label for="lyfd-c-phone">Phone / Viber / WhatsApp</label>
					<input id="lyfd-c-phone" name="phone" type="tel" autocomplete="tel" maxlength="40"></p>
				<p class="lyfd-field"><label for="lyfd-c-topic">I&rsquo;m interested in</label>
					<select id="lyfd-c-topic" name="topic">
						<?php foreach ( lyfd_contact_topics() as $t ) : ?>
							<option value="<?php echo esc_attr( $t ); ?>"><?php echo esc_html( $t ); ?></option>
						<?php endforeach; ?>
					</select></p>
			</div>
			<p class="lyfd-field"><label for="lyfd-c-msg">Tell us about your project <span aria-hidden="true">*</span></label>
				<textarea id="lyfd-c-msg" name="message" rows="5" required minlength="10" maxlength="3000"></textarea></p>
			<p class="lyfd-form__actions"><button type="submit" class="lyfd-btn"><span>Send Message</span></button></p>
			<?php echo lyfd_flash_notice( 'contact' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p class="lyfd-form__status" role="status" aria-live="polite" hidden></p>
		</form>
		<?php
		return ob_get_clean();
	}
);

/* ---------------------------------------------------------------------------
 * Handler
 * ------------------------------------------------------------------------- */

function lyfd_handle_contact() {
	// Bots: honeypot filled or token invalid → pretend success, do nothing.
	if ( '' !== trim( lyfd_post( 'website' ) ) || ! lyfd_form_token_ok( lyfd_post( 'lyfd_t' ) ) ) {
		lyfd_form_respond( true, 'Thank you! Your message has been sent.', 'contact', 'contact' );
	}
	if ( lyfd_rate_limited( 'contact' ) ) {
		lyfd_form_respond( false, 'Too many messages in a short time. Please wait a few minutes and try again.', 'contact', 'contact' );
	}

	$name    = sanitize_text_field( lyfd_post( 'name' ) );
	$email   = sanitize_email( lyfd_post( 'email' ) );
	$phone   = sanitize_text_field( lyfd_post( 'phone' ) );
	$topic   = sanitize_text_field( lyfd_post( 'topic' ) );
	$message = sanitize_textarea_field( lyfd_post( 'message' ) );

	if ( ! in_array( $topic, lyfd_contact_topics(), true ) ) {
		$topic = 'Something else';
	}
	if ( '' === $name || ! is_email( $email ) || strlen( $message ) < 10 ) {
		lyfd_form_respond( false, 'Please enter your name, a valid email and a short message.', 'contact', 'contact' );
	}
	if ( lyfd_count_links( $message ) > 2 ) {
		lyfd_form_respond( false, 'Please remove links from your message.', 'contact', 'contact' );
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'lyfd_inquiry',
			'post_status'  => 'publish',
			'post_title'   => $name,
			'post_content' => $message,
			'meta_input'   => array(
				'_lyfd_email' => $email,
				'_lyfd_phone' => $phone,
				'_lyfd_topic' => $topic,
			),
		)
	);

	$body = "New website inquiry\n\n"
		. "Name:  $name\nEmail: $email\nPhone: " . ( $phone ? $phone : '-' ) . "\nTopic: $topic\n\n"
		. "Message:\n$message\n\n--\nSent from " . home_url( '/' ) . "\n";

	$sent = lyfd_send_mail( lyfd_mail_to(), "Website inquiry from $name — $topic", $body, $email );

	// The message is stored in wp-admin → Inquiries even if the mail server is down.
	if ( $sent || ( $id && ! is_wp_error( $id ) ) ) {
		lyfd_form_respond( true, 'Thank you! Your message has been sent. We will get back to you shortly.', 'contact', 'contact' );
	}
	lyfd_form_respond( false, 'Sorry, we could not send your message. Please message us on Facebook instead.', 'contact', 'contact' );
}
add_action( 'admin_post_nopriv_lyfd_contact', 'lyfd_handle_contact' );
add_action( 'admin_post_lyfd_contact', 'lyfd_handle_contact' );
