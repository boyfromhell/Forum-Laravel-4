@section('heading')
<div class="row">
<div class="col-md-12 col-lg-8">
<div class="panel panel-info">

	<div class="panel-heading">Search Results</div>
	
	<div class="panel-body">
		{{{ $searched_for_html }}}
	</div>
</div>
</div>
</div>
@stop

@section('buttons')
{{ $results->paginate() }}
@stop

@section('content')
@if( $query->show == 0 && count($posts) > 0 )
	@foreach ( $posts as $post_key => $post )

	<div class="panel panel-primary">

		<div class="panel-heading">
			Posted by <a href="{{ $post->user->url }}"><b>{{{ $post->user->name }}}</b></a>, {{ $post->formatted_date }}
		</div>

		<div class="subheading" style="padding:8px 10px">
			<a href="{{ $post->url }}"><b>{{{ $post->topic->title }}}</b></a>
			in forum <a href="{{ $post->forum->url }}"><b>{{{ $post->forum->name }}}</b></a>
		</div>
		
		<div class="body">
			{{ BBCode::parse($post->content, $post->smileys, true) }}
		</div>
	</div>

	@endforeach
@elseif( $query->show == 1 && count($topics) > 0 )

	<div class="panel panel-primary">

		<div class="panel-heading">Search Results</div>

		@include ('topics.list', ['topic_mode' => 'forum'])

	</div>

@else

	<div class="panel panel-primary">

		<div class="panel-heading">Search Results</div>

		<div class="panel-body">
			<p class="empty">
			No search results matched your critera. <a href="{{ $query->edit_url }}">Modify</a> your search
			</p>
		</div>

	</div>

@endif
@stop
