@extends('layout')

@section('content')

@foreach ( $categories as $count => $category )
@if ( count($category->modules) > 0 )
<div class="panel panel-primary">

	<div class="panel-heading">{{{ $category->name }}}</div>

	<div class="panel-body">

	<ul>
		@foreach ( $category->modules as $app )
		<li><a href="{{ $app->url }}">{{{ $app->name }}}</a> - {{{ $app->description }}}
		@endforeach
	</ul>

	</div>

</div>
@endif
@endforeach

@stop
