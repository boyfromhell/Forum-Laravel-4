<div id="welcome">
	@if ( $me->id )
		@if ( $me->avatar->id )
			<img src="{{ $cdn }}/images/avatars/{{ $me->avatar->file }}" style="max-width:24px; max-height:24px">
			<div class="text">
		@endif
		Welcome, <a href="/profile" style="color:#fff">{{{ $me->name }}}</a>. <a href="/logout">Logout</a>
		@if ( $me->avatar->id )
			</div>
			<div class="break"></div>
		@endif
	@else
		<a href="/register">Register</a> or <a href="/login">Login</a>
	@endif
</div>
