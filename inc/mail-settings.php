<?php
/**
 * wp-admin → Settings → LYFD Email: SMTP access + "send test email".
 *
 * @package lyfd-luxe
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	function () {
		add_options_page( 'LYFD Email (SMTP)', 'LYFD Email', 'manage_options', 'lyfd-email', 'lyfd_mail_settings_page' );
	}
);

add_action(
	'admin_init',
	function () {
		register_setting(
			'lyfd_mail',
			'lyfd_mail_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'lyfd_mail_sanitize',
			)
		);
	}
);

function lyfd_mail_sanitize( $in ) {
	$d   = lyfd_mail_defaults();
	$old = get_option( 'lyfd_mail_settings', array() );
	$old = is_array( $old ) ? $old : array();
	$in  = is_array( $in ) ? $in : array();

	$email = function ( $v, $fallback ) {
		$v = sanitize_email( $v );
		return is_email( $v ) ? $v : $fallback;
	};
	$enc = isset( $in['encryption'] ) ? sanitize_key( $in['encryption'] ) : $d['encryption'];
	$enc = in_array( $enc, array( 'ssl', 'tls', 'none' ), true ) ? $enc : $d['encryption'];

	$pass_in = isset( $in['pass'] ) ? (string) $in['pass'] : '';
	$pass    = '' !== $pass_in ? lyfd_secret_encrypt( $pass_in ) : ( isset( $old['pass_enc'] ) ? $old['pass_enc'] : '' );
	if ( ! empty( $in['clear_pass'] ) ) {
		$pass = '';
	}

	return array(
		'enabled'    => empty( $in['enabled'] ) ? 0 : 1,
		'host'       => preg_replace( '/[^a-z0-9.\-]/i', '', isset( $in['host'] ) ? $in['host'] : $d['host'] ),
		'port'       => min( 65535, max( 1, (int) ( isset( $in['port'] ) ? $in['port'] : $d['port'] ) ) ),
		'encryption' => $enc,
		'auth'       => empty( $in['auth'] ) ? 0 : 1,
		'user'       => sanitize_text_field( isset( $in['user'] ) ? $in['user'] : $d['user'] ),
		'pass_enc'   => $pass,
		'from_email' => $email( isset( $in['from_email'] ) ? $in['from_email'] : '', $d['from_email'] ),
		'from_name'  => sanitize_text_field( isset( $in['from_name'] ) ? $in['from_name'] : $d['from_name'] ),
		'to_email'   => implode( ', ', lyfd_parse_emails( isset( $in['to_email'] ) ? $in['to_email'] : '' ) ) ?: $d['to_email'],
	);
}

/* Send a test email ---------------------------------------------------------- */

add_action(
	'admin_post_lyfd_mail_test',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Not allowed.', 403 );
		}
		check_admin_referer( 'lyfd_mail_test' );

		$to = isset( $_POST['test_to'] ) ? sanitize_email( wp_unslash( $_POST['test_to'] ) ) : '';
		if ( ! is_email( $to ) ) {
			$to = lyfd_mail_to();
		}
		$to_label = is_array( $to ) ? implode( ', ', $to ) : $to;
		unset( $GLOBALS['lyfd_last_mail_error'] );
		$ok = lyfd_send_mail(
			$to,
			'LYFD website — test email',
			"This is a test email from " . home_url( '/' ) . ".\n\nSMTP status: " . lyfd_smtp_status()[1] . "\nIf you can read this, the website can send email.\n"
		);
		set_transient(
			'lyfd_mail_test_' . get_current_user_id(),
			array(
				'ok'  => (bool) $ok,
				'msg' => $ok
					? 'Test email sent to ' . $to_label . '. Check that inbox (and spam).'
					: 'Sending failed' . ( isset( $GLOBALS['lyfd_last_mail_error'] ) ? ': ' . $GLOBALS['lyfd_last_mail_error'] : '.' ),
			),
			120
		);
		wp_safe_redirect( admin_url( 'options-general.php?page=lyfd-email' ) );
		exit;
	}
);

/* The page -------------------------------------------------------------------- */

