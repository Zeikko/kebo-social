<?php
/**
 * Template file to show Twitter Feed
 */

/**
 * TODO: Check if I should re-define the $view instance each time? More confusing/less confusing, etc?
 */
?>

<?php echo $before_widget; ?>

<?php do_action( 'kbso_before_twitter_followers', $followers, $instance, $widget_id ); ?>

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

<ul class="kfollowers">
    
    <?php
    /**
     * Loop through each Tweet and render contents.
     */
    foreach ( $followers as $follower ) {
        
        /**
         * Already contains: $widget_id, $friends, $instance
         */
        $view
            ->set_view( '_follower' )
            ->set( 'follower', $follower )
            ->render();
                
    }
    ?>
    
</ul><!-- .ktweets -->

<?php do_action( 'kbso_after_twitter_followers', $followers, $instance, $widget_id ); ?>

<?php echo $after_widget; ?>