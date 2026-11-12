<?php
/**
 * Appointment / store-visit booking calendar.
 *
 * - [lyfd_booking] shortcode renders the calendar; assets/js/forms.js drives it.
 * - Availability comes from Bookings → Settings (days, hours, slot length, blocked dates).
 * - Requests are stored in Bookings (wp-admin), emailed to LYFD, and acknowledged to the visitor.
 * - A slot is taken as soon as a request is made (pending or confirmed); cancelling frees it.
 *
 * @package lyfd-luxe
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
 * Settings
 * ------------------------------------------------------------------------- */

function lyfd_booking_defaults() {
	return array(
		'days'     => array( 1, 2, 3, 4, 5, 6 ), // 0 = Sunday … 6 = Saturday.
		'open'     => '09:00',
		'close'    => '17:00',
		'slot'     => 60,   // minutes.
		'capacity' => 1,    // visits per time slot.
		'lead'     => 1,    // earliest bookable day = today + N days.
		'advance'  => 60,   // latest bookable day = today + N days.
		'blocked'  => '',   // one YYYY-MM-DD per line (holidays, closures).
	);
}

function lyfd_booking_settings() {
	$saved = get_option( 'lyfd_booking_settings', array() );
	return wp_parse_args( is_array( $saved ) ? $saved : array(), lyfd_booking_defaults() );
}

function lyfd_booking_purposes() {
	return array(
		'visit'     => 'Store / workshop visit',
		'measure'   => 'Site inspection & measurement',
		'quotation' => 'Quotation consultation',
	);
}

/* ---------------------------------------------------------------------------
 * Availability engine
 * ------------------------------------------------------------------------- */

function lyfd_booking_parse_date( $date ) {
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $date ) ) {
		return null;
	}
	$d = DateTimeImmutable::createFromFormat( '!Y-m-d', $date, wp_timezone() );
	return ( $d && $d->format( 'Y-m-d' ) === $date ) ? $d : null;
}

/** Bookable times (H:i) for a date according to the rules only (ignores existing bookings). */
function lyfd_booking_day_times( $date ) {
	$s = lyfd_booking_settings();
	$d = lyfd_booking_parse_date( $date );
	if ( ! $d ) {
		return array();
	}
	$today = new DateTimeImmutable( 'today', wp_timezone() );
	$min   = $today->modify( '+' . (int) $s['lead'] . ' days' );
	$max   = $today->modify( '+' . (int) $s['advance'] . ' days' );
	if ( $d < $min || $d > $max ) {
		return array();
	}
	if ( ! in_array( (int) $d->format( 'w' ), array_map( 'intval', (array) $s['days'] ), true ) ) {
		return array();
	}
	$blocked = array_filter( array_map( 'trim', preg_split( '/\R/', (string) $s['blocked'] ) ) );
	if ( in_array( $date, $blocked, true ) ) {
		return array();
	}

	$step  = max( 15, (int) $s['slot'] );
	$start = strtotime( '1970-01-01 ' . $s['open'] . ':00 UTC' );
	$end   = strtotime( '1970-01-01 ' . $s['close'] . ':00 UTC' );
	$now   = new DateTimeImmutable( 'now', wp_timezone() );
	$out   = array();
	for ( $t = $start; $t + $step * 60 <= $end; $t += $step * 60 ) {
		$hm = gmdate( 'H:i', $t );
		if ( $d->setTime( (int) substr( $hm, 0, 2 ), (int) substr( $hm, 3, 2 ) ) <= $now->modify( '+1 hour' ) ) {
			continue; // never offer a time that is already (almost) past.
		}
		$out[] = $hm;
	}
	return $out;
}

/** How many active (non-cancelled) bookings each time already has on a date. */
function lyfd_booking_counts( $date ) {
	$ids    = get_posts(
		array(
			'post_type'      => 'lyfd_booking',
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array(
				array( 'key' => '_lyfd_date', 'value' => $date ),
				array( 'key' => '_lyfd_status', 'value' => 'cancelled', 'compare' => '!=' ),
			),
		)
	);
	$counts = array();
	foreach ( $ids as $id ) {
		$t            = get_post_meta( $id, '_lyfd_time', true );
		$counts[ $t ] = isset( $counts[ $t ] ) ? $counts[ $t ] + 1 : 1;
	}
	return $counts;
}

