@extends('print')

@section('content')

<h1>{{{ $topic->title }}}</h1>
<h3><a href="{{ $topic->url }}">http://{{ Config::get('app.domain') }}{{ $topic->url }}</a></h3><br>

<a href="/forum/"><b>{{ Config::get('app.forum_name') }}</b></a> &gt; <a href="{{ $forum->url }}"><b>{{ $forum->name }}</b></a><br><br>

<div class="print-hide">
<div style="float:right"><a href="" onClick="$('.post').show(); return false">Show all posts</a></div>

<label><input type="checkbox" checked tabindex="1" onChange="$('fieldset').toggle()">Display attachments</label>
<label><input type="checkbox" checked tabindex="1" onChange="$('img.post-image').toggle()">Display images</label>
<div class="break"></div>
</div>

@foreach ( $posts as $post )
@if ( !$post->ignored )
<div class="post">
	<div class="post-date">
		<span>#{{ $post->count }}</span>
		<span>{{ $post->formatted_date }}</span>
		<span class="print-hide"><a href="" onClick="$(this).closest('.post').hide(); return false">Hide</a></span>
	</div>
	<div class="post-author">
		<h2><a href="{{ $post->user->url }}">{{{ $post->user->name }}}</a></h2>
	</div>
	
	<div class="break"></div>

	{{ BBCode::parse($post->text, $post->smileys) }}

	<div class="break"></div>
	
	@include ('posts.attachments')
</div>
@endif
@endforeach

@stop

