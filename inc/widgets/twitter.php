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

/**
 * Search each connection looking for a twitter account.
 * We don't need to activate the Widget if there is no Twitter account.
 */
foreach ( $connections as $connection ) {

    if ( 'twitter' == strtolower( $connection['service'] ) ) {

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

        register_widget( 'Kbso_Twitter_Widget' );
        
    }

}

class Kbso_Twitter_Widget extends WP_Widget {

    /**
     * Default Widget Options
     */
    public $default_options = array(
        'account' => null,
        'title' => null,
        'type' => 'tweets',
        'display' => '',
        'style' => '',
        'theme' => '',
        'count' => '',
        'avatar' => '',
        'etc' => '',
    );
    
    function Kbso_Twitter_Widget() {

        $widget_ops = array(
            'classname' => 'kbso_twitter_widget',
            'description' => __( 'Displays many types of Twitter data.', 'kbso' )
        );

        $this->WP_Widget(
            false,
            __( 'Kebo Social - Twitter', 'kbso' ),
            $widget_ops
        );
        
    }

    /*
     * Outputs Content
     */
    function widget( $args, $instance ) {

        $time_start = microtime(true);
        
        extract( $args, EXTR_SKIP );
        
        $instance = wp_parse_args( $instance, $this->default_options );
        
        $service = 'twitter';
        $type = 'tweets';
        $accounts = array();
        
        wp_enqueue_style( 'kebo-twitter-plugin' );
        
        add_action( 'wp_footer', 'kbso_twitter_intent_js_print' );
        
        if ( is_array( $instance['accounts'] ) ) {
        
            foreach ( $instance['accounts'] as $account_id ) {

                $account = kebo_se_get_connection( $account_id, $service );
                
                $accounts[] = $account;
                
            }
            
            $data = new Kbso_Api;
            $data->set_service( $service );
            $data->set_type( $type );
            $data->set_accounts( $accounts );
                        
            $tweets = $data->get_data();
            
            $view = new Kbso_View( KBSO_PATH . 'views/twitter' );
            
            $view
                ->set_view( 'feed' )
                ->set( 'widget_id', $widget_id )
                ->set( 'tweets', $tweets )
                ->set( 'instance', $instance )
                ->set( 'count', $instance['count'] )
                ->set( 'before_widget', $before_widget )
                ->set( 'before_title', $before_title )
                ->set( 'title', $instance['title'] )
                ->set( 'after_title', $after_title )
                ->set( 'after_widget', $after_widget )
                ->set( 'view', $view )
                ->render();
        
        } else {
            
            _e('You must select an account to begin showing Tweets.', 'kbso');
            
        }
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;

        echo "Rendered Widget in $time seconds\n";
        
    }

    /*
     * Outputs Options Form
     */

    function form( $instance ) {

        // Add defaults.
        $instance = wp_parse_args( $instance, $this->default_options );

        $connections = get_option( 'kebo_se_connections' );
        $user_id = get_current_user_id();
        $counter = 0;
        
        foreach ( $connections as $connection ) {

            if ( 'twitter' == strtolower( $connection['service'] ) && ( $user_id == $connection['user_id'] || 1 == $connection['shared'] ) ) {

                $twitter_accounts[] = $connection;
                $counter++;
                
            }

        }
        
        /*
         * Output Relevant Script in the Footer.
         */
        add_action( 'admin_print_footer_scripts', array( $this, 'print_js' ) );
        ?>

        <?php if ( ! empty( $twitter_accounts ) ) { ?>
        <label for="<?php echo $this->get_field_id('accounts'); ?>">
            <p>
                <?php _e('Accounts', 'kbso'); ?>:
                <select style="width: 100%;" size="<?php echo ( 3 < $counter ) ? '4' : $counter ; ?>" id="<?php echo $this->get_field_id('accounts') ?>" name="<?php echo $this->get_field_name('accounts'); ?>[]" multiple="multiple">
                    <?php
                    foreach ( $twitter_accounts as $account ) {
                        
                        $selected = false;
                        
                        if ( is_array( $instance['accounts'] ) ) {
                            
                            foreach ( $instance['accounts'] as $account_id ) {

                                if ( $account['account_id'] == $account_id ) {
                                    $selected = true;
                                }

                            }
                            
                        }
                        
                        ?>
                        <option value="<?php echo $account['account_id']; ?>"<?php if ( true == $selected ) { echo ' selected="selected"'; } ?>>@<?php echo $account['account_name']; ?></option>
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
                    <option value="feed"<?php if ( 'feed' == $instance['type'] ) { echo ' selected="selected"'; } ?>><?php _e('Tweet Feed', 'kebo-se'); ?></option>
                    <option value="follower"<?php if ( 'follower' == $instance['type'] ) { echo ' selected="selected"'; } ?>><?php _e('Latest Followers', 'kebo-se'); ?></option>
                    <option value="friend"<?php if ( 'friend' == $instance['type'] ) { echo ' selected="selected"'; } ?>><?php _e('Latest Friends', 'kebo-se'); ?></option>
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

    function update( $new_instance, $old_instance ) {

        $instance = array();

        // Use old figures in case they are not updated.
        $instance = $old_instance;

        $instance['accounts'] = $new_instance['accounts'];
        
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

