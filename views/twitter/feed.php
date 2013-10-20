<?php
/**
 * Template file to show Twitter Feed
 */
?>

<?php do_action( 'kbso_before_twitter_feed', $tweets, $instance, $widget_id ); ?>

<?php

/**
 * If the Title has been set, output it.
 */
if ( ! empty( $title ) ) {

    $view
        ->set_view( '_feed-title' )
        ->set( 'instance', $instance )
        ->set( 'widget_id', $widget_id )
        ->set( 'before_title', $before_title )
        ->set( 'title', $title )
        ->set( 'after_title', $after_title )
        ->render();
    
}

?>

<ul class="kebo-tweets dark ktweets">
    
    <?php
    /**
     * Loop through each Tweet and render contents.
     */
    foreach ( $tweets as $key => $tweet ) {
            
        // TODO: remove this once number of tweets is processed when fetching data
        if ( $key == $count ) {
            break;
        }

        $view
            ->set_view( '_feed-tweet' )
            ->set( 'instance', $instance )
            ->set( 'tweet', $tweet )
            ->render();
                
    }
    
    ?>
    
</ul><!-- .ktweets -->

<?php do_action( 'kbso_after_twitter_feed', $tweets, $instance, $widget_id );
