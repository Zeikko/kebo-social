<?php
/*
 * Twitter Widget
 * Supports Tweet Feed and Follower List types
 */

/*
 * Check a Twitter account exists.
 */
$connections = get_option( 'kebo_se_connections' );

$found = false;

foreach ( $connections as $connection ) {

    if ( 'Twitter' == $connection['service'] ) {

        $found[] = $connection;
    }
    
}

// We only need Twitter connections now.
$twitter_accounts = $found;

/*
 * Only register Widget if connection has been made to our Twitter App.
 */
if ( ! empty( $twitter_accounts ) ) {

    add_action( 'widgets_init', 'kebo_se_twitter_register_widget' );

    function kebo_se_twitter_register_widget() {

        register_widget( 'KeboSE_Twitter_Widget' );
        
    }

}

class KeboSE_Twitter_Widget extends WP_Widget {

    function KeboSE_Twitter_Widget() {

        $widget_ops = array(
            'classname' => 'kebo_se_twitter_widget',
            'description' => __( 'Displays many types of Twitter data.', 'kebo-se' )
        );

        $this->WP_Widget(
            false,
            __( 'Kebo Social - Twitter', 'kebo-se' ),
            $widget_ops
        );
        
    }

    /*
     * Outputs Content
     */
    function widget( $args, $instance ) {

        extract($args, EXTR_SKIP);

        // Enqueue Style Sheet
        //wp_enqueue_style('kebo-twitter-plugin');

        /*
         * Get tweets from transient and refresh if its expired.
         */
        if (false === ( $tweets = kebo_twitter_get_tweets() ))
            return;

        // Ensure not undefined for updates
        if (!isset($instance['conversations']))
            $instance['conversations'] = false;

        // Ensure not undefined for updates
        if (!isset($instance['media']))
            $instance['media'] = false;

        // Ensure not undefined for updates
        if (!isset($instance['display']))
            $instance['display'] = 'tweets';

        // Output opening Widget HTML
        echo $before_widget;

        // If Title is set, output it with Widget title opening and closing HTML
        if (isset($instance['title']) && !empty($instance['title'])) {

            echo $before_title;
            echo $instance['title'];
            echo $after_title;
        }

        /*
         * Check which Style (Slider/List) has been chosen and use correct view file, default List.
         */
        if (2 == $instance['style']) {

            require( KEBO_TWITTER_PLUGIN_PATH . 'views/slider.php' );
        } else {

            require( KEBO_TWITTER_PLUGIN_PATH . 'views/list.php' );
        }

        // Output closing Widget HTML
        echo $after_widget;
    }

    /*
     * Outputs Options Form
     */

