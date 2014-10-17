@extends('layout')

@section('content')

<h1><a href="{{ $album->url }}">{{{ $album->name }}}</a></h1>
@if ( $album->id != 1 )
<a href="/">{{{ Config::get('app.forum_name') }}}</a>

@foreach ( $album->parents as $parent )
	&gt; <a href="{{ $parent->url }}">{{{ $parent->name }}}</a>
@endforeach
<br>
@endif
<br>

@if ( $allow || ( $album->parent_id == 1 && $me->id ))
<a class="btn btn-primary" href="/albums/new?id={{ $album->id }}">New{{ !$is_mobile ? ' Album' : '' }}</a>
@endif
@if (( $me->is_admin || $album->user_id == $me->id ) && $album->id != 1)
<a class="btn btn-default" href="/albums/edit?id={{ $album->id }}">Edit{{ !$is_mobile ? ' Album' : '' }}</a>
@endif
@if ( $allow )
<a class="btn btn-default" href="/media/upload?id={{ $album->id }}">Upload</a>
@endif

{{ $photos->links() }}

<div class="break"></div>

@if ( $album->description )

<div class="welcome wide">

	<div class="header">{{{ $album->name }}}</div>
	
	<div class="body"><p>
		{{ BBCode::parse($album->description) }}
	</p></div>
</div>
@endif

@if ( count($album->children) > 0 )
<div class="welcome wide{{ !count($photos) ? ' no-margin' : '' }}">

	<div class="header">Albums</div>

	<div class="body row">
		@foreach ( $album->children as $child )
			@include ('albums.row')
		@endforeach
	</div>
</div>
@endif

@if ( count($photos) > 0 || !count($album->children) )
<div class="welcome wide no-margin">

	<div class="header">Photos</div>

	<div class="body">
		@if ( count($photos) )
		@foreach ( $photos as $photo )
			<div class="photo"><a class="thumb" href="{{ $photo->url }}"><img src="{{ $cdn }}{{ $photo->thumbnail }}"></a></div>
		@endforeach

		<div class="break"></div>
		@else
		<center>
		<br>
		There are no photos in this album
		<br><br>
		</center>
		@endif
	</div>
</div>
@endif

@if ( $allow || ( $album->parent_id == 1 && $me->id ))
<a class="btn btn-primary" href="/albums/new?id={{ $album->id }}">New{{ !$is_mobile ? ' Album' : '' }}</a>
@endif
@if (( $me->is_admin || $album->user_id == $me->id ) && $album->id != 1)
<a class="btn btn-default" href="/albums/edit?id={{ $album->id }}">Edit{{ !$is_mobile ? ' Album' : '' }}</a>
@endif
@if ( $allow )
<a class="btn btn-default" href="/media/upload?id={{ $album->id }}">Upload</a>
@endif

{{ $photos->links() }}

<div class="break"></div>

@stop
