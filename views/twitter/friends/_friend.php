<?php
/**
 * View File - Twitter Feed Tweet Content
 */
?>

<li class="ktweet" style="overflow: hidden; width: auto; margin-bottom: 10px;">

    <?php do_action( 'kbso_before_twitter_friends_friend', $tweet, $instance, $widget_id ); ?>
    
        
    
    <?php do_action( 'kbso_after_twitter_friends_friend', $tweet, $instance, $widget_id ); ?>

</li>