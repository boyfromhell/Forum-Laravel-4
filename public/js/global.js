jQuery.fn.exists = function() {
	return this.length > 0;
}

var parangi = {

	/**
	 * Initialization stuff
	 */
	init: function( cfg ) {
	
		parangi.cfg = cfg;

		$('.announcement').dblclick( function() {
			var id = $(this).attr('id').match(/[\d]+/g)[0];
			
			$.get('/admin/ajax/edit_announcement.php', { id: id }, function( data ) {
				json = $.parseJSON(data);
				if( json.success ) {
					$('#announcement_'+id).html( json.html );
					$('#announcement_'+id+' textarea').focus();
					
					$('#announcement_'+id+' textarea').blur( function() {
						$.post('/admin/ajax/edit_announcement.php', $(this).closest('form').serialize(), function( data ) {
							json = $.parseJSON(data);
							if( json.success ) {
								$('#announcement_'+id).html( json.html );
							}
						});
					});
				}
			});
		});
		
		$('.alert_box.fade').delay(2000).fadeOut('slow');
		
		$('.focus').focus();

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
			$.post('/messages/preview.php', send_data, function(data) {
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
				$.post('/ajax/delete_attachment.php', send_data, function(data) {
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
			var id = $(this).data('id');
			parangi.quickEdit(id, 'save');
		});
		$('body').on('click', '#quickedit input.cancel', function(e) {
			e.preventDefault();
			var id = $(this).data('id');
			parangi.quickEdit(id, 'cancel');
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
		url = '/forum/quick_edit.php?p=' + id;

		if( button == 'edit' ) {
			$.get(url, function( data ) {
				$('#post'+id).html( data );
				anchor_to(id);
				$('#post'+id+' pre.code').each(function(i, e) { hljs.highlightBlock(e) });
			});
		}
		else {
			$.post(url, $('#quickedit').serialize() + '&' + button + '=1', function( data ) {
				$('#post'+id).html( data );
				anchor_to(id);
				$('#post'+id+' pre.code').each(function(i, e) { hljs.highlightBlock(e) });
			});
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
	$.post('/community/delete_member.php', args, function(data) {
		json = $.parseJSON(data);
		if( json.success ) {
			$('#member'+user_id).hide();
		}
	});
}












function colorover() {
	if( $('#colorbb').hasClass('bbcode')) {
		$('#colorbb').removeClass('bbcode');
		$('#colorbb').addClass('bbhov');
	}
}

function colorout() {
	if( $('#colorbb').hasClass('bbhov')) {
		$('#colorbb').removeClass('bbhov');
		$('#colorbb').addClass('bbcode');
	}
}

function userover(pid) {
	if( $('#user'+pid).hasClass('usermenu')) {
		$('#user'+pid).removeClass('usermenu');
		$('#user'+pid).addClass('userhov');
	}
}

function userout(pid) {
	if( $('#user'+pid).hasClass('userhov')) {
		$('#user'+pid).removeClass('userhov');
		$('#user'+pid).addClass('usermenu');
	}
}

function sizeover() {
	if( $('#sizebb').hasClass('bbcode')) {
		$('#sizebb').removeClass('bbcode');
		$('#sizebb').addClass('bbhov');
	}
}

function sizeout() {
	if( $('#sizebb').hasClass('bbhov')) {
		$('#sizebb').removeClass('bbhov');
		$('#sizebb').addClass('bbcode');
	}
}

function showcolors() {
	var cbb = $('#colorbb');
	var cbox = document.getElementById("colorBox");
	if( cbb != null ) {
		var offset = $('#colorbb').offset();

		var cy = offset.top;
		var cx = offset.left;
		cbb.className = "bbsel";
	}
	if( cbox != null ) {
		cbox.style.top = cy+22+"px"; cbox.style.left = cx+"px";
	}
}

function showuser(pid) {
	var $ubb = $('#user'+pid);
	var $ubox = $('#ubox'+pid);

	var offset = $ubb.offset();
	var uy = offset.top;
	var ux = offset.left;

	$ubox.css({top: uy+27+"px", left: ux+"px"});
	$ubb.addClass('usersel');
}

function showsizes() {
	var sbb = document.getElementById("sizebb");
	var sbox = document.getElementById("sizeBox");

	var offset = $('#sizebb').offset();
	var sy = offset.top;
	var sx = offset.left;

	if( sbox != null ) {
		sbox.style.top = sy+22+"px"; sbox.style.left = sx+"px";
		$('#sizebb').addClass('bbsel');
	}
}

function hideboxes(e) {
	var target=e?e.target:event.srcElement;
	var cbb = document.getElementById("colorbb");
	var cimg = document.getElementById("colorimg");
	var sbb = document.getElementById("sizebb");
	var simg = document.getElementById("sizeimg");
	var cbox = document.getElementById("colorBox");
	var sbox = document.getElementById("sizeBox");
	
	var unames=document.getElementsByName("uname");
	var uboxes=document.getElementsByName("ubox");
	for(var i=0; i<unames.length; i++) {
		if( target!=unames[i] && target!=uboxes[i] ) {
			unames[i].className = "usermenu";	
			uboxes[i].style.top = "-500px";
			uboxes[i].style.left = "-500px";
		}
	}
	
	if( target!=cbb && target!=cimg && cbox != null ) {
		cbox.style.top = "-500px"; cbox.style.left = "-500px";
		cbb.className = "bbcode";
	}
	if( target!=sbb && target!=simg && sbox != null ) {
		sbox.style.top = "-500px"; sbox.style.left = "-500px";
		sbb.className = "bbcode";
	}
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

document.onclick=hideboxes
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
	if( cury > offset.top ) {
		window.scroll(0, offset.top);
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

setInterval("showOnline()", 20000);
