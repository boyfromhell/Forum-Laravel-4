@extends('layout')

@section('buttons')

@if ( $me->is_admin )
<a href="/admin/groups/create" class="btn btn-primary">New Group</a>
@endif

@stop

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">Groups</div>

@if ( count($groups) > 0 )
	<table class="table table-hover">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th style="width:15%">Name</th>
		<th class="posts" style="width:15%">Members</th>
		<th style="width:15%">Type</th>
		<th>Description</th>
	</tr>
	</thead>
	<tbody>
	@foreach ( $groups as $group )
	<tr>
		<td class="icon"><img src="{{ $group->badge ? '/images/groups/'.$group->badge : $skin.'icons/group.png' }}"></td>
		<td style="width:15%"><a href="{{ $group->url }}">{{{ $group->name }}}</a></td>
		<td class="posts" style="width:15%">{{ number_format($group->allMembers()->count()) }}</td>
		<td style="width:15%">{{ $group->type }}</a></td>
		<td>{{ BBCode::parse($group->description) }}</a></td>
	</tr>
	@endforeach
	</tbody>
	</table>
@else

	<div class="panel-body">
		<p class="empty">No groups found</p>
	</div>

@endif

</div>

@stop
