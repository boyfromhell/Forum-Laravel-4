@include ('users.menu')

<div class="bg-info">
	<div class="row">
	<div class="col-sm-8">

	<div class="avatar pull-left" style="margin:10px">
	@if ( $user->avatar->id )
	<a href="{{ $user->url }}"><img src="{{ $cdn }}/images/avatars/{{ $user->avatar->file }}" alt="{{{ $user->name }}}'s avatar" title="{{{ $user->name }}}'s avatar"></a>
	@endif

	</div>
	<div class="pull-left" style="padding:10px">

	<div id="user{{ $content_id }}" class="usermenu" name="uname" onMouseOver="userover({{ $content_id }});" onMouseOut="userout({{ $content_id }});" onClick="showuser({{ $content_id }});">{{{ $user->name }}}</div>
	
	<img src="/images/{{ $user->online_text }}.png" style="padding:7px 0px" title="{{{ $user->name }}} is {{ $user->online_text }}">
	<div class="break"></div>
	
	<div style="padding-left:11px; font-size:8pt; margin-top:-8px;">

	@if ( $user->level->image )
	<img src="/images/titles/{{ $user->level->image }}" style="vertical-align:middle; padding-right:6px;">
	@endif
	{{{ $user->level->name }}}<br>

	<br><br>
	</div>
	</div>

	</div>	
	<div class="col-sm-4">
	Joined: {{ Helpers::local_date("M j, Y", $user->created_at) }}<br>
	@if ( $user->location )
	Location: {{{ $user->location }}}<br>
	@endif
	
	@foreach ( $user->custom as $custom )
		{{{ $custom->field->name }}}: {{{ $custom->value }}}<br>
	@endforeach
	
	Posts: {{ number_format($user->total_posts) }}<br>
	
	@if ( count($user->groups) > 0 )
		@foreach ( $user->groups as $group )
			@if ( $group->badge )
			<a href="{{ $group->url }}" title="{{{ $group->name }}}"><img src="/images/groups/{{ $group->badge }}"></a>
			@endif
		@endforeach
	@endif

	</div>
	</div>
</div>
