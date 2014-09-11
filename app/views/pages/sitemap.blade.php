@extends('layout')

@section('content')

@foreach ( $categories as $count => $category )
@if ( count($category->modules) > 0 )
<div class="welcome{{ $count == count($categories)-1 ? ' no-margin' : '' }}">

	<div class="header">{{{ $category->name }}}</div>

	<div class="body">

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
