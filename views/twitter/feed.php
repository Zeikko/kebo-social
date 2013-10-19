<?php
/**
 * Template file to show Twitter Feed
 */
?>

<?php do_action( 'kbso_before_twitter_feed', $tweets, $instance ); ?>

<?php if ( isset( $tweets[0]['created_at'] ) ) : ?>

    <ul class="kebo-tweets dark ktweets">

        <?php for ( $c = 0; $c < $count; ++$c ) : ?>

            <li class="ktweet" style="overflow: hidden; width: auto; margin-bottom: 10px;">                
                
                <div class="kheader" style="overflow: hidden;">
                    
                    <a class="kname" href="https://twitter.com/<?php echo $tweets[$c]['user']['screen_name']; ?>" style="float: left; font-weight: bold; margin-right: 5px;">
                        <?php echo ( ! empty( $tweets[$c]['retweeted_status'] ) ) ? $tweets[$c]['retweeted_status']['user']['name'] : $tweets[$c]['user']['name'] ; ?>
                    </a>
                    
                    <a class="kdate" style="float: right;">
                        <?php echo kbso_tweet_date( $tweets[$c]['created_at'] ); ?>
                    </a>
                    
                    <a class="kscreen" href="https://twitter.com/<?php echo $tweets[$c]['user']['screen_name']; ?>" style="float: left; opacity: 0.7;">
                        @<?php echo ( ! empty( $tweets[$c]['retweeted_status'] ) ) ? $tweets[$c]['retweeted_status']['user']['screen_name'] : $tweets[$c]['user']['screen_name'] ; ?>
                    </a>
                    
                </div>
                
                <div class="ktext">
                    
                    <a class="kavatar" href="https://twitter.com/<?php echo $tweets[$c]['user']['screen_name']; ?>" style="float: left; margin-top: 6px; margin-right: 10px; margin-bottom: 5px;">
                        
                        <img src="<?php if ( ! empty( $tweets[$c]['retweeted_status'] ) ) { echo ( is_ssl() ) ? $tweets[$c]['retweeted_status']['user']['profile_image_url_https'] : $tweets[$c]['retweeted_status']['user']['profile_image_url'] ; } else { echo ( is_ssl() ) ? $tweets[$c]['user']['profile_image_url_https'] : $tweets[$c]['user']['profile_image_url'] ; } ?>" />
                        
                    </a>
                    
                    <?php echo ( ! empty( $tweets[$c]['retweeted_status'] ) ) ? $tweets[$c]['retweeted_status']['text'] : $tweets[$c]['text'] ; ?>
                    
                </div>
                
                <div class="kfooter">
                    
                    <?php if ( ! empty( $tweets[$c]['entities']['media'] ) && true == $instance['media'] ) : ?>
                        <a class="ktogglemedia kclosed" href="#" data-id="<?php echo $tweets[$c]['id_str']; ?>"><span class="kshow" title="<?php _e('View Media', 'kbso'); ?>"><?php _e('View Media', 'kbso'); ?></span><span class="khide" title="<?php _e('Hide Media', 'kbso'); ?>"><?php _e('Hide Media', 'kbso'); ?></span></a>
                    <?php endif; ?>
                        
                    <a class="kreply" title="<?php _e('Reply', 'kbso'); ?>" href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $tweets[$c]['id_str']; ?>"></a>
                    <a class="kretweet" title="<?php _e('Re-Tweet', 'kbso'); ?>" href="https://twitter.com/intent/retweet?tweet_id=<?php echo ( isset( $tweets[$c]['retweeted_status'] ) ) ? $tweets[$c]['retweeted_status']['id_str'] : $tweets[$c]['id_str'] ; ?>"></a>
                    <a class="kfavorite" title="<?php _e('Favorite', 'kbso'); ?>" href="https://twitter.com/intent/favorite?tweet_id=<?php echo $tweets[$c]['id_str']; ?>"></a>
                
                </div>

            </li>

        <?php endfor; ?>

    </ul>

<?php else: ?>

    <?php _e('Sorry, no Tweets found.', 'kbso'); ?>

<?php endif; ?>

<?php do_action( 'kbso_after_twitter_feed', $tweets, $instance ); ?>