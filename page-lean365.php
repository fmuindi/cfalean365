<?php
/**
 * Template Name: LEAN365 Home
 * ---------------------------------------------------------------------------
 * Standalone LEAN365 Essentials landing page (Chick-fil-A / Divi site).
 *
 * HOW TO USE:
 *   1. Upload this file to your ACTIVE theme (ideally a child theme):
 *      wp-content/themes/<your-theme>/page-lean365.php
 *   2. Pages > edit Home > Page Attributes > Template = "LEAN365 Home".
 *   3. Settings > Reading > A static page > Home.
 *
 * Ships its own header/footer (does NOT call the theme's), but DOES call
 * wp_head()/wp_footer() so plugins load — including the Chick-fil-A address
 * autocomplete snippet and GravityForms. The two forms are your real GF forms,
 * embedded via do_shortcode(), so all existing behavior works unchanged.
 *
 * Logged-in users are already redirected to the LMS by your snippet, so no
 * redirect is added here (avoids loops).
 * ---------------------------------------------------------------------------
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<?php wp_head(); ?>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800;900&family=Lato:wght@400;500;700&display=swap" rel="stylesheet" />

<style>
  /* ====================== TOKENS ====================== */
  :root{
    --red:#E4002B; --red-dark:#C10024; --red-soft:#FCE6EA;
    --navy:#0B4C5E; --navy-dark:#073744; --navy-soft:#E4EFF2;
    --gold:#F5B335;
    --ink:#191919; --ink-soft:#454545; --muted:#6E6E6E;
    --cream:#FBF7F1; --cream-2:#F4EDE3; --white:#fff; --line:#EBE2D6;
    --r-pill:999px; --r-xl:34px; --r-lg:26px; --r-md:18px; --r-sm:12px;
    --shadow-sm:0 2px 10px rgba(20,25,30,.07);
    --shadow-md:0 16px 38px rgba(20,25,30,.12);
    --shadow-lg:0 30px 70px rgba(20,25,30,.18);
    --font-head:"Apercu Pro","Apercu","Aptos Display",Poppins,"Segoe UI",system-ui,sans-serif;
    --font-body:"Apercu Pro","Apercu",Calibri,Carlito,Lato,"Segoe UI",system-ui,sans-serif;
    --maxw:1180px; --ease:cubic-bezier(.22,.61,.36,1);
    --dots:radial-gradient(currentColor 1.6px, transparent 1.7px);
  }
  /* Load Apercu from the SAME origin as the page so it isn't blocked by CORS
     (the theme references it on cfalean365.wpengine.com, which cross-origin-fails). */
  @font-face{ font-family:'Apercu Pro'; src:url('/wp-content/uploads/et-fonts/ApercuPro.ttf') format('truetype'); font-weight:400; font-style:normal; font-display:swap; }
  @font-face{ font-family:'Apercu';     src:url('/wp-content/uploads/et-fonts/ApercuPro.ttf') format('truetype'); font-weight:400; font-style:normal; font-display:swap; }
  *,*::before,*::after{ box-sizing:border-box; }
  html{ scroll-behavior:smooth; -webkit-text-size-adjust:100%; }
  @media (prefers-reduced-motion:reduce){ html{ scroll-behavior:auto; } }
  body.cfa-page{ margin:0; font-family:var(--font-body); color:var(--ink); background:var(--cream);
    font-size:clamp(16px,1.05vw,18px); line-height:1.65; -webkit-font-smoothing:antialiased; overflow-x:hidden; }
  .cfa-page h1,.cfa-page h2,.cfa-page h3,.cfa-page h4{ font-family:var(--font-head); font-weight:800; line-height:1.07; letter-spacing:-.02em; color:var(--ink); margin:0 0 .4em; }

  /* ---- THEME-PROOFING: keep our type/spacing inside .cfa-page no matter the host theme ---- */
  .cfa-page{ font-family:var(--font-body) !important; }
  .cfa-page h1,.cfa-page h2,.cfa-page h3,.cfa-page h4,
  .cfa-page .btn,.cfa-page .eyebrow,.cfa-page .cfa-logo .fallback,
  .cfa-page .cfa-navlinks a,.cfa-page .cfa-login,.cfa-page .cfa-mobile a,
  .cfa-page .kpi,.cfa-page .stat .n,.cfa-page .statc .n,.cfa-page .tl-weeks,
  .cfa-page .tl-node,.cfa-page .kick,.cfa-page .rcard .top .badge,
  .cfa-page .ppath .num,.cfa-page .ppath h4,.cfa-page .incl-list .t,
  .cfa-page .foot-logo-chip .fallback,.cfa-page .cfa-footer h4,
  .cfa-page .form-card h3,.cfa-page .field label,.cfa-page .hero-art .chip .t,
  .cfa-page .donut-center .dn,.cfa-page .donut-legend b,.cfa-page .why-list b,
  .cfa-page .pill-badge{ font-family:var(--font-head) !important; }
  .cfa-page h1,.cfa-page h2,.cfa-page h3,.cfa-page h4{ font-weight:800 !important; line-height:1.08 !important; }
  .cfa-page .hero h1{ font-weight:800 !important; }
  .cfa-page p,.cfa-page li,.cfa-page a,.cfa-page label,.cfa-page input,.cfa-page select,.cfa-page textarea{ letter-spacing:normal; }
  .cfa-page ul{ margin:0; padding:0; }
  .cfa-page button{ font-family:var(--font-head) !important; }
  .cfa-page p{ margin:0 0 1rem; color:var(--ink-soft); }
  .cfa-page a{ color:inherit; text-decoration:none; }
  .cfa-page img{ max-width:100%; display:block; }
  .cfa-page :focus-visible{ outline:3px solid var(--navy); outline-offset:3px; border-radius:6px; }
  .cfa-wrap{ width:100%; max-width:var(--maxw); margin-inline:auto; padding-inline:clamp(20px,5vw,40px); }
  .cfa-section{ padding-block:clamp(58px,8vw,116px); }
  .eyebrow{ display:inline-flex; align-items:center; gap:.55rem; font-family:var(--font-head); font-weight:700; font-size:.8rem; letter-spacing:.16em; text-transform:uppercase; color:var(--red); margin-bottom:1rem; }
  .eyebrow.navy{ color:var(--navy); } .eyebrow.gold{ color:var(--gold); }
  .eyebrow::before{ content:""; width:26px; height:2px; background:currentColor; display:inline-block; }
  .lead{ font-size:clamp(1.06rem,1.6vw,1.3rem); color:var(--ink-soft); max-width:60ch; }
  .center{ text-align:center; } .center .lead{ margin-inline:auto; } .center .eyebrow{ justify-content:center; }

  /* ====================== BUTTONS ====================== */
  .btn{ --_bg:var(--red); --_fg:#fff; --_bd:var(--red);
    display:inline-flex; align-items:center; justify-content:center; gap:.55em; font-family:var(--font-head); font-weight:700; font-size:1rem;
    padding:.92em 1.75em; border-radius:var(--r-pill); background:var(--_bg) !important; color:var(--_fg) !important; border:2px solid var(--_bd) !important; cursor:pointer;
    transition:transform .18s var(--ease), background .18s var(--ease), box-shadow .18s var(--ease), color .18s var(--ease), border-color .18s var(--ease);
    box-shadow:var(--shadow-sm); white-space:nowrap; max-width:100%; text-align:center; text-decoration:none !important; }
  .btn:hover{ transform:translateY(-2px); box-shadow:var(--shadow-md); background:var(--red-dark) !important; border-color:var(--red-dark) !important; }
  .btn:active{ transform:translateY(0); }
  .btn--navy{ --_bg:var(--navy); --_bd:var(--navy); } .btn--navy:hover{ background:var(--navy-dark) !important; border-color:var(--navy-dark) !important; }
  .btn--ghost{ --_bg:transparent; --_fg:var(--navy); --_bd:var(--navy); box-shadow:none; } .btn--ghost:hover{ background:var(--navy) !important; color:#fff !important; }
  .btn--light{ --_bg:#fff; --_fg:var(--red); --_bd:#fff; } .btn--light:hover{ background:var(--red-soft) !important; border-color:var(--red-soft) !important; color:var(--red-dark) !important; }
  .btn--outline-light{ --_bg:transparent; --_fg:#fff; --_bd:#fff; box-shadow:none; } .btn--outline-light:hover{ background:rgba(255,255,255,.15) !important; border-color:#fff !important; color:#fff !important; }
  .btn--lg{ padding:1.06em 2.15em; font-size:1.08rem; } .btn .ico{ width:1.05em; height:1.05em; }

  /* photo helper (fallback if an image fails) */
  .ph{ position:relative; overflow:hidden; background:linear-gradient(150deg,var(--navy) 0%, var(--navy-dark) 100%); }
  .ph img{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; z-index:1; }
  .ph .ph-note{ position:absolute; inset:0; z-index:0; display:flex; align-items:center; justify-content:center; text-align:center; color:rgba(255,255,255,.85); padding:1rem; font-size:.8rem; }

  /* ====================== TOPBAR + HEADER ====================== */
  .cfa-topbar{ background:var(--navy); color:#fff; text-align:center; font-size:.85rem; padding:.55rem 1rem; font-weight:500; }
  .cfa-topbar a{ color:#fff; font-weight:700; text-decoration:underline; text-underline-offset:3px; }
  .cfa-header{ position:sticky; top:0; z-index:60; background:rgba(251,247,241,.82); backdrop-filter:saturate(1.4) blur(12px); border-bottom:1px solid transparent; transition:box-shadow .25s var(--ease), border-color .25s, background .25s; }
  .cfa-header.is-scrolled{ box-shadow:var(--shadow-sm); border-color:var(--line); background:rgba(251,247,241,.96); }
  .cfa-nav{ display:flex; align-items:center; gap:1.5rem; height:82px; }
  .cfa-logo{ display:flex; align-items:center; flex:0 0 auto; }
  .cfa-logo img{ height:48px; width:auto; }
  .cfa-logo .fallback{ font-family:var(--font-head); font-weight:800; font-size:1.2rem; color:var(--navy); } .cfa-logo .fallback b{ color:var(--red); }
  .cfa-navlinks{ display:flex; align-items:center; gap:1.7rem; margin-left:auto; }
  .cfa-navlinks a{ font-family:var(--font-head); font-weight:600; font-size:.98rem; position:relative; padding:.25rem 0; }
  .cfa-navlinks a::after{ content:""; position:absolute; left:0; right:100%; bottom:-3px; height:2px; background:var(--red); transition:right .25s var(--ease); }
  .cfa-navlinks a:hover::after{ right:0; }
  .cfa-actions{ display:flex; align-items:center; gap:.7rem; }
  .cfa-login{ font-family:var(--font-head); font-weight:700; color:var(--navy); padding:.55rem .2rem; } .cfa-login:hover{ color:var(--red); }
  .cfa-burger{ display:none; background:none; border:0; cursor:pointer; padding:.5rem; margin-left:auto; }
  .cfa-burger span{ display:block; width:26px; height:2.5px; background:var(--navy); border-radius:2px; transition:.25s var(--ease); }
  .cfa-burger span+span{ margin-top:6px; }
  .cfa-burger[aria-expanded="true"] span:nth-child(1){ transform:translateY(8.5px) rotate(45deg); }
  .cfa-burger[aria-expanded="true"] span:nth-child(2){ opacity:0; }
  .cfa-burger[aria-expanded="true"] span:nth-child(3){ transform:translateY(-8.5px) rotate(-45deg); }

  /* ====================== HERO ====================== */
  .hero{ position:relative; overflow-x:clip; padding-top:clamp(36px,5vw,60px); padding-bottom:clamp(48px,7vw,92px); }
  .hero::before{ content:""; position:absolute; top:-80px; right:0; width:440px; height:440px; z-index:0; background:radial-gradient(circle at center, rgba(228,0,43,.09), transparent 62%); pointer-events:none; }
  .hero-grid{ display:grid; grid-template-columns:1.02fr .98fr; gap:clamp(30px,5vw,64px); align-items:center; position:relative; }
  .hero-badges{ display:flex; gap:.6rem; flex-wrap:wrap; margin-bottom:1.3rem; }
  .pill-badge{ display:inline-flex; align-items:center; gap:.5rem; background:#fff; border:1px solid var(--line); padding:.42rem .9rem; border-radius:var(--r-pill); font-size:.79rem; font-weight:600; color:var(--ink-soft); box-shadow:var(--shadow-sm); }
  .pill-badge .dot{ width:9px; height:9px; border-radius:50%; background:var(--red); }
  .hero h1{ font-size:clamp(1.85rem,4.2vw,3.25rem); font-weight:800; }
  .hero h1 .hl{ color:var(--red); position:relative; }
  .hero h1 .hl::after{ content:""; position:absolute; left:0; right:0; bottom:.04em; height:.12em; background:var(--gold); border-radius:2px; opacity:.85; }
  .hero .lead{ margin-block:1.2rem 2rem; }
  .hero-cta{ display:flex; flex-wrap:wrap; gap:.9rem; align-items:center; }
  .hero-copy .hero-note{ margin-top:2.8rem; font-size:.92rem; color:var(--muted); }
  .hero-note{ font-size:.92rem; color:var(--muted); }
  .hero-note a{ color:var(--red); font-weight:700; text-decoration:underline; text-underline-offset:3px; }

  .hero-art{ position:relative; }
  .hero-art .blob{ position:absolute; z-index:0; right:-7%; top:-9%; width:62%; height:72%;
    background:linear-gradient(160deg,var(--red),var(--red-dark)); border-radius:46% 54% 58% 42% / 50% 44% 56% 50%; opacity:.9; }
  .hero-art .dots{ position:absolute; z-index:0; left:-5%; bottom:-6%; width:120px; height:120px; color:var(--navy); background:var(--dots); background-size:15px 15px; opacity:.5; }
  .hero-art .frame{ position:relative; z-index:2; border-radius:var(--r-xl); overflow:hidden; border:8px solid #fff; box-shadow:var(--shadow-lg); aspect-ratio:5/4; }
  .hero-art .chip{ position:absolute; z-index:3; right:4%; bottom:8%; background:#fff; border-radius:var(--r-md); box-shadow:var(--shadow-md); padding:.75rem 1rem; display:flex; align-items:center; gap:.65rem; }
  .hero-art .chip .ic{ width:42px; height:42px; border-radius:12px; background:var(--navy-soft); display:grid; place-items:center; }
  .hero-art .chip .ic svg{ width:23px; height:23px; color:var(--navy); }
  .hero-art .chip .t{ font-family:var(--font-head); font-weight:800; font-size:1.1rem; line-height:1; }
  .hero-art .chip .s{ font-size:.73rem; color:var(--muted); }

  /* ====================== WHAT IS LEAN + DONUT ====================== */
  .lean{ background:#fff; }
  .lean-grid{ display:grid; grid-template-columns:.9fr 1.1fr; gap:clamp(32px,5vw,68px); align-items:center; }
  .lean-viz{ text-align:center; }
  .donut-wrap{ position:relative; width:min(300px,82%); margin:0 auto; }
  .donut{ width:100%; height:auto; display:block; }
  .donut-track{ fill:none; stroke:var(--cream-2); stroke-width:3.4; }
  .donut-seg{ fill:none; stroke-width:3.4; stroke-linecap:butt; }
  .s-val{ stroke:var(--red); } .s-nec{ stroke:var(--navy); } .s-waste{ stroke:var(--gold); }
  .donut-center{ position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
  .donut-center .dn{ font-weight:800; font-size:clamp(1.9rem,4vw,2.4rem); color:var(--navy); line-height:1; }
  .donut-center .dl{ font-size:.74rem; color:var(--muted); margin-top:.25rem; line-height:1.15; }
  .donut-legend{ list-style:none; padding:0; margin:1.5rem auto 0; display:inline-grid; gap:.55rem; text-align:left; min-width:230px; }
  .donut-legend li{ display:flex; align-items:center; gap:.6rem; font-size:.92rem; font-weight:500; color:var(--ink); }
  .donut-legend b{ margin-left:auto; color:var(--ink); }
  .donut-legend .k{ width:14px; height:14px; border-radius:4px; flex:0 0 auto; }
  .k-val{ background:var(--red); } .k-nec{ background:var(--navy); } .k-waste{ background:var(--gold); }
  .donut-note{ text-align:center; font-size:.82rem; color:var(--muted); margin:1.2rem auto 0; max-width:36ch; line-height:1.5; }
  .lean-copy h2{ font-size:clamp(1.8rem,3.6vw,2.7rem); }
  .lean-copy>p{ font-size:1.02rem; }
  .why-list{ list-style:none; padding:0; margin:1.4rem 0 0; display:grid; gap:1rem; }
  .why-list li{ display:flex; gap:.85rem; align-items:flex-start; }
  .why-list .ic{ width:44px; height:44px; border-radius:13px; background:var(--navy-soft); display:grid; place-items:center; flex:0 0 auto; }
  .why-list .ic svg{ width:22px; height:22px; color:var(--navy); }
  .why-list li:nth-child(odd) .ic{ background:var(--red-soft); } .why-list li:nth-child(odd) .ic svg{ color:var(--red); }
  .why-list b{ display:block; margin-bottom:.1rem; }
  .why-list div{ font-size:.95rem; color:var(--ink-soft); }
  @media (max-width:860px){ .lean-grid{ grid-template-columns:1fr; } .lean-viz{ order:2; } }

  /* ====================== OUR PURPOSE ====================== */
  .purpose{ background:var(--cream-2); }
  .purpose-grid{ display:grid; grid-template-columns:1.15fr .85fr; gap:clamp(24px,3vw,40px); align-items:stretch; margin-top:clamp(28px,4vw,48px); }
  .purpose-quote{ position:relative; margin:0; background:#fff; border:1px solid var(--line); border-radius:var(--r-xl); padding:clamp(30px,4vw,52px); box-shadow:var(--shadow-sm); display:flex; flex-direction:column; justify-content:center; }
  .purpose-quote .qmark{ font-family:var(--font-head); font-weight:800; color:var(--red); font-size:5rem; line-height:.7; height:.5em; display:block; margin-bottom:.2rem; }
  .purpose-quote p{ font-family:var(--font-head); font-weight:700; font-size:clamp(1.25rem,2.2vw,1.7rem); line-height:1.3; color:var(--ink); margin:0 0 1.1rem; letter-spacing:-.01em; }
  .purpose-quote cite{ font-style:normal; font-weight:700; font-size:.85rem; letter-spacing:.12em; text-transform:uppercase; color:var(--navy); }
  .purpose-part{ background:linear-gradient(155deg,var(--navy) 0%, var(--navy-dark) 100%); color:#fff; border-radius:var(--r-xl); padding:clamp(30px,4vw,44px); box-shadow:var(--shadow-md); display:flex; flex-direction:column; justify-content:center; }
  .purpose-part .pp-ic{ width:56px; height:56px; border-radius:16px; background:rgba(255,255,255,.14); display:grid; place-items:center; margin-bottom:1.2rem; }
  .purpose-part .pp-ic svg{ width:28px; height:28px; color:#fff; }
  .purpose-part h3{ color:#fff; font-size:1.4rem; margin-bottom:.6rem; }
  .purpose-part p{ color:rgba(255,255,255,.92); margin:0; font-size:1.02rem; }
  @media (max-width:820px){ .purpose-grid{ grid-template-columns:1fr; } }

  /* ====================== E / R / R ====================== */
  .err-head{ display:grid; grid-template-columns:1.1fr .9fr; gap:1.4rem 3rem; align-items:end; margin-bottom:clamp(28px,4vw,48px); }
  @media (max-width:820px){ .err-head{ grid-template-columns:1fr; align-items:start; } }
  .err-head h2{ font-size:clamp(2rem,4vw,3.1rem); margin:0; }
  .cards{ display:grid; grid-template-columns:repeat(3,1fr); gap:clamp(18px,2.4vw,26px); }
  .rcard{ background:#fff; border:1px solid var(--line); border-radius:var(--r-lg); overflow:hidden; box-shadow:var(--shadow-sm); transition:transform .25s var(--ease), box-shadow .25s var(--ease); display:flex; flex-direction:column; }
  .rcard:hover{ transform:translateY(-6px); box-shadow:var(--shadow-md); }
  .rcard .top{ position:relative; aspect-ratio:16/10; }
  .rcard .top .badge{ position:absolute; z-index:4; left:16px; bottom:-22px; width:56px; height:56px; border-radius:16px; background:var(--red); color:#fff; font-family:var(--font-head); font-weight:900; font-size:1.7rem; display:grid; place-items:center; box-shadow:var(--shadow-md); }
  .rcard:nth-child(2) .top .badge{ background:var(--navy); } .rcard:nth-child(3) .top .badge{ background:var(--gold); color:#5a3d00; }
  .rcard .body{ padding:2.1rem 1.6rem 1.7rem; }
  .rcard h3{ font-size:1.35rem; margin-bottom:.4rem; } .rcard p{ margin:0; font-size:.97rem; }
  .rcard .kpi{ font-family:var(--font-head); font-weight:800; color:var(--navy); }

  /* ====================== PROGRAM TIMELINE ====================== */
  .program{ background:linear-gradient(180deg,#fff 0%, var(--cream-2) 100%); }
  .tl{ position:relative; margin-top:clamp(34px,5vw,60px); }
  .tl .rail{ position:absolute; left:16.66%; right:16.66%; top:33px; height:3px; color:var(--navy); background:var(--dots); background-size:14px 3px; opacity:.55; z-index:0; }
  .tl-grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:clamp(18px,2.4vw,28px); position:relative; z-index:1; }
  .tl-phase{ text-align:center; display:flex; flex-direction:column; }
  .tl-node{ width:66px; height:66px; border-radius:50%; background:#fff; border:3px solid var(--red); color:var(--red); display:grid; place-items:center; font-family:var(--font-head); font-weight:900; font-size:1.5rem; margin:0 auto 1.2rem; box-shadow:var(--shadow-sm); flex:0 0 auto; }
  .tl-phase:nth-child(2) .tl-node{ border-color:var(--navy); color:var(--navy); } .tl-phase:nth-child(3) .tl-node{ border-color:var(--gold); color:#C98A12; }
  .tl-card{ text-align:left; background:#fff; border:1px solid var(--line); border-radius:var(--r-lg); padding:clamp(20px,2.4vw,28px); box-shadow:var(--shadow-sm); flex:1 1 auto; }
  .tl-weeks{ font-family:var(--font-head); font-weight:700; font-size:.76rem; letter-spacing:.09em; text-transform:uppercase; color:var(--red); margin-bottom:.5rem; }
  .tl-phase:nth-child(2) .tl-weeks{ color:var(--navy); } .tl-phase:nth-child(3) .tl-weeks{ color:#C98A12; }
  .tl-card h3{ font-size:1.3rem; } .tl-card>p{ font-size:.95rem; }
  .tl-card ul{ list-style:none; margin:.9rem 0 0; padding:0; display:grid; gap:.55rem; }
  .tl-card li{ display:flex; gap:.55rem; font-size:.92rem; font-weight:500; color:var(--ink); align-items:flex-start; }
  .tl-card li svg{ width:19px; height:19px; color:var(--red); flex:0 0 auto; margin-top:2px; }
  .tl-phase:nth-child(2) li svg{ color:var(--navy); } .tl-phase:nth-child(3) li svg{ color:#C98A12; }
  .kick{ display:inline-flex; align-items:center; gap:.6rem; margin-top:clamp(30px,4vw,48px); background:var(--navy); color:#fff; padding:.72rem 1.35rem; border-radius:var(--r-pill); font-family:var(--font-head); font-weight:700; font-size:.95rem; }
  .kick svg{ width:20px; height:20px; }
  @media (max-width:860px){ .tl .rail{ display:none; } .tl-grid{ grid-template-columns:1fr; gap:1.4rem; } .tl-phase{ text-align:left; } .tl-node{ margin:0 0 .9rem; } }

  /* ====================== STATS ====================== */
  .stats{ display:grid; grid-template-columns:repeat(4,1fr); gap:clamp(16px,2vw,24px); }
  .statc{ background:#fff; border:1px solid var(--line); border-radius:var(--r-lg); padding:clamp(22px,2.6vw,30px) clamp(20px,2.4vw,28px); box-shadow:var(--shadow-sm); position:relative; overflow:hidden; display:flex; flex-direction:column; justify-content:center; }
  .statc::before{ content:""; position:absolute; left:0; top:0; bottom:0; width:5px; background:var(--red); }
  .statc:nth-child(2)::before{ background:var(--navy); } .statc:nth-child(3)::before{ background:var(--gold); } .statc:nth-child(4)::before{ background:var(--red); }
  .statc .n{ font-family:var(--font-head); font-weight:800; font-size:clamp(2.1rem,4vw,3rem); line-height:1; color:var(--navy); }
  .statc:nth-child(odd) .n{ color:var(--red); }
  .statc .l{ font-size:.9rem; color:var(--ink-soft); margin-top:.55rem; line-height:1.35; }

  /* ====================== PILOT PATH ====================== */
  .pilot{ background:var(--navy); color:#fff; }
  .pilot .eyebrow{ color:var(--gold); } .pilot h2{ color:#fff; } .pilot .lead{ color:rgba(255,255,255,.85); }
  .ppath{ display:grid; grid-template-columns:repeat(6,1fr); gap:12px; margin-top:clamp(32px,4vw,52px); }
  .ppath .ps{ position:relative; text-align:center; padding:0 .3rem; }
  .ppath .ps .num{ width:52px; height:52px; margin:0 auto .8rem; border-radius:50%; background:rgba(255,255,255,.08); border:2px solid rgba(255,255,255,.35); color:#fff; font-family:var(--font-head); font-weight:900; font-size:1.2rem; display:grid; place-items:center; position:relative; z-index:1; }
  .ppath .ps:nth-child(1) .num, .ppath .ps:nth-child(6) .num{ background:var(--red); border-color:var(--red); }
  .ppath .ps::after{ content:""; position:absolute; top:25px; left:60%; right:-40%; height:0; border-top:2px dashed rgba(245,179,53,.85); z-index:0; }
  .ppath .ps:last-child::after{ display:none; }
  .ppath h4{ color:#fff; font-size:.98rem; margin-bottom:.3rem; }
  .ppath p{ color:rgba(255,255,255,.72); font-size:.82rem; margin:0; }
  .pilot-note{ text-align:center; align-items: center; font-weight:600; color:#fff !important; font-size:1rem; max-width:680px; line-height:1.5; }
  .pilot-note svg{ display:inline-block; vertical-align:-4px; margin-right:.45rem; color:var(--gold); width:19px; height:19px; }
  .pilot-cta{ text-align:center; margin-top:1.5rem; }
  @media (max-width:980px){ .ppath{ grid-template-columns:repeat(3,1fr); gap:28px 12px; } .ppath .ps:nth-child(3)::after{ display:none; } }
  @media (max-width:560px){ .ppath{ grid-template-columns:1fr 1fr; } .ppath .ps::after{ display:none; } }

  /* ====================== WHAT'S INCLUDED ====================== */
  .incl-grid{ display:grid; grid-template-columns:1.05fr .95fr; gap:clamp(30px,4vw,56px); align-items:center; }
  .browser{ border-radius:var(--r-lg); overflow:hidden; box-shadow:var(--shadow-lg); border:1px solid var(--line); background:#fff; }
  .browser .bar{ display:flex; align-items:center; gap:.5rem; padding:.7rem .9rem; background:#F1EDE6; border-bottom:1px solid var(--line); }
  .browser .bar i{ width:11px; height:11px; border-radius:50%; background:#d9d2c7; display:inline-block; }
  .browser .bar i:nth-child(1){ background:#ff5f57; } .browser .bar i:nth-child(2){ background:#febc2e; } .browser .bar i:nth-child(3){ background:#28c840; }
  .browser .bar .url{ margin-left:.6rem; font-size:.76rem; color:var(--muted); background:#fff; border:1px solid var(--line); border-radius:var(--r-pill); padding:.2rem .8rem; }
  .browser .ph{ aspect-ratio:16/10.5; background:#fff; }
  .browser .ph img{ object-fit:cover; object-position:top; }
  .incl-list{ list-style:none; padding:0; margin:1.3rem 0 0; display:grid; grid-template-columns:1fr 1fr; gap:1rem 1.3rem; }
  .incl-list li{ display:flex; gap:.7rem; align-items:flex-start; }
  .incl-list .ic{ width:40px; height:40px; border-radius:12px; background:var(--red-soft); display:grid; place-items:center; flex:0 0 auto; }
  .incl-list .ic svg{ width:21px; height:21px; color:var(--red); }
  .incl-list li:nth-child(even) .ic{ background:var(--navy-soft); } .incl-list li:nth-child(even) .ic svg{ color:var(--navy); }
  .incl-list .t{ font-family:var(--font-head); font-weight:700; font-size:.97rem; display:block; margin-bottom:.1rem; }
  .incl-list .s{ font-size:.84rem; color:var(--muted); }
  @media (max-width:860px){ .incl-grid{ grid-template-columns:1fr; } .incl-list{ grid-template-columns:1fr 1fr; } }
  @media (max-width:520px){ .incl-list{ grid-template-columns:1fr; } }

  /* ====================== VIDEO ====================== */
  .video-sec{ background:linear-gradient(180deg,var(--cream-2) 0%, var(--cream) 100%); }
  .video-grid{ display:grid; grid-template-columns:.85fr 1.15fr; gap:clamp(28px,4vw,54px); align-items:center; }
  .video-embed{ position:relative; aspect-ratio:16/9; border-radius:var(--r-lg); overflow:hidden; box-shadow:var(--shadow-lg); background:var(--navy-dark); }
  .video-embed iframe{ position:absolute; inset:0; width:100%; height:100%; border:0; }
  @media (max-width:860px){ .video-grid{ grid-template-columns:1fr; } }

  /* ====================== FORMS ====================== */
  .forms{ background:#fff; }
  .cohort-callout{ display:flex; gap:1rem; align-items:flex-start; background:var(--cream); border:1px solid var(--line); border-left:5px solid var(--red); border-radius:var(--r-md); padding:1.2rem 1.4rem; margin:0 auto clamp(30px,4vw,44px); max-width:920px; box-shadow:var(--shadow-sm); font-size:.97rem; color:var(--ink-soft); }
  .cohort-callout svg{ width:26px; height:26px; color:var(--red); flex:0 0 auto; margin-top:2px; }
  .cohort-callout strong{ color:var(--ink); }
  .forms-grid{ display:grid; grid-template-columns:1fr 1fr; gap:clamp(24px,3.5vw,44px); align-items:stretch; }
  .forms-grid .form-card{ height:100%; display:flex; flex-direction:column; }
  .form-card{ background:var(--cream); border:1px solid var(--line); border-radius:var(--r-lg); padding:clamp(26px,3.2vw,40px); box-shadow:var(--shadow-sm); }
  .form-card.accent{ background:linear-gradient(155deg,var(--red) 0%, var(--red-dark) 100%); color:#fff; border-color:var(--red); }
  .form-card.accent h3,.form-card.accent .eyebrow{ color:#fff; }
  .form-card .eyebrow{ margin-bottom:.5rem; } .form-card h3{ font-size:1.7rem; margin-bottom:.4rem; }
  .form-card .sub{ margin-bottom:1.5rem; font-size:.97rem; } .form-card.accent .sub{ color:rgba(255,255,255,.92); }
  .field{ margin-bottom:1rem; }
  .field label{ display:block; font-family:var(--font-head); font-weight:600; font-size:.9rem; margin-bottom:.35rem; }
  .field .req{ color:var(--red); } .form-card.accent .field .req{ color:#fff; }
  .field input,.field select,.field textarea{ width:100%; font-family:var(--font-body); font-size:1rem; color:var(--ink); padding:.85rem 1rem; border:1.5px solid var(--line); border-radius:var(--r-sm); background:#fff; transition:border-color .18s var(--ease), box-shadow .18s var(--ease); }
  .field textarea{ min-height:120px; resize:vertical; }
  .field input:focus,.field select:focus,.field textarea:focus{ outline:none; border-color:var(--navy); box-shadow:0 0 0 4px var(--navy-soft); }
  .form-card.accent .field input:focus,.form-card.accent .field textarea:focus,.form-card.accent .field select:focus{ border-color:#fff; box-shadow:0 0 0 4px rgba(255,255,255,.35); }
  .field .err{ color:#8A0015; font-size:.82rem; margin-top:.3rem; display:none; font-weight:600; } .form-card.accent .field .err{ color:#fff; }
  .field.invalid input,.field.invalid select,.field.invalid textarea{ border-color:#8A0015; } .form-card.accent .field.invalid input,.form-card.accent .field.invalid textarea{ border-color:#fff; }
  .field.invalid .err{ display:block; }
  .field .hint{ font-size:.78rem; color:var(--muted); margin-top:.3rem; } .form-card.accent .field .hint{ color:rgba(255,255,255,.8); }
  .two-col{ display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
  .form-card.accent .btn{ --_bg:#fff; --_fg:var(--red); --_bd:#fff; } .form-card.accent .btn:hover{ background:var(--red-soft); border-color:var(--red-soft); color:var(--red-dark); }
  .form-msg{ display:none; margin-top:1rem; padding:.9rem 1.1rem; border-radius:var(--r-sm); font-size:.95rem; font-weight:600; }
  .form-msg.ok{ display:block; background:#E7F6EC; color:#1E6B3A; border:1px solid #B7E3C4; }
  .form-msg.bad{ display:block; background:#FDEBEC; color:#8A0015; border:1px solid #F3C2C6; }
  .form-card.accent .form-msg{ background:#fff; }
  .hp{ position:absolute; left:-9999px; width:1px; height:1px; overflow:hidden; }
  .btn[aria-busy="true"]{ opacity:.75; pointer-events:none; }
  .btn .spin{ width:1em; height:1em; border:2px solid currentColor; border-right-color:transparent; border-radius:50%; animation:spin .7s linear infinite; display:none; } .btn[aria-busy="true"] .spin{ display:inline-block; }
  @keyframes spin{ to{ transform:rotate(360deg); } }
  @media (max-width:860px){ .forms-grid{ grid-template-columns:1fr; } }
  /* Chick-fil-A store-address autocomplete (snippet-driven) — brand-match the dropdown */
  .cfa-input-wrapper{ position:relative; }
  .forms .cfa-suggestions{ border:1px solid var(--line); border-radius:0 0 var(--r-sm) var(--r-sm); box-shadow:var(--shadow-md); margin-top:2px; }
  .forms .cfa-suggestion-item{ font-family:var(--font-body); color:var(--ink); }
  .forms .cfa-suggestion-item:hover{ background:var(--red-soft); }
  /* keep suggestion text readable inside the red accent card */
  .form-card.accent .cfa-suggestions, .form-card.accent .cfa-suggestion-item{ color:var(--ink); }

  /* ===== Native GravityForms embeds, styled to match the cards ===== */
  .gf-embed .gf-note{ font-size:.9rem; color:var(--muted); background:#fff; border:1px dashed var(--line); border-radius:var(--r-sm); padding:1rem 1.1rem; margin:0; }
  .form-card.accent .gf-embed .gf-note{ color:#fff; border-color:rgba(255,255,255,.5); background:transparent; }
  .form-card .gform_wrapper, .form-card .gform_wrapper form{ margin:0 !important; }
  .form-card .gform_wrapper .gfield{ margin-bottom:1rem !important; }
  .form-card .gform_wrapper .gfield_label{ font-family:var(--font-head) !important; font-weight:600 !important; font-size:.9rem !important; margin-bottom:.35rem !important; color:var(--ink); }
  .form-card .gform_wrapper .ginput_container input[type=text],
  .form-card .gform_wrapper .ginput_container input[type=email],
  .form-card .gform_wrapper .ginput_container input[type=tel],
  .form-card .gform_wrapper .ginput_container input[type=number],
  .form-card .gform_wrapper .ginput_container textarea,
  .form-card .gform_wrapper textarea,
  .form-card .gform_wrapper select,
  .form-card .gform_wrapper .cfa-autocomplete{
    width:100% !important; font-family:var(--font-body) !important; font-size:1rem !important; color:var(--ink) !important;
    padding:.85rem 1rem !important; border:1.5px solid var(--line) !important; border-radius:var(--r-sm) !important; background:#fff !important; box-shadow:none !important; }
  .form-card .gform_wrapper textarea{ min-height:120px !important; }
  .form-card .gform_wrapper input:focus,.form-card .gform_wrapper textarea:focus,.form-card .gform_wrapper select:focus{ outline:none !important; border-color:var(--navy) !important; box-shadow:0 0 0 4px var(--navy-soft) !important; }
  .form-card .gform_wrapper .gfield_description{ font-size:.78rem; color:var(--muted); padding-top:.3rem; }
  .form-card .gform_wrapper .gfield_required{ color:var(--red); }
  .form-card .gform_footer,.form-card .gform_wrapper .gform_footer{ margin-top:1.2rem !important; padding:0 !important; }
  .form-card .gform_wrapper .gform_footer input[type=submit],
  .form-card .gform_wrapper button.gform_button{
    font-family:var(--font-head) !important; font-weight:700 !important; font-size:1.05rem !important; color:#fff !important;
    background:var(--navy) !important; border:2px solid var(--navy) !important; border-radius:var(--r-pill) !important;
    padding:.95em 2em !important; width:auto !important; cursor:pointer !important; transition:transform .18s var(--ease),background .18s var(--ease); }
  .form-card .gform_wrapper .gform_footer input[type=submit]:hover{ transform:translateY(-2px); background:var(--navy-dark) !important; border-color:var(--navy-dark) !important; }
  .form-card .gform_wrapper .gfield_error input,.form-card .gform_wrapper .gfield_error textarea,.form-card .gform_wrapper .gfield_error select{ border-color:#8A0015 !important; }
  .form-card .gform_wrapper .validation_message{ color:#8A0015 !important; font-size:.82rem; font-weight:600; }
  .form-card .gform_wrapper .gform_validation_errors{ background:#FDEBEC; border:1px solid #F3C2C6; border-radius:var(--r-sm); padding:.8rem 1rem; margin-bottom:1rem; box-shadow:none; }
  .form-card .gform_confirmation_message{ color:var(--ink); font-weight:600; }
  .form-card.accent .gform_wrapper .gfield_label,.form-card.accent .gform_wrapper .gfield_description,.form-card.accent .gform_confirmation_message{ color:#fff !important; }
  .form-card.accent .gform_wrapper .gform_footer input[type=submit],.form-card.accent .gform_wrapper button.gform_button{ background:#fff !important; color:var(--red) !important; border-color:#fff !important; }
  .form-card.accent .gform_wrapper .gform_footer input[type=submit]:hover{ background:var(--red-soft) !important; color:var(--red-dark) !important; border-color:var(--red-soft) !important; }
  .form-card.accent .gform_wrapper .validation_message{ color:#fff !important; }

  /* ============================================================
     BULLETPROOF full-width, uniform fields. Handles BOTH GF markups:
     orbital/grid (.gform_fields is a grid) AND legacy (ul > li.gfield floated).
     Overrides GF "field size" (small/medium) and partial column widths.
     ============================================================ */
  .form-card .gform_wrapper .gform_fields{ display:grid !important; grid-template-columns:1fr !important; grid-row-gap:20px !important; row-gap:20px !important; }
  .form-card .gform_wrapper .gform_fields > .gfield,
  .form-card .gform_wrapper li.gfield,
  .form-card .gform_wrapper .gfield{ grid-column:1 / -1 !important; width:100% !important; max-width:100% !important; float:none !important; clear:both !important; margin:0 !important; padding:0 !important; list-style:none !important; }
  .form-card .gform_wrapper .ginput_container{ width:100% !important; max-width:100% !important; float:none !important; margin-top:0 !important; }
  /* first/last name (advanced Name) => two equal columns in every GF markup */
  .form-card .gform_wrapper .ginput_complex{ display:grid !important; grid-template-columns:1fr 1fr !important; gap:14px !important; width:100% !important; max-width:100% !important; float:none !important; }
  .form-card .gform_wrapper .ginput_complex > span,
  .form-card .gform_wrapper .ginput_complex > .ginput_left,
  .form-card .gform_wrapper .ginput_complex > .ginput_right{ width:100% !important; max-width:100% !important; min-width:0 !important; float:none !important; margin:0 !important; padding:0 !important; }
  /* the actual controls */
  .form-card .gform_wrapper input:not([type=submit]):not([type=button]):not([type=checkbox]):not([type=radio]),
  .form-card .gform_wrapper select,
  .form-card .gform_wrapper textarea,
  .form-card .cfa-autocomplete{ width:100% !important; max-width:100% !important; min-width:0 !important; min-height:52px !important; box-sizing:border-box !important; }
  .form-card .gform_wrapper textarea{ min-height:120px !important; }
  .form-card .cfa-input-wrapper{ width:100% !important; max-width:100% !important; margin:0 !important; }

  /* --- Pilot-card polish: spacing + visible address label + nicer suggestions --- */
  .form-card .gform_wrapper .gsection,.form-card .gform_wrapper .gfield--type-section{ margin:.3rem 0 !important; padding:0 !important; border:0 !important; min-height:0 !important; }
  .form-card .gform_wrapper .gsection .gsection_title{ display:none !important; }
  .form-card .gform_wrapper .ginput_complex label,.form-card .gform_wrapper .gfield_label{ margin-bottom:.45rem !important; }
  .form-card .gform_wrapper .ginput_container{ margin-top:0 !important; }
  .form-card .cfa-input-wrapper{ margin:0 !important; width:100% !important; }
  .form-card .cfa-input-wrapper .cfa-autocomplete{ width:100% !important; }
  /* store-address label — force visible/white on the red card */
  .form-card .cfa-input-wrapper label{ font-family:var(--font-head) !important; font-weight:600 !important; font-size:.9rem !important; margin-bottom:.45rem !important; display:block; }
  .form-card.accent .cfa-input-wrapper label,.form-card.accent .cfa-input-wrapper label strong{ color:#fff !important; }
  /* autocomplete dropdown */
  .form-card .cfa-suggestions{ border:1px solid var(--line) !important; border-radius:var(--r-sm) !important; box-shadow:var(--shadow-md) !important; margin-top:4px !important; padding:0 !important; overflow:hidden auto; max-height:240px; background:#fff !important; }
  .form-card .cfa-suggestion-item{ font-family:var(--font-body) !important; color:var(--ink) !important; padding:.7rem .9rem !important; border-bottom:1px solid var(--line) !important; line-height:1.35 !important; font-size:.9rem !important; cursor:pointer; }
  .form-card .cfa-suggestion-item:last-child{ border-bottom:0 !important; }
  .form-card .cfa-suggestion-item:hover{ background:var(--red-soft) !important; color:var(--red-dark) !important; }

  /* ====================== FINAL CTA ====================== */
  .cta-band{ text-align:center; }
  .cta-inner{ position:relative; overflow:hidden; background:linear-gradient(135deg,var(--red) 0%, var(--red-dark) 100%); color:#fff; border-radius:var(--r-xl); padding:clamp(42px,6vw,80px); box-shadow:var(--shadow-lg); }
  .cta-inner .dots-dec{ position:absolute; z-index:0; color:#fff; opacity:.16; background:var(--dots); background-size:18px 18px; }
  .cta-inner .dots-dec.a{ left:-30px; top:-30px; width:180px; height:180px; } .cta-inner .dots-dec.b{ right:-30px; bottom:-30px; width:220px; height:220px; }
  .cta-inner h2{ color:#fff; font-size:clamp(2rem,4.2vw,3.2rem); position:relative; z-index:1; }
  .cta-inner p{ color:rgba(255,255,255,.94); max-width:54ch; margin-inline:auto; position:relative; z-index:1; }
  .cta-inner .hero-cta{ justify-content:center; margin-top:1.6rem; position:relative; z-index:1; }

  /* ====================== FOOTER ====================== */
  .cfa-footer{ background:var(--navy-dark); color:#c3d2d7; padding-block:clamp(46px,6vw,72px) 2rem; }
  .cfa-footer a{ color:#c3d2d7; } .cfa-footer a:hover{ color:#fff; }
  .foot-grid{ display:grid; grid-template-columns:1.6fr 1fr 1fr 1fr; gap:clamp(24px,3vw,40px); }
  .cfa-footer h4{ color:#fff; font-size:1rem; margin-bottom:1rem; }
  .cfa-footer ul{ list-style:none; padding:0; margin:0; display:grid; gap:.6rem; font-size:.95rem; }
  .foot-logo-chip{ display:inline-flex; background:#fff; border-radius:14px; padding:.7rem 1rem; margin-bottom:1rem; }
  .foot-logo-chip img{ height:40px; } .foot-logo-chip .fallback{ font-family:var(--font-head); font-weight:800; font-size:1.2rem; color:var(--navy); } .foot-logo-chip .fallback b{ color:var(--red); }
  .foot-brand p{ color:#93a8ae; font-size:.94rem; max-width:36ch; }
  .foot-bottom{ border-top:1px solid rgba(255,255,255,.12); margin-top:clamp(30px,4vw,48px); padding-top:1.6rem; display:flex; justify-content:space-between; gap:1rem; flex-wrap:wrap; font-size:.86rem; color:#93a8ae; }
  .foot-bottom a{ color:#93a8ae; }
  @media (max-width:860px){ .foot-grid{ grid-template-columns:1fr 1fr; } }
  @media (max-width:560px){ .foot-grid{ grid-template-columns:1fr; } }

  /* ====================== REVEAL + MOBILE NAV ====================== */
  .reveal{ opacity:0; transform:translateY(26px); transition:opacity .6s var(--ease), transform .6s var(--ease); }
  .reveal.in{ opacity:1; transform:none; }
  @media (prefers-reduced-motion:reduce){ .reveal{ opacity:1; transform:none; transition:none; } }
  .cfa-mobile{ position:fixed; top:82px; left:0; right:0; background:var(--cream); border-bottom:1px solid var(--line); box-shadow:var(--shadow-md); transform:translateY(-140%); transition:transform .32s var(--ease); z-index:59; padding:1.2rem clamp(20px,5vw,40px) 1.6rem; max-height:calc(100vh - 82px); overflow-y:auto; }
  .cfa-mobile.open{ transform:translateY(0); }
  .cfa-mobile a{ display:block; font-family:var(--font-head); font-weight:700; font-size:1.1rem; padding:.85rem 0; border-bottom:1px solid var(--line); }
  .cfa-mobile .m-actions{ display:flex; gap:.8rem; margin-top:1.2rem; } .cfa-mobile .m-actions .btn{ flex:1; }

  @media (max-width:960px){
    .hero-grid{ grid-template-columns:1fr; } .hero-art{ max-width:520px; margin:.5rem auto 0; }
    .cards{ grid-template-columns:1fr; } .stats{ grid-template-columns:1fr 1fr; }
    .cfa-navlinks,.cfa-actions{ display:none; } .cfa-burger{ display:block; }
  }
  @media (max-width:560px){ .two-col{ grid-template-columns:1fr; } .stats{ grid-template-columns:1fr; } .hero-cta .btn{ width:100%; } .btn{ white-space:normal; } }
</style>
</head>
<body <?php body_class( 'cfa-page' ); ?>>
<noscript><style>.reveal{opacity:1 !important;transform:none !important}</style></noscript>

  <div class="cfa-topbar">
    Pilot Cohort 1 kicks off <strong>July&nbsp;2026</strong>. A second cohort of 100 restaurants follows in January&nbsp;2027. <a href="#interest">Express your interest →</a>
  </div>

  <header class="cfa-header" id="cfaHeader">
    <div class="cfa-wrap">
      <nav class="cfa-nav" aria-label="Primary">
        <a class="cfa-logo" href="#top" aria-label="Chick-fil-A LEAN365 home">
          <img src="https://cfalean365.com/wp-content/uploads/2025/12/Chick-fil-A-LEAN365-Logo.png" alt="Chick-fil-A LEAN365"
               onerror="this.style.display='none';this.nextElementSibling.style.display='block';" />
          <span class="fallback" style="display:none">CFA&nbsp;<b>LEAN365</b></span>
        </a>
        <div class="cfa-navlinks">
          <a href="#program">The Program</a>
          <a href="#pilot">How the Pilot Works</a>
          <a href="#included">What's Included</a>
          <a href="#support">Support</a>
        </div>
        <div class="cfa-actions">
          <a class="cfa-login" data-login href="#">Member Login</a>
          <a class="btn" href="#interest">Express Interest</a>
        </div>
        <button class="cfa-burger" id="cfaBurger" aria-label="Open menu" aria-expanded="false" aria-controls="cfaMobile"><span></span><span></span><span></span></button>
      </nav>
    </div>
  </header>
  <div class="cfa-mobile" id="cfaMobile">
    <a href="#program">The Program</a><a href="#pilot">How the Pilot Works</a><a href="#included">What's Included</a><a href="#support">Support</a>
    <div class="m-actions"><a class="btn btn--ghost" data-login href="#">Login</a><a class="btn" href="#interest">Express Interest</a></div>
  </div>

  <span id="top"></span>

  <!-- ===================== HERO ===================== -->
  <section class="hero cfa-section" aria-labelledby="heroTitle">
    <div class="cfa-wrap">
      <div class="hero-grid">
        <div class="hero-copy reveal">
          <div class="hero-badges">
            <span class="pill-badge"><span class="dot"></span> In partnership with Chick-fil-A</span>
            <span class="pill-badge">A Circle&nbsp;K Supply&nbsp;Chain Solutions company</span>
          </div>
          <span class="eyebrow">LEAN365 Essentials</span>
          <h1 id="heroTitle">Create capacity. Build a culture that <span class="hl">keeps improving</span>.</h1>
          <p class="lead">A step-by-step plan that helps Chick-fil-A teams create capacity through transformational tools that drive results, turning everyday effort into lasting continuous improvement.</p>
          <div class="hero-cta">
            <a class="btn btn--lg" href="#interest">Express Interest
              <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            <a class="btn btn--ghost btn--lg" href="#program">Explore the Program</a>
          </div>
          <p class="hero-note">Already part of LEAN365? <a data-login href="#">Log in to the learning portal →</a></p>
        </div>

        <div class="hero-art reveal">
          <span class="blob" aria-hidden="true"></span>
          <span class="dots" aria-hidden="true"></span>
          <div class="frame ph">
            <img src="https://cfalean365.com/wp-content/uploads/2022/04/LEAN365-Intro-Image.020.png" alt="LEAN365 in a Chick-fil-A restaurant" loading="eager" onerror="this.style.display='none'" />
            <span class="ph-note">LEAN365</span>
          </div>
          <div class="chip">
            <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l2 2M9 2h6"/></svg></span>
            <span><span class="t">20 weeks</span><span class="s">guided to lasting habits</span></span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== WHAT IS LEAN ===================== -->
  <section class="cfa-section lean" id="what-is-lean" aria-labelledby="leanTitle">
    <div class="cfa-wrap">
      <div class="lean-grid">
        <div class="lean-viz reveal">
          <div class="donut-wrap">
            <svg class="donut" viewBox="0 0 36 36" role="img" aria-label="Where a typical shift goes: 20% value-adding work, 35% necessary support, 45% waste that Lean targets">
              <circle class="donut-track" cx="18" cy="18" r="15.9155"></circle>
              <circle class="donut-seg s-val"   cx="18" cy="18" r="15.9155" stroke-dasharray="20 80" stroke-dashoffset="25"></circle>
              <circle class="donut-seg s-nec"   cx="18" cy="18" r="15.9155" stroke-dasharray="35 65" stroke-dashoffset="5"></circle>
              <circle class="donut-seg s-waste" cx="18" cy="18" r="15.9155" stroke-dasharray="45 55" stroke-dashoffset="70"></circle>
            </svg>
            <div class="donut-center"><span class="dn">45%</span><span class="dl">waste to<br>reclaim</span></div>
          </div>
          <ul class="donut-legend">
            <li><span class="k k-val"></span> Value-adding work <b>20%</b></li>
            <li><span class="k k-nec"></span> Necessary support <b>35%</b></li>
            <li><span class="k k-waste"></span> Waste Lean targets <b>45%</b></li>
          </ul>
          <p class="donut-note">Illustrative. In most operations a large share of effort is non-value-adding. Lean turns that waste back into capacity for your team and guests.</p>
        </div>
        <div class="lean-copy reveal">
          <span class="eyebrow navy">What is Lean</span>
          <h2 id="leanTitle">Less waste. More of what matters.</h2>
          <p>Lean is a proven, people-first way of working that helps your team <strong>see and remove waste</strong>: the waiting, extra motion and rework that quietly eat up a shift, so more time and energy go to caring for guests and each other.</p>
          <p>It isn't about working harder or cutting corners. It's about making the <strong>right way the easy way</strong>, then improving it a little every day.</p>
          <ul class="why-list">
            <li><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg></span><div><b>Create capacity</b>Do more of what matters, without burning people out.</div></li>
            <li><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h4l3 8 4-16 3 8h4"/></svg></span><div><b>Improve flow &amp; consistency</b>Smoother shifts and fewer surprises, every day.</div></li>
            <li><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg></span><div><b>Engage &amp; retain your team</b>People stay where they're growing and heard.</div></li>
            <li><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.2-8.6"/><path d="M22 4 12 14.01l-3-3"/></svg></span><div><b>Build a daily habit of improvement</b>Small wins that compound into a lasting culture.</div></li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== ENGAGE / RETAIN / REWARD ===================== -->
  <section class="cfa-section" id="why" aria-labelledby="whyTitle">
    <div class="cfa-wrap">
      <div class="err-head reveal">
        <div>
          <span class="eyebrow navy">Why LEAN365</span>
          <h2 id="whyTitle">Engage. Retain. Reward.</h2>
        </div>
        <p class="lead">A practical program built for the way restaurants really run: energizing Team Members, keeping them longer, and rewarding their growth.</p>
      </div>
      <div class="cards">
        <article class="rcard reveal">
          <div class="top ph"><img src="https://cfalean365.com/wp-content/uploads/2022/04/LEAN365-Intro-Image.017.png" alt="Engaging Chick-fil-A Team Members" loading="lazy" onerror="this.style.display='none'" /><span class="ph-note">Engage</span><span class="badge">E</span></div>
          <div class="body"><h3>Engage</h3><p>Energize Team Members with video-based tutorials, high-energy activities and creative challenges that make learning stick.</p></div>
        </article>
        <article class="rcard reveal">
          <div class="top ph"><img src="https://cfalean365.com/wp-content/uploads/2022/04/LEAN365-Intro-Image.019.png" alt="Team Members who stay and grow" loading="lazy" onerror="this.style.display='none'" /><span class="ph-note">Retain</span><span class="badge">R</span></div>
          <div class="body"><h3>Retain</h3><p>Participants are more likely to stay, with a <span class="kpi">70% retention rate</span> among Team Members who take part in LEAN365 Essentials.</p></div>
        </article>
        <article class="rcard reveal">
          <div class="top ph"><img src="https://cfalean365.com/wp-content/uploads/2022/04/LEAN365-Intro-Image.021.png" alt="Rewarding growth and learning" loading="lazy" onerror="this.style.display='none'" /><span class="ph-note">Reward</span><span class="badge">R</span></div>
          <div class="body"><h3>Reward</h3><p>Team Members can earn College Credit for their participation through our partnership with <span class="kpi">Point University</span>.</p></div>
        </article>
      </div>
    </div>
  </section>

  <!-- ===================== OUR PURPOSE ===================== -->
  <section class="cfa-section purpose" id="purpose" aria-labelledby="purposeTitle">
    <div class="cfa-wrap">
      <div class="center reveal">
        <span class="eyebrow navy">Our Purpose</span>
        <h2 id="purposeTitle">Rooted in a bigger why</h2>
      </div>
      <div class="purpose-grid">
        <blockquote class="purpose-quote reveal">
          <span class="qmark" aria-hidden="true">&ldquo;</span>
          <p>To glorify God by being a faithful steward of all that is entrusted to us and to have a positive influence on all who come in contact with Chick-fil-A.</p>
          <cite>Chick-fil-A Corporate Purpose</cite>
        </blockquote>
        <div class="purpose-part reveal">
          <span class="pp-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 1 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/></svg></span>
          <h3>How LEAN365 plays a part</h3>
          <p>We positively influence people to solve everyday problems, create a better tomorrow, and win hearts through continuous process improvement.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== PROGRAM TIMELINE ===================== -->
  <section class="cfa-section program" id="program" aria-labelledby="progTitle">
    <div class="cfa-wrap">
      <div class="center reveal">
        <span class="eyebrow">The program at a glance</span>
        <h2 id="progTitle">Three phases that build Lean capability that lasts</h2>
        <p class="lead">A guided, 20-week journey from developing yourself, to developing your team, to embedding continuous improvement into daily restaurant life.</p>
      </div>
      <div class="tl">
        <span class="rail" aria-hidden="true"></span>
        <div class="tl-grid">
          <div class="tl-phase reveal">
            <div class="tl-node">1</div>
            <div class="tl-card">
              <div class="tl-weeks">Weeks 1–8</div>
              <h3>Develop Self</h3>
              <p>The Lean Leader learns Lean tools and applies them to their own restaurant's performance.</p>
              <ul>
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> Use Lean tools and data to identify and eliminate waste</li>
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> Prioritize and execute high-impact opportunities</li>
              </ul>
            </div>
          </div>
          <div class="tl-phase reveal">
            <div class="tl-node">2</div>
            <div class="tl-card">
              <div class="tl-weeks">Weeks 9–20</div>
              <h3>Develop Others</h3>
              <p>The Lean Leader coaches their team and runs structured improvement using LEAN365.</p>
              <ul>
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> Inspire team engagement and commitment to improvement</li>
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> Lead change and leverage resources to sustain results</li>
              </ul>
            </div>
          </div>
          <div class="tl-phase reveal">
            <div class="tl-node">3</div>
            <div class="tl-card">
              <div class="tl-weeks">Ongoing</div>
              <h3>Lean Culture &amp; Value</h3>
              <p>Lean becomes part of how the restaurant runs: sustained, recognized and continuously improving.</p>
              <ul>
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> Reward, recognize and celebrate Lean champions</li>
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> Model and champion Lean leadership by example</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="center"><span class="kick"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> Target program kickoff · July 22, 2026</span></div>
      </div>
    </div>
  </section>

  <!-- ===================== STATS ===================== -->
  <section class="cfa-section" aria-label="Program at a glance" style="padding-block:clamp(20px,3vw,44px)">
    <div class="cfa-wrap">
      <div class="stats">
        <div class="statc reveal"><div class="n" data-count="70" data-suffix="%">70%</div><div class="l">Team retention rate among participants</div></div>
        <div class="statc reveal"><div class="n" data-count="20" data-suffix="">20</div><div class="l">Weeks of guided delivery</div></div>
        <div class="statc reveal"><div class="n" data-count="3" data-suffix="">3</div><div class="l">Phases from self to culture</div></div>
        <div class="statc reveal"><div class="n" data-count="100" data-suffix="+">100+</div><div class="l">Restaurants in Cohort 2 (Jan 2027)</div></div>
      </div>
    </div>
  </section>

  <!-- ===================== PILOT PATH ===================== -->
  <section class="cfa-section pilot" id="pilot" aria-labelledby="pilotTitle">
    <div class="cfa-wrap">
      <div class="center reveal">
        <span class="eyebrow">How the pilot works</span>
        <h2 id="pilotTitle">From first interest to program kickoff</h2>
        <p class="lead">A simple, supported path of six steps, from expressing interest to launching with your cohort.</p>
      </div>
      <div class="ppath">
        <div class="ps reveal"><div class="num">1</div><h4>Express Interest</h4><p>Request to join, or connect through CFA outreach.</p></div>
        <div class="ps reveal"><div class="num">2</div><h4>Get Selected</h4><p>Your restaurant is accepted; expectations confirmed.</p></div>
        <div class="ps reveal"><div class="num">3</div><h4>Name a Lean Leader</h4><p>Choose a team member to lead the work.</p></div>
        <div class="ps reveal"><div class="num">4</div><h4>Get Set Up</h4><p>Training matched; tools and access prepared.</p></div>
        <div class="ps reveal"><div class="num">5</div><h4>Prepare to Launch</h4><p>Cohort confirmed; welcome and logistics shared.</p></div>
        <div class="ps reveal"><div class="num">6</div><h4>Program Kicks Off</h4><p>Your Lean Leader begins with the cohort.</p></div>
      </div>
      <p class="pilot-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> Throughout the process, CFA and the training team provide support, so Operators are never on their own.</p>
      <div class="pilot-cta"><a class="btn btn--lg" href="#interest">Start with Step 1: Express Interest</a></div>
    </div>
  </section>

  <!-- ===================== WHAT'S INCLUDED ===================== -->
  <section class="cfa-section forms" id="included" aria-labelledby="inclTitle" style="background:var(--cream)">
    <div class="cfa-wrap">
      <div class="incl-grid">
        <div class="reveal">
          <div class="browser">
            <div class="bar"><i></i><i></i><i></i><span class="url">cfalean365.com · learning portal</span></div>
            <div class="ph"><img src="https://cfalean365.com/wp-content/uploads/2026/07/cfalean365.com-1-4.png" alt="LEAN365 online learning portal" loading="lazy" onerror="this.style.display='none'" /><span class="ph-note">Learning portal</span></div>
          </div>
        </div>
        <div class="reveal">
          <span class="eyebrow">What's included</span>
          <h2 id="inclTitle">Everything you need for a transformational LEAN journey</h2>
          <p>Digital and physical assets that engage Team Members. Every Team Member gets access to an online learning portal, and every restaurant receives the physical assets needed to launch.</p>
          <ul class="incl-list">
            <li><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 8-6 4 6 4V8z"/><rect x="2" y="6" width="14" height="12" rx="2"/></svg></span><span><span class="t">Video Tutorials</span><span class="s">LEAN concepts with real restaurant examples.</span></span></li>
            <li><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10M7 12h6"/></svg></span><span><span class="t">Challenge Cards</span><span class="s">Done solo or as a team within each unit.</span></span></li>
            <li><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg></span><span><span class="t">Games &amp; Activities</span><span class="s">Reinforce content in a fun, engaging way.</span></span></li>
            <li><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></span><span><span class="t">Resources &amp; Tools</span><span class="s">Apply LEAN concepts immediately on the floor.</span></span></li>
            <li><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span><span><span class="t">Leader Guide</span><span class="s">Equips LEAN Leaders to facilitate the journey.</span></span></li>
            <li><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg></span><span><span class="t">Promotional Materials</span><span class="s">Posters, prizes and giveaways to energize teams.</span></span></li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== VIDEO ===================== -->
  <section class="cfa-section video-sec" aria-labelledby="vidTitle">
    <div class="cfa-wrap">
      <div class="video-grid">
        <div class="reveal">
          <span class="eyebrow navy">See the impact</span>
          <h2 id="vidTitle">What can LEAN365 Essentials do?</h2>
          <p class="lead">Hear directly from Operators and Team Members who have experienced the impact of LEAN365 Essentials and the difference it's made in their restaurants.</p>
          <a class="btn btn--navy" href="#interest">Bring LEAN365 to your restaurant</a>
        </div>
        <div class="video-embed reveal">
          <iframe src="https://player.vimeo.com/video/1161914882?badge=0&amp;autopause=0&amp;player_id=0&amp;app_id=58479" allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" referrerpolicy="strict-origin-when-cross-origin" title="LEAN365 Testimonial" loading="lazy"></iframe>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== FORMS ===================== -->
  <section class="cfa-section forms" id="interest" aria-labelledby="formsTitle">
    <div class="cfa-wrap">
      <div class="center reveal">
        <span class="eyebrow">Get involved</span>
        <h2 id="formsTitle">Join the pilot, or reach out to our team</h2>
        <p class="lead">Interested restaurants can share their information to learn more or be considered for a future cohort.</p>
      </div>
      <div class="cohort-callout reveal">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
        <div><strong>About the pilot:</strong> Chick-fil-A's first LEAN365 pilot cohort begins in July 2026, helping participating restaurants strengthen team habits and improve operational performance through guided training. A second pilot cohort of 100 restaurants is planned for January 2027. Enter your information below to learn more or be considered for a future cohort.</div>
      </div>
      <div class="forms-grid">
        <!-- CFA Pilot Interest Form — GravityForms id 36 -->
        <div class="form-card accent reveal">
          <span class="eyebrow">Pilot interest</span>
          <h3>Express Interest in the Pilot</h3>
          <p class="sub">Tell us about your restaurant and we'll be in touch about current and future cohorts.</p>
          <!-- GF-EMBED 38 · page-lean365.php replaces this with the real form via do_shortcode -->
          <div class="gf-embed" data-gf="38"><?php echo do_shortcode('[gravityform id="38" title="false" description="false" ajax="true"]'); ?></div>
        </div>
        <!-- Support Contact Form — GravityForms id 37 -->
        <div class="form-card reveal" id="support">
          <span class="eyebrow navy">Support</span>
          <h3>Need Help? Our Pleasure!</h3>
          <p class="sub">For questions about LEAN365, your boxes or physical materials, or about this website, let us know how we can help. Someone from the LEAN Team will reach out within 48 hours.</p>
          <!-- GF-EMBED 37 · page-lean365.php replaces this with the real form via do_shortcode -->
          <div class="gf-embed" data-gf="37"><?php echo do_shortcode('[gravityform id="37" title="false" description="false" ajax="true"]'); ?></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== FINAL CTA ===================== -->
  <section class="cfa-section cta-band">
    <div class="cfa-wrap">
      <div class="cta-inner reveal">
        <span class="dots-dec a" aria-hidden="true"></span><span class="dots-dec b" aria-hidden="true"></span>
        <h2>Ready to build a culture of continuous improvement?</h2>
        <p>Join the growing network of Chick-fil-A restaurants transforming the way work gets done with LEAN365 Essentials.</p>
        <div class="hero-cta">
          <a class="btn btn--light btn--lg" href="#interest">Express Interest</a>
          <a class="btn btn--lg btn--outline-light" data-login href="#">Member Login</a>
        </div>
      </div>
    </div>
  </section>

  <footer class="cfa-footer">
    <div class="cfa-wrap">
      <div class="foot-grid">
        <div class="foot-brand">
          <span class="foot-logo-chip"><img src="https://cfalean365.com/wp-content/uploads/2025/12/Chick-fil-A-LEAN365-Logo.png" alt="Chick-fil-A LEAN365" onerror="this.style.display='none';this.nextElementSibling.style.display='block';" /><span class="fallback" style="display:none">CFA&nbsp;<b>LEAN365</b></span></span>
          <p>LEAN365, in partnership with Chick-fil-A and Circle K Supply Chain Solutions, helping teams create capacity and build a lasting culture of continuous improvement.</p>
        </div>
        <div><h4>Program</h4><ul><li><a href="#program">The Program</a></li><li><a href="#pilot">How the Pilot Works</a></li><li><a href="#included">What's Included</a></li><li><a href="#why">Why LEAN365</a></li></ul></div>
        <div><h4>Members</h4><ul><li><a data-login href="#">Member Login</a></li><li><a data-login href="#">Learning Portal</a></li><li><a href="#interest">Express Interest</a></li></ul></div>
        <div><h4>Contact</h4><ul><li><a href="#support">Contact Support</a></li><li><a href="mailto:support@cfalean365.com">support@cfalean365.com</a></li></ul></div>
      </div>
      <div class="foot-bottom">
        <span>© <span id="cfaYear"></span> LEAN365 · Circle K Supply Chain Solutions. All rights reserved.</span>
        <span><a href="#top">Back to top ↑</a></span>
      </div>
    </div>
  </footer>

<script>
(function(){
  "use strict";
  /* ============ CONFIG — EDIT BEFORE GO-LIVE ============ */
  window.CFA_CONFIG = window.CFA_CONFIG || {
    loginUrl:"https://cfalean365.com/wp-login.php",
    // WordPress REST endpoint registered by cfa-forms-snippet.php (recommended,
    // no file upload). If you instead upload gf-proxy.php to site root, set this
    // to "/gf-proxy.php".
    formEndpoint:"/wp-json/cfa/v1/submit"
  };

  document.querySelectorAll('[data-login]').forEach(function(a){ a.setAttribute('href', window.CFA_CONFIG.loginUrl); });
  var y=document.getElementById('cfaYear'); if(y) y.textContent=new Date().getFullYear();

  var header=document.getElementById('cfaHeader');
  var onScroll=function(){ if(header) header.classList.toggle('is-scrolled', window.scrollY>8); };
  onScroll(); window.addEventListener('scroll', onScroll, {passive:true});

  var burger=document.getElementById('cfaBurger'), mobile=document.getElementById('cfaMobile');
  function closeMenu(){ if(!burger||!mobile)return; burger.setAttribute('aria-expanded','false'); mobile.classList.remove('open'); }
  function openMenu(){ if(!burger||!mobile)return;
    // anchor the panel just below the header (handles topbar / WP admin bar offsets)
    if(header){ mobile.style.top = Math.max(0, Math.round(header.getBoundingClientRect().bottom)) + 'px'; }
    burger.setAttribute('aria-expanded','true'); mobile.classList.add('open'); }
  if(burger&&mobile){
    burger.addEventListener('click',function(e){ e.stopPropagation(); (burger.getAttribute('aria-expanded')==='true'?closeMenu:openMenu)(); });
    mobile.addEventListener('click',function(e){ e.stopPropagation(); });
    mobile.querySelectorAll('a').forEach(function(a){ a.addEventListener('click',closeMenu); });
    document.addEventListener('click',closeMenu);
    document.addEventListener('keydown',function(e){ if(e.key==='Escape'||e.keyCode===27) closeMenu(); });
    window.addEventListener('resize',function(){ if(window.innerWidth>960) closeMenu(); });
  }

  var reduce=window.matchMedia('(prefers-reduced-motion:reduce)').matches;
  var reveals=document.querySelectorAll('.reveal');
  if(reduce||!('IntersectionObserver' in window)){ reveals.forEach(function(el){ el.classList.add('in'); }); }
  else { var io=new IntersectionObserver(function(es){ es.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target); } }); },{threshold:.12,rootMargin:'0px 0px -8% 0px'});
    reveals.forEach(function(el){ io.observe(el); }); }

  var counters=document.querySelectorAll('.n[data-count]');
  function animateCount(el){ var target=parseFloat(el.getAttribute('data-count')), suffix=el.getAttribute('data-suffix')||'', dur=1400, t0=null;
    function frame(t){ if(!t0)t0=t; var p=Math.min((t-t0)/dur,1), eased=1-Math.pow(1-p,3);
      el.textContent=Math.round(target*eased).toLocaleString()+suffix; if(p<1) requestAnimationFrame(frame); } requestAnimationFrame(frame); }
  if(!reduce&&'IntersectionObserver' in window){ var cio=new IntersectionObserver(function(es){ es.forEach(function(e){ if(e.isIntersecting){ animateCount(e.target); cio.unobserve(e.target); } }); },{threshold:.6});
    counters.forEach(function(el){ cio.observe(el); }); }

  /* Forms are native GravityForms embeds (rendered by page-lean365.php).
     GF handles submission, validation, the Chick-fil-A autocomplete and
     anti-spam, so no custom form JS is needed here. */
})();
</script>
<?php wp_footer(); ?>
</body>
</html>
