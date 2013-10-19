<?php
/**
 * View File - Twitter Feed Tweet Content
 */
?>

<li class="ktweet" style="overflow: hidden; width: auto; margin-bottom: 10px;">

    <div class="kheader" style="overflow: hidden;">

        <a class="kname" href="https://twitter.com/<?php echo $tweet['user']['screen_name']; ?>" style="float: left; font-weight: bold; margin-right: 5px;">
            <?php echo ( ! empty( $tweet['retweeted_status'] ) ) ? $tweet['retweeted_status']['user']['name'] : $tweet['user']['name']; ?>
        </a>

        <a class="kdate" style="float: right;">
            <?php echo kbso_tweet_date( $tweet['created_at'] ); ?>
        </a>

        <a class="kscreen" href="https://twitter.com/<?php echo $tweet['user']['screen_name']; ?>" style="float: left; line-height: 1.6em; font-size: 0.8em;">
            @<?php echo ( ! empty( $tweet['retweeted_status'] ) ) ? $tweet['retweeted_status']['user']['screen_name'] : $tweet['user']['screen_name']; ?>
        </a>

    </div>

    <div class="ktext">

        <a class="kavatar" href="https://twitter.com/<?php echo $tweet['user']['screen_name']; ?>" style="float: left; margin-top: 6px; margin-right: 10px; margin-bottom: 5px;">

            <img src="<?php if ( ! empty( $tweet['retweeted_status'] ) ) {
                echo ( is_ssl() ) ? $tweet['retweeted_status']['user']['profile_image_url_https'] : $tweet['retweeted_status']['user']['profile_image_url'];
            } else {
                echo ( is_ssl() ) ? $tweet['user']['profile_image_url_https'] : $tweet['user']['profile_image_url'];
            } ?>" />

        </a>

        <?php echo ( ! empty( $tweet['retweeted_status'] ) ) ? $tweet['retweeted_status']['text'] : $tweet['text']; ?>

    </div>

    <div class="kfooter">

        <?php if ( ! empty($tweet['entities']['media']) && true == $instance['media']) : ?>
            <a class="ktogglemedia kclosed" href="#" data-id="<?php echo $tweet['id_str']; ?>"><span class="kshow" title="<?php _e('View Media', 'kbso'); ?>"><?php _e('View Media', 'kbso'); ?></span><span class="khide" title="<?php _e('Hide Media', 'kbso'); ?>"><?php _e('Hide Media', 'kbso'); ?></span></a>
        <?php endif; ?>

        <a class="kreply" title="<?php _e('Reply', 'kbso'); ?>" href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $tweet['id_str']; ?>"></a>
        <a class="kretweet" title="<?php _e('Re-Tweet', 'kbso'); ?>" href="https://twitter.com/intent/retweet?tweet_id=<?php echo ( isset($tweet['retweeted_status']) ) ? $tweet['retweeted_status']['id_str'] : $tweet['id_str']; ?>"></a>
        <a class="kfavorite" title="<?php _e('Favorite', 'kbso'); ?>" href="https://twitter.com/intent/favorite?tweet_id=<?php echo $tweet['id_str']; ?>"></a>

    </div>

</li>