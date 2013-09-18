/*
 * Plugin WP Admin Javascript File
 */

var ajax_url = 'http://localhost/wp/test/wp-admin/admin-ajax.php';

/*
 *  Block Google Service clicks, as no URL.
 */
jQuery( '.kconnection.google .kservice' ).click(function(e) {
    
    // Prevent Click from Reloading page
    e.preventDefault();

});

/*
 * Takeover Disconnect Link.
 */
jQuery( '.kconnection .kdisconnect' ).click(function(e) {
    
    // Prevent Click from Reloading page
    e.preventDefault();
    
    if (confirm('Are you sure you want to delete this connection?')) {
        
        var service = jQuery(this).parent('.kconnection').data('service');
        var account = jQuery(this).parent('.kconnection').data('account');

        var data = {
            action: 'kebo_se_delete_connection',
            service: service,
            account_id: account
        };

        var link = jQuery(this);

        jQuery.post( ajax_url, data, function(response) {
            
            response = jQuery.parseJSON( response );

            if ( 'true' === response.success && 'delete' === response.action ) {

                // Fade then Remove element
                link.parent('.kconnection').fadeOut( 1000, function() { jQuery(this).remove(); });

            } else {

                // Do nothing! ?? display error?

            };

        });
        
    } else {
        
        // Do nothing!
        
    }
    
});

/*
 * Takeover Share Link.
 */
jQuery( '.kconnection .kshare' ).click(function(e) {
    
    // Prevent Click from Reloading page
    e.preventDefault();

    var service = jQuery(this).parent('.kconnection').data('service');
    var account = jQuery(this).parent('.kconnection').data('account');
    
    var data = {
        action: 'kebo_se_share_connection',
        service: service,
        account_id: account
    };
    
    var link = jQuery(this);
    
    jQuery.post( ajax_url, data, function(response) {
        
        response = jQuery.parseJSON( response );
        
        if ( 'true' === response.success && 'share' === response.action ) {
        
            if ( link.hasClass('enabled') ) {

                link.removeClass('enabled');

            } else {

                link.addClass('enabled');

            };
        
        };
        
    });
    
});