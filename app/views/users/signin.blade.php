@extends('layout')

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">Sign in</div>

	<div class="panel-body">

		<div class="col-md-9">

			<form class="form-horizontal" method="post" action="/signin">

<div class="form-group">
	<label class="col-sm-3 control-label">Username / Email *</label>
	<div class="col-sm-6">
	{{ Form::text('email', '', ['tabindex' => 1, 'class' => 'form-control']) }}
	</div>
</div>

<div class="form-group">
	<label class="col-sm-3 control-label">Password *</label>
	<div class="col-sm-6">
	{{ Form::password('password', ['tabindex' => 1, 'class' => 'form-control']) }}
	</div>
</div>

<div class="form-group">
	<div class="col-sm-offset-3 col-sm-6">
	<input type="submit" name="signin" class="btn btn-primary" value="Sign in" tabindex="1">
	</div>
</div>

<div class="form-group">
	<div class="col-sm-offset-3 col-sm-6">
		<p class="form-control-static"><a href="/lost-password" style="color:#666">I forgot my password</a></p>
	</div>
</div>

</div>
</form>

		</div>
	</div>
</div>

@stop
