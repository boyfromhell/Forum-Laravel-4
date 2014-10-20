@extends('layout')

@section('header')
<h1>{{{ $thread->title }}}</h1>

<ol class="breadcrumb">
	<li><a href="/messages">Messages</a></li>
</ol>
@stop

@section('buttons')
<a href="/messages/compose?t={{ $thread->id }}&all" class="btn btn-primary">Reply All</a>
<a href="/messages/compose?t={{ $thread->id }}" class="btn btn-default">Reply</a>
@stop

@section('content')

@foreach ( $thread->messages as $count => $message )
	@include ('messages.row')
@endforeach

@stop
