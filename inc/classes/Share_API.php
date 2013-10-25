<?php

if (!class_exists('Share_API')) {

    class Share_API
    {

        public static function add_create_site()
        {
            add_action('shutdown', array(Share_API, 'create_site'));
        }

        /**
         * Creates a site to the share api service.
         * After the site is created the service will start gathering share data.
         */
        public static function create_site()
        {
            //TODO Create site only if it has not been created already

            $queried_object = get_queried_object();
            if (isset($queried_object->post_status) && isset($queried_object->guid)) {
                if ($queried_object->post_status == 'publish') {
                    $url = $queried_object->guid;

//                    $request_url = KBSO_API_URL;
                    //Temporary API URL
                    $request_url = "http://fb-sovellus.fi/share-api/pages/create";

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
                            'key' => 'B97Pj0T02UNr2LuEm8lU2I2Qjnvlx17V',
                        ),
                        'cookies' => array(),
                        'sslverify' => false,
                    );

                    $request = wp_remote_post(esc_url_raw($request_url), $args);
                    //TODO Handle API error somehow
                }
            }
        }

        /**
         * Get share statistics of a url
         */
        public static function site()
        {
//          $request_url = KBSO_API_URL;
            //Temporary API URL
            $request_url = "http://fb-sovellus.fi/share-api/metrics/total/?" . http_build_query(array(
                        'key' => 'B97Pj0T02UNr2LuEm8lU2I2Qjnvlx17V',
                        'source' => 'kbso',
            ));

            $args = array(
                'method' => 'POST',
                'timeout' => 10,
                'redirection' => 5,
                'httpversion' => '1.1',
                'blocking' => true,
                'headers' => array(
                ),
                'body' => array(
                ),
                'cookies' => array(),
                'sslverify' => false,
            );

            $request = wp_remote_get(esc_url_raw($request_url), $args);
            $metrics = array();
            if(isset($request['body'])) {
                $metrics = $request['body'];
            }
            return $metrics;
        }

    }

}