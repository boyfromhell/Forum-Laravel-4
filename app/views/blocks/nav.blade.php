<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
<div class="container-fluid">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>

		<a class="navbar-brand" href="/">
			<span class="hidden-xs">{{{ Config::get('app.forum_name') }}}</span>
			<span class="visible-xs">{{{ Config::get('app.short_name') }}}</span>
		</a>
	</div>

	<div class="collapse navbar-collapse" id="nav">
	<ul class="nav navbar-nav">
	@foreach ( $menu as $toc )
		<li class="{{ $_PAGE['category'] == $toc->page ? 'active' : '' }} {{ $toc->class }}"><a href="{{ $toc->url }}">{{ $toc->name }}</a></li>
	@endforeach
	</ul>

	<ul class="nav navbar-nav navbar-right">
		<li class="dropdown">
			<a href="{{ $me->url }}" class="dropdown-toggle" data-toggle="dropdown">
@if ( $me->avatar->id )
<img class="avatar" src="{{ $cdn }}/images/avatars/{{ $me->avatar->file }}">
@endif
{{{ $me->name }}} <span class="caret"></span>
</a>
			<ul class="dropdown-menu no-collapse" role="menu">
			@if ( $me->id )
				<li><a href="/profile"><span class="glyphicon glyphicon-user"></span> My profile</a></li>
				<li><a href="/edit-profile"><span class="glyphicon glyphicon-pencil"></span> Edit profile</a></li>
				<li><a href="/settings"><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
				<li><a href="/signout"><span class="glyphicon glyphicon-off"></span> Sign out</a></li>
			@else
				<li><a href="/signin"><span class="glyphicon glyphicon-log-in"></span> Sign in</a></li>
				<li><a href="/signup"><span class="glyphicon glyphicon-user"></span> Register</a></li>
			@endif
			</ul>
		</li>
	</ul>
</div>
</nav>
