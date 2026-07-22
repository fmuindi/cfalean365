<?php
/**
 * ============================================================================
 * Chick-fil-A Autocomplete for Gravity Forms — CORRECTED
 * ----------------------------------------------------------------------------
 * Drop-in replacement for your existing "Chick-fil-A Autocomplete" Code Snippet.
 * Paste this over the old snippet's code (keep it a Code Snippets / WPCode PHP
 * snippet, "Run everywhere", Active) and RE-ENABLE it.
 *
 * WHAT WAS BROKEN (and is fixed here):
 *   1. The hidden field is named  input_<id>[place_id]  → PHP receives it as
 *      $_POST['input_<id>']['place_id'], but the old validation/pre-submission
 *      read $_POST['input_<id>_place_id'] (underscore). They never matched, so
 *      validation ALWAYS failed with "Please select a valid Chick-fil-A
 *      location…" and no form with the .cfa-field address could submit.
 *      → Fixed in cfa_validate_form() and cfa_populate_fields() (read the array).
 *   2. If the GF Address field is marked "Required", GF core flags it before our
 *      filter runs (its sub-inputs are empty until pre-submission). We now
 *      populate the sub-inputs AND clear the field's error inside validation, so
 *      it works whether or not the field is set Required in GF. (You may also
 *      simply untick "Required" on the address field — either way works now.)
 *   3. Google Maps JS is enqueued only if not already present (stops the
 *      "included multiple times" console warning).
 *
 * SETUP unchanged: add CSS class "cfa-field" to the GF Address field, and keep
 * your two Google API keys below.
 * ============================================================================
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// === CONFIGURATION === (keep your existing keys)
if ( ! defined( 'CFA_API_KEY' ) )        define( 'CFA_API_KEY', 'YOUR_BROWSER_API_KEY' );        // browser key (HTTP-referrer restricted)
if ( ! defined( 'CFA_SERVER_API_KEY' ) ) define( 'CFA_SERVER_API_KEY', 'YOUR_SERVER_API_KEY' );  // server key (IP / none restricted)
if ( ! defined( 'CFA_FIELD_CSS_CLASS' ) )define( 'CFA_FIELD_CSS_CLASS', 'cfa-field' );

// ===================================================
// 1. ENQUEUE GOOGLE PLACES API & INLINE OUR JAVASCRIPT
// ===================================================
add_action( 'wp_enqueue_scripts', 'cfa_enqueue_all' );
function cfa_enqueue_all() {
    // FIX #3: only load Maps if nothing else already did.
    if ( ! wp_script_is( 'google-places-api', 'enqueued' ) && ! wp_script_is( 'google-places-api', 'registered' ) ) {
        wp_enqueue_script(
            'google-places-api',
            'https://maps.googleapis.com/maps/api/js?key=' . CFA_API_KEY . '&libraries=places',
            [], null, false
        );
    }
    wp_enqueue_script( 'jquery' );
    add_action( 'wp_head', 'cfa_print_styles' );
    add_action( 'wp_footer', 'cfa_print_javascript', 100 );
}

// ===================================================
// 2. CSS
// ===================================================
function cfa_print_styles() { ?>
    <style>
    .cfa-input-wrapper { margin-bottom: 15px; position: relative; }
    .cfa-input-wrapper label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; }
    .cfa-autocomplete { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; box-sizing: border-box; font-family: inherit; }
    .cfa-autocomplete:focus { outline: none; border-color: #da291c; box-shadow: 0 0 0 3px rgba(218,41,28,.1); }
    .cfa-suggestions { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #ddd; border-top: none; border-radius: 0 0 4px 4px; max-height: 300px; overflow-y: auto; z-index: 999; list-style: none; margin: 4px 0 0 0; padding: 0; box-shadow: 0 4px 12px rgba(0,0,0,.15); }
    .cfa-suggestion-item { padding: 12px; cursor: pointer; border-bottom: 1px solid #f0f0f0; }
    .cfa-suggestion-item:last-child { border-bottom: none; }
    .cfa-suggestion-item:hover { background-color: #f5f5f5; }
    </style>
<?php }

// ===================================================
// 3. JAVASCRIPT (unchanged behavior)
// ===================================================
function cfa_print_javascript() {
    $ajax_url = admin_url( 'admin-ajax.php' );
    $nonce    = wp_create_nonce( 'cfa_nonce' );
    ?>
    <script>
    jQuery(document).ready(function($) {
        var ajaxUrl = <?php echo json_encode( $ajax_url ); ?>;
        var nonce   = <?php echo json_encode( $nonce ); ?>;

        function bind($input){
            var $wrapper = $input.closest('.cfa-input-wrapper');
            var $suggestions = $wrapper.find('.cfa-suggestions');
            var $placeIdField = $wrapper.find('.cfa-place-id');
            $input.on('input', function(){
                var value = $(this).val().trim();
                // typing invalidates a previous selection
                $placeIdField.val('');
                if (value.length < 3){ $suggestions.empty().hide(); return; }
                clearTimeout(window.cfaTimeout);
                window.cfaTimeout = setTimeout(function(){ fetchSuggestions(value,$input,$suggestions,$placeIdField); }, 300);
            });
            $suggestions.on('click', '.cfa-suggestion-item', function(){
                var placeId = $(this).data('place-id');
                $input.val('Loading...');
                fetchPlaceDetails(placeId,$input,$suggestions,$placeIdField);
            });
            $input.on('blur', function(){ setTimeout(function(){ $suggestions.empty().hide(); }, 150); });
        }
        $('.cfa-autocomplete').each(function(){ bind($(this)); });

        function fetchSuggestions(input,$input,$suggestions,$placeIdField){
            $.ajax({ url:ajaxUrl, type:'POST', data:{ action:'cfa_get_suggestions', input:input, nonce:nonce },
                success:function(response){
                    if(!response.success){ $suggestions.empty().hide(); return; }
                    var data = response.data.results;
                    if(!data.length){ $suggestions.html('<li style="padding:10px;color:#999;">No Chick-fil-A locations found</li>').show(); return; }
                    var html=''; $.each(data,function(i,item){ html+='<li class="cfa-suggestion-item" data-place-id="'+escapeHtml(item.place_id)+'">'+escapeHtml(item.description)+'</li>'; });
                    $suggestions.html(html).show();
                },
                error:function(){ $suggestions.empty().hide(); }
            });
        }
        function fetchPlaceDetails(placeId,$input,$suggestions,$placeIdField){
            $.ajax({ url:ajaxUrl, type:'POST', data:{ action:'cfa_get_place_details', place_id:placeId, nonce:nonce },
                success:function(response){
                    if(!response.success){ $input.val(''); alert('Error: '+response.data.message); return; }
                    var details=response.data;
                    $input.val(details.formatted);
                    $placeIdField.val(placeId);
                    $input.css('border-color','#28a745'); setTimeout(function(){ $input.css('border-color',''); },2000);
                    $suggestions.empty().hide();
                },
                error:function(){ $input.val(''); alert('Error retrieving location details'); }
            });
        }
        function escapeHtml(t){ var m={'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}; return String(t).replace(/[&<>"']/g,function(c){return m[c];}); }
    });
    </script>
    <?php
}

// ===================================================
// 4. RENDER: single input + hidden place_id
// ===================================================
add_filter( 'gform_field_content', 'cfa_replace_address_field', 10, 5 );
function cfa_replace_address_field( $content, $field, $value, $entry, $form ) {
    if ( $field->type !== 'address' || ! isset( $field->cssClass ) || strpos( $field->cssClass, CFA_FIELD_CSS_CLASS ) === false ) {
        return $content;
    }
    $field_id = 'input_' . $form['id'] . '_' . $field->id;
    return sprintf(
        '<div class="cfa-input-wrapper">
            <label for="%s"><strong style="color:#da291c;">Chick-fil-A</strong> Store Address</label>
            <input type="text" id="%s" name="input_%d[0]" class="cfa-autocomplete large" placeholder="Type store address..." autocomplete="off" />
            <ul class="cfa-suggestions" style="display:none;"></ul>
            <input type="hidden" class="cfa-place-id" name="input_%d[place_id]" />
        </div>',
        esc_attr( $field_id ), esc_attr( $field_id ), (int) $field->id, (int) $field->id
    );
}

// ===================================================
// 5 & 6. AJAX endpoints (unchanged)
// ===================================================
add_action( 'wp_ajax_nopriv_cfa_get_suggestions', 'cfa_ajax_get_suggestions' );
add_action( 'wp_ajax_cfa_get_suggestions', 'cfa_ajax_get_suggestions' );
function cfa_ajax_get_suggestions() {
    check_ajax_referer( 'cfa_nonce', 'nonce' );
    $input = sanitize_text_field( $_POST['input'] ?? '' );
    if ( strlen( $input ) < 3 ) { wp_send_json_error( [ 'message' => 'Type at least 3 characters' ] ); }
    $result = cfa_get_suggestions_from_api( $input );
    wp_send_json_success( [ 'results' => $result['results'], 'debug' => $result['debug'] ] );
}
function cfa_get_suggestions_from_api( $input ) {
    $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';
    $params = [ 'input' => 'Chick-fil-A ' . $input, 'key' => CFA_SERVER_API_KEY, 'types' => 'establishment' ];
    $response = wp_remote_get( $url . '?' . http_build_query( $params ) );
    if ( is_wp_error( $response ) ) { return [ 'results' => [], 'debug' => [ 'error' => $response->get_error_message() ] ]; }
    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    $status = $data['status'] ?? 'unknown';
    if ( $status !== 'OK' && $status !== 'ZERO_RESULTS' ) {
        return [ 'results' => [], 'debug' => [ 'status' => $status, 'error_message' => $data['error_message'] ?? null ] ];
    }
    $results = [];
    foreach ( $data['predictions'] ?? [] as $p ) {
        $normalized = strtolower( str_replace( [ '-', ' ' ], '', $p['description'] ) );
        if ( strpos( $normalized, 'chickfila' ) !== false ) {
            $results[] = [ 'place_id' => $p['place_id'], 'description' => $p['description'] ];
        }
    }
    return [ 'results' => $results, 'debug' => [ 'status' => $status ] ];
}

add_action( 'wp_ajax_nopriv_cfa_get_place_details', 'cfa_ajax_get_place_details' );
add_action( 'wp_ajax_cfa_get_place_details', 'cfa_ajax_get_place_details' );
function cfa_ajax_get_place_details() {
    check_ajax_referer( 'cfa_nonce', 'nonce' );
    $place_id = sanitize_text_field( $_POST['place_id'] ?? '' );
    if ( empty( $place_id ) ) { wp_send_json_error( [ 'message' => 'Invalid selection' ] ); }
    $details = cfa_fetch_place_details( $place_id );
    if ( empty( $details ) ) { wp_send_json_error( [ 'message' => 'Location not found or not a valid Chick-fil-A' ] ); }
    wp_send_json_success( $details );
}
function cfa_fetch_place_details( $place_id ) {
    $url = 'https://maps.googleapis.com/maps/api/place/details/json';
    $params = [ 'place_id' => $place_id, 'key' => CFA_SERVER_API_KEY, 'fields' => 'formatted_address,address_components,name' ];
    $response = wp_remote_get( $url . '?' . http_build_query( $params ) );
    if ( is_wp_error( $response ) ) { return null; }
    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! isset( $data['status'] ) || $data['status'] !== 'OK' ) { return null; }
    $result = $data['result'];
    if ( stripos( $result['name'], 'chick-fil-a' ) === false ) { return null; }
    $parts = [];
    foreach ( $result['address_components'] as $c ) {
        if ( in_array( 'street_number', $c['types'] ) || in_array( 'route', $c['types'] ) ) { $parts['street'] = ( $parts['street'] ?? '' ) . ' ' . $c['long_name']; }
        if ( in_array( 'locality', $c['types'] ) ) { $parts['city'] = $c['long_name']; }
        if ( in_array( 'administrative_area_level_1', $c['types'] ) ) { $parts['state'] = $c['short_name']; }
        if ( in_array( 'postal_code', $c['types'] ) ) { $parts['zip'] = $c['long_name']; }
    }
    return [
        'place_id' => $place_id,
        'address'  => trim( $parts['street'] ?? '' ),
        'city'     => $parts['city'] ?? '',
        'state'    => $parts['state'] ?? '',
        'zip'      => $parts['zip'] ?? '',
        'formatted'=> $result['formatted_address'],
    ];
}

/**
 * Read the selected place_id from the array-named hidden input:
 *   <input name="input_34[place_id]"> → $_POST['input_34']['place_id']
 */
