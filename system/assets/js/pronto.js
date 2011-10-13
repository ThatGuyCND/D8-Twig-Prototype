(function( win, $, undefined ){
    
    PT = jQuery({}); // so we can bind/trigger events off here if needed
    
    // PT.session : Basic session (cookie) storage, integrates with PHP session storage
    
    PT.session = function( cookie_prefix, cookie_expiration ) {
        
        this.cookie_prefix = cookie_prefix || 'prototype_';
        this.cookie_expiration = cookie_expiration || 7; // 7 days
    };
    
    PT.session.prototype.store = function( key, val ) {
        $.cookie(this.cookie_prefix + key, JSON.stringify(val), { expires : this.cookie_expiration, path : '/' });
    }
    
    PT.session.prototype.retrieve = function( key ) {
        var cookie = $.cookie( this.cookie_prefix + key );
        if ( cookie !== null && cookie !== '' ){
            return JSON.parse( cookie );
        }
    }
    
    PT.session.prototype.clear = function( key ) {
        $.cookie(this.cookie_prefix + key, null);
    }
    
})( this, jQuery );