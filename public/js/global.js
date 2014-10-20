jQuery.fn.exists = function() {
	return this.length > 0;
}

var sending = false, active = 1;

var parangi = {

	/**
	 * Initialization stuff
	 */
	init: function( cfg ) {
	
		parangi.cfg = cfg;

		var sb_reload = setInterval("showData()", 10000);
		setInterval("showOnline()", 20000);

		$('.announcement').dblclick( function() {
			var id = $(this).attr('id').match(/[\d]+/g)[0];
			
			$.get('/admin/edit-announcement', { id: id }, function( data ) {
				json = $.parseJSON(data);
				if( json.success ) {
					$('#announcement_'+id).html( json.html );
					$('#announcement_'+id+' textarea').focus();
					
					$('#announcement_'+id+' textarea').blur( function() {
						$.post('/admin/edit-announcement', $(this).closest('form').serialize(), function( data ) {
							json = $.parseJSON(data);
							if( json.success ) {
								$('#announcement_'+id).html( json.html );
							}
						});
					});
				}
			});
		});

		/**
		 * Toggle spoiler alert blocks
		 */
		$('body').on('click', '.spoiler-alert', function(e) {
			e.preventDefault();
			$(this).next('.spoiler').toggle();
		});
		
		/**
		 * Check if any field has changed and if so warn before leaving page
		 */
		$('body').on('submit', 'form.unload-warning', function() {
			window.onbeforeunload = null;
		});
		window.onbeforeunload = function() {
			var unsaved = 0;
			$('form.unload-warning').each(function() {
				if( $(this).data('changed') ) {
					unsaved++;
				}
			});
			if( unsaved > 0 ) { return 'You have unsaved changes'; }
		}
		$('body').on('change', 'form.unload-warning input,form.unload-warning select,form.unload-warning textarea', function() {
			var $form = $(this).closest('form.unload-warning');
			$form.data('changed', 1);
		});

		/**
		 * Handle form previews
		 */
		$('input.preview').on('click', function(e) {
			e.preventDefault();
			var $form = $(this).closest('form');
			var send_data = {
				'content' : $('#bbtext').val()
			};
			$.post('/messages/preview', send_data, function(data) {
				$('#preview').html(data.html);
			}, 'json');
		});
		
		/**
		 * Delete attachments
		 */
		$('a.delete-attachment').click( function(e) {
			e.preventDefault();
			var $attachment = $(this).closest('div');
			var send_data = {
				'id' : $(this).data('id')
			};
			if( confirm('Delete this attachment?') ) {
				$.post('/delete-attachment', send_data, function(data) {
					$attachment.fadeOut('fast');
				});
			} else {
				return false;
			}
		});
		
		/**
		 * Highlight code block syntax
		 */
		$('pre.code').each(function(i, e) { hljs.highlightBlock(e) });
		
		/**
		 * Confirm deletion of an item
		 */
		$('#delete').on('click', function(e) {
			e.preventDefault();
			if( confirm('Delete this ' + $(this).data('item') + '?') ) {
				$(this).off('click').click();
			} else {
				return false;
			}
		});
		
		/**
		 * Confirm deletion of multiple items
		 */
		$('#delete-multiple').on('click', function(e) {
			e.preventDefault();
			var total = 0, title = '', conf_message = '';
			$('.thread-row').each( function() {
				if( $(this).find('.icon').find('input').prop('checked') === true ) {
					total++;
					title = $(this).data('title');
				}
			});
			if( total === 0 ) {
				return false;
			} else if( total == 1 ) {
				conf_message = $(this).data('action') + ' ' + $(this).data('item') + ' "' + title + '"?';
			} else {
				conf_message = $(this).data('action') + ' ' + total + ' ' + $(this).data('item') + 's?';
			}
			if( confirm(conf_message) ) {
				$(this).off('click').click();
			} else {
				return false;
			}
		});
		
		$('body').on('click', '#quickedit input.btn-primary', function(e) {
			e.preventDefault();
			var id = $('#quickedit').data('id');
			parangi.quickEdit(id, 'save');
		});
		$('body').on('click', '#quickedit input.cancel', function(e) {
			e.preventDefault();
			var id = $('#quickedit').data('id');
			parangi.quickEdit(id, 'cancel');
		});

		/**
		 * Shoutbox
		 */
		$('#sb-toggle').on('click', function(e) {
			e.preventDefault();

			if( active == 1 ) {
				$('#shoutbox input').attr('disabled', 'disabled');
				$('#sb-toggle').html('Enable');
				active = 0;
				clearInterval(sb_reload);
			} else {
				showData();
				$('#shoutbox input').removeAttr('disabled');
				$('#sb_toggle').html('Disable');
				active = 1;
				sb_reload = setInterval("showData()", 10000);
			}
		});

		// Don't allow submit buttons to be pressed more than once
		$('body').on('submit', 'form', function(e) {
			var $btn = $(this).find('.btn-once');

			if( $btn.exists() ) {
				$btn.button('loading');
			}
		});

		/**
		 * Photo AJAX
		 */
		/*if( parangi.cfg.id == 'viewphoto' ) {
			// Clicks
			$('body').on('click', 'a.ajax-photo', function(e) {
				e.preventDefault();
				
				var id = $(this).data('id');
				parangi.loadPhoto(id, true);
			});
			
			// Arrow keys
			document.onkeyup = parangi.photoKeyHandle;
			
			// Back button
			window.addEventListener('popstate', function(e) {
				var parts = location.search.split('=');
				parangi.loadPhoto(parts[1], false);
			});
		}*/
	},

	/**
	 * Handle quick edit processing
	 */
	quickEdit: function(id, button) {
		url = '/quick-edit/' + id;

		if( button == 'edit' ) {
			$.get(url, function( data ) {
				$('#post'+id).html( data.html );
				anchor_to(id);
			}, 'json');
		}
		else {
			$.post(url, $('#quickedit').serialize() + '&' + button + '=1', function( data ) {
				$('#post'+id).html( data.html );
				anchor_to(id);
				$('#post'+id+' pre.code').each(function(i, e) { hljs.highlightBlock(e) });
			}, 'json');
		}
	},
	
	/**
	 * Handle left and right arrow keys when viewing photo
	 */
	photoKeyHandle: function(e) {
		var KeyID = (window.event) ? event.keyCode : e.keyCode;
		switch( KeyID ) {
			case 37:
				id = $('#main-photo').data('prev');
				parangi.loadPhoto(id, true);
				break;
			case 39:
				id = $('#main-photo').data('id');
				parangi.loadPhoto(id, true);
				break;
		}
	},
	
	/**
	 * Load a photo HTML through AJAX
	 */
	loadPhoto: function(id, pushState) {
		var send_data = {
			'id'   : id,
			'ajax' : true
		};
		$.get('/media/photo', send_data, function(data) {
			$('#ajaxphoto').html(data.html);
			if( pushState ) {
				history.pushState(null, null, '/media/photo?id='+id);
			}
		}, 'json');
	}

};


