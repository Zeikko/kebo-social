<?php
/*
 * Plugin Name: Kebo Social
 * Plugin URI: http://kebopowered.com/plugins/kebo-social/
 * Description: User-friendly and Business focused social integration. Improve your business by leveraging social media.
 * Version: 0.1.0
 * Author: Kebo
 * Author URI: http://kebopowered.com
 * Text Domain: kebo-so
 * Domain Path: languages
 */

// Block direct access
if ( ! defined( 'ABSPATH' ) )
    die( 'Sorry, no direct access.' );

define( 'KBSO_VERSION', '0.1.0' );
define( 'KBSO_URL', plugin_dir_url(__FILE__) );
define( 'KBSO_PATH', plugin_dir_path(__FILE__) );
define( 'KBSO_API_URL', 'http://auth.kebopowered.com/request/request.php' );

/*
 * Load textdomain early, as we need it for the PHP version check.
 */
function kbso_load_textdomain() {
    
    load_plugin_textdomain( 'kbso', false, KEBO_SE_PATH . '/languages' );
    
}
add_filter( 'wp_loaded', 'kbso_load_textdomain' );

/*
 * Check for the required version of PHP
 */
if ( version_compare( PHP_VERSION, '5.2', '<' ) ) {
    
    if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
        
        require_once ABSPATH . '/wp-admin/includes/plugin.php';
        deactivate_plugins(__FILE__);
        wp_die( __( 'Kebo Social Engagement requires PHP 5.2 or higher, as does WordPress 3.2 and higher.', 'kbso' ) );
        
    } else {
        
        return;
        
    }
    
}

/*
 * Load Relevant Internal Files
 */
function kbso_plugin_setup() {

    /*
     * Include Menu page.
     */
    require_once( KBSO_PATH . 'inc/menu.php' );
    
    /*
     * Include oAuth Connection Functions.
     */
    require_once( KBSO_PATH . 'inc/connections.php' );
    
    /*
     * Include Visitor Statistic Functions.
     */
    require_once( KBSO_PATH . 'inc/stats.php' );
    
    /*
     * Include AJAX Functions.
     */
    require_once( KBSO_PATH . 'inc/ajax.php' );
    
    /*
     * Include Misc Functions.
     */
    require_once( KBSO_PATH . 'inc/misc.php' );
    
    /*
     * Include all Classes
     */
    foreach ( glob( KBSO_PATH . 'inc/classes/*.php' ) as $filename ) {
        
        include_once( $filename );
        
    }
    
    /*
     * Include all Widget files
     */
    $widgets = array( 'twitter', 'facebook', 'linkedin', 'pinterest' );
    
    foreach ( $widgets as $widget ) {
        
        require_once( KBSO_PATH . 'inc/widgets/' . $widget . '.php' );
        
    }
    
    unset( $widgets );
    
    /*
     * Include and Activates Updater
     */
    require_once( KBSO_PATH . 'inc/updater/wp-updates-plugin.php' );
    new WPUpdatesPluginUpdater_234( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__) );
    
}
add_action( 'plugins_loaded', 'kbso_plugin_setup', 15 );

if ( ! function_exists( 'kbso_register_files' ) ) :

    /**
     * Register plugin scripts and styles.
     */
    function kbso_register_files() {

        // Register Styles
        wp_register_style( 'kbso-admin-css', KBSO_URL . 'assets/css/admin.css', array(), KBSO_VERSION, 'all' );
        
        // Register Scripts
        wp_register_script( 'kbso-admin-js', KBSO_URL . 'assets/js/admin.js', array(), KBSO_VERSION, true );
        wp_register_script( 'kbso-flot-js', KBSO_URL . 'assets/js/vendor/jquery.flot.min.js', array('jquery'), KBSO_VERSION, false );
        wp_register_script( 'kbso-flot-cats-js', KBSO_URL . 'assets/js/vendor/jquery.flot.categories.min.js', array('kbso-flot-js'), KBSO_VERSION, false );
        wp_register_script( 'kbso-flot-resize-js', KBSO_URL . 'assets/js/vendor/jquery.flot.resize.min.js', array('kbso-flot-js'), KBSO_VERSION, false );
        wp_register_script( 'kbso-flot-pie-js', KBSO_URL . 'assets/js/vendor/jquery.flot.pie.min.js', array('kbso-flot-js'), KBSO_VERSION, false );
        
    }
    add_action('wp_enqueue_scripts', 'kbso_register_files');
    add_action('admin_enqueue_scripts', 'kbso_register_files');
    
endif;
    
    /**
     * Enqueue frontend plugin scripts and styles.
     */
    function kbso_enqueue_frontend() {

        // do stuff
        
    }
    add_action('wp_enqueue_scripts', 'kbso_enqueue_frontend');
    
    /**
     * Enqueue backend plugin scripts and styles.
     */
    function kbso_enqueue_backend( $hook_suffix ) {
        
        // Enqueue on all pages
        wp_enqueue_style( 'kbso-admin-css' );
        
        // Enqueue files for dashboard page
        if ( 'toplevel_page_kbso-dashboard' == $hook_suffix ) {
            
            wp_enqueue_script( 'kbso-flot-js' );
            wp_enqueue_script( 'kbso-flot-cats-js' );
            wp_enqueue_script( 'kbso-flot-resize-js' );
            wp_enqueue_script( 'kbso-flot-pie-js' );
            wp_enqueue_script( 'jquery-ui-sortable' );
            
        }
        
        // Enqueue files for connections page
        if ( 'kebo-social_page_kbso-connections' == $hook_suffix ) {
            
            wp_enqueue_script( 'kbso-admin-js' );
            
        }
        
        // Enqueue files for sharing page
        if ( 'kebo-social_page_kbso-sharing' == $hook_suffix ) {
            
            wp_enqueue_script( 'jquery-ui-sortable' );
            
        }
        
    }
    add_action( 'admin_enqueue_scripts', 'kbso_enqueue_backend' );

/**
 * Add a link to the plugin screen, to allow users to jump straight to the settings page.
 */
function kbso_plugin_meta( $links ) {
    
    $links[] = '<a href="' . admin_url( 'options-general.php?page=kebo-twitter' ) . '">' . __( 'Settings', 'kbso' ) . '</a>';
    return $links;
    
}
add_filter( 'plugin_action_links_kebo-so/kebo-so.php', 'kbso_plugin_meta' ); 

/**
 * Adds a WordPress Pointer to Kebo SE options page.
 */
function kbso_pointer_script_style() {

    // Assume pointer shouldn't be shown
    $enqueue_pointer_script_style = false;

    // Get array list of dismissed pointers for current user and convert it to array
    $dismissed_pointers = explode( ',', get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

    // Check if our pointer is not among dismissed ones
    if ( ! in_array( 'kbso_settings_pointer', $dismissed_pointers ) ) {
        $enqueue_pointer_script_style = true;

        // Add footer scripts using callback function
        add_action( 'admin_print_footer_scripts', 'kbso_pointer_script_style' );
    }

    // Enqueue pointer CSS and JS files, if needed
    if ( $enqueue_pointer_script_style ) {
        wp_enqueue_style( 'wp-pointer' );
        wp_enqueue_script( 'wp-pointer' );
    }
    
}
add_action( 'admin_enqueue_scripts', 'kbso_pointer_script_style' );