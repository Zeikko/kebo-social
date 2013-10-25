<?php

if (!class_exists('Share_API')) {

    class Share_API
    {

        public function add_create_site()
        {
            add_action('shutdown', array($this, 'create_site'));
        }

        /**
         * Creates a site to the share api service.
         * After the site is created the service will start gathering share data.
         */
        public function create_site()
        {
            //TODO Create site only if it has not been created already
            
            $queried_object = get_queried_object();
            if (isset($queried_object->post_status) && isset($queried_object->guid)) {
                if ($queried_object->post_status == 'publish') {
                    $url = $queried_object->guid;

//                    $request_url = KBSO_API_URL;
                    //Temporary API URL
                    $request_url = "http://fb-sovellus.fi/share-api/sites/create";

                    $args = array(
                        'method' => 'POST',
                        'timeout' => 10,
                        'redirection' => 5,
                        'httpversion' => '1.1',
                        'blocking' => true,
                        'headers' => array(
                        ),
                        'body' => array(
                            'source' => 'kbso',
                            'url' => $url,
                        ),
                        'cookies' => array(),
                        'sslverify' => false,
                    );

                    $request = wp_remote_post(esc_url_raw($request_url), $args);
                    
                    //TODO Handle API error somehow
                }
            }
        }

    }

}