@extends('layout')

@section('content')

@foreach ( $categories as $category => $links )
<div class="panel panel-primary">

	<div class="panel-heading">{{{ $category }}}</div>
	
	<div class="panel-body">

	<ul>
		@foreach ( $links as $url => $name )
		<li><a href="{{ $url }}">{{ $url }}</a> - {{{ $name }}}</li>
		@endforeach
	</ul>

	</div>

</div>
@endforeach

@stop
