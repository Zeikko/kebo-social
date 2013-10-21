<?php
/**
 * View File - Twitter Feed Tweet Content
 */
?>

<li class="kfriend">

    <?php do_action( 'kbso_before_twitter_friends_friend', $friend, $instance, $widget_id ); ?>
    
    <a href="https://twitter.com/<?php echo $friend['screen_name']; ?>" title="<?php echo $friend['name']; ?> @<?php echo $friend['screen_name']; ?>" target="_blank">
        <img style="border-radius: 5px;" alt="<?php echo $friend['name']; ?>" src="<?php echo ( is_ssl() ) ? $friend['profile_image_url_https'] : $friend['profile_image_url'] ; ?>" />
    </a>
    
    <?php do_action( 'kbso_after_twitter_friends_friend', $friend, $instance, $widget_id ); ?>

</li>