function lyfd_booking_free_times( $date ) {
	$times = lyfd_booking_day_times( $date );
	if ( ! $times ) {
		return array();
	}
	$cap    = max( 1, (int) lyfd_booking_settings()['capacity'] );
	$counts = lyfd_booking_counts( $date );
	return array_values(
		array_filter(
			$times,
			function ( $t ) use ( $counts, $cap ) {
				return ( isset( $counts[ $t ] ) ? $counts[ $t ] : 0 ) < $cap;
			}
		)
	);
}

/* ---------------------------------------------------------------------------
 * REST: GET /wp-json/lyfd/v1/availability?month=YYYY-MM
 * ------------------------------------------------------------------------- */

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'lyfd/v1',
			'/availability',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'args'                => array(
					'month' => array(
						'required'          => true,
						'validate_callback' => function ( $v ) {
							return (bool) preg_match( '/^\d{4}-\d{2}$/', (string) $v );
						},
					),
				),
				'callback'            => function ( WP_REST_Request $req ) {
					$month = $req['month'];
					$first = lyfd_booking_parse_date( $month . '-01' );
					if ( ! $first ) {
						return new WP_Error( 'lyfd_bad_month', 'Invalid month', array( 'status' => 400 ) );
					}
					$s     = lyfd_booking_settings();
					$today = new DateTimeImmutable( 'today', wp_timezone() );
					$days  = array();
					$n     = (int) $first->format( 't' );
					for ( $i = 1; $i <= $n; $i++ ) {
						$date  = sprintf( '%s-%02d', $month, $i );
						$times = lyfd_booking_free_times( $date );
						if ( $times ) {
							$days[ $date ] = $times;
						}
					}
					$res = new WP_REST_Response(
						array(
							'month' => $month,
							'today' => $today->format( 'Y-m-d' ),
							'max'   => $today->modify( '+' . (int) $s['advance'] . ' days' )->format( 'Y-m-d' ),
							'days'  => (object) $days,
						)
					);
					$res->header( 'Cache-Control', 'no-store' );
					return $res;
				},
			)
		);
	}
);

/* ---------------------------------------------------------------------------
 * Bookings inbox (custom post type)
 * ------------------------------------------------------------------------- */

add_action(
	'init',
	function () {
		register_post_type(
			'lyfd_booking',
			array(
				'labels'              => array(
					'name'          => 'Bookings',
					'singular_name' => 'Booking',
					'menu_name'     => 'Bookings',
					'all_items'     => 'All Bookings',
					'search_items'  => 'Search Bookings',
					'not_found'     => 'No bookings yet.',
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_icon'           => 'dashicons-calendar-alt',
				'menu_position'       => 25,
				'supports'            => array( 'title' ),
				'capability_type'     => 'post',
				'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'        => true,
				'exclude_from_search' => true,
			)
		);
	}
);

function lyfd_booking_status_labels() {
	return array(
		'pending'   => 'Pending',
		'confirmed' => 'Confirmed',
		'cancelled' => 'Cancelled',
	);
}

function lyfd_booking_when( $post_id ) {
	$date = get_post_meta( $post_id, '_lyfd_date', true );
	$time = get_post_meta( $post_id, '_lyfd_time', true );
	$d    = lyfd_booking_parse_date( $date );
	if ( ! $d ) {
		return '';
	}
	return wp_date( 'l, F j, Y', $d->getTimestamp() ) . ' at ' . wp_date( 'g:i A', strtotime( $date . ' ' . $time . ':00 ' . wp_timezone_string() ) );
}

add_filter(
	'manage_lyfd_booking_posts_columns',
	function () {
		return array(
			'cb'      => '<input type="checkbox" />',
			'title'   => 'Visitor',
			'when'    => 'Appointment',
			'purpose' => 'Purpose',
			'contact' => 'Contact',
			'status'  => 'Status',
			'date'    => 'Requested',
		);
	}
);

add_action(
	'manage_lyfd_booking_posts_custom_column',
	function ( $col, $id ) {
		switch ( $col ) {
			case 'when':
				echo esc_html( lyfd_booking_when( $id ) );
				break;
			case 'purpose':
				$p = lyfd_booking_purposes();
				$k = get_post_meta( $id, '_lyfd_purpose', true );
				echo esc_html( isset( $p[ $k ] ) ? $p[ $k ] : $k );
				break;
			case 'contact':
				echo esc_html( get_post_meta( $id, '_lyfd_email', true ) ) . '<br>' . esc_html( get_post_meta( $id, '_lyfd_phone', true ) );
				break;
			case 'status':
				$l = lyfd_booking_status_labels();
				$k = get_post_meta( $id, '_lyfd_status', true );
				echo '<strong>' . esc_html( isset( $l[ $k ] ) ? $l[ $k ] : $k ) . '</strong>';
				break;
		}
	},
	10,
	2
);

add_filter(
	'manage_edit-lyfd_booking_sortable_columns',
	function ( $cols ) {
		$cols['when'] = 'lyfd_when';
		return $cols;
	}
);

add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( is_admin() && $q->is_main_query() && 'lyfd_when' === $q->get( 'orderby' ) ) {
			$q->set( 'meta_key', '_lyfd_date' );
			$q->set( 'orderby', 'meta_value' );
		}
	}
);

