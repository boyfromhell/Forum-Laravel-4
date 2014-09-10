<a class="button" href="{{ $album->url }}{{ $page > 1 ? '?page='.$page : '' }}">Return</a>
<a class="button" href="/media/download/{{ $photo->id }}">Download</a>

<div class="float_right">
<a class="button ajax-photo" data-id="{{ $prev->id }}" href="{{ $prev->url }}">Previous</a>
<a class="button ajax-photo" data-id="{{ $next->id }}" href="{{ $next->url }}">Next</a>
</div>

<div class="break"></div>

<div class="welcome">

	<div class="header">Photo {{ $photo->counter }} of {{ $album->total }}</div>
	
	<div class="body">
	
	<div class="float_left">
	Uploaded by <a href="{{ $photo->user->url }}">{{{ $photo->user->name }}}</a>
	</div>
	<div class="float_right" style="text-align:right">
	Viewed {{ $photo->views }} time{{ $photo->views != 1 ? 's' : '' }}
	@if( $me->is_admin )
		<br>
		Downloaded {{ $photo->downloads }} time{{ $photo->downloads != 1 ? 's' : '' }}
	@endif
	</div>
	<div class="break"></div>
	<br>
	
	@if ( $photo->description )
	<p class="photo-description">
	{{ BBCode::parse($photo->description) }}
	</p>
	@endif
	
	<center>
	<div class="photo large" style="width:{{ $photo->width+22 }}px; height:{{ $photo->height+22 }}px">
	<a id="main-photo" class="thumb ajax-photo" data-id="{{ $next->id }}" data-prev="{{ $prev->id }}" href="{{ $next->url }}"><img src="{{ $cdn }}{{ $photo->scale }}" {{ $photo->attr }}></a></div>
	</center>

	</div>
</div>

<div class="welcome no-margin">

	<div class="header">Share this Photo</div>
	
	<div class="body form2">
	<script type='text/javascript'><!--
	function copyToClipboard( id ) {
		$('#'+id).focus();
		$('#'+id).select();
		copiedText = document.selection.createRange();
		copiedText.execCommand('Copy');
	} 
	--></script>
	
	<label class="left">URL</label>
	<input id="url" class="left" onclick="copyToClipboard('url')" type="text" value="http://{{ Config::get('app.domain') }}{{ $photo->url }}" style="width:450px">
	<div class="break"></div>
	
	@if ( $me->id )
	<label class="left">BB Code ({{ $photo->width }}x{{ $photo->height }})</label>
	<input id="large" class="left" onclick="copyToClipboard('large')" type="text" value="[url=http://{{ Config::get('app.domain') }}{{ $photo->url }}][img]{{ $cdn }}{{ $photo->scale }}[/img][/url]" style="width:450px">
	<div class="break"></div>
	
	<label class="left">BB Code (Thumbnail)</label>
	<input id="small" class="left" onclick="copyToClipboard('small')" type="text" value="[url=http://{{ Config::get('app.domain') }}{{ $photo->url }}][img]{{ $cdn }}{{ $photo->thumbnail }}[/img][/url]" style="width:450px">
	<div class="break"></div>
	@endif
	
	@if ( $photo->user_id == $me->id || $me->is_admin )
	<a class="button small" href="/media/edit_photo/{{ $photo->id }}">Edit</a>
	@endif
	
	<div class="break"></div>
	
	</div>
</div>

<div style="display:none">
<script type="text/javascript">
$(document).ready(function() {
	var images = new Array()
	function preload() {
		for (i = 0; i < preload.arguments.length; i++) {
			images[i] = new Image()
			images[i].src = preload.arguments[i]
		}
	}
	preload("{{ $cdn }}{{ $next_photo->scale }}");
});
</script>
</div>

<a class="button" href="{{ $album->url }}{{ $page > 1 ? '?page='.$page : '' }}">Return</a>
<a class="button" href="/media/download/{{ $photo->id }}">Download</a>

<div class="float_right">
<a class="button ajax-photo" data-id="{{ $prev->id }}" href="{{ $prev->url }}">Previous</a>
<a class="button ajax-photo" data-id="{{ $next->id }}" href="{{ $next->url }}">Next</a>
</div>

<div class="break"></div>
