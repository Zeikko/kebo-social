<?php
/**
 * Template file to show Twitter Feed
 */

/**
 * TODO: Check if I should re-define the $view instance each time? More confusing/less confusing, etc?
 */
?>

<?php echo apply_filters( 'kbso_twitter_tweets_before_widget', $before_widget, $instance, $widget_id ); ?>

<?php do_action( 'kbso_before_twitter_tweets', $tweets, $instance, $widget_id ); ?>

<?php

/**
 * If the Title has been set, output it.
 */
if ( ! empty( $title ) ) {

    /**
     * Already contains: $widget_id, $friends, $instance, $before $before_title, $title, $after_title
     */
    $view
        ->set_view( '_title' )
        ->render();
    
}

?>

<ul id="<?php echo $widget_id; ?>" class="<?php echo implode( ' ', $classes ); ?>">
    
    <?php
    /**
     * Loop through each Tweet and render contents.
     */
    foreach ( $tweets as $tweet ) {

        if ( ! empty( $tweet['retweeted_status'] ) ) {
            $profile_image = ( is_ssl() ) ? $tweet['retweeted_status']['user']['profile_image_url_https'] : $tweet['retweeted_status']['user']['profile_image_url'];
        } else {
            $profile_image = ( is_ssl() ) ? $tweet['user']['profile_image_url_https'] : $tweet['user']['profile_image_url'];
        }
        
        /**
         * Already contains: $widget_id, $friends, $instance
         */
        $view
            ->set_view( '_tweet' )
            ->set( 'tweet', $tweet )
            ->set( 'profile_image', $profile_image )
            ->render();
                
    }
    
    ?>
    
</ul><!-- .ktweets -->

<?php do_action( 'kbso_after_twitter_tweets', $tweets, $instance, $widget_id ); ?>

<?php echo apply_filters( 'kbso_twitter_tweets_after_widget', $after_widget, $instance, $widget_id ); ?>