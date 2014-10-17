@extends('layout')

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">Create account</div>

	<div class="panel-body">

		<div class="col-md-6">

<form class="form-horizontal" method="post" action="/signup">

<div class="form-group">
	<div class="col-md-6">
		{{ Form::text('first_name', '', ['placeholder' => 'First *', 'tabindex' => 1, 'class' => 'form-control', 'required']) }}
	</div>
	<div class="col-md-6">
		{{ Form::text('last_name', '', ['placeholder' => 'Last *', 'tabindex' => 1, 'class' => 'form-control', 'required']) }}
	</div>
</div>

<div class="form-group">
	<div class="col-md-12">
		{{ Form::email('email', '', ['placeholder' => 'Email *', 'tabindex' => 1, 'class' => 'form-control', 'required']) }}
	</div>
</div>

<div class="form-group">
	<div class="col-md-12">
		{{ Form::password('password', ['placeholder' => 'Password *', 'tabindex' => 1, 'class' => 'form-control', 'required']) }}
	</div>
</div>

<div class="form-group">
	<div class="col-md-12">
		<p class="form-control-static"><b>* Required.</b></p>
	</div>
</div>

<div class="form-group">
	<div class="col-md-12">
		{{ Form::submit('Sign Up', ['tabindex' => 1, 'class' => 'btn btn-primary']) }}
	</div>
</div>

<div class="form-group">
	<div class="col-md-12">
		<p class="form-control-static"><a href="/lost-password" style="color:#666">I forgot my password</a></p>
	</div>
</div>

</form>

		</div>
	</div>
</div>

@stop
