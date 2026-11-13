<?php
/**
 * Outgoing mail (SMTP) for the contact form and booking calendar.
 *
 * Configure it in wp-admin → Settings → LYFD Email (inc/mail-settings.php).
 * Defaults match the LYFD cPanel mailbox: mail.lyfdfab.com, port 465 (SSL), info@lyfdfab.com.
 *
 * Optional wp-config.php overrides (they win over the settings page; handy for deployments):
 *
 *     define( 'LYFD_SMTP_PASS', '…' );  define( 'LYFD_SMTP_HOST', '…' );
 *     define( 'LYFD_SMTP_PORT', 465 );  define( 'LYFD_SMTP_USER', '…' );
 *     define( 'LYFD_MAIL_TO',   'info@lyfdfab.com' );
 *
 * Until a password is saved, WordPress falls back to PHP mail() (on DDEV this lands in Mailpit).
 *
 * @package lyfd-luxe
 */

defined( 'ABSPATH' ) || exit;

function lyfd_const( $name, $default = '' ) {
	return defined( $name ) ? constant( $name ) : $default;
}

/* ---------------------------------------------------------------------------
 * Settings + password encryption
 * ------------------------------------------------------------------------- */

function lyfd_mail_defaults() {
	return array(
		'enabled'    => 1,
		'host'       => 'mail.lyfdfab.com',
		'port'       => 465,
		'encryption' => 'ssl',   // ssl | tls | none.
		'auth'       => 1,
		'user'       => 'info@lyfdfab.com',
		'from_email' => 'info@lyfdfab.com',
		'from_name'  => 'LYFD Stainless Fabrication',
		'to_email'   => 'info@lyfdfab.com, lyfd.fabrication@gmail.com', // comma-separated inboxes for inquiries and booking requests.
	);
}

/** The saved password is stored encrypted (AES-256, key derived from the site's secret salts). */
function lyfd_secret_encrypt( $plain ) {
	if ( '' === $plain ) {
		return '';
	}
	if ( ! function_exists( 'openssl_encrypt' ) ) {
		return 'b64:' . base64_encode( $plain ); // obfuscation only; hosts without OpenSSL are rare.
	}
	$key = hash( 'sha256', wp_salt( 'auth' ), true );
	$iv  = random_bytes( 16 );
	$enc = openssl_encrypt( $plain, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv );
	return 'enc:' . base64_encode( $iv . $enc );
}

function lyfd_secret_decrypt( $stored ) {
	if ( ! is_string( $stored ) || '' === $stored ) {
		return '';
	}
	if ( 0 === strpos( $stored, 'b64:' ) ) {
		return (string) base64_decode( substr( $stored, 4 ) );
	}
	if ( 0 !== strpos( $stored, 'enc:' ) || ! function_exists( 'openssl_decrypt' ) ) {
		return '';
	}
	$raw = base64_decode( substr( $stored, 4 ), true );
	if ( false === $raw || strlen( $raw ) < 17 ) {
		return '';
	}
	$key = hash( 'sha256', wp_salt( 'auth' ), true );
	$out = openssl_decrypt( substr( $raw, 16 ), 'aes-256-cbc', $key, OPENSSL_RAW_DATA, substr( $raw, 0, 16 ) );
	return false === $out ? '' : $out; // different salts (site moved) → blank; re-enter the password.
}

/** Effective settings: defaults ← saved options ← wp-config constants. */
function lyfd_mail_settings() {
	$saved = get_option( 'lyfd_mail_settings', array() );
	$saved = is_array( $saved ) ? $saved : array();
	$s     = wp_parse_args( $saved, lyfd_mail_defaults() );
	$s['pass'] = lyfd_secret_decrypt( isset( $saved['pass_enc'] ) ? $saved['pass_enc'] : '' );

	$overrides = array(
		'host'     => 'LYFD_SMTP_HOST',
		'port'     => 'LYFD_SMTP_PORT',
		'user'     => 'LYFD_SMTP_USER',
		'pass'     => 'LYFD_SMTP_PASS',
		'to_email' => 'LYFD_MAIL_TO',
	);
	foreach ( $overrides as $key => $const ) {
		if ( defined( $const ) && '' !== (string) constant( $const ) ) {
			$s[ $key ] = constant( $const );
		}
	}
	return $s;
}

