@extends('layout')

@section('buttons')
<a href="/messages/compose?t={{ $thread->id }}&all" class="btn btn-primary">Reply All</a>
<a href="/messages/compose?t={{ $thread->id }}" class="btn btn-default">Reply</a>
@stop

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">{{{ $thread->title }}}</div>

</div>

@foreach ( $thread->messages as $message )
	@include ('messages.row')
@endforeach

@stop