add_action(
	'add_meta_boxes_lyfd_booking',
	function () {
		add_meta_box(
			'lyfd_booking_details',
			'Booking details',
			function ( $post ) {
				$p    = lyfd_booking_purposes();
				$k    = get_post_meta( $post->ID, '_lyfd_purpose', true );
				$rows = array(
					'Visitor'     => $post->post_title,
					'Appointment' => lyfd_booking_when( $post->ID ),
					'Purpose'     => isset( $p[ $k ] ) ? $p[ $k ] : $k,
					'Email'       => get_post_meta( $post->ID, '_lyfd_email', true ),
					'Phone'       => get_post_meta( $post->ID, '_lyfd_phone', true ),
				);
				echo '<table class="form-table"><tbody>';
				foreach ( $rows as $label => $val ) {
					printf( '<tr><th>%s</th><td>%s</td></tr>', esc_html( $label ), esc_html( $val ) );
				}
				printf( '<tr><th>Notes</th><td>%s</td></tr>', nl2br( esc_html( $post->post_content ) ) );

				wp_nonce_field( 'lyfd_booking_status', 'lyfd_booking_nonce' );
				$cur = get_post_meta( $post->ID, '_lyfd_status', true );
				echo '<tr><th><label for="lyfd_status">Status</label></th><td><select name="lyfd_status" id="lyfd_status">';
				foreach ( lyfd_booking_status_labels() as $val => $label ) {
					printf( '<option value="%s"%s>%s</option>', esc_attr( $val ), selected( $cur, $val, false ), esc_html( $label ) );
				}
				echo '</select><p class="description">Setting <em>Confirmed</em> emails the visitor a confirmation. <em>Cancelled</em> frees the time slot.</p></td></tr>';
				echo '</tbody></table>';
			},
			null,
			'normal',
			'high'
		);
	}
);

add_action(
	'save_post_lyfd_booking',
	function ( $post_id ) {
		if ( ! isset( $_POST['lyfd_booking_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lyfd_booking_nonce'] ) ), 'lyfd_booking_status' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) || ! isset( $_POST['lyfd_status'] ) ) {
			return;
		}
		$new = sanitize_key( wp_unslash( $_POST['lyfd_status'] ) );
		$old = get_post_meta( $post_id, '_lyfd_status', true );
		if ( ! isset( lyfd_booking_status_labels()[ $new ] ) || $new === $old ) {
			return;
		}
		update_post_meta( $post_id, '_lyfd_status', $new );

		if ( 'confirmed' === $new ) {
			$email = get_post_meta( $post_id, '_lyfd_email', true );
			$name  = get_the_title( $post_id );
			if ( is_email( $email ) ) {
				lyfd_send_mail(
					$email,
					'Your LYFD appointment is confirmed',
					"Hi $name,\n\nYour appointment with LYFD Stainless Fabrication and Services is confirmed:\n\n  " . lyfd_booking_when( $post_id )
					. "\n\nIf you need to change the schedule, just reply to this email or message us on Facebook.\n\nSee you soon!\nLYFD Stainless Fabrication and Services\nDavao City, Philippines\n",
					lyfd_mail_to()
				);
			}
		}
	}
);

/* ---------------------------------------------------------------------------
 * Settings page (Bookings → Settings)
 * ------------------------------------------------------------------------- */

