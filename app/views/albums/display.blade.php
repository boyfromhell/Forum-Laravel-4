@extends('layout')

@section('header')

<h1><a href="{{ $album->url }}">{{{ $album->name }}}</a></h1>

@if ( $album->id != 1 )
<ol class="breadcrumb">
	<li><a href="/">{{{ Config::get('app.forum_name') }}}</a></li>
@foreach ( $album->parents as $parent )
	<li><a href="{{ $parent->url }}">{{{ $parent->name }}}</a></li>
@endforeach
</ol>
@endif

@stop

@section('buttons')
<div class="btn-group pull-left">
@if ( $allow || ( $album->parent_id == 1 && $me->id ))
<a class="btn btn-primary" href="/create-album/{{ $album->id }}">New Album</a>
@endif
@if (( $me->is_admin || $album->user_id == $me->id ) && $album->id != 1)
<a class="btn btn-default" href="/edit-album/{{ $album->id }}"><span class="glyphicon glyphicon-pencil"></span> Edit Album</a>
@endif
@if ( $allow )
<a class="btn btn-default" href="/upload-photos/{{ $album->id }}"><span class="glyphicon glyphicon-open"></span> Upload</a>
@endif
</div>

<div class="pull-right">
	{{ $photos->links() }}
</div>
@stop

@section('content')

@if ( $album->description )

<div class="panel panel-primary">

	<div class="panel-heading">{{{ $album->name }}}</div>
	
	<div class="panel-body">
		<p>
		{{ BBCode::parse($album->description) }}
		</p>
	</div>
</div>
@endif

@if ( count($album->children) > 0 )
<div class="panel panel-default">

	<div class="panel-heading">Albums</div>

	<div class="panel-body row">
		@foreach ( $album->children as $child )
			@include ('albums.row')
		@endforeach
	</div>
</div>
@endif

@if ( count($photos) > 0 || !count($album->children) )
<div class="panel panel-default">

	<div class="panel-heading">Photos</div>

	<div class="panel-body">
		@if ( count($photos) )
		@foreach ( $photos as $photo )
			<div class="photo"><a class="thumb" href="{{ $photo->url }}"><img src="{{ $cdn }}{{ $photo->thumbnail }}"></a></div>
		@endforeach

		<div class="clearfix"></div>
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

@stop
