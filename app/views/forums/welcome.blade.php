@extends('layout')

@section('content')

<div style="margin-bottom:6px">
	<h1><a href="/forum/">{{{ Config::get('app.forum_name') }}}</a></h1>
</div>

<div class="col-md-9 col-md-push-3">
	@if ( !$me->id && ! Config::get('app.registration_enabled'))
	<div class="alert notice">
		<span style="font-size:16pt">
		<b>MEMBERSHIP APPLICATIONS ARE BACK UP!</b><br>
		Sorry for the inconvenience.<br><br>

		Join MidwestSCC here: <a href="/apply">Apply for membership</a>
	</div>
	@endif

	@if ( !$me->id || $announcement->id )
	<div class="welcome{{ $me->id ? ' no-margin' : '' }}">

		<div class="header">{{{ $me->id ? $announcement->title : 'Welcome to the '.Config::get('app.forum_name') }}}</div>

		@if ( $me->id )
		<div id="announcement_{{ $announcement->id }}" class="announcement body">{{ BBCode::parse($announcement->text) }}</div>
		@else
		<div class="body">
			@include ('custom.welcome')
		</div>
		@endif
		
	</div>
	@endif

	@if ( $me->id )
	<div class="actions">
		<a href="/?mark">Mark all topics read</a>
	</div>
	<div class="break"></div>
	@endif
	
	<div class="welcome wide{{ !$me->id ? ' no-margin' : '' }}">
	
		<div class="table-header">
			<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<th class="icon">&nbsp;</th>
				<th class="icon">&nbsp;</th>
				<th style="width:50%">Topics</th>
				<th>Last Post</th>
				<th class="posts">Replies</th>
				<th class="posts">Views</th>
				<th style="width:10px">&nbsp;</th>
			</tr>
			</table>
		</div>

		<div class="header">Latest Topics</div>
		
		<div class="body">

		<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
			@foreach ( $topics as $topic )
				@include ('topics.row', ['topic_mode' => 'last_post'])
			@endforeach
		</table>
		
		</div>
	</div>
</div>

<div class="col-md-3 col-md-pull-9">

@if ( count($birthdays) > 0 )
	<div class="welcome small">
		<div class="birthday">
		Happy birthday {foreach $birthdays as $k => $birth_user}<a href="{$birth_user->url}">{htmlspecialchars($birth_user->name)}</a>{if count($birthdays) > 2 && $k < count($birthdays)-1}, {/if}{if $k == count($birthdays)-2} and {/if}{/foreach}!
		</div>
	</div>
@endif

	<div class="welcome small">
		<div class="header">Random Photo</div>
		
		<div class="body">
			<div class="photo" style="height:195px; float:none; margin:0 auto">
				<a class="thumb" href="{{ $photo->url }}"><img src="{{ $cdn }}{{ $photo->thumbnail }}" alt="thumbnail"></a>
				by <a href="{{ $photo->user->url }}">{{{ $photo->user->name }}}</a>
			</div>
			<div class="break"></div>
		</div>
	</div>
	
	<div class="welcome small">
		<div class="header">Most Recent Album</div>
		
		<div class="body">
			<div class="photo" style="height:215px; float:none; margin:0 auto">
				<a class="thumb" href="{{ $album->url }}"><img src="{{ $cdn }}{{ $album->coverPhoto->thumbnail }}" alt="album cover"></a>
				<div style="height:18px; overflow:hidden">{{{ $album->name }}}</div>
				by <a href="{{ $album->user->url }}">{{{ $album->user->name }}}</a>
			</div>
			<div class="break"></div>
		</div>
	</div>
	
	<div class="welcome small{{ !$me->id ? ' no-margin' : '' }}">

		<div class="header">
		@if ( $me->id )
			<a href="/forum/stats">Forum Statistics</a>
		@else
			Forum Statistics
		@endif
		</div>

		<div class="body">
		
		Topics: <b>{{ $stats['total_topics'] }}</b><br>
		Posts: <b>{{ $stats['total_posts'] }}</b><br>
		Members: <b>{{ $stats['total_users'] }}</b><br>
		Welcome to our newest member, <a href="{{ $newest_user->url }}">{{{ $newest_user->name }}}</a><br><br>

		<div id="online">
		{{ $online_stats }}
		</div>
		
		</div>

	</div>
	
</div>

<div class="break"></div>

@if ( !$is_mobile && Module::isActive('shoutbox') )
<iframe src="/community/shoutbox" width="100%" height="200" frameborder="no" scrolling="auto"></iframe>
@endif

@stop
