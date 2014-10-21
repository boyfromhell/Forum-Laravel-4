@extends('layout')

@section('content')

<form class="form" method="post" action="/move-topic/{{ $topic->id }}">
<div class="panel panel-primary">

	<div class="panel-heading">Move Topic</div>

	<div class="panel-body">

		<p>
		Move the topic <a href="{{ $topic->url }}">{{{ $topic->title }}}</a>
		from <a href="{{ $topic->forum->url }}">{{{ $topic->forum->name }}}</a> to
		</p>

		<div class="row">
		<div class="col-sm-6 col-md-4">
		<select name="new_forum" class="form-control">
	@foreach ( $categories as $jc )
			<option value="" style="background:#f7f7f7; color:#000" disabled>{{ $jc->name }}</option>
		@foreach ( $jc->forums as $jf )
			<option value="{{ $jf->id }}"{{ $topic->forum_id == $jf->id ? ' selected' : '' }}>&nbsp;&nbsp;&nbsp;&nbsp;{{ $jf->name }}</option>
			@foreach ( $jf->children as $jsf )
			<option value="{{ $jsf->id }}"{{ $topic->forum_id == $jsf->id ? ' selected' : '' }}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $jsf->name }}</option>
			@endforeach
		@endforeach
	@endforeach
		</select>
		</div>
		</div>

	</div>

	<div class="panel-footer text-center">

		{{ Form::submit('Move', ['name' => 'confirm', 'class' => 'btn btn-primary btn-once', 'data-loading-text' => 'Moving...']) }}
		{{ Form::submit('Cancel', ['name' => 'cancel', 'class' => 'btn btn-default']) }}

	</div>

</div>
</form>

@stop
