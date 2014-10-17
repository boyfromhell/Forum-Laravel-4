@extends('layout')

@section('content')

<div class="row">
<div class="col-md-3 col-sm-6">
<div class="panel panel-info">

	<div class="panel-heading">Search Members</div>

	<form method="GET" action="/community/members">
	<div class="panel-body">

	{{ Form::text('search', $search, ['class' => 'form-control input-sm', 'maxlength' => 50]) }}
	{{ Form::submit('Go', ['class' => 'btn btn-primary btn-sm']) }}

	</div>
	</form>
</div>
</div>
</div>

{{ $users->links() }}

<div class="panel panel-primary">

	<div class="panel-heading">Members</div>

	<table class="table table-hover">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th><a href="{{ $sort_url }}sort=name&amp;order={{ $orderby == 'name' && $order == 'asc' ? 'desc' : 'asc' }}">Username</a></th>
		@foreach ( $customs as $custom )
		<th width="{{ $column_width }}%">{{{ $custom->name }}}</th>
		@endforeach
		<th width="14%"><a href="{{ $sort_url }}sort=joined&amp;order={{ $orderby == 'created_at' && $order == 'asc' ? 'desc' : 'asc' }}">Joined</a></th>
		<th class="posts"><a href="{{ $sort_url }}sort=posts&amp;order={{ $orderby == 'posts' && $order == 'desc' ? 'asc' : 'desc' }}">Posts</a></th>
	</tr>
	</thead>
	<tbody>
@if ( count($users) > 0 )
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
	<div class="empty">No members found</div>
@endif

</div>

{{ $users->links() }}

@stop
