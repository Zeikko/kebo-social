<?php

/*
 * Class to handle Social API requests.
 */

if ( ! class_exists( 'Kbso_Api' ) ) {

    class Kbso_Api {
        
        /**
         * Social Accounts
         * @var array
         */
        public $accounts = array();
        
        /**
         * Social Service
         * @var string
         */
        public $service;
        
        /**
         * Type of data
         * @var string
         */
        public $type;
        
        /**
         * Tweet Data
         * @var array
         */
        public $tweets = array();
        
        /**
         * Transients to Refresh
         * @var array
         */
        public $refresh = array();

        /**
         * Constructor Method
         */
        public function __construct() {
            
            add_action( 'shutdown', array( $this, 'refresh_cache' ) );
            
        }
        
        /**
         * Get Social Service
         * 
         * @param type $service
         * @return \Kbso_Api
         */
        public function set_service( $service ) {
            
            $this->service = $service;
            
            return $this;
            
        }
        
        /**
         * Get Type of Data
         * 
         * @param type $type
         * @return \Kbso_Api
         */
        public function set_type( $type ) {
            
            $this->type = $type;
            
            return $this;
            
        }
        
        /**
         * Get Social Accounts
         * 
         * @param type $accounts
         * @return \Kbso_Api
         */
        public function set_accounts( $accounts ) {
            
            $this->accounts = $accounts;
            
            return $this;
            
        }

        /**
         * Collect and organize specified social data.
         * 
         * @param type $accounts
         * @param type $service
         * @param type $type
         * @return boolean
         */
        public function get_data() {

            $accounts = $this->accounts;
            
            if ( isset( $accounts[0]['account_id'] ) ) {

                $combined_data = array();
                $count = 0;

                foreach ( $accounts as $account ) {
                    
                    $count++;

                    if ( false !== ( $data = get_transient( 'kbso_' . $this->service . '_' . $this->type . '_' . $account['account_id'] . get_current_blog_id() ) ) ) {

                        // Decode to Assoc Array
                        $data = json_decode( $data, true );
                        
                        if ( time() > $data['expiry'] ) {
                            
                            $transient = array(
                                'service' => $this->service,
                                'type' => $this->type,
                                'account' => $account,
                                'blog_id' => get_current_blog_id(),
                            );
                            
                            array_push( $this->refresh, $transient );
                            
                            $data['expiry'] = ( time() + 30 );

                            set_transient( 'kbso_' . $this->service . '_' . $this->type . '_' . $account['account_id'] . get_current_blog_id(), json_encode( $data ), 24 * HOUR_IN_SECONDS );
                            
                        }
                        
                    } else {

                        $data = $this->request( $account );

                        $data['expiry'] = ( time() + ( 1 * MINUTE_IN_SECONDS ) );

                        set_transient( 'kbso_' . $this->service . '_' . $this->type . '_' . $account['account_id'] . get_current_blog_id(), json_encode( $data ), 24 * HOUR_IN_SECONDS );
                        
                    }
                    
                    unset( $data['expiry'] );

                    // Add Social Data Together
                    $combined_data = array_merge( $combined_data, $data );
                    
                }

                /*
                 * Sort Combined Data by Timestamp
                 */
                if ( 1 < $count ) {
                    
                    /*
                     * Sort Tweets by date created.
                     */
                    $combined_data = $this->tweet_data_sort( $combined_data );
                    
                }

                return $combined_data;
                
            } else {

                return false;
                
            }
            
        }

        /**
         * Makes Request to Kebo Social API.
         * 
         * @param type $account
         * @param type $service
         * @param type $type
         * @return type
         */
        public function request( $account ) {

            // URL to Kebo Social OAuth Request App
            $request_url = KBSO_API_URL;

            // Setup arguments for OAuth request.
            $data = array(
                'service' => $this->service, // Social Service
                'type' => $this->type, // Type of data to request
                'account' => ( isset( $account['account_name'] ) ) ? $account['account_name'] : null, // Service Account Name
                'userid' => $account['account_id'], // Service User ID
                'token' => $account['token'], // OAuth Token
                'secret' => $account['secret'], // OAuth Secret
            );

            // Setup arguments for POST request.
            $args = array(
                'method' => 'POST',
                'timeout' => 10,
                'redirection' => 5,
                'httpversion' => '1.1',
                'blocking' => true,
                'headers' => array(),
                'body' => array(
                    'source' => 'kbso',
                    'data' => json_encode($data),
                ),
                'cookies' => array(),
                'sslverify' => false,
            );

            // Make POST request to Kebo OAuth App.
            $request = wp_remote_post( $request_url, $args );

            /*
             * Do Error Handling
             */
            if ( ! is_wp_error( $request ) ) {

                $tweets = json_decode( $request['body'], true );
                
                $data = $this->twitter_linkify( $tweets );

                return $data;
                
            } else {

                return $request;
                
            }
            
        }

        /**
         * Sort Tweets by timestamp
         */
        private function tweet_data_sort( $tweets ) {

            // Obtain a list of created dates as timestamps
            foreach ( $tweets as $key => $row ) {
                        
                $date[$key]  = strtotime( $row['created_at'] );
                        
            }

            // Sort the data by created date descending
            // Add $tweets as the last parameter, to sort by the common key
            array_multisort( $date, SORT_DESC, SORT_NUMERIC, $tweets );
            
            return $tweets;
            
        }

        /**
         * Converts Tweet text urls, account names and hashtags into HTML links.
         * 
         * @param array $tweets
         * @return array $tweets
         */
        public function twitter_linkify( $tweets ) {

            foreach ( $tweets as $key => $tweet ) {

                /*
                 * Check if it is the Tweet text or Re-Tweet text which we need to pre-process.
                 */
                if ( ! empty( $tweets[$key]['retweeted_status'] ) ) {
                    
                    /*
                     * Decode HTML Chars like &#039; to '
                     */
                    $tweets[$key]['retweeted_status']['text'] = htmlspecialchars_decode( $tweets[$key]['retweeted_status']['text'], ENT_QUOTES );

                    /*
                     * Turn Hasntags into HTML Links
                     */
                    $tweets[$key]['retweeted_status']['text'] = preg_replace( '/#([A-Za-z0-9\/\.]*)/', '<a href="http://twitter.com/search?q=$1">#$1</a>', $tweets[$key]['retweeted_status']['text'] );

                    /*
                     * Turn Mentions into HTML Links
                     */
                    $tweets[$key]['retweeted_status']['text'] = preg_replace( '/@([A-Za-z0-9_\/\.]*)/', '<a href="http://www.twitter.com/$1">@$1</a>', $tweets[$key]['retweeted_status']['text'] );

                    /*
                     * Linkify text URLs
                     */
                    $tweets[$key]['retweeted_status']['text'] = make_clickable( $tweets[$key]['retweeted_status']['text'] );

                    /*
                     * Add target="_blank" to all links
                     */
                    $tweets[$key]['retweeted_status']['text'] = links_add_target( $tweets[$key]['retweeted_status']['text'], '_blank', array('a') );
                    
                } else {

                    /*
                     * Decode HTML Chars like &#039; to '
                     */
                    $tweets[$key]['text'] = htmlspecialchars_decode( $tweets[$key]['text'], ENT_QUOTES );

                    /*
                     * Turn Hasntags into HTML Links
                     */
                    $tweets[$key]['text'] = preg_replace( '/#([A-Za-z0-9\/\.]*)/', '<a href="http://twitter.com/search?q=$1">#$1</a>', $tweets[$key]['text'] );

                    /*
                     * Turn Mentions into HTML Links
                     */
                    $tweets[$key]['text'] = preg_replace( '/@([A-Za-z0-9_\/\.]*)/', '<a href="http://www.twitter.com/$1">@$1</a>', $tweets[$key]['text'] );

                    /*
                     * Linkify text URLs
                     */
                    $tweets[$key]['text'] = make_clickable( $tweets[$key]['text'] );

                    /*
                     * Add target="_blank" to all links
                     */
                    $tweets[$key]['text'] = links_add_target( $tweets[$key]['text'], '_blank', array('a') );
                    
                }
                
            }

            return $tweets;
            
        }
        
        /**
         * Refreshes any transients which have soft expired.
         * @uses $refresh
         */
        public function refresh_cache() {
            
            if ( ! empty( $this->refresh ) ) {
            
                /*
                 * Loop through each transient
                 */
                foreach ( $this->refresh as $transient ) {
                    
                    /*
                     * Check if we are already updating.
                     */
                    if ( get_transient( 'kbso_update_is_running' . get_current_blog_id() ) ) {
                        die();
                    }

                    /*
                     * Create hash of the current time (nothing else should occupy the same microtime).
                     */
                    $hash = hash( 'sha1', microtime() );

                    /*
                     * Set transient to show we are updating and set the hash for this specific thread.
                     */
                    set_transient( 'kbso_update_is_running' . get_current_blog_id(), $hash, 5 );

                    /*
                     * Sleep so that other threads at the same point can set the hash
                     */
                    usleep( 250000 ); // Sleep for 1/4th of a second

                    /*
                     * Only one thread will have the same hash as is stored in the transient now, all others can die.
                     */
                    if ( get_transient( 'kbso_update_is_running' . get_current_blog_id() ) && ( get_transient( 'kbso_update_is_running' . get_current_blog_id() ) != $hash ) ) {
                        die();
                    }

                    /*
                     * Prepare data
                     */
                    $this->set_service( $transient['service'] );
                    $this->set_type( $transient['type'] );
                    $account = $transient['account'];
                    
                    /*
                     * Make the API Request
                     */
                    $data = $this->request( $account );
                    
                    /*
                     * Add soft expiry time
                     */
                    $data['expiry'] = ( time() + ( 1 * MINUTE_IN_SECONDS ) );

                    /*
                     * Update transient
                     */
                    set_transient( 'kbso_' . $this->service . '_' . $this->type . '_' . $account['account_id'] . get_current_blog_id(), json_encode( $data ), 24 * HOUR_IN_SECONDS );

                    // Unset data before next loop
                    unset( $data );
                    
                    // Delete update transient now we are finished.
                    delete_transient( 'kbso_update_is_running' . get_current_blog_id() );

                }
            
            }
            
        }

    }

}
