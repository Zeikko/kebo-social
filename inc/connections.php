<?php
/*
 * OAuth Connection Functions
 */

/*
 * Collect returned OAuth2 credentials on callback and save in a transient.
 */
function kebo_se_create_connection() {

    if ( isset( $_GET['service'] ) && isset( $_GET['token'] ) && isset( $_GET['account_name'] ) && isset( $_GET['_wpnonce'] ) ) {

        if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'kebo-new-connection' ) ) {
            
            // Nonce not verified.
            add_settings_error(
                    'kebo-se-connections',
                    esc_attr( 'settings_updated' ),
                    __( 'No valid nonce was supplied.', 'kebo-se' ),
                    'error'
            );
            
            return;
            
        }
        
        if ( false === ( $data = get_option( 'kebo_se_connections' ) ) )
            $data = array();

        // Prepare Connection Info
        $args = array(
            'user_id' => wp_get_current_user()->ID,
            'service' => strtolower( $_GET['service'] ), // Service Name
            'account_name' => $_GET['account_name'], // Account Name
            'account_id' => ( isset($_GET['account_id']) ) ? $_GET['account_id'] : false, // User ID
            'account_link' => ( isset($_GET['account_link']) ) ? $_GET['account_link'] : false,
            'token' => $_GET['token'], // OAuth Token
            'secret' => ( isset($_GET['secret']) ) ? $_GET['secret'] : false, // OAuth Secret
            'expires' => ( isset($_GET['expires']) ) ? $_GET['expires'] : false, // Expire Time
            'shared' => false,
        );

        $found = false;

        /*
         * Check to see if Connection already exists
         */
        if ( is_array( $data ) ) {

            foreach ( $data as $conn ) {

                if ( $args['service'] == $conn['service'] && $args['account_id'] == $conn['account_id'] ) {
                    $found = true;
                    
                }
                
            }
            
        }

        // If Connection was not already found, Add it.
        if ( true == $found ) {

            // Connection already exists, let user know.
            add_settings_error(
                    'kebo-se-connections',
                    esc_attr( 'settings_updated' ),
                    __( 'This connection already exists.', 'kebo-se' ),
                    'error'
            );
            
        } else {

            // Not found, so add connection.
            $data[] = $args;

            // Let user know we successfully received and stored their credentials.
            add_settings_error(
                    'kebo-se-connections',
                    esc_attr( 'settings_updated' ),
                    __( 'Connection established.', 'kebo-se' ),
                    'updated'
            );
            
        }

        // Store Website OAuth Credentials in transient
        update_option( 'kebo_se_connections', $data );
        
    }
    
}
add_action( 'admin_init', 'kebo_se_create_connection' );

/*
 * Get a Social Connection
 */
function kebo_se_get_connection( $id = null, $service = null ) {

    if ( ! false == ( $data = get_option( 'kebo_se_connections' ) ) ) {
    
        // Prepare variable to hold connection array key if found.
        $item = false;

        /*
         * Loop connections to check for matching connection
         */
        foreach ( $data as $key => $conn ) {

            if ( ( $service == strtolower( $conn['service'] ) ) && ( $id == $conn['account_id'] ) ) {

                $item = $key;

            }

        }

        /*
         * Check if connection was found
         */
        if ( is_numeric( $item ) ) {

            // Only return first item in array, as we are only fetching one item.
            $connection = array_slice( $data, $item, 1 )[0];
            
            return $connection;

        } else {

            // Not found.
            return false;

        }
    
    }
    
}

/*
 * Delete a Social Connection
 */
