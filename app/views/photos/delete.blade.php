@extends('layout')

@section('content')

<form class="form" method="post" action="/delete-photo/{{ $photo->id }}">
<div class="panel panel-danger">

	<div class="panel-heading">Delete Photo</div>
	
	<div class="panel-body text-center">
		<p>
		Are you sure you wish to delete this photo

		@if ( $me->is_mod )
		by <b>{{{ $photo->user->name }}}</b>
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
