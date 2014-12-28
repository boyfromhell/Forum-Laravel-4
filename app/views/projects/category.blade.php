@extends('layout')

@section('buttons')

@if ( $me->is_admin )
<a href="/admin/projects/create" class="btn btn-primary">New Project</a>
@endif

@stop

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">{{{ $_PAGE['title'] }}}</div>

@if ( count($projects) > 0 )

	<table class="table">
	<thead>
	<tr>
		<th class="icon">
		<th>Project</th>
		<th class="date" style="width:15%">Latest Version</th>
		<th class="posts hidden-xs">Files</th>
		<th class="posts hidden-xs">Views</th>
		<th class="posts">Downloads</th>
		@if ( $_PAGE['section'] != 'official' )
		<th class="date" style="width:15%">Author</th>
		@endif
	</tr>
	</thead>
	<tbody>

@foreach ( $projects as $project )
	<tr>
		<td class="icon">
			<img src="{{ $skin }}icons/project_{{{ $_PAGE['section'] }}}.png" alt="file">
		</td>
		<td>
			<a href="{{ $project->url }}">{{{ $project->name }}}</a>
		</td>
		<td style="width:15%; text-align:center">{{{ $project->version }}}</td>
		<td class="posts hidden-xs">{{ $project->total_files }}</td>
		<td class="posts hidden-xs">{{ number_format($project->views) }}</td>
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
		<td colspan="6">
		{{ BBCode::parse($project->description) }}
		</td>
	</tr>
@endforeach
	</tbody>
	</table>

</div>
@else

	<div class="panel-body">
		<p class="empty">
		There are no projects in this category
		</p>
	</div>

@endif

</div>

@stop