/** Why SMTP is (not) active — shown on the settings page. */
function lyfd_smtp_status() {
	$s = lyfd_mail_settings();
	if ( empty( $s['enabled'] ) ) {
		return array( false, 'SMTP is switched off — WordPress uses PHP mail().' );
	}
	if ( '' === trim( (string) $s['host'] ) ) {
		return array( false, 'No SMTP host set.' );
	}
	if ( ! empty( $s['auth'] ) && ( '' === (string) $s['user'] || '' === (string) $s['pass'] ) ) {
		return array( false, 'Waiting for the mailbox password — until it is saved WordPress uses PHP mail().' );
	}
	return array( true, 'SMTP is active (' . $s['host'] . ':' . (int) $s['port'] . ').' );
}

function lyfd_smtp_enabled() {
	$status = lyfd_smtp_status();
	return $status[0];
}

/** Parse a comma/semicolon/space separated list into unique valid emails. */
function lyfd_parse_emails( $list ) {
	$out = array();
	foreach ( preg_split( '/[\s,;]+/', (string) $list ) as $e ) {
		$e = sanitize_email( $e );
		if ( is_email( $e ) && ! in_array( strtolower( $e ), array_map( 'strtolower', $out ), true ) ) {
			$out[] = $e;
		}
	}
	return $out;
}

/** Inboxes that receive contact messages and booking requests (array). */
function lyfd_mail_to() {
	$s  = lyfd_mail_settings();
	$to = lyfd_parse_emails( $s['to_email'] );
	return $to ? $to : lyfd_parse_emails( lyfd_mail_defaults()['to_email'] );
}

/** Single address visitors reply to (first recipient). */
function lyfd_mail_reply_to() {
	$to = lyfd_mail_to();
	return $to[0];
}

/* ---------------------------------------------------------------------------
 * Hook into wp_mail()
 * ------------------------------------------------------------------------- */

add_action(
	'phpmailer_init',
	function ( $mailer ) {
		if ( ! lyfd_smtp_enabled() ) {
			return;
		}
		$s    = lyfd_mail_settings();
		$enc  = in_array( $s['encryption'], array( 'ssl', 'tls', 'none' ), true ) ? $s['encryption'] : 'ssl';

		$mailer->isSMTP();
		$mailer->Host     = (string) $s['host'];
		$mailer->Port     = (int) $s['port'];
		$mailer->SMTPAuth = ! empty( $s['auth'] );
		if ( $mailer->SMTPAuth ) {
			$mailer->Username = (string) $s['user'];
			$mailer->Password = (string) $s['pass'];
		}
		$mailer->SMTPSecure   = ( 'none' === $enc ) ? '' : $enc;
		$mailer->SMTPAutoTLS  = ( 'none' !== $enc );
		$mailer->Timeout      = 15;
		if ( is_email( $s['from_email'] ) ) {
			$mailer->Sender = $s['from_email']; // envelope sender / return-path.
		}
	}
);

add_filter(
	'wp_mail_from',
	function ( $from ) {
		if ( ! lyfd_smtp_enabled() ) {
			return $from;
		}
		$s = lyfd_mail_settings();
		return is_email( $s['from_email'] ) ? $s['from_email'] : $from;
	}
);

add_filter(
	'wp_mail_from_name',
	function ( $name ) {
		if ( ! lyfd_smtp_enabled() ) {
			return $name;
		}
		$s = lyfd_mail_settings();
		return '' !== trim( (string) $s['from_name'] ) ? $s['from_name'] : $name;
	}
);

add_action(
	'wp_mail_failed',
	function ( $error ) {
		$GLOBALS['lyfd_last_mail_error'] = $error->get_error_message();
		error_log( 'LYFD mail failed: ' . $error->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
	}
);

/**
 * Send a plain-text email. Returns bool.
 *
 * @param string $to       Recipient.
 * @param string $subject  Subject (single line).
 * @param string $body     Plain text body.
 * @param string $reply_to Optional Reply-To address.
 */
function lyfd_send_mail( $to, $subject, $body, $reply_to = '' ) {
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( $reply_to && is_email( $reply_to ) ) {
		$headers[] = 'Reply-To: ' . $reply_to;
	}
	$subject = trim( preg_replace( '/[\r\n]+/', ' ', $subject ) );
	return wp_mail( $to, $subject, $body, $headers );
}
