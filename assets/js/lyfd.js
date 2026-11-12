/**
 * LYFD Luxe – front-end motion.
 * 3D scroll reveals, split-word headlines, hover tilt with spotlight, scroll-linked
 * depth, parallax hero, magnetic buttons, product filter. No dependencies.
 */
( function () {
	'use strict';

	var doc = document;
	var root = doc.documentElement;
	var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var finePointer = window.matchMedia && window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches;

	function $$( sel, ctx ) { return Array.prototype.slice.call( ( ctx || doc ).querySelectorAll( sel ) ); }
	function clamp( v, a, b ) { return Math.min( b, Math.max( a, v ) ); }

	/* ---------- split headlines into words (3D flip-up) ---------- */
	function splitNode( node, counter ) {
		Array.prototype.slice.call( node.childNodes ).forEach( function ( child ) {
			if ( child.nodeType === 3 ) {
				var parts = child.textContent.split( /(\s+)/ );
				var frag = doc.createDocumentFragment();
				parts.forEach( function ( part ) {
					if ( ! part ) { return; }
					if ( /^\s+$/.test( part ) ) {
						frag.appendChild( doc.createTextNode( ' ' ) );
						return;
					}
					var w = doc.createElement( 'span' );
					w.className = 'lyfd-w';
					w.setAttribute( 'aria-hidden', 'true' );
					var inner = doc.createElement( 'span' );
					inner.className = 'lyfd-w__in';
					inner.style.setProperty( '--i', counter.n++ );
					inner.textContent = part;
					w.appendChild( inner );
					frag.appendChild( w );
				} );
				node.replaceChild( frag, child );
			} else if ( child.nodeType === 1 && child.tagName !== 'BR' ) {
				splitNode( child, counter );
			}
		} );
	}

	function initSplit() {
		$$( '.lyfd-split' ).forEach( function ( el ) {
			if ( el.dataset.split ) { return; }
			el.dataset.split = '1';
			var label = el.textContent.trim();
			el.setAttribute( 'aria-label', label );
			splitNode( el, { n: 0 } );
		} );
	}

	/* ---------- scroll reveal + stagger ---------- */
	function initReveal() {
		$$( '.lyfd-stagger' ).forEach( function ( group ) {
			$$( '.lyfd-reveal', group ).forEach( function ( el, i ) {
				el.style.setProperty( '--lyfd-d', ( i % 6 ) * 0.09 + 's' );
			} );
		} );

		var targets = $$( '.lyfd-reveal, .lyfd-split' );
		if ( reduce || ! ( 'IntersectionObserver' in window ) ) {
			targets.forEach( function ( el ) { el.classList.add( 'is-inview', 'is-done' ); } );
			return;
		}

		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) { return; }
				var el = entry.target;
				el.classList.add( 'is-inview' );
				io.unobserve( el );
				if ( el.classList.contains( 'lyfd-reveal' ) ) {
					// once the entrance finished, switch to the fast transition used by tilt
					var delay = ( parseFloat( el.style.getPropertyValue( '--lyfd-d' ) ) || 0 ) * 1000 + 1300;
					setTimeout( function () { el.classList.add( 'is-done' ); }, delay );
				}
			} );
		}, { threshold: 0.14, rootMargin: '0px 0px -6% 0px' } );

		targets.forEach( function ( el ) { io.observe( el ); } );
	}

	/* ---------- hover tilt with moving spotlight ---------- */
	function initTilt() {
		if ( ! finePointer || reduce ) { return; }
		$$( '.lyfd-tilt' ).forEach( function ( el ) {
			var max = el.classList.contains( 'lyfd-cta-panel' ) ? 5 : 8;
			var raf = null;
			function move( e ) {
				var r = el.getBoundingClientRect();
				var px = ( e.clientX - r.left ) / r.width;
				var py = ( e.clientY - r.top ) / r.height;
				if ( raf ) { cancelAnimationFrame( raf ); }
				raf = requestAnimationFrame( function () {
					el.style.setProperty( '--ry', ( ( px - 0.5 ) * 2 * max ).toFixed( 2 ) + 'deg' );
					el.style.setProperty( '--rx', ( ( 0.5 - py ) * 2 * max ).toFixed( 2 ) + 'deg' );
					el.style.setProperty( '--mx', ( px * 100 ).toFixed( 1 ) + '%' );
					el.style.setProperty( '--my', ( py * 100 ).toFixed( 1 ) + '%' );
				} );
			}
			function leave() {
				if ( raf ) { cancelAnimationFrame( raf ); }
				el.style.setProperty( '--rx', '0deg' );
				el.style.setProperty( '--ry', '0deg' );
			}
			el.addEventListener( 'pointermove', move );
			el.addEventListener( 'pointerleave', leave );
		} );
	}

	/* ---------- magnetic buttons ---------- */
	function initMagnetic() {
		if ( ! finePointer || reduce ) { return; }
		$$( '.wp-block-button__link' ).forEach( function ( btn ) {
			btn.addEventListener( 'pointermove', function ( e ) {
				var r = btn.getBoundingClientRect();
				btn.style.setProperty( '--bx', ( ( e.clientX - r.left - r.width / 2 ) * 0.14 ).toFixed( 1 ) + 'px' );
				btn.style.setProperty( '--by', ( ( e.clientY - r.top - r.height / 2 ) * 0.22 ).toFixed( 1 ) + 'px' );
			} );
			btn.addEventListener( 'pointerleave', function () {
				btn.style.setProperty( '--bx', '0px' );
				btn.style.setProperty( '--by', '0px' );
			} );
		} );
	}

	/* ---------- hero scene: mouse-driven 3D + weld sparks ---------- */
	function initScene() {
		var scene = doc.querySelector( '.lyfd-scene' );
		if ( ! scene ) { return; }
		var stage = scene.querySelector( '.lyfd-scene__stage' );
		var sparks = scene.querySelector( '.lyfd-sparks' );

		if ( sparks && ! reduce ) {
			for ( var i = 0; i < 16; i++ ) {
				var s = doc.createElement( 'i' );
				var ang = Math.random() * Math.PI * 2;
				var dist = 60 + Math.random() * 150;
				s.style.setProperty( '--dx', Math.cos( ang ) * dist + 'px' );
				s.style.setProperty( '--dy', Math.sin( ang ) * dist * 0.8 - 20 + 'px' );
				s.style.setProperty( '--t', ( 1.4 + Math.random() * 1.6 ).toFixed( 2 ) + 's' );
				s.style.setProperty( '--d', ( Math.random() * 3 ).toFixed( 2 ) + 's' );
				sparks.appendChild( s );
			}
		}

		if ( ! finePointer || reduce || ! stage ) { return; }
		var hero = scene.closest( '.lyfd-hero' ) || scene;
		hero.addEventListener( 'pointermove', function ( e ) {
			var r = hero.getBoundingClientRect();
			var nx = ( e.clientX - r.left ) / r.width - 0.5;
			var ny = ( e.clientY - r.top ) / r.height - 0.5;
			stage.style.setProperty( '--sry', ( nx * 22 ).toFixed( 2 ) + 'deg' );
			stage.style.setProperty( '--srx', ( -ny * 18 ).toFixed( 2 ) + 'deg' );
		} );
		hero.addEventListener( 'pointerleave', function () {
			stage.style.setProperty( '--sry', '0deg' );
			stage.style.setProperty( '--srx', '0deg' );
		} );
	}

	/* ---------- scroll-linked effects (single rAF loop) ---------- */
	function initScroll() {
		var header = doc.querySelector( '.lyfd-header' );
		var heroBgs = $$( '.lyfd-hero, .lyfd-page-hero' );
		var slabs = $$( '.lyfd-scroll3d' );

		var bar = doc.createElement( 'div' );
		bar.className = 'lyfd-progress';
		bar.setAttribute( 'aria-hidden', 'true' );
		doc.body.appendChild( bar );

		var ticking = false;
		function update() {
			ticking = false;
			var y = window.pageYOffset || root.scrollTop;
			var vh = window.innerHeight;
			var max = root.scrollHeight - vh;

			if ( header ) { header.classList.toggle( 'is-scrolled', y > 24 ); }
			bar.style.setProperty( '--sp', max > 0 ? clamp( y / max, 0, 1 ).toFixed( 4 ) : 0 );

			if ( ! reduce ) {
				heroBgs.forEach( function ( h ) {
					var r = h.getBoundingClientRect();
					if ( r.bottom < 0 || r.top > vh ) { return; }
					h.style.setProperty( '--py', ( -r.top * 0.22 ).toFixed( 1 ) + 'px' );
				} );

				slabs.forEach( function ( el ) {
					var r = el.getBoundingClientRect();
					if ( r.bottom < -100 || r.top > vh + 100 ) { return; }
					var p = clamp( ( r.top + r.height / 2 - vh / 2 ) / ( vh / 2 + r.height / 2 ), -1, 1 );
					el.style.setProperty( '--p', p.toFixed( 3 ) );
					el.style.setProperty( '--ap', Math.abs( p ).toFixed( 3 ) );
				} );
			}
		}
		function req() { if ( ! ticking ) { ticking = true; requestAnimationFrame( update ); } }
		window.addEventListener( 'scroll', req, { passive: true } );
		window.addEventListener( 'resize', req );
		update();
	}

	/* ---------- product filter ---------- */
	function initFilter() {
		var bar = doc.querySelector( '.lyfd-filters' );
		var grid = doc.querySelector( '.lyfd-gallery' );
		if ( ! bar || ! grid ) { return; }
		var items = $$( '.lyfd-product', grid );

		bar.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( '.lyfd-filter' );
			if ( ! btn ) { return; }
			var key = btn.getAttribute( 'data-filter' );
			$$( '.lyfd-filter', bar ).forEach( function ( b ) { b.classList.toggle( 'is-active', b === btn ); } );
			grid.classList.toggle( 'is-filtered', key !== 'all' );

			var n = 0;
			items.forEach( function ( item ) {
				var show = key === 'all' || item.classList.contains( 'lyfd-cat-' + key );
				item.classList.remove( 'is-shown' );
				item.classList.toggle( 'is-hidden', ! show );
				if ( show ) {
					// make sure items revealed by the filter are visible even if never scrolled to
					item.classList.add( 'is-inview', 'is-done' );
					void item.offsetWidth; // restart animation
					item.style.setProperty( '--lyfd-d', ( n++ * 0.07 ).toFixed( 2 ) + 's' );
					item.classList.add( 'is-shown' );
				}
			} );
		} );
	}


	/* ---------- Facebook reels: load the player only after a click ---------- */
	function initReels() {
		$$( '.lyfd-reel' ).forEach( function ( box ) {
			var btn = box.querySelector( '.lyfd-reel__btn' );
			if ( ! btn ) { return; }
			btn.addEventListener( 'click', function () {
				var src = box.getAttribute( 'data-src' );
				if ( ! src ) { return; }
				var w = Math.round( box.getBoundingClientRect().width ) || 270;
				var iframe = doc.createElement( 'iframe' );
				iframe.src = 'https://www.facebook.com/plugins/video.php?href=' + encodeURIComponent( src ) +
					'&show_text=false&autoplay=true&width=' + w;
				iframe.title = 'LYFD Facebook reel';
				iframe.allow = 'autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share';
				iframe.setAttribute( 'allowfullscreen', 'true' );
				iframe.setAttribute( 'scrolling', 'no' );
				box.appendChild( iframe );
				btn.remove();
				var card = box.closest( '.lyfd-tilt' );
				if ( card ) {   // a moving 3D card would fight the video player
					card.classList.remove( 'lyfd-tilt' );
					card.style.setProperty( '--rx', '0deg' );
					card.style.setProperty( '--ry', '0deg' );
				}
			} );
		} );
	}

	/* ---------- welcome popup: at most once every 24 hours ---------- */
	function initPromo() {
		var el = doc.getElementById( 'lyfd-promo' );
		if ( ! el ) { return; }
		var KEY = 'lyfd_promo_ts';
		var DAY = 24 * 60 * 60 * 1000;

		function lastSeen() {
			try { return parseInt( window.localStorage.getItem( KEY ), 10 ) || 0; } catch ( e ) {}
			var m = doc.cookie.match( /(?:^|; )lyfd_promo_ts=(\d+)/ );
			return m ? parseInt( m[ 1 ], 10 ) : 0;
		}
		function markSeen() {
			var now = Date.now();
			try { window.localStorage.setItem( KEY, String( now ) ); } catch ( e ) {}
			doc.cookie = 'lyfd_promo_ts=' + now + '; max-age=86400; path=/; SameSite=Lax';
		}
		if ( Date.now() - lastSeen() < DAY ) { return; }
		if ( /[?&]lyfd=/.test( window.location.search ) ) { return; } // just submitted a form

		var opener = null, shown = false, timer = null;
		var closeBtn = el.querySelector( '.lyfd-promo__close' );

		function focusables() {
			return $$( 'a[href], button:not([disabled])', el ).filter( function ( n ) { return n.offsetParent !== null; } );
		}
		function open() {
			if ( shown ) { return; }
			var a = doc.activeElement;
			if ( a && a.closest && a.closest( 'form' ) && a.matches( 'input, textarea, select' ) ) {
				timer = setTimeout( open, 12000 ); // don't interrupt someone who is typing
				return;
			}
			shown = true;
			opener = a;
			markSeen();
			el.setAttribute( 'aria-hidden', 'false' );
			el.classList.add( 'is-open' );
			root.style.overflow = 'hidden';
			setTimeout( function () { closeBtn.focus(); }, 60 );
			doc.addEventListener( 'keydown', onKey );
		}
		function close() {
			el.classList.remove( 'is-open' );
			el.setAttribute( 'aria-hidden', 'true' );
			root.style.overflow = '';
			doc.removeEventListener( 'keydown', onKey );
			if ( opener && opener.focus ) { opener.focus(); }
		}
		function onKey( e ) {
			if ( e.key === 'Escape' ) { close(); return; }
			if ( e.key !== 'Tab' ) { return; }
			var f = focusables();
			if ( ! f.length ) { return; }
			var first = f[ 0 ], last = f[ f.length - 1 ];
			if ( e.shiftKey && doc.activeElement === first ) { e.preventDefault(); last.focus(); }
			else if ( ! e.shiftKey && doc.activeElement === last ) { e.preventDefault(); first.focus(); }
		}
		$$( '[data-promo-close]', el ).forEach( function ( b ) { b.addEventListener( 'click', close ); } );

		// open after 8 s, or earlier once the visitor has scrolled a good way down
		timer = setTimeout( open, 8000 );
		function onScroll() {
			var max = root.scrollHeight - window.innerHeight;
			if ( max > 0 && ( window.pageYOffset || root.scrollTop ) / max > 0.35 ) {
				window.removeEventListener( 'scroll', onScroll );
				clearTimeout( timer );
				open();
			}
		}
		window.addEventListener( 'scroll', onScroll, { passive: true } );
	}


	/* ---------- mobile menu: inject brand + quick-contact block when it opens ---------- */
	function initMenu() {
		var tpl = doc.getElementById( 'lyfd-menu-extra' );
		var overlay = doc.querySelector( '.lyfd-nav .wp-block-navigation__responsive-container' );
		if ( ! tpl || ! overlay || ! ( 'MutationObserver' in window ) ) { return; }

		function fill() {
			if ( ! overlay.classList.contains( 'is-menu-open' ) || overlay.querySelector( '.lyfd-menu-extra' ) ) { return; }
			var frag = tpl.content.cloneNode( true );
			var brand = frag.querySelector( '.lyfd-menu-brand' );
			var extra = frag.querySelector( '.lyfd-menu-extra' );
			var dialog = overlay.querySelector( '.wp-block-navigation__responsive-dialog' );
			var content = overlay.querySelector( '.wp-block-navigation__responsive-container-content' );
			if ( dialog && brand ) { dialog.insertBefore( brand, dialog.firstChild ); }
			if ( content && extra ) { content.appendChild( extra ); }
		}
		new MutationObserver( fill ).observe( overlay, { attributes: true, attributeFilter: [ 'class' ] } );

		overlay.addEventListener( 'click', function ( e ) {
			var a = e.target.closest( '.lyfd-menu-extra a[href*="#"]' );
			if ( a ) {   // same-page anchor: close the menu so the section is visible
				var close = overlay.querySelector( '.wp-block-navigation__responsive-container-close' );
				if ( close ) { close.click(); }
			}
		} );
	}


	/* ---------- slider: 3D coverflow, swipe/drag, arrows, dots, autoplay ---------- */
	function initSliders() {
		$$( '.lyfd-slider' ).forEach( function ( root ) {
			var track = root.querySelector( '.lyfd-slider__track' );
			var slides = $$( '.lyfd-slide', track );
			var n = slides.length;
			if ( ! track || n < 2 ) { return; }

			root.classList.add( 'is-enhanced' );
			var AUTOPLAY = 5500;
			root.style.setProperty( '--lyfd-slide-ms', AUTOPLAY + 'ms' );

			slides.forEach( function ( s ) {      // wrap content so only the inner box is transformed
				var inner = doc.createElement( 'div' );
				inner.className = 'lyfd-slide__inner';
				while ( s.firstChild ) { inner.appendChild( s.firstChild ); }
				s.appendChild( inner );
			} );

			track.setAttribute( 'tabindex', '0' );
			track.setAttribute( 'role', 'region' );
			track.setAttribute( 'aria-roledescription', 'carousel' );
			track.setAttribute( 'aria-label', 'LYFD project photos' );
			slides.forEach( function ( s, i ) {
				s.setAttribute( 'role', 'group' );
				s.setAttribute( 'aria-roledescription', 'slide' );
				s.setAttribute( 'aria-label', ( i + 1 ) + ' of ' + n );
			} );

			/* controls */
			var ui = doc.createElement( 'div' );
			ui.className = 'lyfd-slider__ui';
			ui.innerHTML = '<div class="lyfd-slider__dots" role="tablist" aria-label="Choose photo"></div>' +
				'<span class="lyfd-slider__count" aria-hidden="true"><b>01</b> / ' + ( n < 10 ? '0' : '' ) + n + '</span>' +
				'<button type="button" class="lyfd-slider__btn" data-dir="-1" aria-label="Previous photo">&lsaquo;</button>' +
				'<button type="button" class="lyfd-slider__btn" data-dir="1" aria-label="Next photo">&rsaquo;</button>';
			root.appendChild( ui );
			var prog = doc.createElement( 'div' );
			prog.className = 'lyfd-slider__progress';
			prog.innerHTML = '<i></i>';
			root.appendChild( prog );

			var dotsBox = ui.querySelector( '.lyfd-slider__dots' );
			var count = ui.querySelector( '.lyfd-slider__count b' );
			var dots = slides.map( function ( s, i ) {
				var d = doc.createElement( 'button' );
				d.type = 'button';
				d.className = 'lyfd-slider__dot';
				d.setAttribute( 'role', 'tab' );
				d.setAttribute( 'aria-label', 'Show photo ' + ( i + 1 ) );
				d.addEventListener( 'click', function () { go( i ); restart(); } );
				dotsBox.appendChild( d );
				return d;
			} );

			var index = 0, raf = null, timer = null, paused = false, visible = false;

			function width() { return track.clientWidth || 1; }
			function go( i, instant ) {
				index = ( i + n ) % n;
				track.scrollTo( { left: index * width(), behavior: ( instant || reduce ) ? 'auto' : 'smooth' } );
			}
			function paint() {
				raf = null;
				var w = width(), x = track.scrollLeft;
				var cur = Math.round( x / w );
				if ( cur !== index || ! dots[ cur ].hasAttribute( 'aria-current' ) ) {
					index = clamp( cur, 0, n - 1 );
					dots.forEach( function ( d, i ) {
						d.setAttribute( 'aria-current', i === index ? 'true' : 'false' );
						d.setAttribute( 'aria-selected', i === index ? 'true' : 'false' );
					} );
					count.textContent = ( index < 9 ? '0' : '' ) + ( index + 1 );
				}
				if ( reduce ) { return; }
				slides.forEach( function ( s, i ) {
					var o = clamp( ( i * w - x ) / w, -1.2, 1.2 );
					s.style.setProperty( '--o', o.toFixed( 3 ) );
					s.style.setProperty( '--ao', Math.abs( o ).toFixed( 3 ) );
				} );
			}
			function queue() { if ( ! raf ) { raf = requestAnimationFrame( paint ); } }
			track.addEventListener( 'scroll', queue, { passive: true } );
			window.addEventListener( 'resize', function () { go( index, true ); queue(); } );

			/* buttons + keyboard */
			ui.addEventListener( 'click', function ( e ) {
				var b = e.target.closest( '.lyfd-slider__btn' );
				if ( b ) { go( index + parseInt( b.getAttribute( 'data-dir' ), 10 ) ); restart(); }
			} );
			track.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'ArrowRight' ) { e.preventDefault(); go( index + 1 ); restart(); }
				if ( e.key === 'ArrowLeft' ) { e.preventDefault(); go( index - 1 ); restart(); }
			} );

			/* mouse drag (touch already swipes natively) */
			var drag = null;
			track.addEventListener( 'pointerdown', function ( e ) {
				if ( e.pointerType !== 'mouse' || e.button !== 0 ) { return; }
				drag = { x: e.clientX, dx: 0, left: track.scrollLeft, start: index, moved: false };
			} );
			window.addEventListener( 'pointermove', function ( e ) {
				if ( ! drag ) { return; }
				var dx = e.clientX - drag.x;
				drag.dx = dx;
				if ( Math.abs( dx ) > 4 ) { drag.moved = true; track.classList.add( 'is-dragging' ); }
				if ( drag.moved ) { track.scrollLeft = drag.left - dx; }
			} );
			window.addEventListener( 'pointerup', function () {
				if ( ! drag ) { return; }
				var moved = drag.moved;
				var dragStart = drag.start;
				var dragDx = drag.dx;
				drag = null;
				if ( moved ) {
					var w = width();
					var target = Math.abs( dragDx ) > w * 0.15 ? clamp( dragStart + ( dragDx < 0 ? 1 : -1 ), 0, n - 1 ) : dragStart;
					track.classList.remove( 'is-dragging' );
					go( target );
					restart();
				}
			} );

			/* autoplay: pauses on hover/focus/drag, when off-screen or tab hidden, never with reduced motion */
			function stop() { clearTimeout( timer ); root.classList.remove( 'is-playing' ); }
			function schedule() {
				stop();
				if ( reduce || paused || ! visible || doc.hidden ) { return; }
				void prog.offsetWidth;                 // restart the CSS progress bar
				root.classList.add( 'is-playing' );
				timer = setTimeout( function () { go( index + 1 ); schedule(); }, AUTOPLAY );
			}
			function restart() { schedule(); }
			root.addEventListener( 'mouseenter', function () { paused = true; stop(); } );
			root.addEventListener( 'mouseleave', function () { paused = false; schedule(); } );
			root.addEventListener( 'focusin', function () { paused = true; stop(); } );
			root.addEventListener( 'focusout', function () { paused = false; schedule(); } );
			doc.addEventListener( 'visibilitychange', schedule );
			if ( 'IntersectionObserver' in window ) {
				new IntersectionObserver( function ( en ) { visible = en[ 0 ].isIntersecting; schedule(); }, { threshold: 0.35 } ).observe( root );
			} else { visible = true; schedule(); }

			paint();
		} );
	}

	/* ---------- boot ---------- */
	function boot() {
		initSplit();
		initReveal();
		initTilt();
		initMagnetic();
		initScene();
		initScroll();
		initFilter();
		initReels();
		initPromo();
		initMenu();
		initSliders();
		root.classList.add( 'lyfd-ready' );
	}

	if ( doc.readyState === 'loading' ) {
		doc.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
