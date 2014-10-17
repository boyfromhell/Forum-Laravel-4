@extends('layout')

@section('content')

@if ( $project->user_id == $me->id || $me->is_admin )
<a href="/projects/upload?id={{ $project->id }}" class="btn btn-primary">Upload</a>

<div class="break"></div>
@endif

<div class="welcome">

	<div class="header">{{{ $project->name }}}</div>
	
	<div class="body">

	@if ( $project->category != 1 )
		@if ( $project->user_id )
			Author: <a href="{{ $project->user->url }}">{{{ $project->user->name }}}</a><br><br>
		@elseif ( $project->author )
			Author: {{{ $project->author }}}<br><br>
		@endif
	@endif

	{{ BBCode::parse($project->description) }}

	@if ( $project->user_id == $me->id || $me->is_admin )
	<br><br>
	<a href="/admin/projects/{{ $project->id }}/edit" class="btn btn-primary btn-xs">Edit</a>
	<div class="break"></div>
	@endif
	</div>

</div>

<div class="welcome wide no-margin">

	<div class="table-header">
		<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<th class="icon">&nbsp;</th>
			<th>File</th>
			<th class="posts" style="width:15%">Version</th>
			<th class="posts">Size</th>
			<th class="posts">Platform</th>
			<th class="posts">Type</th>
			<th class="posts">Downloads</th>
			<th style="width:15%">Date</th>
			<th style="width:10px">&nbsp;</th>
		</tr>
		</table>
	</div>

	<div class="header">Files</div>
	
	<div class="body">
	<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">

@foreach ( $project->downloads as $download )
<tr>
	<td class="icon">
		<img src="{{ $skin }}icons/download{{-- if $download->views > 100}_hot{/if --}}.png">
	</td>
	<td>
		<a href="{{ $download->url }}" rel="nofollow">{{{ $download->file }}}</a>
	</td>
	<td class="posts" style="width:15%">{{{ $download->version }}}</td>
	<td class="posts">{{ $download->size }}</td>
	<td class="posts">{{{ $download->platform }}}</td>
	<td class="posts">{{{ $download->type }}}</td>
	<td class="posts">{{ number_format($download->views) }}</td>
	<td style="width:15%; text-align:center">{{ Helpers::local_date('M j, Y', $download->created_at) }}</td>
</tr>
@endforeach

	</table>
	</div>

</div>

@if ( $project->user_id == $me->id || $me->is_admin )
<a href="/projects/upload?id={{ $project->id }}" class="btn btn-primary">Upload</a>

<div class="break"></div>
@endif

@stop
