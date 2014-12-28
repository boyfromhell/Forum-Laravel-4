@extends('layout')

@section('header')
<h1><a href="{{ $photo->album->url }}">{{{ $photo->album->name }}}</a></h1>

<ol class="breadcrumb">
	<li><a href="/">{{ Config::get('app.forum_name') }}</a></li>
@foreach ( $photo->album->parents as $parent )
	<li><a href="{{ $parent->url }}">{{{ $parent->name }}}</a></li>
@endforeach
</ol>
@stop

@section('buttons')
<div class="btn-group pull-left">
	<a class="btn btn-primary" href="{{ $photo->album->url }}{{ $page > 1 ? '?page='.$page : '' }}">Back to Album</a>
	<a class="btn btn-success" href="/media/download/{{ $photo->id }}"><span class="glyphicon glyphicon-save"></span> Download</a>
</div>
<div class="btn-group pull-right">
	<a class="btn btn-default ajax-photo" data-id="{{ $prev->id }}" href="{{ $prev->url }}"><span class="glyphicon glyphicon-chevron-left"></span> Previous</a>
	<a class="btn btn-default icon-right ajax-photo" data-id="{{ $next->id }}" href="{{ $next->url }}">Next <span class="glyphicon glyphicon-chevron-right"></span></a>
</div>
@stop

@section('content')

<div id="ajaxphoto">

@include ('photos.photo')

</div>

@stop
