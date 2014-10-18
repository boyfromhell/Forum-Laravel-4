@extends('layout')

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">{{{ $group->name }}}</div>

	<div class="panel-body">
	
	@if ( $group->badge )
	<img src="/images/groups/{{ $group->badge }}"><br><br>
	@endif
	
	{{ BBCode::parse($group->description) }}<br>
	@if ( $me->id )
		{{ $info }}
	@endif
	
	@if ( $membership == 2 )
	<br><br>
	<a href="/groups/edit?id={{ $group->id }}" class="btn btn-primary btn-xs">Edit</a>
	<div class="clearfix"></div>
	@endif
	</div>

</div>

@if ( $me->id && !$group->allMembers->contains($me->id) )
@if ( $group->type == 'open' )
<a href="/groups/join?id={{ $group->id }}" class="btn btn-primary">Join Group</a>
@elseif ( $group->type == 'closed' )
<a href="/groups/join?id={{ $group->id }}" class="btn btn-primary">Request to join</a>
@endif
@endif

<div class="panel panel-info">

	<div class="panel-heading">Members</div>

	<table class="table table-hover">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th>Username</th>
		<th style="width:20%">Status</th>
	</tr>
	</thead>
	<tbody>
	@foreach ( $group->moderators as $member )
		@include ('groups.members.row', ['role' => 'Moderator'])
	@endforeach

	@foreach ( $group->members as $member )
		@include ('groups.members.row', ['role' => ''])
	@endforeach
	</tbody>
	</table>

	{{-- if $membership == 2}
	<form method="post" action="{{ $group->url }}">
	<div>
		<input type="text" name="username" tabindex="1" placeholder="Username">
		<select name="type" tabindex="1">
			<option value="0" selected>Member</option>
			<option value="1">Moderator</option>
		</select>
		<input type="submit" name="add_member" tabindex="1" value="Add">
	</div>
	</form>
	{/if --}}

</div>

@if ( $me->id && !$group->allMembers->contains($me->id) )
@if ( $group->type == 'open' )
<a href="/groups/join?id={{ $group->id }}" class="btn btn-primary">Join Group</a>
@elseif ( $group->type == 'closed' )
<a href="/groups/join?id={{ $group->id }}" class="btn btn-primary">Request to join</a>
@endif
@endif

@stop