add_action(
	'admin_menu',
	function () {
		add_submenu_page( 'edit.php?post_type=lyfd_booking', 'Booking Settings', 'Settings', 'manage_options', 'lyfd-booking-settings', 'lyfd_booking_settings_page' );
	}
);

add_action(
	'admin_init',
	function () {
		register_setting(
			'lyfd_booking',
			'lyfd_booking_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => function ( $in ) {
					$d    = lyfd_booking_defaults();
					$in   = is_array( $in ) ? $in : array();
					$time = function ( $v, $fallback ) {
						return preg_match( '/^([01]\d|2[0-3]):[0-5]\d$/', (string) $v ) ? $v : $fallback;
					};
					$days = isset( $in['days'] ) ? array_values( array_unique( array_filter( array_map( 'intval', (array) $in['days'] ), function ( $n ) {
						return $n >= 0 && $n <= 6;
					} ) ) ) : array();
					$blocked = array();
					foreach ( preg_split( '/\R/', isset( $in['blocked'] ) ? (string) $in['blocked'] : '' ) as $line ) {
						$line = trim( $line );
						if ( lyfd_booking_parse_date( $line ) ) {
							$blocked[] = $line;
						}
					}
					return array(
						'days'     => $days,
						'open'     => $time( isset( $in['open'] ) ? $in['open'] : '', $d['open'] ),
						'close'    => $time( isset( $in['close'] ) ? $in['close'] : '', $d['close'] ),
						'slot'     => min( 240, max( 15, (int) ( isset( $in['slot'] ) ? $in['slot'] : $d['slot'] ) ) ),
						'capacity' => min( 20, max( 1, (int) ( isset( $in['capacity'] ) ? $in['capacity'] : $d['capacity'] ) ) ),
						'lead'     => min( 30, max( 0, (int) ( isset( $in['lead'] ) ? $in['lead'] : $d['lead'] ) ) ),
						'advance'  => min( 365, max( 7, (int) ( isset( $in['advance'] ) ? $in['advance'] : $d['advance'] ) ) ),
						'blocked'  => implode( "\n", array_unique( $blocked ) ),
					);
				},
			)
		);
	}
);

function lyfd_booking_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$s     = lyfd_booking_settings();
	$names = array( 0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday' );
	?>
	<div class="wrap">
		<h1>Booking Settings</h1>
		<p>Controls which dates and times visitors can pick in the booking calendar on the homepage. Time zone: <strong><?php echo esc_html( wp_timezone_string() ); ?></strong> (change under Settings → General).</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'lyfd_booking' ); ?>
			<table class="form-table" role="presentation">
				<tr><th>Open days</th><td>
					<?php foreach ( $names as $n => $label ) : ?>
						<label style="margin-right:14px"><input type="checkbox" name="lyfd_booking_settings[days][]" value="<?php echo (int) $n; ?>" <?php checked( in_array( $n, array_map( 'intval', (array) $s['days'] ), true ) ); ?>> <?php echo esc_html( $label ); ?></label>
					<?php endforeach; ?>
				</td></tr>
				<tr><th><label for="lb-open">Opening time</label></th><td><input type="time" id="lb-open" name="lyfd_booking_settings[open]" value="<?php echo esc_attr( $s['open'] ); ?>"></td></tr>
				<tr><th><label for="lb-close">Closing time</label></th><td><input type="time" id="lb-close" name="lyfd_booking_settings[close]" value="<?php echo esc_attr( $s['close'] ); ?>"></td></tr>
				<tr><th><label for="lb-slot">Appointment length (minutes)</label></th><td><input type="number" id="lb-slot" min="15" max="240" step="15" name="lyfd_booking_settings[slot]" value="<?php echo (int) $s['slot']; ?>"></td></tr>
				<tr><th><label for="lb-cap">Visits per time slot</label></th><td><input type="number" id="lb-cap" min="1" max="20" name="lyfd_booking_settings[capacity]" value="<?php echo (int) $s['capacity']; ?>"><p class="description">How many visitors can book the same time.</p></td></tr>
				<tr><th><label for="lb-lead">Minimum notice (days)</label></th><td><input type="number" id="lb-lead" min="0" max="30" name="lyfd_booking_settings[lead]" value="<?php echo (int) $s['lead']; ?>"><p class="description">0 = same-day bookings allowed (at least 1 hour ahead).</p></td></tr>
				<tr><th><label for="lb-adv">Book up to (days ahead)</label></th><td><input type="number" id="lb-adv" min="7" max="365" name="lyfd_booking_settings[advance]" value="<?php echo (int) $s['advance']; ?>"></td></tr>
				<tr><th><label for="lb-blk">Closed dates</label></th><td><textarea id="lb-blk" rows="5" cols="20" name="lyfd_booking_settings[blocked]" placeholder="2026-12-25"><?php echo esc_textarea( $s['blocked'] ); ?></textarea><p class="description">One date per line, format YYYY-MM-DD (holidays, shop closures).</p></td></tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/* ---------------------------------------------------------------------------
 * Shortcode
 * ------------------------------------------------------------------------- */

