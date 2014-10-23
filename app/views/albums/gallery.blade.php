@extends('layout')

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">Random Photos</div>

	<div class="panel-body" style="overflow:hidden; height:225px;">
	@foreach ( $photos as $photo )
	
	<div class="photo" style="height:225px">
		<a class="thumb" href="{{ $photo->url }}"><img src="{{ $cdn }}{{ $photo->thumbnail }}" alt="thumbnail"></a>
		by <a href="{{ $photo->user->url }}">{{{ $photo->user->name }}}</a>
	</div>

	@endforeach
	</div>

</div>

<div class="panel panel-primary">

	<div class="panel-heading">Recent Albums</div>

	<div class="panel-body" style="overflow:hidden; height:245px;">
	@foreach ( $albums as $album )

	<div class="photo" style="height:245px">
		<a class="thumb" href="{{ $album->url }}"><img src="{{ $cdn }}{{ $album->coverPhoto->thumbnail }}" alt="album cover"></a>
		<div style="height:18px; overflow:hidden">{{{ $album->name }}}</div>
		by <a href="{{ $album->user->url }}">{{{ $album->user->name }}}</a>
	</div>

	@endforeach
	</div>

</div>

@stop
