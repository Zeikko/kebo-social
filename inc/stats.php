<?php

/*
 * Statistics
 */

/*
 * TODO
 * 
 * Create DB Table
 * 
 * ID
 * Geo
 * IP
 * Website
 * Date/Time
 * LoggedIn
 * Post/Page ID
 * 
 * 
 */

function kebo_se_create_db_table() {

    global $wpdb;
    global $charset_collate;

    $table_name = $wpdb->prefix . 'kebo_se_stats';

    $table = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        referer varchar(255) NOT NULL,
        user_agent varchar(255) NOT NULL,
        visitor_ip varchar(15) NOT NULL,
        http_status varchar(3) NOT NULL,
        post_id mediumint(9) NOT NULL,
        user_id mediumint(9) NOT NULL,
        query_string varchar(255) NOT NULL,
        social varchar(20) NOT NULL,
        is_mobile varchar(20) NOT NULL,
        browser_name varchar(100) NOT NULL,
        browser_version varchar(100) NOT NULL,
        user_hash varchar(40) NOT NULL,
        PRIMARY KEY  (id),
        KEY user_id (user_id)
    ); $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
    dbDelta( $table );
    
}
register_activation_hook( __FILE__, 'kebo_se_create_db_table' );

function kebo_se_count_views() {

    if ( is_admin() )
        return;

    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
    } else {
        $user_id = 0;
    }

    // Ignore Firefox/Safari prefetching requests (X-Moz: Prefetch and X-purpose: Preview)
    if ( ( isset($_SERVER['HTTP_X_MOZ'] ) && ( strtolower( $_SERVER['HTTP_X_MOZ'] ) == 'prefetch' ) ) || ( isset( $_SERVER["HTTP_X_PURPOSE"] ) && ( strtolower( $_SERVER['HTTP_X_PURPOSE'] ) == 'preview' ) ) )
        return;

    // Get Visitors IP Address
    $user_ip = kebo_se_get_visitor_IP();

    global $post;
    
    // Get Post/Page ID or DO WHAT??? Cant use numbers, will conflict
    if ( is_single() || is_page() ) {
        
        $id = $post->ID;
        
    } elseif ( is_search() || is_archive() ) {
        
        $id = 'none';
        
    } elseif ( is_home ) {
        
        $id = 'none';
        
    } else {
        
        $id = 'none';
        
    }
    
    // Get HTTP Code
    $http_status = http_response_code();
    
    // Get Browser Info
    $browser = kebo_se_get_browser();
    
    $social = kebo_se_is_social_referer( $_SERVER['HTTP_REFERER'] );
    
    // Check if user is on mobile device
    if ( wp_is_mobile() )
        $is_mobile = 'true';
    else
        $is_mobile = 'false';
    
    // Prepare Visitor Info
    $visitor = array(
        'time' => date( 'Y-m-d H:i:s' ),
        'referer' => ( isset( $_SERVER['HTTP_REFERER'] ) ) ? substr( $_SERVER['HTTP_REFERER'], 0, 255 ) : 'none', // Where they came from
        'user_agent' => substr( $_SERVER['HTTP_USER_AGENT'], 0, 255 ), // User user-agent
        'remote_address' => $user_ip, // User IP
        'http_status' => $http_status, // doesnt always work?
        'post_id' => ( isset( $post->ID ) ) ? $post->ID : false,
        'user_id' => '',
        'query_string' => substr( $_SERVER['REQUEST_URI'], 0, 255 ),
        'social' => $social,
        'is_mobile' => $is_mobile,
        'browser_name' => $browser['name'],
        'browser_version' => $browser['version'],
        'user_hash' => hash( 'sha1', $user_ip . substr( $_SERVER['HTTP_USER_AGENT'], 0, 255 ) ),
    );
    
    global $wpdb;
    
    $operation = $wpdb->insert( 
	$wpdb->prefix . 'kebo_se_stats', 
	array( 
            'time' => $visitor['time'], 
            'referer' => $visitor['referer'],
            'user_agent' => $visitor['user_agent'],
            'visitor_ip' => $visitor['remote_address'],
            'http_status' => $visitor['http_status'],
            'post_id' => $visitor['post_id'],
            'user_id' => $visitor['user_id'],
            'query_string' => $visitor['query_string'],
            'social' => $visitor['social'],
            'is_mobile' => $visitor['is_mobile'],
            'browser_name' => $visitor['browser_name'],
            'browser_version' => $visitor['browser_version'],
            'user_hash' => $visitor['user_hash'],
	), 
	array( 
            '%s', 
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
	) 
    );
    
    // TODO: Handle Error Checking
    if ( $operation ) {
        // Insert was successful
    } else {
        // Not successful
    }

    
}
add_action( 'wp', 'kebo_se_count_views' );
// Change to 'shutdown' hook after live, using 'wp' for testing for errors

/*
 * Find the Visitors IP Address
 */
function kebo_se_get_visitor_IP() {

    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'];

    if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {

        $ip = $client;
        
    } elseif ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {

        $ip = $forward;
        
    } else {

        $ip = $remote;
        
    }

    return $ip;
    
}

