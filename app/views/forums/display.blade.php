@extends('layout')

@section('content')

<h1><a href="{{ $forum->url }}">{{{ $forum->name }}}</a></h1>
<a href="/">{{{ Config::get('app.forum_name') }}}</a> &gt; <a href="/forum/">Forum</a>

@foreach ( $forum->parents as $parent )
	&gt; <a href="{{ $parent->url }}">{{{ $parent->name }}}</a>
@endforeach

<br><br>

{{ $topics->links() }}

@if ( count($children) > 0 )
	@include ('forums.list', ['forums' => $children])
@endif

@if ( $me->id )
<a href="/forum/post?f={{ $forum->id }}" class="button">New Topic</a>

<div class="actions">
	<a href="{{ $forum->url }}?mark">Mark all topics read</a>
</div>
<div class="break"></div>
@endif

<div class="welcome wide no-margin">
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
</div>

<div class="welcome wide{{ $me->id ? ' no-margin' : '' }}">

	<div class="header">Topics</div>
	
	<div class="body">

	<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
		@foreach ( $topics as $topic )
			@include ('topics.row', ['topic_mode' => 'last_post'])
		@endforeach
	</table>
	
	</div>
</div>

@if ( $me->id )
<a href="/forum/post?f={{ $forum->id }}" class="button">New Topic</a>

{{ $topics->links() }}

<div class="break"></div>
@endif

@stop
