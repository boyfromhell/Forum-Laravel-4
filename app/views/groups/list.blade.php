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
		<th>Name</th>
		<th class="posts" style="width:15%">Members</th>
		<th style="width:15%" class="hidden-xs">Type</th>
		<th style="width:45%" class="hidden-xs">Description</th>
	</tr>
	</thead>
	<tbody>
	@foreach ( $groups as $group )
	<tr>
		<td class="icon"><img src="{{ $group->badge ? '/images/groups/'.$group->badge : $skin.'icons/group.png' }}"></td>
		<td><a href="{{ $group->url }}">{{{ $group->name }}}</a></td>
		<td class="posts">{{ number_format($group->allMembers()->count()) }}</td>
		<td class="hidden-xs">{{ $group->type }}</a></td>
		<td class="hidden-xs">{{ BBCode::parse($group->description) }}</a></td>
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
