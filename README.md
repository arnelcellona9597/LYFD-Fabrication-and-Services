# LYFD Luxe — child theme of Twenty Twenty-Five

Gutenberg (block / Site Editor) theme for **LYFD Stainless Fabrication and Services**, Davao City.
**Two pages only**

| Page | Contents |
|---|---|
| **Home** | Hero · services ribbon · About / company profile · capabilities · approach · featured work · **Book a Visit calendar** · **Contact (details + form)** |
| **Products** | Filterable photo gallery with lightbox · five Facebook reels (click-to-play) |

Plus, on every page: a floating **Messenger** button (bottom right, opens `m.me/61595745041102` in a new tab). On the homepage a **welcome popup** points visitors to Products — shown at most once every 24 hours per visitor.

## Install
1. Parent theme **Twenty Twenty-Five** must be installed (bundled with WordPress 6.7+).
2. Put this folder in `wp-content/themes/lyfd-luxe/` (clone the repo there, or upload a zip) and activate **LYFD Luxe**.
3. On activation the theme creates the **Home** and **Products** pages (never overwriting edits), makes Home the front page, sets `/%postname%/` permalinks, sets the time zone to `Asia/Manila`, and removes WordPress' sample page / "Hello world" / draft privacy page so the site has exactly two pages.

## Email (contact form + bookings) — SMTP
Built into the theme (no plugin needed): **wp-admin → Settings → LYFD Email**.
It is pre-filled with the LYFD cPanel mailbox — host `mail.lyfdfab.com`, port `465`, SSL, user `info@lyfdfab.com`. **Type the mailbox password once and Save**, then press **Send test email**; if sending fails the mail server's error is shown there.

- Messages go to **info@lyfdfab.com** (changeable on the same page) and are also stored in wp-admin → **Inquiries** / **Bookings**, so nothing is lost if the mail server is down.
- The password is stored **encrypted** in the database, never shown again, and never in the repository. If the site moves to a server whose security keys (salts in `wp-config.php`) differ, enter it again.
- Until a password is saved, WordPress uses PHP `mail()` (on DDEV that lands in Mailpit).
- Incoming mail (IMAP 993 / POP3 995) is only for Outlook/phone apps — the website only sends.
- Optional `wp-config.php` overrides (win over the page): `LYFD_SMTP_PASS`, `LYFD_SMTP_HOST`, `LYFD_SMTP_PORT`, `LYFD_SMTP_USER`, `LYFD_MAIL_TO`.

## SEO (built in — no Yoast needed)
- **Per page:** the *SEO — Google & social sharing* box under the page editor (open “Meta Boxes” at the bottom of the block editor): SEO title, meta description with a live Google preview and character counter, share image, “hide from Google”.
- **Site-wide:** Settings → **LYFD SEO** — business name/phone/email/address/GPS (feeds Google's local-business data), Google Search Console & Bing verification codes, default share image.
- **Output:** optimised `<title>`, meta description, robots (search/404/attachments noindexed), canonical, Open Graph + Twitter cards (1200×630 `assets/images/og-image.jpg`), JSON-LD (LocalBusiness, WebSite, WebPage/CollectionPage, BreadcrumbList, 16-photo ImageGallery).
- **Sitemap:** `/wp-sitemap.xml` (pages only, with last-modified dates); `/sitemap.xml` redirects there. **robots.txt** lists the sitemap.
- Turns itself off automatically if Yoast, Rank Math, SEOPress or AIOSEO is activated.
- Go-live: untick *Discourage search engines* (Settings → Reading), verify in Google Search Console, submit the sitemap, set up a Google Business Profile.

## Booking calendar
**Bookings → Settings**: open days, opening/closing time, appointment length, visits per slot, minimum notice, how far ahead, closed dates. Default: Mon–Sat, 9:00–17:00, 1-hour slots — **adjust to the real shop hours**.
A request instantly blocks its slot, emails LYFD, and sends the visitor an acknowledgement. In **Bookings** set a request to *Confirmed* (visitor gets a confirmation email) or *Cancelled* (frees the slot).

## Editing content
Everything is native blocks: **Pages → Home / Products**; header & footer under **Appearance → Editor → Patterns**. Colours, fonts, spacing: **Editor → Styles** (`theme.json`).
- **Gallery** – replace an image with *Replace*. Category = Additional CSS class on the card: `lyfd-cat-kitchens | counters | cooking | blowers`. `is-wide` / `is-tall` make a card span 2 columns / rows.
- **Reels** – each reel is an HTML block: change the Facebook reel URL (`data-src`) and the poster image. The Facebook player only loads after a click (faster page, no tracking before).
- **Phone / email / address** – Home → Contact section and the footer (Template parts).
- **Messenger link** – `inc/floating.php` (or `define( 'LYFD_MESSENGER_URL', '…' )`).

## Typography
Matches the logo: **Saira** bold italic (wordmark-style headings) + **Barlow** (body, close to the tagline lettering). Self-hosted in `assets/fonts` (no Google requests).

## 3D effects (Additional CSS classes)
`lyfd-slider` (group > track > slides) · `lyfd-reveal` (+ `--left` / `--right`) · `lyfd-stagger` (on a parent) · `lyfd-split` (headings) · `lyfd-tilt` · `lyfd-scroll3d`. Group style **3D Card** / **Brushed Steel Panel**, Image style **Steel Frame (3D)**. Disabled automatically for *prefers-reduced-motion*; tilt only on mouse devices.

## Files
```
style.css  theme.json  functions.php
inc/       mail.php  mail-settings.php  seo.php  seo-admin.php  forms.php  booking.php  floating.php  setup.php
patterns/  header, footer, home-*, products-*
templates/ front-page.html  page-products.html     parts/  header.html  footer.html
assets/    css/lyfd.css  js/lyfd.js  js/forms.js  fonts/  images/   (≈2.5 MB total)
```
Images are WebP (≤1400 px) — keep new uploads similarly small; the host has 10 GB.
