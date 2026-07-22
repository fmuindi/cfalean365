<?php
/**
 * ============================================================================
 * CFA LEAN365 — Form handler as a WordPress REST endpoint (RECOMMENDED)
 * ----------------------------------------------------------------------------
 * Paste this into a NEW PHP snippet in "WPCode" or "Code Snippets" and ACTIVATE
 * it (run everywhere / site-wide). No file upload, no API keys in the page.
 *
 * It exposes:  POST /wp-json/cfa/v1/submit
 * The homepage posts JSON here; this runs GravityForms server-side via GFAPI
 * (full validation, notifications, confirmations). The Chick-fil-A store-address
 * field (form 38, field 34) is resolved from the Google place_id by REUSING your
 * existing snippet's cfa_fetch_place_details() — so no Google key is needed here.
 *
 * In index.html / page-lean365.php set:
 *   window.CFA_CONFIG.formEndpoint = "/wp-json/cfa/v1/submit";
 * ============================================================================
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'rest_api_init', function () {
    register_rest_route( 'cfa/v1', '/submit', [
        'methods'             => 'POST',
        'callback'            => 'cfa_rest_submit',
        'permission_callback' => '__return_true', // public form; protected by honeypot + validation below
    ] );
} );

function cfa_rest_submit( WP_REST_Request $request ) {

    // Field maps: friendly name (from the HTML "name") => GravityForms field ID.
    $maps = [
        '38' => [ // CFA Pilot Interest Form
            'first_name' => '3.3',
            'last_name'  => '3.6',
            'email'      => '4',
            'phone'      => '23',
            'message'    => '32',
            // store address (field 34, Address) handled below via place_id
        ],
        '37' => [ // Support Contact Form
            'first_name' => '1.3',
            'last_name'  => '1.6',
            'email'      => '2',
            'subject'    => '4',
            'message'    => '3',
        ],
    ];

    $data   = $request->get_json_params();
    if ( ! is_array( $data ) ) {
        return new WP_REST_Response( [ 'ok' => false, 'message' => 'Invalid request.' ], 400 );
    }
    $formId = isset( $data['form_id'] ) ? preg_replace( '/\D/', '', (string) $data['form_id'] ) : '';
    $fields = ( isset( $data['fields'] ) && is_array( $data['fields'] ) ) ? $data['fields'] : [];

    if ( ! isset( $maps[ $formId ] ) ) {
        return new WP_REST_Response( [ 'ok' => false, 'message' => 'Unknown form.' ], 400 );
    }

    // Honeypot — bots fill "website". Pretend success.
    if ( ! empty( $fields['website'] ) ) {
        return new WP_REST_Response( [ 'ok' => true, 'message' => 'Thank you! Your submission was received.' ], 200 );
    }

    // Basic email check (defence in depth).
    $email = trim( (string) ( $fields['email'] ?? '' ) );
    if ( $email === '' || ! is_email( $email ) ) {
        return new WP_REST_Response( [ 'ok' => false, 'message' => 'Please provide a valid email address.' ], 422 );
    }

    if ( ! class_exists( 'GFAPI' ) ) {
        return new WP_REST_Response( [ 'ok' => false, 'message' => 'Forms are temporarily unavailable. Please email support@cfalean365.com.' ], 500 );
    }

    // Build GFAPI values keyed by field id.
    $values = [];
    foreach ( $maps[ $formId ] as $friendly => $gfId ) {
        if ( array_key_exists( $friendly, $fields ) ) {
            $v = $fields[ $friendly ];
            $values[ (string) $gfId ] = is_string( $v ) ? substr( trim( $v ), 0, 5000 ) : $v;
        }
    }

    // Chick-fil-A store address (form 38, field 34): resolve the Google place_id
    // into the Address sub-inputs, reusing your autocomplete snippet's function.
    if ( $formId === '38' ) {
        $placeId = isset( $fields['store_place_id'] ) ? trim( (string) $fields['store_place_id'] ) : '';
        if ( $placeId !== '' ) {
            $_POST['input_34_place_id'] = $placeId; // satisfies the snippet's validation if it runs
            if ( function_exists( 'cfa_fetch_place_details' ) ) {
                $d = cfa_fetch_place_details( $placeId );
                if ( is_array( $d ) ) {
                    $values['34.1'] = $d['address'] ?? '';
                    $values['34.3'] = $d['city']    ?? '';
                    $values['34.4'] = $d['state']   ?? '';
                    $values['34.5'] = $d['zip']     ?? '';
                }
            }
        }
        if ( empty( $values['34.1'] ) && ! empty( $fields['store_address'] ) ) {
            $values['34.1'] = trim( (string) $fields['store_address'] );
        }
    }

    $result = GFAPI::submit_form( (int) $formId, $values );

    if ( is_wp_error( $result ) ) {
        return new WP_REST_Response( [ 'ok' => false, 'message' => 'We could not submit your form right now. Please try again shortly.' ], 502 );
    }
    if ( is_array( $result ) && ! empty( $result['is_valid'] ) ) {
        return new WP_REST_Response( [ 'ok' => true, 'message' => 'Thank you! Your submission was received — we’ll be in touch soon.' ], 200 );
    }
    return new WP_REST_Response( [ 'ok' => false, 'message' => 'Please check your details and try again.' ], 422 );
}
