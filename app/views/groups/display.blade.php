@extends('layout')

@section('content')

<div class="welcome">

	<div class="header">{{{ $group->name }}}</div>

	<div class="body">
	
	@if ( $group->badge )
	<img src="/images/groups/{{ $group->badge }}"><br><br>
	@endif
	
	{{ BBCode::parse($group->description) }}<br>
	@if ( $me->id )
		{{ $info }}
	@endif
	
	@if ( $membership == 2 )
	<br><br>
	<a href="/groups/edit?id={{ $group->id }}" class="button small">Edit</a>
	<div class="break"></div>
	@endif
	</div>

</div>

@if ( $me->id && !$group->allMembers->contains($me->id) )
@if ( $group->type == 'open' )
<a href="/groups/join?id={{ $group->id }}" class="button">Join Group</a>
@elseif ( $group->type == 'closed' )
<a href="/groups/join?id={{ $group->id }}" class="button">Request to join</a>
@endif
@endif

<div class="welcome wide no-margin">

	<div class="table-header">
		<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<th class="icon">&nbsp;</th>
			<th>Username</th>
			<th style="width:20%">Status</th>
			<th style="width:10px">&nbsp;</th>
		</tr>
		</table>
	</div>

	<div class="header">Members</div>
	
	<div class="body">

	<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
	
	@foreach ( $group->moderators as $member )
		@include ('groups.members.row', ['role' => 'Moderator'])
	@endforeach

	@foreach ( $group->members as $member )
		@include ('groups.members.row', ['role' => ''])
	@endforeach

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

</div>

@if ( $me->id && !$group->allMembers->contains($me->id) )
@if ( $group->type == 'open' )
<a href="/groups/join?id={{ $group->id }}" class="button">Join Group</a>
@elseif ( $group->type == 'closed' )
<a href="/groups/join?id={{ $group->id }}" class="button">Request to join</a>
@endif
@endif

<div class="break"></div>

@stop
