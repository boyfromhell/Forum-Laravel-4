@extends('layout')

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">I forgot my password</div>

	<div class="panel-body">

<form class="form-horizontal" action="{{ action('RemindersController@postRemind') }}" method="POST">
<div class="col-md-4">

<div class="form-group">
	{{ Form::email('email', '', ['class' => 'form-control', 'placeholder' => 'Username / Email']) }}
</div>

<div class="form-group">
	{{ Form::submit('Reset my password', ['class' => 'btn btn-primary']) }}
</div>

</div>
</form>

	</div>
</div>

@stop

