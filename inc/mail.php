<?php
/**
 * Outgoing mail: SMTP via mail.lyfdfab.com (SSL, port 465).
 *
 * The SMTP *password is never stored in the theme or the repository*.
 * Add it to wp-config.php on the server (above "That's all, stop editing!"):
 *
 *     define( 'LYFD_SMTP_PASS', 'the-email-account-password' );
 *
 * Optional overrides (defaults shown):
 *
 *     define( 'LYFD_SMTP_HOST', 'mail.lyfdfab.com' );
 *     define( 'LYFD_SMTP_PORT', 465 );
 *     define( 'LYFD_SMTP_USER', 'info@lyfdfab.com' );
 *     define( 'LYFD_MAIL_TO',   'info@lyfdfab.com' );   // where inquiries/bookings are delivered
 *
 * Without LYFD_SMTP_PASS WordPress falls back to PHP mail() (on DDEV this lands in Mailpit).
 * If you prefer the WP Mail SMTP plugin, configure it instead (Other SMTP: mail.lyfdfab.com, port 465,
 * SSL, user info@lyfdfab.com); the theme then leaves mail settings alone.
 *
 * @package lyfd-luxe
 */

defined( 'ABSPATH' ) || exit;

function lyfd_const( $name, $default = '' ) {
	return defined( $name ) ? constant( $name ) : $default;
}

function lyfd_smtp_enabled() {
	// If the WP Mail SMTP plugin is active it owns the mail settings; the theme steps aside.
	if ( function_exists( 'wp_mail_smtp' ) ) {
		return false;
	}
	return '' !== (string) lyfd_const( 'LYFD_SMTP_PASS', '' );
}

/** Address that receives contact messages and booking requests. */
function lyfd_mail_to() {
	$to = is_email( lyfd_const( 'LYFD_MAIL_TO', 'info@lyfdfab.com' ) );
	return $to ? $to : 'info@lyfdfab.com';
}

function lyfd_mail_from() {
	$from = is_email( lyfd_const( 'LYFD_SMTP_USER', 'info@lyfdfab.com' ) );
	return $from ? $from : 'info@lyfdfab.com';
}

add_action(
	'phpmailer_init',
	function ( $mailer ) {
		if ( ! lyfd_smtp_enabled() ) {
			return;
		}
		$port = (int) lyfd_const( 'LYFD_SMTP_PORT', 465 );

		$mailer->isSMTP();
		$mailer->Host       = (string) lyfd_const( 'LYFD_SMTP_HOST', 'mail.lyfdfab.com' );
		$mailer->Port       = $port;
		$mailer->SMTPAuth   = true;
		$mailer->Username   = lyfd_mail_from();
		$mailer->Password   = (string) lyfd_const( 'LYFD_SMTP_PASS', '' );
		$mailer->SMTPSecure = ( 465 === $port ) ? 'ssl' : 'tls';
		$mailer->Timeout    = 15;
	}
);

add_filter(
	'wp_mail_from',
	function ( $from ) {
		return lyfd_smtp_enabled() ? lyfd_mail_from() : $from;
	}
);

add_filter(
	'wp_mail_from_name',
	function ( $name ) {
		return lyfd_smtp_enabled() ? 'LYFD Stainless Fabrication' : $name;
	}
);

add_action(
	'wp_mail_failed',
	function ( $error ) {
		// Visible in the PHP error log; never shows the password.
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
