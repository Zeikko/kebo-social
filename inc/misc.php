<?php
/**
 * Misc functions.
 */

/*
 * Returns Tweet date in Twitter Format
 */
if ( ! function_exists( 'kbso_tweet_date' ) ) {

    function kbso_tweet_date( $date ) {

        $format = get_option( 'date_format' );
        
        // Prepare Date Formats
        if ( date('Ymd') == date( 'Ymd', strtotime( $date ) ) ) {

            // Covert created at date into timeago format
            $created = human_time_diff( date( 'U', strtotime( $date ) ), current_time( 'timestamp', $gmt = 1 ) );
            
        } else {

            // Convert created at date into easily readable format.
            $created = date_i18n( $format, strtotime( $date ) );
            
        }
        
        return $created;
        
    }

}

/*
 * Prints Twitter Intent button javascript
 */
if ( ! function_exists( 'kbso_twitter_intent_js_print' ) ) {

    function kbso_twitter_intent_js_print() {
        
        // Begin Output Buffering
        ob_start();
        ?>

        <script type="text/javascript">
            
            /*
             * Capture Show/Hide photo link clicks, then show/hide the photo.
             */
            jQuery( '.ktweet .kfooter a:not(.ktogglemedia)' ).on( 'click', function(e) {

                // Prevent Click from Reloading page
                e.defaultPrevented();

                var khref = jQuery(this).attr('href');
                window.open( khref, 'twitter', 'width=600, height=400, top=0, left=0');

            });

        </script>

        <?php
        
        // End Output Buffering and Clear Buffer
        $output = ob_get_contents();
        ob_end_clean();
        
        echo $output;
        
    }

}