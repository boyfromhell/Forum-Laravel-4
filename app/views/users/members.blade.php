@extends('layout')

@section('content')

<div style="width:30%; text-align:center;">
<div class="welcome small{{ $pages > 1 ? ' no-margin' : '' }}">

	<div class="header">Search Members</div>

	<form method="GET" action="/community/members">
	<div class="body">

	{{ Form::text('search', $search, ['class' => 'form-control input-sm', 'maxlength' => 50]) }}
	{{ Form::submit('Go', ['class' => 'btn btn-primary btn-sm']) }}

	</div>
	</form>

</div>
</div>

{{ $users->links() }}

<div class="break"></div>

<div class="welcome wide no-margin">

	<div class="table-header">
		<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<th class="icon">&nbsp;</th>
			<th><a href="{{ $sort_url }}sort=name&amp;order={{ $orderby == 'name' && $order == 'asc' ? 'desc' : 'asc' }}">Username</a></th>
			@foreach ( $customs as $custom )
			<th width="{{ $column_width }}%">{{{ $custom->name }}}</th>
			@endforeach
			<th width="14%"><a href="{{ $sort_url }}sort=joined&amp;order={{ $orderby == 'created_at' && $order == 'asc' ? 'desc' : 'asc' }}">Joined</a></th>
			<th class="posts"><a href="{{ $sort_url }}sort=posts&amp;order={{ $orderby == 'posts' && $order == 'desc' ? 'asc' : 'desc' }}">Posts</a></th>
			<th style="width:10px">&nbsp;</th>
		</tr>
		</table>
	</div>

	<div class="header">Members</div>

	<div class="body">

@if ( count($users) > 0 )
<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
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
</table>
@else
	<div class="empty">No members found</div>
@endif

	</div>
	
</div>

{{ $users->links() }}

@stop
