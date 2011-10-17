(function( win, $, undefined ){
    
    PT = jQuery({}); // so we can bind/trigger events off here if needed
    
    PT.SETTINGS = {
        
        prefix : 'prontotype-',
        cookieExpiration : 7 // 7 days
        
    };
    
    // PT.store : Basic (cookie) storage, integrates with data stored in JSON cookies via PHP
    
    PT.store = function() {
        this.cookiePrefix = PT.SETTINGS.prefix;
        this.cookieExpiration = PT.SETTINGS.cookieExpiration; 
    };
    
    PT.store.prototype.set = function( key, val ) {
        $.cookie(this.cookiePrefix + key, JSON.stringify(val), { expires : this.cookieExpiration, path : '/' });
    };
    
    PT.store.prototype.get = function( key ) {
        var cookie = $.cookie( this.cookiePrefix + key );
        if ( cookie !== null && cookie !== '' ){
            return JSON.parse( cookie );
        }
    };
    
    PT.store.prototype.clear = function( key ) {
        $.cookie(this.cookiePrefix + key, null);
    };
    
    
    // PT.notes : Retrieves notes from the server and attaches them to the appropriate element via the data-note attribute
    
    PT.notes = function( callback, notesfile ) {
        
        var self = this,
            callback = callback || function(){},
            body = $('body');
            
        this.notesfile = notesfile || 'notes';
        this.classPrefix = PT.SETTINGS.prefix;
        this.notes = {};
        this.noted = jQuery({});
        this.notesOn = false;
        this.noted = $('[data-note]');
        this.noteEl = $('<div />', {
            'class' : this.classPrefix + 'note'
        });
        
        $.getJSON('/__data/' + this.notesfile + '/', function( data ){
            self.notes = data;
            self.noted.each(function(){
                self._makeNotable( $(this) );
            });
            callback();
        });
        
        body.append(this.noteEl);
    };
    
    PT.notes.prototype.showNotes = function( el ) {
        this.highlightNotes(true);
        this.notesOn = true;
    };
    
    PT.notes.prototype.hideNotes = function( el ) {
        this.highlightNotes(false);
        this.notesOn = false;
    };
    
    PT.notes.prototype.highlightNotes = function( turnOn ) {
        if ( turnOn ) {
            this.noted.addClass( this.classPrefix + 'highlight-note' );
        } else {
            this.noted.removeClass( this.classPrefix + 'highlight-note' );
        }
    };
    
    PT.notes.prototype._showNote = function( el ) {
        var self = this;
        this.noteEl.html(el.data('note'))
            .css('display', 'block');
        el.bind('mousemove', function( e ){
            self._positionTooltip( e );
        });
    };
    
    PT.notes.prototype._hideNote = function( el ) {
        this.noteEl.html('')
            .css('display', 'none');
        el.unbind('mousemove');
    };
    
    PT.notes.prototype._positionTooltip = function( e ) {
        
        var ttWidth = this.noteEl.outerWidth(),
            ttHeight = this.noteEl.outerHeight(),
            winWidth = $(win).width(),
            winHeight = $(win).height(),
            ttLeft = e.pageX+10,
            ttTop = e.pageY+10;

        if ( ttLeft + ttWidth > winWidth ) {
            ttLeft = ttLeft - ttWidth -10;
        }
        
        if ( ttTop + ttHeight > winHeight ) {
            ttTop = ttTop - ttHeight -10;
        }

        this.noteEl.offset({ top: ttTop, left : ttLeft });
    };
    
    PT.notes.prototype._makeNotable = function( el ) {
        var self = this,
            note = this._getNote( el.attr('data-note') );
        if ( note ) {
            el.addClass( self.classPrefix + 'has-note' );
            el.data({
                'outline': el.css('outline'),
                'note': note
            });
            el.hover(function(){
                if ( self.notesOn ) self._showNote( el );
            }, function(){
                if ( self.notesOn ) self._hideNote( el );
            });
        }
    };
    
    PT.notes.prototype._getNote = function( path ) {
        if ( path === "" ) {
            return this.notes;
        }
        var parts = path.split(/\./g),
            numparts = parts.length,
            notes = $.extend({}, this.notes);
        for ( var i=0; i<numparts; i++ ) {
            if ( notes[parts[i]] !== undefined ) {
                notes = notes[parts[i]];
            } else {
                return null;
            }
        }
        return notes;
    };
    
    // PT Toolbar: 
    
    PT.toolbar = function(){
        var pre = PT.SETTINGS.prefix,
            self = this;   
        this.toolbar = $('<div class="' + pre + 'toolbar ' + pre + 'loading' + '"></div>');             
        this.store = new PT.store();
        this.notes = new PT.notes(function(){
            self.toolbar.removeClass(pre + 'loading');
            self._addNotesTools();
        });
        $('body').append(this.toolbar);
    };
    
    PT.toolbar.prototype._addNotesTools = function() {
        
        var off, on,
            section = $('<div class="'+PT.SETTINGS.prefix + 'section'+'"></div>'),
            self = this;
        
        off = $('<a href="#">Show notes</a>').bind('click', function(){
            self.notes.showNotes();
            return false;
        });
        
        on = $('<a href="#">Hide notes</a>').bind('click', function(){
            self.notes.hideNotes();
            return false;
        });
        
        section.append(off).append(on);
        this.toolbar.append(section);
    };


})( this, jQuery );