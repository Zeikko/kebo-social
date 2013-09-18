<?php
/*
 * Connects to the Kebo OAuth script.
 */

if ( ! class_exists( 'KeboSE_OAuth' ) ) :

    class KeboSE_OAuth {

        // URL of OAuth Script
        var $server = 'http://auth.kebopowered.com/request/index.php';
        
        // Query Parameters
        var $params = array();
        
        // Request Type
        var $type = 'POST';
        
        // HTTP Response
        var $response;

        /*
         * Makes the HTTP request, using the WP HTTP API
         */

        function make_request() {
            
            // Setup arguments for HTTP request.
            $args = array(
                'method' => $this->type,
                'timeout' => 5,
                'redirection' => 3,
                'httpversion' => '1.1',
                'blocking' => true,
                'headers' => array(),
                'body' => array(
                    'feed' => 'true',
                    'data' => json_encode( $this->params ),
                ),
                'cookies' => array(),
                'sslverify' => false,
            );

            // Make HTTP request to Kebo App.
            $this->response = wp_remote_post($this->server, $args);
            
        }
        
        /*
         * Set the Parameters
         */
        public function set($variable, $value) {
            
            $this->params[$variable] = $value;
            return $this;
            
        }

    }

endif;