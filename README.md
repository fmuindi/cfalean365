# CFA LEAN365 — Homepage Redesign

A modern, Chick-fil-A-inspired homepage for **cfalean365.com** — clean layout,
smooth rounded edges, brand-red styling, subtle motion, fully responsive and
accessible. Built as a **self-contained file** so it drops into your existing
DIVI / WordPress site **without touching the LMS or theme code**.

> Logged-in users keep being redirected to the LMS by your existing snippet.
> This homepage only renders for logged-out visitors — nothing here changes that.

---

## Files

| File | What it is |
|------|-----------|
| `index.html` | The complete homepage — HTML + CSS + JS in one file (no build step, no external JS). Use for the Code-module / WPCode routes and for previewing. |
| `page-lean365.php` | WordPress **page template** version of the same page (generated from `index.html`). Use for the drop-in-file route. Calls `wp_head()`/`wp_footer()` so plugins — including the Chick-fil-A address-autocomplete snippet — load. |
| `cfa-forms-snippet.php` | **Recommended** form handler — paste into WPCode/Code Snippets to expose `POST /wp-json/cfa/v1/submit`. No file upload, no keys. |
| `gf-proxy.php` | Alternative standalone form handler (upload to site root). Same behavior as the snippet. |
| `README.md` | This guide. |

**Fonts:** headings use **Apercu** (your Chick-fil-A brand font) with a Poppins/
system fallback; body uses Apercu → Calibri/Lato fallback. Font rules are marked
`!important` and scoped to `.cfa-page` so the host theme can't override them —
this is what makes the deployed page match the preview.

> **Note on this site's builder:** the "Chick-fil-A Builder / Theme Builder" is
> **Divi, white-labeled** by the *Chick-fil-A Ghoster* plugin. Wherever Divi docs
> say "Divi Builder", your admin says "Chick-fil-A Builder". The **Theme Builder**
> screen is for site-wide headers/footers — **not** where you add this page.

---

## Install — pick ONE route

### Route A · Divi "Code" module (all in the admin UI)
1. **Pages → Add New**, title it "Home", **Publish** once.
2. In Page Attributes / page settings choose the **Blank Page** template (hides the theme header/footer; this page has its own).
3. Click **"Use The Chick-fil-A Builder"** → **Build From Scratch**.
4. Add a **Regular** section → **single-column** row → **(+) → Code** module.
5. Paste **everything between `<body>` and `</body>`** from `index.html`. Save.
6. **Settings → Reading → homepage → A static page → Home**.

### Route B · Drop-in PHP template (most foolproof — no builder) ✅ recommended for this site
1. Upload **`page-lean365.php`** to your active theme (ideally a **child theme**):
   `wp-content/themes/<your-theme>/page-lean365.php` — via **Appearance → Theme File Editor** or SFTP.
2. Edit the **Home** page → **Page Attributes → Template → "LEAN365 Home"**.
3. **Settings → Reading → homepage → A static page → Home**.
   *(No `<body>` copy/paste — the template already contains everything.)*