function deleteMember( group_id, user_id ) {
	var args = {
		delete_member: true,
		id: group_id,
		user_id: user_id
	}
	$.post('/groups/delete-member', args, function(data) {
		json = $.parseJSON(data);
		if( json.success ) {
			$('#member'+user_id).hide();
		}
	});
}












function stripbbtags() {
	myField = document.getElementById("bbtext");
	var text = myField.value;
	text = text.replace( /\[\/?[biu]\]/gi, "" );
	text = text.replace( /\[\/?strike\]/gi, "" );
	text = text.replace( /\[\/?size=?[0-9]*\]/gi, "" );
	text = text.replace( /\[\/?colou?r=?#?[0-9a-zA-Z]*\]/gi, "" );
	text = text.replace( /\[\/?center\]/gi, "" );
	text = text.replace( /\[\/?left\]/gi, "" );
	text = text.replace( /\[\/?right\]/gi, "" );
	myField.value = text;
}

function showspoiler(id) { 
	var myBox = document.getElementById("spoiler"+id);
	if( myBox.style.display == "none" ) {
		myBox.style.display = "block";
	}
	else {
		myBox.style.display = "none";
	}
}

function addtext(head,tail,popup) {
	var myField;
	if( popup==0 ) {
		myField = document.getElementById("bbtext");
	}
	else {
		myField = window.opener.document.getElementById("bbtext");
	}

	if( document.selection ) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = head + tail;
	}
	else if( myField.selectionStart || myField.selectionStart == '0' ) {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos) + head + myField.value.substring(startPos,endPos) + tail + 
						myField.value.substring(endPos, myField.value.length);
		myField.setSelectionRange(startPos+head.length,endPos+head.length);
		myField.focus();
	}
	else {
		myField.value = myField.value + head + tail;
	}
}

