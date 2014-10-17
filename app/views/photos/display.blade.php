@extends('layout')

@section('content')

<h1><a href="{{ $album->url }}">{{{ $album->name }}}</a></h1>

<ol class="breadcrumb">
	<li><a href="/">{{ Config::get('app.forum_name') }}</a></li>
@foreach ( $photo->album->parents as $parent )
	<li><a href="{{ $parent->url }}">{{{ $parent->name }}}</a></li>
@endforeach
</ol>

<div id="ajaxphoto">

@include ('photos.photo')

</div>

@stop