function cfa_get_posted_place_id( $field_id ) {
    $key = 'input_' . $field_id;
    if ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) && isset( $_POST[ $key ]['place_id'] ) ) {
        return sanitize_text_field( $_POST[ $key ]['place_id'] );
    }
    // legacy/flat fallback, just in case
    return sanitize_text_field( $_POST[ 'input_' . $field_id . '_place_id' ] ?? '' );
}

// ===================================================
// 7. VALIDATION — FIXED: read array place_id, populate sub-inputs, clear error
// ===================================================
add_filter( 'gform_validation', 'cfa_validate_form' );
function cfa_validate_form( $validation_result ) {
    $form = $validation_result['form'];

    foreach ( $form['fields'] as &$field ) {
        if ( $field->type !== 'address' || ! isset( $field->cssClass ) || strpos( $field->cssClass, CFA_FIELD_CSS_CLASS ) === false ) {
            continue;
        }

        $place_id = cfa_get_posted_place_id( $field->id );

        if ( empty( $place_id ) ) {
            $field->failed_validation  = true;
            $field->validation_message = 'Please select a valid Chick-fil-A location from the dropdown suggestions.';
            continue;
        }

        $details = cfa_fetch_place_details( $place_id );
        if ( empty( $details ) ) {
            $field->failed_validation  = true;
            $field->validation_message = 'That address could not be verified as a Chick-fil-A. Please choose one from the suggestions.';
            continue;
        }

        // Valid selection → fill the real GF Address sub-inputs now so GF core
        // required-validation passes and the entry stores the address…
        $_POST[ 'input_' . $field->id . '_1' ] = $details['address'];
        $_POST[ 'input_' . $field->id . '_2' ] = '';
        $_POST[ 'input_' . $field->id . '_3' ] = $details['city'];
        $_POST[ 'input_' . $field->id . '_4' ] = $details['state'];
        $_POST[ 'input_' . $field->id . '_5' ] = $details['zip'];

        // …and clear any error GF already set on this field.
        $field->failed_validation  = false;
        $field->validation_message = '';
    }
    unset( $field );

    // Recompute overall validity from all fields.
    $is_valid = true;
    foreach ( $form['fields'] as $f ) {
        if ( ! empty( $f->failed_validation ) ) { $is_valid = false; break; }
    }
    $validation_result['is_valid'] = $is_valid;
    $validation_result['form']     = $form;
    return $validation_result;
}

// ===================================================
// 8. PRE-SUBMISSION — FIXED read (belt & suspenders)
// ===================================================
add_filter( 'gform_pre_submission', 'cfa_populate_fields' );
function cfa_populate_fields( $form ) {
    foreach ( $form['fields'] as $field ) {
        if ( $field->type !== 'address' || ! isset( $field->cssClass ) || strpos( $field->cssClass, CFA_FIELD_CSS_CLASS ) === false ) {
            continue;
        }
        $place_id = cfa_get_posted_place_id( $field->id );
        if ( empty( $place_id ) ) { continue; }
        $details = cfa_fetch_place_details( $place_id );
        if ( empty( $details ) ) { continue; }
        $_POST[ 'input_' . $field->id . '_1' ] = $details['address'];
        $_POST[ 'input_' . $field->id . '_2' ] = '';
        $_POST[ 'input_' . $field->id . '_3' ] = $details['city'];
        $_POST[ 'input_' . $field->id . '_4' ] = $details['state'];
        $_POST[ 'input_' . $field->id . '_5' ] = $details['zip'];
    }
    return $form;
}
