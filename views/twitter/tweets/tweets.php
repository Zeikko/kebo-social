<?php
/**
 * Template file to show Twitter Feed
 */
?>

<?php echo $before_widget; ?>

<?php do_action( 'kbso_before_twitter_tweets', $tweets, $instance, $widget_id ); ?>

<?php

/**
 * If the Title has been set, output it.
 */
if ( ! empty( $title ) ) {

    $view
        ->set_view( '_title' )
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
    foreach ( $tweets as $tweet ) {

        $view
            ->set_view( '_tweet' )
            ->set( 'instance', $instance )
            ->set( 'widget_id', $widget_id )
            ->set( 'tweet', $tweet )
            ->render();
                
    }
    
    ?>
    
</ul><!-- .ktweets -->

<?php do_action( 'kbso_after_twitter_tweets', $tweets, $instance, $widget_id ); ?>

<?php echo $after_widget; ?>