### Route C · WPCode HTML shortcode (if the builder fights you)
1. **WPCode → Add Snippet → Custom Code → "HTML Snippet."**
2. Paste the `<body>` contents of `index.html`. Set insert method to **Shortcode**, Save/Activate.
3. Put the generated `[wpcode id="…"]` on a plain page; set that page as the homepage.
   *(WPCode keeps `<style>`/`<script>` intact; the plain block-editor "Custom HTML" box can mangle them — don't use that.)*

**All routes:** upload `gf-proxy.php` (see **Forms**) and keep the Chick-fil-A
autocomplete Code Snippet active. `page-lean365.php` is generated from
`index.html`, so edit `index.html` and regenerate if you change the page.

---

## ⚙️ Before you go live — 4 quick edits

Open `index.html` and find the **`CFA_CONFIG`** block near the bottom (in the `<script>`):

```js
window.CFA_CONFIG = {
  loginUrl:     "/wp-login.php",   // ← set to your real LMS/login URL
  formEndpoint: "/gf-proxy.php"    // ← path where you uploaded gf-proxy.php
};
```

1. **`loginUrl`** — every "Login / Member Login / Learning Center" link uses this. Point it at your actual member login page.
2. **`formEndpoint`** — where the forms POST. Match wherever you place `gf-proxy.php`.
3. **Logo** — the header uses your hosted logo URL; if it moves, update the `<img src>` in the header (a text logo shows automatically if the image fails).
4. **Images** — search the file for **`IMAGE SLOT`**. Replace each decorative placeholder `<div class="placeholder">…</div>` with a real `<img src="…" alt="…">`. Suggested spots: hero photo, learning-center screenshot.

---

## Forms — "Sorry, something went wrong" means the endpoint isn't live yet

The page posts to `window.CFA_CONFIG.formEndpoint`. If that URL doesn't exist,
you get *"Sorry, something went wrong."* Pick ONE handler:

### Option 1 · WordPress REST snippet — RECOMMENDED (no file upload) ✅
1. **WPCode → Add Snippet → Add Your Custom Code → "PHP Snippet."**
2. Paste the contents of **`cfa-forms-snippet.php`**, set it to **Run Everywhere**, Save & Activate.
3. It registers **`POST /wp-json/cfa/v1/submit`** — which is the default
   `formEndpoint` already set in the page. Nothing else to change.
   *(Best on WP Engine — runs inside WordPress, no loose PHP file, no caching quirks.)*

### Option 2 · Upload `gf-proxy.php` (if you prefer a file)
- **Where:** your site's **web root** — the folder that contains `wp-config.php`
  / `wp-load.php`. On **WP Engine**: *User Portal → your site → SFTP/Git → connect
  via SFTP → drop `gf-proxy.php` in the root* (same level as `wp-config.php`).
- Then set `formEndpoint` to **`/gf-proxy.php`** in the page's `CFA_CONFIG`.

Both handlers do the same thing: run GravityForms **server-side** via
`GFAPI::submit_form()` (full validation, notifications, confirmations), resolve
the Chick-fil-A store address from the Google `place_id` by reusing your own
`cfa_fetch_place_details()`, and need **no API keys in the page**. Field IDs are
already mapped (Interest 36: `3/4/23/34/32`; Support 37: `1/2/4/3`).

**Requirement either way:** keep your "Chick-fil-A Autocomplete" Code Snippet
active site-wide (it powers the store-address field).

---

## 🔐 Security — please read

- **Rotate the REST keys you shared in chat.** They were transmitted in plain
  text, so treat them as compromised: WooCommerce → Advanced → REST API (or
  Forms → Settings → REST API) → revoke and regenerate.
- **Never put the keys in `index.html` or any client-side JavaScript** — anyone
  could view-source and steal them. That's the whole reason for the PHP proxy.
- If you use Mode B, store the new keys as server **environment variables**
  (`GF_CONSUMER_KEY`, `GF_CONSUMER_SECRET`) or in `wp-config.php`, not in the file.
- Prefer **Mode A** — it needs no keys at all.

---

## Design notes

- **Colors:** Chick-fil-A signature red (`#E51636`) as primary, warm cream
  neutrals, deep-navy footer. Tokens live in the `:root` block — easy to retune.
- **Fonts:** Aptos Display and Calibri are **proprietary Microsoft fonts that
  can't be served on the open web.** The page lists them *first* in the font
  stack (so any visitor who has them locally gets them) and falls back to close,
  licensed Google Fonts — **Poppins** (headings) and **Lato** (body). If you have
  licensed `.woff2` files for Aptos/Calibri, self-host them and add `@font-face`
  rules; the stack will pick them up with no other changes.
- **Motion:** gentle scroll-reveal + count-up stats, all disabled automatically
  under `prefers-reduced-motion`.
- **Accessibility:** semantic landmarks, visible focus rings, labelled fields,
  44px+ touch targets, AA contrast, keyboard-friendly mobile menu.
- **Responsive:** tuned for 375 / 768 / 1024 / 1440px.

---

## Content — what's real vs. what to add

The copy now reflects **your real content**, drawn from the current site and the
Lean Leader Program deck:

- Hero, ENGAGE / RETAIN / REWARD (70% retention, Point University college credit)
- The 3-phase / 20-week program (Develop Self → Develop Others → Lean Culture)
- "How the Pilot Works" (6 steps; July 2026 kickoff; 100-store Jan 2027 cohort)
- "What's Included" (6 items) and the "Need Help? Our Pleasure!" support copy

Internal-only material from the deck (risks, open questions, legal/onboarding
steps) was **deliberately left off** the public page.

**Now wired to live data:**

- **Images** — all `src`s point at your `cfalean365.com` media URLs (hero, the
  Engage/Retain/Reward cards, and the LMS screenshot). Any image that fails to
  load falls back to a branded block. If a `LEAN365-Intro-Image.0xx.png` is a
  slide/graphic that looks cropped, tell me and I'll switch its fit or swap it.
- **Video** — your live Vimeo embed (`video/1161914882`).
- **Login** — `https://cfalean365.com/wp-login.php`.
- **Form field IDs** — mapped in `gf-proxy.php`: Interest (36) Name `3`, Email `4`,
  Phone `23`, Message `32`; Support (37) Name `1`, Email `2`, Subject `4`, Message `3`.

**Chick-fil-A store-address autocomplete — wired, no keys in the page:**

Field `34` on the Interest form is a GF Address field driven by your existing
"Chick-fil-A Autocomplete for Gravity Forms" Code Snippet. Because that snippet
enqueues its JS site-wide and binds to any `.cfa-autocomplete` element, the
custom input on this page uses the **same markup** (`.cfa-input-wrapper`,
`input.cfa-autocomplete`, `ul.cfa-suggestions`, hidden `.cfa-place-id`) so the
snippet's own autocomplete + nonce'd AJAX bind to it automatically — **no Google
key lives in this page.** On submit, `gf-proxy.php` reuses the snippet's own
`cfa_fetch_place_details()` to resolve the `place_id` into the address sub-inputs
(`34.1` street / `34.3` city / `34.4` state / `34.5` zip), so **no key lives in
the proxy either.** The user must still pick a store from the suggestions
(validated client- and server-side), matching the native form's behavior.

Requirement: the Code Snippet must stay active site-wide so its JS loads on the
homepage. (It already is.)

**Still open:**

- Rotate the GravityForms REST keys shared earlier (the proxy prefers Mode A and
  needs no keys; rotate them anyway since they were shared in chat).
- The Google browser/server keys stay in your Code Snippet — keep them there,
  never in this page.

## Design language

Deliberately built around real brand cues rather than generic templates:
the **dotted-line motif** from the Chick-fil-A | LEAN365 logo (section rails,
step connectors, decorative dot fields), the **stopwatch / continuous-loop**
idea from the LEAN365 mark, an editorial **photo-collage hero**, a real
**20-week timeline**, a moving highlight marquee, and an "Our Pleasure" tone.