add_shortcode(
	'lyfd_booking',
	function () {
		ob_start();
		?>
		<div class="lyfd-book" data-endpoint="<?php echo esc_url( rest_url( 'lyfd/v1/availability' ) ); ?>">
			<div class="lyfd-book__cal">
				<div class="lyfd-cal__head">
					<button type="button" class="lyfd-cal__nav" data-nav="-1" aria-label="Previous month">&lsaquo;</button>
					<h3 class="lyfd-cal__month" aria-live="polite">Loading&hellip;</h3>
					<button type="button" class="lyfd-cal__nav" data-nav="1" aria-label="Next month">&rsaquo;</button>
				</div>
				<div class="lyfd-cal__dow" aria-hidden="true"><span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span></div>
				<div class="lyfd-cal__grid" role="grid" aria-label="Choose a date"></div>
				<p class="lyfd-cal__legend"><i class="is-open"></i> Available <i class="is-off"></i> Unavailable</p>
			</div>

			<div class="lyfd-book__side">
				<div class="lyfd-book__step is-active" data-step="1">
					<p class="lyfd-book__hint">Choose an available date to see the times.</p>
				</div>

				<div class="lyfd-book__step" data-step="2" hidden>
					<p class="lyfd-book__label">Available times <span class="lyfd-book__date"></span></p>
					<div class="lyfd-slots" role="group" aria-label="Available times"></div>
				</div>

				<form class="lyfd-form lyfd-form--booking" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-step="3" hidden novalidate>
					<input type="hidden" name="action" value="lyfd_booking">
					<input type="hidden" name="lyfd_t" value="<?php echo esc_attr( lyfd_form_token() ); ?>">
					<input type="hidden" name="date" value="">
					<input type="hidden" name="time" value="">
					<p class="lyfd-hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>

					<p class="lyfd-book__chosen" aria-live="polite"></p>
					<p class="lyfd-field"><label for="lyfd-b-purpose">Purpose</label>
						<select id="lyfd-b-purpose" name="purpose">
							<?php foreach ( lyfd_booking_purposes() as $k => $label ) : ?>
								<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select></p>
					<div class="lyfd-form__row">
						<p class="lyfd-field"><label for="lyfd-b-name">Your name <span aria-hidden="true">*</span></label>
							<input id="lyfd-b-name" name="name" type="text" autocomplete="name" required maxlength="120"></p>
						<p class="lyfd-field"><label for="lyfd-b-phone">Phone <span aria-hidden="true">*</span></label>
							<input id="lyfd-b-phone" name="phone" type="tel" autocomplete="tel" required maxlength="40"></p>
					</div>
					<p class="lyfd-field"><label for="lyfd-b-email">Email <span aria-hidden="true">*</span></label>
						<input id="lyfd-b-email" name="email" type="email" autocomplete="email" required maxlength="160"></p>
					<p class="lyfd-field"><label for="lyfd-b-notes">Notes (optional)</label>
						<textarea id="lyfd-b-notes" name="notes" rows="3" maxlength="1500" placeholder="Location, what you need measured, etc."></textarea></p>
					<p class="lyfd-form__actions"><button type="submit" class="lyfd-btn"><span>Request Appointment</span></button>
						<button type="button" class="lyfd-link" data-back>Change time</button></p>
					<p class="lyfd-form__status" role="status" aria-live="polite" hidden></p>
				</form>

				<div class="lyfd-book__done" data-step="4" hidden>
					<p class="lyfd-book__tick" aria-hidden="true">&#10003;</p>
					<h3>Request received</h3>
					<p class="lyfd-book__done-msg"></p>
					<button type="button" class="lyfd-link" data-restart>Book another time</button>
				</div>
			</div>
		</div>
		<noscript><p>Please enable JavaScript to use the booking calendar, or message us on Facebook to book a visit.</p></noscript>
		<?php
		return ob_get_clean();
	}
);

