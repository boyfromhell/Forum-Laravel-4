@extends('layout')

@section('header')
<div class="row">
<div class="col-md-4 col-sm-6">
<div class="panel panel-info">

	<div class="panel-heading">Search Members</div>

	<form method="GET" action="/members">
	<div class="panel-body">

	<div class="input-group">
	{{ Form::text('search', $search, ['class' => 'form-control input-sm', 'maxlength' => 50]) }}
	<div class="input-group-btn">
	{{ Form::submit('Go', ['class' => 'btn btn-primary btn-sm']) }}
	</div>
	</div>

	</div>
	</form>
</div>
</div>
</div>
@stop

@section('buttons')
<div class="pull-right">
	{{ $users->appends(['search' => $search])->links() }}
</div>
@stop

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">Members</div>

@if ( count($users) > 0 )
	<table class="table table-hover">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th><a href="{{ $sort_url }}sort=name&amp;order={{ $orderby == 'name' && $order == 'asc' ? 'desc' : 'asc' }}">Username</a></th>
		@foreach ( $customs as $custom )
		<th width="{{ $column_width }}%">{{{ $custom->name }}}</th>
		@endforeach
		<th class="date" width="14%"><a href="{{ $sort_url }}sort=joined&amp;order={{ $orderby == 'created_at' && $order == 'asc' ? 'desc' : 'asc' }}">Joined</a></th>
		<th class="posts"><a href="{{ $sort_url }}sort=posts&amp;order={{ $orderby == 'posts' && $order == 'desc' ? 'asc' : 'desc' }}">Posts</a></th>
	</tr>
	</thead>
	<tbody>
@foreach ( $users as $member )
	<tr class="trhov">
		<td class="icon">
		@if ( $member->level->image )
			<img src="/images/titles/{{ $member->level->image }}" title="{{{ $member->level->name }}}">
		@else
			{{ $member->counter }}
		@endif
		</td>
		<td>
			<a href="{{ $member->url }}">{{{ $member->name }}}</a>
		</td>
	@foreach ( $customs as $custom )
		<td width="{{ $column_width }}%">{{{ $member->custom[$custom->id]->value }}}</td>
	@endforeach
		<td width="14%" class="date">{{ Helpers::local_date('M j, Y', $member->created_at) }}</td>
		<td class="posts">{{ number_format($member->total_posts) }}</td>
	</tr>
@endforeach
	</tbody>
</table>
@else

	<div class="panel-body">
		<p class="empty">No members found</p>
	</div>

@endif

</div>

@stop
