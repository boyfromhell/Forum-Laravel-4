@extends('layout')

@section('content')

<form class="form-horizontal unload-warning" method="post" action="/flag-post/{{ $post->id }}">
<div class="panel panel-danger">

	<div class="panel-heading">Flag Post</div>
	
	<div class="panel-body">
		<p>
		Flagging post # <a href="{{ $post->url }}">{{ $post->id }}</a> by <a href="{{ $post->user->url }}">{{{ $post->user->name }}}</a>
		</p>

		<div class="form-group">
			<label class="control-label col-sm-3">Reason</label>
			<div class="col-sm-3">
			<select class="form-control" name="reason">
				<option value="1">Useless / Off Topic</option>
				<option value="2">Advertisement</option>
				<option value="3">Personal Attack</option>
				<option value="4">Obscene Language</option>
				<option value="5">Copyright Infringement</option>
				<option value="6">Other</option>
			</select>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-sm-3">Additional Comments (optional)</label>
			<div class="col-sm-7">
			{{ BBCode::show_bbcode_controls() }}
			{{ Form::textarea('comments', '', ['id' => 'bbtext', 'class' => 'form-control']) }}
			</div>
		</div>

	</div>

	<div class="panel-footer">
		<div class="form-group">
			<div class="col-sm-9 col-sm-offset-3">
			{{ Form::submit('Flag Post', ['name' => 'flag', 'class' => 'btn btn-danger btn-once', 'data-loading-text' => 'Flagging...']) }}
			{{ Form::submit('Cancel', ['name' => 'cancel', 'class' => 'btn btn-default']) }}
			</div>
		</div>
	</div>

</div>

</form>

@stop

