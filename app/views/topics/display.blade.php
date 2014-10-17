@extends('layout')

@section('content')

<h1><a href="{{ $topic->url }}">{{{ $topic->title }}}</a></h1>

<ol class="breadcrumb">
	<li><a href="/">{{{ Config::get('app.forum_name') }}}</a></li>
	<li><a href="/forum/">Forum</a></li>
@foreach ( $forum->parents as $parent )
	<li><a href="{{ $parent->url }}">{{{ $parent->name }}}</a></li>
@endforeach
	<li><a href="{{ $forum->url }}">{{{ $forum->name }}}</a></li>
</ol>

{{ $posts->links() }}

@if ( $me->id )
<a href="/new-topic/{{ $forum->id }}" class="btn btn-default">New Topic</a>
<a href="/reply-to-topic/{{ $topic->id }}" class="btn btn-{{ $topic->status ? 'danger' : 'primary' }}">{{ $topic->status ? 'Locked' : 'Reply' }}</a>
@endif

<div class="actions">
	@if ( $me->id )
	<a href="{{ $topic->url }}?{{ $page > 1 ? 'page='.$page.'&amp;' : '' }}{{ $subscribed ? 'un' : '' }}subscribe">{{ $subscribed ? 'Unsubscribe' : 'Subscribe' }}</a> - 
	@endif
	<a href="{{ str_replace('topics/', 'print/', $topic->url) }}" rel="nofollow">Print</a>
</div>

<div class="break"></div>

@include ('topics.poll', ['poll' => $topic->poll])

@foreach ( $posts as $post_key => $post )

	@include ('posts.row')

@endforeach

@if ( $me->id )
<a href="/new-topic/{{ $forum->id }}" class="btn btn-default">New Topic</a>
<a href="/reply-to-topic/{{ $topic->id }}" class="btn btn-{{ $topic->status ? 'danger' : 'primary' }}">{{ $topic->status ? 'Locked' : 'Reply' }}</a>
@endif

{{ $posts->links() }}

<div class="break"></div>

<br>
<a href="/" style="text-decoration:none;">{{{ Config::get('app.forum_name') }}}</a> &gt; <a href="/forum/">Forum</a> &gt; <a href="{{ $forum->url }}" style="text-decoration:none;">{{{ $forum->name }}}</a> &gt; <a href="{{ $topic->url }}" style="text-decoration:none;">{{{ $topic->title }}}</a>
<br><br>

@if ( $me->id && !$topic->status )
	@include ('topics.quick_reply')
@endif

@if ( $me->is_moderator )
	@include ('topics.moderate')
@endif

@stop
