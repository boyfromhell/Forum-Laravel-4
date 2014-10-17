@extends('layout')

@section('content')

@include ('blocks.social')

<div class="panel panel-primary">

	<div class="panel-heading">{{{ $_PAGE['title'] }}}</div>

	<div class="panel-body">

	@include ('custom.welcome')

	</div>

</div>

@stop
