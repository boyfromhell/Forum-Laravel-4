@extends('layout')

@section('header')
<h1><a href="{{ $topic->url }}">{{{ $topic->title }}}</a></h1>

<ol class="breadcrumb">
	<li><a href="/">{{{ Config::get('app.forum_name') }}}</a></li>
	<li><a href="/forum/">Forum</a></li>
@foreach ( $forum->parents as $parent )
	<li><a href="{{ $parent->url }}">{{{ $parent->name }}}</a></li>
@endforeach
	<li><a href="{{ $forum->url }}">{{{ $forum->name }}}</a></li>
</ol>
@stop

@section('buttons')
@if ( $me->id )
<div class="pull-left">
<a href="/new-topic/{{ $forum->id }}" class="btn btn-default">New Topic</a>
<a href="/reply-to-topic/{{ $topic->id }}" class="btn btn-{{ $topic->is_locked ? 'danger' : 'primary' }}">{{ $topic->is_locked ? 'Locked' : 'Reply' }}</a>
</div>
@endif

<div class="pull-right">
	{{ $posts->links() }}
</div>
@stop

@section('content')

<div class="pull-right">
	@if ( $me->id )
	<a href="{{ $topic->url }}?{{ $page > 1 ? 'page='.$page.'&amp;' : '' }}{{ $subscribed ? 'un' : '' }}subscribe">{{ $subscribed ? 'Unsubscribe' : 'Subscribe' }}</a> - 
	@endif
	<a href="{{ str_replace('topics/', 'print/', $topic->url) }}" rel="nofollow">Print</a>
</div>

<div class="clearfix"></div>

@include ('topics.poll', ['poll' => $topic->poll])

@foreach ( $posts as $post_key => $post )

	@include ('posts.row')

@endforeach

@stop

@section('footer')
@include('blocks.jumpbox', ['selected' => $forum->id])

<ol class="breadcrumb">
	<li><a href="/">{{{ Config::get('app.forum_name') }}}</a></li>
	<li><a href="/forum/">Forum</a></li>
	<li><a href="{{ $forum->url }}">{{{ $forum->name }}}</a></li>
	<li><a href="{{ $topic->url }}">{{{ $topic->title }}}</a></li>
</ol>

@if ( $me->id && ! $topic->is_locked )
	@include ('topics.quick_reply')
@endif

@if ( $me->is_moderator )
	@include ('topics.moderate')
@endif

@stop