    function form($instance) {
        ?>

        <?php
        // Add defaults.
        if (!isset($instance['account']))
            $instance['account'] = null;
        if (!isset($instance['account']))
            $instance['account'] = 'null';
        if (!isset($instance['count']))
            $instance['count'] = 5;
        if (!isset($instance['avatar']))
            $instance['avatar'] = '';
        if (!isset($instance['style']))
            $instance['style'] = 1;
        if (!isset($instance['theme']))
            $instance['theme'] = 'light';
        if (!isset($instance['title']))
            $instance['title'] = '';
        if (!isset($instance['conversations']))
            $instance['conversations'] = false;
        if (!isset($instance['media']))
            $instance['media'] = false;
        if (!isset($instance['display']))
            $instance['display'] = 'tweets';
        ?>

        <?php
        $connections = get_option( 'kebo_se_connections' );

        foreach ( $connections as $connection ) {

            if ( 'Twitter' == $connection['service'] ) {

                $twitter_accounts[] = $connection;
            }

        }
        
        add_action( 'admin_print_footer_scripts', array( $this, 'print_js' ) );

        ?>
        <?php if ( ! empty( $twitter_accounts ) ) { ?>
        <label for="<?php echo $this->get_field_id('account'); ?>">
            <p>
                <?php _e('Account', 'kebo-se'); ?>:
                <select style="width: 100%;" id="<?php echo $this->get_field_id('account') ?>" name="<?php echo $this->get_field_name('account'); ?>">
                    <?php
                    foreach ( $twitter_accounts as $account ) {
                        
                        ?>
                        <option value="<?php echo $account['account_id']; ?>" <?php if ( $account['account_name'] == $instance['account']) { echo 'selected="selected"'; } ?>>@<?php echo $account['account_name']; ?></option>
                        <?php
                        
                    }
                    ?>
                </select>
            </p>
        </label>
        <?php } ?>

        <label for="<?php echo $this->get_field_id('title'); ?>">
            <p><?php _e('Title', 'kebo_twitter'); ?>: <input style="width: 100%;" type="text" value="<?php echo $instance['title']; ?>" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>"></p>
        </label>

        <label for="<?php echo $this->get_field_id('type'); ?>">
            <p>
                <?php _e('Type', 'kebo-se'); ?>:
                <select style="width: 100%;" id="<?php echo $this->get_field_id('type') ?>" name="<?php echo $this->get_field_name('type'); ?>">
                    <option value="null" <?php if ( 'null' == $instance['type'] ) { echo 'selected="selected"'; } ?>></option>
                    <option value="feed" <?php if ( 'feed' == $instance['type'] ) { echo 'selected="selected"'; } ?>><?php _e('Tweet Feed', 'kebo-se'); ?></option>
                    <option value="follower" <?php if ( 'follower' == $instance['type'] ) { echo 'selected="selected"'; } ?>><?php _e('Latest Followers', 'kebo-se'); ?></option>
                </select>
                <span class="howto">Please choose a type of Widget to see more options.</span>
            </p>
        </label>

        <div class="feed-container<?php echo ( isset( $instance['type'] ) ) ? ' ' . $instance['type'] : ''; ?>">

        <label for="<?php echo $this->get_field_id('display'); ?>">
            <p>
                <?php _e('Display', 'kebo_twitter'); ?>:
                <select style="width: 100%;" id="<?php echo $this->get_field_id('display') ?>" name="<?php echo $this->get_field_name('display'); ?>">
                    <option value="tweets" <?php
                    if ('tweets' == $instance['display']) {
                        echo 'selected="selected"';
                    }
                    ?>><?php _e('Tweets', 'kebo_twitter'); ?></option>
                    <option value="retweets" <?php
                    if ('retweets' == $instance['display']) {
                        echo 'selected="selected"';
                    }
                    ?>><?php _e('Re-Tweets', 'kebo_twitter'); ?></option>
                    <option value="all" <?php
                    if ('all' == $instance['display']) {
                        echo 'selected="selected"';
                    }
                    ?>><?php _e('All Tweets', 'kebo_twitter'); ?></option>
                </select>
                <span class="howto">Explanation text</span>
            </p>
        </label>

        <label for="<?php echo $this->get_field_id('style'); ?>">
            <p>
                <?php _e('Style', 'kebo_twitter'); ?>:
                <select style="width: 100%;" id="<?php echo $this->get_field_id('style') ?>" name="<?php echo $this->get_field_name('style'); ?>">
                    <option value="1" <?php
                    if (1 == $instance['style']) {
                        echo 'selected="selected"';
                    }
                    ?>><?php _e('List', 'kebo_twitter'); ?></option>
                    <option value="2" <?php
                    if (2 == $instance['style']) {
                        echo 'selected="selected"';
                    }
                    ?>><?php _e('Slider', 'kebo_twitter'); ?></option>
                </select>
            </p>
        </label>

        <label for="<?php echo $this->get_field_id('theme'); ?>">
            <p>
                <?php _e('Theme', 'kebo_twitter'); ?>:
                <select style="width: 100%;" id="<?php echo $this->get_field_id('theme') ?>" name="<?php echo $this->get_field_name('theme'); ?>">
                    <option value="light" <?php
                    if ('light' == $instance['theme']) {
                        echo 'selected="selected"';
                    }
                    ?>><?php _e('Light', 'kebo_twitter'); ?></option>
                    <option value="dark" <?php
                    if ('dark' == $instance['theme']) {
                        echo 'selected="selected"';
                    }
                    ?>><?php _e('Dark', 'kebo_twitter'); ?></option>
                </select>
            </p>
        </label>

        <label for="<?php echo $this->get_field_id('count'); ?>">
            <p><?php _e('Number Of Tweets', 'kebo_twitter'); ?>: <input style="width: 28px;" type="text" value="<?php echo $instance['count']; ?>" name="<?php echo $this->get_field_name('count'); ?>" id="<?php echo $this->get_field_id('count'); ?>"> <span><?php _e('Range 1-50', 'kebo_twitter') ?></span></p>
        </label>

        <label for="<?php echo $this->get_field_id('avatar'); ?>">
            <p><input style="width: 28px;" type="checkbox" value="avatar" name="<?php echo $this->get_field_name('avatar'); ?>" id="<?php echo $this->get_field_id('avatar'); ?>" <?php
                if ('avatar' == $instance['avatar']) {
                    echo 'checked="checked"';
                }
                ?>> <?php _e('Show profile image?', 'kebo_twitter'); ?> </p>
        </label>

        <label for="<?php echo $this->get_field_id('conversations'); ?>">
            <p><input style="width: 28px;" type="checkbox" value="true" name="<?php echo $this->get_field_name('conversations'); ?>" id="<?php echo $this->get_field_id('conversations'); ?>" <?php
                if ('true' == $instance['conversations']) {
                    echo 'checked="checked"';
                }
                ?>> <?php _e('Show conversations?', 'kebo_twitter'); ?> </p>
        </label>

        <label for="<?php echo $this->get_field_id('media'); ?>">
            <p><input style="width: 28px;" type="checkbox" value="true" name="<?php echo $this->get_field_name('media'); ?>" id="<?php echo $this->get_field_id('media'); ?>" <?php
                      if ('true' == $instance['media']) {
                          echo 'checked="checked"';
                      }
                      ?>> <?php _e('Show media? (only Lists)', 'kebo_twitter'); ?> </p>
        </label>
            
        </div>

        <?php
    }

