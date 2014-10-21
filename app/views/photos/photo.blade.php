<div class="panel panel-primary">

	<div class="panel-heading">Photo {{ $photo->counter }} of {{ $album->total }}</div>
	
	<div class="panel-body">

	<div class="row">
	<div class="col-sm-6">
	Uploaded by <a href="{{ $photo->user->url }}">{{{ $photo->user->name }}}</a>
	</div>
	<div class="col-sm-6 text-right">
	Viewed {{ $photo->views }} time{{ $photo->views != 1 ? 's' : '' }}
	@if( $me->is_admin )
		<br>
		Downloaded {{ $photo->downloads }} time{{ $photo->downloads != 1 ? 's' : '' }}
	@endif
	</div>
	</div>
	
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

<div class="panel panel-info">

	<div class="panel-heading">Share this Photo</div>

	<div class="panel-body">
	<script type='text/javascript'><!--
	function copyToClipboard( id ) {
		$('#'+id).focus();
		$('#'+id).select();
		copiedText = document.selection.createRange();
		copiedText.execCommand('Copy');
	} 
	--></script>

	<form class="form-horizontal">

	<div class="form-group">
		<label class="col-sm-4 control-label">URL</label>
		<div class="col-sm-5">
			<input id="url" class="form-control" onclick="copyToClipboard('url')" type="text" value="http://{{ Config::get('app.domain') }}{{ $photo->url }}">
		</div>
	</div>
	
	@if ( $me->id )
	<div class="form-group">
		<label class="col-sm-4 control-label">BB Code ({{ $photo->width }}x{{ $photo->height }})</label>
		<div class="col-sm-5">
			<input id="large" class="form-control" onclick="copyToClipboard('large')" type="text" value="[url=http://{{ Config::get('app.domain') }}{{ $photo->url }}][img]{{ $cdn }}{{ $photo->scale }}[/img][/url]">
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-4 control-label">BB Code (Thumbnail)</label>
		<div class="col-sm-5">
			<input id="small" class="form-control" onclick="copyToClipboard('small')" type="text" value="[url=http://{{ Config::get('app.domain') }}{{ $photo->url }}][img]{{ $cdn }}{{ $photo->thumbnail }}[/img][/url]">
		</div>
	</div>
	@endif

	@if ( $photo->user_id == $me->id || $me->is_moderator )
	<div class="form-group">
		<div class="col-sm-5 col-sm-offset-4">
		<div class="btn-group btn-group-sm">
			<a class="btn btn-primary" href="/edit-photo/{{ $photo->id }}"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
			<a class="btn btn-danger" href="/delete-photo/{{ $photo->id }}"><span class="glyphicon glyphicon-remove"></span></a>
		</div>
		</div>
	</div>
	@endif

	</form>

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

