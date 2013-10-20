<?php
/**
 * View File - Tweet Media
 */
?>

<div id="<?php echo $tweet['id_str']; ?>" class="kmedia kclosed">
    
    <?php foreach ( $tweet['entities']['media'] as $media ) { ?>
    
        <a href="<?php echo $media->expanded_url; ?>" target="_blank">
            <img alt="<?php _e( 'Tweet Media', 'kbso' ); ?>" src="<?php echo ( is_ssl() ) ? $media->media_url_https : $media->media_url; ?>" />
        </a>
    
    <?php } ?>
    
</div>