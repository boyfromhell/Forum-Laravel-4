@extends('layout')

@section('content')

<div class="row content-container">
    <div class="content-header">
        <h2>I forgot my password</h2>
    </div>
    <div class="content-middle">

<form class="form-horizontal" action="{{ action('RemindersController@postReset') }}" method="POST">
<div>

	{{ Form::hidden('token', $token) }}

<div class="form-group">
	<label class="col-sm-2 control-label">Email</label>
	<div class="col-sm-4">
		{{ Form::email('email', '', ['class' => 'form-control']) }}
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label">New Password</label>
	<div class="col-sm-4">
		{{ Form::password('password', ['class' => 'form-control']) }}
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label">Password (Again)</label>
	<div class="col-sm-4">
		{{ Form::password('password_confirmation', ['class' => 'form-control']) }}
	</div>
</div>

<div class="form-group">
	<div class="col-sm-offset-2 col-sm-4">
		{{ Form::submit('Reset Password', ['class' => 'btn btn-primary']) }}
	</div>
</div>

</div>
</form>

	</div>

</div>

@stop

