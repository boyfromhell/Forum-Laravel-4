@extends('layout')

@section('content')

@include ('blocks.social')

<div class="welcome no-margin">

	<div class="header">{{{ $_PAGE['title'] }}}</div>

	<div class="body">

	@include ('custom.welcome')

	</div>

</div>

@stop
