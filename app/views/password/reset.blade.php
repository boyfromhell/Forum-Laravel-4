@extends('layout')

@section('content')

<div class="row content-container">
    <div class="content-header">
        <h2>I forgot my password</h2>
    </div>
    <div class="content-middle">

<form class="form-horizontal" action="{{ action('RemindersController@postReset') }}" method="POST">
<div>

    <input type="hidden" name="token" value="{{ $token }}">

<div class="form-group">
	<label class="col-sm-2 control-label">Email</label>
	<div class="col-sm-4">
	    <input type="email" name="email" class="form-control">
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label">New Password</label>
	<div class="col-sm-4">
	    <input type="password" name="password" class="form-control">
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label">Password (Again)</label>
	<div class="col-sm-4">
	    <input type="password" name="password_confirmation" class="form-control">
	</div>
</div>

<div class="form-group">
	<div class="col-sm-offset-2 col-sm-4">
	    <input type="submit" value="Reset Password" class="btn btn-main">
	</div>
</div>

</div>
</form>

	</div>

</div>

@stop