function lyfd_mail_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$s      = lyfd_mail_settings();
	$saved  = get_option( 'lyfd_mail_settings', array() );
	$has_pw = '' !== (string) $s['pass'];
	$pw_const = defined( 'LYFD_SMTP_PASS' ) && '' !== (string) LYFD_SMTP_PASS;
	$status = lyfd_smtp_status();
	$result = get_transient( 'lyfd_mail_test_' . get_current_user_id() );
	if ( $result ) {
		delete_transient( 'lyfd_mail_test_' . get_current_user_id() );
	}
	$opt = 'lyfd_mail_settings';
	?>
	<div class="wrap">
		<h1>LYFD Email (SMTP)</h1>
		<p>How the website sends the contact-form messages and booking emails. Messages are always saved in <strong>Inquiries</strong> / <strong>Bookings</strong> as well.</p>

		<?php if ( $result ) : ?>
			<div class="notice <?php echo $result['ok'] ? 'notice-success' : 'notice-error'; ?> is-dismissible"><p><?php echo esc_html( $result['msg'] ); ?></p></div>
		<?php endif; ?>
		<?php if ( isset( $_GET['settings-updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
			<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>
		<?php endif; ?>

		<p><strong>Status:</strong> <span style="color:<?php echo $status[0] ? '#1a7f37' : '#b45309'; ?>">&#9679;</span> <?php echo esc_html( $status[1] ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'lyfd_mail' ); ?>
			<table class="form-table" role="presentation">
				<tr><th>Use SMTP</th><td><label><input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[enabled]" value="1" <?php checked( ! empty( $s['enabled'] ) ); ?>> Send website email through the mail server below</label></td></tr>
				<tr><th><label for="lm-host">Outgoing server (SMTP host)</label></th><td><input type="text" class="regular-text" id="lm-host" name="<?php echo esc_attr( $opt ); ?>[host]" value="<?php echo esc_attr( $s['host'] ); ?>" <?php disabled( defined( 'LYFD_SMTP_HOST' ) ); ?>></td></tr>
				<tr><th><label for="lm-port">SMTP port</label></th><td>
					<input type="number" id="lm-port" class="small-text" min="1" max="65535" name="<?php echo esc_attr( $opt ); ?>[port]" value="<?php echo (int) $s['port']; ?>" <?php disabled( defined( 'LYFD_SMTP_PORT' ) ); ?>>
					&nbsp;<label for="lm-enc" class="screen-reader-text">Encryption</label>
					<select id="lm-enc" name="<?php echo esc_attr( $opt ); ?>[encryption]">
						<option value="ssl" <?php selected( $s['encryption'], 'ssl' ); ?>>SSL (port 465)</option>
						<option value="tls" <?php selected( $s['encryption'], 'tls' ); ?>>TLS / STARTTLS (port 587)</option>
						<option value="none" <?php selected( $s['encryption'], 'none' ); ?>>None</option>
					</select>
				</td></tr>
				<tr><th>Authentication</th><td><label><input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[auth]" value="1" <?php checked( ! empty( $s['auth'] ) ); ?>> The server requires a username and password (leave on for cPanel mail)</label></td></tr>
				<tr><th><label for="lm-user">Username</label></th><td><input type="text" class="regular-text" id="lm-user" autocomplete="off" name="<?php echo esc_attr( $opt ); ?>[user]" value="<?php echo esc_attr( $s['user'] ); ?>" <?php disabled( defined( 'LYFD_SMTP_USER' ) ); ?>></td></tr>
				<tr><th><label for="lm-pass">Password</label></th><td>
					<?php if ( $pw_const ) : ?>
						<em>Set in wp-config.php (LYFD_SMTP_PASS) — that value is used.</em>
					<?php else : ?>
						<input type="password" class="regular-text" id="lm-pass" autocomplete="new-password" name="<?php echo esc_attr( $opt ); ?>[pass]" value="" placeholder="<?php echo $has_pw ? '•••••••••• (saved — type to replace)' : 'The email account’s password'; ?>">
						<?php if ( $has_pw ) : ?><label style="margin-left:10px"><input type="checkbox" name="<?php echo esc_attr( $opt ); ?>[clear_pass]" value="1"> remove saved password</label><?php endif; ?>
						<p class="description">Stored encrypted in the database and never shown again. If you move the site to a server with different security keys in wp-config.php, enter it once more.</p>
					<?php endif; ?>
				</td></tr>
				<tr><th><label for="lm-from">“From” email</label></th><td><input type="email" class="regular-text" id="lm-from" name="<?php echo esc_attr( $opt ); ?>[from_email]" value="<?php echo esc_attr( $s['from_email'] ); ?>"><p class="description">Should be the same mailbox as the username, or the server may reject the message.</p></td></tr>
				<tr><th><label for="lm-name">“From” name</label></th><td><input type="text" class="regular-text" id="lm-name" name="<?php echo esc_attr( $opt ); ?>[from_name]" value="<?php echo esc_attr( $s['from_name'] ); ?>"></td></tr>
				<tr><th><label for="lm-to">Send inquiries &amp; bookings to</label></th><td><input type="text" class="large-text" id="lm-to" name="<?php echo esc_attr( $opt ); ?>[to_email]" value="<?php echo esc_attr( $s['to_email'] ); ?>" <?php disabled( defined( 'LYFD_MAIL_TO' ) ); ?>><p class="description">One or more addresses, separated by commas. Every address receives each inquiry and booking request.</p></td></tr>
			</table>
			<p class="description">Incoming mail (IMAP 993 / POP3 995 on mail.lyfdfab.com) is only needed in an email app such as Outlook or a phone — the website only <em>sends</em> email.</p>
			<?php submit_button( 'Save settings' ); ?>
		</form>

		<hr>
		<h2>Send a test email</h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="lyfd_mail_test">
			<?php wp_nonce_field( 'lyfd_mail_test' ); ?>
			<p><label for="lm-test">Send to&nbsp;</label><input type="email" id="lm-test" class="regular-text" name="test_to" value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>"> <button type="submit" class="button button-secondary">Send test email</button></p>
			<p class="description">Save your settings first. If sending fails, the error message from the mail server is shown here.</p>
		</form>
	</div>
	<?php
}
