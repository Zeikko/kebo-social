<?php
/**
 * View File - Twitter Feed Tweet Content
 */
?>

<li class="ktweet" style="overflow: hidden; width: auto; margin-bottom: 10px;">

    <?php do_action( 'kbso_before_twitter_tweets_tweet', $tweet, $instance, $widget_id ); ?>
    
    <div class="kheader" style="overflow: hidden;">

        <a class="kname" href="<?php echo esc_url( 'https://twitter.com/' . $tweet['user']['screen_name'] ); ?>" style="float: left; font-weight: bold; margin-right: 5px;">
            <?php echo esc_html( ( ! empty( $tweet['retweeted_status'] ) ) ? $tweet['retweeted_status']['user']['name'] : $tweet['user']['name'] ); ?>
        </a>

        <a class="kdate" style="float: right;">
            <?php echo esc_html( kbso_tweet_date( $tweet['created_at'] ) ); ?>
        </a>

        <a class="kscreen" href="https://twitter.com/<?php echo $tweet['user']['screen_name']; ?>" style="float: left; line-height: 1.6em; font-size: 0.8em;">
            @<?php echo esc_html( ( ! empty( $tweet['retweeted_status'] ) ) ? $tweet['retweeted_status']['user']['screen_name'] : $tweet['user']['screen_name'] ); ?>
        </a>

    </div>

    <div class="ktext">

        <?php if ( true == $instance['avatar'] ) { ?>
        
        <a class="kavatar" href="<?php echo esc_url ( 'https://twitter.com/' . $tweet['user']['screen_name'] ); ?>">

            <img src="<?php echo esc_url( $profile_image ); ?>" />

        </a>
        
        <?php } ?>

        <?php echo wp_kses_post( ( ! empty( $tweet['retweeted_status'] ) ) ? $tweet['retweeted_status']['text'] : $tweet['text'] ); ?>

    </div>

    <div class="kfooter">

        <?php if ( ! empty( $tweet['entities']['media'] ) && true == $instance['media'] ) : ?>
            <a class="ktogglemedia kclosed" href="#" data-id="<?php echo $tweet['id_str']; ?>"><span class="kshow" title="<?php _e('View Media', 'kbso'); ?>"><?php _e('View Media', 'kbso'); ?></span><span class="khide" title="<?php _e('Hide Media', 'kbso'); ?>"><?php _e('Hide Media', 'kbso'); ?></span></a>
        <?php endif; ?>

        <a class="kreply" title="<?php _e('Reply', 'kbso'); ?>" href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $tweet['id_str']; ?>"></a>
        <a class="kretweet" title="<?php _e('Re-Tweet', 'kbso'); ?>" href="https://twitter.com/intent/retweet?tweet_id=<?php echo ( isset($tweet['retweeted_status']) ) ? $tweet['retweeted_status']['id_str'] : $tweet['id_str']; ?>"></a>
        <a class="kfavorite" title="<?php _e('Favorite', 'kbso'); ?>" href="https://twitter.com/intent/favorite?tweet_id=<?php echo $tweet['id_str']; ?>"></a>

    </div>
    
    <?php
    /**
     * Check for Media attached to the Tweet and display.
     */
    if ( ! empty( $tweet['entities']['media'] ) && true == $instance['media'] ) {
    
        $view
            ->set_view( '_media' )
            ->set( 'instance', $instance )
            ->set( 'tweet', $tweet )
            ->render();
        
    }
    ?>
    
    <?php do_action( 'kbso_after_twitter_tweets_tweet', $tweet, $instance, $widget_id ); ?>

</li>