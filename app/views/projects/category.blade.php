@extends('layout')

@section('buttons')

@if ( $me->is_admin )
<a href="/admin/projects/create" class="btn btn-primary">New Project</a>
@endif

@stop

@section('content')

@if ( count($projects) > 0 )

@foreach ( $projects as $i => $project )

<div class="panel panel-primary">

	<div class="panel-heading">{{{ $project->name }}}</div>

	<table class="table">
@if ( $i == 0 )
	<thead>
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
	</tr>
	</thead>
	<tbody>
@endif
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
	</tbody>
	</table>

</div>
@endforeach
@else

<div class="panel panel-primary">

	<div class="panel-heading">Projects</div>
	
	<div class="panel-body">
		<p class="empty">
		There are no projects in this category
		</p>
	</div>
	
</div>

@endif

@stop
