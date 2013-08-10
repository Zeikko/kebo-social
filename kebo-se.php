<?php
/*
 * Plugin Name: Kebo Social Engagement
 * Plugin URI: http://kebopowered.com/plugins/kebo-social-engagement/
 * Description: Description text stuff.
 * Version: 0.1.0
 * Author: Kebo
 * Author URI: http://kebopowered.com
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
    exit;

if ( ! defined( 'KEBO_SE_PLUGIN_VERSION' ) )
    define( 'KEBO_SE_PLUGIN_VERSION', '0.1.0' );
if ( ! defined( 'KEBO_SE_PLUGIN_URL' ) )
    define( 'KEBO_SE_PLUGIN_URL', plugin_dir_url(__FILE__) );
if ( ! defined( 'KEBO_SE_PLUGIN_PATH' ) )
    define( 'KEBO_SE_PLUGIN_PATH', plugin_dir_path(__FILE__) );

function kebo_twitter_plugin_setup() {

    /**
     * Include Plugin Options.
     */
    //require_once( KEBO_TWITTER_PLUGIN_PATH . 'inc/options.php' );

    /**
     * Load Text Domain for Translations.
     */
    load_plugin_textdomain( 'kebo-se', false, KEBO_TWITTER_PLUGIN_PATH . 'languages/' );
    
}
add_action( 'plugins_loaded', 'kebo_twitter_plugin_setup', 15 );