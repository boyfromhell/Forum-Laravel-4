@extends('layout')

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">
		{{ $message->subject }}
	</div>

	<div class="subheading">
		From:
		@if ( $message->user->id )
			<a href="{{ $message->user->url }}">{{{ $message->user->name }}}</a>
		@else
			<a href="mailto:{{ $message->email }}">{{{ $message->name }}}</a>
		@endif
	</div>

	<div class="panel-body">
		{datestring($message->date, 1)}

		{{ BBCode::parse($message->message) }}
	</div>
	
</div>

@stop

