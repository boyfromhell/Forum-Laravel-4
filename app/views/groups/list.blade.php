@extends('layout')

@section('content')

@if ( $me->id )
<a href="/admin/groups/create" class="button">New Group</a>

<div class="break"></div>
@endif

<div class="welcome wide no-margin">

	<div class="table-header">
		<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<th class="icon">&nbsp;</th>
			<th style="width:15%">Name</th>
			<th class="posts" style="width:15%">Members</th>
			<th style="width:15%">Type</th>
			<th>Description</th>
			<th style="width:10px">&nbsp;</th>
		</tr>
		</table>
	</div>

	<div class="header">Groups</div>

	<div class="body">
	<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
	
	@foreach ( $groups as $group )
	<tr>
		<td class="icon"><img src="{{ $group->badge ? '/images/groups/'.$group->badge : $skin.'icons/group.png' }}"></td>
		<td style="width:15%"><a href="{{ $group->url }}">{{{ $group->name }}}</a></td>
		<td class="posts" style="width:15%">{{ number_format($group->allMembers()->count()) }}</td>
		<td style="width:15%">{{ $group->type }}</a></td>
		<td>{{ BBCode::parse($group->description) }}</a></td>
	</tr>
	@endforeach

	</table>
	</div>
	
</div>

@if ( $me->id )
<a href="/admin/groups/create" class="button">New Group</a>

<div class="break"></div>
@endif

@stop
