<ul class="dropdown-menu">
	<li><a href="{{ $user->url }}">View profile</a></li>	
@if ( $me->is_admin )
	<li><a href="/admin/edit_user?id={{ $user->id }}">Edit profile</a></li>
@elseif ( $user->id == $me->id )
	<li><a href="/users/edit">Edit profile</a></li>
@endif

	<li class="divider"></li>
	
	<li><a href="/messages/compose?u={{ $user->id }}">Send message</a></li>
@if ( $user->allow )
	<li><a href="/email.php?u={{ $user->id }}">Send email</a></li>
@endif

	<li class="divider">
	
	<li><a href="/forum/search?u={{ $user->id }}">Find posts</a></li>
	<li><a href="/forum/search?u={{ $user->id }}&amp;mode=topics">Find topics</a></li>
</ul>