function kebo_se_delete_connection() {

    if ( isset( $_GET['action']) && 'delete' == $_GET['action'] && isset( $_GET['service'] ) && isset( $_GET['account_id'] ) ) {

        if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'kebo-delete-connection' ) ) {
            return;
        }
        
        if ( ! false == ( $data = get_option( 'kebo_se_connections' ) ) ) {

            // Prepare variable to hold connection array key if found.
            $item = false;

            /*
             * Loop connections to check for matching connection
             */
            foreach ( $data as $key => $conn ) {

                if ( ( $_GET['service'] == strtolower( $conn['service'] ) ) && ( $_GET['account_id'] == $conn['account_id'] ) ) {

                    $item = $key;
                    
                }
                
            }

            /*
             * Check if connection was found
             */
            if ( is_numeric( $item ) ) {

                array_splice( $data, $item, 1 );

                update_option( 'kebo_se_connections', $data );

                add_settings_error(
                        'kebose-connections',
                        esc_attr('settings_updated'),
                        __('Connection removed.', 'kebo-se'),
                        'updated'
                );
                
            } else {

                add_settings_error(
                        'kebose-connections',
                        esc_attr('settings_updated'),
                        __('Connection not found.', 'kebo-se'),
                        'updated'
                );
                
            }
            
        }
        
    }
    
}
add_action( 'admin_init', 'kebo_se_delete_connection' );

/*
 * Toggle Share Status of Social Connections
 */
function kebo_se_share_connection() {

    if ( isset( $_GET['action'] ) && 'share' == $_GET['action'] && isset( $_GET['service'] ) && isset( $_GET['account_id'] ) ) {

        if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'kebo-share-connection' ) ) {
            return;
        }
        
        if ( ! false == ( $data = get_option( 'kebo_se_connections' ) ) ) {

            // Prepare variable to hold connection array key if found.
            $item = false;

            /*
             * Loop connections to check for matching connection
             */
            foreach ( $data as $key => $conn ) {

                if ( ( $_GET['service'] == strtolower( $conn['service'] ) ) && ( $_GET['account_id'] == $conn['account_id'] ) ) {

                    $item = $key;
                    
                }
            }

            /*
             * Check if connection was found
             */
            if ( is_numeric( $item ) ) {

                if ( true == $data[$item]['shared'] ) {
                    
                    $data[$item]['shared'] = false;
                    
                } else {
                    
                    $data[$item]['shared'] = true;
                    
                }

                update_option( 'kebo_se_connections', $data );

                add_settings_error(
                        'kebose-connections',
                        esc_attr('settings_updated'),
                        __('Connection updated.', 'kebo-se'),
                        'updated'
                );
                
            } else {

                add_settings_error(
                        'kebose-connections',
                        esc_attr('settings_updated'),
                        __('Connection not found.', 'kebo-se'),
                        'updated'
                );
                
            }
            
        }
        
    }
    
}
add_action( 'admin_init', 'kebo_se_share_connection' );

/*
 * Collect returned OAuth2 credentials on callback and save in a transient.
 */

function kebo_se_create_connection_button($service, $account_name, $account_id, $account_link, $shared) {

    // Prepare Service variable
    $service = strtolower($service);

    // Prepare Container Classes
    $main_classes = array(
        'kconnection',
        $service,
    );

    // Prepare Icon Class
    switch ($service) {

        case 'dribbble':
            $icon_class = 'icon-dribbble';
            break;
        case 'dropbox':
            $icon_class = 'icon-dropbox';
            break;
        case 'facebook':
            $icon_class = 'icon-facebook';
            break;
        case 'flickr':
            $icon_class = 'icon-flickr';
            break;
        case 'foursquare':
            $icon_class = 'icon-foursquare';
            break;
        case 'google':
            $icon_class = 'icon-google-plus';
            break;
        case 'linkedin':
            $icon_class = 'icon-linkedin';
            break;
        case 'tumblr':
            $icon_class = 'icon-tumblr';
            break;
        case 'twitter':
            $icon_class = 'icon-twitter';
            break;
        default:
            $icon_class = 'icon-info';
            break;
    }
    
    ?>

    <div class="<?php echo implode(' ', $main_classes); ?>" data-service="<?php echo $service; ?>" data-account="<?php echo $account_id; ?>">
        <a class="kservice" href="<?php if ($account_link) { ?>http://<?php echo $account_link; } else { echo '#'; } ?>" <?php if ($account_link) { ?> target="_blank" <?php } ?>><i class="<?php echo $icon_class; ?>"></i> <?php if ('twitter' == $service) { echo '@'; } ?><?php echo $account_name; ?></a>
        <a class="kdisconnect" title="Disconnect Service" href="<?php echo admin_url('admin.php?page=kebo-se-connections'); ?>&action=delete&service=<?php echo $service; ?>&account_id=<?php echo $account_id; ?>&_wpnonce=<?php echo wp_create_nonce( 'kebo-delete-connection' ); ?>">&times;</a>
        <a class="kshare<?php if ('true' == $shared) { echo ' enabled'; } ?>" title="Share Service" href="<?php echo admin_url('admin.php?page=kebo-se-connections'); ?>&action=share&service=<?php echo $service; ?>&account_id=<?php echo $account_id; ?>&_wpnonce=<?php echo wp_create_nonce( 'kebo-share-connection' ); ?>"><?php _e('Share', 'kebo-se'); ?></a>
    </div>

    <?php
    
}