function kebo_se_is_social_referer( $referer ) {
    
    if ( strpos( $referer, 'twitter.com' ) !== false ) {
        $social = 'twitter';
    } elseif ( strpos( $referer, 'facebook.com' ) !== false ) {
        $social = 'facebook';
    } elseif ( strpos( $referer, 'linkedin.com' ) !== false ) {
        $social = 'linkedin';
    } elseif ( strpos( $referer, 'plus.google.com' ) !== false ) {
        $social = 'googleplus';
    } elseif ( strpos( $referer, 'pinterest.com' ) !== false ) {
        $social = 'pinterest';
    } else {
        $social = 'false';
    }
    
    return $social;
    
}

function kebo_se_get_browser() {
    
    if ( isset( $_SERVER['HTTP_USER_AGENT'] ) OR ( '' != $_SERVER['HTTP_USER_AGENT'] ) ) {
        $visitor_user_agent = $_SERVER['HTTP_USER_AGENT'];
    } else {
        $visitor_user_agent = 'Unknown';
    }
    
    $bname = 'Unknown';
    $version = '0.0.0';
 
    // Next get the name of the useragent yes seperately and for good reason
    if ( preg_match( '/MSIE/i', $visitor_user_agent) && ! preg_match( '/Opera/i', $visitor_user_agent ) ) {
        $bname = 'Internet Explorer';
        $ub = 'MSIE';
    } elseif (preg_match('/Firefox/i', $visitor_user_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = 'Firefox';
    } elseif (preg_match('/Chrome/i', $visitor_user_agent)) {
        $bname = 'Google Chrome';
        $ub = 'Chrome';
    } elseif (preg_match('/Safari/i', $visitor_user_agent)) {
        $bname = 'Apple Safari';
        $ub = 'Safari';
    } elseif (preg_match('/Opera/i', $visitor_user_agent)) {
        $bname = 'Opera';
        $ub = 'Opera';
    } elseif (preg_match('/Netscape/i', $visitor_user_agent)) {
        $bname = 'Netscape';
        $ub = 'Netscape';
    } elseif (preg_match('/Seamonkey/i', $visitor_user_agent)) {
        $bname = 'Seamonkey';
        $ub = 'Seamonkey';
    } elseif (preg_match('/Konqueror/i', $visitor_user_agent)) {
        $bname = 'Konqueror';
        $ub = 'Konqueror';
    } elseif (preg_match('/Navigator/i', $visitor_user_agent)) {
        $bname = 'Navigator';
        $ub = 'Navigator';
    } elseif (preg_match('/Mosaic/i', $visitor_user_agent)) {
        $bname = 'Mosaic';
        $ub = 'Mosaic';
    } elseif (preg_match('/Lynx/i', $visitor_user_agent)) {
        $bname = 'Lynx';
        $ub = 'Lynx';
    } elseif (preg_match('/Amaya/i', $visitor_user_agent)) {
        $bname = 'Amaya';
        $ub = 'Amaya';
    } elseif (preg_match('/Omniweb/i', $visitor_user_agent)) {
        $bname = 'Omniweb';
        $ub = 'Omniweb';
    } elseif (preg_match('/Avant/i', $visitor_user_agent)) {
        $bname = 'Avant';
        $ub = 'Avant';
    } elseif (preg_match('/Camino/i', $visitor_user_agent)) {
        $bname = 'Camino';
        $ub = 'Camino';
    } elseif (preg_match('/Flock/i', $visitor_user_agent)) {
        $bname = 'Flock';
        $ub = 'Flock';
    } elseif (preg_match('/AOL/i', $visitor_user_agent)) {
        $bname = 'AOL';
        $ub = 'AOL';
    } elseif (preg_match('/AIR/i', $visitor_user_agent)) {
        $bname = 'AIR';
        $ub = 'AIR';
    } elseif (preg_match('/Fluid/i', $visitor_user_agent)) {
        $bname = 'Fluid';
        $ub = 'Fluid';
    } else {
        $bname = 'Unknown';
        $ub = 'Unknown';
    }
 
    // finally get the correct version number
    $known = array( 'Version', $ub, 'other' );
    $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if ( !preg_match_all( $pattern, $visitor_user_agent, $matches ) ) {
        // we have no matching number just continue
    }
 
    // see how many we have
    $i = count( $matches['browser'] );
    if ( $i != 1 ) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if ( strripos($visitor_user_agent, 'Version' ) < strripos( $visitor_user_agent, $ub ) ) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }
 
    // check if we have a number
    if ( $version == null || '' == $version ) {
        $version = '?';
    }
 
    return array(
        'userAgent' => $visitor_user_agent,
        'name' => $bname,
        'version' => $version,
        'pattern' => $pattern
    );
    
}

/*
 * Get Hex Color for given Browser Name
 */
function kebo_se_get_browser_color( $browser_name ) {
    
    switch ( $browser_name ) {
        
        case 'Google Chrome':
            $color = '#dd4b39';
            break;
        case 'Mozilla Firefox':
            $color = '#e55b0a';
            break;
        case 'Internet Explorer':
            $color = '#00ccff';
            break;
        case 'Apple Safari':
            $color = '#364ea6';
            break;
        default:
            $color = '#777';
            break;
        
    }
    
    return $color;
    
}