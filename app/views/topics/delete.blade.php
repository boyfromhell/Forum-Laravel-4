@extends('layout')

@section('content')

<form class="form" method="post" action="/delete-topic/{{ $topic->id }}">
<div class="panel panel-danger">

	<div class="panel-heading">Delete Topic</div>
	
	<div class="panel-body text-center">
		<p>
		Are you sure you wish to delete this topic ({{ $topic->posts()->count() }} posts)?
		</p>

		<p>
		<a href="{{ $topic->url }}">{{{ $topic->title }}}</a> in forum <a href="{{ $topic->forum->url }}">{{{ $topic->forum->name }}}</a>
		</p>
	</div>

	<div class="panel-footer text-center">

		{{ Form::submit('Delete', ['name' => 'confirm', 'class' => 'btn btn-danger btn-once', 'data-loading-text' => 'Deleting...']) }}
		{{ Form::submit('Cancel', ['name' => 'cancel', 'class' => 'btn btn-default']) }}

	</div>
</div>
</form>

@stop
