<?php
/**
 * ============================================================================
 * CFA LEAN365 — Secure GravityForms submission proxy
 * ----------------------------------------------------------------------------
 * The homepage (index.html) POSTs JSON here. This file talks to GravityForms
 * on the SERVER SIDE so that NO API keys are ever exposed in the browser.
 *
 * Two modes — the file auto-detects which to use:
 *
 *   MODE A  (RECOMMENDED — no API keys needed):
 *     Place this file inside your WordPress root (same folder as wp-load.php),
 *     or anywhere it can find wp-load.php. It will bootstrap WordPress and call
 *     GFAPI::submit_form() directly. This runs full validation, notifications
 *     and confirmations — exactly like a normal front-end submission — and
 *     needs NO consumer key/secret at all.
 *
 *   MODE B  (fallback — REST API with keys):
 *     If WordPress can't be bootstrapped, it falls back to the GravityForms
 *     REST API v2 using the keys below. IMPORTANT: put real keys in
 *     wp-config.php / environment variables, NOT in this file, and ROTATE the
 *     keys that were shared in chat (WooCommerce → Advanced → REST API, or
 *     Forms → Settings → REST API).
 *
 * REQUIRED SETUP — see the two CONFIG blocks below:
 *   1. FIELD MAPS: map each friendly field name to its GravityForms field ID.
 *      Find IDs in the GF form editor (click a field → "Field ID" shown on the
 *      right), or GET /wp-json/gf/v2/forms/36 and /37.
 *   2. ALLOWED_ORIGINS: your site domain(s), to block cross-site abuse.
 * ============================================================================
 */

declare(strict_types=1);

/* ============================ CONFIG ============================ */

// 1) Allowed origins (CSRF / cross-site hardening). Add www + non-www.
const ALLOWED_ORIGINS = [
    'https://cfalean365.com',
    'https://www.cfalean365.com',
];

// 2) Field maps: friendly name (from the HTML "name" attribute)  =>  GF field ID.
//    ⚠️ VERIFY THESE IDs against your real forms 36 & 37 before going live.
//    For a GF "Name" advanced field the sub-inputs are like "1.3" (first),
//    "1.6" (last). A simple single-line field is just "1", "2", etc.
$FIELD_MAPS = [
    // CFA Pilot Interest Form — form 36 (IDs confirmed from GF editor)
    '36' => [
        'first_name' => '3.3',   // Name (ID 3) → First
        'last_name'  => '3.6',   // Name (ID 3) → Last
        'email'      => '4',     // Email (ID 4)
        'phone'      => '23',    // Phone (ID 23)
        'message'    => '32',    // "Any question or a message for us?" (ID 32)
        // Chick-fil-A Store Address is field ID 34 (GF "Address" type). It is
        // handled specially below: the Google place_id from the CFA autocomplete
        // snippet is resolved into sub-inputs 34.1 (street) / 34.3 (city) /
        // 34.4 (state) / 34.5 (zip), reusing the site's own cfa_fetch_place_details().
    ],
    // Support Contact Form — form 37 (IDs confirmed from GF editor)
    '37' => [
        'first_name' => '1.3',   // Name (ID 1) → First
        'last_name'  => '1.6',   // Name (ID 1) → Last
        'email'      => '2',     // Email (ID 2)
        'subject'    => '4',     // Subject (ID 4)
        'message'    => '3',     // Message (ID 3)
    ],
];

// 3) MODE B only — REST API base + keys. Prefer environment variables.
//    Leave keys empty to force MODE A (WordPress bootstrap) only.
const GF_SITE_BASE = 'https://cfalean365.com';
$GF_CONSUMER_KEY    = getenv('GF_CONSUMER_KEY')    ?: ''; // set in server env — DO NOT hardcode
$GF_CONSUMER_SECRET = getenv('GF_CONSUMER_SECRET') ?: ''; // set in server env — DO NOT hardcode

/* ========================= END CONFIG ========================== */


/* ------------------------- helpers ------------------------- */
function respond(int $status, array $body): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($body);
    exit;
}

function client_origin(): string {
    if (!empty($_SERVER['HTTP_ORIGIN'])) return $_SERVER['HTTP_ORIGIN'];
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $p = parse_url($_SERVER['HTTP_REFERER']);
        if ($p && isset($p['scheme'], $p['host'])) {
            return $p['scheme'] . '://' . $p['host'] . (isset($p['port']) ? ':' . $p['port'] : '');
        }
    }
    return '';
}

/* ------------------------- guards -------------------------- */
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(405, ['ok' => false, 'message' => 'Method not allowed.']);
}

$origin = client_origin();
if ($origin !== '' && !in_array($origin, ALLOWED_ORIGINS, true)) {
    respond(403, ['ok' => false, 'message' => 'Origin not allowed.']);
}
// Echo CORS header only for allowed same-brand origins (usually same-origin, so optional).
if (in_array($origin, ALLOWED_ORIGINS, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
}

/* --------------------- parse + validate -------------------- */
$raw   = file_get_contents('php://input') ?: '';
$data  = json_decode($raw, true);
if (!is_array($data)) {
    respond(400, ['ok' => false, 'message' => 'Invalid request.']);
}

$formId = isset($data['form_id']) ? preg_replace('/\D/', '', (string) $data['form_id']) : '';
$fields = (isset($data['fields']) && is_array($data['fields'])) ? $data['fields'] : [];

if (!isset($FIELD_MAPS[$formId])) {
    respond(400, ['ok' => false, 'message' => 'Unknown form.']);
}

// Honeypot: bots fill "website". Pretend success, do nothing.
if (!empty($fields['website'])) {
    respond(200, ['ok' => true, 'message' => 'Thank you! Your submission was received.']);
}

// Minimal server-side validation (defence in depth).
$email = trim((string) ($fields['email'] ?? ''));
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(422, ['ok' => false, 'message' => 'Please provide a valid email address.']);
}

