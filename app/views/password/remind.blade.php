@extends('layout')

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">I forgot my password</div>

	<div class="panel-body">

<form class="form-horizontal" action="{{ action('Parangi\RemindersController@postRemind') }}" method="POST">
<div class="col-md-4">

<div class="form-group">
	{{ Form::email('email', '', ['class' => 'form-control', 'placeholder' => 'Email']) }}
</div>

<div class="form-group">
	{{ Form::submit('Reset my password', ['class' => 'btn btn-primary btn-once', 'data-loading-text' => 'Resetting...']) }}
</div>

</div>
</form>

	<div class="clearfix"></div>

	<p>
	Still having issues? <a href="/contact">Contact me</a> for assistance
	</p>

	</div>
</div>

@stop

