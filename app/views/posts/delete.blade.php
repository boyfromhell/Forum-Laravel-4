@extends('layout')

@section('content')

<form class="form" method="post" action="/delete-post/{{ $post->id }}">
<div class="panel panel-danger">

	<div class="panel-heading">Delete Post</div>
	
	<div class="panel-body text-center">
		<p>
		Are you sure you wish to delete this post

		@if ( $me->is_moderator || $me->is_admin )
		by <b>{{{ $post->user->name }}}</b>
		@endif
		?
		</p>
	</div>

	<div class="panel-footer text-center">

		{{ Form::submit('Delete', ['name' => 'confirm', 'class' => 'btn btn-danger btn-once', 'data-loading-text' => 'Deleting...']) }}
		{{ Form::submit('Cancel', ['name' => 'cancel', 'class' => 'btn btn-default']) }}

	</div>
</div>
</form>

@stop
