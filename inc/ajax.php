<?php
/*
 * Contains all AJAX functions.
 */

/*
 * AJAX Save Dashboard Positions
 */
function kebo_se_save_dashboard_config() {
    
    // Get the action
    $action = $_POST['action'];
    
    // Get the data
    $data = $_POST['data'];
    
    // Get current users ID
    $user_id = $_POST['user_id'];
    
    // Get nonce
    $nonce = $_POST['nonce'];
    
    /*
     * Check action
     */
    if ( 'kebo_se_save_dashboard_config' !== $action )
        die();
    
    /*
     * Check nonce
     */
    if ( ! wp_verify_nonce( $nonce, 'kebo_se_dash_config' ) )
        die();
    
    /*
     * Check user_id
     */
    if ( empty( $user_id ) || ( 0 == $user_id ) )
        die();
    
    // Save Dashboard Positions
    $test = update_user_meta( $user_id, 'kebo_se_dashboard_config', $data );
    
    // Send successful response
    $response = array(
        'action' => 'save',
        'success' => 'true',
    );
    
    // Clear and previous output, like errors.
    ob_clean();
    
    // Output response
    print_r( json_encode( $response ) );
    
    // Send Output
    die();
    
}
add_action( 'wp_ajax_kebo_se_save_dashboard_config', 'kebo_se_save_dashboard_config' );