document.onkeydown=function(e) {
	if(e.which == 66 && e.ctrlKey ) { 
		addtext("[b]","[/b]",0); return false;
	}
	if(e.which == 69 && e.ctrlKey ) { 
		addtext("[center]","[/center]",0); return false;
	}
	if(e.which == 76 && e.ctrlKey ) { 
		addtext("[left]","[/left]",0); return false;
	}
	if(e.which == 82 && e.ctrlKey ) { 
		addtext("[right]","[/right]",0); return false;
	}
	if(e.which == 73 && e.ctrlKey ) { 
		addtext("[i]","[/i]",0); return false;
	}
	if(e.which == 85 && e.ctrlKey ) { 
		addtext("[u]","[/u]",0); return false;
	}
}

/**
 * Modify Math.round to take a decimal argument
 */
var _round = Math.round;
Math.round = function(number, decimals) {
	if (arguments.length == 1)
		return _round(number);

	var multiplier = Math.pow(10, decimals);
	return _round(number * multiplier) / multiplier;
}

function format_size( bytes ) {
	if( bytes < 1024 ) {
		return( bytes + ' bytes' );
	} else if( bytes < 1024*1024 ) {
		return( parseInt(bytes/1024, 10) + ' kb' ); 
	} else if( bytes < 1024*1024*1024 ) {
		return( Math.round(bytes/(1024*1024), 2) + ' MB' );
	}
}

function anchor_to( id ) {
	// @todo use jquery
	var cury = typeof window.pageYOffset != 'undefined' ? window.pageYOffset : document.documentElement.scrollTop;
	var offset = $('#post' + id).offset();
	var jumpto = offset.top-70;

	if( cury > jumpto ) {
		window.scroll(0, jumpto);
	}
}

function anchor( p ) {
	var offset = $('#pt'+p).offset();
	window.scroll(0, offset.top);
}

function showOnline() {
	$.get('/whos-online', function( data ) {
		$('#online').html(data.html);
	}, 'json');
}

function showData() 
{
	if( sending == true ) return;

	sending = true;
	var send_data = {
		last_id: $('#shoutbox').data('lastId'),
		last_time: $('#shoutbox').data('lastTime')
	};
	$.get('/shoutbox/fetch', send_data, function( data ) {
		if( $('[name='+data.group+']').exists() ) {
			$('[name='+data.group+']').closest('tr').after(data.html);
		} else {
			$('#shoutbox tbody').prepend(data.html);
		}
		$('#shoutbox').data('lastId', data.last_id);
		$('#shoutbox').data('lastTime', data.last_time);
		sending = false;
	}, 'json');
}

function saveData()
{
	$('#shoutbox .btn').button('loading');

	$.post('/shoutbox/post', {message: $('input[name=message]').val()}, function( data ) {
		if( data.success ) {
			$('#shoutbox input[type="text"]').val('');
			$('#shoutbox input[type="text"]').focus();
			showData();
		}
		$('#shoutbox .btn').button('reset');
	}, 'json');
}
