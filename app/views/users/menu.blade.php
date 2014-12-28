<ul class="dropdown-menu">
	<li><a href="{{ $user->url }}">View profile</a></li>	
@if ($me->is_admin)
	<li><a href="/admin/edit_user?id={{ $user->id }}">Edit profile</a></li>
@elseif ($user->id == $me->id)
	<li><a href="/edit-profile">Edit profile</a></li>
@endif

@if ($user->id != $me->id)
	<li class="divider"></li>
	
	<li><a href="/messages/compose?user={{ $user->id }}">Send message</a></li>
@endif

@if ($user->allow)
	<li><a href="/email.php?user={{ $user->id }}">Send email</a></li>
@endif

	<li class="divider">
	
	<li><a href="/search?user={{ $user->id }}">Find posts</a></li>
	<li><a href="/search?user={{ $user->id }}&amp;mode=topics">Find topics</a></li>
</ul>
