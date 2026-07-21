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
| `index.html` | The complete homepage — HTML + CSS + JS in one file (no build step, no external JS). |
| `gf-proxy.php` | Secure server-side handler for the two GravityForms. Keys stay on the server. |
| `README.md` | This guide. |

---

## Install (DIVI — recommended, no theme edits)

1. In WordPress, create a new **blank page** (e.g. "Home v2"). Use a blank/full-width DIVI template.
2. Add a single **Code** module.
3. Open `index.html`, copy **everything between `<body>` and `</body>`** (the `<style>`, the markup, and the `<script>`), and paste it into the Code module.
   - The `<link>` for Google Fonts can go in the DIVI page's *Integration → head* box, or leave the fonts to fall back automatically.
4. Save, then set this page as your homepage under **Settings → Reading → Your homepage displays → A static page**.
5. Upload `gf-proxy.php` (see **Forms** below).

*(Alternative: paste the whole `index.html` into a blank PHP page template. Either works — the file is standalone.)*

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

## Forms (Support #37 + Join Pilot Interest #36)

The forms submit to `gf-proxy.php`, which talks to GravityForms **server-side**
so your API keys are never in the browser. It works two ways and auto-detects:

- **Mode A (recommended, no keys):** place `gf-proxy.php` where it can reach
  `wp-load.php` (your WordPress root). It calls `GFAPI::submit_form()` directly —
  full validation, notifications and confirmations, **no consumer key/secret needed.**
- **Mode B (fallback):** if WordPress can't be loaded, it uses the GF REST API v2
  with keys read from environment variables.

### You must verify the field-ID map

Open `gf-proxy.php` → the **`$FIELD_MAPS`** block. Map each field name to the real
GravityForms field ID for forms 36 and 37. Find IDs in the GF form editor (click a
field → the Field ID shows on the right), or by fetching
`/wp-json/gf/v2/forms/36` and `/wp-json/gf/v2/forms/37`. I used sensible
placeholders (e.g. `1.3`/`1.6` for a Name field's first/last) — **these are
guesses and almost certainly need adjusting.**

Also set **`ALLOWED_ORIGINS`** to your domain(s) at the top of the file.

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

**Still to add before launch:**

- **Photos** — add 5 team photos to `assets/` (see `assets/README.md`) or point
  the `src` paths at your media library. The LMS screenshot is already wired to
  its live URL. Missing photos fall back to a branded block automatically.
- **Video** — search `VIDEO EMBED` and paste your YouTube/Vimeo `<iframe>`.
- **Login URL** — set `loginUrl` in `CFA_CONFIG`.
- **Form field IDs** — verify `$FIELD_MAPS` in `gf-proxy.php` against forms 36 & 37.
- Double-check stats and the Point University / cohort details are current.

## Design language

Deliberately built around real brand cues rather than generic templates:
the **dotted-line motif** from the Chick-fil-A | LEAN365 logo (section rails,
step connectors, decorative dot fields), the **stopwatch / continuous-loop**
idea from the LEAN365 mark, an editorial **photo-collage hero**, a real
**20-week timeline**, a moving highlight marquee, and an "Our Pleasure" tone.
