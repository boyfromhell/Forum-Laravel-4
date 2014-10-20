@extends('layout')

@section('content')

<form class="form" method="post" action="/delete-message/{{ $message->id }}">
<div class="panel panel-danger">

	<div class="panel-heading">Delete Private Message</div>
	
	<div class="panel-body text-center">
		<p>
		Are you sure you wish to delete this message from <b>{{{ $message->from->name }}}</b>?
		</p>
	</div>

	<div class="panel-footer text-center">

		{{ Form::submit('Delete', ['name' => 'confirm', 'class' => 'btn btn-danger btn-once', 'data-loading-text' => 'Deleting...']) }}
		{{ Form::submit('Cancel', ['name' => 'cancel', 'class' => 'btn btn-default']) }}

	</div>
</div>
</form>

@stop