    /*
     * Validates and Updates Options
     */

    function update($new_instance, $old_instance) {

        $instance = array();

        // Use old figures in case they are not updated.
        $instance = $old_instance;

        // Update text inputs and remove HTML.
        $instance['title'] = wp_filter_nohtml_kses($new_instance['title']);
        $instance['style'] = wp_filter_nohtml_kses($new_instance['style']);
        $instance['theme'] = wp_filter_nohtml_kses($new_instance['theme']);
        $instance['avatar'] = wp_filter_nohtml_kses($new_instance['avatar']);
        $instance['conversations'] = wp_filter_nohtml_kses($new_instance['conversations']);
        $instance['media'] = wp_filter_nohtml_kses($new_instance['media']);
        $instance['display'] = wp_filter_nohtml_kses($new_instance['display']);
        $instance['type'] = wp_filter_nohtml_kses($new_instance['type']);

        // Check 'count' is numeric.
        if (is_numeric($new_instance['count'])) {

            // If 'count' is above 50 reset to 50.
            if (50 <= intval($new_instance['count'])) {
                $new_instance['count'] = 50;
            }

            // If 'count' is below 1 reset to 1.
            if (1 >= intval($new_instance['count'])) {
                $new_instance['count'] = 1;
            }

            // Update 'count' using intval to remove decimals.
            $instance['count'] = intval($new_instance['count']);
        }

        return $instance;
    }
    
    function print_js() {
        
        // Begin Output Buffering
        ob_start();
        ?>

        <script type="text/javascript">
        
            jQuery('[id^="widget-kebose_twitter_widget-"][id$="-type"]').change( function() {
                
                // Get the currently selected value
                var selected = jQuery(this).val();
                // Add this value to the Widget contain container
                jQuery(this).parent().parent().parent().children('.feed-container').eq(0).removeClass('feed follower null').addClass( selected );
                
            });
            
        </script>
        
        <style type="text/css">
            
            .widget-content .feed-container {
                display: none;
            }
            .widget-content .feed-container.feed {
                display: block;
            }
            
        </style>

        <?php
        // End Output Buffering and Clear Buffer
        $output = ob_get_contents();
        ob_end_clean();
        
        echo $output;
        
    }

}

