@extends('layout')

@section('content')

<h1><a href="{{ $album->url }}">{{{ $album->name }}}</a></h1>
<a href="/">{{ Config::get('app.forum_name') }}</a>

@foreach ( $photo->album->parents as $parent )
	&gt; <a href="{{ $parent->url }}">{{{ $parent->name }}}</a>
@endforeach

<br><br>

<div id="ajaxphoto">

@include ('photos.photo')

</div>

@stop
