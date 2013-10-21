<?php
/**
 * View File - Twitter Feed Tweet Content
 */
?>

<li class="kfollower">

    <?php do_action( 'kbso_before_twitter_followers_follower', $follower, $instance, $widget_id ); ?>
    
    <a href="https://twitter.com/<?php echo $follower['screen_name']; ?>" title="<?php echo $follower['name']; ?> @<?php echo $follower['screen_name']; ?>" target="_blank">
        <img style="border-radius: 5px;" alt="<?php echo $follower['name']; ?>" src="<?php echo ( is_ssl() ) ? $follower['profile_image_url_https'] : $follower['profile_image_url'] ; ?>" />
    </a>
    
    <?php do_action( 'kbso_after_twitter_followers_follower', $follower, $instance, $widget_id ); ?>

</li>