function kebo_se_print_connections() {

    /*
     * Output Relevant Social Connections
     */
    if ( ! false == ( $data = get_option( 'kebo_se_connections' ) ) ) {
        
        // Get columns we want to sort
        foreach ($data as $key => $row) {
            $service[$key]  = $row['service'];
            $name[$key] = $row['account_name'];
        }
        
        // Sort Array by Service then Account Name
        array_multisort($service, SORT_ASC, $name, SORT_ASC, $data);
        
        $userid = wp_get_current_user()->ID;

        foreach ($data as $conn) {

            if ($userid == $conn['user_id'] || true == $conn['shared']) {

                kebo_se_create_connection_button(
                        $conn['service'], // Service
                        $conn['account_name'], // Account Name
                        $conn['account_id'], // Account ID
                        $conn['account_link'], // Account URL
                        $conn['shared'] // Shared
                );
                
            }
            
        }
        
    } else {

        echo '<p>';
        _e('Ready to get started? Click on any of the large social service buttons to create your first connection.', 'kebo-se');
        echo '</p>';
        
    }
    
}

/*
 * AJAX Social Connections Share Status Toggle
 */
function kebo_se_share_connection_callback() {
    
    // Create Array of POST data
    $post_data = $_POST;
    
    if ( ! false == ( $data = get_option( 'kebo_se_connections' ) ) ) {

            // Prepare variable to hold connection array key if found.
            $item = false;

            /*
             * Loop connections to check for matching connection
             */
            foreach ( $data as $key => $conn ) {

                if ( ( $post_data['service'] == strtolower( $conn['service'] ) ) && ( $post_data['account_id'] == $conn['account_id'] ) ) {

                    $item = $key;
                }
            }

            /*
             * Check if connection was found
             */
            if ( is_numeric( $item ) ) {

                if ( true == $data[$item]['shared'] ) {
                    
                    $data[$item]['shared'] = false;
                    
                } else {
                    
                    $data[$item]['shared'] = true;
                    
                }

                update_option( 'kebo_se_connections', $data );
                
                $response = array(
                    'action' => 'share',
                    'success' => 'true',
                );
                
            } else {
                
                $response = array(
                    'action' => 'share',
                    'success' => 'false',
                );
                
            }
            
        }
    
    print_r( json_encode($response) );
    
    die();
    
}
add_action('wp_ajax_kebo_se_share_connection', 'kebo_se_share_connection_callback');

/*
 * AJAX Social Delete Connection
 */
function kebo_se_delete_connection_callback() {
    
    // Create Array of POST data
    $post_data = $_POST;
    
    if ( ! false == ( $data = get_option( 'kebo_se_connections' ) ) ) {

        // Prepare variable to hold connection array key if found.
        $item = false;

        /*
         * Loop connections to check for matching connection
         */
        foreach ( $data as $key => $conn ) {

            if ( ( $post_data['service'] == strtolower( $conn['service'] ) ) && ( $post_data['account_id'] == $conn['account_id'] ) ) {

                $item = $key;
                
            }
            
        }

        /*
         * Check if connection was found
         */
        if ( is_numeric( $item ) ) {

            array_splice( $data, $item, 1 );

            update_option( 'kebo_se_connections', $data );

            $response = array(
                'action' => 'delete',
                'success' => 'true',
            );
                
        } else {

            $response = array(
                'action' => 'delete',
                'success' => 'false',
            );
                
        }
            
    }
    
    print_r( json_encode( $response ) );
    
    die();
    
}
add_action( 'wp_ajax_kebo_se_delete_connection', 'kebo_se_delete_connection_callback' );