<?php
/*
 * Plugin Name: Kebo Social
 * Plugin URI: http://kebopowered.com/plugins/kebo-social-engagement/
 * Description: User-friendly and Business focused social integration. Improve your business by leveraging social media.
 * Version: 0.1.0
 * Author: Kebo
 * Author URI: http://kebopowered.com
 * Text Domain: kebo-se
 * Domain Path: languages
 */

// Block direct access
if ( ! defined( 'ABSPATH' ) )
    die( 'Sorry, No Direct Access' );

define( 'KEBO_SE_VERSION', '0.1.0' );
define( 'KEBO_SE_URL', plugin_dir_url(__FILE__) );
define( 'KEBO_SE_PATH', plugin_dir_path(__FILE__) );

/*
 * Load textdomain early, as we need it for the PHP version check.
 */
function kebo_se_load_textdomain() {
    
    load_plugin_textdomain( 'kebo-se', false, KEBO_SE_PATH . '/languages' );
    
}
add_filter( 'wp_loaded', 'kebo_se_load_textdomain' );

/*
 * Check for the required version of PHP
 */
if ( version_compare( PHP_VERSION, '5.2', '<' ) ) {
    
    if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
        
        require_once ABSPATH . '/wp-admin/includes/plugin.php';
        deactivate_plugins(__FILE__);
        wp_die( __( 'Kebo Social Engagement requires PHP 5.2 or higher, as does WordPress 3.2 and higher.', 'kebo-se' ) );
        
    } else {
        
        return;
        
    }
    
}

/*
 * Load Relevant Internal Files
 */
function kebo_se_plugin_setup() {

    /*
     * Include Menu page.
     */
    require_once( KEBO_SE_PATH . 'inc/menu.php' );
    
    /*
     * Include oAuth Connection Functions.
     */
    require_once( KEBO_SE_PATH . 'inc/connections.php' );
    
    /*
     * Include Visitor Statistic Functions.
     */
    require_once( KEBO_SE_PATH . 'inc/stats.php' );
    
    /*
     * Include AJAX Functions.
     */
    require_once( KEBO_SE_PATH . 'inc/ajax.php' );
    
    /*
     * Include all Classes
     */
    foreach ( glob( KEBO_SE_PATH . 'inc/classes/*.php' ) as $filename ) {
        
        include_once( $filename );
        
    }
    
}
add_action( 'plugins_loaded', 'kebo_se_plugin_setup', 15 );

if ( !function_exists( 'kebo_se_scripts' ) ):

    /**
     * Register plugin scripts and styles.
     */
    function kebo_se_register_files() {

        // Register Styles
        wp_register_style( 'kebo-se-admin-css', KEBO_SE_URL . 'assets/css/admin.css', array(), KEBO_SE_VERSION, 'all' );
        
        // Register Scripts
        wp_register_script( 'kebo-se-admin-js', KEBO_SE_URL . 'assets/js/admin.js', array(), KEBO_SE_VERSION, true );
        wp_register_script( 'kebo-se-chart-js', KEBO_SE_URL . 'assets/js/vendor/Chart.labels.min.js', array('jquery'), KEBO_SE_VERSION, false );
        wp_register_script( 'kebo-se-flot-js', KEBO_SE_URL . 'assets/js/vendor/jquery.flot.min.js', array('jquery'), KEBO_SE_VERSION, false );
        wp_register_script( 'kebo-se-flot-cats-js', KEBO_SE_URL . 'assets/js/vendor/jquery.flot.categories.min.js', array('kebo-se-flot-js'), KEBO_SE_VERSION, false );
        wp_register_script( 'kebo-se-flot-resize-js', KEBO_SE_URL . 'assets/js/vendor/jquery.flot.resize.min.js', array('kebo-se-flot-js'), KEBO_SE_VERSION, false );
        wp_register_script( 'kebo-se-flot-pie-js', KEBO_SE_URL . 'assets/js/vendor/jquery.flot.pie.min.js', array('kebo-se-flot-js'), KEBO_SE_VERSION, false );
        
    }
    add_action('wp_enqueue_scripts', 'kebo_se_register_files');
    add_action('admin_enqueue_scripts', 'kebo_se_register_files');
    
    /**
     * Enqueue frontend plugin scripts and styles.
     */
    function kebo_se_enqueue_frontend() {

        // do stuff
        
    }
    add_action('wp_enqueue_scripts', 'kebo_se_enqueue_frontend');
    
    /**
     * Enqueue backend plugin scripts and styles.
     */
    function kebo_se_enqueue_backend( $hook_suffix ) {
        
        // Enqueue on all pages
        wp_enqueue_style( 'kebo-se-admin-css' );
        
        // Enqueue files for dashboard page
        if( 'toplevel_page_kebo-se-dashboard' == $hook_suffix ) {
            
            wp_enqueue_script( 'kebo-se-chart-js' );
            wp_enqueue_script( 'kebo-se-flot-js' );
            wp_enqueue_script( 'kebo-se-flot-cats-js' );
            wp_enqueue_script( 'kebo-se-flot-resize-js' );
            wp_enqueue_script( 'kebo-se-flot-pie-js' );
            wp_enqueue_script( 'jquery-ui-sortable' );
            
        }
        
        // Enqueue files for connections page
        if( 'kebo-social_page_kebo-se-connections' == $hook_suffix ) {
            
            wp_enqueue_script( 'kebo-se-admin-js' );
            
        }
        
        // Enqueue files for connections page
        if( 'kebo-social_page_kebo-se-sharing' == $hook_suffix ) {
            
            wp_enqueue_script( 'jquery-ui-sortable' );
            
        }
        
    }
    add_action('admin_enqueue_scripts', 'kebo_se_enqueue_backend');

endif;

/**
 * Add a link to the plugin screen, to allow users to jump straight to the settings page.
 */
function kebo_se_plugin_meta( $links ) {
    
    $links[] = '<a href="' . admin_url( 'options-general.php?page=kebo-twitter' ) . '">' . __( 'Settings', 'kebo-se' ) . '</a>';
    return $links;
    
}
add_filter( 'plugin_action_links_kebo-se/kebo-se.php', 'kebo_se_plugin_meta' ); 

/**
 * Adds a WordPress Pointer to Kebo SE options page.
 */
function kebo_se_pointer_script_style() {

    // Assume pointer shouldn't be shown
    $enqueue_pointer_script_style = false;

    // Get array list of dismissed pointers for current user and convert it to array
    $dismissed_pointers = explode(',', get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));

    // Check if our pointer is not among dismissed ones
    if ( !in_array('kebo_se_settings_pointer', $dismissed_pointers) ) {
        $enqueue_pointer_script_style = true;

        // Add footer scripts using callback function
        add_action('admin_print_footer_scripts', 'kebo_se_pointer_script_style');
    }

    // Enqueue pointer CSS and JS files, if needed
    if ($enqueue_pointer_script_style) {
        wp_enqueue_style('wp-pointer');
        wp_enqueue_script('wp-pointer');
    }
    
}
add_action('admin_enqueue_scripts', 'kebo_se_pointer_script_style');