// Build GF input map: "input_{id}" => value
$map    = $FIELD_MAPS[$formId];
$inputs = [];
foreach ($map as $friendly => $gfId) {
    if (!array_key_exists($friendly, $fields)) continue;
    $val = $fields[$friendly];
    if (is_string($val)) {
        $val = trim($val);
        // basic length cap to avoid abuse
        if (strlen($val) > 5000) $val = substr($val, 0, 5000);
    }
    $inputs['input_' . str_replace('.', '_', (string) $gfId)] = $val;
}
// Store address (form 36, field 34) for the REST fallback: pass the place_id
// (the site's snippet resolves it on submit) and the typed text as the street line.
if ($formId === '36') {
    if (!empty($fields['store_place_id'])) $inputs['input_34_place_id'] = trim((string) $fields['store_place_id']);
    if (!empty($fields['store_address']))  $inputs['input_34_1']       = trim((string) $fields['store_address']);
}

/* ===================== MODE A: WordPress + GFAPI ===================== */
$wpLoad = null;
foreach ([
    __DIR__ . '/wp-load.php',
    __DIR__ . '/../wp-load.php',
    __DIR__ . '/../../wp-load.php',
    ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/wp-load.php',
] as $candidate) {
    if ($candidate && is_readable($candidate)) { $wpLoad = $candidate; break; }
}

if ($wpLoad !== null) {
    // Bootstrap WP but skip theme setup for speed.
    if (!defined('SHORTINIT')) define('SHORTINIT', false);
    require_once $wpLoad;

    if (class_exists('GFAPI')) {
        // submit_form expects field values keyed by field id (e.g. "1.3", "2").
        $values = [];
        foreach ($map as $friendly => $gfId) {
            if (array_key_exists($friendly, $fields)) {
                $v = $fields[$friendly];
                $values[(string) $gfId] = is_string($v) ? trim($v) : $v;
            }
        }

        // Chick-fil-A Store Address (form 36, field 34). The custom autocomplete
        // sets a Google place_id; resolve it into the GF Address sub-inputs by
        // reusing the site's own snippet function — no extra API key needed here.
        if ($formId === '36') {
            $placeId = isset($fields['store_place_id']) ? trim((string) $fields['store_place_id']) : '';
            if ($placeId !== '') {
                // Satisfy the snippet's gform_validation if it runs during submit.
                $_POST['input_34_place_id'] = $placeId;
                if (function_exists('cfa_fetch_place_details')) {
                    $d = cfa_fetch_place_details($placeId);
                    if (is_array($d)) {
                        $values['34.1'] = $d['address'] ?? '';
                        $values['34.3'] = $d['city']    ?? '';
                        $values['34.4'] = $d['state']   ?? '';
                        $values['34.5'] = $d['zip']     ?? '';
                    }
                }
            }
            if (empty($values['34.1']) && !empty($fields['store_address'])) {
                $values['34.1'] = trim((string) $fields['store_address']); // fallback: raw text
            }
        }

        $result = GFAPI::submit_form((int) $formId, $values);

        if (is_wp_error($result)) {
            respond(502, ['ok' => false, 'message' => 'We could not submit your form right now. Please try again shortly.']);
        }
        if (is_array($result) && !empty($result['is_valid'])) {
            respond(200, [
                'ok'      => true,
                'message' => 'Thank you! Your submission was received — we’ll be in touch soon.',
            ]);
        }
        // Validation failed inside GF — surface a friendly message.
        respond(422, ['ok' => false, 'message' => 'Please check your details and try again.']);
    }
    // GF not active but WP loaded — fall through to REST as last resort.
}

/* ===================== MODE B: REST API fallback ===================== */
if ($GF_CONSUMER_KEY === '' || $GF_CONSUMER_SECRET === '') {
    respond(500, ['ok' => false, 'message' => 'Form handler is not fully configured. Please contact support.']);
}

$endpoint = rtrim(GF_SITE_BASE, '/') . '/wp-json/gf/v2/forms/' . $formId . '/submissions';
$ch = curl_init($endpoint);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query($inputs),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Basic ' . base64_encode($GF_CONSUMER_KEY . ':' . $GF_CONSUMER_SECRET),
    ],
]);
$response = curl_exec($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($response === false) {
    respond(502, ['ok' => false, 'message' => 'We could not reach the form service. Please try again shortly.']);
}

$json = json_decode($response, true);
if ($httpCode >= 200 && $httpCode < 300 && is_array($json) && (($json['is_valid'] ?? true) !== false)) {
    respond(200, [
        'ok'      => true,
        'message' => 'Thank you! Your submission was received — we’ll be in touch soon.',
    ]);
}

respond(422, ['ok' => false, 'message' => 'Please check your details and try again.']);
