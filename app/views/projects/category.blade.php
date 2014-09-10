@extends('layout')

@section('content')

@if ( $me->is_admin )
<a href="/admin/projects/create" class="button">New Project</a>

<div class="break"></div>
@endif

@if ( count($projects) > 0 )
<div class="welcome wide no-margin">
	<div class="table-header">
		<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<th class="icon">
			<th>Project</th>
			<th style="width:15%">Latest Version</th>
			<th class="posts">Files</th>
			<th class="posts">Views</th>
			<th class="posts">Downloads</th>
			@if ( $_PAGE['section'] != 'official' )
			<th style="width:15%">Author</th>
			@endif
			<th style="width:10px">&nbsp;</th>
		</tr>
		</table>
	</div>
</div>

@foreach ( $projects as $i => $project )

<div class="welcome wide{{ $i == count($projects)-1 ? ' no-margin' : '' }}">
	
	<div class="header">{{{ $project->name }}}</div>
	
	<div class="body">
	
	<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td class="icon">
			<img src="{{ $skin }}icons/project_{{{ $_PAGE['section'] }}}.png" alt="file">
		</td>
		<td>
			<a href="{{ $project->url }}">{{{ $project->name }}}</a>
		</td>
		<td style="width:15%; text-align:center">{{{ $project->version }}}</td>
		<td class="posts">{{ $project->total_files }}</td>
		<td class="posts">{{ number_format($project->views) }}</td>
		<td class="posts">{{ number_format($project->total_downloads) }}</td>
		@if ( $_PAGE['section'] != 'official' )
		<td style="width:15%; text-align:center">
			@if ( $project->user_id )
				<a href="{{ $project->user->url }}">{{{ $project->user->name }}}</a>
			@elseif ( $project->author )
				{{{ $project->author }}}
			@endif
		</td>
		@endif
	</tr>
	<tr>
		<td class="icon">&nbsp;</td>
		<td colspan="6" style="padding:5px 0">
		{{ BBCode::parse($project->description) }}
		</td>
	</tr>
	</table>
	
	</div>

</div>
@endforeach
@else

<div class="welcome wide no-margin">

	<div class="header">Projects</div>
	
	<div class="body">

	<div class="empty">
		There are no projects in this category
	</div>
	
	</div>
	
</div>

@endif

@if ( $me->is_admin )
<a href="/admin/projects/create" class="button">New Project</a>

<div class="break"></div>
@endif

@stop
