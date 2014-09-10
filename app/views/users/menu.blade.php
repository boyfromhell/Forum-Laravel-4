<div id="ubox{{ $content_id }}" class="userbox" name="ubox">
	<a href="{{ $user->url }}">View profile</a>
	
	@if ( $me->is_admin )
		<a href="/admin/edit_user?id={{ $user->id }}">Edit profile</a>
	@elseif ( $user->id == $me->id )
		<a href="/users/edit">Edit profile</a>
	@endif
	<hr>
	
	<a href="/messages/compose?u={{ $user->id }}">Send message</a>
	@if ( $user->allow )
		<a href="/email.php?u={{ $user->id }}">Send email</a>
	@endif
	<hr>
	
	<a href="/forum/search?u={{ $user->id }}">Find posts</a>
	<a href="/forum/search?u={{ $user->id }}&amp;mode=topics">Find topics</a>
</div>
