@extends('layout')

@section('content')

<div class="welcome wide">

	<div class="header">View Profile</div>

	<div class="body">
	
	<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>

	<td class="left v_top" style="width:50%; padding:10px 15px;">
	<h1>{{{ $user->name }}} <img src="/images/{{ $online_text }}.png" alt="{{{ $user->name }}} is {{ $online_text }}" title="{{{ $user->name }}} is {{ $online_text }}"></h1>
	<small>
	@if ( $user->level->image )
	<img src="/images/titles/{{ $user->level->image }}" style="vertical-align:middle">
	@endif
	{{{ $user->level->name }}}</small>

	@if ( $user->avatar->id )<br><br>
	<img id="profileavatar" src="{{ $cdn }}/images/avatars/{{ $user->avatar->file }}" alt="{{{ $user->name }}}'s avatar" title="{{{ $user->name }}}'s avatar">
	@endif
	</td>
	<td class="right v_top" style="width:50%; padding:15px;">
	Last Visit: <b>{{ $user->last_online }}</b><br>
	Viewed <b>{{ number_format($user->views) }}</b> time{{ $user->views != 1 ? 's' : '' }}<br><br>
	@if ( $show_birthday )
	Birthday: <b>{{ $birthday }}</b><br>
	@endif
	Member Since: <b>{{-- local_date('F j, Y', $user->joined) --}}</b><br><br>
	
	Posts: <b>{{ number_format($user->posts) }}</b> ({{ $user->posts_per_day }} posts per day, {{ $user->posts_percent }}% of total)<br>
	Shoutbox Posts: <b>{{ number_format($user->shouts) }}</b> ({{ $user->shouts_per_day }} per day, {{ $user->shouts_percent }}% of total)
	@if ( $me->id )<br><br>
	<a href="/forum/search?u={{ $user->id }}">Find all posts by {{{ $user->name }}}</a><br>
	<a href="/forum/search?u={{ $user->id }}&amp;mode=topics">Find all topics started by {{{ $user->name }}}</a>
	@endif
	<br><br>

	@if ( $me->administrator || $me->id == $user->id )<a href="{{ $edit_url }}" class="button small">Edit</a>@endif
	@if ( $me->administrator || $me->moderator )<a href="/lookup.php?u={{ $user->id }}" class="button small">IP</a>@endif
	<div class="break"></div>
	</td>
</tr>
</table>

	</div>

</div>

<div class="welcome wide no-margin">

	<div class="header">Info</div>

	<div class="body">
	
	<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>

	<td class="left v_top" style="width:50%; padding:10px 15px;">
	@if ( count($user->groups) > 0 )<b>Groups:</b><br>
		@foreach ( $user->groups as $group )
			<img src="{{ $group->badge ? '/images/groups/'.$group->badge : $skin.'icons/group.png' }}"> <a href="{{ $group->url }}">{{{ $group->name }}}</a><br>
		@endforeach
		<br>
	@endif
	@foreach ( $custom as $field )
		<b>{{{ $field->name }}}:</b><br>
		{{{ $field->value }}}<br><br>
	@endforeach
	</td>
	<td class="right v_top" style="width:50%; padding:10px 15px;">@if ( $me->id )
	<b>Contact:</b><br>

	@foreach ( $user->screennames as $im )
		<b>{{ $im->name }}</b> <img src="{{ $im->image }}" alt="{{ $im->name }} status" style="vertical-align:middle"> {{{ $im->screenname }}}<br>
	@endforeach

	@if ( $me->id != $user->id )
	{{--if $allow_email}<a href="/email.php?u={{ $user->id }}">Send {{{ $user->name }}} an email</a><br>--}}
	<a href="/messages/compose?u={{ $user->id }}">Send {{{ $user->name }}} a message</a>
	<br>
	@endif
	<br>
	@endif
	@if ( $website_url )<b>Website:</b><br><a href="{{{ $website_url }}}" rel="nofollow">{{{ $website_text }}}</a><br><br>
	@endif
	@if ( !empty($scores) )
	<b>Honor Rolls:</b><br>
	{if $scores[1]}#{$scores[1]->rank} in victories - {$scores[1]->score}<br>{/if}
	{if $scores[0]}#{$scores[0]->rank} in defeats - {$scores[0]->score}<br>{/if}
	<br>
	@endif
	{if $me->loggedin}
	{if $me->id != $user->id}{if $on_list}<a href="/userlist.php?u={$user->id}&amp;remove=1">Remove from {$list_text} list</a>{else}<a href="/userlist.php?u={$user->id}&amp;buddy=1">Add to buddy list</a><br>
	{if $user->level == 0}<a href="/userlist.php?u={$user->id}&amp;ignore=1">Add to ignore list</a>{/if}{/if}{/if}
	{/if}
	</td>
	</tr>
	
	@if ( $user->sig )
	<tr>
		<td colspan="2"><div class="sig">{{ BBCode::parse($user->sig) }}</div></td>
	</tr>
	@endif
	</table>
	
	</div>
	
</div>

@stop