/* ---------------------------------------------------------------------------
 * Handler
 * ------------------------------------------------------------------------- */

function lyfd_handle_booking() {
	if ( '' !== trim( lyfd_post( 'website' ) ) || ! lyfd_form_token_ok( lyfd_post( 'lyfd_t' ) ) ) {
		lyfd_form_respond( true, 'Thank you! Your request has been received.', 'book', 'book' );
	}
	if ( lyfd_rate_limited( 'booking', 4 ) ) {
		lyfd_form_respond( false, 'Too many requests in a short time. Please wait a few minutes and try again.', 'book', 'book' );
	}

	$date    = sanitize_text_field( lyfd_post( 'date' ) );
	$time    = sanitize_text_field( lyfd_post( 'time' ) );
	$name    = sanitize_text_field( lyfd_post( 'name' ) );
	$phone   = sanitize_text_field( lyfd_post( 'phone' ) );
	$email   = sanitize_email( lyfd_post( 'email' ) );
	$notes   = sanitize_textarea_field( lyfd_post( 'notes' ) );
	$purpose = sanitize_key( lyfd_post( 'purpose' ) );

	if ( ! isset( lyfd_booking_purposes()[ $purpose ] ) ) {
		$purpose = 'visit';
	}
	if ( '' === $name || '' === $phone || ! is_email( $email ) ) {
		lyfd_form_respond( false, 'Please enter your name, phone number and a valid email.', 'book', 'book' );
	}
	if ( lyfd_count_links( $notes ) > 1 ) {
		lyfd_form_respond( false, 'Please remove links from your notes.', 'book', 'book' );
	}
	if ( ! in_array( $time, lyfd_booking_free_times( $date ), true ) ) {
		lyfd_form_respond( false, 'Sorry, that time is no longer available. Please pick another one.', 'book', 'book', array( 'refresh' => true ) );
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'lyfd_booking',
			'post_status'  => 'publish',
			'post_title'   => $name,
			'post_content' => $notes,
			'meta_input'   => array(
				'_lyfd_date'    => $date,
				'_lyfd_time'    => $time,
				'_lyfd_purpose' => $purpose,
				'_lyfd_email'   => $email,
				'_lyfd_phone'   => $phone,
				'_lyfd_status'  => 'pending',
			),
		),
		true
	);
	if ( is_wp_error( $id ) || ! $id ) {
		lyfd_form_respond( false, 'Sorry, something went wrong. Please try again or message us on Facebook.', 'book', 'book' );
	}

	$when    = lyfd_booking_when( $id );
	$purpose_label = lyfd_booking_purposes()[ $purpose ];
	$edit    = admin_url( 'post.php?post=' . $id . '&action=edit' );

	lyfd_send_mail(
		lyfd_mail_to(),
		"New appointment request: $when — $name",
		"New appointment request\n\nWhen:    $when\nPurpose: $purpose_label\nName:    $name\nPhone:   $phone\nEmail:   $email\n\nNotes:\n" . ( $notes ? $notes : '-' )
		. "\n\nConfirm or cancel it here:\n$edit\n",
		$email
	);
	lyfd_send_mail(
		$email,
		'We received your appointment request — LYFD',
		"Hi $name,\n\nThank you for booking with LYFD Stainless Fabrication and Services.\n\nRequested: $when\nPurpose:   $purpose_label\n\nWe will confirm your appointment shortly. If you need to change anything, just reply to this email or message us on Facebook.\n\nLYFD Stainless Fabrication and Services\nDavao City, Philippines\n",
		lyfd_mail_to()
	);

	lyfd_form_respond( true, "Thank you, $name! We received your request for $when. We will confirm it by email shortly.", 'book', 'book', array( 'when' => $when ) );
}
add_action( 'admin_post_nopriv_lyfd_booking', 'lyfd_handle_booking' );
add_action( 'admin_post_lyfd_booking', 'lyfd_handle_booking' );
