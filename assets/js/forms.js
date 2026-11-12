/**
 * LYFD Luxe – contact form (AJAX) + booking calendar.
 * Works without a framework. Forms still post normally if this script fails to load.
 */
( function () {
	'use strict';

	var doc = document;
	function $( sel, ctx ) { return ( ctx || doc ).querySelector( sel ); }
	function $$( sel, ctx ) { return Array.prototype.slice.call( ( ctx || doc ).querySelectorAll( sel ) ); }
	function pad( n ) { return ( n < 10 ? '0' : '' ) + n; }
	function ymd( y, m, d ) { return y + '-' + pad( m ) + '-' + pad( d ); }

	function fmtTime( hm ) {
		var p = hm.split( ':' ), h = parseInt( p[ 0 ], 10 );
		return ( ( h + 11 ) % 12 + 1 ) + ':' + p[ 1 ] + ' ' + ( h < 12 ? 'AM' : 'PM' );
	}
	function fmtDate( iso ) {
		var p = iso.split( '-' );
		return new Date( +p[ 0 ], +p[ 1 ] - 1, +p[ 2 ] ).toLocaleDateString( 'en-PH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' } );
	}

	/* ---------- status line ---------- */
	function showStatus( form, ok, msg ) {
		var el = $( '.lyfd-form__status[aria-live]', form );
		if ( ! el ) { return; }
		el.hidden = false;
		el.textContent = msg;
		el.classList.toggle( 'is-ok', !! ok );
		el.classList.toggle( 'is-error', ! ok );
	}

	/* ---------- AJAX submit (shared by both forms) ---------- */
	function enhanceForm( form, onDone ) {
		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			var status = $( '.lyfd-form__status[aria-live]', form );
			if ( status ) { status.hidden = true; }

			var invalid = $$( '[required]', form ).filter( function ( f ) { return ! f.checkValidity(); } );
			if ( invalid.length ) {
				invalid[ 0 ].reportValidity();
				return;
			}

			var btn = $( 'button[type="submit"]', form );
			btn.disabled = true;
			form.classList.add( 'is-busy' );

			fetch( form.getAttribute( 'action' ), {
				method: 'POST',
				body: new FormData( form ),
				headers: { 'Accept': 'application/json' },
				credentials: 'same-origin'
			} )
				.then( function ( r ) { return r.json().catch( function () { return { ok: false, message: 'Unexpected response. Please try again.' }; } ); } )
				.then( function ( res ) {
					if ( onDone ) { onDone( res ); } else {
						showStatus( form, res.ok, res.message );
						if ( res.ok ) { form.reset(); }
					}
				} )
				.catch( function () { showStatus( form, false, 'Network error. Please check your connection and try again.' ); } )
				.then( function () {
					btn.disabled = false;
					form.classList.remove( 'is-busy' );
				} );
		} );
	}

	$$( '.lyfd-form--contact' ).forEach( function ( f ) { enhanceForm( f ); } );

	/* ---------- booking calendar ---------- */
	$$( '.lyfd-book' ).forEach( function ( root ) {
		var endpoint = root.getAttribute( 'data-endpoint' );
		var grid = $( '.lyfd-cal__grid', root );
		var title = $( '.lyfd-cal__month', root );
		var prev = $( '[data-nav="-1"]', root );
		var next = $( '[data-nav="1"]', root );
		var step1 = $( '[data-step="1"]', root );
		var step2 = $( '[data-step="2"]', root );
		var form = $( '[data-step="3"]', root );
		var done = $( '[data-step="4"]', root );
		var slotsBox = $( '.lyfd-slots', root );

		var now = new Date();
		var view = { y: now.getFullYear(), m: now.getMonth() + 1 };
		var minView = { y: view.y, m: view.m };
		var maxView = null;
		var cache = {};
		var selected = null;
		var autoSkipped = 0;

		function key( v ) { return v.y + '-' + pad( v.m ); }

		function show( which ) {
			[ step1, step2, form, done ].forEach( function ( el ) {
				var on = el === which;
				el.hidden = ! on;
				el.classList.toggle( 'is-active', on );
			} );
		}

		function load( v ) {
			var k = key( v );
			if ( cache[ k ] ) { return Promise.resolve( cache[ k ] ); }
			return fetch( endpoint + ( endpoint.indexOf( '?' ) > -1 ? '&' : '?' ) + 'month=' + k, { headers: { 'Accept': 'application/json' } } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( data ) { cache[ k ] = data; return data; } );
		}

		function render( data ) {
			var first = new Date( view.y, view.m - 1, 1 );
			var count = new Date( view.y, view.m, 0 ).getDate();
			title.textContent = first.toLocaleDateString( 'en-PH', { month: 'long', year: 'numeric' } );
			grid.innerHTML = '';
			for ( var b = 0; b < first.getDay(); b++ ) {
				var blank = doc.createElement( 'span' );
				blank.className = 'lyfd-cal__blank';
				grid.appendChild( blank );
			}
			for ( var d = 1; d <= count; d++ ) {
				var iso = ymd( view.y, view.m, d );
				var btn = doc.createElement( 'button' );
				btn.type = 'button';
				btn.className = 'lyfd-cal__day';
				btn.textContent = d;
				btn.setAttribute( 'data-date', iso );
				var open = data.days && data.days[ iso ];
				if ( ! open ) {
					btn.disabled = true;
				} else {
					btn.classList.add( 'is-open' );
					btn.setAttribute( 'aria-label', fmtDate( iso ) + ', ' + open.length + ' times available' );
				}
				if ( iso === data.today ) { btn.classList.add( 'is-today' ); }
				if ( selected && selected.date === iso ) { btn.classList.add( 'is-selected' ); }
				grid.appendChild( btn );
			}
			prev.disabled = ( view.y === minView.y && view.m <= minView.m ) || view.y < minView.y;
			var mx = data.max ? data.max.split( '-' ) : null;
			next.disabled = !! mx && ( view.y > +mx[ 0 ] || ( view.y === +mx[ 0 ] && view.m >= +mx[ 1 ] ) );
			grid.classList.remove( 'is-loading' );
		}

		function refresh() {
			grid.classList.add( 'is-loading' );
			return load( view ).then( function ( data ) {
				// first visit: if this month has nothing left, move forward to the next month that has
				var hasDays = data.days && Object.keys( data.days ).length;
				if ( ! hasDays && ! selected && autoSkipped < 4 && ! ( data.max && key( view ) >= data.max.slice( 0, 7 ) ) ) {
					autoSkipped++;
					view.m++;
					if ( view.m > 12 ) { view.m = 1; view.y++; }
					return refresh();
				}
				autoSkipped = 99;
				render( data );
			} ).catch( function () {
				grid.classList.remove( 'is-loading' );
				title.textContent = 'Calendar unavailable';
				grid.innerHTML = '<p class="lyfd-cal__error">We could not load the calendar. Please message us on Facebook to book a visit.</p>';
			} );
		}

		function pickDate( iso ) {
			var data = cache[ key( view ) ];
			var times = data && data.days && data.days[ iso ];
			if ( ! times ) { return; }
			selected = { date: iso, time: null };
			$$( '.lyfd-cal__day', grid ).forEach( function ( b ) { b.classList.toggle( 'is-selected', b.getAttribute( 'data-date' ) === iso ); } );
			$( '.lyfd-book__date', step2 ).textContent = '— ' + fmtDate( iso );
			slotsBox.innerHTML = '';
			times.forEach( function ( t ) {
				var b = doc.createElement( 'button' );
				b.type = 'button';
				b.className = 'lyfd-slot';
				b.textContent = fmtTime( t );
				b.setAttribute( 'data-time', t );
				slotsBox.appendChild( b );
			} );
			show( step2 );
		}

		function pickTime( t ) {
			selected.time = t;
			form.elements.date.value = selected.date;
			form.elements.time.value = t;
			$( '.lyfd-book__chosen', form ).textContent = fmtDate( selected.date ) + ' · ' + fmtTime( t );
			var st = $( '.lyfd-form__status[aria-live]', form );
			if ( st ) { st.hidden = true; }
			show( form );
		}

		grid.addEventListener( 'click', function ( e ) {
			var b = e.target.closest( '.lyfd-cal__day.is-open' );
			if ( b ) { pickDate( b.getAttribute( 'data-date' ) ); }
		} );
		slotsBox.addEventListener( 'click', function ( e ) {
			var b = e.target.closest( '.lyfd-slot' );
			if ( b ) { pickTime( b.getAttribute( 'data-time' ) ); }
		} );
		[ prev, next ].forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				view.m += parseInt( btn.getAttribute( 'data-nav' ), 10 );
				if ( view.m > 12 ) { view.m = 1; view.y++; }
				if ( view.m < 1 ) { view.m = 12; view.y--; }
				autoSkipped = 99;
				refresh();
			} );
		} );
		$( '[data-back]', form ).addEventListener( 'click', function () { show( step2 ); } );
		$( '[data-restart]', done ).addEventListener( 'click', function () {
			selected = null;
			cache = {};
			form.reset();
			show( step1 );
			refresh();
		} );

		enhanceForm( form, function ( res ) {
			if ( res.ok ) {
				$( '.lyfd-book__done-msg', done ).textContent = res.message;
				cache = {};
				show( done );
				refresh();
			} else {
				showStatus( form, false, res.message );
				if ( res.refresh ) {
					cache = {};
					refresh().then( function () { show( step1 ); selected = null; } );
				}
			}
		} );

		refresh();
	} );
} )();
