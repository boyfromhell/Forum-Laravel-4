@extends('layout')

@section('content')

<h1><a href="{{ $topic->url }}">{{{ $topic->title }}}</a></h1>
<a href="/">{{{ Config::get('app.forum_name') }}}</a> &gt; <a href="/forum/">Forum</a>

@foreach ( $forum->parents as $parent )
	&gt; <a href="{{ $parent->url }}">{{{ $parent->name }}}</a>
@endforeach

&gt; <a href="{{ $forum->url }}">{{{ $forum->name }}}</a>

<br><br>

{{ $posts->links() }}

@if ( $me->id )
<a href="/forum/post?f={{ $forum->id }}" class="button">New Topic</a>
<a href="/forum/post?mode=reply&amp;t={{ $topic->id }}" class="button">{{ $topic->status ? 'Locked' : 'Reply' }}</a>
@endif

<div class="actions">
	@if ( $me->id )
	<a href="{{ $topic->url }}?{{ $page > 1 ? 'page='.$page.'&amp;' : '' }}{{ $subscribed ? 'un' : '' }}subscribe">{{ $subscribed ? 'Unsubscribe' : 'Subscribe' }}</a> - 
	@endif
	<a href="{{ str_replace('topics/', 'print/', $topic->url) }}" rel="nofollow">Print</a>
</div>

<div class="break"></div>

@foreach ( $posts as $post_key => $post )

	@include ('posts.row')

@endforeach

@if ( $me->loggedin )
<a href="/forum/post?f={{ $forum->id }}" class="button">New Topic</a>
<a href="/forum/post?mode=reply&amp;t={{ $topic->id }}" class="button">{{ $topic->status ? 'Locked' : 'Reply' }}</a>
@endif

{{ $posts->links() }}

<div class="break"></div>

<br>
<a href="/" style="text-decoration:none;">{{{ Config::get('app.forum_name') }}}</a> &gt; <a href="/forum/">Forum</a> &gt; <a href="{{ $forum->url }}" style="text-decoration:none;">{{{ $forum->name }}}</a> &gt; <a href="{{ $topic->url }}" style="text-decoration:none;">{{{ $topic->title }}}</a>
<br><br>

@if ( $me->loggedin && !$topic->status )
	@include ('topics.quick_reply')
@endif

@if ( $me->administrator || $me->moderator )
	@include ('topics.moderate')
@endif

@stop
