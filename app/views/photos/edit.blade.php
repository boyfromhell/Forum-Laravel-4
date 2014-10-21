@extends('layout')

@section('header')
<h1><a href="{{ $album->url }}">{{{ $album->name }}}</a></h1>
<ol class="breadcrumb">
	<li><a href="/">{{ Config::get('app.forum_name') }}</a></li>
@foreach( $album->parents as $parent )
	<li><a href="{{ $parent->url }}">{{{ $parent->name }}}</a></li>
@endforeach
</ol>
@stop

@section('buttons')
<a class="btn btn-primary" href="{{ $photo->url }}">Back to Photo</a>
@stop

@section('content')

<form class="form-horizontal unload-warning" method="post" action="/edit-photo/{{ $photo->id }}">
<div class="panel panel-primary">

	<div class="panel-heading">Edit Photo</div>

	<div class="panel-body">

		<div class="form-group">
			<div class="col-sm-9 col-sm-offset-3">
			<div class="photo">
			<a class="thumb" href="{{ $photo->url }}"><img src="{{ $cdn }}{{ $photo->thumbnail }}"></a>
			</div>
			<div class="clearfix"></div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">Album</label>
			<div class="col-sm-9">
			<p class="form-control-static">
				{{{ $album->name }}}
			</p>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">Album Cover</label>
			<div class="col-sm-9">
				{{ Helpers::radioGroup('cover', ['1' => 'Yes', '0' => 'No'], ( $album->cover_id == $photo->id )) }}
			</div>
		</div>

		@if ( $me->is_admin )
		<div class="form-group">
			<label class="control-label col-sm-3">Uploaded by *</label>
			<div class="col-sm-3">
				{{ Form::text('author', $photo->user->name, ['class' => 'form-control', 'maxlength' => 25, 'required']) }}
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">Date uploaded *</label>
			<div class="col-sm-3">
				{{ Form::text('date', $photo->created_at, ['class' => 'form-control', 'required']) }}
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">Views *</label>
			<div class="col-sm-3">
				{{ Form::text('views', $photo->views, ['class' => 'form-control', 'maxlength' => 10, 'required']) }}
			</div>
		</div>

		{{--
		<label class="left">Update file</label>
		--}}

		<div class="form-group">
			<label class="control-label col-sm-3">Downloads</label>
			<div class="col-sm-3">
				<p class="form-control-static">{{ number_format($photo->downloads) }}</p>
			</div>
		</div>
		@endif

		<div class="form-group">
			<label class="control-label col-sm-3">Description</label>
			<div class="col-sm-7">
				{{ BBCode::show_bbcode_controls() }}
				{{ Form::textarea('description', $photo->description, ['id' => 'bbtext', 'class' => 'form-control']) }}
			</div>
		</div>

	</div>
	
	<div class="panel-footer">
		<div class="form-group">
			<div class="col-sm-9 col-sm-offset-3">
			{{ Form::submit('Save Photo', ['class' => 'btn btn-primary btn-once', 'data-loading-text' => 'Saving...']) }}
			{{ Form::reset('Reset', ['class' => 'btn btn-default']) }}
			</div>
		</div>
	</div>

</div>

</form>

@stop
