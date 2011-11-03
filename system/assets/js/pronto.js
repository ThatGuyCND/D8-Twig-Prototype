(function( win, $, undefined ){
	
	PT = jQuery({}); // so we can bind/trigger events off here if needed
	
	PT.SETTINGS = {
		prefix : 'prontotype-',
		cookieLifetime : 604800,
		jsonDataTrigger : '__data'
	};
	
	PT.configure = function( config ){
		$.extend(PT.SETTINGS, config);
	};
	
	PT.helpers = {
		
		makeQs : function( qs, params ) {
		
			var qsPairs = qs.replace('?','').split('&'),
				numPairs = qsPairs.length,
				qsParams = {},
				newQs = [];
			
			// build object of param key/val paris
			for( var i = 0; i < numPairs; i++ ) {
				if ( qsPairs[i] !== '' ) {
					var parts = qsPairs[i].split('=');
					if ( parts[1] === undefined ) parts[1] = '';
					qsParams[parts[0]] = parts[1];
				}
			}
			
			$.each( params, function( key, val ){
				if ( qsParams[key] === undefined ) {
					if ( val !== null ) newQs.push(key + '=' + val );
				}
				delete qsParams[key];
			});
			
			$.each( qsParams, function( key, val ){
				newQs.push(key + '=' + val );
			});

			return newQs.join('&');
		}
		
	};
	
	// PT.store : Basic (cookie) storage, integrates with data stored in JSON cookies via PHP
	
	PT.store = function() {
		this.cookiePrefix = PT.SETTINGS.prefix;
		this.cookieLifetime = PT.SETTINGS.cookieLifetime / 24*60*60;
	};
	
	PT.store.prototype.set = function( key, val ) {
		$.cookie(this.cookiePrefix + key, JSON.stringify(val), { expires : this.cookieLifetime, path : '/' });
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
		this.store = new PT.store();
		this.noted = jQuery({});
		this.notesOn = false;
		this.noted = $('[data-note]');
		this.noteEl = $('<div />', {
			'class' : this.classPrefix + 'note'
		});
		
		$.getJSON('/' + PT.SETTINGS.jsonDataTrigger + '/' + this.notesfile + '/', function( data ){
			self.notes = data;
			self.noted.each(function(){
				self._makeNotable( $(this) );
			});
			if ( self.store.get('show-notes') === true ) {
				self.showNotes();
			}
			callback();
		});
		
		body.append(this.noteEl);
	};
	
	PT.notes.prototype.showNotes = function() {
		this.highlightNotes(true);
		this.notesOn = true;
		this.store.set('show-notes', true);
	};
	
	PT.notes.prototype.hideNotes = function() {
		this.highlightNotes(false);
		this.notesOn = false;
		this.store.set('show-notes', false);
	};
	
	PT.notes.prototype.toggleNotes = function() {
		if ( this.notesOn ) {
			this.hideNotes();
		} else {
			this.showNotes();
		}
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
	
	// PT.users : Grab the users from the users file.
	
	PT.users = function( callback, usersfile ) {
		
		var self = this,
			callback = callback || function(){},
			body = $('body');
			
		this.usersfile = usersfile || 'users';
		this.classPrefix = PT.SETTINGS.prefix;
		this.users = {};
		this.store = new PT.store();
		this.currentUser = this.store.get('user');
		
		$.getJSON('/' + PT.SETTINGS.jsonDataTrigger + '/' + this.usersfile + '/', function( data ){
			self.users = data;
			callback();
		});
	};
	
	// PT Toolbar: Wrapper to pull together some of the tools into an easy to use package
	
	PT.toolbar = function(){
		var pre = PT.SETTINGS.prefix,
			self = this;   
		this.toolbar = $('<div id="' + pre + 'toolbar" class="' + pre + 'loading"></div>');				
		this.store = new PT.store();
		this.notes = new PT.notes(function(){
			self._addNotesTools();
			self.users = new PT.users(function(){
				self.toolbar.removeClass(pre + 'loading');
				self._addUserTools();
			});
		});

		$('body').append(this.toolbar);
	};
	
	PT.toolbar.prototype._addNotesTools = function() {
		var toggle,
			section = $('<div class="'+PT.SETTINGS.prefix + 'section'+'"></div>'),
			self = this,
			checked = self.store.get('show-notes') === true ? ' checked' : '';

			toggle = $('<input type="checkbox" id="' + PT.SETTINGS.prefix + 'notes-toggle" ' + checked + '>').bind('change', function(){
				self.notes.toggleNotes();
				return false;
			});
		
		section.append(toggle).append('<label for="' + PT.SETTINGS.prefix + 'notes-toggle">Show notes</label>');
		this.toolbar.append(section);
	};
	
	PT.toolbar.prototype._addUserTools = function() {
		var userMenu,
			section = $('<div class="'+PT.SETTINGS.prefix + 'section'+'"></div>'),
			self = this;
		
		if ( ! $.isEmptyObject(this.users.users) )
		{
			if ( self.users.currentUser === undefined ) {
				// logged out 
				userMenu = $('<select id="' + PT.SETTINGS.prefix + 'user-select">');
				userMenu.append('<option value="">--</option>');
				
				$.each(this.users.users, function( username, details ){
					var sel = '<option value="' + username + '">' + username;
					if ( details.role !== undefined ) sel = sel + ' (' + details.role + ')';
					sel = sel + '</option>';
					userMenu.append(sel);
				});
				
				userMenu.bind('change', function(){
					var val = $(this).find(':selected').attr('value');
					if ( val !== '' ) {
						window.location.search = PT.helpers.makeQs(window.location.search, {'login':val, 'logout':null});
					}
					return false;
				});
				
				section.append('<label for="' + PT.SETTINGS.prefix + 'user-select">Login as user:</label>').append(userMenu);
				
			} else {
				// logged in
				userMenu = $('<span>Logged in as <strong>' + self.users.currentUser.username + '</strong> (' + self.users.currentUser.role + ')' + ' <a href="#">Log out</a></span>');
				userMenu.find('a').bind('click', function(){
					window.location.search = PT.helpers.makeQs(window.location.search, {'logout':1, 'login':null});
					return false;
				});
				section.append(userMenu);
			}

			if ( self.users.currentUser !== undefined ) {
				userMenu.append('<option value="logout">[logout]</option>');
			}
				
			this.toolbar.append(section);			
		}
	};


})( this, jQuery );