<?php
/**
 * View File - Twitter Feed Footer
 */

/*
 * Print the 'before_title' HMTL set by the Theme.
 */
echo apply_filters( 'kbso_twitter_tweets_before_title', $before_title, $instance, $widget_id );

/*
 * Output the Title text set on the Widget.
 */
echo apply_filters( 'kbso_twitter_tweets_title', $title, $instance, $widget_id );

/*
 * Print the 'after_title' HMTL set by the Theme.
 */
echo apply_filters( 'kbso_twitter_tweets_after_title', $after_title, $instance, $widget_id );