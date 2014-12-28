@extends('layout')

@section('header')
<div class="row">
<div class="col-md-12 col-lg-8">
<div class="panel panel-info">

	<div class="panel-heading">Search Results</div>
	
	<div class="panel-body">
		{{ $searched_for_html }}
	</div>
</div>
</div>
</div>
@stop

@section('buttons')
<div class="pull-right">
{{ $results->links() }}
</div>
@stop

@section('content')
@if( $query->show == 0 && count($posts) > 0 )
	@foreach ( $posts as $post_key => $post )

	<div class="panel panel-primary">

		<div class="panel-heading">
			Posted by <a href="{{ $post->user->url }}"><b>{{{ $post->user->name }}}</b></a>, {{ Helpers::date_string($post->created_at, 1) }}
		</div>

		<div class="subheading" style="padding:8px 10px">
			<a href="{{ $post->url }}"><b>{{{ $post->topic->title }}}</b></a>
			in forum <a href="{{ $post->topic->forum->url }}"><b>{{{ $post->topic->forum->name }}}</b></a>
		</div>
		
		<div class="body">
			@include ('posts.body')
		</div>
	</div>

	@endforeach
@elseif( $query->show == 1 && count($topics) > 0 )

	<div class="panel panel-primary">

		<div class="panel-heading">Search Results</div>

		@include ('topics.list', ['show_last_post' => true, 'show_forum' => true])

	</div>

@else

	<div class="panel panel-primary">

		<div class="panel-heading">Search Results</div>

		<div class="panel-body">
			<p class="empty">
			No search results matched your critera. <a href="{{ $query->url }}">Modify</a> your search
			</p>
		</div>

	</div>

@endif
@stop
